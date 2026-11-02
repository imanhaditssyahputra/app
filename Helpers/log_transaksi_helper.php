<?php

if (!function_exists('audit_log_detail')) {
    /**
     * @param string $no_bukti         Nomor transaksi (SO, PO, dll)
     * @param string $jenis_transaksi   Nama transaksi ('SO', 'INVOICE')
     * @param array $kolom_diperiksa    Kolom yang dipantau (termasuk kodeitem)
     * @param array|null $old_data      Data lama dari DB (berdasarkan ID detail)
     * @param array $new_data          Data baru dari input
     */
    function audit_log_detail(string $no_bukti, string $jenis_transaksi, array $kolom_diperiksa, ?array $old_data, array $new_data)
    {
        $db = \Config\Database::connect();
        $builderLog = $db->table('log_detail_transaksi');
        $session = \Config\Services::session();
        $userId = $session->get('username') ?? 0;

        // 1. KASUS INSERT (Baris Baru)
        if (empty($old_data)) {
            $builderLog->insert([
                'nobukti'         => $no_bukti,
                'jenis_transaksi' => $jenis_transaksi,
                'kodeitem_lama'   => null,
                'kodeitem_baru'   => $new_data['kodeitem'] ?? '',
                'aksi'            => 'INSERT_' . $jenis_transaksi,
                'kolom'           => 'ALL',
                'nilai_lama'      => null,
                'nilai_baru'      => json_encode($new_data),
                'user_id'         => $userId
            ]);
            return;
        }else{
            $builderLog->insert([
                'nobukti'          => $no_bukti,
                'jenis_transaksi' => $jenis_transaksi,
                // Tetap catat kodeitem saat ini untuk kemudahan tracking
                'kodeitem_lama'   => $old_data['kodeitem'] ?? '', 
                'kodeitem_baru'   => $new_data['kodeitem'] ?? '',
                'aksi'            => 'UPDATE_' . $jenis_transaksi,
                'kolom'           => json_encode($new_data),
                'nilai_lama'      => json_encode($new_data),
                'nilai_baru'      => json_encode($new_data),
                'user_id'         => $userId
            ]);
        }
    }
}
