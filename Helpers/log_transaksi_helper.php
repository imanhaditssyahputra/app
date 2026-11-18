
<?php

if (!function_exists('audit_log_detail')) {

    function audit_log_detail(
        string $no_bukti,
        string $jenis_transaksi,
        array $kolom_diperiksa,
        ?array $old_data,
        ?array $new_data
    ) {
        $db = \Config\Database::connect();
        $builderLog = $db->table('log_detail_transaksi');

        $session = \Config\Services::session();
        $userId = $session->get('username') ?? 0;

        // 1. INSERT
        if (empty($old_data) && !empty($new_data)) {

            $builderLog->insert([
                'nobukti'          => $no_bukti,
                'jenis_transaksi'  => $jenis_transaksi,
                'kodeitem_lama'    => null,
                'kodeitem_baru'    => $new_data['kodeitem'] ?? '',
                'aksi'             => 'INSERT_' . $jenis_transaksi,
                'kolom'            => 'ALL',
                'nilai_lama'       => null,
                'nilai_baru'       => json_encode($new_data),
                'user_id'          => $userId,
                'cabang'           => $session->get('cabang') ?? null,
            ]);

            return;
        }

        // 2. DELETE
        if (!empty($old_data) && empty($new_data)) {

            $builderLog->insert([
                'nobukti'          => $no_bukti,
                'jenis_transaksi'  => $jenis_transaksi,
                'kodeitem_lama'    => $old_data['kodeitem'] ?? '',
                'kodeitem_baru'    => null,
                'aksi'             => 'DELETE_' . $jenis_transaksi,
                'kolom'            => 'ALL',
                'nilai_lama'       => json_encode($old_data),
                'nilai_baru'       => null,
                'user_id'          => $userId,
                'cabang'           => $session->get('cabang') ?? null,
            ]);

            return;
        }

        // 3. UPDATE
        if (!empty($old_data) && !empty($new_data)) {

            $perubahan = [];

            foreach ($kolom_diperiksa as $kolom) {

                $nilaiLama = $old_data[$kolom] ?? null;
                $nilaiBaru = $new_data[$kolom] ?? null;

                if ((string)$nilaiLama !== (string)$nilaiBaru) {
                    $perubahan[$kolom] = [
                        'lama' => $nilaiLama,
                        'baru' => $nilaiBaru
                    ];
                }
            }

            // Tidak ada perubahan pada kolom yang dipantau
            if (empty($perubahan)) {
                return;
            }

            $builderLog->insert([
                'nobukti'          => $no_bukti,
                'jenis_transaksi'  => $jenis_transaksi,
                'kodeitem_lama'    => $old_data['kodeitem'] ?? '',
                'kodeitem_baru'    => $new_data['kodeitem'] ?? '',
                'aksi'             => 'UPDATE_' . $jenis_transaksi,
                'kolom'            => json_encode(array_keys($perubahan)),
                'nilai_lama'       => json_encode($old_data),
                'nilai_baru'       => json_encode($new_data),
                'user_id'          => $userId,
                'cabang'           => $session->get('cabang') ?? null,
            ]);
        }
    }
}
