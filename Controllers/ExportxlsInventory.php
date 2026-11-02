<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Export xls Inventory
 **/

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_faktur_helper;
use App\Helpers\simkeu_rpt_helper;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportxlsInventory extends BaseController
{
    public function __construct()
    {
        // Inisialisasi yang diperlukan
        // Contoh: Memuat model
        $this->_CheckAksesWebsite();
    }

    private function _CheckAksesWebsite()
    {
        $response = service('response');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $DataIdentity = $model_Global->_LoadProfile();

        if ($DataIdentity->active_website == 1) {
            return $response->redirect(base_url('/logout'));
        }
    }

    // Fungsi untuk menambahkan data validation
    private function addDataValidation($sheet, $cell, $optionsRange)
    {
        $dataValidation = new DataValidation();
        $dataValidation->setType(DataValidation::TYPE_LIST);
        $dataValidation->setAllowBlank(true);
        $dataValidation->setFormula1($optionsRange);
        $dataValidation->setShowDropDown(true);
        $sheet->getCell($cell)->setDataValidation($dataValidation);
    }

    public function ExportInventory($x)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $cek  = $session->get('level');
        $unit = $session->get('cabang');
        if (!empty($cek)) {
            $profile = $model_Global->_LoadProfile();
            $nama_usaha = $profile->nama_usaha;
            $motto = '';
            $alamat = '';
            $namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
            $data  = explode("~", $x);
            $idlp  = $data[0];
            $tgl1  = $data[1];
            $tgl2  = $data[2];
            $kat   = $data[3];
            $brg   = $data[4];
            $cab   = $data[5];
            $gud   = $data[6];
            $profit    = $data[7];

            $_tgl_awal = date('Y-m-d', strtotime($tgl1) - 1);
            $_tgl1 = date('Y-m-d', strtotime($tgl1));
            $_tgl2 = date('Y-m-d', strtotime($tgl2));
            $_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
            $_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
            $_peri2 = $profit ? 'Range Profit kurang dari ' . $profit . '%' : 'Range Profit Semua Profit';

            //LAPORAN DAFTAR BARANG
            if ($idlp == 101) {
                $query =
                    "select * from inv_barang where kodeitem is not null";

                if ($kat != "") {
                    $query .= " and kdkategori = '$kat'";
                }
                if ($cab != "") {
                    $query .= " and kdcbg = '$cab'";
                }
                if ($brg != "") {
                    $query .= " and kodeitem = '$brg'";
                }
                if ($gud != "") {
                    $query .= " and kdgudang = '$gud'";
                }
                $query .= " group by kodeitem";
                $query .= " order by namabarang";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('DAFTAR BARANG');
                $sheet->setCellValue('A1', 'DAFTAR BARANG');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                if ($cab != "") {
                    $sheet->setCellValue('A3', 'Region');
                    $sheet->setCellValue('B3', $db->table('ms_unit')->getWhere(['kode' => $cab])->getRow()->nama);
                } else {
                    $sheet->setCellValue('A3', 'Region');
                    $sheet->setCellValue('B3', 'Semua Region');
                }
                if ($gud != '') {
                    $sheet->setCellValue('A4', 'Gudang');
                    $sheet->setCellValue('B4', $db->table('inv_gudang')->getWhere(['kode' => $gud])->getRow()->nama);
                } else {
                    $sheet->setCellValue('A4', 'Gudang');
                    $sheet->setCellValue('B4', 'Semua Gudang');
                }
                // Column DetailData
                $headers = ['NO', 'KODE', 'NAMA BARANG', 'KUANTITAS', 'SATUAN', 'HARGA JUAL', 'HARGA BELI'];
                $sheet->fromArray($headers, NULL, 'A5');
                $sheet->getStyle('A1:J5')->getFont()->setBold(true);
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 6;
                foreach ($lap as $key => $value) {
                    $db = db_connect();
                    $Vbarang = $db->query("SELECT sum(stok) as totalqty from inv_barang where kodeitem='$value->kodeitem'")->getRow();
                    if ($cab != "") {
                        $stok = $value->stok;
                    } else {
                        $stok = $Vbarang->totalqty;
                    }

                    $sheet->setCellValue('A' . $column, ($column - 5));
                    $sheet->setCellValue('B' . $column, $value->kodeitem);
                    $sheet->setCellValue('C' . $column, $value->namabarang);
                    $sheet->setCellValue('D' . $column, $stok);
                    $sheet->setCellValue('E' . $column, $value->satuan);
                    $sheet->setCellValue('F' . $column, number_format($value->hargajual, 0, '.', ','));
                    $sheet->setCellValue('G' . $column, number_format($value->hargabeli, 0, '.', ','));
                    $column++;
                }

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'DAFTAR BARANG.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            //LAPORAN KARTU STOK
            else if ($idlp == 102) {

                $query =
                    "select * from inv_transaksi where kodeitem = '$brg' and tanggal between '$_tgl1' and '$_tgl2' ";

                if ($gud != "") {
                    $query .= " and gudang = '$gud'";
                }
                if ($cab != "") {
                    $query .= " and cabang = '$cab'";
                }
                $query .= " order by tglrekam";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('KARTU STOCK BARANG');
                $sheet->setCellValue('A1', 'KARTU STOCK BARANG');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $sheet->setCellValue('A3', 'Kode Barang');
                $sheet->setCellValue('B3', $brg);
                $sheet->setCellValue('A4', 'Nama Barang');
                $sheet->setCellValue('B4', $db->table('inv_barang')->getWhere(['kodeitem' => $brg])->getRow()->namabarang);
                if ($gud != "") {
                    $sheet->setCellValue('A5', 'Gudang');
                    $sheet->setCellValue('B5', $db->table('inv_gudang')->getWhere(['kode' => $gud])->getRow()->nama);
                    $colDetail = 6;
                    $column = 7;
                } else {
                    $colDetail = 5;
                    $column = 6;
                }
                // Column DetailData
                $headers = ['NO', 'TANGGAL', 'NOMOR', 'NAMA CUSTOMER', 'MASUK', 'KELUAR', 'SO', 'SALDO', 'HARGA', 'BATCH', 'ED'];
                $sheet->fromArray($headers, NULL, 'A' . $colDetail);
                $sheet->getStyle('A1:K' . $colDetail)->getFont()->setBold(true);
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $nourut = 1;
                $totkeluar = 0;
                $totmasuk = 0;
                $totso = 0;
                $so = 0;
                $saldo = 0;

                if ($gud != "") {
                    $invgud = " and gudang = '$gud' ";
                } else {
                    $invgud = '';
                }
                if ($cab != "") {
                    $invcab = " and cabang = '$cab' ";
                } else {
                    $invcab = '';
                }

                error_reporting(0);
                $harga = 0;
                $db = db_connect();
                $so = $db->query("select * from inv_transaksi where kodeitem='$brg' $invgud $invcab  and tanggal < '$_tgl1' order by tglrekam ")->getResult();
                foreach ($so as $value) {
                    $jenis = $value->jenis;
                    $nobukti = $value->nobukti;

                    if ($jenis == 'Stock Opname') {
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $value->nobukti;
                        $vendor = $value->jenis;
                        $terima = 0;
                        $keluar = 0;
                        $so = $value->so;
                    }

                    if ($jenis == 'Pembelian') {
                        $db = db_connect();
                        $faktur_pembelian = $db->query("SELECT ap_pufile.kodepu, ap_pufile.kodesup, ap_pufile.kodepb, ap_supplier.kode, ap_supplier.nama from ap_pufile inner join ap_supplier on ap_pufile.kodesup = ap_supplier.kode where ap_pufile.kodepb='$nobukti' OR ap_pufile.kodepu='$nobukti'")->getRow();
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $faktur_pembelian->kodepu;
                        $vendor = $faktur_pembelian->nama;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        $so = 0;
                    }

                    if ($jenis == 'Penjualan') {
                        $db = db_connect();
                        $faktur_penjualan = $db->query("SELECT ar_sifile.kodesi, ar_sifile.kodecust, ar_sifile.kodesd, ar_customer.kode, ar_customer.nama from ar_sifile inner join ar_customer on ar_sifile.kodecust = ar_customer.kode where ar_sifile.kodesd = '$nobukti' or ar_sifile.kodesi='$nobukti' ")->getRow();
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $faktur_penjualan->kodesi;
                        $vendor = $faktur_penjualan->nama;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        $so = 0;
                    }
                    if ($jenis == 'Retur Penjualan') {
                        $db = db_connect();
                        $penjualan_retur = $db->query("SELECT ar_retur.kodesi, ar_retur.kodecust, ar_retur.koderetur, ar_customer.kode, ar_customer.nama from ar_retur inner join ar_customer on ar_retur.kodecust = ar_customer.kode where ar_retur.koderetur='$nobukti'")->getRow();
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $penjualan_retur->koderetur;
                        $vendor = $penjualan_retur->nama;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        $so = 0;
                    }
                    if ($jenis == 'Retur Pembelian') {
                        $db = db_connect();
                        $Pembelian_retur = $db->query("SELECT ap_retur.kodepu, ap_retur.kodesup, ap_retur.koderetur, ap_supplier.kode, ap_supplier.nama from ap_retur inner join ap_supplier on ap_retur.kodesup = ap_supplier.kode where ap_retur.koderetur='$nobukti'")->getRow();
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $Pembelian_retur->koderetur;
                        $vendor = $Pembelian_retur->nama;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        $so = 0;
                    }
                    if ($jenis == 'Mutasi Cabang') {
                        $db = db_connect();
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $value->nobukti;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        if ($terima == 0) {
                            $VmutasiCabang = $db->query("select * from inv_mutasi_cabang where nomutasi_cabang='$nomor_bukti'")->getRow();
                            $VmutasiCabangAcc = $db->query("select * from inv_acc_mutasi_cabang where noacc_mutasi='$nomor_bukti'")->getRow();
                            if ($VmutasiCabang->nomutasi_cabang != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_asal])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_tujuan])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            } else if ($VmutasiCabangAcc->noacc_mutasi != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_terima])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        } else {
                            $VmutasiCabang = $db->query("select * from inv_mutasi_cabang where nomutasi_cabang='$nomor_bukti'")->getRow();
                            $VmutasiCabangAcc = $db->query("select * from inv_acc_mutasi_cabang where noacc_mutasi='$nomor_bukti'")->getRow();
                            if ($VmutasiCabang->nomutasi_cabang != null) {

                                $Vcabang = $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_asal])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_tujuan])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            } else if ($VmutasiCabangAcc->noacc_mutasi != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_terima])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        }

                        $so = 0;
                    }

                    if ($jenis == 'Retur Mutasi Cabang') {
                        $tglrekam = $value->tglrekam;
                        $nomor_bukti = $value->nobukti;
                        $terima = $value->penerimaan;
                        $keluar = $value->pengeluaran;
                        if ($terima == 0) {
                            $VmutasiRCabang = $db->query("select * from inv_mutasi_cabang_retur where nomutasi_retur='$nomor_bukti'")->getRow();
                            if ($VmutasiRCabang->nomutasi_retur != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_terima])->getRow()->nama;
                                $vendor = 'Retur Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        } else {
                            $VmutasiRCabang = $db->query("select * from inv_mutasi_cabang_retur where nomutasi_retur='$nomor_bukti'")->getRow();
                            if ($VmutasiRCabang->nomutasi_retur != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_terima])->getRow()->nama;
                                $vendor = 'Retur Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        }
                        $so = 0;
                    }

                    $db = db_connect();
                    $so_percust = $db->query("SELECT * from inv_barang_harga where kodeitem='$brg' $invgud $invcab ")->getRow();
                    $harga = $so_percust->netto_spl;
                    if ($jenis == 'Stock Opname') {
                        $saldo = $so;
                    } else {
                        $saldo = $saldo + $value->penerimaan - $value->pengeluaran;
                    }

                    $nilaisaldo = $saldo * $harga;
                    if ($value->penerimaan != 0 || $value->pengeluaran != 0 || $so >= 0) {
                    }
                }

                //SALDO AWAL
                $sheet->setCellValue('A' . $column, $nourut);
                $sheet->setCellValue('B' . $column, date('d-m-Y H:i:s', strtotime($_tgl1)));
                $sheet->setCellValue('C' . $column, 'SALDO AWAL');
                $sheet->setCellValue('H' . $column, number_format($saldo, 0, '.', ','));
                $sheet->setCellValue('I' . $column,  number_format($nilaisaldo, 0, '.', ','));

                foreach ($lap as $row) {
                    $jenis = $row->jenis;
                    $nobukti = $row->nobukti;

                    if ($jenis == 'Stock Opname') {
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $row->nobukti;
                        $vendor = $row->jenis;
                        $terima = 0;
                        $keluar = 0;
                        $so = $row->so;
                    }

                    if ($jenis == 'Pembelian') {
                        $db = db_connect();
                        $faktur_pembelian = $db->query("SELECT ap_pufile.kodepu, ap_pufile.kodesup, ap_pufile.kodepb, ap_supplier.kode, ap_supplier.nama from ap_pufile inner join ap_supplier on ap_pufile.kodesup = ap_supplier.kode where ap_pufile.kodepb='$nobukti' OR ap_pufile.kodepu='$nobukti'")->getRow();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $faktur_pembelian->kodepu;
                        $vendor = $faktur_pembelian->nama;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        $so = 0;
                    }

                    if ($jenis == 'Penjualan') {
                        $db = db_connect();
                        $faktur_penjualan = $db->query("SELECT ar_sifile.kodesi, ar_sifile.kodecust, ar_sifile.kodesd, ar_customer.kode, ar_customer.nama from ar_sifile inner join ar_customer on ar_sifile.kodecust = ar_customer.kode where ar_sifile.kodesd = '$nobukti' or ar_sifile.kodesi='$nobukti' ")->getRow();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $faktur_penjualan->kodesi;
                        $vendor = $faktur_penjualan->nama;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        $so = 0;
                    }
                    if ($jenis == 'Retur Penjualan') {
                        $db = db_connect();
                        $penjualan_retur = $db->query("SELECT ar_retur.kodesi, ar_retur.kodecust, ar_retur.koderetur, ar_customer.kode, ar_customer.nama from ar_retur inner join ar_customer on ar_retur.kodecust = ar_customer.kode where ar_retur.koderetur='$nobukti'")->getRow();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $penjualan_retur->koderetur;
                        $vendor = $penjualan_retur->nama;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        $so = 0;
                    }
                    if ($jenis == 'Retur Pembelian') {
                        $db = db_connect();
                        $Pembelian_retur = $db->query("SELECT ap_retur.kodepu, ap_retur.kodesup, ap_retur.koderetur, ap_supplier.kode, ap_supplier.nama from ap_retur inner join ap_supplier on ap_retur.kodesup = ap_supplier.kode where ap_retur.koderetur='$nobukti'")->getRow();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $Pembelian_retur->koderetur;
                        $vendor = $Pembelian_retur->nama;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        $so = 0;
                    }

                    if ($jenis == 'Mutasi Cabang') {
                        $db = db_connect();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $row->nobukti;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        if ($terima == 0) {
                            $VmutasiCabang = $db->query("select * from inv_mutasi_cabang where nomutasi_cabang='$nomor_bukti'")->getRow();
                            $VmutasiCabangAcc = $db->query("select * from inv_acc_mutasi_cabang where noacc_mutasi='$nomor_bukti'")->getRow();
                            if ($VmutasiCabang->nomutasi_cabang != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_asal])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_tujuan])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            } else if ($VmutasiCabangAcc->noacc_mutasi != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_terima])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        } else {
                            $VmutasiCabang = $db->query("select * from inv_mutasi_cabang where nomutasi_cabang='$nomor_bukti'")->getRow();
                            $VmutasiCabangAcc = $db->query("select * from inv_acc_mutasi_cabang where noacc_mutasi='$nomor_bukti'")->getRow();
                            if ($VmutasiCabang->nomutasi_cabang != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_asal])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabang->cabang_tujuan])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            } else if ($VmutasiCabangAcc->noacc_mutasi != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan = $db->table('ms_unit')->getWhere(['kode' => $VmutasiCabangAcc->cabang_terima])->getRow()->nama;
                                $vendor = 'Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        }
                        $so = 0;
                    }
                    if ($jenis == 'Retur Mutasi Cabang') {
                        $db = db_connect();
                        $tglrekam = $row->tglrekam;
                        $nomor_bukti = $row->nobukti;
                        $terima = $row->penerimaan;
                        $keluar = $row->pengeluaran;
                        if ($terima == 0) {
                            $VmutasiRCabang = $db->query("select * from inv_mutasi_cabang_retur where nomutasi_retur='$nomor_bukti'")->getRow();
                            if ($VmutasiRCabang->nomutasi_retur != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan =  $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_terima])->getRow()->nama;
                                $vendor = 'Retur Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        } else {
                            $VmutasiRCabang = $db->query("select * from inv_mutasi_cabang_retur where nomutasi_retur='$nomor_bukti'")->getRow();
                            if ($VmutasiRCabang->nomutasi_retur != null) {
                                $Vcabang = $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_kirim])->getRow()->nama;
                                $Vcabangtujuan =  $db->table('ms_unit')->getWhere(['kode' => $VmutasiRCabang->cabang_terima])->getRow()->nama;
                                $vendor = 'Retur Mutasi Cabang Dari ' . $Vcabang . ' Ke ' . $Vcabangtujuan;
                            }
                        }
                        $so = 0;
                    }

                    $db = db_connect();
                    $so_percust = $db->query("SELECT * from inv_barang_harga where kodeitem='$brg' $invgud $invcab")->getRow();
                    $harga = $so_percust->netto_spl;
                    if ($jenis == 'Stock Opname') {
                        $saldo = $so;
                    } else {
                        $saldo = $saldo + $row->penerimaan - $row->pengeluaran;
                    }

                    $nilaisaldo = $saldo * $harga;
                    if ($row->penerimaan != 0 || $row->pengeluaran != 0 || $so >= 0) {

                        $sheet->setCellValue('A' . $column, $nourut);
                        $sheet->setCellValue('B' . $column, date('d-m-Y H:i:s', strtotime($tglrekam)));
                        $sheet->setCellValue('C' . $column, $nomor_bukti);
                        $sheet->setCellValue('D' . $column, $vendor);
                        $sheet->setCellValue('E' . $column, number_format($terima, 0, '.', ','));
                        $sheet->setCellValue('F' . $column, number_format($keluar, 0, '.', ','));
                        $sheet->setCellValue('G' . $column, number_format($so, 0, '.', ','));
                        $sheet->setCellValue('H' . $column, number_format($saldo, 0, '.', ','));
                        $sheet->setCellValue('I' . $column, number_format($nilaisaldo, 0, '.', ','));
                        $sheet->setCellValue('J' . $column, $row->no_batch);
                        $sheet->setCellValue('K' . $column, $row->tanggal_exp);
                    }

                    $nourut++;
                    $column++;
                    $totkeluar = $totkeluar + $row->pengeluaran;
                    $totmasuk = $totmasuk + $row->penerimaan;
                    $totso = $totso + $row->so;
                }

                //CALCULATE TOTAL
                $sheet->setCellValue('A' . $column, 'TOTAL');
                $sheet->setCellValue('E' . $column, number_format($totmasuk, 0, ',', '.'));
                $sheet->setCellValue('F' . $column,  number_format($totkeluar, 0, ',', '.'));
                $sheet->setCellValue('G' . $column, number_format($totso, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'KARTU STOCK BARANG.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // LAPORAN UMUR PERSEDIAAN
            else if ($idlp == 103) {
                $query =
                    "select * from inv_barang where kodeitem is not null";

                if ($kat != "") {
                    $query .= " and kdkategori = '$kat'";
                }
                if ($cab != "") {
                    $query .= " and kdcbg = '$cab'";
                }
                $query .= " order by kodeitem";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('UMUR PERSEDIAAN');
                $sheet->setCellValue('A1', 'UMUR PERSEDIAAN');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $headers = ['NO', 'KODE BARANG', 'NAMA BARANG', 'STOCK', 'SATUAN', ' 1 - 30', '31 - 60', '61 - 90', '91 - 120', '> 120'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                foreach ($lap as $key => $value) {
                    $stok1 = 0;
                    $stok2 = 0;
                    $stok3 = 0;
                    $stok4 = 0;
                    $stok5 = 0;

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->kodeitem);
                    $sheet->setCellValue('C' . $column, $value->namabarang);
                    $sheet->setCellValue('D' . $column, $value->stok);
                    $sheet->setCellValue('E' . $column, $value->satuan);
                    $sheet->setCellValue('F' . $column, $stok1);
                    $sheet->setCellValue('G' . $column, $stok2);
                    $sheet->setCellValue('H' . $column, $stok3);
                    $sheet->setCellValue('I' . $column, $stok4);
                    $sheet->setCellValue('J' . $column, $stok5);
                    $column++;
                }

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'UMUR PERSEDIAAN.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // LAPORAN MUTASI CABANG
            else if ($idlp == 104) {
                $query =
                    "SELECT * from inv_mutasi_cabang where tanggal between '$_tgl1' and '$_tgl2' ";

                if ($cab != "") {
                    $query .= "and cabang_asal = '$cab'";
                }
                $query .= "group by cabang_asal,nomutasi_cabang order by nomutasi_cabang";

                // Execute query and initialize spreadsheet
                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('MUTASI REGION');
                $sheet->setCellValue('A1', 'LAPORAN MUTASI REGION');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Starting line for data entry
                $column = 4;

                foreach ($lap as $inventory) {
                    // Add Mutasi Name
                    $sheet->setCellValue('A' . $column, 'Mutasi Asal');
                    $sheet->setCellValue('B' . $column, $db->table('ms_unit')->getWhere(['kode' => $inventory->cabang_asal])->getRow()->nama);
                    $sheet->setCellValue('C' . $column, '>>>');
                    $sheet->setCellValue('D' . $column, 'Mutasi Tujuan');
                    $sheet->setCellValue('E' . $column, $db->table('ms_unit')->getWhere(['kode' => $inventory->cabang_tujuan])->getRow()->nama);
                    $sheet->getStyle('A' . $column . ':E' . $column)->getFont()->setBold(true);

                    // Move to the next row after Mutasi name
                    $column++;

                    // Query for detailed Mutasi items for this Cabang
                    $cabangD = "SELECT * from inv_mutasi_cabang_detail a inner join inv_barang b on a.kodeitem = b.kodeitem where nomutasi_cabang = '$inventory->nomutasi_cabang' GROUP BY a.nobatch, a.kodeitem";
                    $lap1 = $db->query($cabangD)->getResult();
                    // Column Headers
                    $headers = ['NO', 'NO BUKTI', 'TANGGAL', 'NAMA BARANG', 'QTY'];
                    $sheet->fromArray($headers, NULL, 'A' . $column);
                    $sheet->getStyle('A' . $column . ':E' . $column)->getFont()->setBold(true);
                    $column++;

                    foreach ($lap1 as $index => $MutasiItem) {

                            $sheet->setCellValue('A' . $column, $index + 1);
                            $sheet->setCellValue('B' . $column, $MutasiItem->nomutasi_cabang);
                            $sheet->setCellValue('C' . $column, $inventory->tanggal);
                            $sheet->setCellValue('D' . $column, $MutasiItem->namabarang);
                            $sheet->setCellValue('E' . $column, $MutasiItem->qty);
                            // Increment row count
                            $column++;
                        }
                    }

                    // Incrementing row for next customer
                    $column++;

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'M') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set header for download
                $fileName = 'LAPORAN MUTASI REGION.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Save and output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Prevent further output
            }
            //LAPORAN PROFIT MARGIN
            else if ($idlp == 105) {
                $query =
                    "select * from inv_barang_harga where kodeitem is not null";
                if ($profit != "") {
                    $query .= " and profit_pr <= '$profit'";
                }
                if ($cab != "") {
                    $query .= " and cabang = '$cab'";
                }
                if ($brg != "") {
                    $query .= " and kodeitem = '$brg'";
                }
                if ($gud != "") {
                    $query .= " and gudang = '$gud'";
                }
                $query .= " order by profit_pr asc";
                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('Laporan Profit Margin');
                $sheet->setCellValue('A1', 'Laporan Profit Margin');
                $sheet->setCellValue('A2', $_peri2);
                // Column DetailData
                $headers = ['NO', 'NAMA BARANG', 'CUSTOMER', 'BRUTO', 'DISC', 'NETTO', 'PARTISI 1', 'PARTISI 2', 'PARTISI 3', 'PROFIT %'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                foreach ($lap as $key => $value) {
                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->nama_barang);
                    $sheet->setCellValue('C' . $column, $value->nama_customer);
                    $sheet->setCellValue('D' . $column, number_format($value->bruto_cust, 0, '.', ','));
                    $sheet->setCellValue('E' . $column, $value->diskon_pr.' %');
                    $sheet->setCellValue('F' . $column, number_format($value->netto_cust, 0, '.', ','));
                    $sheet->setCellValue('G' . $column, $value->fee1_pr.' %');
                    $sheet->setCellValue('H' . $column, $value->fee2_pr.' %');
                    $sheet->setCellValue('I' . $column, $value->fee3_pr.' %');
                    $sheet->setCellValue('J' . $column, number_format($value->profit_pr, 0, '.', ',').' %');
                    $column++;
                }

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'Laporan Profit Margin.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
			//LAPORAN DATA STOCK
            else if ($idlp == 106) {
                $query =
                    "SELECT
                        b.kodeitem,
                        b.kode_ereport,
                        b.namabarang,
                        b.satuan,
                        b.hargabeli,
                        b.stok_min,
                        b.stok_max,
                        b.kdmerk,
                        b.kdkategori,
                        b.ppn,
                        b.kdgudang,
                        b.kdcbg,
                        b.tglbeliakhir,
                        b.tgljualakhir,
                        b.kode_spl1,
                        b.kode_spl2,
                        b.kode_spl3,
                        b.kode_spl4,
                        b.bruto_spl1,
                        b.bruto_spl2,
                        b.bruto_spl3,
                        b.bruto_spl4,
                        b.disc_spl1,
                        b.disc_spl2,
                        b.disc_spl3,
                        b.disc_spl4,
                        b.netto_spl1,
                        b.netto_spl2,
                        b.netto_spl3,
                        b.netto_spl4,
                        COALESCE(s.stok, 0) AS stok,
                        COALESCE(so.qty_so, 0) AS qty_so,
                        COALESCE(ordr.qty_order, 0) AS qty_order,
                        COALESCE(jual.qty_jual, 0) AS qty_jual,
                        COALESCE(jual.hpp_jual, 0) AS hpp_jual,
                        COALESCE(jual.disc_jual, 0) AS disc_jual,
                        COALESCE(bh.netto_spl, 0) AS netto_spl,
                        g.nama AS gudang_nama,
                        m.nama AS merk_nama,
                        k.nama AS kategori_nama,
                        u.nama AS cabang_nama,
                        spl1.nama AS spl1_nama,
                        spl2.nama AS spl2_nama,
                        spl3.nama AS spl3_nama,
                        spl4.nama AS spl4_nama,
                        nb.tanggal_exp
                    FROM inv_barang b
                    LEFT JOIN (
                        SELECT kodeitem, kdcbg, kdgudang, SUM(stok) AS stok
                        FROM inv_barang
                        GROUP BY kodeitem, kdcbg, kdgudang
                    ) s ON s.kodeitem = b.kodeitem AND s.kdcbg = b.kdcbg AND s.kdgudang = b.kdgudang
                    LEFT JOIN (
                        SELECT kodeitem, cabang, gudang, SUM(qty_stok) AS qty_so
                        FROM inv_sodetail
                        GROUP BY kodeitem, cabang, gudang
                    ) so ON so.kodeitem = b.kodeitem AND so.cabang = b.kdcbg AND so.gudang = b.kdgudang
                    LEFT JOIN (
                        SELECT kodeitem, cabang, gudang, SUM(qtyorder) AS qty_order
                        FROM ar_sodetail
                        GROUP BY kodeitem, cabang, gudang
                    ) ordr ON ordr.kodeitem = b.kodeitem AND ordr.cabang = b.kdcbg AND ordr.gudang = b.kdgudang
                    LEFT JOIN (
                        SELECT kodeitem, gudang, SUM(qtysi) AS qty_jual, SUM(hargajual) AS hpp_jual, SUM(disc) AS disc_jual
                        FROM ar_sidetail
                        GROUP BY kodeitem, gudang
                    ) jual ON jual.kodeitem = b.kodeitem AND jual.gudang = b.kdgudang
                    LEFT JOIN (
                        SELECT kodeitem, cabang, gudang, MAX(netto_spl) AS netto_spl
                        FROM inv_barang_harga
                        GROUP BY kodeitem, cabang, gudang
                    ) bh ON bh.kodeitem = b.kodeitem AND bh.cabang = b.kdcbg AND bh.gudang = b.kdgudang
                    LEFT JOIN inv_gudang g ON g.kode = b.kdgudang
                    LEFT JOIN inv_merk m ON m.kode = b.kdmerk
                    LEFT JOIN inv_kategori k ON k.kode = b.kdkategori
                    LEFT JOIN ms_unit u ON u.kode = b.kdcbg
                    LEFT JOIN ap_supplier spl1 ON spl1.kode = b.kode_spl1
                    LEFT JOIN ap_supplier spl2 ON spl2.kode = b.kode_spl2
                    LEFT JOIN ap_supplier spl3 ON spl3.kode = b.kode_spl3
                    LEFT JOIN ap_supplier spl4 ON spl4.kode = b.kode_spl4
                    LEFT JOIN (
                        SELECT kodeitem, MAX(tanggal_exp) AS tanggal_exp
                        FROM inv_nobatch
                        GROUP BY kodeitem
                    ) nb ON nb.kodeitem = b.kodeitem
                    WHERE b.kodeitem IS NOT NULL";

                if ($kat != "") {
                    $query .= " AND b.kdkategori = '$kat'";
                }
                if ($cab != "") {
                    $query .= " AND b.kdcbg = '$cab'";
                }
                if ($brg != "") {
                    $query .= " AND b.kodeitem = '$brg'";
                }
                if ($gud != "") {
                    $query .= " AND b.kdgudang = '$gud'";
                }
                $query .= " ORDER BY b.namabarang";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN DATA STOCK');
                $sheet->setCellValue('A1', 'LAPORAN DATA BARANG');
                // $sheet->setCellValue('A2', $_peri);
                // Column DetailData
                $headers = ['KODE STOCK', 'KODE E-REPORT', 'NAMA STOCK', 'SATUAN', 'STOCK AWAL', 'STOCK AKHIR', 'STOCK ORDER',
                            'HARGA BELI', 'NILAI STOCK', 'STOCK MINIMUM', 'STOCK MAXIMUM', 'MERK', 'KATEGORI', 'PPN', 'GUDANG',
                            'TANGGAL BELI', 'TANGGAL EXP', 'TANGGAL JUAL', 'REGION', 'QTY KERJUAL', 'LABA TERJUAL', 'Supplier 1', 'Supplier 2',
                            'Supplier 3', 'Supplier 4', 'BRUTO 1', 'BRUTO 2', 'BRUTO 3', 'BRUTO 4', 'DISC 1', 'DISC 2','DISC 3', 'DISC 4',
                            'NETTO 1', 'NETTO 2','NETTO 3', 'NETTO 4'];
                $sheet->fromArray($headers, NULL, 'A2');
                $sheet->getStyle('A1:AK2')->getFont()->setBold(true);
                $sheet->mergeCells('A1:AK1');
                $sheet->getStyle('A1:A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $rowNum = 3; // Mulai menulis data dari baris ke-3

                foreach ($lap as $key => $row) {
                    $qty_so    = (float) ($row->qty_so ?? 0);
                    $stok      = (float) ($row->stok ?? 0);
                    $qty_order = (float) ($row->qty_order ?? 0);
                    $qty_jual  = (float) ($row->qty_jual ?? 0);

                    $dpp = $qty_jual * ((float) ($row->hpp_jual ?? 0));
                    $laba = $dpp * (((float) ($row->disc_jual ?? 0)) / 100);

                    $netto_spl_val = (float) ($row->netto_spl ?? 0);
                    $nilai_stock   = $netto_spl_val * $stok;

                    $data = [
                        $row->kodeitem,
                        $row->kode_ereport,
                        $row->namabarang,
                        $row->satuan,
                        $qty_so,
                        $stok,
                        $qty_order,
                        $row->hargabeli,
                        $nilai_stock,
                        $row->stok_min,
                        $row->stok_max,
                        $row->merk_nama ?? '-',
                        $row->kategori_nama ?? '-',
                        $row->ppn,
                        $row->gudang_nama ?? '-',
                        $row->tglbeliakhir ? date('d-M-y', strtotime($row->tglbeliakhir)) : '-',
                        $row->tanggal_exp ? date('d-M-y', strtotime($row->tanggal_exp)) : '-',
                        $row->tgljualakhir ? date('d-M-y', strtotime($row->tgljualakhir)) : '-',
                        $row->cabang_nama ?? '-',
                        $qty_jual,
                        $laba,
                        $row->spl1_nama ?? '-',
                        $row->spl2_nama ?? '-',
                        $row->spl3_nama ?? '-',
                        $row->spl4_nama ?? '-',
                        $row->bruto_spl1,
                        $row->bruto_spl2,
                        $row->bruto_spl3,
                        $row->bruto_spl4,
                        $row->disc_spl1,
                        $row->disc_spl2,
                        $row->disc_spl3,
                        $row->disc_spl4,
                        $row->netto_spl1,
                        $row->netto_spl2,
                        $row->netto_spl3,
                        $row->netto_spl4
                    ];

                    $sheet->fromArray($data, NULL, 'A' . $rowNum);
                    
                    $rowNum++; 
                }

                $highestRow = $rowNum - 1;

                if ($highestRow >= 3) {
                    // Format Ribuan untuk Kolom Angka (Kolom E sampai K) -> Qty SO, Stok, Qty Order, Harga Beli, Nilai Stock, Stok Min, Stok Max
                    $sheet->getStyle('E3:K' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

                    // Format Ribuan untuk Qty Jual & Laba (Kolom T dan U)
                    $sheet->getStyle('T3:U' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

                    // Format Ribuan untuk Detail Supplier (Kolom Z sampai AK) -> Bruto 1 hingga Netto 4
                    $sheet->getStyle('Z3:AK' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

                    // --- 5. AUTOMATIS KAN LEBAR KOLOM UNTUK SEMUA KOLOM (A sampai AK) ---
                    foreach (range('A', 'Z') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true);
                    }
                    // Lanjutan kolom setelah Z (AA sampai AK)
                    foreach (range('A', 'K') as $columnID) {
                        $sheet->getColumnDimension('A' . $columnID)->setAutoSize(true);
                    }
                }

                // Tentukan nama file
                $fileName = 'LAPORAN DATA STOCK.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            
            }
        }
    }
}
