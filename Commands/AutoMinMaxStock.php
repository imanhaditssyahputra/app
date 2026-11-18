<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class AutoMinMaxStock extends BaseCommand
{
    protected $group       = 'Inventory';
    protected $name        = 'stock:minmax';
    protected $description = 'Menghitung Stock Minimum dan Maximum otomatis';

    public function run(array $params)
    {
        $db = Database::connect();

        $dateNow = date('Y-m-d');

        // Periode perhitungan
        $minPusat  = date('Y-m-d', strtotime('-120 days', strtotime($dateNow)));
        $maxPusat  = date('Y-m-d', strtotime('-160 days', strtotime($dateNow)));

        $minCabang = date('Y-m-d', strtotime('-30 days', strtotime($dateNow)));
        $maxCabang = date('Y-m-d', strtotime('-60 days', strtotime($dateNow)));

        CLI::write('====================================', 'yellow');
        CLI::write(' AUTO MIN MAX STOCK', 'yellow');
        CLI::write('====================================', 'yellow');

        CLI::write('Tanggal proses : ' . $dateNow);
        CLI::write('Mengambil histori transaksi...');

        try {

            /*
             * 1. Ambil total pengeluaran seluruh barang
             *    berdasarkan cabang dan kode item.
             */

            $sql = "
                SELECT
                    kodeitem,
                    cabang,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN (
                                    (cabang = '01' AND tanggal >= ?)
                                    OR
                                    (cabang <> '01' AND tanggal >= ?)
                                )
                                THEN COALESCE(pengeluaran, 0)
                                ELSE 0
                            END
                        ), 0
                    ) AS stok_min,

                    COALESCE(
                        SUM(
                            CASE
                                WHEN (
                                    (cabang = '01' AND tanggal >= ?)
                                    OR
                                    (cabang <> '01' AND tanggal >= ?)
                                )
                                THEN COALESCE(pengeluaran, 0)
                                ELSE 0
                            END
                        ), 0
                    ) AS stok_max

                FROM inv_transaksi

                WHERE tanggal >= ?
                  AND tanggal <= ?

                GROUP BY kodeitem, cabang
            ";

            $resultTransaksi = $db->query($sql, [
                $minPusat,
                $minCabang,
                $maxPusat,
                $maxCabang,
                $maxPusat,
                $dateNow
            ])->getResultArray();

            // Mapping transaksi berdasarkan cabang + kode item
            $mapTransaksi = [];

            foreach ($resultTransaksi as $row) {

                $key = $row['cabang'] . '|' . $row['kodeitem'];

                $mapTransaksi[$key] = [
                    'stok_min' => (float) $row['stok_min'],
                    'stok_max' => (float) $row['stok_max']
                ];
            }

            CLI::write(
                'Total kombinasi barang dan cabang transaksi: ' .
                count($mapTransaksi),
                'green'
            );

            /*
             * 2. Ambil seluruh barang paket.
             *    Barang yang menjadi paket maupun komponen paket
             *    tidak akan dihitung.
             */

            $resultPaket = $db->table('inv_barang_paket')
                ->select('cabang, kodeitem_barang, kodeitem_paket')
                ->get()
                ->getResultArray();

            $mapPaket = [];

            foreach ($resultPaket as $paket) {

                $cabangPaket = $paket['cabang'];

                if (!empty($paket['kodeitem_barang'])) {
                    $mapPaket[$cabangPaket][$paket['kodeitem_barang']] = true;
                }

                if (!empty($paket['kodeitem_paket'])) {
                    $mapPaket[$cabangPaket][$paket['kodeitem_paket']] = true;
                }
            }

            /*
             * 3. Ambil seluruh data inventory.
             */

            $resultBarang = $db->table('inv_barang')
                ->select('kodeitem, kdcbg, kdgudang')
                ->get()
                ->getResultArray();

            CLI::write(
                'Total data inventory: ' . count($resultBarang),
                'green'
            );

            /*
             * 4. Update Stock Min dan Max.
             */

            $totalUpdate = 0;
            $totalSkip   = 0;

            $db->transBegin();

            foreach ($resultBarang as $barang) {

                $kodeitem = $barang['kodeitem'];
                $cabang   = $barang['kdcbg'];
                $gudang   = $barang['kdgudang'];

                // Skip barang paket dan komponen paket
                if (isset($mapPaket[$cabang][$kodeitem])) {
                    $totalSkip++;
                    continue;
                }

                // Ambil hasil perhitungan
                $key = $cabang . '|' . $kodeitem;

                if (isset($mapTransaksi[$key])) {

                    $stokMin = $mapTransaksi[$key]['stok_min'];
                    $stokMax = $mapTransaksi[$key]['stok_max'];

                } else {

                    // Barang tanpa transaksi pengeluaran
                    $stokMin = 0;
                    $stokMax = 0;
                }

                // Update berdasarkan barang, cabang, dan gudang
                $db->table('inv_barang')
                    ->where('kodeitem', $kodeitem)
                    ->where('kdcbg', $cabang)
                    ->where('kdgudang', $gudang)
                    ->update([
                        'stok_min' => $stokMin,
                        'stok_max' => $stokMax
                    ]);

                $totalUpdate++;
            }

            if ($db->transStatus() === false) {

                $db->transRollback();

                throw new \RuntimeException(
                    'Gagal melakukan update Stock Min-Max.'
                );

            } else {

                $db->transCommit();
            }

            /*
             * 5. Tampilkan hasil proses.
             */

            CLI::write('====================================', 'yellow');
            CLI::write(' PROSES SELESAI', 'green');
            CLI::write('====================================', 'yellow');

            CLI::write('Total inventory : ' . count($resultBarang));
            CLI::write('Berhasil update : ' . $totalUpdate, 'green');
            CLI::write('Barang paket    : ' . $totalSkip);
            CLI::write('Tanggal proses  : ' . $dateNow);

            log_message(
                'info',
                'Auto Min-Max Stock berhasil. Update: ' .
                $totalUpdate . ', Skip paket: ' . $totalSkip
            );

        } catch (\Throwable $e) {

            if ($db->transStatus() !== false) {
                $db->transRollback();
            }

            CLI::error('ERROR: ' . $e->getMessage());

            log_message(
                'error',
                'Auto Min-Max Stock gagal: ' . $e->getMessage()
            );

        }
    }
}
