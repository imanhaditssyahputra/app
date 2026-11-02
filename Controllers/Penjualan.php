<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Penjualan
 **/

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_faktur_helper;
use App\Helpers\simkeu_rpt_helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Penjualan extends BaseController
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

	private function hitung_waktu_lampau($datetime) {
		// Pastikan zona waktu sinkron dengan lokasi Anda
		date_default_timezone_set('Asia/Jakarta'); 
		
		$waktu_log = strtotime($datetime);
		$waktu_sekarang = time();
		$selisih_detik = $waktu_sekarang - $waktu_log;
		
		// Antispasi jika ada selisih waktu sistem yang keliru (waktu masa depan)
		if ($selisih_detik < 1) {
			return 'Just now';
		}
		
		// Definisikan aturan konversi waktu dalam detik
		$aturan_waktu = [
			31536000 => ['year', 'a year'],
			2592000  => ['month', 'a month'],
			86400    => ['day', '1 day'],
			3600     => ['hour', '1 hour'],
			60       => ['minute', '1 minute']
		];
		
		foreach ($aturan_waktu as $detik => $info) {
			$pembagi = $selisih_detik / $detik;
			
			if ($pembagi >= 1) {
				$hasil_bulat = round($pembagi);
				$nama_satuan = $info[0];
				$sebutan_tunggal = $info[1];
				
				// Jika hasilnya tepat 1 (Tunggal), gunakan format "a month", "a year", dll.
				if ($hasil_bulat == 1) {
					return $sebutan_tunggal . ' ago';
				} 
				// Jika hasil lebih dari 1 (Jamak), tambahkan akhiran 's' (e.g., 20 minutes ago, 26 days ago)
				else {
					return $hasil_bulat . ' ' . $nama_satuan . 's ago';
				}
			}
		}
		
		return 'Just now';
	}

	// public function getMessageLog($data)
	// {
	// 	$model_Global =  model('Global_model', 'M_GLOBAL');
	// 	$session = session();
	// 	$level = $session->get('level');
	// 	$cabang = $session->get('cabang');
	// 	$db = db_connect();
		
	// 	$query = $db->table('logs')
    //         ->like('detail', $data)
    //         ->orderBy('date', 'ASC')
    //         ->orderBy('id', 'ASC')
    //         ->get()
    //         ->getResult();
		
	// 	$html = '';
	// 	$tanggal_terakhir = ''; // Variabel penanda untuk melacak tanggal sebelumnya

	// 	foreach ($query as $value) {
	// 		$Datauser = $db->table('userlogin')->getWhere(['uidlogin' => $value->userid])->getRow();
			
	// 		// Ambil format tanggal saja (tanpa jam) untuk pembanding
	// 		$tanggal_sekarang = date('Y-m-d', strtotime($value->date));

	// 		// JIKA TANGGAL BERBEDA DENGAN LOG SEBELUMNYA, CETAK GARIS TANGGAL BARU
	// 		if ($tanggal_sekarang != $tanggal_terakhir) {
	// 			$html .= '<div class="text-center my-4 position-relative">
	// 						<hr class="position-absolute top-50 start-0 end-0 m-0 z-0">
	// 						<span class="bg-light px-3 text-secondary fw-bold small position-relative z-1">' . date('d-M-Y', strtotime($value->date)) . '</span>
	// 					</div>';
				
	// 			// Perbarui penanda dengan tanggal yang baru saja dicetak
	// 			$tanggal_terakhir = $tanggal_sekarang; 
	// 		}
					
	// 		$html .= '<div class="d-flex align-items-start mb-3 text-start">
	// 					<div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0;"> 
	// 						<img class="rounded-circle header-profile-user" src="' . BASEURL . 'assets/puser/' . $Datauser->avatar . '" alt="Header Avatar"> 
	// 					</div>

	// 					<div>
	// 						<div class="small">
	// 							<span class="fw-bold text-dark">'.$value->userid.'</span>
	// 							<span class="text-muted ms-1">'.date('d-M-Y H:i:s', strtotime($value->date)).'</span>
	// 						</div>
	// 						<div class="text-secondary small mt-1">
	// 							<ul class="list-unstyled mb-0 ps-0">
	// 								<li>'.$value->detail.'</li>
	// 							</ul>
	// 						</div>
	// 					</div>
	// 				</div>';
	// 	}

	// 	// CETAK SELURUH HTML SEKALIGUS DI LUAR LOOPING FOREACH
	// 	echo $html; 
	// }

	public function getMessageLog($data)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$cabang = $session->get('cabang');
		$db = db_connect();
		
		$query = $db->table('log_detail_transaksi')
			->where('nobukti', $data)
			->orderBy('created_at', 'ASC')
			->orderBy('id', 'ASC')
			->get()
			->getResult();
		
		// --- HELPER FUNCTION UNTUK PARSING STRING LOG SECARA AKURAT ---
		$parseLogData = function($string) {
			$fields = ['kodeitem', 'qtyorder', 'satuan', 'hargajual', 'disc'];
			$result = [];
			
			$clean_str = trim($string, '[] ');
			$pairs = explode(',', $clean_str);
			
			foreach ($pairs as $pair) {
				if (strpos($pair, ':') !== false) {
					list($key, $value) = explode(':', $pair, 2);
					$key = preg_replace('/[^a-zA-Z0-9_]/', '', trim($key));
					$value = trim($value, ' "'); 
					
					if (in_array($key, $fields)) {
						$result[$key] = $value;
					}
				}
			}
			return $result;
		};
		// --------------------------------------------------------------

		$history_items = [];

		// === TAHAP 1: KELOMPOKKAN DATA BERDASARKAN USER, MENIT, DAN AKSI ===
		$grouped_logs = [];
		foreach ($query as $value) {
			$tipe_aksi = (stripos($value->aksi, 'INSERT') !== false) ? 'INSERT' : 'UPDATE';
			
			$waktu_menit = date('Y-m-d H:i', strtotime($value->created_at));
			$group_key = $waktu_menit . '_' . $value->user_id . '_' . $tipe_aksi;

			$data_baru = $parseLogData($value->nilai_baru);
			$kode_item_aktif = $data_baru['kodeitem'] ?? 'UNKNOWN';

			$data_lama_asli = [];
			$is_new_item_in_update = false; // Flag penanda jika item baru ditambahkan saat proses update

			if ($tipe_aksi === 'UPDATE') {
				if (isset($history_items[$kode_item_aktif])) {
					$data_lama_asli = $history_items[$kode_item_aktif];
				} else {
					// Jika di history tidak ada, dan nilai_lama bawaan DB kosong/null/tidak valid
					$db_lama = $parseLogData($value->nilai_lama);
					if (empty($db_lama) || !isset($db_lama['kodeitem']) || $db_lama['kodeitem'] == '' || $db_lama['kodeitem'] == 'NULL') {
						$is_new_item_in_update = true;
					} else {
						$data_lama_asli = $db_lama;
					}
				}
			}

			if (!empty($data_baru)) {
				$history_items[$kode_item_aktif] = $data_baru;
			}

			if (!isset($grouped_logs[$group_key])) {
				$grouped_logs[$group_key] = [
					'created_at' => $value->created_at, 
					'user_id'    => $value->user_id,
					'aksi'       => $tipe_aksi,
					'items'      => []
				];
			}

			$grouped_logs[$group_key]['items'][] = [
				'data_lama'             => $data_lama_asli,
				'data_baru'             => $data_baru,
				'is_new_item_in_update' => $is_new_item_in_update,
				'kolom'                 => $value->kolom
			];
		}

		// === TAHAP 2: GENERATE HTML DARI DATA YANG SUDAH DIGABUNG ===
		$html = '';
		$tanggal_terakhir = ''; 

		foreach ($grouped_logs as $log) {
			$Datauser = $db->table('userlogin')->getWhere(['uidlogin' => $log['user_id']])->getRow();
			$tanggal_sekarang = date('Y-m-d', strtotime($log['created_at']));

			if ($tanggal_sekarang != $tanggal_terakhir) {
				$html .= '<div class="text-center my-4 position-relative">
							<hr class="position-absolute top-50 start-0 end-0 m-0 z-0">
							<span class="bg-light px-3 text-secondary fw-bold small position-relative z-1">' . date('d-M-Y', strtotime($log['created_at'])) . '</span>
						</div>';
				$tanggal_terakhir = $tanggal_sekarang; 
			}
					
			$keterangan_detail = '';

			if ($log['aksi'] === 'INSERT') {
				$keterangan_detail = '<div class="text-success fw-bold mb-1"><i class="fas fa-plus-circle"></i> Menambahkan ' . count($log['items']) . ' data baru:</div>';
				$keterangan_detail .= '<table class="table table-sm table-bordered text-dark bg-white shadow-sm mb-0" style="max-width: 650px; font-size: 11px;">
										<thead class="table-light text-center">
											<tr>
												<th width="40%">Item</th><th width="15%">Qty</th><th width="15%">Satuan</th><th width="15%">Harga</th><th width="15%">Disc</th>
											</tr>
										</thead>
										<tbody>';
				
				foreach ($log['items'] as $item) {
					$data_baru = $item['data_baru'];
					$keterangan_detail .= '<tr>
											<td>' . ($data_baru['kodeitem'] ?? '-') . '</td>
											<td class="text-center">' . ($data_baru['qtyorder'] ?? '0') . '</td>
											<td class="text-center">' . ($data_baru['satuan'] ?? '-') . '</td>
											<td class="text-end">' . number_format((float)($data_baru['hargajual'] ?? 0), 0, ',', '.') . '</td>
											<td class="text-center">' . ($data_baru['disc'] ?? '0') . '%</td>
										</tr>';
				}
				$keterangan_detail .= '</tbody></table>';

			} else if ($log['aksi'] === 'UPDATE') {
				$keterangan_detail = '<div class="text-warning fw-bold mb-1"><i class="fas fa-edit"></i> Mengubah / Memperbarui ' . count($log['items']) . ' data:</div>';
				$keterangan_detail .= '<table class="table table-sm table-bordered text-dark bg-white shadow-sm mb-0" style="max-width: 650px; font-size: 11px;">
										<thead class="table-light text-center">
											<tr>
												<th width="15%">Status</th><th width="35%">Item</th><th width="12%">Qty</th><th width="12%">Satuan</th><th width="13%">Harga</th><th width="13%">Disc</th>
											</tr>
										</thead>
										<tbody>';
				
				foreach ($log['items'] as $item) {
					$data_lama = $item['data_lama'];
					$data_baru = $item['data_baru'];
					
					// JIKA ITEM INI BARU DITAMBAHKAN PADA SAAT UPDATE PESANAN
					if ($item['is_new_item_in_update']) {
						$keterangan_detail .= '<tr class="table-success fw-bold">
												<td class="text-center text-success"><i class="fas fa-plus"></i> Baru</td>
												<td>' . ($data_baru['kodeitem'] ?? '-') . '</td>
												<td class="text-center">' . ($data_baru['qtyorder'] ?? '0') . '</td>
												<td class="text-center">' . ($data_baru['satuan'] ?? '-') . '</td>
												<td class="text-end">' . number_format((float)($data_baru['hargajual'] ?? 0), 0, ',', '.') . '</td>
												<td class="text-center">' . ($data_baru['disc'] ?? '0') . '%</td>
											</tr>';
					} else {
						// JIKA MEMANG ITEM LAMA YANG DI-UPDATE NILAINYA
						$keterangan_detail .= '<tr class="table-danger text-muted text-decoration-line-through">
												<td class="text-center">Semula</td>
												<td>' . ($data_lama['kodeitem'] ?? '-') . '</td>
												<td class="text-center">' . ($data_lama['qtyorder'] ?? '0') . '</td>
												<td class="text-center">' . ($data_lama['satuan'] ?? '-') . '</td>
												<td class="text-end">' . number_format((float)($data_lama['hargajual'] ?? 0), 0, ',', '.') . '</td>
												<td class="text-center">' . ($data_lama['disc'] ?? '0') . '%</td>
											</tr>';
						$keterangan_detail .= '<tr class="table-primary fw-bold">
												<td class="text-center">Menjadi</td>
												<td>' . ($data_baru['kodeitem'] ?? '-') . '</td>
												<td class="text-center">' . ($data_baru['qtyorder'] ?? '0') . '</td>
												<td class="text-center">' . ($data_baru['satuan'] ?? '-') . '</td>
												<td class="text-end">' . number_format((float)($data_baru['hargajual'] ?? 0), 0, ',', '.') . '</td>
												<td class="text-center">' . ($data_baru['disc'] ?? '0') . '%</td>
											</tr>';
					}
				}
				$keterangan_detail .= '</tbody></table>';
			}

			$html .= '<div class="d-flex align-items-start mb-4 text-start">
						<div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0;"> 
							<img class="rounded-circle header-profile-user" src="' . BASEURL . 'assets/puser/' . ($Datauser->avatar ?? 'default.png') . '" alt="Header Avatar"> 
						</div>

						<div class="w-100">
							<div class="small">
								<span class="fw-bold text-dark">' . ($log['user_id'] ?? 'Unknown') . '</span>
								<span class="text-muted ms-1">' . date('d-M-Y H:i:s', strtotime($log['created_at'])) . '</span>
							</div>
							<div class="text-secondary small mt-1">
								' . $keterangan_detail . '
							</div>
						</div>
										</div>';
		}
		echo $html;
	}


	//Data Customer Page Start Here
	public function data_customer()
	{

		$session = session();
		$db = db_connect();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$dataLogin = $model_Global->_Autono('CS');
		$akses = $model_Global->cek_menu_akses($level, 412);
		$DataJenisIdCS = $db->table('coretax_jenisidcs')->get()->GetResult();
		$DataNegara = $db->table('coretax_negara')->get()->GetResult();
		if ($akses->uadd == 1) {
			$tambah = 'Data Baru';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Data Customer',
			'body' => 'admin/penjualan/datacustomer',
			'nourut' => $dataLogin,
			'tambah' => $tambah,
			'DataJenisIdCS' => $DataJenisIdCS,
			'DataNegara' => $DataNegara
		];
		return view('layout/layout', $data);
	}

	public function data_customer_add()
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$validation =  \Config\Services::validation();
		$input = $this->request->getPost();


		$builder = $db->table('ar_customer');
		$GetMarketing = $model_Global->GetMarketing($input['sales']);
		$validation->setRule('tipe', 'Telp', 'required');
		$validation->setRule('kredit', 'Kredit', 'required');
		$validation->setRule('alamat1', 'Alamat 1', 'required');
		$validation->setRule('npwp', 'NPWP', 'required');
		$validation->setRule('bataskredit', 'BatasKredit', 'required');
		$validation->setRule('cabang', 'Cabang', 'required');

		if ($validation->withRequest($this->request)->run()) {
			try {
				$db->transStart();
				$BtsKredit = str_replace('.', '', $input['bataskredit']);
				$data = [
					'kode_ereport' => $input['kodeereport'],
					'kode' => $input['kode'],
					'nama' => $input['nama'],
					'kode_cabang' => $input['cabang'],
					'alamat1' => $input['alamat1'],
					'alamat2' => $input['alamat2'],
					'kota' => $input['kota'],
					'kodepos' => $input['kodepos'],
					'telp' => $input['telp'],
					'fax' => $input['fax'],
					'hp' => $input['hp'],
					'contactname' => $input['contactname'],
					'email' => $input['email'],
					'nama_pt' => $input['namapt'],
					'top' => $input['top'],
					'kode_user' => $session->get('username'),
					'tglentry' => date('Y-m-d H:i:s'),
					'npwp' => $input['npwp'],
					'ktp' => $input['ktp'],
					'tipe' => $input['tipe'],
					'kredit' => $input['kredit'],
					'bataskredit' => $BtsKredit,
					'saldokredit' => $BtsKredit,
					'kodesales' => $input['sales'],
					'nama_marketing' => $GetMarketing,
					'ppn' => $input['sppn'],
					'taxjenisidcs' => $input['taxjenisidcs'],
					'taxnegaracs' => $input['taxnegaracs'],
					'nojenisid' => $input['nojenisid'],
				];
				$datalogs = [
					'detail' => 'tambah data Customer dengan kode ' . $input['kode'],
					'nobukti' => $input['kode'],
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				$result = $builder->insert($data);
				$model_Global->_updatecounter1('CS');
				if ($result) {
					$db->table("logs")->insert($datalogs);

					echo json_encode(['message' => 'Data Berhasil Di Tambah']);
				}
				$db->transComplete();
			} catch (\Exception $e) {
				echo json_encode(['message' => $e->getMessage()]);
			}
		} else {
			$arr = [
				'error' => true,
				'tipeErr' => $validation->showError('tipe'),
				'kreditErr' => $validation->showError('kredit'),
				'alamat1Err' => $validation->showError('alamat1'),
				'npwpErr' => $validation->showError('npwp'),
				'bataskreditErr' => $validation->showError('bataskredit'),
				'cabangErr' => $validation->showError('cabang'),

			];
			echo json_encode($arr);
		}
	}

	public function data_customer_table()
	{
		$session = session();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$akses = $model_Global->cek_menu_akses($level, 412);
		$db = db_connect();
		$res = $db->table('ar_customer')->get()->getResult();
		$result = array();
		$data = [];
		foreach ($res as $value) {
			if ($akses->udel == 1 && $akses->uedit == 1) {
				$editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_customer_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
				$delete = '<button class="btn btn-danger mb-2" onclick="data_customer_delete(' . "'" . $value->id . "'" . ',' . "'" . $value->nama . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				$delete = '<button class="btn btn-danger mb-2" onclick="data_customer_delete(' . "'" . $value->id . "'" . ',' . "'" . $value->nama . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
				$editview = '';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_customer_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
				$delete = '';
			} else {
				$editview = '';
				$delete = '';
			}
			$data[] = [
				$value->kode,
				$value->nama,
				$value->nama_pt,
				$value->alamat1,
				$value->telp,
				$value->contactname,
				$value->top,
				number_format($value->saldokredit),
				$editview . $delete,
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function data_customer_edit($id)
	{
		$db = db_connect();
		$builder = $db->table('ar_customer')->where('id', $id)->get()->getRow();
		echo json_encode($builder);
	}

	public function data_customer_update($id)
	{
		$validation =  \Config\Services::validation();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$db = db_connect();

		$builder = $db->table('ar_customer');
		$input = $this->request->getPost();
		$GetMarketing = $model_Global->GetMarketing($input['sales']);
		$validation->setRule('tipe', 'Telp', 'required');
		$validation->setRule('kredit', 'Nama', 'required');
		$validation->setRule('alamat1', 'Alamat 1', 'required');
		$validation->setRule('npwp', 'NPWP', 'required');
		$validation->setRule('bataskredit', 'BatasKredit', 'required');
		$validation->setRule('cabang', 'Cabang', 'required');

		$data_customer = $db->query("select * from ar_customer where kode='" . $input['kode'] . "'")->getRow();
		$BtsKredit = str_replace('.', '', $input['bataskredit']);
		$BtsKredit = str_replace(',', '', $BtsKredit);
		if ($BtsKredit == '0' || empty($BtsKredit)) {
			$saldokredit		= 0;
		} else if ($data_customer->bataskredit > $BtsKredit) {
			$hitung_bts_kredit 	= $data_customer->bataskredit - $BtsKredit;
			$saldokredit		= $data_customer->saldokredit - $hitung_bts_kredit;
		} else if ($data_customer->bataskredit < $BtsKredit) {
			$hitung_bts_kredit 	= $BtsKredit - $data_customer->bataskredit;
			$saldokredit		= $data_customer->saldokredit + $hitung_bts_kredit;
		} else {
			$saldokredit		= $data_customer->saldokredit;
		}

		if ($validation->withRequest($this->request)->run()) {
			try {
				$db->transStart();
				$data = [
					'kode_ereport' => $input['kodeereport'],
					'kode' => $input['kode'],
					'nama' => $input['nama'],
					'kode_cabang' => $input['cabang'],
					'alamat1' => $input['alamat1'],
					'alamat2' => $input['alamat2'],
					'kota' => $input['kota'],
					'kodepos' => $input['kodepos'],
					'telp' => $input['telp'],
					'fax' => $input['fax'],
					'hp' => $input['hp'],
					'contactname' => $input['contactname'],
					'email' => $input['email'],
					'nama_pt' => $input['namapt'],
					'top' => $input['top'],
					'kode_user' => $session->get('username'),
					'tglentry' => date('Y-m-d H:i:s'),
					'npwp' => $input['npwp'],
					'ktp' => $input['ktp'],
					'tipe' => $input['tipe'],
					'kredit' => $input['kredit'],
					'bataskredit' => $BtsKredit,
					'saldokredit' => $saldokredit,
					'kodesales' => $input['sales'],
					'nama_marketing' => $GetMarketing,
					'ppn' => $input['sppn'],
					'taxjenisidcs' => $input['taxjenisidcs'],
					'taxnegaracs' => $input['taxnegaracs'],
					'nojenisid' => $input['nojenisid'],
				];

				$datalogs = [
					'detail' => 'Update data Customer dengan kode ' . $input['kode'],
					'nobukti' => $input['kode'],
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

				$result = $builder->where('id', $id)->update($data);
				if ($result) {
					$db->table("logs")->insert($datalogs);
					echo json_encode(['message' => 'Data Berhasil Di Update']);
				}
				$db->transComplete();
			} catch (\Exception $e) {
				echo json_encode(['message' => $e->getMessage()]);
			}
		} else {
			$arr = [
				'error' => true,
				'tipeErr' => $validation->showError('tipe'),
				'kreditErr' => $validation->showError('kredit'),
				'alamat1Err' => $validation->showError('alamat1'),
				'npwpErr' => $validation->showError('npwp'),
				'bataskreditErr' => $validation->showError('bataskredit'),
				'cabangErr' => $validation->showError('cabang'),

			];
			echo json_encode($arr);
		}
	}

	public function data_customer_delete($id)
	{
		$db = db_connect();
		$session = session();
		$builder = $db->table('ar_customer');
		$Row = $builder->getWhere(['id' => $id])->getRow();
		$datalogs = [
			'detail' => 'Hapus data Customer dengan kode ' . $Row->kode,
			'date' => date('Y-m-d H:i:s'),
			'userid' => $session->get('username'),
		];
		$delete = $builder->delete(['id' => $id]);
		if ($delete) {
			$db->table("logs")->insert($datalogs);
			echo json_encode(['message' => 'Deleted']);
		}
	}

	//Daftar Marketing Page Start Here
	public function daftar_marketing()
	{
		$session = session();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$akses = $model_Global->cek_menu_akses($level, 411);
		if ($akses->uadd == 1) {
			$tambah = 'Data Baru';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Daftar Marketing',
			'body' => 'admin/penjualan/daftarmarketing',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	// Add & Update 
	public function marketing_add($id = '')
	{
		$validation =  \Config\Services::validation();
		$db = db_connect();
		$session = session();
		$builder = $db->table('ar_sales');
		$input = $this->request->getPost();
		$validation->setRule('nama', 'Nama', 'required');
		$validation->setRule('telp', 'Telpon', 'required');
		$validation->setRule('area', 'Area', 'required');

		if ($validation->withRequest($this->request)->run()) {
			try {
				$db->transStart();
				$data = [
					'nama_sales' => $input['nama'],
					'telpon' => $input['telp'],
					'area' => $input['area'],
					'usersales' => $input['usersales'],
				];
				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				if ($id == 0) {
					$datalogs['detail'] = 'Tambah data Marketing dengan nama ' . $input['nama'];
					$result = $builder->insert($data);
					if ($result) {
						$db->table("logs")->insert($datalogs);
						echo json_encode(['message' => 'Data Berhasil di Tambah']);
					}
				} else {
					$datalogs['detail'] = 'Update data Marketing dengan nama ' . $input['nama'];
					$result = $builder->where('id', $id)->update($data);
					if ($result) {
						$db->table("logs")->insert($datalogs);
						echo json_encode(['message' => 'Data Berhasil Di Update']);
					}
				}
				$db->transComplete();
			} catch (\Exception $e) {
				echo json_encode(['message' => $e->getMessage()]);
			}
		} else {
			$arr = [
				'error' => true,
				'namaErr' => $validation->showError('nama'),
				'telpErr' => $validation->showError('telp'),
				'areaErr' => $validation->showError('area'),

			];
			echo json_encode($arr);
		}
	}

	public function daftar_marketing_table()
	{
		$session = session();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$akses = $model_Global->cek_menu_akses($level, 411);
		$db = db_connect();
		$res = $db->table('ar_sales')->get()->getResult();
		$result = array();
		$data = [];
		foreach ($res as $value) {
			if ($akses->udel == 1 && $akses->uedit == 1) {
				$editview = '<button class="btn btn-primary me-2 mb-2" onclick="daftar_marketing_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
				$delete = '<button class="btn btn-danger mb-2" onclick="daftar_marketing_delete(' . $value->id . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				$delete = '<button class="btn btn-danger mb-2" onclick="daftar_marketing_delete(' . $value->id . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
				$editview = '';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$editview = '<button class="btn btn-primary me-2 mb-2" onclick="daftar_marketing_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
				$delete = '';
			} else {
				$editview = '';
				$delete = '';
			}
			 $userdata = $db->table('userlogin')->getWhere(['uidlogin' => $value->usersales,])->getRow();
			$data[] = [
				$value->nama_sales,
				$value->telpon,
				$value->area,
				($userdata)?$userdata->username: '',
				$editview . $delete
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function marketing_edit($id)
	{
		$db = db_connect();
		$builder = $db->table('ar_sales')->where('id', $id)->get()->getRow();
		echo json_encode($builder);
	}

	public function daftar_marketing_delete($id)
	{
		$db = db_connect();
		$session = session();
		$builder = $db->table('ar_sales');
		$Row = $builder->getWhere(['id' => $id])->getRow();
		$datalogs = [
			'detail' => 'Hapus data Marketing dengan nama ' . $Row->nama_sales,
			'date' => date('Y-m-d H:i:s'),
			'userid' => $session->get('username'),
		];
		$delete = $builder->delete(['id' => $id]);
		if ($delete) {
			$db->table("logs")->insert($datalogs);
			echo json_encode(['message' => 'Deleted']);
		}
	}

	// =========================== PESANAN MASUK PENJUALAN SELECTION START ======================//
	//Pesanan Masuk Page Start Here
	public function pesanan_masuk()
	{
		$session = session();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$akses = $model_Global->cek_menu_akses($level, 421);
		if ($akses->uadd == 1) {
			$tambah = 'PESANAN MASUK';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'dfaf' => 'dfasfd',
			'title' => 'Pesanan Masuk',
			'body' => 'admin/penjualan/transaksi/pasananmasuk',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function pesanan_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$taxes = $model_Global->_LoadProfile()->taxes_desimal;
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$lvldebug = $session->get('debug');
		$akses = $model_Global->cek_menu_akses($level, 421);
		$db = db_connect();
		$query = "SELECT * FROM ar_sofile where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
			$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
			$query .= " and tglso between '$_tgl1' and '$_tgl2'";
		} else {
			$query .= " and year(tglso)= (select periode_tahun from ms_identity) and
            month(tglso)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$nomor = 1;
		foreach ($res as $value) {
			if ($value->statusid != 4) {
				$sodetail = $db->query("SELECT * from ar_sodetail where kodeso='$value->kodeso'")->getResult();
				$tjumlah = 0;
				$tdiskon = 0;
				foreach ($sodetail as $rowd) {
					$Kodeitem   = $rowd->kodeitem;
					$qtykode    = ($rowd->qtyorder - $rowd->qtykirim);
					$hargakode  = $rowd->hargajual;
					$disckode   = $rowd->disc;

					$tjumlah = $tjumlah + $qtykode * $hargakode;
					$diskon = ($disckode / 100) * $qtykode * $hargakode;
					$tdiskon = $tdiskon + $diskon;
					$dpptot = $tjumlah - $tdiskon;
				}
				$sofile = $db->query("SELECT * from ar_sofile where kodeso='$value->kodeso'")->getRow();
				$totdpp = $dpptot;
				$biayalain  = $sofile->biayalain;
				$cekppn = $sofile->sppn;
				if ($cekppn == 'Y') {
					$ppn = $totdpp * $taxes;
				} else {
					$ppn = 0;
				}
				$totalSoD = $dpptot + $biayalain + $ppn;

				if ($value->statusid == 1) {
					$status = '<span class="badge badge-primary">Menunggu Proses</span>';
				} else if ($value->statusid == 2) {
					$status =  '<span class="badge badge-success">Sudah Disiapkan</span>';
				} else if ($value->statusid == 3) {
					$status =  '<span class="badge badge-warning">PESANAN BELUM TERPENUHI SEMUA</span>';
				} else if ($value->statusid == 5) {
					$status =  '<span class="badge badge-danger">Tanda Terima</span>';
				} else {
					$status =  '';
				}

				if ($akses->uedit == 1 && $akses->udel == 1) {
					if ($value->statusid == 1 || $value->statusid == 5) {
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn"><i class="fa-solid fa-edit"></i></a>';
						$delete = '<button class="btn" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash"></i></button>';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn"><i class="fa-solid fa-eye"></i></a>';
						$delete = '';
					} else {
						$editview = '';
						$delete = '';
					}
				} else if ($akses->udel == 1 && $akses->uedit == 0) {
					$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
					$editview = '';
				} else if ($akses->udel == 0 && $akses->uedit == 1) {
					if ($value->statusid == 1 || $value->statusid == 5) {
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
						$delete = '';
					} else {
						$editview = '';
						$delete = '';
					}
				} else {
					$editview = '';
					$delete = '';
				}

				if($lvldebug == 1){
					$debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->kodeso . '\')"><i class="fas fa-bug"></i></a>';
				}else{
					$debug = '';
				}

				$tanggal_saja = date('d-m-y', strtotime($value->tglso));
				$jam_saja     = date('H:i:s', strtotime($value->tglso));
				$warna_jam = !empty($value->userupdate) ? 'text-danger font-weight-bold' : '';
				$tgl_so_format = $tanggal_saja . '<br><span class="' . $warna_jam . '">' . $jam_saja . '</span>';
				$data[] = [
					$nomor,
					$value->kodeso,
					$tgl_so_format,
					$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
					$value->ket,
					$status,
					$akses->uharga == 1 ? number_format($totalSoD) : '0',
					$editview . $delete . $debug,
				];
				$nomor++;
			}
		}
		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function pesanan_masuk_entri($id = '')
	{
		$css = [];
		$js = [];
		$db = db_connect();
		$session = session();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$data = [];
		if ($id > 0) {
			$data       = $db->table('ar_sofile')->where('id', $id)->get()->getRow();
			$DetailItem = $db->table('ar_sodetail')->where('kodeso', $data->kodeso)->get()->getResult();
			$BiayaLain  = $db->table('ar_sobiaya')->where('kodeso', $data->kodeso)->get()->getResult();
			$jumdata = $db->table('ar_sodetail')->where('kodeso', $data->kodeso)->countAllResults();
		} else {
			$jumdata = 0;
		}

		$res = $db->table('ar_customer')->getWhere(['kode_cabang' => $session->get('cabang')])->getResult();
		$data  = [
			'css' => $css,
			'data' => $data ?? [],
			'DetailItem' => $DetailItem ?? [],
			'BiayaLain' => $BiayaLain ?? [],
			'cabang' => $session->get('cabang'),
			'nomor' => $model_Global->_Autonomor('SO'),
			'nomor_ppn' => $model_Global->_Autonomor('SOP'),
			'NoPOBeli' => $model_Global->_Autonomor('PO'),
			'jumdata1' => $jumdata,
			'js' => $js,
			'res' => $res,
			'title' => 'Pesanan Masuk',
			'body' => 'admin/penjualan/transaksi/pasananmasukentri',

		];
		return view('layout/layout', $data);
	}

	public function DataCustomer($id)
	{
		$db = db_connect();
		$customer_data = $db->table('ar_customer')->where('kode', $id)->get()->getRow();
		$marketting_data = $db->table('ar_sales')->where('id', $customer_data->kodesales)->get()->getRow();
		$response = ['customer_data' => $customer_data, 'marketting_data' => $marketting_data];
		echo json_encode($response);
	}

	public function ShowBarangPO($id, $customer, $cabang)
	{
		$db = db_connect();
		$BarangPerCust_data = $db->table('inv_barang_harga')->where(['kodeitem' => $id, 'kode_customer' => $customer, 'cabang' => $cabang])->get()->getRow();
		$Barang_data = $db->table('inv_barang')->where(['kodeitem' => $id, 'kdcbg' => $cabang])->get()->getRow();
		$response = ['BarangPerCust_data' => $BarangPerCust_data, 'Barang_data' => $Barang_data];
		echo json_encode($response);
	}

	public function infoibrg($cust, $item, $qty)
	{
		if (!empty($item)) {
			$db = db_connect();

			$query = "SELECT inv_barang.* from inv_barang 
						inner join ar_customer on inv_barang.kdcbg=ar_customer.kode_cabang 
						where inv_barang.kodeitem = '$item' and inv_barang.kdcbg=ar_customer.kode_cabang
						and ar_customer.kode='$cust' group by inv_barang.kodeitem";
			$data  = $db->query($query)->getResult();
?>
			<div class="table-responsive">

				<table class="table table-striped table-hover table-bordered" id="tabelinfobrg">
					<tr class="headerx">
						<th style="width:20%;">Nama Barang</th>
						<th style="width:15%;">qty</th>
						<th style="width:15%;">Satuan</th>
					</tr>

					<?php
					foreach ($data  as $row) {
					?>
						<tr>
							<td id="nama_brg">
								<?php echo $row->namabarang; ?>
							</td>
							<td id="stok"><?php echo  $qty; ?></td>
							<td id="satuan"> <?php echo $row->satuan; ?></td>
						</tr>
					<?php } ?>

				<?php
			} else {
				echo "</table>
		  </div>";
			}
		}

		public function ShowBarangPaket($kode, $qty)
		{
			if (!empty($kode)) {
				$db = db_connect();
				$session = session();
				$cabang = $session->get('cabang');
				$buildBarangPaket  = $db->table('inv_barang_paket');
				$ResultBarangPaket = $buildBarangPaket->getWhere(array('kodeitem_barang' => $kode, 'cabang' => $cabang))->getResult();

				echo "
				<table id='DatatablePaket' class='table table-hover table-striped table-bordered table-condensed table-scrollable'>
					<thead>
						<th style='text-align: center'>Nama Barang Paket</th>
						<th style='text-align: center'>Kuantitas</th>
						<th style='text-align: center'>Satuan</th>
					</thead>
					<tbody>";
				foreach ($ResultBarangPaket as $Result) {
					$hitung = ($qty * $Result->qty_paket);
					echo "
						<tr>
							<td>" . $Result->namabarang_paket . "</td>
							<td>" . number_format($hitung) . "</td>
							<td>" . $Result->satuan_paket . "</td>
						</tr>";
				}
				echo "
					</tbody>
				</table>";
			}
		}

		public function syncdata($kode, $cust)
		{
			error_reporting(0);
			$db = db_connect();
			$session = session();
			$cabang = $session->get('cabang');
			$builderINVBarangHarga  = $db->table('inv_barang_harga');
			$hargaPercust = $builderINVBarangHarga->getWhere(array('kode_customer' => $cust, 'kodeitem' => $kode, 'cabang' => $cabang))->getRow();

			echo json_encode($hargaPercust);
		}

		public function pesananmasuksave()
		{
			helper('log_transaksi'); 
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$taxes = $model_Global->_LoadProfile()->taxes_desimal;
			$db = db_connect();
			$input = $this->request->getPost();
			$param = $input['id'];
			$session = session();
			$level = $session->get('level');
			$SessionCabang = $session->get('cabang');
			$akses = $model_Global->cek_menu_akses($level, 421);
			// Table
			$builder            = $db->table('ar_sofile');
			$builderDetail      = $db->table('ar_sodetail');
			$builderBayar       = $db->table('ar_sobiaya');
			$builderINVBarangHarga  = $db->table('inv_barang_harga');
			$buildPofile            = $db->table('ap_pofile');
			$builderDetailPofile    = $db->table('ap_podetail');
			$builderDetailPB    	= $db->table('ap_lpbdetil');
			$builderApPufile    	= $db->table('ap_pufile');
			$builderDetailPU    	= $db->table('ap_pudetail');
			$buildInvBarangPaket    = $db->table('inv_barang_paket');
			$builderINVBarang		= $db->table('inv_barang');
			$builderINVTransaksi	= $db->table('inv_transaksi');
			$buildInvMCDetail    	= $db->table('inv_mutasi_cabang_detail');
			$builderINVAMC			= $db->table('inv_acc_mutasi_cabang');
			$builderINVAMCDetail	= $db->table('inv_accd_mutasi_cabang');
			$builderCustomer        = $db->table('ar_customer');
			try {
				$DataArCs = $builderCustomer->getWHere(['kode' => $input['cust']])->getRow();
				$saldokredit = (int)$DataArCs->saldokredit;
				$totalSofile = (int)$input['_h_total'];
				$Pembayaran  = $input['pembayaran'];
				if($totalSofile > $saldokredit && $Pembayaran == 'K'){
					echo json_encode(['status' => 3, 'message' => 'Saldo Kredit tidak mencukupi']);
				}else{
					if ($akses->uadd == 1 || $akses->uedit == 1) {

						$db->transStart();
						$gudang = $db->table("inv_gudang")->getWhere(array('cabang' => $session->get('cabang')))->getRow();
						$DataSoFile = $builder->getWhere(['kodeso' => $input['nomorbukti']])->getRow();
						if ($param == 0) {
							if (empty($DataSoFile)) {
								if ($input['sppn'] == 'Y') {
									$nobukti = $input['nomorbukti'];
								} else {
									$nobukti = $input['nomorbukti'];
								}
							} else {
								if ($input['sppn'] == 'Y') {
									$nobukti = $model_Global->_Autonomor('SOP');
								} else {
									$nobukti = $model_Global->_Autonomor('SO');
								}
							}
						}

						$noautopo = $buildPofile->getWhere(['kodepo' => $model_Global->_Autonomor('PO'), 'kodecbg' => '01'])->getRow();

						if (empty($noautopo)) {
							$NoKodePO = $model_Global->_Autonomor('PO');
						} else {
							$NoKodePO = $model_Global->_Autonomor('PO');
						}


						if ($param != 0) {
							if ($DataSoFile->statusid == 3) {

								$DataDetail = $builderDetail->getWhere(['kodeso' => $input['nomorbukti'], 'cabang' => $session->get('cabang')])->getResult();
								$cekkode = $input['kode'];
								if ($cekkode == null) {
									$stskode = array('KB.000000');
								} else {
									$stskode = $input['kode'];
								}
								$differentData = [];
								foreach ($DataDetail as $view) {
									$array = in_array($view->kodeitem, $stskode);
									if ($array == false) {
										$differentData[] = ['kodeitem' => $view->kodeitem, 'qtyorder' => $view->qtyorder, 'qtykirim' => $view->qtykirim];
									}
								}
								$differentData;
								$jumdatasts  = count($differentData);

								for ($i = 0; $i < $jumdatasts; $i++) {
									$where = [
										'kodeso' => $input['nomorbukti'],
										'kodeitem' => $differentData[$i]['kodeitem'],
										'cabang' => $session->get('cabang'),
									];

									$dataUpdate = [
										'qtyorder' => $differentData[$i]['qtykirim'],
									];
									$builderDetail->where($where)->update($dataUpdate);

									//update pembelian Barang pack packan
									$DataArSofilePaket = $builder->getWhere(['kodeso' => $input['nomorbukti'], 'kodecbg' => $session->get('cabang')])->getRow();
									$DataBarangPODetail = $builderDetailPofile->getWhere(['kodepo' => $DataArSofilePaket->kodepo, 'kodeitem' => $differentData[$i]['kodeitem']])->getRow();
									if (!empty($DataBarangPODetail)) {
										$DataPuFile = $builderApPufile->getWhere(['kodepo' => $DataArSofilePaket->kodepo, 'kodecbg' => '01'])->getRow();
										$DataPuDetail = $builderDetailPU->getWhere(['kodepu' => $DataPuFile->kodepu, 'kodeitem' => $differentData[$i]['kodeitem']])->getRow();
										$JumlahSisaQty = $differentData[$i]['qtyorder'] - $differentData[$i]['qtykirim'];
										$db->query("UPDATE inv_barang set stok = stok - '$JumlahSisaQty',stok_so = stok_so - '$JumlahSisaQty' where kodeitem = '$DataBarangPODetail->kodeitem' and kdcbg = '$SessionCabang' ");
										$db->query("UPDATE inv_nobatch set qty_terima = qty_terima - '$JumlahSisaQty',qty_saldo = qty_saldo - '$JumlahSisaQty' where kodeitem = '$DataBarangPODetail->kodeitem' and cabang = '$SessionCabang' and no_batch='$DataPuDetail->no_batch' ");
										$builderDetailPofile->where([
											'kodepo' => $DataArSofilePaket->kodepo,
											'kodeitem' => $differentData[$i]['kodeitem']
										])->update([
											'qtyorder' => $differentData[$i]['qtykirim'],
											'qtykirim' => $differentData[$i]['qtykirim']
										]);

										$builderDetailPB->where([
											'nolpb' => $DataPuFile->kodepb,
											'kodeitem' => $differentData[$i]['kodeitem']
										])->update([
											'qtyterima' => $differentData[$i]['qtykirim'],
											'qtyorder' => $differentData[$i]['qtykirim']
										]);

										$builderDetailPU->where([
											'kodepu' => $DataPuFile->kodepu,
											'kodeitem' => $differentData[$i]['kodeitem']
										])->update([
											'qtypu' => $differentData[$i]['qtykirim']
										]);


										$builderINVTransaksi->where([
											'nobukti' => $DataPuFile->kodepu,
											'kodeitem' => $differentData[$i]['kodeitem']
										])->update([
											'penerimaan' => $differentData[$i]['qtykirim']
										]);


										if ($SessionCabang == '02') {
											$DataInAMC = $builderINVAMC->getWhere(['kodepu' => $DataPuFile->kodepu, 'cabang' => $SessionCabang])->getRow();
											$buildInvMCDetail->where([
												'nomutasi_cabang' => $DataInAMC->nomutasi_cabang,
												'kodeitem' => $differentData[$i]['kodeitem'],
												'cabang' => '01'
											])->update([
												'qty' => $differentData[$i]['qtykirim']
											]);
											$builderINVAMCDetail->where([
												'noacc_mutasi' => $DataInAMC->noacc_mutasi,
												'kodeitem' => $differentData[$i]['kodeitem'],
												'cabang' => $SessionCabang
											])->update([
												'qtyacc' => $differentData[$i]['qtykirim']
											]);
											$builderINVTransaksi->where([
												'nobukti' => $DataInAMC->noacc_mutasi,
												'kodeitem' => $differentData[$i]['kodeitem'],
												'cabang' => '01'
											])->update([
												'pengeluaran' => $differentData[$i]['qtykirim']
											]);

											$builderINVTransaksi->where([
												'nobukti' => $DataInAMC->noacc_mutasi,
												'kodeitem' => $differentData[$i]['kodeitem'],
												'cabang' => $SessionCabang
											])->update([
												'penerimaan' => $differentData[$i]['qtykirim']
											]);
										}
									}
								}
							} else {
								$builderDetail->delete(['kodeso' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
								$builderDetailPofile->delete(['kodepo' => $input['NoPOBeli']]);
							}
						}

						if ($param >= 0) {
							$kode  = $input['kode'];
							$qty   = $input['qty'];
							$sat   = $input['sat'];
							$harga = $input['harga'];
							$disc  = $input['disc'];

							$jumdata  = count($kode);

							$tot = 0;
							$tdisc = 0;
							$totPOBeli   = 0;
							$tdiscPOBeli = 0;
							$ArrayKodepo = [];
							for ($i = 0; $i < $jumdata; $i++) {
								$vjum  = $qty[$i] * $harga[$i];
								$vdisc = $vjum * ($disc[$i] / 100);
								$tot   = $tot + $vjum;
								$tdisc = $tdisc + $vdisc;

								$data_harga = $builderINVBarangHarga->getWhere(['kodeitem' => $kode[$i], 'kode_customer' => $input['cust'], 'cabang' => $session->get('cabang'), 'gudang' => $gudang->kode])->getRow();
								if ($data_harga) {
									$partisipasi_pr = $data_harga->partisipasi_pr;
									$partisipasi_rp = $data_harga->partisipasi_rp;
									$fee1_pr = $data_harga->fee1_pr;
									$fee1_rp = $data_harga->fee1_rp;
									$fee2_pr = $data_harga->fee2_pr;
									$fee2_rp = $data_harga->fee2_rp;
									$fee3_pr = $data_harga->fee3_pr;
									$fee3_rp = $data_harga->fee3_rp;
								} else {
									$partisipasi_pr = 0;
									$partisipasi_rp = 0;
									$fee1_pr = 0;
									$fee1_rp = 0;
									$fee2_pr = 0;
									$fee2_rp = 0;
									$fee3_pr = 0;
									$fee3_rp = 0;
								}

								$datad = array(
									'cabang'   => $session->get('cabang'),
									'gudang'   => $gudang->kode,
									'no_po' => $input['no-po'],
									'kodeitem' => $kode[$i],
									'qtyorder' => $qty[$i],
									'satuan' => $sat[$i],
									'hargajual' => $harga[$i],
									'disc' => $disc[$i],
									'partisipasi_pr' => $partisipasi_pr,
									'partisipasi_rp' => $partisipasi_rp,
									'fee1_pr' => $fee1_pr,
									'fee1_rp' => $fee1_rp,
									'fee2_pr' => $fee2_pr,
									'fee2_rp' => $fee2_rp,
									'fee3_pr' => $fee3_pr,
									'fee3_rp' => $fee3_rp,
								);
								$kolom_pantau = ['kodeitem' , 'qtyorder', 'satuan'];
								if ($kode[$i] != "") {
									if ($param == 0) {
										$datad['kodeso'] = $nobukti;
										$builderDetail->insert($datad);
										$insertId = $builderDetail->db()->insertID();
										audit_log_detail($nobukti, 'SO', $kolom_pantau, null, $datad);
									} else {
										if ($DataSoFile->statusid != 3) {
											$datad['kodeso'] = $input['nomorbukti'];
											$builderDetail->insert($datad);
											$insertId = $builderDetail->db()->insertID();
											$oldData = $builderDetail->getWhere(['kodeso' => $input['nomorbukti'], 'cabang' => $session->get('cabang')])->getRowArray();
											audit_log_detail($input['nomorbukti'], 'SO', $kolom_pantau, $oldData, $datad);
										}
									}
								}

								$DataBarangPaket = $buildInvBarangPaket->getWhere(['kodeitem_barang' => $kode[$i]])->getRow();
								if (!empty($DataBarangPaket)) {
									$DataBarangHarga = $builderINVBarangHarga->getWhere(['kodeitem' => $kode[$i], 'cabang' => $session->get('cabang')])->getRow();
									$HargaBarangBeli = $DataBarangHarga->bruto_spl;
									$DiscBarangBeli = $DataBarangHarga->disc_spl;

									$vjumPOBeli  = $qty[$i] * $HargaBarangBeli;
									$vdiscPOBeli = $vjumPOBeli * ($DiscBarangBeli / 100);
									$totPOBeli   = $totPOBeli + $vjumPOBeli;
									$tdiscPOBeli = $tdiscPOBeli + $vdiscPOBeli;

									$datadPOBeli = array(
										'kodeitem' => $kode[$i],
										'qtyorder' => $qty[$i],
										'satuan' => $sat[$i],
										'hargabeli' => $HargaBarangBeli,
										'disc' => $DiscBarangBeli,
										'sodetail_id' => $insertId,
									);
									$ArrayKodepo[] = ['KOdeToPO' => $NoKodePO];
									if ($kode[$i] != "") {
										if ($param == 0) {

											$datadPOBeli['kodepo'] = $NoKodePO;
											$builderDetailPofile->insert($datadPOBeli);
										} else {
											if ($DataSoFile->statusid != 3) {
												if ($input['NoPOBeli'] == "") {
													$datadPOBeli['kodepo'] = $NoKodePO;
												} else {
													$datadPOBeli['kodepo'] = $input['NoPOBeli'];
												}

												$builderDetailPofile->insert($datadPOBeli);
											}
										}
										$builderINVBarang->where(['kodeitem' => $kode[$i]])->update(['tglbeliakhir' => $input['tanggal']]);
									}
								}
							}
						}

						if ($input['sppn'] == "Y") {
							$tppn = ($tot - $tdisc) * $taxes;
						} else {
							$tppn = 0;
						}
						//rincian biaya lain-lain
						$lkode  = $input['lkode'];
						$jumlah = $input['ljumlah'];
						$ket   = $input['lket'];
						$jumdata  = count($lkode);

						if ($param != 0) {
							$builderBayar->delete(['kodeso' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
						}

						$totbiaya = 0;
						for ($i = 0; $i < $jumdata; $i++) {
							$totbiaya = $totbiaya + $jumlah[$i];
							$datad = array(
								'cabang'   => $session->get('cabang'),
								'gudang'   => $gudang->kode,
								'kodeakun' => $lkode[$i],
								'jumlah' => $jumlah[$i],
								'keterangan' => $ket[$i],

							);
							if ($lkode[$i] != "") {
								if ($param == 1) {
									$datad['kodeso'] = $nobukti;
									$builderBayar->insert($datad);
								} else {
									$datad['kodeso'] = $input['nomorbukti'];
									$builderBayar->insert($datad);
								}
							}
						}

						$data_sales = $db->table("ar_sales")->getWhere(['nama_sales' => $input['sales']])->getRow();
						if ($data_sales) {
							$nama_sales = $data_sales->nama_sales;
							$kode_sales = $data_sales->id;
						} else {
							$nama_sales = '';
							$kode_sales = '';
						}

						$data = array(
							'kodecbg' => $session->get('cabang'),
							'gudang'   => $gudang->kode,
							'kodecust' => $input['cust'],
							'no_po'  => $input['no-po'],
							'tglkirim' => date('Y-m-d', strtotime($input['tanggal_kirim'])),
							'ket'     => $input['keterangan'],
							'pembayaran'     => $input['pembayaran'],
							'dpp'     => $tot,
							'fee1_rp'     => $fee1_rp,
							'fee2_rp'     => $fee2_rp,
							'fee3_rp'     => $fee3_rp,
							'ppn'     => $tppn,
							'diskon'  => $tdisc,
							'biayalain' => $totbiaya,
							'totalso' => $tot + $tppn - $tdisc + $totbiaya,
							'sppn'    => $input['sppn'],
							'alamat'  => $input['alamat'],
							'id_sales'  => $kode_sales,
							'nama_sales' => $nama_sales,
						);

						$dataHeaderPOBeli = array(
							'kodecbg' 		=> '01',
							'kodesup' 		=> 'SP.000103',
							'tglpo'   		=> date('Y-m-d', strtotime($input['tanggal'])),
							'tglkirim'		=> date('Y-m-d', strtotime($input['tanggal_kirim'])),
							'kodeuser'		=> $session->get('username'),
							'ket'     		=> $input['keterangan'],
							'dpp'     		=> $totPOBeli,
							'ppn'     		=> 0,
							'pembayaran'    => 'T',
							'diskon'  		=> $tdiscPOBeli,
							'biayalain'		=> 0,
							'totalpo' 		=> $totPOBeli + 0 + 0 - $tdiscPOBeli,
							'sppn'    		=> 'T',
						);

						$datalogs = [
							'date' => date('Y-m-d H:i:s'),
							'userid' => $session->get('username'),
						];

						if ($param == 0) {
							$data['kodeso'] = $nobukti;
							$data['tglso'] = date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s')));
							$data['tglentry'] =  date('Y-m-d H:i:s');
							$data['kodeuser'] =  $session->get('username');
							$data['statusid'] = 1;
							$data['no_get'] = 1;
							if (!empty($ArrayKodepo)) {
								$data['kodepo'] = $NoKodePO;
							} else {
								$data['kodepo'] = '';
							}

							$builder->insert($data);
							$insertIdHeader = $builder->db()->insertID();
							$datalogs['detail'] = 'Tambah data Penjualan Pesanan Masuk dengan kode ' . $nobukti;
							$datalogs['nobukti'] = $nobukti;

							$db->table("logs")->insert($datalogs);
							if ($input['sppn'] == 'Y') {
								$model_Global->_updatecounter1('SOP');
							} else {
								$model_Global->_updatecounter1('SO');
							}

							if (!empty($ArrayKodepo)) {
								$dataHeaderPOBeli['statusid'] = 4;
								$dataHeaderPOBeli['no_get'] = 3;
								$dataHeaderPOBeli['kodepo'] = $NoKodePO;
								$buildPofile->insert($dataHeaderPOBeli);
								$datalogs['detail'] = 'Tambah data Pembelian PO dengan kode ' . $NoKodePO;
								$db->table("logs")->insert($datalogs);
								$model_Global->_updatecounter1('PO');
							}

							echo json_encode(['status' => 1, 'message' => $nobukti]);
						} else {
							$kodeso = $input['nomorbukti'];
							$cab = $session->get('cabang');
							if ($DataSoFile->statusid == 3) {
								$dataHupdate = array(
									'ket' => $input['keterangan'],
									'tglso'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
									'tglkirim' => date('Y-m-d', strtotime($input['tanggal_kirim'])),
									'tglupdate' => date('Y-m-d H:i:s'),
									'userupdate' => $session->get('username'),
								);
								$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ar_sodetail where kodeso = '$kodeso' and cabang='$cab'")->getRow()->total;
								if ($total <= 0) {
									$dataHupdate['statusid'] = 4;
									$dataHupdate['no_get'] = 2;
									$builder->where(['kodeso' => $kodeso, 'kodecbg' => $cab])->update($dataHupdate);
								} else {
									$builder->where(['kodeso' => $kodeso, 'kodecbg' => $cab])->update($dataHupdate);
								}
							} else {
								$data['kodeso'] = $input['nomorbukti'];
								$data['tglso'] = date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s')));
								$data['tglupdate'] =  date('Y-m-d H:i:s');
								$data['userupdate'] =  $session->get('username');
								if (!empty($ArrayKodepo)) {
									if ($input['NoPOBeli'] == "") {
										$data['kodepo'] = $NoKodePO;
										$dataHeaderPOBeli['statusid'] = 4;
										$dataHeaderPOBeli['no_get'] = 3;
										$dataHeaderPOBeli['kodepo'] = $NoKodePO;
										$buildPofile->insert($dataHeaderPOBeli);
										$model_Global->_updatecounter1('PO');
									} else {
										$data['kodepo'] = $input['NoPOBeli'];
										$buildPofile->where(['kodepo' => $input['NoPOBeli'], 'kodecbg' => '01'])->update($dataHeaderPOBeli);
									}
								} else {
									$data['kodepo'] = '';
								}
								$builder->where(['kodeso' => $input['nomorbukti'], 'kodecbg' => $session->get('cabang')])->update($data);
							}
							$datalogs['detail'] = 'Update data Penjualan Pesanan Masuk dengan kode ' . $input['nomorbukti'];
							$datalogs['nobukti'] = $input['nomorbukti'];
							$db->table("logs")->insert($datalogs);

							$datalogs['detail'] = 'Edit data Pembelian PO dengan kode ' . $input['NoPOBeli'];
							$datalogs['nobukti'] = $input['NoPOBeli'];
							$db->table("logs")->insert($datalogs);
							echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
						}
						$db->transComplete();
						if ($db->transStatus() === false) {
							// Jika terjadi kesalahan, batalkan transaksi
							$db->transRollback();
							echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
						}
						// Transaksi sukses, commit perubahan ke database
						$db->transCommit();
					} else {
						echo json_encode(['status' => 2, 'message' => 'Akses Ditolak']);
					}
				}
			} catch (\Exception $e) {
				echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
			}
		}

		public function pesananmasukDelete($nomor)
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$db->transStart();
			$session = session();
			$level = $session->get('level');
			$cabang = $session->get('cabang');
			$akses = $model_Global->cek_menu_akses($level, 421);
			$builder            = $db->table('ar_sofile');
			$builderDetail      = $db->table('ar_sodetail');
			$builderBayar       = $db->table('ar_sobiaya');
			$buildPoBeli      	= $db->table('ap_pofile');
			$buildPoBeliDetail  = $db->table('ap_podetail');
			if ($akses->udel == 1) {
				$sofile = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();
				$datalogs = [
					'detail' => 'Hapus data Penjualan Pesanan Masuk dengan Kode ' . $sofile->kodeso,
					'nobukti' => $sofile->kodeso,
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				$delDetailso    = $builderDetail->delete(['kodeso' => $sofile->kodeso, 'cabang' => $cabang]);
				$delBayarso     = $builderBayar->delete(['kodeso' => $sofile->kodeso, 'cabang' => $cabang]);
				$delDetailPo    = $buildPoBeliDetail->delete(['kodepo' => $sofile->kodepo]);
				$delP     		= $buildPoBeli->delete(['kodepo' => $sofile->kodepo, 'kodecbg' => '01']);
				$DataSo         = $builder->delete(['id' => $nomor, 'kodecbg' => $cabang]);
				if ($DataSo == true && $delBayarso == true && $delDetailso == true) {
					$db->table("logs")->insert($datalogs);
					echo json_encode(['message' => 'Deleted']);
				}
			}
			$db->transComplete();
		}

		public function cetak_pesananmasuk($param)
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$session = session();
			$cek = $session->get('level');
			$cabang = $session->get('cabang');
			if (!empty($cek)) {
				$db->query("UPDATE ar_sofile set statusid=5 where kodeso='$param'");
				$profile = $model_Global->_LoadProfile();
				$nama_usaha = $profile->nama_usaha;
				$alamat1  = $profile->alamat1;
				$alamat2  = $profile->alamat2;
				$hp 	  = $profile->hp;
				$email 	  = $profile->email;
				$CekCabangSo = $db->query("SELECT * from ar_sofile where kodeso='$param'")->getRow();
				$CabangSo = $CekCabangSo->kodecbg;
				$queryh = "select * from ar_sofile inner join ar_customer on ar_sofile.kodecust=ar_customer.kode where kodeso = '$param'";
				$queryd = "select ar_sodetail.*, inv_barang.namabarang 
							from ar_sodetail 
							inner join inv_barang on ar_sodetail.kodeitem=inv_barang.kodeitem 
							where ar_sodetail.kodeso = '$param' and kdcbg='$CabangSo' order by inv_barang.namabarang ";
				$queryb = "select sum(jumlah) as total from ar_sobiaya  where ar_sobiaya.kodeso = '$param'";

				$QueryPaket = $db->query("SELECT b.kodeitem_barang, c.namabarang 
											FROM ar_sodetail a 
											INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
											INNER JOIN inv_barang c ON c.kodeitem = b.kodeitem_barang
											WHERE a.kodeso='$param' AND a.cabang='$CabangSo' AND b.cabang='$CabangSo' 
											GROUP BY b.kodeitem_barang ORDER BY c.namabarang")->getResult();
				$QueryPaketRow = $db->query("SELECT * FROM ar_sodetail a INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
											WHERE a.kodeso='$param' AND a.cabang='$CabangSo' AND b.cabang='$CabangSo' GROUP BY b.kodeitem_barang ")->getRow();
				$QueryPaketNumRow = $db->query("SELECT * FROM ar_sodetail a INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
											WHERE a.kodeso='$param' AND a.cabang='$CabangSo' AND b.cabang='$CabangSo' GROUP BY b.kodeitem_barang ")->getNumRows();

				$detil  = $db->query($queryd)->getResult();
				$header = $db->query($queryh)->getRow();
				$biaya  = $db->query($queryb)->getRow();
				$pdf = new simkeu_nota_helper();
				$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
				$pdf->setjudul('');
				$pdf->setsubjudul('');
				$pdf->addpage("P", "A4");
				$pdf->setsize("P", "A4");
				$pdf->SetWidths(array(70, 30, 90));
				$border = array('B', '', 'B');
				$size   = array('0', '0', '0');
				$pdf->setfont('Arial', 'B', 7.5);
				$pdf->SetAligns(array('C', 'C', 'C'));
				$align = array('L', 'C', 'L');
				$style = array('B', '', 'B');
				$size  = array('9', '0', '9');
				$max   = array(5, 5, 20);
				$judul = array('Kepada :', '', 'Tanda Terima');
				$fc     = array('0', '0', '0');
				$hc     = array('20', '20', '20');
				$pdf->FancyRow2(5, $judul, $fc,  $border, $align, $style, $size, $max);
				$pdf->ln(0);
				$pdf->setfont('Arial', 'B', 7.5);
				$pdf->SetWidths(array(70, 30, 30, 5, 55));
				$border = array('', '', '', '', '');
				$fc     = array('0', '0', '0', '0', '0');
				$pdf->SetFillColor(230, 230, 230);
				$pdf->setfont('Arial', '', 7.5);
				$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodeso), $fc, $border);
				$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglso))), $fc, $border);
				$pdf->FancyRow(array($header->alamat2, '', 'Tgl. Kirim', ':', date('d M Y', strtotime($header->tglkirim))), $fc, $border);
				$fc     = array('0', '0', '0', '0', '0');
				// $pdf->FancyRow(array($header->telp,'','','',''), $fc, $border);
				$pdf->ln(0);

				$pdf->SetWidths(array(25, 80, 20, 20, 20, 20));
				$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
				$align  = array('L', 'L', 'R', 'C', 'R', 'R');
				$pdf->setfont('Arial', 'B', 7.5);
				$pdf->SetAligns(array('L', 'C', 'R'));
				$fc = array('0', '0', '0', '0', '0', '0');
				$judul = array('Kode Barang', 'Nama Barang', 'Qty', 'Satuan', 'Harga Satuan', 'Diskon');
				$pdf->FancyRow2(5, $judul, $fc, $border, $align);
				$border = array('', '', '');
				$pdf->setfont('Arial', '', 7.5);
				$tot = 0;
				$subtot = 0;
				$tdisc  = 0;
				$border = array('', '', '', '', '', '');
				$align  = array('L', 'L', 'R', 'C', 'R', 'R');
				$fc = array('0', '0', '0', '0', '0', '0');
				$pdf->SetFillColor(0, 0, 139);
				$pdf->settextcolor(0);
				foreach ($detil as $db) {
					$dpp = $db->qtyorder * $db->hargajual;
					$dis = ($db->disc / 100) * $dpp;
					$jum = $dpp - $dis;
					$tot = $tot + $jum;

					$subtot = $subtot + $dpp;
					$tdisc  = $tdisc + $dis;

					$pdf->FancyRow(array(
						$db->kodeitem,
						$db->namabarang,
						$db->qtyorder,
						$db->satuan,
						number_format($db->hargajual, 0, '.', ','),
						$db->disc,
					), $fc, $border, $align);
				}


				if ($header->sppn == "Y") {
					$ppn = $subtot * 0.1;
				} else {
					$ppn = 0;
				}
				$biayalain = $biaya->total;
				$tot = $tot + $ppn + $biayalain;
				$pdf->ln(-4);
				$pdf->SetFillColor(230);
				$border = array('B', 'B', 'B', 'B', 'B', 'B');
				$pdf->FancyRow(array('', '', '', '', '', ''), 0, $border);
				$pdf->ln(2);
				$pdf->SetWidths(array(100, 20, 30, 40));
				$border = array('TB', '', '', '');
				$align  = array('L', '', 'L', 'R');

				$fc = array('0', '0', '0', '0');
				$pdf->FancyRow(array('Keterangan', '', '', ''), $fc, $border, $align, 0);
				$border = array('', '', '', '');
				$pdf->FancyRow(array($header->ket, '', '', ''), $fc, $border, $align);
				$pdf->FancyRow(array('', '', '', ''), $fc, $border, $align);
				$style = array('', '', 'B', 'B');
				$size  = array('0', '0', '0', '0');
				$border = array('T', '', '', '');

				$fc = array('0', '0', '0', '0');
				$pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, $style, $size);
				$pdf->settextcolor(0);
				$pdf->SetWidths(array(45, 2, 45, 15, 45, 4, 45));
				$border = array('', '', '', '',);
				$align  = array('C', 'C', 'C', 'C');
				$pdf->FancyRow(array('Hormat Kami', '', 'Disiapkan oleh', '', 'Dikirim oleh', '', 'Penerima'), 0, $border, $align);
				$pdf->ln(10);
				$pdf->SetWidths(array(45, 2, 45, 2, 45, 2, 45));
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
				$border = array('B', '', 'B', '', 'B', '', 'B');
				$pdf->FancyRow(array('', '', '', '', '', '', ''), 0, $border, $align);
				$border = array('', '', '', '', '', '', '');
				$align  = array('L', 'C', 'L', 'C', 'L', 'C', 'L');
				$pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.', '', 'Tgl.'), 0, $border, $align);
				$pdf->Ln(15);

				if ($QueryPaketRow != null || $QueryPaketRow != '') {
					$columnWidth = ($pdf->GetPageWidth() - 20) / 3; // Minus margin
					$cellHeight = 5; // Tinggi sel
					// Set posisi awal
					$xLeft = 10;
					$xCenter = $xLeft + $columnWidth + 2; // 10 adalah jarak antar kolom
					$xRight = $xCenter + $columnWidth + 2;
					$y = $pdf->GetY();


					$columnWidthC = ($pdf->GetPageWidth() - 20) / 3; // Minus margin
					$cellHeightC = 5; // Tinggi sel
					// Set posisi awal
					$xLeftC = 10;
					$xCenterC = $xLeftC + $columnWidthC + 2; // 10 adalah jarak antar kolom
					$xRightC = $xCenterC + $columnWidthC + 2;
					$yC = $pdf->GetY();
					foreach ($QueryPaket as $index => $pack) {

						$base = db_connect();
						$ItemBarang = $base->query("SELECT namabarang from inv_barang where kodeitem = '$pack->kodeitem_barang' and kdcbg='$CabangSo'")->getRow();
						$pdf->SetFont('Arial', 'B', 7.5);
						$pdf->SetFillColor(255, 165, 0);

						if ($index % 3 == 0) {
							// Kolom kiri
							$pdf->SetXY($xLeft, $y);
							$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
						} else if ($index % 3 == 1) {
							// Kolom tengah
							$pdf->SetXY($xCenter, $y);
							$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
						} else {
							// Kolom kanan
							$pdf->SetXY($xRight, $y);
							$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
							// Update posisi y untuk baris berikutnya
						}

						$DetailBarang   = $base->query("SELECT a.qtyorder, b.qty_paket, b.namabarang_paket, b.kodeitem_paket FROM ar_sodetail a 
														INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
														WHERE a.kodeso='$param' AND b.cabang='$CabangSo' AND b.kodeitem_barang='$pack->kodeitem_barang' ORDER BY namabarang_paket ")->getResult();
						foreach ($DetailBarang as $id => $view) {
							$BarangName   = $base->query("SELECT namabarang from inv_barang where kodeitem = '$view->kodeitem_paket' and kdcbg='$CabangSo'")->getRow();
							$BarangPaket   = $base->query("SELECT qty_paket, satuan_paket FROM inv_barang_paket WHERE kodeitem_paket='$view->kodeitem_paket' AND cabang='$CabangSo'")->getRow();
							$jumlahqtypaket = ($BarangPaket->qty_paket * $view->qtyorder);
							$pdf->SetFont('Arial', '', 7);
							$pdf->SetFillColor(230, 230, 230);
							$namaBarangDetail = $pdf->limit_text($BarangName->namabarang, 5);

							if ($index % 3 == 0) {
								// Kolom kiri

								if ($id == 0) {
									$pdf->SetXY($xLeftC, $yC + 5);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 1) {
									$pdf->SetXY($xLeftC, $yC + 15);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 2) {
									$pdf->SetXY($xLeftC, $yC + 25);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 3) {
									$pdf->SetXY($xLeftC, $yC + 35);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 4) {
									$pdf->SetXY($xLeftC, $yC + 45);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 5) {
									$pdf->SetXY($xLeftC, $yC + 55);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 6) {
									$pdf->SetXY($xLeftC, $yC + 65);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 7) {
									$pdf->SetXY($xLeftC, $yC + 75);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								}
							} else if ($index % 3 == 1) {
								// Kolom tengah
								if ($id == 0) {
									$pdf->SetXY($xCenterC, $yC + 5);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 1) {
									$pdf->SetXY($xCenterC, $yC + 15);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 2) {
									$pdf->SetXY($xCenterC, $yC + 25);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 3) {
									$pdf->SetXY($xCenterC, $yC + 35);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 4) {
									$pdf->SetXY($xCenterC, $yC + 45);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 5) {
									$pdf->SetXY($xCenterC, $yC + 55);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 6) {
									$pdf->SetXY($xCenterC, $yC + 65);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 7) {
									$pdf->SetXY($xCenterC, $yC + 75);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								}
							} else {
								// Kolom kanan
								if ($id == 0) {
									$pdf->SetXY($xRightC, $yC + 5);
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 1) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 3);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC + 3);
									}

									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 2) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 11);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 5);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC + 1);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 3) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 19);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 10);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC - 1);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 4) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 29);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 15);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC - 3);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 5) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 39);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 20);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC - 5);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 6) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 39);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 20);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC - 7);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								} else if ($id == 7) {
									if ($QueryPaketNumRow > 4) {
										$pdf->SetXY($xRightC, $yC - 39);
									} else if ($QueryPaketNumRow == 4) {
										$pdf->SetXY($xRightC, $yC - 20);
									} else if ($QueryPaketNumRow < 4) {
										$pdf->SetXY($xRightC, $yC - 10);
									}
									$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
										. "\nQTY PAKET : " . $BarangPaket->qty_paket . ' ' . $BarangPaket->satuan_paket . "  QTY ORDER : " . $jumlahqtypaket . ' ' . $BarangPaket->satuan_paket, 1, 1, 'L');
								}

								// Update posisi y untuk baris berikutnya
								$y += 3 * ($QueryPaketNumRow + 1);
								$yC += 3 * ($QueryPaketNumRow + 1);
							}
						}
					}
					$pdf->Ln(0);
				}

				$pdf->AliasNbPages();
				$pdf->output($param . '.PDF', 'I');
				exit;
			} else {
				header('location:' . base_url());
			}
		}

		public function cetak_shipping($param)
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$session = session();
			$cek = $session->get('level');
			$unit = $session->get('cabang');
			if (!empty($cek)) {
				$profile = $model_Global->_LoadProfile();
				$nama_usaha = $profile->nama_usaha;
				$alamat1  = $profile->alamat1;
				$alamat2  = $profile->alamat2;
				$hp 	  = $profile->hp;
				$email 	  = $profile->email;

				$CekCabangSo = $db->query("SELECT * from ar_sofile where kodeso='$param'")->getRow();
				$CabangSo = $CekCabangSo->kodecbg;
				$queryh = "select * from ar_sofile inner join ar_customer on ar_sofile.kodecust=ar_customer.kode where kodeso = '$param'";
				$queryd = "select ar_sodetail.*, inv_barang.namabarang 
							from ar_sodetail 
							inner join inv_barang on ar_sodetail.kodeitem=inv_barang.kodeitem 
							where ar_sodetail.kodeso = '$param' and kdcbg='$CabangSo' ORDER BY inv_barang.namabarang";
				$queryb = "select sum(jumlah) as total from ar_sobiaya  where ar_sobiaya.kodeso = '$param'";

				$QueryPaket = $db->query("SELECT b.kodeitem_barang, c.namabarang 
											FROM ar_sodetail a 
											INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
											INNER JOIN inv_barang c ON c.kodeitem = b.kodeitem_barang
											WHERE a.kodeso='$param' AND a.cabang='$CabangSo' AND b.cabang='$CabangSo' 
											GROUP BY b.kodeitem_barang ORDER BY c.namabarang")->getResult();
				$QueryPaketRow = $db->query("SELECT * FROM ar_sodetail a INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
										WHERE a.kodeso='$param' AND a.cabang='$CabangSo' AND b.cabang='$CabangSo' GROUP BY b.kodeitem_barang ")->getRow();

				$detil  = $db->query($queryd)->getResult();
				$header = $db->query($queryh)->getRow();
				$biaya  = $db->query($queryb)->getRow();
				$pdf = new simkeu_nota_helper();
				$pdf->setjudul('');
				$pdf->SetMargins(2, 5, 0);
				$pdf->setsubjudul('');
				$pdf->addpage("P", "a4");
				$pdf->setsize("P", "a4");
				$pdf->SetWidths(array(70));
				$border = array('');
				$size   = array('0');
				$pdf->setfont('Arial', '', 9);
				$pdf->SetAligns(array('C', 'C', 'C'));
				$align = array('L');
				$style = array('B');
				$size  = array('9');
				$max   = array(5);
				$judul = array('Kepada :');
				$fc     = array('0');
				$hc     = array('20');
				$pdf->FancyRow2(5, $judul, $fc,  $border, $align, $style, $size, $max);
				$pdf->ln(0);
				$pdf->SetWidths(array(70));
				$border = array('');
				$fc     = array('0', '0', '0', '0', '0');
				$pdf->SetFillColor(230, 230, 230);
				$pdf->setfont('Arial', '', 9);

				$pdf->FancyRow(array($header->nama), $fc, $border);
				$pdf->FancyRow(array('No Pesanan : ' . $header->kodeso), $fc, $border);
				$pdf->ln(1);

				$pdf->SetWidths(array(10, 60, 10));
				$border = array('TB', 'TB', 'TB');
				$align  = array('L', 'L', 'R');
				$pdf->setfont('Arial', 'B', 10);
				$pdf->SetAligns(array('L', 'C', 'R'));
				$fc = array('0', '0', '0');
				$judul = array('no', 'Nama Barang', 'Qty');
				$pdf->FancyRow2(8, $judul, $fc, $border, $align);
				$border = array('', '', '');
				$pdf->setfont('Arial', '', 10);

				$border = array('', '', '');
				$align  = array('L', 'L', 'R');
				$fc = array('0', '0', '0');
				$pdf->SetFillColor(0, 0, 139);
				$pdf->settextcolor(0);
				$no = 1;
				$qty = 0;
				$tot = 0;
				foreach ($detil as $db) {
					$hitung_qty = $db->qtyorder - $db->qtykirim;
					$qty = $qty + $hitung_qty;
					if ($hitung_qty >= 1) {
						$pdf->FancyRow(array(
							$no,
							$db->namabarang,
							$hitung_qty
						), $fc, $border, $align);

						$tot++;
						$no++;
					}
				}
				$pdf->SetFillColor(230);
				$border = array('B', 'B', 'B');
				$pdf->FancyRow(array('', '', ''), 0, $border);
				$pdf->ln(2);
				$pdf->SetWidths(array(30, 38));
				$border = array('T', 'T');
				$align  = array('L', 'R');
				$fc = array('0', '0');
				$border = array('T', 'T');
				$align  = array('L', 'R');
				$pdf->FancyRow(array('Total Kuantitas', $qty), $fc, $border, $align);
				$border = array('B', 'B');
				$pdf->FancyRow(array('Jumlah Barang', $tot), $fc, $border, $align);
				$border = array('', '');
				$pdf->FancyRow(array('', ''), $fc, $border, $align);
				$pdf->ln(5);


				if ($QueryPaketRow != null || $QueryPaketRow != '') {
					foreach ($QueryPaket as $row) {
						$base = db_connect();
						$ItemBarang = $base->query("SELECT namabarang from inv_barang where kodeitem = '$row->kodeitem_barang' and kdcbg='$unit'")->getRow();
						$pdf->setfont('Times', 'B', 10);
						$border = array('');
						$align = array('');
						$pdf->SetWidths(array(100));
						$pdf->ln(5);
						$pdf->Fancyrow(array($ItemBarang->namabarang), $border, $align);
						$pdf->ln(1);
						$DetailBarang   = $base->query("SELECT a.qtyorder, b.qty_paket, b.namabarang_paket FROM ar_sodetail a 
														INNER JOIN inv_barang_paket b ON  a.kodeitem = b.kodeitem_barang
														WHERE a.kodeso='$param' AND b.cabang='$unit' AND b.kodeitem_barang='$row->kodeitem_barang' ORDER BY namabarang_paket")->getResult();
						$pdf->SetWidths(array(8, 35, 20, 18));
						$pdf->SetAligns(array('C', 'C', 'C', 'C'));
						$judul = array('qty', 'Item Barang', 'batch', 'Ed');
						$pdf->setfont('Times', 'B', 10);
						$pdf->row($judul);
						$pdf->SetWidths(array(8, 35, 20, 18));
						$pdf->SetAligns(array('C', 'C', 'C', 'C'));
						$pdf->setfont('Times', '', 9);
						$pdf->SetFillColor(224, 235, 255);
						$pdf->SetTextColor(0);
						$pdf->SetFont('');
						foreach ($DetailBarang as $row2) {
							$hitung = $row2->qtyorder * $row2->qty_paket;
							$pdf->row(
								array(
									$hitung,
									$row2->namabarang_paket,
									'',
									'',
								)
							);
						}
					}
				}
				$pdf->AliasNbPages();
				$pdf->output($param . '.PDF', 'I');
				exit;
			} else {

				header('location:' . base_url());
			}
		}

		// =========================== PESANAN DISIAPKAN PENJUALAN SELECTION START ======================//
		//Pesanan Disiapkan Page Start Here
		public function pesanan_disiapkan()
		{
			$session = session();
			$level = $session->get('level');
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$akses = $model_Global->cek_menu_akses($level, 422);
			if ($akses->uadd == 1) {
				$tambah = 'Daftar Pesanan Baru Masuk';
			} else {
				$tambah = '';
			}
			$css = [];
			$js = [];
			$data  = [
				'css' => $css,
				'js' => $js,
				'title' => 'Pesanan Disiapkan',
				'body' => 'admin/penjualan/transaksi/pasanandisiapkan',
				'tambah' => $tambah
			];
			return view('layout/layout', $data);
		}

		public function pesanandisiapkan_table($tgl1, $tgl2)
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$session = session();
			$cabang = $session->get('cabang');
			$level = $session->get('level');
			$lvldebug = $session->get('debug');
			$akses = $model_Global->cek_menu_akses($level, 422);
			$db = db_connect();
			$query = "SELECT * FROM ar_kirim where kodecbg='$cabang'";
			if ($tgl1 != "1" && $tgl2 !== "1") {
				$query .= " and tglkirim between '$tgl1' and '$tgl2'";
			} else {
				$query .= " and year(tglkirim)= (select periode_tahun from ms_identity) and
            month(tglkirim)= (select periode_bulan from ms_identity)";
			}
			$res = $db->query($query)->getResult();
			$result = array();
			$data = [];
			$count = 0;
			foreach ($res as $value) {
				if ($value->statusid != 3) {
					if ($value->statusid == 1) {
						$status = '<span class="badge badge-primary">Dikirim</span>';
					} else if ($value->statusid == 2) {
						$status =  '<span class="badge badge-success">Terproses</span>';
					} else {
						$status =  '';
					}
					if($lvldebug == 1){
						$debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->kodekirim . '\')"><i class="fas fa-bug"></i></a>';
					}else{
						$debug = '';
					}
					$count++;
					$data[] = [
						$count,
						$value->kodekirim,
						date('d-m-y H:i:s', strtotime($value->tglkirim)),
						$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
						$value->keterangan,
						$status,
						($akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/pesanandisiapkan/entri/' . $value->id) . '" class="btn"><i class="fa-solid fa-edit"></i></a>' : '' ).
						($akses->udel == 1 ?  '<button class="btn" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash"></i></button>' : '' ) . 
						($debug)
					];
				}
			}
			$result = ['data' => $data];
			echo json_encode($result);
		}

		public function pesanan_disiapkan_entri($id = '')
		{
			$css = [];
			$js = [];
			$db = db_connect();
			$session = session();
			$cabang = $session->get('cabang');
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$data = [];
			if ($id > 0) {
				$data       = $db->table('ar_kirim')->where('id', $id)->get()->getRow();
				$DetailItem = $db->table('ar_kirimdetil')->where('kodekirim', $data->kodekirim)->get()->getResult();
				$KirimPaket = $db->table('ar_kirimpaket')->where('kodekirim', $data->kodekirim)->get()->getResult();
				$DataSo     = $db->table('ar_sofile')->where('kodeso', $data->kodeso)->get()->getRow();
			}
			$res = $db->query("SELECT * FROM ar_customer a inner join ar_sofile b on a.kode = b.kodecust where b.statusid != 4 and b.no_get != 3 and a.kode_cabang='$cabang' and b.kodecbg='$cabang' group by b.kodecust")->getResult();
			$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
			$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
			$data  = [
				'data' => $data ?? [],
				'DetailItem' => $DetailItem ?? [],
				'KirimPaket' => $KirimPaket ?? [],
				'BiayaLain' => $BiayaLain ?? [],
				'DataSo' => $DataSo ?? [],
				'cabang' => $session->get('cabang'),
				'nomor' => $model_Global->_Autonomor('SD'),
				'nomor_ppn' => $model_Global->_Autonomor('SDP'),
				'NoPBBeli' => $model_Global->_Autonomor('PB'),
				'NoPUBeli' => $model_Global->_Autonomor('PU'),
				'gudang' => $gudang,
				'arCust' => $arCust,
				'css' => $css,
				'js' => $js,
				'res' => $res,
				'title' => 'Pesanan Disiapkan',
				'body' => 'admin/penjualan/transaksi/pasanandisiapkanentri',
			];
			return view('layout/layout', $data);
		}

		public function ShowSo($kode)
		{
			$db = db_connect();
			if (!empty($kode)) {
				$so = $db->table("ar_sofile")->getWhere(['no_get' => 1, 'kodecust' => $kode])->getResult();
				?>
					<select name="kodeso" id="kodeso" class="form-control  input-medium select2">
						<option value="">-- PILIH NO BUKTI ---</option>
						<?php
						foreach ($so  as $row) {
						?>
							<option value="<?php echo $row->kodeso; ?>"><?php echo $row->kodeso; ?></option>
						<?php
						}
						?>
					</select>

				<?php

			} else {
				echo "";
			}
		}

		public function DataSo($so)
		{
			$db = db_connect();
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$session = session();
			$cabang = $session->get('cabang');
			$query = $db->query("SELECT a.*, c.sppn, c.gudang as kode, c.ket, b.namabarang, b.stok, b.satuan, a.qtyorder - a.qtykirim as sisa 
							from ar_sodetail a 
							inner join inv_barang b on b.kodeitem=a.kodeitem
							inner join ar_sofile c on c.kodeso=a.kodeso
							where a.kodeso='$so' and a.cabang='$cabang' and b.kdcbg='$cabang' and a.qtyorder > a.qtykirim ORDER BY b.namabarang")->getResult();

			$query2 = $db->query("SELECT * from ar_sodetail a inner join inv_barang b on b.kodeitem=a.kodeitem
								where a.kodeso = '$so' AND  a.cabang='$cabang' and b.kdcbg='$cabang' order by b.namabarang")->getResult();
			$DataTampung2 = [];
			foreach ($query2 as $row) {
				$DataBarangPaket = $db->table('inv_barang_paket')->getWhere(['kodeitem_barang' => $row->kodeitem, 'cabang' => $cabang])->getRow();
				if ($DataBarangPaket) {
					$DataTampung2[] = ['idData' => 1];
				} else {
					$DataTampung2[] = ['idData' => 0];
				}
			}
			$response = [
				'DataOne' => $query,
				'DataTwo' => $DataTampung2
			];
			echo json_encode($response);
		}

		public function showbarang($kode, $cust, $kodeso)
		{
			$db = db_connect();
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$session = session();
			$cabang = $session->get('cabang');
			$builder = $db->table('ar_sodetail a');
			$builder->select('*');
			$builder->join('inv_barang b', 'b.kodeitem=a.kodeitem', 'left');
			$builder->join('inv_barang_harga c', 'c.kodeitem=a.kodeitem', 'left');
			$builder->Where(['a.kodeso' => $kodeso, 'a.cabang' => $cabang, 'b.kdcbg' => $cabang, 'c.kode_customer' => $cust, 'c.cabang' => $cabang, 'b.kodeitem' => $kode]);
			$query = $builder->get()->getRow();

			$query2 = $builder->getWhere(['a.kodeso' => $kodeso, 'a.kodeitem' => $kode])->getRow();

			$DataBarangPaket = $db->table('inv_barang_paket')->getWhere(['kodeitem_barang' => $kode, 'cabang' => $cabang])->getRow();
			if ($DataBarangPaket) {
				$idData = 0;
			} else {
				$idData = 1;
			}
			$response = [
				'DataOne' => $query,
				'DataTwo' => $idData,
				'DataThree' => $query2

			];
			echo json_encode($response);
		}

		public function ShowBatchPaketDisiapkan($kode, $gudang)
		{
			if (!empty($kode)) {
				$db = db_connect();
				$session = session();
				$cabang = $session->get('cabang');
				$query = "SELECT a.*, b.namabarang 
						from inv_nobatch a
						inner join inv_barang b on b.kodeitem=a.kodeitem 
						where b.kodeitem = '$kode' and a.cabang='$cabang' and a.gudang='$gudang'
						and b.kdcbg='$cabang' and b.kdgudang='$gudang' GROUP BY no_batch order by a.no_batch asc";
				$data  = $db->query($query)->getResult();

				echo "
				<table id='DatatablePaket' class='table table-hover table-striped table-bordered table-condensed table-scrollable'>
					<tr>
						<th>Kode item</th>
						<th>Nama Barang</th>
						<th>Nobatch</th>
						<th>Tanggal exp</th>
						<th>stok Batch</th>
						<th></th>
					</tr>
					<tbody>";
				$no = 0;
				foreach ($data as $Result) {
					if ($Result->qty_saldo > '0') {

						echo "
						<tr>
							<td>" . $Result->kodeitem . "</td>
							<td>" . $Result->namabarang  . "</td>
							<td>" . $Result->no_batch . "</td>
							<td>" . $Result->tanggal_exp  . "</td>
							<td>" . $Result->qty_saldo . "</td>
							<td>" . "<button type='button' onclick='post_batch(" . $Result->kodeitem . "','" . $Result->no_batch . "','" . $Result->tanggal_exp . "','" . $Result->qty_saldo . ")' class='btn green' id='btntutup' data-dismiss='modal'><i class='fa fa-plus'></i> </button>" . "</td>
						</tr>";
						$no++;
					}
				}
				echo "
					</tbody>
				</table>";
			}
		}

		public function CheckBarangPaket($kode)
		{
			$db = db_connect();
			$session = session();
			$query = $db->table("inv_barang_paket")->getWhere(['kodeitem_barang' => $kode, 'cabang' => $session->get('cabang')])->getResult();
			echo json_encode($query);
		}

		public function ShowPaketDisiapkan($kode, $qty, $vid)
		{
			if (!empty($kode)) {

				$db = db_connect();
				$session = session();
				$cabang = $session->get('cabang');
				$buildBarangPaket  = $db->table('inv_barang_paket');
				$ResultBarangPaket = $buildBarangPaket->getWhere(array('kodeitem_barang' => $kode, 'cabang' => $cabang))->getResult();
				?>
					<div class="table-responsive">
						<button type="button" onclick="TerapkanBatchPaket(<?= $vid ?>)" class="btn green" id="terapkanpaket"><i class="fa fa-plus"></i> Terapkan</button>
						<table id='DatatablePaket' class='table table-hover table-striped table-bordered table-condensed table-scrollable'>
							<thead>
								<th style='text-align: center'>Nama Barang Paket</th>
								<th style='text-align: center'>Kuantitas</th>
								<th style='text-align: center'>Satuan</th>
								<th style='text-align: center'>Batch ~ ED ~ Stock</th>
							</thead>
							<tbody>

								<?php
								$no = 1;
								foreach ($ResultBarangPaket as $Result) {

									$hitung = ($qty * $Result->qty_paket);
									$BarangPaket = $db->table('inv_barang')->getWhere(['kodeitem' => $Result->kodeitem_barang, 'kdcbg' => $cabang])->getRow();
								?>
									<tr>
										<td>
											<input id="kodebarang_paket<?= $no ?>" name="kodebarang_paket" class="form-control input-large" type="hidden" value="<?= $Result->kodeitem_barang ?>">
											<input id="namabarang_paket<?= $no ?>" name="namabarang_paket" class="form-control input-large" type="hidden" value="<?= $BarangPaket->namabarang ?>">
											<input id="namabarangPaketDetail<?= $no ?>" name="namabarangPaketDetail" class="form-control input-large" type="text" value="<?= $Result->namabarang_paket ?>">
											<input id="kodebarang_detail<?= $no ?>" name="kodebarang_detail" class="form-control input-large" type="hidden" value="<?= $Result->kodeitem_paket ?>">
										</td>
										<td>
											<input id="hitung_paket<?= $no ?>" name="hitung_paket" class="form-control input-small" type="text" value="<?php echo number_format($hitung) ?>" readonly>
										</td>
										<td id="satuan_paket">
											<input id="satuan_paket<?= $no ?>" name="satuan_paket" class="form-control input-small" type="text" value="<?= $Result->satuan_paket ?>">
										</td>
										<td id="batchpaket">
											<select name="batchpaket" id="batchpaket<?= $no ?>" class="selectbatch form-select input-medium" onchange="CheckQtyBatchPaket(this.value, <?= $no ?>)">
												<option value="">-- pilih batch --</option>
												<?php
												$ResultBatch = $db->table('inv_nobatch')->getWhere(['kodeitem' => $Result->kodeitem_paket, 'cabang' => $cabang])->getResult();
												foreach ($ResultBatch as $ResBatch) {
													if ($ResBatch->qty_saldo > '0') {

												?>
														<option value="<?= $ResBatch->no_batch ?>"> <?= $ResBatch->no_batch ?> ~ <?= $ResBatch->tanggal_exp ?> ~ <?= $ResBatch->qty_saldo ?> </option>
												<?php
													}
												}
												?>
											</select>
											<input id="edpaket<?= $no ?>" name="edpaket" class="form-control input-small" type="hidden" value="" placeholder="tanggal exp">
											<input id="stockbatchpaket<?= $no ?>" name="stockbatchpaket" class="form-control input-small" type="hidden" value="0" placeholder="stock batch">
										</td>
									</tr>
								<?php
									$no++;
								} ?>
								<input id="countpaket" name="countpaket" class="form-control input-medium" type="hidden" value="<?= $no ?>">
							<?php
						} else {
							echo "
						</tbody>
					</table>
				</div>";
						}
					}

					public function SelectBatchPaket($batch, $kode)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$query = $db->table("inv_nobatch")->getWhere(['kodeitem' => $kode, 'no_batch' => $batch, 'cabang' => $cabang])->getRow();
						echo json_encode($query);
					}

					public function pesanandisiapkansavelpb()
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$akses = $model_Global->cek_menu_akses($level, 422);
						// Table
						$builder            = $db->table('ap_lpb');
						$builderDetail      = $db->table('ap_lpbdetil');
						$buildSOFile      	= $db->table('ar_sofile');
						$buildPOFile      	= $db->table('ap_pofile');
						$buildPODetail		= $db->table('ap_podetail');
						$buildPUFile        = $db->table('ap_pufile');
						$buildPUDetail      = $db->table('ap_pudetail');
						$builderINVBarang   = $db->table('inv_barang');
						$INVBarangBatch     = $db->table('inv_nobatch');
						$INVhargapercust    = $db->table('inv_barang_harga');
						$INVtransaksi       = $db->table('inv_transaksi');
						try {
							$db->transStart();
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$nomorbukti  	= $input['NoPBBeli'];
								$nokodepu  		= $input['NoPUBeli'];
								$kodeso  		= $input['kodeso'];

								$Datalpb = $builder->getWhere(['nolpb' => $nomorbukti, 'kodecbg' => '01'])->getRow();
								if (empty($Datalpb)) {
									$nobukti = $nomorbukti;
								} else {
									$nobukti = $model_Global->_Autonomor('PB');
								}

								$DataPU = $buildPUFile->getWhere(['kodepu' => $nokodepu, 'kodecbg' => '01'])->getRow();
								if (empty($DataPU)) {
									$kodepu = $nokodepu;
								} else {
									$kodepu = $model_Global->_Autonomor('PU');
								}
								if ($param != 0) {

									// $dataD = $builderDetail->where(['nolpb' => $nomorbukti, 'gudang' => $input['gudang']])->get()->getResult();
									// foreach ($dataD as $row) {
									// 	$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$row->qtyterima' where kodepo = '$kodepo' and kodeitem='$row->kodeitem'");
									// }
									// $builderDetail->delete(['nolpb' => $nomorbukti]);
								}
								$RowSOfile = $buildSOFile->getWhere(['kodeso' => $kodeso, 'kodecbg' => $session->get('cabang')])->getRow();
								if ($RowSOfile->kodepo != '') {
									$RowPOFile = $buildPOFile->getWhere(['kodepo' => $RowSOfile->kodepo, 'kodecbg' => '01'])->getRow();
									$RowLPB = $builder->getWhere(['kodepo' => $RowSOfile->kodepo, 'kodecbg' => '01'])->getRow();
									$ResultPODetail = $buildPODetail->getWhere(['kodepo' => $RowSOfile->kodepo])->getResult();
									$tot = 0;
									$tdisc = 0;
									foreach ($ResultPODetail as $res) {

										$harga = $res->hargabeli;

										$vjum  = $res->qtyorder * $harga;
										$vdisc = $vjum * ($res->disc / 100);
										$tot   = $tot + $vjum;
										$tdisc = $tdisc + $vdisc;

										$datad = array(
											'kodeitem' => $res->kodeitem,
											'qtyterima' => $res->qtyorder,
											'qtyorder' => $res->qtyorder,
											'satuan' => $res->satuan,
											'disc' => $res->disc,
											'hargabeli' => $res->hargabeli,
											'gudang' => 1,
											'no_batch'   => '0000',
											'tanggal_exp' => '0000'
										);

										$datadPU = array(
											'kodeitem' => $res->kodeitem,
											'qtypu' => $res->qtyorder,
											'satuan' => $res->satuan,
											'no_batch' => '0000',
											'tanggal_exp' => '0000',
											'hargabeli' => $res->hargabeli,
											'disc' => $res->disc,
											'gudang' => 1,
										);

										if ($cabang == "01") {
											$gudang = 1;
										} else {
											$gudang = 3;
										}

										$data_batch = array(
											'no_batch'   => '0000',
											'kd_jenis'   => 'Pembelian',
											'kodeitem' => $res->kodeitem,
											'qty_terima' => $res->qtyorder,
											'qty_saldo' => $res->qtyorder,
											'gudang' => $gudang,
											'cabang' => $cabang,
											'tanggal_exp' => '0000',
										);

										$data_inv = array(
											'tanggal'   => date('Y-m-d'),
											'kodeitem' => $res->kodeitem,
											'penerimaan' => $res->qtyorder,
											'pengeluaran' => 0,
											'satuan' => $res->satuan,
											'jenis' => 'Pembelian',
											'nobukti' => $kodepu,
											'tglrekam'   => date('Y-m-d H:i:s'),
											'hpp'   => $res->hargabeli,
											'hargajual'   => 0,
											'gudang' => 1,
											'no_batch'   => '0000',
											'tanggal_exp' => '0000',
											'cabang' => '01',
										);

										$DataInv = $builderINVBarang->getWhere(['kodeitem' => $res->kodeitem, 'kdcbg' => $cabang])->getRow();

										$data_harga_percustomer = array(
											'kodeitem' => $res->kodeitem,
											'nama_barang' => $DataInv->namabarang,
											'hargamodal_spl' => $res->hargabeli,
											'kode_spl' => $RowPOFile->kodesup,
											'bruto_spl' => $res->hargabeli,
											'disc_spl' => $res->disc,
											'netto_spl' => $harga - ($res->disc / 100) * $harga,
											'tanggal' => $input['tanggal'],
											'cabang' => '01',
											'gudang' => 1,
											'tglrekam' => date('Y-m-d H:i:s'),
											'userid' => $session->get('username'),
										);

										$update_hargapercs = array(
											'kode_spl'   => $RowPOFile->kodesup,
											'bruto_spl' => $harga,
											'disc_spl' => $res->disc,
											'netto_spl' => $harga - ($res->disc / 100) * $harga,
										);

										$update_spl_barang = array(
											'kode_spl1'   => $RowPOFile->kodesup,
											'bruto_spl1' => $harga,
											'disc_spl1' => $res->disc,
											'netto_spl1' => $harga - ($res->disc / 100) * $harga,
										);

										$view_batch = $INVBarangBatch->getWhere(['no_batch' => '0000', 'kodeitem' => $res->kodeitem,  'cabang' => $cabang])->getRow();
										$view_percustomer = $db->query("SELECT * FROM inv_barang_harga where kodeitem='$res->kodeitem' and cabang='01' group by kodeitem");

										if ($res->kodeitem != "") {
											if ($param == 0) {

												if (empty($RowLPB)) {
													$datad['nolpb'] = $nobukti;
													$builderDetail->insert($datad);
													$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$res->qtyorder' where kodepo = '$RowSOfile->kodepo' and kodeitem='$res->kodeitem'");

													$datadPU['kodepu'] = $kodepu;
													$buildPUDetail->insert($datadPU);
													$INVtransaksi->insert($data_inv);
													if (empty($view_batch)) {
														$INVBarangBatch->insert($data_batch);
													} else {
														$ResultUpdateStokBarangBatch = [
															'qty_terima ' => $view_batch->qty_terima + $res->qtyorder,
															'qty_saldo ' => $view_batch->qty_saldo + $res->qtyorder,
															'tanggal_exp' => '0000'
														];
														$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $res->kodeitem,  'cabang' => $cabang])->update($ResultUpdateStokBarangBatch);
													}

													if (empty($view_percustomer)) {
														$INVhargapercust->insert($data_harga_percustomer);
													} else {
														$INVhargapercust->where(['kodeitem' => $res->kodeitem])->update($update_hargapercs);
													}

													if ($DataInv->kode_spl1 == $RowPOFile->kodesup || empty($DataInv->kode_spl1)) {
														$builderINVBarang->where(['kodeitem' => $res->kodeitem])->update($update_spl_barang);
													} else if ($DataInv->kode_spl2 == $RowPOFile->kodesup || empty($DataInv->kode_spl2)) {
														$spl2InvBarang = array(
															'kode_spl2'   => $RowPOFile->kodesup,
															'bruto_spl2' => $harga,
															'disc_spl2' => $res->disc,
															'netto_spl2' => $harga - ($res->disc / 100) * $harga
														);
														$builderINVBarang->where(['kodeitem' => $res->kodeitem])->update($spl2InvBarang);
													} else if ($DataInv->kode_spl3 == $RowPOFile->kodesup || empty($DataInv->kode_spl3)) {
														$spl3InvBarang = array(
															'kode_spl3'   => $RowPOFile->kodesup,
															'bruto_spl3' => $harga,
															'disc_spl3' => $res->disc,
															'netto_spl3' => $harga - ($res->disc / 100) * $harga
														);
														$builderINVBarang->where(['kodeitem' => $res->kodeitem])->update($spl3InvBarang);
													} else if ($DataInv->kode_spl4 == $RowPOFile->kodesup || empty($DataInv->kode_spl4)) {
														$spl4InvBarang = array(
															'kode_spl4'   => $RowPOFile->kodesup,
															'bruto_spl4' => $harga,
															'disc_spl4' => $res->disc,
															'netto_spl4' => $harga - ($res->disc / 100) * $harga
														);
														$builderINVBarang->where(['kodeitem' => $res->kodeitem])->update($spl4InvBarang);
													}

													$ResultUpdateStokBarang = [
														'stok ' => $DataInv->stok + $res->qtyorder,
														'stok_so ' => $DataInv->stok_so + $res->qtyorder
													];
													$builderINVBarang->where(['kodeitem' => $res->kodeitem, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													$netto_brg = $harga - ($res->disc / 100) * $harga;
													$HrgBarang = [
														'hargabeli' => $netto_brg,
														'tglbeliakhir' => $input['tanggal']
													];
													$builderINVBarang->where(['kodeitem' => $res->kodeitem])->update($HrgBarang);
												} else {
													$DataLpbDetail = $builderDetail->getWhere(['nolpb' => $RowLPB->nolpb, 'kodeitem' => $res->kodeitem])->getRow();
													$DataPU = $buildPUFile->getWhere(['kodepb' => $RowLPB->nolpb])->getRow();
													if ($res->qtyorder > $DataLpbDetail->qtyterima) {
														$hitungQty = $res->qtyorder - $DataLpbDetail->qtyterima;
														$db->query("UPDATE ap_lpbdetil set qtyterima = qtyterima + '$hitungQty' where nolpb = '$RowLPB->nolpb' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$hitungQty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE ap_pudetail set qtypu = qtypu + '$hitungQty' where kodepu = '$DataPU->kodepu' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE inv_transaksi set penerimaan = penerimaan + '$hitungQty' where nobukti = '$DataPU->kodepu' and kodeitem='$res->kodeitem'");
														$ResultUpdateStokBarangBatch = [
															'qty_terima ' => $view_batch->qty_terima + $hitungQty,
															'qty_saldo ' => $view_batch->qty_saldo + $hitungQty,
															'tanggal_exp' => '0000'
														];
														$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $res->kodeitem,  'cabang' => $cabang])->update($ResultUpdateStokBarangBatch);

														$ResultUpdateStokBarang = [
															'stok ' => $DataInv->stok + $hitungQty,
															'stok_so ' => $DataInv->stok_so + $hitungQty
														];
														$builderINVBarang->where(['kodeitem' => $res->kodeitem, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													} else if ($res->qtyorder < $DataLpbDetail->qtyterima) {
														$hitungQty = $DataLpbDetail->qtyterima - $res->qtyorder;
														$db->query("UPDATE ap_lpbdetil set qtyterima = qtyterima - '$hitungQty' where nolpb = '$RowLPB->nolpb' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$hitungQty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE ap_pudetail set qtypu = qtypu - '$hitungQty' where kodepu = '$DataPU->kodepu' and kodeitem='$res->kodeitem'");
														$db->query("UPDATE inv_transaksi set penerimaan = penerimaan - '$hitungQty' where nobukti = '$DataPU->kodepu' and kodeitem='$res->kodeitem'");
														$ResultUpdateStokBarangBatch = [
															'qty_terima ' => $view_batch->qty_terima - $hitungQty,
															'qty_saldo ' => $view_batch->qty_saldo - $hitungQty,
															'tanggal_exp' => '0000'
														];
														$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $res->kodeitem,  'cabang' => $cabang])->update($ResultUpdateStokBarangBatch);

														$ResultUpdateStokBarang = [
															'stok ' => $DataInv->stok - $hitungQty,
															'stok_so ' => $DataInv->stok_so - $hitungQty
														];
														$builderINVBarang->where(['kodeitem' => $res->kodeitem, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													}
												}
											} else {
												$datad['nolpb'] = $nomorbukti;
												// $builderDetail->insert($datad);
												// $db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$res->qtyorder' where kodepo = '$RowSOfile->kodepo' and kodeitem='$res->kodeitem'");
											}
										}
									}


									$data = array(
										'kodecbg'       => '01',
										'kodesup'       => $RowPOFile->kodesup,
										'kodepo'        => $RowSOfile->kodepo,
										'noterima'      => 'ADJ',
										'gudang'        => 1,
										'tgllpb'        => date('Y-m-d', strtotime($input['tanggal'])),
										'tglkirim'      => date('Y-m-d', strtotime($input['tanggal'])),
										'kodeuser'      => $session->get('username'),
										'statusid'      => 1,
										'no_terima'     => 3,
									);

									$dataPUFile = array(
										'kodecbg' 			=> '01',
										'kodesup' 			=> $RowPOFile->kodesup,
										'nomorfaktur'  		=> 'ADJ',
										'nomorfaktur_pajak' => '-',
										'pembayaran'  		=> 'T',
										'kodepo'  			=> $RowSOfile->kodepo,
										'tglpu'   			=> date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
										'tglkirim'			=> date('Y-m-d', strtotime($input['tanggal'])),
										'tgljthtempo'		=> $input['tanggal'],
										'kodeuser'			=> $session->get('username'),
										'ket'     			=> $input['keterangan'],
										'dpp'     			=> $RowPOFile->dpp,
										'ppn'     			=> $RowPOFile->ppn,
										'diskon'  			=> $RowPOFile->diskon,
										'biayalain'			=> $RowPOFile->biayalain,
										'totalpu' 			=> $RowPOFile->totalpo,
										'sppn'    			=> $RowPOFile->sppn,
										'statusid'			=> 2,
										'retur'				=> '1',
									);
									$datalogs = [
										'date' => date('Y-m-d H:i:s'),
										'userid' => $session->get('username'),
									];


									if ($param == 0) {
										if (empty($RowLPB)) {
											$data['nolpb'] = $nobukti;
											$builder->insert($data);
											$datalogs['detail'] = 'Tambah data Pembelian PO Terima dengan kode ' . $nobukti;
											$db->table("logs")->insert($datalogs);
											$model_Global->_updatecounter1('PB');

											$dataPUFile['kodepb'] = $nobukti;
											$dataPUFile['kodepu'] = $kodepu;
											$buildPUFile->insert($dataPUFile);
											$model_Global->_updatecounter1('PU');
										}
										echo json_encode(['status' => 1, 'message' => $nobukti]);
									} else {
										$data['nolpb'] = $nomorbukti;
										$builder->where(['nolpb' => $nomorbukti, 'kodecbg' => '01'])->update($data);
										$datalogs['detail'] = 'Edit data Pembelian PO Terima dengan kode ' . $nomorbukti;
										$db->table("logs")->insert($datalogs);
										echo json_encode(['status' => 1, 'message' => $nomorbukti]);
									}
								} else {
									echo json_encode(['status' => 1, 'message' => 'cek']);
								}
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
							$db->transComplete();
							if ($db->transStatus() === false) {
								// Jika terjadi kesalahan, batalkan transaksi
								$db->transRollback();
								echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
							}
							// Transaksi sukses, commit perubahan ke database
							$db->transCommit();
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function savelpbPerBarang($kode, $NoBuktiPB, $NoBuktiPU, $NoBuktiSO, $sodetailid, $qty)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$akses = $model_Global->cek_menu_akses($level, 422);
						// Table
						$builder            = $db->table('ap_lpb');
						$builderDetail      = $db->table('ap_lpbdetil');
						$buildSOFile      	= $db->table('ar_sofile');
						$buildPOFile      	= $db->table('ap_pofile');
						$buildPODetail		= $db->table('ap_podetail');
						$buildPUFile        = $db->table('ap_pufile');
						$buildPUDetail      = $db->table('ap_pudetail');
						$builderINVBarang   = $db->table('inv_barang');
						$INVBarangBatch     = $db->table('inv_nobatch');
						$INVtransaksi       = $db->table('inv_transaksi');
						//Tabel Mutasi Cabang
						$TableMohonMutasiCabang 	= $db->table('inv_mohon_mutasi_cabang');
						$TableMutasiCabang 			= $db->table('inv_mutasi_cabang');
						$TableMutasiCabangDetail 	= $db->table('inv_mutasi_cabang_detail');
						$TableAccMutasiCabang 		= $db->table('inv_acc_mutasi_cabang');
						$TableAccMutasiCabangDetail = $db->table('inv_accd_mutasi_cabang');
						$TableInvGudang 			= $db->table('inv_gudang');
						$TableInvBarangGudang 		= $db->table('inv_barang_gudang');
						try {
							$db->transStart();
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$nomorbukti  	= $NoBuktiPB;
								$nokodepu  		= $NoBuktiPU;
								$kodeso  		= $NoBuktiSO;

								$Datalpb = $builder->getWhere(['nolpb' => $nomorbukti, 'kodecbg' => '01'])->getRow();
								if (empty($Datalpb)) {
									$nobukti = $nomorbukti;
								} else {
									$nobukti = $model_Global->_Autonomor('PB');
								}

								$DataPU = $buildPUFile->getWhere(['kodepu' => $nokodepu, 'kodecbg' => '01'])->getRow();
								if (empty($DataPU)) {
									$kodepu = $nokodepu;
								} else {
									$kodepu = $model_Global->_Autonomor('PU');
								}
								$RowSOfile = $buildSOFile->getWhere(['kodeso' => $kodeso, 'kodecbg' => $session->get('cabang')])->getRow();
								if ($RowSOfile->kodepo != '') {
									$RowPOFile = $buildPOFile->getWhere(['kodepo' => $RowSOfile->kodepo, 'kodecbg' => '01'])->getRow();
									$RowLPB = $builder->getWhere(['kodepo' => $RowSOfile->kodepo, 'kodecbg' => '01'])->getRow();
									$RowPoDetail = $buildPODetail->getWhere(['kodepo' => $RowSOfile->kodepo, 'kodeitem' => $kode, 'sodetail_id' => $sodetailid])->getRow();
									$tot = 0;
									$tdisc = 0;

									$harga = $RowPoDetail->hargabeli;

									$vjum  = $qty * $harga;
									$vdisc = $vjum * ($RowPoDetail->disc / 100);
									$tot   = $tot + $vjum;
									$tdisc = $tdisc + $vdisc;

									$datad = array(
										'kodeitem' => $RowPoDetail->kodeitem,
										'qtyterima' => $qty,
										'qtyorder' => $qty,
										'satuan' => $RowPoDetail->satuan,
										'disc' => $RowPoDetail->disc,
										'hargabeli' => $RowPoDetail->hargabeli,
										'gudang' => 1,
										'no_batch'   => '0000',
										'tanggal_exp' => '0000',
										'podetail_id' => $RowPoDetail->id,
									);

									$datadPU = array(
										'kodeitem' => $RowPoDetail->kodeitem,
										'qtypu' => $qty,
										'satuan' => $RowPoDetail->satuan,
										'no_batch' => '0000',
										'tanggal_exp' => '0000',
										'hargabeli' => $RowPoDetail->hargabeli,
										'disc' => $RowPoDetail->disc,
										'gudang' => 1,
										'podetail_id' => $RowPoDetail->id,
									);

									$data_batch = array(
										'no_batch'   => '0000',
										'kd_jenis'   => 'Pembelian',
										'kodeitem' => $RowPoDetail->kodeitem,
										'qty_terima' => $qty,
										'qty_saldo' => $qty,
										'gudang' => 1,
										'cabang' => '01',
										'tanggal_exp' => '0000',
									);

									$data_inv = array(
										'tanggal'   => date('Y-m-d'),
										'kodeitem' => $RowPoDetail->kodeitem,
										'penerimaan' => $qty,
										'pengeluaran' => 0,
										'satuan' => $RowPoDetail->satuan,
										'jenis' => 'Pembelian',
										'tglrekam'   => date('Y-m-d H:i:s'),
										'hpp'   => $RowPoDetail->hargabeli,
										'hargajual'   => 0,
										'gudang' => 1,
										'no_batch'   => '0000',
										'tanggal_exp' => '0000',
										'cabang' => '01',
										'ket' => 'ADJ AUTO',
									);

									$DataInv = $builderINVBarang->getWhere(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => '01'])->getRow();

									$view_batch = $INVBarangBatch->getWhere(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => '01'])->getRow();

									if ($RowPoDetail->kodeitem != "") {

										if (empty($RowLPB)) {
											$datad['nolpb'] = $nobukti;
											$builderDetail->insert($datad);
											$insertIdLPBDetail = $builderDetail->db()->insertID();
											$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$qty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$RowPoDetail->kodeitem' and id = '$RowPoDetail->id'");

											$datadPU['kodepu'] = $kodepu;
											$datadPU['lpbdetail_id'] = $insertIdLPBDetail;
											$buildPUDetail->insert($datadPU);
											$insertIdPUDetail = $buildPUDetail->db()->insertID();
											$data_inv['nobukti'] = $kodepu;
											$data_inv['nobukti_id'] = $insertIdPUDetail;
											$INVtransaksi->insert($data_inv);
											if (empty($view_batch)) {
												$INVBarangBatch->insert($data_batch);
											} else {
												$ResultUpdateStokBarangBatch = [
													'qty_terima ' => $view_batch->qty_terima + $qty,
													'qty_saldo ' => $view_batch->qty_saldo + $qty,
													'tanggal_exp' => '0000'
												];
												$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => '01'])->update($ResultUpdateStokBarangBatch);
											}

											$ResultUpdateStokBarang = [
												'stok ' => $DataInv->stok + $qty,
												'stok_so ' => $DataInv->stok_so + $qty
											];
											$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => '01'])->update($ResultUpdateStokBarang);

											//Insert Data Header ap_lpb  dan ap_pufile
											$DataHeaderlpb = array(
												'kodecbg'       => '01',
												'kodesup'       => $RowPOFile->kodesup,
												'kodepo'        => $RowSOfile->kodepo,
												'kodeso'        => $RowSOfile->kodeso,
												'noterima'      => 'ADJ',
												'gudang'        => 1,
												'tgllpb'        => date('Y-m-d'),
												'tglkirim'      => date('Y-m-d'),
												'kodeuser'      => $session->get('username'),
												'statusid'      => 1,
												'no_terima'     => 3,
											);

											$DataHeaderPUFile = array(
												'kodecbg' 			=> '01',
												'kodesup' 			=> $RowPOFile->kodesup,
												'nomorfaktur'  		=> 'ADJ',
												'nomorfaktur_pajak' => '-',
												'pembayaran'  		=> 'T',
												'kodepo'  			=> $RowSOfile->kodepo,
												'tglpu'   			=> date('Y-m-d H:i:s'),
												'tglkirim'			=> date('Y-m-d'),
												'tgljthtempo'		=> date('Y-m-d'),
												'kodeuser'			=> $session->get('username'),
												'ket'     			=> 'ADJ AUTO',
												'dpp'     			=> $RowPOFile->dpp,
												'ppn'     			=> $RowPOFile->ppn,
												'diskon'  			=> $RowPOFile->diskon,
												'biayalain'			=> $RowPOFile->biayalain,
												'totalpu' 			=> $RowPOFile->totalpo,
												'sppn'    			=> $RowPOFile->sppn,
												'statusid'			=> 2,
												'retur'				=> '1',
											);

											$datalogs = [
												'date' => date('Y-m-d H:i:s'),
												'userid' => $session->get('username'),
											];

											if (empty($RowLPB)) {
												$DataHeaderlpb['nolpb'] = $nobukti;
												$builder->insert($DataHeaderlpb);
												$datalogs['detail'] = 'Tambah data Pembelian PO Terima dengan kode ' . $nobukti;
												$db->table("logs")->insert($datalogs);
												$model_Global->_updatecounter1('PB');

												$DataHeaderPUFile['kodepb'] = $nobukti;
												$DataHeaderPUFile['kodepu'] = $kodepu;
												$buildPUFile->insert($DataHeaderPUFile);
												$model_Global->_updatecounter1('PU');
											}

											//Insert Mutasi Cabang Dan Acc Mutasi Cabang

											if ($cabang == "02") {
												$NomohonMMC = $model_Global->_Autonomor('MMC');
												$NomutasiMC = $model_Global->_Autonomor('MC');
												$NoAccAMC = $model_Global->_Autonomor('AMC');
												$DatamohonCabang = $TableMohonMutasiCabang->getWhere(['nomohon' => $NomohonMMC, 'cabang' => $cabang])->getRow();

												if (empty($DatamohonCabang)) {
													$nobuktiMMC = $NomohonMMC;
												} else {
													$nobuktiMMC = $model_Global->_Autonomor('MMC');
												}

												$DatamutasiCabang = $TableMutasiCabang->getWhere(['nomutasi_cabang' => $NomutasiMC, 'cabang' => '01'])->getRow();

												if (empty($DatamutasiCabang)) {
													$nobuktiMC = $NomutasiMC;
												} else {
													$nobuktiMC = $model_Global->_Autonomor('MC');
												}

												$DataAccmutasiCabang = $TableAccMutasiCabang->getWhere(['noacc_mutasi' => $NoAccAMC, 'cabang' => $cabang])->getRow();

												if (empty($DataAccmutasiCabang)) {
													$nobuktiAMC = $NoAccAMC;
												} else {
													$nobuktiAMC = $model_Global->_Autonomor('AMC');
												}

												$DataGudangMinta     = $TableInvGudang->getWhere(['cabang' => $cabang])->getRow();
												$DataGudangKirim   = $TableInvGudang->getWhere(['cabang' => '01'])->getRow();

												//Insert Data MUTASI CABANG
												$data_headerMC = array(
													'nomutasi_cabang' => $nobuktiMC,
													'nomohon' => $nobuktiMMC,
													'cabang_asal' => '01',
													'cabang_tujuan' => $cabang,
													'gudang_asal' => $DataGudangKirim->kode,
													'gudang_tujuan' => $DataGudangMinta->kode,
													'keterangan' => 'ADJ AUTO Barang Pack Packan',
													'tanggal' => date('Y-m-d'),
													'tglrekam' => date('Y-m-d H:i:s'),
													'userid' => $session->get('username'),
													'statusid' => 2,
													'cabang' => '01',
												);
												$TableMutasiCabang->insert($data_headerMC);
												$model_Global->_updatecounter1('MC');
												$model_Global->_updatecounter1('MMC');
												$rowmutasiCabang = $TableMutasiCabang->getWhere(['nomutasi_cabang' => $nobuktiMC, 'cabang' => '01'])->getRow();
												$datadMC = array(
													'nomutasi_cabang' => $nobuktiMC,
													'id_mutasi_cabang' => $rowmutasiCabang->id_mutasi_cabang,
													'kodeitem' => $RowPoDetail->kodeitem,
													'nobatch' => '0000',
													'tglexp' => '0000',
													'qty' => $qty,
													'satuan' => $RowPoDetail->satuan,
													'harga' => $RowPoDetail->hargabeli,
													'cabang' => '01',
													'pudetail_id' => $insertIdPUDetail
												);
												$TableMutasiCabangDetail->insert($datadMC);
												$insertIdMC = $TableMutasiCabangDetail->db()->insertID();

												// INSERT DATA ACC MUTASI CABANG
												$data_headerAMC = array(
													'noacc_mutasi' => $nobuktiAMC,
													'nomutasi_cabang' => $nobuktiMC,
													'nomohon' => $nobuktiMMC,
													'cabang_terima' => $cabang,
													'cabang_kirim' => '01',
													'gudang_terima' => $DataGudangMinta->kode,
													'gudang_kirim' => $DataGudangKirim->kode,
													'keterangan' => 'ADJ AUTO Barang Pack Packan',
													'tanggal' => date('Y-m-d'),
													'tglrekam' => date('Y-m-d H:i:s'),
													'userid' => $session->get('username'),
													'statusid' => '1',
													'cabang' => $cabang,
													'kodepu' => $kodepu,
												);

												$TableAccMutasiCabang->insert($data_headerAMC);
												$model_Global->_updatecounter1('AMC');

												$barang = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => '01'])->get()->getRow();
												$datadAMC = array(
													'noacc_mutasi' => $nobuktiAMC,
													'kodeitem' => $RowPoDetail->kodeitem,
													'nobatch' => '0000',
													'tglexp' => '0000',
													'qtyacc' => $qty,
													'satuan' => $RowPoDetail->satuan,
													'harga' => $RowPoDetail->hargabeli,
													'cabang' => $cabang,
													'mutasimc_id' => $insertIdMC
												);

												$inv_barang_gudang = array(
													'kodeitem' => $RowPoDetail->kodeitem,
													'gudang' => $DataGudangMinta->kode,
													'qty' => $qty,
													'cabang' => $cabang,
												);

												$inv_nobatch = array(
													'no_batch' => '0000',
													'kd_jenis' => 'Mutasi Cabang',
													'kodeitem' => $RowPoDetail->kodeitem,
													'qty_terima' => $qty,
													'qty_saldo' => $qty,
													'gudang' => $DataGudangMinta->kode,
													'cabang' => $cabang,
													'tanggal_exp' => '0000',
												);

												$inv_barang = array(
													'kodeitem' => $RowPoDetail->kodeitem,
													'kdcbg' => $cabang,
													'namabarang' => $barang->namabarang,
													'hargabeli' => $barang->hargabeli,
													'hargajual' => $barang->hargajual,
													'tglbeliakhir' => $barang->tglbeliakhir,
													'stok' => $qty,
													'stok_so' => $qty,
													'satuan' => $barang->satuan,
													'kdgudang' => $DataGudangMinta->kode,
													'kdrak' => $barang->kdrak,
													'kdmerk' => $barang->kdmerk,
													'kode_user' => $session->get('username'),
													'ppn' => $barang->ppn,
													'kode_spl1' => $barang->kode_spl1,
													'bruto_spl1' => $barang->bruto_spl1,
													'disc_spl1' => $barang->disc_spl1,
													'netto_spl1' => $barang->netto_spl1,

													'kode_spl2' => $barang->kode_spl2,
													'bruto_spl2' => $barang->bruto_spl2,
													'disc_spl2' => $barang->disc_spl2,
													'netto_spl2' => $barang->netto_spl2,

													'kode_spl3' => $barang->kode_spl3,
													'bruto_spl3' => $barang->bruto_spl3,
													'disc_spl3' => $barang->disc_spl3,
													'netto_spl3' => $barang->netto_spl3,

													'kode_spl4' => $barang->kode_spl4,
													'bruto_spl4' => $barang->bruto_spl4,
													'disc_spl4' => $barang->disc_spl4,
													'netto_spl4' => $barang->netto_spl4,
												);

												$rowmutasi = $TableMutasiCabangDetail->where(['kodeitem' => $RowPoDetail->kodeitem, 'nobatch' => '0000', 'nomutasi_cabang' => $nobuktiMC, 'cabang' => '01', 'idcabang_detail' => $insertIdMC])->get()->getRow();

												$TableAccMutasiCabangDetail->insert($datadAMC);
												$insertIdAMC = $TableAccMutasiCabangDetail->db()->insertID();
												$TableMutasiCabangDetail->where(['kodeitem' => $RowPoDetail->kodeitem, 'nobatch' => '0000', 'nomutasi_cabang' => $nobuktiMC, 'cabang' => '01', 'idcabang_detail' => $insertIdMC])->update(['qtysetuju' => $rowmutasi->qtysetuju + $qty]);


												$cek_barang = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => $cabang])->get()->getRow();
												$GudangBarangKirim = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->get()->getRow();

												$cek_nobatch = $INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'cabang' => $cabang, 'gudang' => $DataGudangMinta->kode])->get()->getRow();
												$Batchkirim = $INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'cabang' => '01', 'gudang' => $DataGudangKirim->kode])->get()->getRow();

												$cekBrgGudang = $TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->get()->getRow();
												$BarangGudangKirim = $TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->get()->getRow();
												if ($BarangGudangKirim == null) {
													$qtyBarangGudang = 0;
												} else {
													$qtyBarangGudang = $BarangGudangKirim->qty;
												}

												//update dan insert inv_barang
												if (!empty($cek_barang)) {
													$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => $cabang])->update(['stok' => $cek_barang->stok + $qty, 'stok_so' => $cek_barang->stok_so + $qty]);
													$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->update(['stok' => $GudangBarangKirim->stok -  $qty, 'stok_so' => $GudangBarangKirim->stok_so - $qty]);
												} else {
													$builderINVBarang->insert($inv_barang);
													$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->update(['stok' => $GudangBarangKirim->stok - $qty, 'stok_so' => $GudangBarangKirim->stok_so + $qty]);
												}

												//update dan insert inv_nobatch
												if (!empty($cek_nobatch)) {
													$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty_jual' => $Batchkirim->qty_jual + $qty, 'qty_saldo' => $Batchkirim->qty_saldo - $qty]);
													$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->update(['qty_terima' => $cek_nobatch->qty_terima + $qty, 'qty_saldo' => $cek_nobatch->qty_saldo + $qty]);
												} else {
													$INVBarangBatch->insert($inv_nobatch);
													$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty_jual' => $Batchkirim->qty_jual - $qty, 'qty_saldo' => $Batchkirim->qty_saldo - $qty]);
												}

												//update dan insert inv_barang_gudang
												if (!empty($cekBrgGudang)) {
													$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty' => $qtyBarangGudang -  $qty]);
													$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->update(['qty' => $cekBrgGudang->qty + $qty]);
												} else {
													$TableInvBarangGudang->insert($inv_barang_gudang);
													$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty' => $qtyBarangGudang -  $qty]);
												}


												$DataInvKirim = array(
													'tanggal'   => date('Y-m-d'),
													'kodeitem' => $RowPoDetail->kodeitem,
													'penerimaan' => 0,
													'pengeluaran' => $qty,
													'satuan' => $RowPoDetail->satuan,
													'jenis' => 'Mutasi Cabang',
													'nobukti' => $nobuktiAMC,
													'nobukti_id' => $insertIdAMC,
													'tglrekam'   => date('Y-m-d H:i:s'),
													'hpp'   => $barang->hargabeli,
													'hargajual'   => 0,
													'gudang' => $DataGudangKirim->kode,
													'no_batch' => '0000',
													'tanggal_exp' => '0000',
													'cabang' => '01',
												);
												$INVtransaksi->insert($DataInvKirim);

												$DataInvTerima = array(
													'tanggal'   => date('Y-m-d'),
													'kodeitem' => $RowPoDetail->kodeitem,
													'penerimaan' =>  $qty,
													'pengeluaran' => 0,
													'satuan' => $RowPoDetail->satuan,
													'jenis' => 'Mutasi Cabang',
													'nobukti' => $nobuktiAMC,
													'nobukti_id' => $insertIdAMC,
													'tglrekam'   => date('Y-m-d H:i:s'),
													'hpp'   => $barang->hargabeli,
													'hargajual'   => 0,
													'gudang' => $DataGudangMinta->kode,
													'no_batch' => '0000',
													'tanggal_exp' => '0000',
													'cabang' => $cabang,
												);

												$INVtransaksi->insert($DataInvTerima);
											}
										} else {
											$DataLpbDetail = $builderDetail->getWhere(['nolpb' => $RowLPB->nolpb, 'kodeitem' => $RowPoDetail->kodeitem, 'podetail_id' => $RowPoDetail->id])->getRow();
											$DataPUDetail = $buildPUDetail->getWhere(['podetail_id' => $RowPoDetail->id])->getRow();
											$DataPU = $buildPUFile->getWhere(['kodepb' => $RowLPB->nolpb])->getRow();
											if (!empty($DataLpbDetail)) {
												$DataInvStock = $builderINVBarang->getWhere(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => $cabang])->getRow();

												$view_batchStock = $INVBarangBatch->getWhere(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => $cabang])->getRow();
												if ($RowPoDetail->qtyorder > $DataLpbDetail->qtyterima) {
													$hitungQty = $RowPoDetail->qtyorder - $DataLpbDetail->qtyterima;
													$db->query("UPDATE ap_lpbdetil set qtyterima = qtyterima + '$hitungQty',qtyorder = qtyorder + '$hitungQty'  where nolpb = '$RowLPB->nolpb' and kodeitem='$RowPoDetail->kodeitem' and podetail_id = '$RowPoDetail->id'");
													$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$hitungQty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$RowPoDetail->kodeitem' and id = '$RowPoDetail->id'");
													$db->query("UPDATE ap_pudetail set qtypu = qtypu + '$hitungQty' where kodepu = '$DataPU->kodepu' and kodeitem='$RowPoDetail->kodeitem' and podetail_id = '$RowPoDetail->id'");
													$db->query("UPDATE inv_transaksi set penerimaan = penerimaan + '$hitungQty' where nobukti = '$DataPU->kodepu' and nobukti_id ='$DataPUDetail->id' and kodeitem='$RowPoDetail->kodeitem'");
													$ResultUpdateStokBarangBatch = [
														'qty_terima ' => $view_batchStock->qty_terima + $hitungQty,
														'qty_saldo ' => $view_batchStock->qty_saldo + $hitungQty,
														'tanggal_exp' => '0000'
													];
													$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => $cabang])->update($ResultUpdateStokBarangBatch);

													$ResultUpdateStokBarang = [
														'stok ' => $DataInvStock->stok + $hitungQty,
														'stok_so ' => $DataInvStock->stok_so + $hitungQty
													];
													$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													if ($cabang == '02') {
														$DataAccMutasiCabang = $TableAccMutasiCabang->getWhere(['kodepu' => $DataPU->kodepu, 'cabang' => $cabang])->getRow();
														$rowmutasiCabangDetail = $TableMutasiCabangDetail->getWhere(['nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang, 'cabang' => '01', 'kodeitem' => $RowPoDetail->kodeitem, 'pudetail_id' => $DataPUDetail->id])->getRow();
														$DataAMCDetail = $TableAccMutasiCabangDetail->getWhere(['kodeitem' => $RowPoDetail->kodeitem, 'mutasimc_id' => $rowmutasiCabangDetail->idcabang_detail])->getRow();
														//Update acc mutasi cabang
														$qtymutasicabangdetail = intval($rowmutasiCabangDetail->qty) + $hitungQty;
														$TableAccMutasiCabangDetail->where([
															'noacc_mutasi' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => $cabang,
															'mutasimc_id' => $rowmutasiCabangDetail->idcabang_detail
														])->update([
															'qtyacc' => $qtymutasicabangdetail
														]);
														//Update mutasi cabang
														$TableMutasiCabangDetail->where([
															'nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => '01',
															'pudetail_id' => $DataPUDetail->id
														])->update([
															'qty' => $qtymutasicabangdetail,
															'qtysetuju' => $rowmutasiCabangDetail->qtysetuju + $hitungQty
														]);
														//Update inv transaksi cabang mutasi
														$INVtransaksi->where([
															'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => '01',
															'nobukti_id' => $DataAMCDetail->idaccd_mutasi_cabang
														])->update([
															'pengeluaran' => $qtymutasicabangdetail
														]);
														//Update inv transaksi cabang mutasi acc
														$INVtransaksi->where([
															'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' =>  $cabang
														])->update([
															'penerimaan' => $qtymutasicabangdetail
														]);
													}
												} else if ($RowPoDetail->qtyorder < $DataLpbDetail->qtyterima) {
													$hitungQty = $DataLpbDetail->qtyterima - $RowPoDetail->qtyorder;
													$db->query("UPDATE ap_lpbdetil set qtyterima = qtyterima - '$hitungQty' where nolpb = '$RowLPB->nolpb' and kodeitem='$RowPoDetail->kodeitem' and podetail_id = '$RowPoDetail->id'");
													$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$hitungQty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$RowPoDetail->kodeitem' and id = '$RowPoDetail->id'");
													$db->query("UPDATE ap_pudetail set qtypu = qtypu - '$hitungQty' where kodepu = '$DataPU->kodepu' and kodeitem='$RowPoDetail->kodeitem' and podetail_id = '$RowPoDetail->id'");
													$db->query("UPDATE inv_transaksi set penerimaan = penerimaan - '$hitungQty' where nobukti = '$DataPU->kodepu'  and nobukti_id ='$DataPUDetail->id' and kodeitem='$RowPoDetail->kodeitem'");
													$ResultUpdateStokBarangBatch = [
														'qty_terima ' => $view_batchStock->qty_terima - $hitungQty,
														'qty_saldo ' => $view_batchStock->qty_saldo - $hitungQty,
														'tanggal_exp' => '0000'
													];
													$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => $cabang])->update($ResultUpdateStokBarangBatch);

													$ResultUpdateStokBarang = [
														'stok ' => $DataInvStock->stok - $hitungQty,
														'stok_so ' => $DataInvStock->stok_so - $hitungQty
													];
													$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													if ($cabang == '02') {
														$DataAccMutasiCabang = $TableAccMutasiCabang->getWhere(['kodepu' => $DataPU->kodepu, 'cabang' => $cabang])->getRow();
														$rowmutasiCabangDetail = $TableMutasiCabangDetail->getWhere(['nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang, 'cabang' => '01', 'kodeitem' => $RowPoDetail->kodeitem, 'pudetail_id' => $DataPUDetail->id])->getRow();
														$DataAMCDetail = $TableAccMutasiCabangDetail->getWhere(['kodeitem' => $RowPoDetail->kodeitem, 'mutasimc_id' => $rowmutasiCabangDetail->idcabang_detail])->getRow();
														//Update acc mutasi cabang
														$TableAccMutasiCabangDetail->where([
															'noacc_mutasi' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => $cabang,
															'mutasimc_id' => $rowmutasiCabangDetail->idcabang_detail
														])->update([
															'qtyacc' => $rowmutasiCabangDetail->qty - $hitungQty
														]);
														//Update mutasi cabang
														$TableMutasiCabangDetail->where([
															'nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => '01',
															'pudetail_id' => $DataPUDetail->id
														])->update([
															'qty' => $rowmutasiCabangDetail->qty - $hitungQty,
															'qtysetuju' => $rowmutasiCabangDetail->qtysetuju - $hitungQty
														]);
														//Update inv transaksi cabang mutasi
														$INVtransaksi->where([
															'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' => '01',
															'nobukti_id' => $$DataAMCDetail->idaccd_mutasi_cabang
														])->update([
															'pengeluaran' => $rowmutasiCabangDetail->qty - $hitungQty
														]);
														//Update inv transaksi cabang mutasi acc
														$INVtransaksi->where([
															'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
															'kodeitem' => $RowPoDetail->kodeitem,
															'cabang' =>  $cabang,
															'nobukti_id' => $$DataAMCDetail->idaccd_mutasi_cabang
														])->update([
															'penerimaan' => $rowmutasiCabangDetail->qty - $hitungQty
														]);
													}
												}
											} else {
												$datad['nolpb'] = $RowLPB->nolpb;
												$builderDetail->insert($datad);
												$insertIdLPBDetail = $builderDetail->db()->insertID();
												$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$qty' where kodepo = '$RowSOfile->kodepo' and kodeitem='$RowPoDetail->kodeitem' and id = '$RowPoDetail->id'");

												$datadPU['kodepu'] = $DataPU->kodepu;
												$datadPU['lpbdetail_id'] = $insertIdLPBDetail;
												$buildPUDetail->insert($datadPU);
												$insertIdPUDetail = $buildPUDetail->db()->insertID();
												$data_inv['nobukti'] = $DataPU->kodepu;
												$data_inv['nobukti_id'] = $insertIdPUDetail;
												$INVtransaksi->insert($data_inv);
												if (empty($view_batch)) {
													$INVBarangBatch->insert($data_batch);
												} else {
													$ResultUpdateStokBarangBatch = [
														'qty_terima ' => $view_batch->qty_terima + $qty,
														'qty_saldo ' => $view_batch->qty_saldo + $qty,
														'tanggal_exp' => '0000'
													];
													$INVBarangBatch->where(['no_batch' => '0000', 'kodeitem' => $RowPoDetail->kodeitem,  'cabang' => '01'])->update($ResultUpdateStokBarangBatch);
												}

												$ResultUpdateStokBarang = [
													'stok ' => $DataInv->stok + $qty,
													'stok_so ' => $DataInv->stok_so + $qty
												];
												$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => '01'])->update($ResultUpdateStokBarang);

												if ($cabang == "02") {
													$DataAccMutasiCabang = $TableAccMutasiCabang->getWhere(['kodepu' => $DataPU->kodepu, 'cabang' => $cabang])->getRow();
													$rowmutasiCabang = $TableMutasiCabang->getWhere(['nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang, 'cabang' => '01'])->getRow();
													$datadMC = array(
														'nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang,
														'id_mutasi_cabang' => $rowmutasiCabang->id_mutasi_cabang,
														'kodeitem' => $RowPoDetail->kodeitem,
														'nobatch' => '0000',
														'tglexp' => '0000',
														'qty' => $qty,
														'satuan' => $RowPoDetail->satuan,
														'harga' => $RowPoDetail->hargabeli,
														'cabang' => '01',
														'pudetail_id' => $insertIdPUDetail
													);
													$TableMutasiCabangDetail->insert($datadMC);
													$insertIdMC = $TableMutasiCabangDetail->db()->insertID();

													$DataGudangMinta     = $TableInvGudang->getWhere(['cabang' => $cabang])->getRow();
													$DataGudangKirim   = $TableInvGudang->getWhere(['cabang' => '01'])->getRow();

													$barang = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdcbg' => '01'])->get()->getRow();
													$datadAMC = array(
														'noacc_mutasi' => $DataAccMutasiCabang->noacc_mutasi,
														'kodeitem' => $RowPoDetail->kodeitem,
														'nobatch' => '0000',
														'tglexp' => '0000',
														'qtyacc' => $qty,
														'satuan' => $RowPoDetail->satuan,
														'harga' => $RowPoDetail->hargabeli,
														'cabang' => $cabang,
														'mutasimc_id' => $insertIdMC
													);

													$inv_barang_gudang = array(
														'kodeitem' => $RowPoDetail->kodeitem,
														'gudang' => $DataGudangMinta->kode,
														'qty' => $qty,
														'cabang' => $cabang,
													);

													$inv_nobatch = array(
														'no_batch' => '0000',
														'kd_jenis' => 'Mutasi Cabang',
														'kodeitem' => $RowPoDetail->kodeitem,
														'qty_terima' => $qty,
														'qty_saldo' => $qty,
														'gudang' => $DataGudangMinta->kode,
														'cabang' => $cabang,
														'tanggal_exp' => '0000',
													);

													$inv_barang = array(
														'kodeitem' => $RowPoDetail->kodeitem,
														'kdcbg' => $cabang,
														'namabarang' => $barang->namabarang,
														'hargabeli' => $barang->hargabeli,
														'hargajual' => $barang->hargajual,
														'tglbeliakhir' => $barang->tglbeliakhir,
														'stok' => $qty,
														'stok_so' => $qty,
														'satuan' => $barang->satuan,
														'kdgudang' => $DataGudangMinta->kode,
														'kdrak' => $barang->kdrak,
														'kdmerk' => $barang->kdmerk,
														'kode_user' => $session->get('username'),
														'ppn' => $barang->ppn,
														'kode_spl1' => $barang->kode_spl1,
														'bruto_spl1' => $barang->bruto_spl1,
														'disc_spl1' => $barang->disc_spl1,
														'netto_spl1' => $barang->netto_spl1,

														'kode_spl2' => $barang->kode_spl2,
														'bruto_spl2' => $barang->bruto_spl2,
														'disc_spl2' => $barang->disc_spl2,
														'netto_spl2' => $barang->netto_spl2,

														'kode_spl3' => $barang->kode_spl3,
														'bruto_spl3' => $barang->bruto_spl3,
														'disc_spl3' => $barang->disc_spl3,
														'netto_spl3' => $barang->netto_spl3,

														'kode_spl4' => $barang->kode_spl4,
														'bruto_spl4' => $barang->bruto_spl4,
														'disc_spl4' => $barang->disc_spl4,
														'netto_spl4' => $barang->netto_spl4,
													);

													$rowmutasi = $TableMutasiCabangDetail->where(['kodeitem' => $RowPoDetail->kodeitem, 'nobatch' => '0000', 'nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang, 'cabang' => '01', 'idcabang_detail' => $insertIdMC])->get()->getRow();

													$TableAccMutasiCabangDetail->insert($datadAMC);
													$insertIdAMC = $TableAccMutasiCabangDetail->db()->insertID();
													$TableMutasiCabangDetail->where(['kodeitem' => $RowPoDetail->kodeitem, 'nobatch' => '0000', 'nomutasi_cabang' => $DataAccMutasiCabang->nomutasi_cabang, 'cabang' => '01', 'idcabang_detail' => $insertIdMC])->update(['qtysetuju' => $rowmutasi->qtysetuju + $qty]);

													$cek_barang = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => $cabang])->get()->getRow();
													$GudangBarangKirim = $builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->get()->getRow();

													$cek_nobatch = $INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'cabang' => $cabang, 'gudang' => $DataGudangMinta->kode])->get()->getRow();
													$Batchkirim = $INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'cabang' => '01', 'gudang' => $DataGudangKirim->kode])->get()->getRow();

													$cekBrgGudang = $TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->get()->getRow();
													$BarangGudangKirim = $TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->get()->getRow();
													if ($BarangGudangKirim == null) {
														$qtyBarangGudang = 0;
													} else {
														$qtyBarangGudang = $BarangGudangKirim->qty;
													}

													//update dan insert inv_barang
													if (!empty($cek_barang)) {
														$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => $cabang])->update(['stok' => $cek_barang->stok + $qty, 'stok_so' => $cek_barang->stok_so + $qty]);
														$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->update(['stok' => $GudangBarangKirim->stok -  $qty, 'stok_so' => $GudangBarangKirim->stok_so - $qty]);
													} else {
														$builderINVBarang->insert($inv_barang);
														$builderINVBarang->where(['kodeitem' => $RowPoDetail->kodeitem, 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => '01'])->update(['stok' => $GudangBarangKirim->stok - $qty, 'stok_so' => $GudangBarangKirim->stok_so + $qty]);
													}

													//update dan insert inv_nobatch
													if (!empty($cek_nobatch)) {
														$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty_jual' => $Batchkirim->qty_jual + $qty, 'qty_saldo' => $Batchkirim->qty_saldo - $qty]);
														$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->update(['qty_terima' => $cek_nobatch->qty_terima + $qty, 'qty_saldo' => $cek_nobatch->qty_saldo + $qty]);
													} else {
														$INVBarangBatch->insert($inv_nobatch);
														$INVBarangBatch->where(['kodeitem' => $RowPoDetail->kodeitem, 'no_batch' => '0000', 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty_jual' => $Batchkirim->qty_jual - $qty, 'qty_saldo' => $Batchkirim->qty_saldo - $qty]);
													}

													//update dan insert inv_barang_gudang
													if (!empty($cekBrgGudang)) {
														$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty' => $qtyBarangGudang -  $qty]);
														$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangMinta->kode, 'cabang' => $cabang])->update(['qty' => $cekBrgGudang->qty + $qty]);
													} else {
														$TableInvBarangGudang->insert($inv_barang_gudang);
														$TableInvBarangGudang->where(['kodeitem' => $RowPoDetail->kodeitem, 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty' => $qtyBarangGudang -  $qty]);
													}


													$DataInvKirim = array(
														'tanggal'   => date('Y-m-d'),
														'kodeitem' => $RowPoDetail->kodeitem,
														'penerimaan' => 0,
														'pengeluaran' => $qty,
														'satuan' => $RowPoDetail->satuan,
														'jenis' => 'Mutasi Cabang',
														'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
														'nobukti_id' => $insertIdAMC,
														'tglrekam'   => date('Y-m-d H:i:s'),
														'hpp'   => $barang->hargabeli,
														'hargajual'   => 0,
														'gudang' => $DataGudangKirim->kode,
														'no_batch' => '0000',
														'tanggal_exp' => '0000',
														'cabang' => '01',
													);
													$INVtransaksi->insert($DataInvKirim);

													$DataInvTerima = array(
														'tanggal'   => date('Y-m-d'),
														'kodeitem' => $RowPoDetail->kodeitem,
														'penerimaan' =>  $qty,
														'pengeluaran' => 0,
														'satuan' => $RowPoDetail->satuan,
														'jenis' => 'Mutasi Cabang',
														'nobukti' => $DataAccMutasiCabang->noacc_mutasi,
														'nobukti_id' => $insertIdAMC,
														'tglrekam'   => date('Y-m-d H:i:s'),
														'hpp'   => $barang->hargabeli,
														'hargajual'   => 0,
														'gudang' => $DataGudangMinta->kode,
														'no_batch' => '0000',
														'tanggal_exp' => '0000',
														'cabang' => $cabang,
													);

													$INVtransaksi->insert($DataInvTerima);
												}
											}
										}
										$DataInvBarangRow = $db->query("SELECT * from inv_barang where kodeitem='$RowPoDetail->kodeitem' and kdcbg='$cabang'")->getRow();
										if (empty($RowLPB)) {
											$MessageNobukti = $nobukti;
										} else {
											$MessageNobukti = $RowLPB->nolpb;
										}
										echo json_encode(['status' => 1, 'message' => $MessageNobukti, 'stok' => $DataInvBarangRow->stok]);
									}
								} else {
									echo json_encode(['status' => 1, 'message' => 'cek']);
								}
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
							$db->transComplete();
							if ($db->transStatus() === false) {
								// Jika terjadi kesalahan, batalkan transaksi
								$db->transRollback();
								echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
							}
							// Transaksi sukses, commit perubahan ke database
							$db->transCommit();
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function pesanandisiapkansave()
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];
						$session = session();
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 422);
						// Table
						$builder            = $db->table('ar_kirim');
						$builderDetail      = $db->table('ar_kirimdetil');
						$buildKirimPaket    = $db->table('ar_kirimpaket');
						try {
							$db->transStart();
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$kodeso   	= $input['kodeso'];
								$sescabang 	= $session->get('cabang');

								$DataKirim = $builder->getWhere(['kodekirim' => $input['nomorbukti']])->getRow();

								if ($param == 0) {
									if (empty($DataKirim)) {
										if ($input['sppn'] == 'Y') {
											$nobukti = $input['nomorbukti'];
										} else {
											$nobukti = $input['nomorbukti'];
										}
									} else {
										if ($input['sppn'] == 'Y') {
											$nobukti = $model_Global->_Autonomor('SDP');
										} else {
											$nobukti = $model_Global->_Autonomor('SD');
										}
									}
								}

								if ($param != 0) {

									$datalpb = $builderDetail->getWhere(['kodekirim' => $input['nomorbukti'], 'gudang' => $input['gudang']])->getResult();
									foreach ($datalpb as $row) {
										$db->query("UPDATE ar_sodetail set qtykirim = qtykirim - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kodeso = '$kodeso' and cabang='$sescabang' and ar_kirimdetil_id = $row->id");
									}
									$builderDetail->delete(['kodekirim' => $input['nomorbukti'], 'gudang' => $input['gudang']]);
									$buildKirimPaket->delete(['kodekirim' => $input['nomorbukti'], 'gudang' => $input['gudang'], 'cabang' => $sescabang]);
								}

								$kode  		= $input['kode'];
								$idkode  	= $input['idkode'];
								$no_batch  	= $input['no_batch'];
								$tgl_exp  	= $input['tgl_exp'];
								$qty   		= $input['qty'];
								$sat   		= $input['sat'];

								$jumdata  = count($kode);

								for ($i = 0; $i < $jumdata; $i++) {
									$datad = array(
										'no_po'   => $input['no-po'],
										'kodeitem' => $kode[$i],
										'kodeso'  => $input['kodeso'],
										'no_batch'  => $no_batch[$i],
										'tgl_exp'  => $tgl_exp[$i],
										'qtykirim' => $qty[$i],
										'satuan' => $sat[$i],
										'gudang' => $input['gudang'],
										'sodetail_id' => $idkode[$i],
									);

									if ($kode[$i] != "") {
										if ($param == 0) {
											$datad['kodekirim'] = $nobukti;
											$builderDetail->insert($datad);
										} else {
											$datad['kodekirim'] = $input['nomorbukti'];
											$builderDetail->insert($datad);
										}
										if ($kodeso != "") {
											$DataSodeteail = $db->query("SELECT * from ar_sodetail where kodeso = '$kodeso' and kodeitem = '$kode[$i]' and id='$idkode[$i]' and cabang='$sescabang'")->getRow();
											$insertId = $builderDetail->db()->insertID();
											if ($DataSodeteail) {
												$db->query("UPDATE ar_sodetail set qtykirim = qtykirim + '$qty[$i]', ar_kirimdetil_id = $insertId where kodeitem = '$kode[$i]' and id='$idkode[$i]' and kodeso = '$kodeso' and cabang='$sescabang'");
											} else {
												$db->query("UPDATE ar_sodetail set qtykirim = qtykirim + '$qty[$i]', ar_kirimdetil_id = $insertId where kodeitem = '$kode[$i]' and id='$idkode[$i]' and kodeso = '$kodeso' and cabang='$sescabang'");
											}
										}
									}
								}

								$kodebarangpaket  		= $input['kodebarangpaket'];
								if ($kodebarangpaket != null) {
									$jumdata2  = count($kodebarangpaket);
									for ($i = 0; $i < $jumdata2; $i++) {
										$datapaket = array(
											'kodeitem_paket'  => $input['kodebarangdetail'][$i],
											'kodeso'  => $input['kodeso'],
											'nobatch_paket'  => $input['no_batchpaket'][$i],
											'tglexp_paket'  => $input['edpaket'][$i],
											'qtykirim_paket' => $input['qtypaket'][$i],
											'satuan' => $input['satpaket'][$i],
											'kodeitem_barang' => $kodebarangpaket[$i],
											'gudang' => $input['gudang'],
											'cabang' => $session->get('cabang'),
											'id_kodeitembarang' => $input['idkodeitembarang'][$i],
										);

										if ($kodebarangpaket[$i] != "") {
											if ($param == 0) {
												$datapaket['kodekirim'] = $nobukti;
												$buildKirimPaket->insert($datapaket);
											} else {
												$datapaket['kodekirim'] = $input['nomorbukti'];
												$buildKirimPaket->insert($datapaket);
											}
										}
									}
								}

								$DataApLpb = $db->table('ap_lpb')->getWhere(['kodeso' => $input['kodeso'], 'kodecbg' => '01'])->getRow();
								if ($DataApLpb->nolpb == null) {
									$ShowNolpb = '';
								} else {
									$ShowNolpb = $DataApLpb->nolpb;
								}

								$DataApPU = $db->table('ap_pufile')->getWhere(['kodepb' => $DataApLpb->nolpb, 'kodecbg' => '01'])->getRow();
								if ($DataApPU->kodepu == null) {
									$ShowNoPU = '';
								} else {
									$ShowNoPU = $DataApPU->kodepu;
								}
								$data = array(
									'kodecbg' => $session->get('cabang'),
									'kodecust' => $input['cust'],
									'kodeso'  => $input['kodeso'],
									'nolpb'  => $ShowNolpb,
									'kodepu'  => $ShowNoPU,
									'gudang'  => $input['gudang'],
									'tglkirim'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
									'kodeuser' => $session->get('username'),
									'keterangan' => $input['keterangan'],
									'alamat' => $input['alamat'],
								);

								//Table 2
								$buildarSofile   = $db->table('ar_sofile');
								$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ar_sodetail where kodeso = '$kodeso' and cabang='$sescabang' ")->getRow()->total;

								if ($total <= 0) {
									$buildarSofile->where(['kodeso' => $kodeso, 'kodecbg' => $sescabang])->update(['statusid' => 2, 'no_get' => 2]);
								} else {
									$buildarSofile->where(['kodeso' => $kodeso, 'kodecbg' => $sescabang])->update(['statusid' => 3, 'no_get' => 1]);
								}

								$datalogs = [
									'date' => date('Y-m-d H:i:s'),
									'userid' => $session->get('username'),
								];

								if ($param == 0) {
									$data['kodekirim'] = $nobukti;
									$data['statusid'] = '1';
									$builder->insert($data);
									$datalogs['detail'] = 'Tambah data Penjualan Pesanan Disiapkan dengan kode ' . $nobukti;
									$db->table("logs")->insert($datalogs);
									if ($input['sppn'] == 'Y') {
										$model_Global->_updatecounter1('SDP');
									} else {
										$model_Global->_updatecounter1('SD');
									}
									echo json_encode(['status' => 1, 'message' => $nobukti]);
								} else {
									$builder->where(['kodekirim' => $input['nomorbukti'], 'kodecbg' => $sescabang])->update($data);
									$datalogs['detail'] = 'Update data Penjualan Pesanan Disiapkan dengan kode ' . $input['nomorbukti'];
									$db->table("logs")->insert($datalogs);
									echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
								}
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
							$db->transComplete();
							if ($db->transStatus() === false) {
								// Jika terjadi kesalahan, batalkan transaksi
								$db->transRollback();
								echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
							}
							// Transaksi sukses, commit perubahan ke database
							$db->transCommit();
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function pesanandisiapkandelete($nomor)
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$db->transStart();
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$akses = $model_Global->cek_menu_akses($level, 422);
						$builder            = $db->table('ar_kirim');
						$builderDetail      = $db->table('ar_kirimdetil');
						$buildKirimPaket    = $db->table('ar_kirimpaket');
						$buildSofile        = $db->table('ar_sofile');
						$buildSifile        = $db->table('ar_sifile');
						$buildSiDetail      = $db->table('ar_sidetail');
						$buildPODetail     	= $db->table('ap_podetail');
						$buildLpb           = $db->table('ap_lpb');
						$buildLpbDetail     = $db->table('ap_lpbdetil');
						$buildPU       		= $db->table('ap_pufile');
						$buildPUDetail      = $db->table('ap_pudetail');
						$buildInvBatch  	= $db->table('inv_nobatch');
						$buildInvTransaksi  = $db->table('inv_transaksi');
						$buildInvMC     	= $db->table('inv_mutasi_cabang');
						$buildInvMCDetail   = $db->table('inv_mutasi_cabang_detail');
						$buildInvAMC      	= $db->table('inv_acc_mutasi_cabang');
						$buildInvAMCDetail  = $db->table('inv_accd_mutasi_cabang');
						if ($akses->udel == 1) {
							$arKirim = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();

							if ($arKirim->kodeso != "") {
								$arKirimDetail = $builderDetail->getWhere(['kodekirim' => $arKirim->kodekirim, 'gudang' => $arKirim->gudang])->getResult();
								foreach ($arKirimDetail as $row) {
									$db->query("UPDATE ar_sodetail set qtykirim = qtykirim - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kodeso = '$arKirim->kodeso' and id='$row->sodetail_id' and cabang='$cabang' ");
									$DataPODetail = $buildPODetail->getWhere(['sodetail_id' => $row->sodetail_id, 'kodeitem' => $row->kodeitem])->getRow();
									if (!empty($DataPODetail)) {
									     $DataLpbDetail = $buildLpbDetail->getWhere(['nolpb' => $arKirim->nolpb, 'kodeitem' => $row->kodeitem, 'podetail_id' => $DataPODetail->id])->getRow();
									}
									if (!empty($DataLpbDetail)) {
										$ArSidetail = $buildSiDetail->getWhere(['kodesd' => $arKirim->kodekirim, 'kodeitem' => $row->kodeitem])->getRow();
										if (!empty($ArSidetail)) {
											$db->query("UPDATE inv_barang set stok = stok - '$row->qtykirim',stok_so = stok_so - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kdcbg = '$cabang' ");
											$db->query("UPDATE inv_nobatch set qty_terima = qty_terima - '$row->qtykirim',qty_saldo = qty_saldo - '$row->qtykirim' where kodeitem = '$row->kodeitem' and cabang = '$cabang' and no_batch='$row->no_batch' ");
										} else {
											$db->query("UPDATE inv_barang set stok = stok - '$row->qtykirim',stok_so = stok_so - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kdcbg = '$cabang' ");
											$db->query("UPDATE inv_nobatch set qty_terima = qty_terima - '$row->qtykirim',qty_saldo = qty_saldo - '$row->qtykirim' where kodeitem = '$row->kodeitem' and cabang = '$cabang' and no_batch='$row->no_batch' ");
										}
										$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$row->qtykirim' where kodeitem = '$row->kodeitem' and sodetail_id = '$row->sodetail_id' ");
									}
								}

								foreach ($arKirimDetail as $row) {
									$DataPODetail = $buildPODetail->getWhere(['sodetail_id' => $row->sodetail_id, 'kodeitem' => $row->kodeitem])->getRow();
									if (!empty($DataPODetail)) {
									    $DataLpbDetail = $buildLpbDetail->getWhere(['nolpb' => $arKirim->nolpb, 'kodeitem' => $row->kodeitem, 'podetail_id' => $DataPODetail->id])->getRow();
									}
									$DataPUDetail = $buildPUDetail->getWhere(['lpbdetail_id' => $DataLpbDetail->id])->getRow();
									if ($cabang == '02') {
										$DataAMC = $buildInvAMC->getWhere(['kodepu' => $arKirim->kodepu, 'cabang' => $cabang])->getRow();
									}

									if (!empty($DataLpbDetail)) {
										$ArSidetail = $buildSiDetail->getWhere(['kodesd' => $arKirim->kodekirim, 'kodeitem' => $row->kodeitem])->getRow();
										if ($totalKirimCek > 1) {
											$db->query("UPDATE ap_lpbdetil set qtyterima = qtyterima - '$row->qtykirim' where kodeitem = '$row->kodeitem' and nolpb = '$arKirim->nolpb' and podetail_id = '$DataPODetail->id' ");
											$db->query("UPDATE ap_pudetail set qtypu = qtypu - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kodepu = '$arKirim->kodepu' and lpbdetail_id = '$DataLpbDetail->id' ");
											$db->query("UPDATE inv_transaksi set penerimaan = penerimaan - '$row->qtykirim' where kodeitem = '$row->kodeitem' and nobukti = '$arKirim->kodepu' and nobukti_id = '$DataPUDetail->id'");
											if ($cabang == '02') {
												$db->query("UPDATE inv_transaksi set pengeluaran = pengeluaran - '$row->qtykirim' where kodeitem = '$row->kodeitem' and nobukti = '$DataAMC->noacc_mutasi' and cabang='01' ");
												$db->query("UPDATE inv_transaksi set penerimaan = penerimaan - '$row->qtykirim' where kodeitem = '$row->kodeitem' and nobukti = '$DataAMC->noacc_mutasi' and cabang='$cabang' ");
												$db->query("UPDATE inv_mutasi_cabang_detail set qty = qty - '$row->qtykirim', qtysetuju = qtysetuju - '$row->qtykirim'  where kodeitem = '$row->kodeitem' and nomutasi_cabang = '$DataAMC->nomutasi_cabang' and cabang='01' ");
												$db->query("UPDATE inv_accd_mutasi_cabang set qtyacc = qtyacc - '$row->qtykirim' where kodeitem = '$row->kodeitem' and noacc_mutasi = '$DataAMC->noacc_mutasi' and cabang='$cabang' ");
											}
										} else {
											$delLpb    			= $buildLpb->delete(['nolpb' => $arKirim->nolpb, 'kodecbg' => '01']);
											$delLpbDetail   	= $buildLpbDetail->delete(['nolpb' => $arKirim->nolpb]);
											$delPU    			= $buildPU->delete(['kodepu' => $arKirim->kodepu, 'kodecbg' => '01']);
											$delPUDetail   		= $buildPUDetail->delete(['kodepu' => $arKirim->kodepu]);
											$delInvTransaksi 	= $buildInvTransaksi->delete(['nobukti' => $arKirim->kodepu]);
											if ($cabang == '02') {
												$delInvTransaksiAMC = $buildInvTransaksi->delete(['nobukti' => $DataAMC->noacc_mutasi]);
												$delMC   			= $buildInvMC->delete(['nomutasi_cabang' => $DataAMC->nomutasi_cabang]);
												$delMCDetail   		= $buildInvMCDetail->delete(['nomutasi_cabang' => $DataAMC->nomutasi_cabang]);
												$delAMC				= $buildInvAMC->delete(['noacc_mutasi' => $DataAMC->noacc_mutasi, 'cabang' => $cabang]);
												$delAMCDetail   	= $buildInvAMCDetail->delete(['noacc_mutasi' => $DataAMC->noacc_mutasi]);
											}
										}
									}
								}
							}

							$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ar_sodetail where kodeso = '$arKirim->kodeso' ")->getRow()->total;

							if ($total <= 0) {
								$buildSofile->where(['kodeso' => $arKirim->kodeso, 'kodecbg' => $cabang])->update(['statusid' => 2]);
							} else {
								$buildSofile->where(['kodeso' => $arKirim->kodeso, 'kodecbg' => $cabang])->update(['statusid' => 1, 'no_get' => 1]);
							}

							$datalogs = [
								'detail' => 'Hapus data Penjualan Pesanan Disiapkan dengan Kode ' . $arKirim->kodekirim,
								'date' => date('Y-m-d H:i:s'),
								'userid' => $session->get('username'),
							];


							$buildKirimPaket   = $buildKirimPaket->delete(['kodekirim' => $arKirim->kodekirim]);
							$delDetail    	= $builderDetail->delete(['kodekirim' => $arKirim->kodekirim, 'gudang' => $arKirim->gudang]);
							$DataKirim    	= $builder->delete(['id' => $nomor, 'kodecbg' => $cabang]);
							if ($DataKirim == true && $delDetail == true) {
								$db->table("logs")->insert($datalogs);
								echo json_encode(['message' => 'Deleted']);
							}
						}
						$db->transComplete();
						if ($db->transStatus() === false) {
							// Jika terjadi kesalahan, batalkan transaksi
							$db->transRollback();
							echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
						}
						// Transaksi sukses, commit perubahan ke database
						$db->transCommit();
					}

					// =========================== FAKTUR PESANAN PENJUALAN SELECTION START ======================//
					//Faktur Penjualan Page Start Here
					public function faktur_penjualan()
					{
						$session = session();
						$username = $session->get('username');
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 423);
						if ($akses->uadd == 1) {
							$tambah = 'Daftar Pesanan Siap Faktur';
						} else {
							$tambah = '';
						}

						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Faktur Penjualan',
							'body' => 'admin/penjualan/transaksi/fakturpenjualan',
							'tambah' => $tambah,
							'username' => $username
						];
						return view('layout/layout', $data);
					}

					public function fakturpenjualan_table($tgl1, $tgl2)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$session = session();
						$cabang = $session->get('cabang');
						$level = $session->get('level');
						$lvldebug = $session->get('debug');
						$akses = $model_Global->cek_menu_akses($level, 423);
						$db = db_connect();
						$query = "SELECT * FROM ar_sifile where kodecbg='$cabang'";
						if ($tgl1 != "1" && $tgl2 !== "1") {
							$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
							$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
							$query .= " and tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(tglsi)= (select periode_tahun from ms_identity) and
            							month(tglsi)= (select periode_bulan from ms_identity)";
						}
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						$count = 0;
						foreach ($res as $value) {
							$hitung_bayar = ($value->totalsi - $value->jumlahbayar);
							if ($value->statusid == 1 && $value->pembayaran == 'K') {
								$status = '<span class="badge badge-warning">Belum Lunas</span>';
							} else if ($value->statusid == 2 && $hitung_bayar <= 0 && $hitung_bayar <= 0.00) {
								$status =  '<span class="badge badge-success">Lunas</span>';
							} else if ($value->statusid == '1' && $value->pembayaran === 'T' && $hitung_bayar != 0 && $hitung_bayar != 0.00) {
								$status =  '<span class="badge badge-primary">Tunai</span>';
							} else {
								$status =  '';
							}
							if ($value->retur == 1) {
								$statusretur = '<span class="badge badge-primary">Tidak ada retur</span>';
							} else if ($value->retur == 2) {
								$statusretur =  '<span class="badge badge-success">barang Teretur</span>';
							} else {
								$statusretur = '';
							}
							if ($akses->ukurir == 1) {
								if ($value->step_kurir == 1) {
									$kurir = '<label><span class="badge badge-info btn btn-secondary" onclick="kurir('.$value->id.')" style="height:30px;">Delivery By </br>'.$value->kurir.' </span></label>';
								} else if ($value->step_kurir == 2) {
									$kurir = '<label><span class="badge badge-success" style="height:30px;">Barang Sudah Sampai, Delivery By </br>'.$value->kurir.'</span></label>';
								}else{
									$kurir = '<a href="#" onclick="kurir('.$value->id.')" class="btn btn-dark"><i class="fas fa-shipping-fast text-white"></i> kurir</a>';
								}
							}else{
								if ($value->step_kurir == 1) {
									$kurir = '<label><span class="badge badge-info" style="height:30px;">Delivery By </br>'.$value->kurir.' </span></label>';
								} else if ($value->step_kurir == 2) {
									$kurir = '<label><span class="badge badge-success" style="height:30px;">Barang Sudah Sampai, Delivery By </br>'.$value->kurir.'</span></label>';
								}else {
									$kurir = '';
								}
							}

							if($lvldebug == 1){
								$debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->kodesi . '\')"><i class="fas fa-bug"></i></a>';
							}else{
								$debug = '';
							}
							$count++;
							$data[] = [
								$count,
								$value->kodesi,
								date('d-m-y H:i:s', strtotime($value->tglsi)),
								$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
								$value->ket,
								$status,
								$statusretur,
								date('d-m-y H:i:s', strtotime($value->tgljthtempo)),
								$akses->uharga == 1 ? number_format($hitung_bayar) : '',
								($akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/fakturpenjualan/entri/' . $value->id) . '" class="btn"><i class="fa-solid fa-eye"></i></a>' : '') .
								($debug),
								$kurir,
							];
						}
						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function faktur_penjualan_entri($id = '')
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$taxes = $model_Global->_LoadProfile()->taxes_desimal;
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 423);
						$data = [];
						if ($id > 0) {

							$data       = $db->table('ar_sifile')->where('id', $id)->get()->getRow();
							$DataKirim  = $db->table("ar_kirim")->where(['kodekirim' => $data->kodesd, 'kodecbg' => $data->kodecbg])->get()->getRow();
							$DetailItem = $db->table('ar_sidetail')->where(['kodesi' => $data->kodesi, 'gudang' => $DataKirim->gudang])->get()->getResult();
							$fakturPaket = $db->table("ar_sipaket")->where(['kodesi' => $data->kodesi])->get()->getResult();
							$DataSibiaya = $db->table('ar_sibiaya')->where(['kodesi' => $data->kodesi, 'cabang' => $data->kodecbg])->get()->getResult();
						}
						$res = $db->query("SELECT * FROM ar_customer a inner join ar_kirim b on a.kode = b.kodecust where b.statusid = 1 and b.kodecbg='$cabang' group by b.kodecust")->getResult();
						$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
						$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
						$css = [];
						$js = [];
						$data  = [
							'data' => $data ?? [],
							'DetailItem' => $DetailItem ?? [],
							'BiayaLain' => $DataSibiaya ?? [],
							'fakturPaket' => $fakturPaket ?? [],
							'DataSo' => $DataSo ?? [],
							'cabang' => $session->get('cabang'),
							'nomor' => $model_Global->_Autonomor('SI'),
							'nomor_ppn' => $model_Global->_Autonomor('SIP'),
							'NoFakturPajak' => $model_Global->_AutonomorFakturPenjualan('FP'),
							'gudang' => $gudang,
							'arCust' => $arCust,
							'res' => $res,
							'css' => $css,
							'js' => $js,
							'title' => 'Faktur Penjualan',
							'body' => 'admin/penjualan/transaksi/fakturpenjualanentri',
							'PrintFaktur' => $akses->ucetak,
							'taxes' =>  $taxes
						];
						return view('layout/layout', $data);
					}

					public function ShowSd($cust)
					{
						$db = db_connect();
						if (!empty($cust)) {
							$sd  = $db->query("SELECT * from ar_kirim where kodecust = '$cust' and statusid=1 ")->getResult();
							?>
								<select name="kodesd" id="kodesd" class="form-control  input-medium select2me">
									<option value="">-- NO BUKTI KODEKIRIM ---</option>
									<?php
									foreach ($sd  as $row) {
									?>
										<option value="<?php echo $row->kodekirim; ?>"><?php echo $row->kodekirim; ?></option>

									<?php } ?>
								</select>


							<?php

						} else {
							echo "";
						}
					}

					public function getcs($kode)
					{
						$db = db_connect();
						$data = $db->query("SELECT * from ar_customer a inner join ar_sales b on a.kodesales=b.id where  a.kode='$kode'")->getRow();
						echo json_encode($data);
					}

					public function getsd($po, $cust)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$DataKe1 = $db->query("SELECT a.*,  b.namabarang, b.stok 
                                    FROM ar_kirimdetil a
                                    INNER JOIN inv_barang b on b.kodeitem = a.kodeitem 
                                    where a.kodekirim = '$po' and b.kdcbg='$cabang' group by id")->getResult();

						$DataKe2 = $db->query("SELECT * FROM inv_barang_harga a INNER JOIN ar_kirimdetil b ON a.kodeitem = b.kodeitem 
		                        WHERE a.kode_customer='$cust' AND b.kodekirim='$po'")->getResult();

						$DataKe3 = $db->query("SELECT b.* FROM ar_kirim a inner join ar_sofile b on a.kodeso = b.kodeso where a.kodekirim='$po'")->getRow();

						$DataKe4 = $db->query("SELECT * FROM ar_kirimdetil a INNER JOIN inv_nobatch b ON a.kodeitem = b.kodeitem 
						WHERE a.kodekirim='$po' AND b.no_batch = a.no_batch AND b.tanggal_exp = a.tgl_exp AND b.cabang='$cabang' order by a.id asc")->getResult();

						$DataKe5 = $db->query("SELECT a.*, b.namabarang AS namabarang_paket, c.namabarang AS namabarang, d.qty_saldo, d.no_batch FROM ar_kirimpaket a 
						INNER JOIN inv_barang b ON a.kodeitem_paket = b.kodeitem 
						INNER JOIN inv_barang c ON a.kodeitem_barang = c.kodeitem 
						INNER JOIN inv_nobatch d ON a.nobatch_paket = d.no_batch
						WHERE a.kodekirim='$po' AND b.kdcbg='$cabang' AND c.kdcbg='$cabang' AND 
						d.kodeitem = a.kodeitem_paket AND d.cabang='$cabang' ORDER BY a.kodeitem_barang")->getResult();

						$DataGetSDPaket = $db->query("SELECT * FROM ar_kirimdetil a INNER JOIN inv_barang_paket b ON b.kodeitem_barang = a.kodeitem 
						WHERE a.kodekirim='$po' AND b.cabang='$cabang' ORDER BY a.kodeitem, b.kodeitem_paket")->getResult();
						$DataTidakDiinput = [];
						foreach ($DataGetSDPaket as $view) {
							$RowBarangPaket = $db->table('ar_kirimpaket')->getWhere(['kodekirim' => $po, 'kodeitem_barang' => $view->kodeitem_barang])->getRow();
							$RowBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $view->kodeitem_barang, 'kdcbg' => $cabang])->getRow();
							if ($RowBarangPaket == "") {
								$DataTidakDiinput[] = [
									'kodeitem_paket' => $view->kodeitem_paket,
									'namabarang' => $RowBarang->namabarang,
									'namabarang_paket' => $view->namabarang_paket
								];
							}
						}

						$DataKe7 = $db->query("SELECT * FROM ar_customer where kode='$cust'")->getRow();
						$response = [
							'DataOne' => $DataKe1,
							'DataTwo' => $DataKe2,
							'DataThird' => $DataKe3,
							'DataFourth' => $DataKe4,
							'DataFiveth' => $DataKe5,
							'DataSixth' => $DataTidakDiinput,
							'DataSeventh' => $DataKe7
						];
						echo json_encode($response);
					}

					public function getsd3($po)
					{
						$db = db_connect();
						$data = $db->query("SELECT a.*, b.keterangan
									FROM ar_kirimdetil a
									INNER JOIN ar_kirim b ON a.kodekirim = b.kodekirim
									where a.kodekirim = '$po'")->getRow();
						echo json_encode($data);
					}

					public function fakturpenjualansave()
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$taxes = $model_Global->_LoadProfile()->taxes_desimal;
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];
						$session = session();
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 423);
						// Table
						$builder            = $db->table('ar_sifile');
						$builderDetail      = $db->table('ar_sidetail');
						$buildSiPaket       = $db->table('ar_sipaket');
						$buildKirim         = $db->table('ar_kirim');
						$buildKirimDetil    = $db->table('ar_kirimdetil');
						$builderINVBarang   = $db->table('inv_barang');
						$INVhargapercust    = $db->table('inv_barang_harga');
						$INVtransaksi       = $db->table('inv_transaksi');
						$buildCs            = $db->table("ar_customer");
					
						try {
							$db->transStart(); // Mulai transaksi
							$DataArCs = $buildCs->getWHere(['kode' => $input['cust']])->getRow();
							$saldokredit = (int)$DataArCs->saldokredit;
							$totalSifile = (int)$input['_H_vtotal'];
							$Pembayaran  = $input['pembayaran'];
							if($totalSifile > $saldokredit && $Pembayaran == 'K'){
								echo json_encode(['status' => 3, 'message' => 'Saldo Kredit tidak mencukupi']);
							}else{
								if ($akses->uadd == 1 || $akses->uedit == 1) {
									if($param != 0){
										$faktur_pajak = $input['no-pajak'];
										$datalogs = [
											'date' => date('Y-m-d H:i:s'),
											'userid' => $session->get('username'),
										];
										$builder->where(['kodesi' => $input['nomorbukti']])->update(['faktur_pajak'  => $faktur_pajak]);
										$datalogs['detail'] = 'Update data Penjualan Faktur dengan kode ' . $input['nomorbukti'];
										$db->table("logs")->insert($datalogs);

										echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
									}else{
										$cabang   	= $session->get('cabang');
										$tanggal  	= $input['tanggal'];
										$cs 		= $input['cust'];
										$_vvsubtotal	= str_replace(",", "", $input['vsubtotal']);
										$_vvdiskon		= str_replace(",", "", $input['vdiskon']);
										$_vvdpp			= str_replace(",", "", $input['vdpp']);
										$_vvppn			= str_replace(",", "", $input['vppn']);
										$_vvbiayadll 	= str_replace(",", "", $input['vbiayalain']);
										$_vvtotal		= str_replace(",", "", $input['vtotal']);

										$datacustomer = $buildCs->getWhere(['kode' => $input['cust'], 'kode_cabang' => $cabang])->getRow();
										$cabang = $datacustomer->kode_cabang;

										$DataKirim = $buildKirim->getWhere(['kodekirim' => $input['kodesd'], 'kodecbg' => $cabang])->getRow();
										if ($DataKirim) {
											$nomorso = $DataKirim->kodeso;
											$kodegudang = $DataKirim->gudang;
										} else {
											$nomorso = '';
										}

										$DataSifile = $builder->getWhere(['kodesi' => $input['nomorbukti']])->getRow();
										if (empty($DataSifile)) {
											$input['sppn'] == 'Y' ? $nobukti = $input['nomorbukti'] : $nobukti = $input['nomorbukti'];
											$faktur_pajak = $input['no-pajak'];
										} else {
											if ($input['sppn'] == 'Y') {
												$nobukti = $model_Global->_Autonomor('SIP');
												$faktur_pajak = $model_Global->_AutonomorFakturPenjualan('FP');
											} else {
												$nobukti = $model_Global->_Autonomor('SI');
												$faktur_pajak = $input['no-pajak'];
											}
										}

										if ($param != 0) {
											$builderDetail->delete(['kodesi' => $input['nomorbukti']]);
										}

										$idkode  	= $input['idkode'];
										$kode  		= $input['kode'];
										$qty   		= $input['qty'];
										$sat   		= $input['sat'];
										$no_batch   = $input['no_batch'];
										$tgl_exp   	= $input['tgl_exp'];
										$harga 		= $input['harga'];
										$disc  		= $input['disc'];

										$jumdata  = count($kode);

										$tot = 0;
										$tdisc = 0;
										$subtotal = 0;
										$tothpp = 0;
										for ($i = 0; $i < $jumdata; $i++) {
											$_kode   = $kode[$i];
											$_harga  = str_replace(",", "", $harga[$i]);

											$vjum  = $qty[$i] * $_harga;
											$vdisc = $vjum * ($disc[$i] / 100);
											$subtotal = $subtotal + $vjum;
											$tot   = $tot + $vjum;
											$tdisc = $tdisc + $vdisc;

											$DataBarang = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->getRow();
											$tothpp = $tothpp + ($DataBarang->hargabeli * $qty[$i]);

											$DataInvBarangHarga = $INVhargapercust->getWhere(['kodeitem' => $kode[$i], 'cabang' => $cabang, 'kode_customer' =>  $input['cust']])->getRow();
											if ($DataInvBarangHarga) {
												$fee1_rp = $DataInvBarangHarga->fee1_rp;
												$fee2_rp = $DataInvBarangHarga->fee2_rp;
												$fee3_rp = $DataInvBarangHarga->fee3_rp;
												$brutopercust = $DataInvBarangHarga->bruto_cust;
												$discpercust = $DataInvBarangHarga->diskon_pr;
											} else {
												$fee1_rp = 0;
												$fee2_rp = 0;
												$fee3_rp = 0;
												$brutopercust = $_harga;
												$discpercust = $disc[$i];
											}

											$datad = array(
												'kodeitem' 			=> $kode[$i],
												'kodesd'   			=> $input['kodesd'],
												'ar_kirimdetil_id' 	=> $idkode[$i],
												'kodeso'   			=> $nomorso,
												'qtysi' 			=> $qty[$i],
												'satuan' 			=> $sat[$i],
												'no_batch'  		=> $no_batch[$i],
												'tanggal_exp'  		=> $tgl_exp[$i],
												'hpp' 				=> $DataBarang->hargabeli,
												'hargajual' 		=> $brutopercust,
												'hargapercust' 		=> $brutopercust,
												'fee1_rp' 			=> $fee1_rp,
												'fee2_rp' 			=> $fee2_rp,
												'fee3_rp' 			=> $fee3_rp,
												'disc' 				=> $discpercust,
												'gudang' 			=> $kodegudang,
											);

											$data_inv = array(
												'tanggal'   => date('Y-m-d'),
												'kodeitem' => $kode[$i],
												'penerimaan' => 0,
												'pengeluaran' => $qty[$i],
												'satuan' => $sat[$i],
												'jenis' => 'Penjualan',
												'nobukti' => $nobukti,
												'tglrekam'   => date('Y-m-d H:i:s'),
												'hpp'   => $DataBarang->hargabeli,
												'hargajual'   => 0,
												'gudang' => $kodegudang,
												'no_batch' => $no_batch[$i],
												'tanggal_exp' => $tgl_exp[$i],
												'cabang' => $cabang,
											);

											if ($_kode != "") {
												if ($param == 0) {
													$datad['kodesi'] = $nobukti;
													$builderDetail->insert($datad);
													$insertId = $builderDetail->db()->insertID();

													$data_inv['nobukti_id'] = $insertId;
													$INVtransaksi->insert($data_inv);
												}
												$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo - '$qty[$i]', qty_jual = qty_jual + '$qty[$i]' 
															where kodeitem = '$kode[$i]' and no_batch='$no_batch[$i]' and gudang = '$kodegudang' and cabang = '$cabang'");

												$DataKirimDetil = $buildKirimDetil->getWHere(['id' => $idkode[$i]])->getRow();
												if ($nomorso != "") {
													$db->query("UPDATE ar_sodetail set qtyfaktur = qtyfaktur + '$qty[$i]' where kodeitem = '$kode[$i]' and id = '$DataKirimDetil->sodetail_id' and kodeso = '$nomorso'");
													$db->query("UPDATE ar_kirimdetil set qtyterima = qtyterima + '$qty[$i]' where kodeitem = '$kode[$i]' and id = '$idkode[$i]'");
												}

												$db->table("inv_barang_gudang")
													->where(['kodeitem' => $kode[$i], 'cabang' => $cabang, 'gudang' => $kodegudang])
													->update(['qty' => $DataBarang->stok - $qty[$i]]);

												$DateNow = date('Y-m-d');
												$oneHundredTwentyDaysAgoPusat = date('Y-m-d', strtotime('-120 days'));
												$oneHundredsixtyDaysAgoPusat = date('Y-m-d', strtotime('-160 days'));
												$sixtyDaysAgoCabang = date('Y-m-d', strtotime('-30 days'));
												$ninetyDaysAgoCabang = date('Y-m-d', strtotime('-60 days'));
												$GetQtyKeluarTwentyDaysAgo = $db->table('inv_transaksi');
																			$GetQtyKeluarTwentyDaysAgo->select('SUM(pengeluaran) as total');
																			$GetQtyKeluarTwentyDaysAgo->where('kodeitem', $kode[$i]);
																			$GetQtyKeluarTwentyDaysAgo->where('cabang', $cabang);
																			if($cabang == '01'){
																				$GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $oneHundredTwentyDaysAgoPusat);
																			}else{
																				$GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $sixtyDaysAgoCabang);
																			}
																			$GetQtyKeluarTwentyDaysAgo->where('tanggal <=', $DateNow);
												$ResultTwentysDayAgo = $GetQtyKeluarTwentyDaysAgo->get()->getRow();

												$GetQtyKeluarSixtyDaysAgo = $db->table('inv_transaksi');
																			$GetQtyKeluarSixtyDaysAgo->select('SUM(pengeluaran) as total');
																			$GetQtyKeluarSixtyDaysAgo->where('kodeitem', $kode[$i]);
																			$GetQtyKeluarSixtyDaysAgo->where('cabang', $cabang);
																			if($cabang == '01'){
																				$GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $oneHundredsixtyDaysAgoPusat);
																			}else{
																				$GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $ninetyDaysAgoCabang);
																			}
																			$GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $oneHundredsixtyDaysAgoPusat);
																			$GetQtyKeluarSixtyDaysAgo->where('tanggal <=', $DateNow);
												$ResultSixtyDayAgo = $GetQtyKeluarSixtyDaysAgo->get()->getRow();

												$buildPaket 		= $db->table('inv_barang_paket');
												$DataPaket 			= $buildPaket->getWhere(['kodeitem_barang' => $kode[$i], 'cabang' => $cabang])->getRow();
												$DataPaketDetail    = $buildPaket->getWhere(['kodeitem_paket' => $kode[$i], 'cabang' => $cabang])->getRow();
												if(empty($DataPaket)){
													if(empty($DataPaketDetail)){
														$db->table("inv_barang")
															->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $kodegudang])
															->update([
																	'stok_min' => $ResultTwentysDayAgo->total,
																	'stok_max' => $ResultSixtyDayAgo->total,
																	]);
													}
												}

											}
										}

										//table 2
										$buildBiaya     = $db->table("ar_sibiaya");
										$buildSofile     = $db->table("ar_sofile");

										if ($input['sppn'] == "Y") {
											$tppn = ($tot - $tdisc) * $taxes;
										} else {
											$tppn = 0;
										}
										//rincian biaya lain-lain
										$kode  = $input['lkode'];
										$jumlah = $input['ljumlah'];
										$ket   = $input['lket'];
										$jumdata  = count($kode);
										if ($param != 0) {
											$buildBiaya->delete(['kodesi' => $input['nomorbukti']]);
											$model_Global->_hapusjurnal($input['nomorbukti'], 'JJ');
										}

										$totbiaya = 0;
										$itembiaya = 2;
										for ($i = 0; $i < $jumdata; $i++) {
											$totbiaya = $totbiaya + $jumlah[$i];
											$datad = array(
												'kodeakun' => $kode[$i],
												'jumlah' => $jumlah[$i],
												'keterangan' => $ket[$i],
												'cabang' 	=> $cabang,
											);
											if ($kode[$i] != "") {
												if ($param == 0) {
													$datad['kodesi'] = $nobukti;
													$buildBiaya->insert($datad);
													$model_Global->_rekamjurnal(
														date('Y-m-d', strtotime($input['tanggal'])),
														$nobukti,
														'JJ',
														$input['kodesi'],
														2,
														$kode[$i],
														'Penjualan ke ' . $datacustomer->nama,
														$ket[$i],
														$jumlah[$i],
														0,
														$cabang
													);
													$itembiaya++;
												} else {
													$datad['kodesi'] = $input['nomorbukti'];
													$buildBiaya->insert($datad);

													$model_Global->_rekamjurnal(
														date('Y-m-d', strtotime($input['tanggal'])),
														$input['nomorbukti'],
														'JJ',
														$input['kodesi'],
														2,
														$kode[$i],
														'Penjualan ke ' . $datacustomer->nama,
														$ket[$i],
														$jumlah[$i],
														0,
														$cabang
													);
													$itembiaya++;
												}
											}
										}


										if ($param == 0) {
											if ($input['pembayaran'] == 'T') {
												$tgljthtempo = date('Y-m-d', strtotime($input['tanggal']));
											} else {
												$top  = $datacustomer->top;
												$tgljthtempo = date('Y-m-d', strtotime($input['tanggal'] . ' + ' . $top . ' days'));
											}
										} else {
											if ($input['pembayaran'] == 'T') {
												$tgljthtempo = date('Y-m-d', strtotime($input['tanggal']));
											} else {
												$tgljthtempo = date('Y-m-d', strtotime($input['tanggaljt']));
											}
										}

										if ($_vvtotal == '0' || $_vvtotal == '0.00') {
											$status = 2;
										} else {
											$status = 1;
										}

										$data_sales = $db->table("ar_sales")->where(['id' => $input['sales']])->get()->getRow();
										if ($data_sales) {
											$nama_sales = $data_sales->nama_sales;
											$id_sales = $data_sales->id;
										} else {
											$nama_sales = '';
											$id_sales = '';
										}

										$kodebarangpaket  		= $input['kodebarangpaket'];
										if ($kodebarangpaket != null || $kodebarangpaket != '') {
											$jumdata3  = count($kodebarangpaket);
											for ($i = 0; $i < $jumdata3; $i++) {
												$qtypaket = $input['qtypaket'][$i];
												$kodeitem_paket = $input['kodebarangdetail'][$i];
												$nobatchPaket = $input['no_batchpaket'][$i];
												$DataInvBarangPaket = $builderINVBarang->getWhere(['kodeitem' => $kodeitem_paket, 'kdcbg' => $cabang])->getRow();

												$datapaket = array(
													'cabang'  => $cabang,
													'kodesd'  => $input['kodesd'],
													'kodeso'  => $nomorso,
													'kodeitem_paket' => $kodeitem_paket,
													'nobatch_paket' => $nobatchPaket,
													'tglexp_paket' => $input['edpaket'][$i],
													'qtyterima_paket' => $qtypaket,
													'satuan' => $input['satpaket'][$i],
													'kodeitem_barang' => $kodebarangpaket[$i],
													'id_kodeitembarang' => $input['idkodeitembarang'][$i],
												);

												$data_invTransaksi = array(
													'tanggal'   => date('Y-m-d'),
													'kodeitem' => $kodeitem_paket,
													'penerimaan' => 0,
													'pengeluaran' => $qtypaket,
													'satuan' => $input['satpaket'][$i],
													'jenis' => 'Penjualan',
													'nobukti' => $nobukti,
													'tglrekam'   => date('Y-m-d H:i:s'),
													'hpp'   => $DataInvBarangPaket->hargabeli,
													'hargajual'   => 0,
													'gudang' => $kodegudang,
													'no_batch' => $nobatchPaket,
													'tanggal_exp' => $input['edpaket'][$i],
													'cabang' => $cabang,
												);

												if ($kodebarangpaket[$i] != "") {
													if ($param == 0) {
														$datapaket['kodesi'] = $nobukti;
														$buildSiPaket->insert($datapaket);
														$INVtransaksi->insert($data_invTransaksi);
														$kodekirim = $input['kodesd'];
														$UpdateStockPaketKirim = $db->query("UPDATE ar_kirimpaket set qtyterima_paket = qtyterima_paket + $qtypaket 
																							where kodekirim='$kodekirim' and kodeitem_paket ='$kodeitem_paket' ");

														$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo - '$qtypaket', qty_jual = qty_jual + '$qtypaket' 
																	where kodeitem = '$kodeitem_paket' and no_batch='$nobatchPaket' and gudang = '$kodegudang' 
																	and cabang = '$cabang'");

														$db->table("inv_barang_gudang")
															->where(['kodeitem' => $kodeitem_paket, 'cabang' => $cabang, 'gudang' => $kodegudang])
															->update(['qty' => $DataInvBarangPaket->stok - $qtypaket]);

														$ResultUpdateStokBarang = [
															'stok ' => $DataInvBarangPaket->stok - $qtypaket,
															'stok_so ' => $DataInvBarangPaket->stok_so - $qtypaket,
															'tgljualakhir' => date('Y-m-d')
														];

														$builderINVBarang->where(['kodeitem' => $kodeitem_paket, 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
													} else {
														$datapaket['kodesi'] = $input['nomorbukti'];
														$buildSiPaket->insert($datapaket);
													}
												}
											}
										}


										$data = array(
											'kodecbg' => $cabang,
											'kodecust' => $input['cust'],
											'pembayaran'  => $input['pembayaran'],
											'kodeso'  => $nomorso,
											'kodesd'  => $input['kodesd'],
											'faktur_pajak'  => $faktur_pajak,
											'tglsi'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
											'tglkirim' => date('Y-m-d', strtotime($input['tanggalkirim'])),
											'kurir'     => $input['kurir'],
											'tgljthtempo' => $tgljthtempo,
											'kodeuser' => $session->get('username'),
											'ket'     => $input['keterangan'],
											'alamat'  => $input['alamat'],
											'dpp'     => $tot,
											'ppn'     => $_vvppn,
											'diskon'  => $_vvdiskon,
											'totalsi' => $_vvtotal,
											'biayalain' => $_vvbiayadll,
											'sppn'    => $input['sppn'],
											'alamat1' => $datacustomer->alamat1,
											'alamat2' => $datacustomer->alamat2,
											'namakirim' => $datacustomer->contactname,
											'id_sales'  => $id_sales,
											'nama_sales' => $nama_sales,
										);


										$profile = $model_Global->_LoadProfile();
										$akun_penjualan = $profile->akun_penjualan;
										$akun_kas       = $profile->akun_kas;
										$akun_piutang   = $profile->akun_piutang;
										$akun_ppn       = $profile->akun_ppn;
										$akun_hpp       = $profile->akun_hpp;
										$akun_persediaan = $profile->akun_persediaan;
										$akun_diskon    = $profile->akun_diskonjual;

										if ($input['pembayaran'] == 'T') {
											$akun_debet = $akun_piutang;
										} else {
											$akun_debet = $akun_piutang;
											$update = $db->query("UPDATE ar_customer set saldokredit = saldokredit - '$_vvtotal' where kode='$cs' ");
										}

										if ($param == 0) {
											if ($_vvtotal != '0' || $_vvtotal != '0.00') {
												$model_Global->_rekamjurnal(

													date('Y-m-d', strtotime($input['tanggal'])),
													$nobukti,
													'JJ',
													$input['kodesd'],
													1,
													$akun_debet,
													'Penjualan ke ' . $datacustomer->nama,
													'Penjualan ke ' . $datacustomer->nama,
													$_vvtotal,
													0,
													$cabang
												);


												if ($tdisc > 0) {
													$model_Global->_rekamjurnal(

														date('Y-m-d', strtotime($input['tanggal'])),
														$nobukti,
														'JJ',
														$input['kodesd'],
														$itembiaya++,
														$akun_diskon,
														'Penjualan ke ' . $datacustomer->nama,
														'Diskon Penjualan ke ' . $datacustomer->nama,
														$_vvdiskon,
														0,
														$cabang
													);
												}

												if ($tppn > 0) {
													$model_Global->_rekamjurnal(
														date('Y-m-d', strtotime($input['tanggal'])),
														$nobukti,
														'JJ',
														$input['kodesd'],
														$itembiaya++,
														$akun_ppn,
														'Penjualan ke ' . $datacustomer->nama,
														'PPN Penjualan ke ' . $datacustomer->nama,
														0,
														$_vvppn,
														$cabang
													);
												}

												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$nobukti,
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_penjualan,
													'Penjualan ke ' . $datacustomer->nama,
													'Penjualan ke ' . $datacustomer->nama,
													0,
													$_vvsubtotal,
													$cabang
												);


												//jurnal persediaan

												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$nobukti,
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_hpp,
													'Penjualan ke ' . $datacustomer->nama,
													'HPP Penjualan ke ' . $datacustomer->nama,
													$tothpp,
													0,
													$cabang
												);

												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$nobukti,
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_persediaan,
													'Penjualan ke ' . $datacustomer->nama,
													'HPP Penjualan ke ' . $datacustomer->nama,
													0,
													$tothpp,
													$cabang
												);
											}
										} else {
											if ($_vvtotal != '0' || $_vvtotal != '0.00') {
												$model_Global->_rekamjurnal(

													date('Y-m-d', strtotime($input['tanggal'])),
													$input['nomorbukti'],
													'JJ',
													$input['kodesd'],
													1,
													$akun_debet,
													'Penjualan ke ' . $datacustomer->nama,
													'Penjualan ke ' . $datacustomer->nama,
													$_vvtotal,
													0,
													$cabang
												);


												if ($tdisc > 0) {
													$model_Global->_rekamjurnal(

														date('Y-m-d', strtotime($input['tanggal'])),
														$input['nomorbukti'],
														'JJ',
														$input['kodesd'],
														$itembiaya++,
														$akun_diskon,
														'Penjualan ke ' . $datacustomer->nama,
														'Diskon Penjualan ke ' . $datacustomer->nama,
														$_vvdiskon,
														0,
														$cabang
													);
												}

												if ($tppn > 0) {
													$model_Global->_rekamjurnal(
														date('Y-m-d', strtotime($input['tanggal'])),
														$input['nomorbukti'],
														'JJ',
														$input['kodesd'],
														$itembiaya++,
														$akun_ppn,
														'Penjualan ke ' . $datacustomer->nama,
														'PPN Penjualan ke ' . $datacustomer->nama,
														0,
														$_vvppn,
														$cabang
													);
												}

												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$input['nomorbukti'],
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_penjualan,
													'Penjualan ke ' . $datacustomer->nama,
													'Penjualan ke ' . $datacustomer->nama,
													0,
													$_vvsubtotal,
													$cabang
												);


												//jurnal persediaan
												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$input['nomorbukti'],
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_hpp,
													'Penjualan ke ' . $datacustomer->nama,
													'HPP Penjualan ke ' . $datacustomer->nama,
													$tothpp,
													0,
													$cabang
												);

												$model_Global->_rekamjurnal(
													date('Y-m-d', strtotime($input['tanggal'])),
													$input['nomorbukti'],
													'JJ',
													$input['kodesd'],
													$itembiaya++,
													$akun_persediaan,
													'Penjualan ke ' . $datacustomer->nama,
													'HPP Penjualan ke ' . $datacustomer->nama,
													0,
													$tothpp,
													$cabang
												);
											}
										}

										$datalogs = [
											'date' => date('Y-m-d H:i:s'),
											'userid' => $session->get('username'),
										];

										if ($param == 0) {
											$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ar_sodetail where kodeso = '$nomorso'")->getRow()->total;

											if ($total <= 0) {
												$buildSofile->where(['kodeso' => $nomorso, 'kodecbg' => $cabang])->update(['statusid' => 4, 'no_get' => 3]);
											}
											$data['kodesi'] = $nobukti;
											$data['statusid'] = $status;
											$data['retur'] = 1;
											$builder->insert($data);
											$buildKirim->where(['kodekirim' => $input['kodesd'], 'kodecbg' => $cabang])->update(['statusid' => 3]);

											if ($input['sppn'] == 'Y') {
												$model_Global->_updatecounter1('SIP');
												$model_Global->_updatecounter_pajak('FP');
											} else {
												$model_Global->_updatecounter1('SI');
											}
											$datalogs['detail'] = 'Tambah data Penjualan Faktur dengan kode ' . $nobukti;
											$db->table("logs")->insert($datalogs);
											$kodeBarang  	= $input['kode'];
											$CountKode  = count($kodeBarang);
											for ($i = 0; $i < $CountKode; $i++) {
												$DataBarang = $builderINVBarang->getWhere(['kodeitem' => $kodeBarang[$i], 'kdcbg' => $cabang])->getRow();
												$ResultUpdateStokBarang = [
													'stok ' => $DataBarang->stok - $qty[$i],
													'stok_so ' => $DataBarang->stok_so - $qty[$i],
													'tgljualakhir' => $tanggal
												];

												$builderINVBarang->where(['kodeitem' => $kodeBarang[$i], 'kdcbg' => $cabang])->update($ResultUpdateStokBarang);
											}
											echo json_encode(['status' => 1, 'message' => $nobukti]);
										} else {
											$builder->where(['kodesi' => $input['nomorbukti'], 'kodecbg' => $cabang])->update($data);
											$datalogs['detail'] = 'Update data Penjualan Faktur dengan kode ' . $input['nomorbukti'];
											$db->table("logs")->insert($datalogs);

											echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
										}
									}
								} else {
									echo json_encode(['status' => 2, 'message' => 'Akses Ditolak']);
								}
								$db->transComplete(); // Selesai transaksi
								if ($db->transStatus() === false) {
									// Jika terjadi kesalahan, batalkan transaksi
									$db->transRollback();
									echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
								}
								// Transaksi sukses, commit perubahan ke database
								$db->transCommit();
							}
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function getfaktur($id)
					{
						$db = db_connect();
						$data = $db->query("SELECT * from ar_sifile where id=$id")->getRow();
						echo json_encode($data);
					}

					public function fakturpenjualanupdatekurir($id){
						$model_login =  model('Login_model', 'M_login');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$input = $this->request->getPost();
						$username = $session->get('username');
						$dataLogin = $model_login->validate_user($username, md5($input['password']));
						if ($dataLogin == true) {
							$DataSiFile = $db->query("SELECT * from ar_sifile where id=$id")->getRow();
							
							if($username  == $input['kurir']){
								if($DataSiFile->step_kurir == 0){
									$db->query("UPDATE ar_sifile set kurir='$input[kurir]', step_kurir = 1 where id=$id");
								}else if($DataSiFile->step_kurir == 1){
									$db->query("UPDATE ar_sifile set kurir='$input[kurir]', step_kurir = 2 where id=$id");
								}
								echo json_encode(['status' => 1, 'message' => 'Success Update Kurir']);
							}else{
								echo json_encode(['status' => 2, 'message' => 'Username Kurir Tidak Sesuai']);
							}
						} else {
							echo json_encode(['status' => 2, 'message' => 'Password Wrong']);
						}

					}

					public function cetak_faktur($param1)
					{
						error_reporting(0);
						$param = str_replace('"', '', $param1);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$alamat1  	= $profile->alamat1;
							$alamat2  	= $profile->alamat2;
							$hp 	  	= $profile->hp;
							$email 	  	= $profile->email;
							$taxes 		= $profile->taxes_desimal;
							$taxespersen = $profile->taxes_persen;

							$queryh = "SELECT ar_sifile.*, ar_customer.nama, ar_customer.nama_pt, ar_customer.npwp, ar_customer.telp, ar_sofile.no_po  FROM ar_sifile INNER JOIN ar_customer ON ar_sifile.kodecust = ar_customer.kode INNER JOIN ar_sofile ON ar_sifile.kodeso = ar_sofile.kodeso WHERE ar_sifile.kodesi = '$param'";
							$queryd = "SELECT ar_sidetail.*, ar_kirimdetil.tgl_exp, inv_barang.namabarang from ar_sidetail 
										inner join inv_barang on ar_sidetail.kodeitem=inv_barang.kodeitem 
										inner join ar_kirimdetil on ar_kirimdetil.kodekirim=ar_sidetail.kodesd 
										where ar_sidetail.kodesi = '$param' group by ar_sidetail.id order by inv_barang.namabarang";
							$queryb = "SELECT sum(jumlah) as total from ar_sibiaya  where ar_sibiaya.kodesi = '$param'";
							$QueryPaket = $db->query("SELECT a.*, b.namabarang FROM ar_sipaket a
														INNER JOIN inv_barang b ON a.kodeitem_barang = b.kodeitem  
														WHERE a.kodesi='$param'  AND a.cabang='$unit' AND b.kdcbg='$unit'
														GROUP BY a.id_kodeitembarang order by b.namabarang")->getResult();
							$QueryPaketNumRow = $db->query("SELECT * FROM ar_sipaket  WHERE kodesi='$param' and cabang='$unit' GROUP BY id_kodeitembarang")->getNumRows();
							$QueryPaketRow = $db->query("SELECT * FROM ar_sipaket  WHERE kodesi='$param' and cabang='$unit'")->getRow();
							date_default_timezone_set('Asia/Jakarta');

							$detil  = $db->query($queryd)->getResult();
							$header = $db->query($queryh)->getRow();
							$biaya  = $db->query($queryb)->getRow();
							if ($header->sppn == "Y" || $header->sppn == "Ya") {
								$nofp = $header->faktur_pajak;
							} else {
								$nofp = "";
							}
							$pdf = new simkeu_faktur_helper();
							$pdf->addpage("P", "A4");
							$pdf->setsize("P", "A4");
							$pdf->SetWidths(array(70, 20, 100));
							$border = array('B', '', 'B');
							$size   = array('0', '0', '0');
							$pdf->setfont('Arial', 'B', 7.5);
							$pdf->SetAligns(array('C', 'C', 'C'));
							$align = array('L', 'C', 'L');
							$style = array('B', '', 'B');
							$size  = array('9', '0', '9');
							$max   = array(5, 5, 20);
							$judul = array('Kepada :', '', 'Faktur Penjualan :');
							$fc     = array('0', '0', '0');
							$pdf->FancyRow2(5, $judul, $fc,  $border, $align, $style, $size, $max);
							$pdf->ln(1);
							$pdf->setfont('Arial', '', 7.5);
							$pdf->SetWidths(array(80, 9, 20, 3, 30, 18, 40));
							$border = array('', '', '', '', '', '', '', '');
							$fc     = array('0', '0', '0', '0', '0', '0', '0');
							$fc1     = array('0', '0', '0', '0', '0', '0', '0');
							$pdf->SetFillColor(230, 230, 230);
							$style = array('B', '', '', '', '', '', '');
							$head = array($header->nama, '', 'No Faktur', ':', $header->kodesi, 'Time', ': ' . date('H:i'));
							$pdf->FancyRow2(5, $head, $fc1,  $border, $align, $style);
							$alamat = array($header->alamat1, '', 'No SJ', ':', $header->kodesd, 'NO PO', ': ' . $header->no_po);
							$pdf->FancyRow2(3, $alamat, $fc1,  $border, $align);
							$pdf->ln(1.5);
							$nama_pt = array($header->nama_pt, '', 'Tgl. Faktur', ':', date('d M Y', strtotime($header->tglkirim)), 'Jatuh Tempo', ': ' . date('d M Y', strtotime($header->tgljthtempo)));
							$pdf->FancyRow2(5, $nama_pt, $fc1,  $border, $align);
							$pdf->ln(-1);
							$pdf->FancyRow(array('NO NPWP  : ' . $header->npwp, '', 'Faktur Pajak', ': ', $nofp, 'Marketing', ': ' . $header->nama_sales), $fc, $border);
							$pdf->ln(-5);
							$pdf->SetWidths(array(10, 16, 58, 18, 18, 11, 12, 16, 12, 19));
							$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
							$align  = array('C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'R');
							$pdf->setfont('Arial', 'B', 7.5);
							$pdf->SetAligns(array('L', 'C', 'R'));
							$fc = array('0', '0', '0', '0', '0', '0', '0', '0', '0');
							$judul = array('No', 'Kode', 'Nama Barang', 'Batch', 'Expired', 'Qty', 'Satuan', 'Harga', 'Disc %', 'Total Harga');
							$pdf->FancyRow2(5, $judul, $fc, $border, $align);
							$border = array('', '', '');
							$pdf->setfont('Arial', '', 7.5);
							$tot = 0;
							$subtot = 0;
							$tdisc  = 0;
							$no = 1;
							$border = array('', '', '', '', '', '', '', '', '', '');
							$align  = array('C', 'L', 'L', 'L', 'L', 'R', 'R', 'R', 'C', 'R');
							$fc = array('0', '0', '0', '0', '0', '0', '0');
							$pdf->SetFillColor(0, 0, 139);
							$pdf->settextcolor(0);
							foreach ($detil as $db) {
								$harga_brang = $db->hargajual;
								$dpp = ($db->qtysi * $harga_brang);
								$dis = ($db->disc / 100) * $dpp;
								$jum = $dpp - $dis;
								$tot = $tot + $jum;

								$subtot = $subtot + $dpp;
								$tdisc  = $tdisc + $dis;

								$tdpp = $subtot - $tdisc;

								if ($db->qtysi >= 1 && $jum >= 0) {

									$pdf->FancyRow(array(
										$no,
										$db->kodeitem,
										$db->namabarang,
										$db->no_batch,
										$db->tanggal_exp,
										number_format($db->qtysi),
										$db->satuan,
										number_format($db->hargajual, 2, '.', ','),
										$db->disc,
										number_format($jum, 0, '.', ',')
									), $fc, $border, $align);
									$pdf->ln(-1.5);
									$no++;
								}
							}


							if ($header->sppn == "Y") {
								$ppn = $tdpp * $taxes;
							} else {
								$ppn = 0;
							}
							$biayalain = $biaya->total;
							$tot = $tdpp + $ppn + $biayalain;
							$tot_terbilang = number_format($tot, 0, '', '');
							$pdf->SetFillColor(230);
							$pdf->ln(-4);
							$border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
							$pdf->FancyRow(array('', '', '', '', '', '', '', '', '', ''), 0, $border);
							$pdf->ln(1);
							$pdf->SetWidths(array(100, 20, 30, 40));

							$border = array('TB', '', 'T', 'T');
							$align  = array('L', '', 'L', 'R');
							$fc = array('0', '0', '0', '0');
							$pdf->FancyRow(array('Keterangan', '', 'Sub Total', number_format($subtot, 0, '.', ',')), $fc, $border, $align, 0);
							$border = array('', '', '', '');
							$pdf->ln(-2);
							$pdf->FancyRow(array('', '', 'Diskon', number_format($tdisc, 0, '.', ',')), $fc, $border, $align);
							$pdf->ln(-2);
							$pdf->FancyRow(array($header->ket, '', 'DPP', number_format($tdpp, 0, '.', ',')), $fc, $border, $align);
							$pdf->ln(-2);
							$pdf->FancyRow(array('', '', 'PPN (' . $taxespersen . '%)', number_format($ppn, 0, '.', ',')), $fc, $border, $align);
							$pdf->ln(-2);
							$pdf->FancyRow(array('', '', 'Biaya Lain-lain', number_format($biayalain, 0, '.', ',')), $fc, $border, $align);
							$pdf->ln(0);
							$style = array('B', '', 'B', 'B');
							$size  = array('7', '', '', '');
							$border = array('T', '', 'T', 'T');
							//$pdf->SetFillColor(0,0,139);
							//$pdf->settextcolor(255,255,255);
							$fc = array('0', '0', '0', '0');
							$terbilang = ucwords($model_Global->terbilang($tot_terbilang));
							$vtotal = array('TERBILANG : ' . $terbilang, '', 'Total', number_format($tot, 0, '.', ','));
							$pdf->FancyRow2(3.5, $vtotal, $fc, $border, $align, $style);
							$pdf->ln(3);

							$pdf->settextcolor(0);
							$pdf->SetWidths(array(50, 50, 48, 50));
							$pdf->SetFont('Arial', '', 8);
							$pdf->SetAligns(array('C', 'C', 'C', 'C'));
							$pdf->ln(0);

							$border = array('', '', '', '');
							$align  = array('C', 'C', 'C', 'C');
							$pdf->FancyRow(array('Hormat Kami', 'Penanggung Jawab Teknis', 'Dikirim Oleh', 'Penerima'), 0, $border, $align);
							$pdf->ln(-1);
							$pdf->SetWidths(array(47, 2, 31, 19, 47, 4, 46));
							$pdf->SetFont('Arial', '', 8);
							$border = array('', '', '', '', '', '', '');
							$align  = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
							$pdf->FancyRow(array('Tgl. Nama', '', 'Tgl. Nama', '', 'Tgl. Nama', '', 'Tgl. Nama'), 0, $border, $align);
							$pdf->ln(35);


							if ($QueryPaketRow != null || $QueryPaketRow != '') {
								$columnWidth = ($pdf->GetPageWidth() - 20) / 3; // Minus margin
								$cellHeight = 5; // Tinggi sel
								// Set posisi awal
								$xLeft = 10;
								$xCenter = $xLeft + $columnWidth + 2; // 10 adalah jarak antar kolom
								$xRight = $xCenter + $columnWidth + 2;
								$y = $pdf->GetY();


								$columnWidthC = ($pdf->GetPageWidth() - 20) / 3; // Minus margin
								$cellHeightC = 3; // Tinggi sel
								// Set posisi awal
								$xLeftC = 10;
								$xCenterC = $xLeftC + $columnWidthC + 2; // 10 adalah jarak antar kolom
								$xRightC = $xCenterC + $columnWidthC + 2;
								$yC = $pdf->GetY();
								foreach ($QueryPaket as $index => $pack) {

									$base = db_connect();
									$ItemBarang = $base->query("SELECT namabarang from inv_barang where kodeitem = '$pack->kodeitem_barang' and kdcbg='$unit'")->getRow();
									$pdf->SetFont('Arial', 'B', 7.5);
									$pdf->SetFillColor(255, 165, 0);

									if ($index % 3 == 0) {
										// Kolom kiri
										$pdf->SetXY($xLeft, $y);
										$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
									} else if ($index % 3 == 1) {
										// Kolom tengah
										$pdf->SetXY($xCenter, $y);
										$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
									} else {
										// Kolom kanan
										$pdf->SetXY($xRight, $y);
										$pdf->MultiCell($columnWidth, $cellHeight, $ItemBarang->namabarang, 1, 1, 'L');
										// Update posisi y untuk baris berikutnya
									}

									$DetailBarang   = $base->query("SELECT a.*, b.namabarang FROM ar_sipaket a
																	INNER JOIN inv_barang b ON a.kodeitem_paket = b.kodeitem
																	WHERE a.kodesi='$param' and a.cabang='$unit' and b.kdcbg='$unit' 
																	and id_kodeitembarang='$pack->id_kodeitembarang' order by b.namabarang ")->getResult();
									foreach ($DetailBarang as $id => $view) {
										$BarangName   = $base->query("SELECT namabarang from inv_barang where kodeitem = '$view->kodeitem_paket' and kdcbg='$unit'")->getRow();
										$BarangPaket   = $base->query("SELECT qty_paket FROM inv_barang_paket WHERE kodeitem_paket='$view->kodeitem_paket' AND cabang='$unit' and kodeitem_barang='$pack->kodeitem_barang'")->getRow();
										$pdf->SetFont('Arial', '', 6.5);
										$pdf->SetFillColor(230, 230, 230);
										$namaBarangDetail = $pdf->limit_text($BarangName->namabarang, 5);

										if ($index % 3 == 0) {
											// Kolom kiri

											if ($id == 0) {
												$pdf->SetXY($xLeftC, $yC + 5);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 1) {
												$pdf->SetXY($xLeftC, $yC + 11);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 2) {
												$pdf->SetXY($xLeftC, $yC + 17);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 3) {
												$pdf->SetXY($xLeftC, $yC + 23);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 4) {
												$pdf->SetXY($xLeftC, $yC + 29);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 5) {
												$pdf->SetXY($xLeftC, $yC + 35);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 6) {
												$pdf->SetXY($xLeftC, $yC + 41);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 7) {
												$pdf->SetXY($xLeftC, $yC + 75);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											}
										} else if ($index % 3 == 1) {
											// Kolom tengah
											if ($id == 0) {
												$pdf->SetXY($xCenterC, $yC + 5);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 1) {
												$pdf->SetXY($xCenterC, $yC + 11);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 2) {
												$pdf->SetXY($xCenterC, $yC + 17);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 3) {
												$pdf->SetXY($xCenterC, $yC + 23);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 4) {
												$pdf->SetXY($xCenterC, $yC + 29);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 5) {
												$pdf->SetXY($xCenterC, $yC + 35);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 6) {
												$pdf->SetXY($xCenterC, $yC + 41);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 7) {
												$pdf->SetXY($xCenterC, $yC + 75);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											}
										} else {
											// Kolom kanan
											if ($id == 0) {
												$pdf->SetXY($xRightC, $yC + 5);
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 1) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 1);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC + 2);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 4);
												}

												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 2) {
												if ($QueryPaketNumRow ==  5) {
													$pdf->SetXY($xRightC, $yC - 7);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 1);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 13);
												}

												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 3) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 13);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 4);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 22);
												}

												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 4) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 29);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 7);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 33);
												}
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 5) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 39);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 10);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 43);
												}
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 6) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 39);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 13);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC + 5);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 53);
												}
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											} else if ($id == 7) {
												if ($QueryPaketNumRow == 5) {
													$pdf->SetXY($xRightC, $yC - 39);
												} else if ($QueryPaketNumRow == 4) {
													$pdf->SetXY($xRightC, $yC - 20);
												} else if ($QueryPaketNumRow < 4) {
													$pdf->SetXY($xRightC, $yC - 10);
												} else if ($QueryPaketNumRow > 5) {
													$pdf->SetXY($xRightC, $yC - 63);
												}
												$pdf->MultiCell($columnWidthC, $cellHeightC, $namaBarangDetail
													. "\n" . $BarangPaket->qty_paket . ' ' . $view->satuan . "  LOT : " . $view->nobatch_paket . '  ED : ' . $view->tglexp_paket, 1, 1, 'L');
											}

											// Update posisi y untuk baris berikutnya
											$y += 3 * ($QueryPaketNumRow - 1);
											$yC += 3 * ($QueryPaketNumRow - 1);
										}
									}
								}
								$pdf->Ln(0);
							}

							$pdf->AliasNbPages();
							$pdf->output($param . '.PDF', 'I');
							exit;
						} else {
							header('location:' . base_url());
						}
					}

					public function cetak_suratjalan($param)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$cabang = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$alamat1  = $profile->alamat1;
							$alamat2  = $profile->alamat2;
							$hp 	  = $profile->hp;
							$email 	  = $profile->email;
							$queryh = "SELECT ar_sifile.*, ar_customer.nama, ar_customer.nama_pt, ar_customer.npwp, ar_customer.telp, ar_sofile.no_po  FROM ar_sifile INNER JOIN ar_customer ON ar_sifile.kodecust = ar_customer.kode INNER JOIN ar_sofile ON ar_sifile.kodeso = ar_sofile.kodeso WHERE ar_sifile.kodesi = '$param'";
							$queryd = "SELECT ar_sidetail.*, ar_kirimdetil.tgl_exp, inv_barang.namabarang from ar_sidetail inner join inv_barang on ar_sidetail.kodeitem=inv_barang.kodeitem inner join ar_kirimdetil on ar_kirimdetil.kodekirim=ar_sidetail.kodesd where ar_sidetail.kodesi = '$param' group by ar_sidetail.id";
							$queryb = "SELECT sum(jumlah) as total from ar_sibiaya  where ar_sibiaya.kodesi = '$param'";

							$detil  = $db->query($queryd)->getResult();
							$header = $db->query($queryh)->getRow();
							$biaya  = $db->query($queryb)->getRow();
							$pdf = new simkeu_nota_helper();
							$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
							$pdf->setjudul('');
							$pdf->setsubjudul('');
							$pdf->addpage("P", "A4");
							$pdf->setsize("P", "A4");
							$pdf->SetWidths(array(70, 30, 90));
							$border = array('BT', '', 'BT');
							$size   = array('0', '0', '0');
							$pdf->setfont('Arial', 'B', 18);
							$pdf->SetAligns(array('C', 'C', 'C'));
							$align = array('L', 'C', 'L');
							$style = array('', '', '');
							$size  = array('15', '0', '15');
							$max   = array(5, 5, 20);
							$judul = array('Kepada :', '', 'Surat Jalan');
							$fc     = array('0', '0', '0');
							$hc     = array('20', '20', '20');
							$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
							$pdf->ln(1);
							$pdf->setfont('Arial', 'B', 10);
							$pdf->SetWidths(array(70, 30, 30, 5, 55));
							$border = array('', '', '', '', '');
							$fc     = array('0', '0', '0', '0', '0');
							$pdf->SetFillColor(230, 230, 230);
							$pdf->setfont('Arial', '', 9);
							$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodesi), $fc, $border);
							$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglsi))), $fc, $border);
							$pdf->FancyRow(array($header->alamat2, '', 'Nomor PO', ':', $header->no_po), $fc, $border);
							$pdf->FancyRow(array($header->npwp, '', 'Marketing', ':', $header->nama_sales), $fc, $border);
							$pdf->ln(2);

							$pdf->SetWidths(array(30, 70, 25, 30, 15, 20));
							$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
							$align  = array('L', 'L', 'R', 'C', 'R', 'R');
							$pdf->setfont('Arial', 'B', 10);
							$pdf->SetAligns(array('L', 'C', 'R'));
							$fc = array('0', '0', '0', '0', '0', '0');
							$judul = array('Kode Barang', 'Nama Barang', 'Batch', 'Tanggal Exp', 'Qty', 'Satuan');
							$pdf->FancyRow2(8, $judul, $fc, $border, $align);
							$border = array('', '', '');
							$pdf->setfont('Arial', '', 10);
							$qty = 0;
							$tot = 0;
							$border = array('', '', '', '', '', '');
							$align  = array('L', 'L', 'R', 'C', 'R', 'R');
							$fc = array('0', '0', '0', '0', '0', '0');
							$pdf->SetFillColor(0, 0, 139);
							$pdf->settextcolor(0);

							foreach ($detil as $db) {
								$qty = $qty + $db->qtysi;
								$pdf->FancyRow(array(
									$db->kodeitem,
									$db->namabarang,
									$db->no_batch,
									$db->tanggal_exp,
									number_format($db->qtysi, 0, '.', ','),
									$db->satuan,
								), $fc, $border, $align);

								$tot++;
							}

							$pdf->SetFillColor(230);
							$border = array('B', 'B', 'B', 'B', 'B', 'B');
							$pdf->FancyRow(array('', '', '', '', '', ''), 0, $border);
							$pdf->ln(2);
							$pdf->SetWidths(array(100, 20, 30, 40));
							$border = array('TB', '', '', '');
							$align  = array('L', '', 'L', 'R');
							$fc = array('0', '0', '0', '0');
							$pdf->FancyRow(array('Keterangan', '', 'Total Kuantitas', number_format($qty, 0, '.', ',')), $fc, $border, $align, 0);
							$border = array('', '', '', '');
							$border = array('', '', 'B', 'B');
							$pdf->FancyRow(array('', '', 'Jumlah Barang', number_format($tot, 0, '.', ',')), $fc, $border, $align, 0);
							$border = array('');
							$align  = array('L');
							$pdf->FancyRow(array($header->ket), $fc, $border, $align);

							$style = array('', '', 'B', 'B');
							$size  = array('0', '0', '0', '0');
							$border = array('T', '', '', '');

							$fc = array('0', '0', '0', '0');
							$pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, $style, $size);
							$pdf->settextcolor(0);
							$pdf->ln(5);
							$pdf->SetWidths(array(45, 2, 45, 11, 45, 2, 45));
							$border = array('', '', '', '',);
							$align  = array('C', 'C', 'C', 'C');
							$pdf->FancyRow(array('Hormat Kami', '', 'Disiapkan oleh', '', 'Dikirim oleh', '', 'Penerima'), 0, $border, $align);
							$pdf->ln(1);
							$pdf->ln(15);
							$pdf->SetWidths(array(45, 2, 45, 2, 45, 2, 45));
							$pdf->SetFont('Arial', '', 8);
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
							$border = array('B', '', 'B', '', 'B', '', 'B');
							$pdf->FancyRow(array('', '', '', '', '', '', ''), 0, $border, $align);
							$border = array('', '', '', '', '', '', '');
							$align  = array('L', 'C', 'L', 'C', 'L', 'C', 'L');
							$pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.', '', 'Tgl.'), 0, $border, $align);

							$pdf->AliasNbPages();
							$pdf->output($param . '.PDF', 'I');
							exit;
						} else {

							header('location:' . base_url());
						}
					}

					// =========================== UANGMUKA PESANAN PENJUALAN SELECTION START ======================//
					//Uang Muka Penjualan Page Start Here
					public function uang_muka_penjualan()
					{
						$session = session();
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 424);
						if ($akses->uadd == 1) {
							$tambah = 'Daftar Uang Muka';
						} else {
							$tambah = '';
						}
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Uang Muka Penjualan',
							'body' => 'admin/penjualan/transaksi/uang-muka-penjualan',
							'tambah' => $tambah
						];
						return view('layout/layout', $data);
					}

					public function uang_muka_penjualan_entri()
					{
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Uang Muka Penjualan',
							'body' => 'admin/penjualan/transaksi/uang-muka-penjualan-entri',
						];
						return view('layout/layout', $data);
					}

					// =========================== KONTRABON PESANAN PENJUALAN SELECTION START ======================//
					//KONTRABON Page Start Here
					public function kontrabon()
					{
						$session = session();
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 427);
						if ($akses->uadd == 1) {
							$tambah = 'Data kontrabon';
						} else {
							$tambah = '';
						}
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Kontra Bon',
							'body' => 'admin/penjualan/transaksi/kontrabon',
							'tambah' => $tambah
						];
						return view('layout/layout', $data);
					}

					public function kontrabontable($tgl1, $tgl2)
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$db = db_connect();
						$query = "SELECT * FROM ar_kontrabon ";
						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " where tanggal between '$tgl1' and '$tgl2'";
						} else {
							$query .= " where year(tanggal)= (select periode_tahun from ms_identity) and
            						month(tanggal)= (select periode_bulan from ms_identity)";
						}
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						$count = 0;
						foreach ($res as $value) {
							$DataCS = $db->table('ar_customer')->where(['kode' => $value->kodecust])->get()->getRow();
							$akses = $model_Global->cek_menu_akses($level, 427);
							$count++;
							$data[] = [
								$count,
								$value->kodebon,
								date('d-m-Y', strtotime($value->tanggal)),
								$DataCS->nama,
								number_format($value->nominal),
								$akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/kontrabon/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>' : '',
								$akses->udel == 1 ? '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : '',
							];
						}
						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function kontrabonentri($id = '')
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$data = [];
						if ($id > 0) {
							$data       = $db->table('ar_kontrabon')->where('id', $id)->get()->getRow();
							$DetailItem = $db->table('ar_kontrabondetail')->where(['kodebon' => $data->kodebon])->get()->getResult();
						}
						$res = $db->query("SELECT ar_sifile.*, ar_customer.* FROM ar_customer INNER JOIN ar_sifile ON ar_sifile.kodecust = ar_customer.kode WHERE ar_sifile.kodecust = ar_customer.kode AND ar_sifile.statusid = '1' GROUP by ar_sifile.kodecust")->getResult();
						$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
						$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
						$css = [];
						$js = [];
						$data  = [
							'data' => $data ?? [],
							'DetailItem' => $DetailItem ?? [],
							'DetailByrLain' => $DetailByrLain ?? [],
							'cabang' => $cabang,
							'nomor' => $model_Global->_Autonomor('KTB'),
							'gudang' => $gudang,
							'arCust' => $arCust,
							'res' => $res,
							'css' => $css,
							'js' => $js,
							'title' => 'Kontra Bon',
							'body' => 'admin/penjualan/transaksi/kontrabonentri',
						];
						return view('layout/layout', $data);
					}

					public function kontrabonGetFaktur($data)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');

						$data = $db->query("SELECT 
											ar_sifile.*, totalsi as total, (totalsi-jumlahbayar-uangmuka) as hutang 
											from ar_sifile 
											where kodecust='$data' and statuskontrabon!=1 and totalsi>jumlahbayar+uangmuka")->getResult();
						$vdata = array();
						foreach ($data as $row) {
							$duangmuka = $db->query("SELECT * from ar_uangmuka where kodeso = '$row->kodeso' and jumlahum>jumlahbayar")->getRow();
							if ($duangmuka) {
								$uangmuka = $duangmuka->jumlahum;
							} else {
								$uangmuka = 0;
							}
							$DataSofile = $db->query("SELECT * from ar_sofile where kodeso = '$row->kodeso'")->getRow();

							$vdata[] = array(
								'kodesi' => $row->kodesi,
								'tglsi' => date('d-m-Y', strtotime($row->tglsi)),
								'totalsi' => $row->totalsi,
								'uangmuka' => $uangmuka,
								'hutang' => $row->hutang,
								'total' => $row->total,
								'nopo' => $DataSofile ? $DataSofile->no_po : '-',
							);
						}
						$a = $vdata;
						echo json_encode($vdata);
					}

					public function kontrabonsave()
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];
						$session = session();
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 427);
						// Table
						$builder            = $db->table('ar_kontrabon');
						$builderDetail      = $db->table('ar_kontrabondetail');
						$buildSifile        = $db->table('ar_sifile');
						$buildSofile        = $db->table('ar_sofile');
						$buildCustomer      = $db->table('ar_customer');
						try {
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$db->transStart();
								$cs       = $input['cust'];
								$cabang = $session->get('cabang');
								$data_customer 	= $buildCustomer->getWhere(['kode' => $cs])->getRow();
								$DataKontraBon = $builder->getWhere(['kodebon' => $input['nomorbukti']])->getRow();
								if ($param == 0) {
									empty($DataKontraBon) ? $nobukti = $input['nomorbukti'] : $nobukti = $model_Global->_Autonomor('KTB');
								}

								if ($param != 0) {

									//update faktur	
									$kontrabondetail = $builderDetail->getWhere(['kodebon' => $input['nomorbukti'], 'cabang' => $cabang])->getResult();
									foreach ($kontrabondetail as $row) {
										$faktur = $row->nomorfaktur;
										$db->query("UPDATE ar_sifile set kodebon='', statuskontrabon=0 where kodesi='$faktur' and kodecbg='$cabang'");
									}
									$builderDetail->delete(['kodebon' => $input['nomorbukti'], 'cabang' => $cabang]);
								}

								$check  = $input['checkbox'];
								$jumdata  = count($check);

								$nourut = 1;

								for ($i = 0; $i < $jumdata; $i++) {
									$_check   	= $check[$i];
									$dataSifile = $buildSifile->getWhere(['kodesi' => $_check])->getRow();
									$dataSofile = $buildSofile->getWhere(['kodeso' => $dataSifile->kodeso])->getRow();

									if ($dataSofile->no_po != "") {
										$datatest = $dataSofile->no_po;
									}else{
										$datatest = '';
									}

									$hutang = ($dataSifile->totalsi - $dataSifile->jumlahbayar - $dataSifile->uangmuka);

									$datad = array(
										'nomorfaktur' => $dataSifile->kodesi,
										'tglfaktur' => date('Y-m-d', strtotime($dataSifile->tglsi)),
										'totalfaktur' => $dataSifile->totalsi,
										'terhutang' => $hutang,
										'nopo' => $datatest,
										'cabang' => $cabang
									);
									if ($_check != "") {
										if ($param == 0) {
											$datad['kodebon'] = $nobukti;
											$builderDetail->insert($datad);
											$db->query("UPDATE ar_sifile set kodebon='$nobukti', statuskontrabon=1 where kodesi = '$_check'");
										} else {
											$Kdbon = $input['nomorbukti'];
											$datad['kodebon'] = $Kdbon;
											$builderDetail->insert($datad);
											$db->query("UPDATE ar_sifile set kodebon='$Kdbon', statuskontrabon=1 where kodesi = '$_check'");
										}

										
									}
								}

								$_jumlahbayar = str_replace(',', '', $input['jumlahbayar']);

								$data = array(
									'kodecbg' => $cabang,
									'kodecust' => $input['cust'],
									// 'kodebank'  => $input['kasbank'],
									// 'norek'  	=> $input['norek'],
									// 'namarek'  => $input['namarek'],
									'tanggal'   => date('Y-m-d', strtotime($input['tanggal'])),
									'nominal'  => $_jumlahbayar,
									'tglentri' => date('Y-m-d'),
									'kodeuser' => $session->get('username'),
								);

								$datalogs = [
									'date' => date('Y-m-d H:i:s'),
									'userid' => $session->get('username'),
								];

								if ($param == 0) {
									$data['kodebon'] = $nobukti;
									$builder->insert($data);
									$model_Global->_updatecounter1('KTB');

									$datalogs['detail'] = 'Tambah data Kontra Bon Penjualan dengan kode ' . $nobukti;
									$db->table("logs")->insert($datalogs);

									echo json_encode(['status' => 1, 'message' => $nobukti]);
								} else {
									$builder->where(['kodebon' => $input['nomorbukti'], 'kodecbg' => $cabang])->update($data);

									$datalogs['detail'] = 'Edit data Kontra Bon Penjualan dengan kode ' . $input['nomorbukti'];
									$db->table("logs")->insert($datalogs);

									echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
								}
								$db->transComplete();
								if ($db->transStatus() === false) {
									// Jika terjadi kesalahan, batalkan transaksi
									$db->transRollback();
									echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
								}
								// Transaksi sukses, commit perubahan ke database
								$db->transCommit();
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function kontrabonDelete($id)
					{
						$db = db_connect();
						$session = session();
						$db->transStart();
						$cabang = $session->get('cabang');
						$builder = $db->table('ar_kontrabon');
						$result = $builder->getWhere(['id' => $id])->getRow();
						$datalogs = [
							'detail' => 'Hapus data Kontra bon dengan kode ' . $result->kodebon,
							'date' => date('Y-m-d H:i:s'),
							'userid' => $session->get('username'),
						];
						$db->table("logs")->insert($datalogs);

						$builderD = $db->table('ar_kontrabondetail');
						$resultD = $builderD->getWhere(['kodebon' => $result->kodebon])->getResult();
						foreach($resultD as $Row){
							$db->query("UPDATE ar_sifile set statuskontrabon=0 where kodesi='$Row->nomorfaktur' and kodecbg='$cabang'");
						}
						$delete = $builder->delete(['kodebon' => $result->kodebon]);
						$delete = $builderD->delete(['kodebon' => $result->kodebon]);

						if ($delete) {
							echo json_encode(['message' => 'Deleted']);
						}
						$db->transComplete();
					}

					public function cetakkontrabon($param)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {

							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$alamat 	= $profile->alamat1;
							$fax 		= $profile->fax;

							$queryh = "select * from ar_kontrabon 
										inner join ar_customer on ar_customer.kode = ar_kontrabon.kodecust  
										where kodebon='$param'";
							$queryd = "select * from ar_kontrabondetail where kodebon='$param'";
							$header = $db->query($queryh)->getRow();
							$detail = $db->query($queryd)->getResult();
							$pdf = new simkeu_nota_helper();
							$pdf->addpage("L", "A4");
							$pdf->setsize("L", "A4");
							$pdf->ln(-15);
							//PAGE 1
							$pdf->SetXY(10, 0); // Posisi untuk kolom kiri
							$pdf->setfont('Arial', 'B', 9);
							$align = array('C');
							$style = array('');
							$size  = array('10');
							$pdf->SetFillColor(144, 238, 144);
							$judul = array('KONTRA BON');
							$fc     = array('1');
							$pdf->SetFillColor(144, 238, 144);
							$pdf->ln(5);
							
							// Mengatur posisi untuk kolom kiri dan kanan
							$pdf->SetXY(10, 10); // Posisi untuk kolom kiri
							$pdf->SetWidths(array(130));
							
							$pdf->FancyRow2(9, $judul, $fc,  $border, $align, $style, $size); // Kiri
							
							$pdf->ln(5);
							$buktifk = array('BUKTI TANDA TERIMA FAKTUR ASLI');
							$fcbukti     = array('');
							$pdf->FancyRow2(9, $buktifk, $fcbukti,  $border, $align, $style, $size); // Kiri

							$pdf->ln(5);
							$pdf->setfont('Arial', '', 8);
							$pdf->SetWidths(array(31, 3, 100));
							$borderform = array('', '', '');
							$fcform     = array('0', '0', '0');
							$alignform  = array('L', ':', 'L');
							$pdf->FancyRow(array('TELAH TERIMA DARI', ':', $nama_usaha), $fcform, $borderform, $align);
							$pdf->FancyRow(array('OUTLET', ':', $header->nama_pt), $fcform, $borderform);
							$pdf->FancyRow(array('', '', '( '. $header->nama .' )'), $fcform, $borderform);
							$pdf->FancyRow(array('NOMOR', ':', $param), $fcform, $borderform);
							$pdf->ln(5);

							$judultblfk = array('','FAKTUR','','');
							$judulfield = array('NO', 'NO', 'TGL', 'NILAI FAKTUR', 'NO PO');
							$pdf->setfont('Times', 'B', 9);
							$pdf->SetWidths(array(7, 50, 35, 37));
							$pdf->SetAligns(array('C', 'C'));
							$pdf->setfont('Times', 'B', 9);
							$pdf->row($judultblfk);
							$pdf->SetWidths(array(7, 30, 20, 35, 37	));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
							$pdf->row($judulfield);
							$pdf->SetWidths(array(7, 30, 20, 35, 37));
							$pdf->SetAligns(array('C', 'C', 'C', 'R', 'C'));
							$pdf->setfont('Times', '', 9);
							$pdf->SetFillColor(224, 235, 255);
							$pdf->SetTextColor(0);
							$pdf->SetFont('');

							// Di sini kita menggandakan informasi untuk kolom kanan
							$pdf->SetXY(150, 10); // Posisi untuk kolom kanan
							$pdf->setfont('Arial', 'B', 9);
							$pdf->SetWidths(array(130));
							$pdf->FancyRow2(9, $judul, $fc,  $border, $align, $style, $size); // Kanan
							$pdf->ln(5);
							$pdf->SetXY(150, 25); // Posisi untuk kolom kanan
							$pdf->setfont('Arial', 'B', 9);
							$pdf->SetWidths(array(130));
							$pdf->FancyRow2(9, $buktifk, $fcbukti,  $border, $align, $style, $size); // Kanan
							$pdf->ln(5);

							$pdf->SetXY(150, 37); // Posisi untuk kolom kanan
							$pdf->setfont('Arial', '', 8);
							$pdf->SetWidths(array(31, 3, 100));
							$borderform = array('', '', '');
							$fcform     = array('0', '0', '0');
							$alignform  = array('L', ':', 'L');
							$pdf->FancyRow(array('TELAH TERIMA DARI', ':', $nama_usaha), $fcform, $borderform, $align);
							$pdf->SetXY(150, 43); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('OUTLET', ':', $header->nama_pt), $fcform, $borderform);
							$pdf->SetXY(150, 49); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('', '', '( '. $header->nama .' )'), $fcform, $borderform);
							$pdf->SetXY(150, 55); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('NOMOR', ':', $param), $fcform, $borderform);
							$pdf->ln(5);

							$pdf->SetXY(150, 63); // Posisi untuk kolom kanan
							$pdf->SetWidths(array(7, 50, 35, 37));
							$pdf->setfont('Times', 'B', 9);
							$pdf->SetAligns(array('C', 'C'));
							$pdf->setfont('Times', 'B', 9);
							$pdf->row($judultblfk);
							
							$pdf->SetXY(150, 68); // Posisi untuk kolom kanan
							$pdf->SetWidths(array(7, 30, 20, 35, 37	));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
							$pdf->row($judulfield);
							$pdf->SetAligns(array('C', 'C', 'C', 'R', 'C'));
							

							// Mengatur detail dengan cara yang sama
							$nourut = 1;
							$total = 0;
							$DataArray =[];
							$pdf->setfont('Times', '', 8);
							foreach ($detail as $row) {
								// Kiri
								$pdf->SetXY(10, $pdf->GetY()); // Kembali ke kolom kiri
								$pdf->row(array(
									$nourut,
									$row->nomorfaktur,
									date('d-m-Y', strtotime($row->tglfaktur)),
									number_format($row->terhutang, 0, '.', ','),
									$row->nopo,
								));
								$GetYLeft = $pdf->GetY();
								// Kanan
								$pdf->SetXY(150, $pdf->GetY() - 5); // Posisi untuk kolom kanan
								$pdf->row(array(
									$nourut,
									$row->nomorfaktur,
									date('d-m-Y', strtotime($row->tglfaktur)),
									number_format($row->terhutang, 0, '.', ','),
									$row->nopo,
								));
								$GetYRight = $pdf->GetY() - 5;
								$total += $row->terhutang;
								$DataArray[] = ['faktur' => $row->nomorfaktur];
								$nourut++;
							}

							$pdf->SetXY(10, $GetYLeft); // Posisi untuk kolom kiri
							$pdf->SetWidths(array(57, 35, 37));
							$pdf->SetAligns(array('C', 'R', 'R'));

							$pdf->setfont('Times', 'B', 9);
							$pdf->Row(array('MATERAI  ','',''));
							$pdf->Row(array('JUMLAH + MATERAI',number_format($total, 0, '.', ','),''));
							$pdf->Row(array('Retur  ','',''));
							$pdf->Row(array('JUMLAH DI BAYAR',number_format($total, 0, '.', ','),''));
							$pdf->Ln(5);

							$pdf->SetXY(150, $GetYRight + 5); // Posisi untuk kolom kanan
							$pdf->SetWidths(array(57, 35, 37));
							$pdf->SetAligns(array('C', 'R', 'R'));

							$pdf->setfont('Times', 'B', 9);
							$pdf->Row(array('MATERAI  ','',''));
							$pdf->SetXY(150, $GetYRight + 10); // Posisi untuk kolom kanan
							$pdf->Row(array('JUMLAH + MATERAI',number_format($total, 0, '.', ','),''));
							$pdf->SetXY(150, $GetYRight + 15); // Posisi untuk kolom kanan
							$pdf->Row(array('Retur  ','',''));
							$pdf->SetXY(150, $GetYRight + 20); // Posisi untuk kolom kanan
							$pdf->Row(array('JUMLAH DI BAYAR',number_format($total, 0, '.', ','),''));
							$pdf->Ln(5);

							$pdf->SetWidths(array(120));
							$border = array('');
							$align  = array('R');
							$pdf->FancyRow(array('Bandung, ' . date('d M Y', strtotime($header->tanggal)),), 0, $border, $align);
							$pdf->ln(5);

							$pdf->SetWidths(array(100, 0, 25, 0));
							$pdf->SetFont('Arial', 'B', 8);
							$fc     = array('0', '0', '0', '0');
							$align  = array('L', 'C', 'L', 'L');
							$border = array('', 'B', '', 'B');
							$pdf->FancyRow(array('Kembali / Dibayar tgl :', '', 'Yang Menerima', ''), $fc, $border, $align);
							$pdf->SetFont('Arial', '', 7);
							$pdf->SetWidths(array(20, 3, 100));
							$border = array('', '', '');
							$fc     = array('0', '0', '0');
							$align  = array('L', ':', 'L');
							$NamaBank = $db->table('ms_akun')->getWhere(['kodeakun' => $header->kodebank])->getRow()->namaakun;
							$pdf->FancyRow(array('A/N', ':', ' PT Anugerah Jaya Fordic'), $fc, $border, $align);
							$pdf->FancyRow(array('Bank/ No. rek', ':', '- Bank BCA KOTA BANDUNG : 0088522527'), $fc, $border);
							$pdf->FancyRow(array('', '', '- Bank BJB KOTA BANDUNG : 005987478001'), $fc, $border);
							$pdf->FancyRow(array('', '', '- Bank CIMB Niaga KOTA BANDUNG : 800198608000'), $fc, $border);
							$pdf->ln(-5);
							$pdf->SetWidths(array(40, 3, 55));
							$border = array('', '', '', '');
							$align  = array('L', 'L', 'L', 'L');
							$pdf->FancyRow(array('', '', '', '(                                   )'), $fc, $border, $align);


							$pdf->SetXY(150, $GetYRight + 30); // Posisi untuk kolom kanan
							$pdf->setfont('Times', 'B', 9);
							$pdf->SetWidths(array(120));
							$border = array('');
							$align  = array('R');
							$pdf->FancyRow(array('Bandung, ' . date('d M Y', strtotime($header->tanggal)),), 0, $border, $align);
							$pdf->ln(5);

							$pdf->SetXY(150, $GetYRight + 40); // Posisi untuk kolom kanan
							$pdf->SetWidths(array(100, 0, 25, 0));
							$pdf->SetFont('Arial', 'B', 8);
							$fc     = array('0', '0', '0', '0');
							$align  = array('L', 'C', 'L', 'L');
							$border = array('', 'B', '', 'B');
							$pdf->FancyRow(array('Kembali / Dibayar tgl :', '', 'Yang Menerima', ''), $fc, $border, $align);

							$pdf->SetFont('Arial', '', 7);
							$pdf->SetWidths(array(20, 3, 100));
							$border = array('', '', '');
							$fc     = array('0', '0', '0');
							$align  = array('L', ':', 'L');
							$NamaBank = $db->table('ms_akun')->getWhere(['kodeakun' => $header->kodebank])->getRow()->namaakun;
							$pdf->SetXY(150, $GetYRight + 45); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('A/N', ':', ' PT Anugerah Jaya Fordic'), $fc, $border, $align);
							$pdf->SetXY(150, $GetYRight + 50); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('Bank/ No. rek', ':', '- Bank BCA KOTA BANDUNG : 0088522527'), $fc, $border);
							$pdf->SetXY(150, $GetYRight + 55); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('', '', '- Bank BJB KOTA BANDUNG : 005987478001'), $fc, $border);
							$pdf->SetXY(150, $GetYRight + 60); // Posisi untuk kolom kanan
							$pdf->FancyRow(array('', '', '- Bank CIMB Niaga KOTA BANDUNG : 800198608000'), $fc, $border);
							// $pdf->ln(-5);
							$pdf->SetXY(150, $GetYRight + 60); // Posisi untuk kolom kanan
							$pdf->SetWidths(array(40, 3, 55));
							$border = array('', '', '', '');
							$align  = array('L', 'L', 'L', 'L');
							$pdf->FancyRow(array('', '', '', '(                                   )'), $fc, $border, $align);
							$pdf->AliasNbPages();

							//PAGE 2
								$parts = explode('.', $param);
								// Mengambil elemen terakhir dari array yang dihasilkan
								$kode = end($parts);
								// Mengambil 5 karakter terakhir dari kode
								$kode_akhir = substr($kode, -5);
								$terbilang = ucwords($model_Global->terbilang($total));
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->Ln(-15);
								$pdf->setfont('Arial', 'B', 20);
								$pdf->SetWidths(array(110,25,70));
								$border = array('', '', '');
								$fc     = array('0', '0', '0');
								$align  = array('L', ':', 'L');
								$pdf->FancyRow(array($nama_usaha,'',''), $fc, $border, $align);
								$pdf->setfont('Arial', '', 9);
								$pdf->FancyRow(array($alamat,'A/N',': PT Anugerah Jaya Fordic'), $fc, $border);
								$pdf->FancyRow(array($fax,'Bank / No. Rek',': - Bank BCA : 0088522527'), $fc, $border);
								$pdf->FancyRow(array('','',''. '  - Bank BJB : 005987478001'), $fc, $border);
								$pdf->FancyRow(array('','',''. '  - Bank CIMB Niaga : 800198608000'), $fc, $border);
								$pdf->ln(8);

								$pdf->SetWidths(array(190));
								$pdf->setfont('Arial', 'B', 18);
								$border = array('');
								$align = array('C');
								$style = array('B');
								$style2 = array('');
								$size  = array('18');
								$size2  = array('9');
								$judul1 = array('KWITANSI');
								$judul2 = array('KWT/AJF/'. $kode_akhir.'/'. date('m', strtotime($header->tanggal)).'/'. date('Y', strtotime($header->tanggal)));
								$fc     = array('');
								$pdf->FancyRow2(0, $judul1, $fc,  $border, $align, $style, $size);
								$pdf->FancyRow2(8, $judul2, $fc,  $border, $align, $style2, $size2);
								$pdf->ln(5);
								
								$pdf->setfont('Arial', '', 10);
								$pdf->SetWidths(array(45, 5, 120));
								$border = array('', '', '');
								$fc     = array('0', '0', '0');
								$align  = array('L', ':', 'L');
								$pdf->FancyRow(array('TELAH TERIMA DARI', ':', $header->nama_pt .' ('. $header->nama .')'), $fc, $border, $align);
								$pdf->FancyRow(array('Uang Sejumlah', ':', $terbilang), $fc, $border);
								// Ambil item pertama lengkap
								$firstItem = $DataArray[0]['faktur']; // Corrected to access the first item's 'faktur'
								// Ambil sisa item mulai dari index 1
								$restItems = array_slice($DataArray, 1);
								// Ambil 3 karakter terakhir dari item-item sisanya
								$restNumbers = array_map(function($item) {
									return substr($item['faktur'], -3); // Corrected to access 'faktur' key
								}, $restItems);
								// Gabungkan item pertama lengkap dengan nomor sisanya yang dipisah titik koma
								$noNotaString = $firstItem;
								if (count($restNumbers) > 0) {
									$noNotaString .= '; ' . implode('; ', $restNumbers);
								}
								// Contoh penggunaan FancyRow
								$pdf->FancyRow(array('Untuk Pembayaran', ':', 'No. Faktur : ' . $noNotaString), $fc, $border);
								$pdf->Ln(5);

								$pdf->SetWidths(array(150));
								$border = array('');
								$align  = array('R');
								$pdf->FancyRow(array('Bandung, '. date('d M Y', strtotime($header->tanggal)),), 0, $border, $align);
								$pdf->ln(5);
								$totalbayar = array('Rp. ' . number_format($total). ',-');
								$pdf->SetWidths(array(60));
								$pdf->setfont('Arial', 'B', 18);
								$border = array('1');
								$align = array('C');
								$style = array('B');
								$size  = array('18');
								$pdf->FancyRow2(10, $totalbayar, $fc,  $border, $align, $style, $size);
								$pdf->Ln(5);
								$pdf->SetFont('Arial', '', 8);
								$pdf->SetWidths(array(40, 5, 80));
								$border = array('', '', '');
								$fc     = array('0', '0', '0');
								$align  = array('L', ':', 'L');
								$pdf->FancyRow(array('', '', '', $header->kodeuser), $fc, $border, $align);
								$pdf->AliasNbPages();

							// Akhiri



							$pdf->output($param . '.PDF', 'I');
							exit;
						} else {
							header('location:' . base_url());
						}
					}

					// =========================== PEMBAYARAN PESANAN PENJUALAN SELECTION START ======================//
					//Pembayaran Hutang Customer Page Start Here
					public function pembayaran_hutang_customer()
					{
						$session = session();
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 425);
						if ($akses->uadd == 1) {
							$tambah = 'Data Pembayaran CS';
						} else {
							$tambah = '';
						}
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Pembayaran Hutang Customer',
							'body' => 'admin/penjualan/transaksi/pembayarang-hutang-customer',
							'tambah' => $tambah
						];
						return view('layout/layout', $data);
					}

					public function bayar_table($tgl1, $tgl2)
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$db = db_connect();
						$query = "SELECT * FROM ar_penerimaan ";
						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " where tglbayar between '$tgl1' and '$tgl2'";
						} else {
							$query .= " where year(tglbayar)= (select periode_tahun from ms_identity) and
            						month(tglbayar)= (select periode_bulan from ms_identity)";
						}
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						$count = 0;
						foreach ($res as $value) {
							$DataSifile = $db->table('ar_sifile')->where(['kodebyr' => $value->kodebyr])->get()->getRow();
							if ($DataSifile->statusid == 2) {
								$status =  '<span class="badge badge-success">Lunas</span>';
							} else if ($DataSifile->statusid == 1) {
								$status = '<span class="badge badge-warning">Belum Lunas</span>';
							} else {
								$status =  '';
							}
							$akses = $model_Global->cek_menu_akses($level, 425);
							$count++;
							$data[] = [
								$count,
								$value->kodebyr,
								date('d-m-Y', strtotime($value->tglbayar)),
								'<button class="btn btn-dark" onclick="ShowFaktur(' . $value->idterima . ')"><i class="fa-solid fa-search text-white"></i></button>',
								$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
								$db->table('ms_akun')->where('kodeakun', $value->kodebank)->get()->getRow()->namaakun ?? '',
								$status,
								$akses->uharga == 1 ? number_format($value->jumlahbayar) : '0',
								$akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/pembayaranhutangcustomer/entri/' . $value->idterima) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>' : '',
								$akses->udel == 1 ? '<button class="btn btn-danger" onclick="data_delete(' . $value->idterima . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : '',
							];
						}
						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function getsearchbayarfaktur($data)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$db = db_connect();
						$query = $db->query("SELECT * from ar_penerimaan a inner join ar_penerimaandetil b on a.kodebyr=b.kodebyr where a.idterima='$data'")->getResult();
						$result = array();
						$count = 0;
						foreach ($query as $value) {
							$count++;
							?>
								<tr>
									<td align="centerx"><?php echo $count; ?></a></td>
									<td id="kodebyr"><?php echo $value->kodebyr; ?></td>
									<td id="tglbyr"><?php echo date('d-m-Y', strtotime($value->tglbayar)); ?></td>
									<td id="nomorfaktur"> <?php echo $value->nomorfaktur; ?></td>
								</tr>
							<?php }
					}

					public function pembayaran_hutang_customer_entri($id = '')
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$data = [];
						if ($id > 0) {
							$data       = $db->table('ar_penerimaan')->where('idterima', $id)->get()->getRow();
							$DetailItem = $db->table('ar_penerimaandetil')->where(['kodebyr' => $data->kodebyr])->get()->getResult();
							$DetailByrLain = $db->table('ar_penerimaanbiaya')->where(['kodebyr' => $data->kodebyr])->get()->getResult();
						}
						$res = $db->query("SELECT ar_sifile.*, ar_customer.* FROM ar_customer INNER JOIN ar_sifile ON ar_sifile.kodecust = ar_customer.kode WHERE ar_sifile.kodecust = ar_customer.kode AND ar_sifile.statusid = '1' GROUP by ar_sifile.kodecust")->getResult();
						$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
						$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
						$css = [];
						$js = [];
						$data  = [
							'data' => $data ?? [],
							'DetailItem' => $DetailItem ?? [],
							'DetailByrLain' => $DetailByrLain ?? [],
							'cabang' => $cabang,
							'nomor' => $model_Global->_Autonomor('ST'),
							'gudang' => $gudang,
							'arCust' => $arCust,
							'res' => $res,
							'css' => $css,
							'js' => $js,
							'title' => 'Pembayaran Hutang Customer',
							'body' => 'admin/penjualan/transaksi/pembayarang-hutang-customer-entri',
						];
						return view('layout/layout', $data);
					}

					public function bayar_getfaktur($data)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');

						$data = $db->query("SELECT ar_sifile.*, totalsi as total, (totalsi-jumlahbayar-uangmuka) as hutang from ar_sifile where kodecust='$data' and statuskontrabon=1 and totalsi>jumlahbayar+uangmuka")->getResult();
						$vdata = array();
						foreach ($data as $row) {
							$duangmuka = $db->query("SELECT * from ar_uangmuka where kodeso = '$row->kodeso' and jumlahum>jumlahbayar")->getRow();
							if ($duangmuka) {
								$uangmuka = $duangmuka->jumlahum;
							} else {
								$uangmuka = 0;
							}
							$DataSofile = $db->query("SELECT * from ar_sofile where kodeso = '$row->kodeso'")->getRow();

							$vdata[] = array(
								'kodesi' => $row->kodesi,
								'tglsi' => date('d-m-Y', strtotime($row->tglsi)),
								'totalsi' => $row->totalsi,
								'uangmuka' => $uangmuka,
								'hutang' => $row->hutang,
								'total' => $row->total,
								'nopo' => $DataSofile ? $DataSofile->no_po : '',
								'statusbon' => $row->statuskontrabon,
							);
						}
						echo json_encode($vdata);
					}

					public function bayar_save()
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];
						$session = session();
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 425);
						// Table
						$builder            = $db->table('ar_penerimaan');
						$builderDetail      = $db->table('ar_penerimaandetil');
						$buildSifile        = $db->table('ar_sifile');
						$buildCustomer    	= $db->table('ar_customer');
						$buildBiaya    		= $db->table('ar_penerimaanbiaya');
						try {
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$db->transStart();
								$cs       = $input['cust'];
								$cabang = $session->get('cabang');
								$data_customer 	= $buildCustomer->getWhere(['kode' => $cs])->getRow();
								$DataBayar = $builder->getWhere(['kodebyr' => $input['nomorbukti']])->getRow();
								if ($param == 0) {
									empty($DataBayar) ? $nobukti = $input['nomorbukti'] : $nobukti = $model_Global->_counter1('ST');
								}

								if ($param != 0) {

									//update faktur	
									$bayar = $builderDetail->getWhere(['kodebyr' => $input['nomorbukti'], 'cabang' => $cabang])->getResult();
									foreach ($bayar as $row) {
										$_bayar = $row->bayar;
										$_uangmuka = $row->uangmuka;
										$faktur = $row->nomorfaktur;
										$db->query("UPDATE ar_sifile set jumlahbayar = jumlahbayar - '$_bayar' , uangmuka = uangmuka - '$_uangmuka' where kodesi='$faktur' and kodecbg='$cabang'");

										$dfaktur = $buildSifile->getWhere(['kodesi' => $faktur, 'kodecbg' => $cabang])->getRow();
										$kodeso  =  $dfaktur->kodeso;
										$db->query("UPDATE ar_uangmuka set jumlahbayar = jumlahbayar-'$_uangmuka' where kodeso = '$kodeso'");
									}
									//update ar_customer
									$db->query("UPDATE ar_customer set saldokredit = saldokredit - '$DataBayar->jumlahbayar' where kode = '$DataBayar->kodecust'");
									$builderDetail->delete(['kodebyr' => $input['nomorbukti'], 'cabang' => $cabang]);
								}

								$faktur  = $input['faktur'];
								$tglfaktur   = $input['tglfaktur'];
								$totfaktur   = $input['totfaktur'];
								$hutang = $input['hutang'];
								$bayar = $input['bayar'];
								$disc  = $input['disc'];
								$uangmuka  = $input['uangmuka'];
								$_subtotalplusdiskon = str_replace(',', '', $input['_subtotalplusdiskon']);

								$jumdata  = count($faktur);

								$nourut = 1;

								$profile = $model_Global->_LoadProfile();
								$akun_kredit     = $profile->akun_piutang;
								$akun_debet   	 = $input['kasbank'];

								for ($i = 0; $i < $jumdata; $i++) {
									$_faktur   = $faktur[$i];
									$_totfaktur = str_replace(',', '', $totfaktur[$i]);
									$_hutang    = str_replace(',', '', $hutang[$i]);
									$_bayar     = str_replace(',', '', $bayar[$i]);
									$_disc      = str_replace(',', '', $disc[$i]);
									$_uangmuka  = str_replace(',', '', $uangmuka[$i]);

									$_total = $_bayar - $_disc + $_uangmuka;

									$datad = array(
										'nomorfaktur' => $faktur[$i],
										'tglfaktur' => date('Y-m-d', strtotime($tglfaktur[$i])),
										'totalfaktur' => $_totfaktur,
										'terhutang' => $_hutang,
										'uangmuka' => $_uangmuka,
										'bayar' => $_bayar,
										'diskon' => $_disc,
										'cabang' => $cabang
									);
									if ($_total != "0") {
										if ($param == 0) {
											$datad['kodebyr'] = $nobukti;
											$builderDetail->insert($datad);

											//update faktur	
											$db->query("UPDATE ar_sifile set jumlahbayar = jumlahbayar + '$_bayar' , uangmuka = uangmuka + '$_uangmuka', kodebyr='$nobukti' where kodesi='$faktur[$i]'");

											if ($_uangmuka > 0) {
												$dfaktur = $buildSifile->getWhere(['kodesi' => $faktur[$i], 'kodecbg' => $cabang])->getRow();
												$kodeso  = $dfaktur->kodeso;
												$db->query("UPDATE ar_uangmuka set jumlahbayar=jumlahbayar+$_uangmuka where kodeso = '$kodeso'");
											}
										} else {
											$datad['kodebyr'] = $input['nomorbukti'];
											$builderDetail->insert($datad);

											//update faktur	
											$db->query("UPDATE ar_sifile set jumlahbayar = jumlahbayar + '$_bayar' , uangmuka = uangmuka + '$_uangmuka' where kodesi='$faktur[$i]'");

											if ($_uangmuka > 0) {
												$dfaktur = $buildSifile->getWhere(['kodesi' => $faktur[$i], 'kodecbg' => $cabang])->getRow();
												$kodeso  = $dfaktur->kodeso;
												$db->query("UPDATE ar_uangmuka set jumlahbayar=jumlahbayar+$_uangmuka where kodeso = '$kodeso'");
											}
										}

										$db->query("UPDATE ar_sifile set statusid=2 where jumlahbayar+uangmuka>=totalsi and kodesi = '$faktur[$i]'");
									}
								}

								$_jumlahbayar = str_replace(',', '', $input['jumlahbayar']);

								$data = array(
									'kodecbg' => $cabang,
									'kodecust' => $input['cust'],
									'kodebank'  => $input['kasbank'],
									'metodebayar'  => $input['metodebayar'],
									'tglbayar'   => date('Y-m-d', strtotime($input['tanggal'])),
									'nomorcek'  => $input['nomorcek'],
									'tglcek'   => date('Y-m-d', strtotime($input['tanggalcek'])),
									'jumlahbayar'  => $_jumlahbayar,
									'kodeuser' => $session->get('username'),
									'keterangan'     => $input['keterangan'],
								);

								$datalogs = [
									'date' => date('Y-m-d H:i:s'),
									'userid' => $session->get('username'),
								];

								if ($param == 0) {
									$data['kodebyr'] = $nobukti;
									$builder->insert($data);
									$model_Global->_updatecounter1('ST');
									$db->query("UPDATE ar_customer set saldokredit = saldokredit + $_jumlahbayar where kode = '$cs'");

									$datalogs['detail'] = 'Tambah data Pembayaran Penjualan dengan kode ' . $nobukti;
									$db->table("logs")->insert($datalogs);

									echo json_encode(['status' => 1, 'message' => $nobukti]);
								} else {
									$builder->where(['kodebyr' => $input['nomorbukti'], 'kodecbg' => $cabang])->update($data);

									$datalogs['detail'] = 'Edit data Pembayaran Penjualan dengan kode ' . $input['nomorbukti'];
									$db->table("logs")->insert($datalogs);

									echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
								}

								// Table 2
								$buildakun        = $db->table('ms_akun');
								$buildTerima      = $db->table('tr_penerimaan');
								$buildTerimad     = $db->table('tr_penerimaand');
								$buildTrkasbank   = $db->table('tr_transaksikasbank');
								$buildjurnal      = $db->table('tr_jurnal');
								// data inputan keuangan masuk

								$tahun 		= date('Y', strtotime($input['tanggal']));
								$bulan 		= date('m', strtotime($input['tanggal']));
								$kodebukti 	= $buildakun->getWhere(['kodeakun' => $input['kasbank']])->getRow()->kodem;
								$vbukti 	= $kodebukti . '%';
								$likeTerima = $buildTerima->like(['terima_nomor' => $vbukti])->getWhere(['terima_kodecbg' => $cabang, 'year(terima_tanggal)' => $tahun, 'month(terima_tanggal)' => $bulan]);
								$max        = $likeTerima->getNumRows();
								$nourut  	        = $max + 1;
								$nomorbukti   = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);

								if ($param != 0) {
									$tr_masuk 	    = $buildTerima->getWhere(['terima_kodebyr' => $input['nomorbukti'], 'terima_kodecbg' => $cabang])->getRow();
									$register 		= $tr_masuk->terima_register;
									$nomorbukti  	= $tr_masuk->terima_nomor;
									$buildTrkasbank->delete(['novoucher' => $nomorbukti, 'cabang' => $cabang, 'penerima' => $data_customer->nama]);
								}

								//data input table tr_penerimaan
								$data = array(
									'terima_nomor' 		=> $nomorbukti,
									'terima_tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
									'terima_kasbank' 	=> $input['kasbank'],
									'terima_kodecbg' 	=> $cabang,
									'terima_penerima' 	=> $data_customer->nama,
									'terima_status' 	=> 2,
									'terima_userentry' 	=> $session->get('username'),
									'terima_byrcekgiro' => $input['metodebayar'],
									'terima_tglcekgiro' => $input['tanggalcek'],
									'terima_nocekgiro' => $input['nomorcek'],
									'terima_posted' => 2,
									'terima_tanggalposted' => date('Y-m-d', strtotime($input['tanggal'])),
									'terima_userposted' => $session->get('username'),
								);

								$datatr = array(
									'tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
									'kasbank' 		=> $input['kasbank'],
									'metodebyr' 	=> $input['metodebayar'],
									'penerima' 		=> $data_customer->nama,
									'pemasukan' 	=> $_subtotalplusdiskon,
									'pengeluaran' 	=> 0,
									'tglentry'		=> date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
									'userid'		=> $session->get('username'),
									'cabang'		=> $cabang,
								);

								if ($param == 0) {
									$data['terima_kodebyr'] = $nobukti;
									$data['terima_uraian'] = 'Pembayaran Piutang dari ' . $data_customer->nama . ' Pembayaran ' . $nobukti;
									$buildTerima->insert($data);
									$register = $db->insertID();
									$datatr['novoucher'] = $nomorbukti;
									$datatr['uraian'] = 'Pembayaran Piutang dari ' . $data_customer->nama . ' Pembayaran '  . $nomorbukti;
									$buildTrkasbank->insert($datatr);
								} else {
									$buildTerima->where(['terima_kodebyr' => $input['nomorbukti'], 'terima_register' => $register, 'terima_kodecbg' => $cabang])->update($data);
									$buildTerimad->delete(['terimad_register' => $register, 'cabang' => $cabang]);
									$datatr['novoucher'] = $nomorbukti;
									$datatr['uraian'] = 'Pembayaran Piutang dari ' . $data_customer->nama . ' Pembayaran '  . $input['nomorbukti'];
									$buildTrkasbank->insert($datatr);
								}

								$buildjurnal->delete(['novoucher' => $nomorbukti, 'noref' => $input['nomorbukti'], 'jenis' => 'JK', 'wbs' => $kodebukti]);
								$nourut = 1;

								//data input table tr_penerimaand
								$datad = array(
									'terimad_register' 	=> $register,
									'terimad_nomor' 	=> $nomorbukti,
									'terimad_nourut' 	=> $nourut,
									'terimad_akun' 		=> $akun_debet,
									'terimad_uraian' 	=> 'Pembayaran Piutang dari ' . $data_customer->nama,
									'terimad_jumlah' 	=> $_subtotalplusdiskon,
									'cabang'			=> $cabang

								);
								if ($akun_kredit != "") {
									$buildTerimad->insert($datad);

									$JurnalKredit = array(
										'novoucher' 	=> $nomorbukti,
										'tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
										'keterangan' 	=> 'Pembayaran Piutang dari '  . $data_customer->nama,
										'nourut' 		=> $nourut,
										'jenis' 		=> 'JK',
										'posted' 		=> 'Y',
										'kodecbg' 		=> $cabang,
										'wbs' 			=> $kodebukti,
										'kodeakun' 		=> $akun_kredit,
										'debet' 		=> 0,
										'kredit' 		=> $_jumlahbayar,
										'userid' 		=> $session->get('username'),
									);
									if ($param == 0) {
										$JurnalKredit['noref'] = $nobukti;
										$buildjurnal->insert($JurnalKredit);
									} else {
										$JurnalKredit['noref'] = $input['nomorbukti'];
										$buildjurnal->insert($JurnalKredit);
									}
									$nourut++;
								}

								$JurnalDebet = array(
									'novoucher' 	=> $nomorbukti,
									'noref' 		=> $register,
									'tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
									'keterangan' 	=> 'Pembayaran Piutang dari ' . $data_customer->nama,
									'nourut' 		=> 1,
									'jenis' 		=> 'JK',
									'posted' 		=> 'Y',
									'kodecbg' 		=> $cabang,
									'wbs' 			=> $kodebukti,
									'kodeakun' 		=> $akun_debet,
									'debet' 		=> $_jumlahbayar,
									'kredit' 		=> 0,
									'userid' 		=> $session->get('username'),
								);
								if ($param == 0) {
									$JurnalDebet['noref'] = $nobukti;
									$buildjurnal->insert($JurnalDebet);
								} else {
									$JurnalDebet['noref'] = $input['nomorbukti'];
									$buildjurnal->insert($JurnalDebet);
								}

								//rincian biaya lain-lain
								$lkode  = $input['lkode'];
								$ljumlah = $input['ljumlah'];
								$lket   = $input['lket'];
								$ljumdata  = count($lkode);
								if ($param != 0) {
									$buildBiaya->delete(['kodebyr' => $input['nomorbukti']]);
								}

								$totbiaya = 0;
								$itembiaya = 2;
								for ($i = 0; $i < $ljumdata; $i++) {
									$totbiaya = $totbiaya + $ljumlah[$i];
									$datadbiaya = array(
										'kodeakun' => $lkode[$i],
										'jumlah' => $ljumlah[$i],
										'ket' => $lket[$i],
										'cabang' 	=> $cabang,
									);

									$dataTerima = array(
										'terimad_register' 	=> $register,
										'terimad_nomor' 	=> $nomorbukti,
										'terimad_nourut' 	=> $nourut,
										'terimad_akun' 		=> $lkode[$i],
										'terimad_uraian' 	=> $lket[$i],
										'terimad_jumlah' 	=> $ljumlah[$i],
										'cabang'			=> $cabang

									);

									$dataTrkasbank = array(
										'tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
										'kasbank' 		=> $lkode[$i],
										'metodebyr' 	=> $input['metodebayar'],
										'penerima' 		=> $data_customer->nama,
										'pemasukan' 	=> $ljumlah[$i],
										'pengeluaran' 	=> 0,
										'tglentry'		=> date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
										'userid'		=> $session->get('username'),
										'cabang'		=> $cabang,
									);

									if ($lkode[$i] != "") {
										if ($param == 0) {
											$datadbiaya['kodebyr'] = $nobukti;
											$buildBiaya->insert($datadbiaya);
											$buildTerimad->insert($dataTerima);

											$dataTrkasbank['novoucher'] = $nomorbukti;
											$dataTrkasbank['uraian'] = $lket[$i] . 'Pembayaran Piutang dari ' . $data_customer->nama . ' Pembayaran ' . $nobukti;
											$buildTrkasbank->insert($dataTrkasbank);
											$itembiaya++;
										} else {
											$datadbiaya['kodebyr'] = $input['nomorbukti'];
											$buildBiaya->insert($datadbiaya);
											$buildTerimad->insert($dataTerima);
											$dataTrkasbank['novoucher'] = $nomorbukti;
											$dataTrkasbank['uraian'] = $lket[$i] . 'Pembayaran Piutang dari ' . $data_customer->nama . ' Pembayaran ' . $input['nomorbukti'];
											$buildTrkasbank->insert($dataTrkasbank);
											$itembiaya++;
										}
									}
								}
								$db->transComplete();
								if ($db->transStatus() === false) {
									// Jika terjadi kesalahan, batalkan transaksi
									$db->transRollback();
									echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
								}
								// Transaksi sukses, commit perubahan ke database
								$db->transCommit();
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function cetak_bayar($param)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$alamat1  = $profile->alamat1;
							$alamat2  = $profile->alamat2;
							$hp 	  = $profile->hp;
							$email 	  = $profile->email;

							$queryh = "select * from ar_penerimaan inner join ar_customer on ar_penerimaan.kodecust=ar_customer.kode where kodebyr = '$param'";

							$header = $db->query($queryh)->getRow();
							$pdf = new simkeu_nota_helper();
							$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
							$pdf->setjudul('');
							$pdf->setsubjudul('');
							$pdf->addpage("P", "A4");
							$pdf->setsize("P", "A4");
							$pdf->SetWidths(array(190));
							$size   = array('0', '0', '0');
							$pdf->setfont('Arial', 'B', 18);
							$border = array('');
							$align = array('C');
							$style = array('');
							$size  = array('18');
							$pdf->SetFillColor(230, 230, 230);
							$judul = array('Tanda Terima Pembayaran');
							$fc     = array('1');
							$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size);
							$pdf->ln(5);
							$pdf->setfont('Arial', '', 10);
							$pdf->SetWidths(array(15, 30, 5, 100));
							$border = array('', '', '', '');
							$fc     = array('0', '0', '0', '0');
							$align  = array('L', 'L', ':', 'L');
							$pdf->FancyRow(array('', 'Nomor', ':', $header->kodebyr), $fc, $border, $align);
							$pdf->FancyRow(array('', 'Tanggal', ':', date('d M Y', strtotime($header->tglbayar))), $fc, $border);

							$pdf->ln(2);
							$pdf->SetWidths(array(10, 100));
							$align = array('C', 'L');
							$pdf->FancyRow(array('', 'Telah menerima pembayaran / Cek / Giro sebagai berikut :'), $fc, $border, $align);
							$pdf->ln(2);
							$pdf->SetWidths(array(15, 30, 5, 100));
							$border = array('', '', '', '');
							$fc     = array('0', '0', '0', '0');
							$align  = array('L', 'L', 'C', 'L');
							$style  = array('', '', '', '');
							$size  = array('0', '0', '0', '0');
							$pdf->FancyRow(array('', 'Diterima Dari', ':', $header->nama), $fc, $border, $align,  $style, $size);
							$style = array('', '', 'B', 'B');
							$pdf->FancyRow(array('', 'Jumlah', ':', 'Rp ' . number_format($header->jumlahbayar, 0, '.', ',')), $fc, $border, $align, $style, $size);
							$style = array('', '', '', '');
							$pdf->FancyRow(array('', 'Terbilang', ':', ucwords($model_Global->terbilang($header->jumlahbayar))), $fc, $border, $align, $style, $size);
							$pdf->settextcolor(0);
							$pdf->SetWidths(array(80, 50, 2, 50));
							$pdf->SetFont('Arial', '', 10);
							$pdf->SetAligns(array('C', 'C', 'C', 'C'));
							$pdf->ln(10);

							$border = array('', '', '', '');
							$align  = array('C', 'C', 'C', 'C');
							$pdf->FancyRow(array('', 'Mengetahui', '', 'Penerima'), 0, $border, $align);
							$pdf->ln(1);
							$pdf->ln(15);

							$pdf->SetFont('Arial', '', 8);
							$fc     = array('0', '0', '0', '0');
							$align  = array('L', 'C', 'L', 'L');
							$border = array('', 'B', '', 'B');
							$pdf->FancyRow(array('* Pembayaran akan dianggap sah', '', '', ''), $fc, $border, $align);
							$border = array('', '', '', '');
							$align  = array('L', 'L', 'L', 'L');
							$pdf->FancyRow(array('setelah cek/giro dicairkan', 'Tgl.', '', 'Tgl.'), $fc, $border, $align);
							$pdf->AliasNbPages();
							$pdf->output($param . '.PDF', 'I');
							exit;
						} else {
							header('location:' . base_url());
						}
					}

					public function DeletePembayaranCustomer($nomor)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$level = $session->get('level');
						$cabang = $session->get('cabang');
						$akses = $model_Global->cek_menu_akses($level, 425);
						// Table
						$builder            = $db->table('ar_penerimaan');
						$builderDetail      = $db->table('ar_penerimaandetil');
						$buildSifile        = $db->table('ar_sifile');
						$buildBiaya    		= $db->table('ar_penerimaanbiaya');
						$buildTerima      	= $db->table('tr_penerimaan');
						$buildTerimad     	= $db->table('tr_penerimaand');
						$buildTrkasbank   	= $db->table('tr_transaksikasbank');
						$buildjurnal      	= $db->table('tr_jurnal');
						if ($akses->udel == 1) {
							try {
								$db->transStart();
								$RowBayar = $builder->where('idterima', $nomor)->get()->getRow();
								$RowTerimaKasBank = $buildTerima->where(['terima_kodebyr' => $RowBayar->kodebyr, 'terima_kodecbg' => $cabang])->get()->getRow();
								$ResultBayarDetail = $builderDetail->getWhere(['kodebyr' => $RowBayar->kodebyr, 'cabang' => $cabang])->getResult();
								foreach ($ResultBayarDetail as $row) {
									$_bayar = $row->bayar;
									$_uangmuka = $row->uangmuka;
									$faktur = $row->nomorfaktur;

									$UpdateJumlahByrSifile = $db->query("UPDATE ar_sifile SET jumlahbayar = jumlahbayar - $_bayar, uangmuka = uangmuka - $_uangmuka WHERE kodesi = '$faktur'");

									$UpdateSts2Sifile = $db->query("UPDATE ar_sifile SET statusid = 2 WHERE jumlahbayar+uangmuka>=totalsi and kodesi = '$faktur'");
									$UpdateSts1Sifile = $db->query("UPDATE ar_sifile SET statusid = 1 WHERE jumlahbayar+uangmuka<totalsi and kodesi = '$faktur'");

									$RowFaktur = $buildSifile->where('kodesi', $faktur)->get()->getRow();
									$UpdateUangMuka = $db->query("UPDATE ar_uangmuka SET jumlahbayar = jumlahbayar - $_uangmuka WHERE kodeso = '$RowFaktur->kodeso'");
								}
								$datalogs = [
									'detail' => 'Hapus data Penjualan Pembayaran CS dengan kode ' . $RowBayar->kodebyr,
									'date' => date('Y-m-d H:i:s'),
									'userid' => $session->get('username'),
								];
								$db->table("logs")->insert($datalogs);
								//Pembayaran CS
								$builder->delete(['idterima' => $nomor]);
								$builderDetail->delete(['kodebyr' => $RowBayar->kodebyr, 'cabang' => $cabang]);
								$buildBiaya->delete(['kodebyr' => $RowBayar->kodebyr, 'cabang' => $cabang]);
								//KasBank
								$buildTerimad->delete(['terimad_nomor' => $RowTerimaKasBank->terima_nomor, 'cabang' => $cabang]);
								$buildTrkasbank->delete(['novoucher' => $RowTerimaKasBank->terima_nomor, 'cabang' => $cabang]);
								$buildjurnal->delete(['novoucher' => $RowTerimaKasBank->terima_nomor, 'kodecbg' => $cabang]);
								$buildTerima->delete(['terima_kodebyr' => $RowBayar->kodebyr, 'terima_kodecbg' => $cabang]);
								$db->transComplete();
								if ($db->transStatus() === false) {
									// Jika terjadi kesalahan, batalkan transaksi
									$db->transRollback();
									echo json_encode(['status' => 2, 'message' => 'Delete Gagal Mohon Coba lagi']);
								}
								// Transaksi sukses, commit perubahan ke database
								$db->transCommit();
							} catch (\Exception $e) {
								echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
							}
						}
					}

					// =========================== RETUR PESANAN PENJUALAN SELECTION START ======================//
					//Retur Penjualan Page Start Here
					public function retur_penjualan()
					{
						$session = session();
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 426);
						if ($akses->uadd == 1) {
							$tambah = 'Data Retur';
						} else {
							$tambah = '';
						}
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Retur Penjualan',
							'body' => 'admin/penjualan/transaksi/retur-penjualan',
							'tambah' => $tambah
						];
						return view('layout/layout', $data);
					}

					public function retur_penjualan_entri($id = '')
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$taxes = $model_Global->_LoadProfile()->taxes_desimal;
						$data = [];
						if ($id > 0) {
							$data       = $db->table('ar_retur')->where('idretur', $id)->get()->getRow();
							$DetailItem = $db->table('ar_returdetil')->where(['koderetur' => $data->koderetur])->get()->getResult();
							$ReturPaket = $db->table("ar_returpaket")->where(['koderetur' => $data->koderetur])->get()->getResult();
						}
						$res = $db->query("SELECT * FROM ar_customer a inner join ar_sifile b on a.kode = b.kodecust where b.retur = 1 and b.kodecbg='$cabang' group by b.kodecust")->getResult();
						$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
						$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
						$css = [];
						$js = [];
						$data  = [
							'data' => $data ?? [],
							'DetailItem' => $DetailItem ?? [],
							'ReturPaket' => $ReturPaket ?? [],
							'cabang' => $cabang,
							'nomor' => $model_Global->_Autonomor('SR'),
							'NoFakturPajak' => $model_Global->_AutonomorFakturPenjualan('FP'),
							'gudang' => $gudang,
							'arCust' => $arCust,
							'res' => $res,
							'css' => $css,
							'js' => $js,
							'title' => 'Retur Penjualan',
							'body' => 'admin/penjualan/transaksi/retur-penjualan-entri',
							'taxes' =>  $taxes
						];
						return view('layout/layout', $data);
					}

					public function retur_table($tgl1, $tgl2)
					{
						$session = session();
						$cabang = $session->get('cabang');
						$level = $session->get('level');
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$akses = $model_Global->cek_menu_akses($level, 426);
						$db = db_connect();
						$query = "SELECT * FROM ar_retur where kodecbg='$cabang'";
						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and tglretur between '$tgl1' and '$tgl2'";
						} else {
							$query .= " and year(tglretur)= (select periode_tahun from ms_identity) and
            						month(tglretur)= (select periode_bulan from ms_identity)";
						}
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						$count = 0;
						foreach ($res as $value) {
							if ($value->statusid != 3) {
								$cetak = '<a href="' . base_url('penjualan/transaksi/returpenjualancetak/' . $value->koderetur) . '" target="_blank" class="btn btn-info"><i class="fa-solid fa-print text-white"></i> Cetak</a>';
							} else {
								$cetak =  '';
							}
							$count++;
							$data[] = [
								$count,
								$value->koderetur,
								date('d-m-y H:i:s', strtotime($value->tglretur)),
								$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
								$value->keterangan,
								$akses->uharga == 1 ? number_format($value->totalretur) : '0',
								$akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/returpenjualan/entri/' . $value->idretur) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>' : '',
								$akses->ucetak == 1 ? $cetak : '',
								$akses->udel == 1 ? '<button class="btn btn-danger" onclick="data_delete(' . $value->idretur . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : '',
							];
						}
						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function retur_showsi($data)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get('cabang');
						if (!empty($data)) {
							//$po  = $this->db->get_where('ar_sifile',array('kodecust' => $supp, 'statusid' => 1))->result();	
							$si  = $db->query("SELECT * from ar_sifile where kodesi not in(select kodesi from ar_retur where ar_retur.kodecbg='$cabang') and kodecust='$data' and ar_sifile.kodecbg='$cabang'")->getResult();

							?>
								<select name="kodesi" id="kodesi" class="form-control  input-medium select2">
									<option value="">-- Nomor Faktur ---</option>
									<?php
									foreach ($si  as $row) {
									?>
										<option value="<?php echo $row->kodesi; ?>"><?php echo $row->kodesi; ?></option>

									<?php } ?>
								</select><span class="input-group-btn">
									<a class="btn blue" onclick="getpoheader();getpo();"><i class="fa fa-download"></i></a>
								</span>


					<?php

						} else {
							echo "";
						}
					}

					public function retur_getdatasi($data)
					{
						$db = db_connect();
						$session = session();
						$cabang = $session->get("cabang");
						$DataKe1 = $db->table("ar_sifile")->where(['kodesi' => $data, 'kodecbg' => $cabang])->get()->getRow();

						$DataKe2 = $db->query("SELECT a.*, b.namabarang FROM ar_sidetail a 
                            inner join inv_barang b on a.kodeitem = b.kodeitem 
                            where a.kodesi='$data' and b.kdcbg='$cabang'")->getResult();

						$DataKe3 = $db->query("SELECT a.*, b.namabarang AS namabarang_paket,  c.namabarang AS namabarang
						FROM ar_sipaket a 
						INNER JOIN inv_barang b ON a.kodeitem_paket = b.kodeitem 
						INNER JOIN inv_barang c ON a.kodeitem_barang = c.kodeitem 
						WHERE a.kodesi='$data' AND b.kdcbg='$cabang' AND c.kdcbg='$cabang' ORDER BY id_kodeitembarang")->getResult();

						$response = ['DataHeader' => $DataKe1, 'Dataitem' => $DataKe2, 'DataitemPaket' => $DataKe3];
						echo json_encode($response);
					}

					public function retur_save()
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$taxes = $model_Global->_LoadProfile()->taxes_desimal;
						$db = db_connect();
						$input = $this->request->getPost();
						$param = $input['id'];

						$session = session();
						$level = $session->get('level');
						$akses = $model_Global->cek_menu_akses($level, 426);
						// Table
						$builder            = $db->table('ar_retur');
						$builderDetail      = $db->table('ar_returdetil');
						$builderPaket      	= $db->table('ar_returpaket');
						$builderINVBarang   = $db->table('inv_barang');
						$INVtransaksi       = $db->table('inv_transaksi');
						$INVBarangBatch     = $db->table('inv_nobatch');
						$BuildCs       		= $db->table('ar_customer');
						$TrTerima     		= $db->table('tr_penerimaan');
						$BuildArSifile      = $db->table('ar_sifile');
						$BuildArKirim     	= $db->table('ar_kirim');
						$DataSifile = $BuildArSifile->getWhere(['kodesi' => $input['kodesi'], 'kodecbg' => $session->get('cabang')])->getRow();
						$DataArKirim = $BuildArKirim->getWhere(['kodekirim' => $DataSifile->kodesd, 'kodecbg' => $session->get('cabang')])->getRow();
						//Tabel Mutasi Cabang
						$TableMohonMutasiCabang 	= $db->table('inv_mohon_mutasi_cabang');
						$TableMutasiCabang 			= $db->table('inv_mutasi_cabang');
						$TableMutasiCabangDetail 	= $db->table('inv_mutasi_cabang_detail');
						$TableAccMutasiCabang 		= $db->table('inv_acc_mutasi_cabang');
						$TableAccMutasiCabangDetail = $db->table('inv_accd_mutasi_cabang');
						$TableInvGudang 			= $db->table('inv_gudang');
						$TableInvBarangGudang 		= $db->table('inv_barang_gudang');
						$TableInvBarangPaket 		= $db->table('inv_barang_paket');
						try {
							$db->transStart();
							if ($akses->uadd == 1 || $akses->uedit == 1) {
								$cabang   = $session->get('cabang');
								$kodesi   = $input['kodesi'];
								$gudang   = $input['gudang'];
								$_vvsubtotal	= str_replace(",", "", $input['vsubtotal']);
								$_vvdiskon		= str_replace(",", "", $input['vdiskon']);
								$_vvdpp			= str_replace(",", "", $input['vdpp']);
								$_vvppn			= str_replace(",", "", $input['vppn']);
								$_vvtotal		= str_replace(",", "", $input['vtotal']);

								$DataCs = $BuildCs->getWhere(['kode' => $input['cust']])->getRow();

								$DataRetur = $builder->getWhere(['koderetur' => $input['nomorbukti'], 'kodecbg' => $cabang])->getRow();
								if (empty($DataRetur)) {
									$nobukti = $input['nomorbukti'];
								} else {
									$nobukti = $model_Global->_counter1('SR');
								}
								// Retur Pembelian
								$buildReturPembelian  		= $db->table('ap_retur');
								$buildReturPembelianDetail  = $db->table('ap_returdetil');
								$buildsupplier				= $db->table('ap_supplier');
								$noReturBeli 				= $model_Global->_Autonomor('PR');
								$returfile = $buildReturPembelian->getWhere(['koderetur' => $noReturBeli, 'kodecbg' => '01'])->getRow();
								if (empty($returfile)) {
									$nobuktiReturBeli = $noReturBeli;
								} else {
									$nobuktiReturBeli = $model_Global->_Autonomor('PR');
								}
								//End Bukti Retur Pembelian

								if ($param != 0) {
									$dataretur = $builderDetail->getWhere(['koderetur' => $input['nomorbukti']])->getResult();
									foreach ($dataretur as $row) {
										$db->query("UPDATE inv_barang 
					 					set stok = stok - '$row->qtyretur', stok_so = stok_so - '$row->qtyretur' 
					 					where kodeitem = '$row->kodeitem' and kdcbg='$cabang'");

										$db->query("UPDATE inv_barang_gudang 
					 					set qty = qty - '$row->qtyretur' 
										where gudang = '$gudang' and kodeitem = '$row->kodeitem' and cabang = '$cabang'");

										$INVtransaksi->delete(['nobukti' => $input['nomorbukti'], 'kodeitem' => $row->kodeitem, 'cabang' => $cabang]);
									}
									$builderDetail->delete(['koderetur' => $input['nomorbukti'], 'kodegudang' => $gudang]);
								}
								$kode  = $input['kode'];
								$qty   = $input['qty'];
								$sat   = $input['sat'];
								$no_batch   = $input['no_batch'];
								$tgl_exp   = $input['tgl_exp'];
								$harga = $input['harga'];
								$disc  = $input['disc'];
								$idsiddetail  = $input['idsidetail'];

								$jumdata  = count($kode);

								$tot = 0;
								$tdisc = 0;
								$tothpp = 0;

								for ($i = 0; $i < $jumdata; $i++) {
									$hpp = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->getRow();
									$vjum  = $qty[$i] * $harga[$i];
									$vdisc = $vjum * ($disc[$i] / 100);
									$tot   = $tot + $vjum;
									$tdisc = $tdisc + $vdisc;

									$tothpp = $tothpp + ($hpp->hargabeli * $qty[$i]);

									$datad = array(
										'kodeitem' => $kode[$i],
										'no_batch' => $no_batch[$i],
										'tanggal_exp' => $tgl_exp[$i],
										'qtyretur' => $qty[$i],
										'satuan' => $sat[$i],
										'hargajual' => $harga[$i],
										'disc' => $disc[$i],
										'kodegudang' => $gudang,
										'ar_sidetail_id' => $idsiddetail[$i],
									);

									$data_inv = array(
										'tanggal'   => date('Y-m-d'),
										'kodeitem' => $kode[$i],
										'penerimaan' => $qty[$i],
										'pengeluaran' => 0,
										'satuan' => $sat[$i],
										'jenis' => 'Retur Penjualan',
										'tglrekam'   => date('Y-m-d H:i:s'),
										'hpp'   => $hpp->hargabeli,
										'hargajual'   => 0,
										'gudang' => $gudang,
										'no_batch' => $no_batch[$i],
										'tanggal_exp' => $tgl_exp[$i],
										'cabang' => $cabang,
									);

									if ($qty[$i] != "0" && $kode[$i] != "") {
										if ($param == 0) {
											$datad['koderetur'] = $nobukti;
											$builderDetail->insert($datad);
											$insertIdReturDetail = $builderDetail->db()->insertID();

											$data_inv['nobukti'] = $nobukti;
											$data_inv['nobukti_id'] = $insertIdReturDetail;
											$INVtransaksi->insert($data_inv);
											$kodeFK = $input['kodesi'];
											$DataCekPack = $db->query("SELECT * FROM ar_sifile a INNER JOIN ar_kirim b ON a.kodesd = b.kodekirim WHERE a.kodesi='$kodeFK' ")->getRow();
											if ($DataCekPack->kodepu != "" || $DataCekPack->kodepu != null) {
												$BarangPaket = $TableInvBarangPaket->getWhere(['kodeitem_barang' => $kode[$i], 'cabang' => $cabang])->getRow();
												if ($cabang == '02') {
													$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qty[$i]', qty_jual = qty_jual -'$qty[$i]' 
																where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'
																and gudang = '$gudang' and cabang = '$cabang'");

													$db->query("UPDATE ar_sidetail set qtysi = qtysi - '$qty[$i]' 
																where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' 
																and kodesi = '$kodesi' and gudang = '$gudang' and id ='$idsiddetail[$i]'");

													$db->query("UPDATE inv_barang set stok = stok + '$qty[$i]', stok_so = stok_so + '$qty[$i]' 
																where kodeitem = '$kode[$i]' and kdgudang = '$gudang' and kdcbg = '$cabang'");

													$db->query("UPDATE inv_barang_gudang set qty = qty + '$qty[$i]' 
																where gudang = '$gudang' and kodeitem = '$kode[$i]' and cabang = '$cabang'");

													if (!empty($BarangPaket)) {
														//Insert Mutasi Cabang
														$NomohonMMC = $model_Global->_Autonomor('MMC');
														$NomutasiMC = $model_Global->_Autonomor('MC');
														$NoAccAMC = $model_Global->_Autonomor('AMC');
														$DatamohonCabang = $TableMohonMutasiCabang->getWhere(['nomohon' => $NomohonMMC, 'cabang' => '01'])->getRow();

														if (empty($DatamohonCabang)) {
															$nobuktiMMC = $NomohonMMC;
														} else {
															$nobuktiMMC = $model_Global->_Autonomor('MMC');
														}

														$DatamutasiCabang = $TableMutasiCabang->getWhere(['nomutasi_cabang' => $NomutasiMC, 'cabang' => $cabang])->getRow();

														if (empty($DatamutasiCabang)) {
															$nobuktiMC = $NomutasiMC;
														} else {
															$nobuktiMC = $model_Global->_Autonomor('MC');
														}

														$DataAccmutasiCabang = $TableAccMutasiCabang->getWhere(['noacc_mutasi' => $NoAccAMC, 'cabang' => '01'])->getRow();

														if (empty($DataAccmutasiCabang)) {
															$nobuktiAMC = $NoAccAMC;
														} else {
															$nobuktiAMC = $model_Global->_Autonomor('AMC');
														}
														$DataGudangMinta     = $TableInvGudang->getWhere(['cabang' => '01'])->getRow();
														$DataGudangKirim   = $TableInvGudang->getWhere(['cabang' => $cabang])->getRow();
														//Insert Data MUTASI CABANG
														$data_headerMC = array(
															'nomutasi_cabang' => $nobuktiMC,
															'nomohon' => $nobuktiMMC,
															'kodesiretur' => $kodeFK,
															'cabang_asal' => $cabang,
															'cabang_tujuan' => '01',
															'gudang_asal' => $DataGudangKirim->kode,
															'gudang_tujuan' => $DataGudangMinta->kode,
															'keterangan' => 'Retur AUTO Barang Pack Packan',
															'tanggal' => date('Y-m-d'),
															'tglrekam' => date('Y-m-d H:i:s'),
															'userid' => $session->get('username'),
															'statusid' => 2,
															'cabang' => $cabang,
														);

														$rowmutasiCabangCek = $TableMutasiCabang->getWhere(['kodesiretur' => $kodeFK, 'cabang' => $cabang])->getRow();
														if (empty($rowmutasiCabangCek)) {
															$TableMutasiCabang->insert($data_headerMC);
															$model_Global->_updatecounter1('MC');
															$model_Global->_updatecounter1('MMC');
														}
														$rowmutasiCabang = $TableMutasiCabang->getWhere(['kodesiretur' => $kodeFK, 'cabang' => $cabang])->getRow();
														$datadMC = array(
															'nomutasi_cabang' => $rowmutasiCabang->nomutasi_cabang,
															'id_mutasi_cabang' => $rowmutasiCabang->id_mutasi_cabang,
															'kodeitem' => $kode[$i],
															'nobatch' => $no_batch[$i],
															'tglexp' => $tgl_exp[$i],
															'qty' => $qty[$i],
															'satuan' => $sat[$i],
															'harga' => $harga[$i],
															'cabang' => $cabang,
														);
														$TableMutasiCabangDetail->insert($datadMC);

														// INSERT DATA ACC MUTASI CABANG
														$data_headerAMC = array(
															'noacc_mutasi' => $nobuktiAMC,
															'nomutasi_cabang' => $nobuktiMC,
															'nomohon' => $nobuktiMMC,
															'cabang_terima' => '01',
															'cabang_kirim' => $cabang,
															'gudang_terima' => $DataGudangMinta->kode,
															'gudang_kirim' => $DataGudangKirim->kode,
															'keterangan' => 'Retur AUTO Barang Pack Packan',
															'tanggal' => date('Y-m-d'),
															'tglrekam' => date('Y-m-d H:i:s'),
															'userid' => $session->get('username'),
															'statusid' => '1',
															'cabang' => '01',
														);
														$rowACCMutasiCabangCek = $TableAccMutasiCabang->getWhere(['nomutasi_cabang' => $rowmutasiCabang->nomutasi_cabang, 'cabang' => '01'])->getRow();
														if (empty($rowACCMutasiCabangCek)) {
															$TableAccMutasiCabang->insert($data_headerAMC);
															$model_Global->_updatecounter1('AMC');
														}
														$rowACCmutasiCabang = $TableAccMutasiCabang->getWhere(['nomutasi_cabang' => $rowmutasiCabang->nomutasi_cabang, 'cabang' => '01'])->getRow();
														$barang = $builderINVBarang->where(['kodeitem' => $kode[$i], 'kdcbg' => '01'])->get()->getRow();

														$datadAMC = array(
															'noacc_mutasi' => $rowACCmutasiCabang->noacc_mutasi,
															'kodeitem' => $kode[$i],
															'nobatch' => $no_batch[$i],
															'tglexp' => $tgl_exp[$i],
															'qtyacc' => $qty[$i],
															'satuan' => $sat[$i],
															'harga' => $harga[$i],
															'cabang' => '01',
														);

														$inv_barang_gudang = array(
															'kodeitem' => $kode[$i],
															'gudang' => $DataGudangMinta->kode,
															'qty' => $qty[$i],
															'cabang' => '01',
														);

														$inv_nobatch = array(
															'no_batch' => $no_batch[$i],
															'kd_jenis' => 'Mutasi Cabang',
															'kodeitem' => $kode[$i],
															'qty_terima' => $qty[$i],
															'qty_saldo' => $qty[$i],
															'gudang' => $DataGudangMinta->kode,
															'cabang' => '01',
															'tanggal_exp' => $tgl_exp[$i],
														);

														$inv_barang = array(
															'kodeitem' => $kode[$i],
															'kdcbg' => $cabang,
															'namabarang' => $barang->namabarang,
															'hargabeli' => $barang->hargabeli,
															'hargajual' => $barang->hargajual,
															'tglbeliakhir' => $barang->tglbeliakhir,
															'stok' => $qty[$i],
															'stok_so' => $qty[$i],
															'satuan' => $barang->satuan,
															'kdgudang' => $DataGudangMinta->kode,
															'kdrak' => $barang->kdrak,
															'kdmerk' => $barang->kdmerk,
															'kode_user' => $session->get('username'),
															'ppn' => $barang->ppn,
															'kode_spl1' => $barang->kode_spl1,
															'bruto_spl1' => $barang->bruto_spl1,
															'disc_spl1' => $barang->disc_spl1,
															'netto_spl1' => $barang->netto_spl1,

															'kode_spl2' => $barang->kode_spl2,
															'bruto_spl2' => $barang->bruto_spl2,
															'disc_spl2' => $barang->disc_spl2,
															'netto_spl2' => $barang->netto_spl2,

															'kode_spl3' => $barang->kode_spl3,
															'bruto_spl3' => $barang->bruto_spl3,
															'disc_spl3' => $barang->disc_spl3,
															'netto_spl3' => $barang->netto_spl3,

															'kode_spl4' => $barang->kode_spl4,
															'bruto_spl4' => $barang->bruto_spl4,
															'disc_spl4' => $barang->disc_spl4,
															'netto_spl4' => $barang->netto_spl4,
														);

														$rowmutasi = $TableMutasiCabangDetail->where(['kodeitem' => $kode[$i], 'nobatch' => $no_batch[$i], 'nomutasi_cabang' => $rowmutasiCabang->nomutasi_cabang, 'cabang' =>  $cabang])->get()->getRow();

														$TableAccMutasiCabangDetail->insert($datadAMC);
														$TableMutasiCabangDetail->where(['kodeitem' => $kode[$i], 'nobatch' => $no_batch[$i], 'nomutasi_cabang' => $rowmutasiCabang->nomutasi_cabang, 'cabang' => $cabang])->update(['qtysetuju' => $rowmutasi->qtysetuju + $qty[$i]]);


														$cek_barang = $builderINVBarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => '01'])->get()->getRow();
														$GudangBarangKirim = $builderINVBarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => $cabang])->get()->getRow();

														$cek_nobatch = $INVBarangBatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => '01', 'gudang' => $DataGudangMinta->kode])->get()->getRow();
														$Batchkirim = $INVBarangBatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => $cabang, 'gudang' => $DataGudangKirim->kode])->get()->getRow();

														$cekBrgGudang = $TableInvBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $DataGudangMinta->kode, 'cabang' => '01'])->get()->getRow();
														$BarangGudangKirim = $TableInvBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $DataGudangKirim->kode, 'cabang' => $cabang])->get()->getRow();
														if ($BarangGudangKirim == null) {
															$qtyBarangGudang = 0;
														} else {
															$qtyBarangGudang = $BarangGudangKirim->qty;
														}

														//update dan insert inv_barang
														if (!empty($cek_barang)) {
															$builderINVBarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $DataGudangMinta->kode, 'kdcbg' => '01'])->update(['stok' => $cek_barang->stok + $qty[$i], 'stok_so' => $cek_barang->stok_so + $qty[$i]]);
															$builderINVBarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => $cabang])->update(['stok' => $GudangBarangKirim->stok -  $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so - $qty[$i]]);
														} else {
															$builderINVBarang->insert($inv_barang);
															$builderINVBarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $DataGudangKirim->kode, 'kdcbg' => $cabang])->update(['stok' => $GudangBarangKirim->stok - $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so + $qty[$i]]);
														}

														//update dan insert inv_nobatch
														if (!empty($cek_nobatch)) {
															$INVBarangBatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $DataGudangKirim->kode, 'cabang' => $cabang])->update(['qty_jual' => $Batchkirim->qty_jual + $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
															$INVBarangBatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $DataGudangMinta->kode, 'cabang' => '01'])->update(['qty_terima' => $cek_nobatch->qty_terima + $qty[$i], 'qty_saldo' => $cek_nobatch->qty_saldo + $qty[$i]]);
														} else {
															$INVBarangBatch->insert($inv_nobatch);
															$INVBarangBatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $DataGudangKirim->kode, 'cabang' => $cabang])->update(['qty_jual' => $Batchkirim->qty_jual - $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
														}

														//update dan insert inv_barang_gudang
														if (!empty($cekBrgGudang)) {
															$TableInvBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $DataGudangKirim->kode, 'cabang' => $cabang])->update(['qty' => $qtyBarangGudang -  $qty[$i]]);
															$TableInvBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $DataGudangMinta->kode, 'cabang' => '01'])->update(['qty' => $cekBrgGudang->qty + $qty[$i]]);
														} else {
															$TableInvBarangGudang->insert($inv_barang_gudang);
															$TableInvBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $DataGudangKirim->kode, 'cabang' => '01'])->update(['qty' => $qtyBarangGudang -  $qty[$i]]);
														}


														$DataInvKirim = array(
															'tanggal'   => date('Y-m-d'),
															'kodeitem' => $kode[$i],
															'penerimaan' => 0,
															'pengeluaran' => $qty[$i],
															'satuan' => $sat[$i],
															'jenis' => 'Mutasi Cabang',
															'nobukti' => $rowACCmutasiCabang->noacc_mutasi,
															'tglrekam'   => date('Y-m-d H:i:s'),
															'hpp'   => $barang->hargabeli,
															'hargajual'   => 0,
															'gudang' => $DataGudangKirim->kode,
															'no_batch' => $no_batch[$i],
															'tanggal_exp' => $tgl_exp[$i],
															'cabang' => $cabang,
														);
														$INVtransaksi->insert($DataInvKirim);

														$DataInvTerima = array(
															'tanggal'   => date('Y-m-d'),
															'kodeitem' => $kode[$i],
															'penerimaan' =>  $qty[$i],
															'pengeluaran' => 0,
															'satuan' => $sat[$i],
															'jenis' => 'Mutasi Cabang',
															'nobukti' => $rowACCmutasiCabang->noacc_mutasi,
															'tglrekam'   => date('Y-m-d H:i:s'),
															'hpp'   => $barang->hargabeli,
															'hargajual'   => 0,
															'gudang' => $DataGudangMinta->kode,
															'no_batch' => $no_batch[$i],
															'tanggal_exp' => $tgl_exp[$i],
															'cabang' => '01',
														);
														$INVtransaksi->insert($DataInvTerima);


														// Insert Detail Retur Pembelian
														$datadBeli = array(
															'kodeitem' => $kode[$i],
															'qtyretur' => $qty[$i],
															'satuan' => $sat[$i],
															'no_batch' => $no_batch[$i],
															'tanggal_exp' => $tgl_exp[$i],
															'hargabeli' => $barang->hargabeli,
															'disc' => 100,
															'kodegudang' => $DataGudangMinta->kode,
														);

														$data_invBeli = array(
															'tanggal'   => date('Y-m-d'),
															'kodeitem' => $kode[$i],
															'penerimaan' => 0,
															'pengeluaran' => $qty[$i],
															'satuan' => $sat[$i],
															'jenis' => 'Retur Pembelian',
															'nobukti' => $nobuktiReturBeli,
															'tglrekam'   => date('Y-m-d H:i:s'),
															'hpp'   => $barang->hargabeli,
															'hargajual'   => 0,
															'gudang' => $DataGudangMinta->kode,
															'no_batch' => $no_batch[$i],
															'tanggal_exp' => $tgl_exp[$i],
															'cabang' => '01',
														);

														if ($kode[$i] != "") {
															$datadBeli['koderetur'] = $nobuktiReturBeli;
															$buildReturPembelianDetail->insert($datadBeli);
															$INVtransaksi->insert($data_invBeli);
															$db->query("update inv_nobatch set qty_terima = qty_terima - '$qty[$i]', qty_saldo = qty_saldo - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and cabang='01' and gudang='$DataGudangMinta->kode'");

															$db->query("update ap_pudetail set qtypu = qtypu - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and kodepu = '$DataArKirim->kodepu' and gudang='$DataGudangMinta->kode'");

															$db->query("update inv_barang_supplier set qty_masuk = qty_masuk - '$qty[$i]', stok = stok - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'");

															$db->query("update inv_barang set stok = stok - '$qty[$i]', stok_so = stok_so - '$qty[$i]' where kodeitem = '$kode[$i]' and kdcbg='01' and kdgudang='$DataGudangMinta->kode'");

															$db->query("update inv_barang_gudang set qty = qty - '$qty[$i]' where gudang = '$DataGudangMinta->kode' and kodeitem = '$kode[$i]' and cabang='01' ");
														}
													}
												} else {
													$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qty[$i]', qty_jual = qty_jual -'$qty[$i]' 
																where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'
																and gudang = '$gudang' and cabang = '$cabang'");

													$db->query("UPDATE ar_sidetail set qtysi = qtysi - '$qty[$i]' 
																where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' 
																and kodesi = '$kodesi' and gudang = '$gudang' and id ='$idsiddetail[$i]'");

													$db->query("UPDATE inv_barang set stok = stok + '$qty[$i]', stok_so = stok_so + '$qty[$i]' 
																where kodeitem = '$kode[$i]' and kdgudang = '$gudang' and kdcbg = '$cabang'");

													$db->query("UPDATE inv_barang_gudang set qty = qty + '$qty[$i]' 
																where gudang = '$gudang' and kodeitem = '$kode[$i]' and cabang = '$cabang'");

													// Insert Detail Retur Pembelian
													$barang = $builderINVBarang->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->get()->getRow();
													$DataGudangMinta     = $TableInvGudang->getWhere(['cabang' => $cabang])->getRow();
													$datadBeli = array(
														'kodeitem' => $kode[$i],
														'qtyretur' => $qty[$i],
														'satuan' => $sat[$i],
														'no_batch' => $no_batch[$i],
														'tanggal_exp' => $tgl_exp[$i],
														'hargabeli' => $barang->hargabeli,
														'disc' => 100,
														'kodegudang' => $DataGudangMinta->kode,
													);

													$data_invBeli = array(
														'tanggal'   => date('Y-m-d'),
														'kodeitem' => $kode[$i],
														'penerimaan' => 0,
														'pengeluaran' => $qty[$i],
														'satuan' => $sat[$i],
														'jenis' => 'Retur Pembelian',
														'nobukti' => $nobuktiReturBeli,
														'tglrekam'   => date('Y-m-d H:i:s'),
														'hpp'   => $barang->hargabeli,
														'hargajual'   => 0,
														'gudang' => $DataGudangMinta->kode,
														'no_batch' => $no_batch[$i],
														'tanggal_exp' => $tgl_exp[$i],
														'cabang' => '01',
													);

													if ($kode[$i] != "") {
														if (!empty($BarangPaket)) {
															$datadBeli['koderetur'] = $nobuktiReturBeli;
															$buildReturPembelianDetail->insert($datadBeli);
															$INVtransaksi->insert($data_invBeli);

															$db->query("update inv_nobatch set qty_terima = qty_terima - '$qty[$i]', qty_saldo = qty_saldo - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and cabang='01' and gudang='$DataGudangMinta->kode'");

															$db->query("update ap_pudetail set qtypu = qtypu - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and kodepu = '$DataArKirim->kodepu' and gudang='$DataGudangMinta->kode'");

															$db->query("update inv_barang_supplier set qty_masuk = qty_masuk - '$qty[$i]', stok = stok - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'");

															$db->query("update inv_barang set stok = stok - '$qty[$i]', stok_so = stok_so - '$qty[$i]' where kodeitem = '$kode[$i]' and kdcbg='01' and kdgudang='$DataGudangMinta->kode'");

															$db->query("update inv_barang_gudang set qty = qty - '$qty[$i]' where gudang = '$DataGudangMinta->kode' and kodeitem = '$kode[$i]' and cabang='01' ");
														}
													}
												}
											} else {
												$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qty[$i]', qty_jual = qty_jual -'$qty[$i]' 
												where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'
												and gudang = '$gudang' and cabang = '$cabang'");

												$db->query("UPDATE ar_sidetail set qtysi = qtysi - '$qty[$i]' 
												where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' 
												and kodesi = '$kodesi' and gudang = '$gudang'");

												$db->query("UPDATE inv_barang set stok = stok + '$qty[$i]', stok_so = stok_so + '$qty[$i]' 
												where kodeitem = '$kode[$i]' and kdgudang = '$gudang' and kdcbg = '$cabang'");

												$db->query("UPDATE inv_barang_gudang set qty = qty + '$qty[$i]' 
												where gudang = '$gudang' and kodeitem = '$kode[$i]' and cabang = '$cabang'");
											}
										} else {
											$datad['koderetur'] = $input['nomorbukti'];
											$builderDetail->insert($datad);

											$data_inv['nobukti'] = $input['nomorbukti'];
											$INVtransaksi->insert($data_inv);

											$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qty[$i]', qty_jual = qty_jual -'$qty[$i]' 
														where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'
														and gudang = '$gudang' and cabang = '$cabang'");

											$db->query("UPDATE ar_sidetail set qtysi = qtysi - '$qty[$i]' 
														where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' 
														and kodesi = '$kodesi' and gudang = '$gudang'");

											$db->query("UPDATE inv_barang set stok = stok + '$qty[$i]', stok_so = stok_so + '$qty[$i]' 
                                   		 				where kodeitem = '$kode[$i]' and kdgudang = '$gudang' and kdcbg = '$cabang'");

											$db->query("UPDATE inv_barang_gudang set qty = qty + '$qty[$i]' 
                                    					where gudang = '$gudang' and kodeitem = '$kode[$i]' and cabang = '$cabang'");
										}

										$DateNow = date('Y-m-d');
										$oneHundredTwentyDaysAgoPusat = date('Y-m-d', strtotime('-120 days'));
										$oneHundredsixtyDaysAgoPusat = date('Y-m-d', strtotime('-160 days'));
										$sixtyDaysAgoCabang = date('Y-m-d', strtotime('-30 days'));
										$ninetyDaysAgoCabang = date('Y-m-d', strtotime('-60 days'));
										$GetQtyKeluarTwentyDaysAgo = $db->table('inv_transaksi');
																	$GetQtyKeluarTwentyDaysAgo->select('SUM(pengeluaran) as total');
																	$GetQtyKeluarTwentyDaysAgo->where('kodeitem', $kode[$i]);
																	$GetQtyKeluarTwentyDaysAgo->where('cabang', $cabang);
																	if($cabang == '01'){
																		$GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $oneHundredTwentyDaysAgoPusat);
																	} else {
																		$GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $sixtyDaysAgoCabang);
																	}
																	$GetQtyKeluarTwentyDaysAgo->where('tanggal <=', $DateNow);
										$ResultTwentysDayAgo = $GetQtyKeluarTwentyDaysAgo->get()->getRow();

										$GetQtyKeluarSixtyDaysAgo = $db->table('inv_transaksi');
																	$GetQtyKeluarSixtyDaysAgo->select('SUM(pengeluaran) as total');
																	$GetQtyKeluarSixtyDaysAgo->where('kodeitem', $kode[$i]);
																	$GetQtyKeluarSixtyDaysAgo->where('cabang', $cabang);
																	if($cabang == '01'){
																		$GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $oneHundredsixtyDaysAgoPusat);
																	} else {
																		$GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $ninetyDaysAgoCabang);
																	}
																	$GetQtyKeluarSixtyDaysAgo->where('tanggal <=', $DateNow);
										$ResultSixtyDayAgo = $GetQtyKeluarSixtyDaysAgo->get()->getRow();
										
										$buildPaket 		= $db->table('inv_barang_paket');
										$DataPaket 		= $buildPaket->getWhere(['kodeitem_barang' => $kode[$i], 'cabang' => $cabang])->getRow();
										if(empty($DataPaket)){
											$db->table("inv_barang")
											->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $gudang])
											->update([
													'stok_min' => $ResultTwentysDayAgo->total,
													'stok_max' => $ResultSixtyDayAgo->total,
													]);
										}
									}
								}

								//TABLE 2
								$buildSIBiaya       = $db->table("ar_sibiaya");
								$buildSifile        = $db->table('ar_sifile');
								$buildterimaDetail  = $db->table('ar_penerimaandetil');
								if ($input['sppn'] == "Y") {
									$tppn = ($tot - $tdisc) * $taxes;
								} else {
									$tppn = 0;
								}

								$kodebarangpaket  		= $input['kodebarangpaket'];
								if ($kodebarangpaket != null || $kodebarangpaket != '') {
									$jumdata3  = count($kodebarangpaket);
									for ($i = 0; $i < $jumdata3; $i++) {
										$qtypaket = $input['qtypaket'][$i];
										$kodeitem_paket = $input['kodebarangdetail'][$i];
										$nobatchPaket = $input['no_batchpaket'][$i];
										$DataInvBarangPaket = $builderINVBarang->getWhere(['kodeitem' => $kodeitem_paket, 'kdcbg' => $cabang])->getRow();

										$datapaket = array(
											'kodeitem_paket' => $kodeitem_paket,
											'nobatch_paket' => $nobatchPaket,
											'qtyretur_paket' => $qtypaket,
											'satuan' => $input['satpaket'][$i],
											'kodeitem_barang' => $kodebarangpaket[$i],
											'cabang'  => $cabang,
											'id_kodeitembarang' => $input['idkodeitembarang'][$i],
										);

										$data_invTransaksi = array(
											'tanggal'   => date('Y-m-d'),
											'kodeitem' =>  $kodeitem_paket,
											'penerimaan' => $qtypaket,
											'pengeluaran' => 0,
											'satuan' => $input['satpaket'][$i],
											'jenis' => 'Retur Penjualan',
											'tglrekam'   => date('Y-m-d H:i:s'),
											'hpp'   => $DataInvBarangPaket->hargabeli,
											'hargajual'   => 0,
											'gudang' => $gudang,
											'no_batch' => $nobatchPaket,
											'tanggal_exp' => '',
											'cabang' => $cabang,
										);

										if ($qtypaket != "0" && $kodeitem_paket != "") {
											if ($param == 0) {
												$datapaket['koderetur'] = $nobukti;
												$builderPaket->insert($datapaket);

												$data_invTransaksi['nobukti'] = $nobukti;
												$INVtransaksi->insert($data_invTransaksi);

												$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qtypaket', qty_jual = qty_jual -'$qtypaket' 
															where kodeitem = '$kodeitem_paket' and no_batch = '$nobatchPaket'
															and gudang = '$gudang' and cabang = '$cabang'");

												$db->query("UPDATE ar_sipaket set qtyterima_paket = qtyterima_paket - '$qtypaket' 
															where kodeitem_paket = '$kodeitem_paket' and nobatch_paket = '$nobatchPaket' 
															and kodesi = '$kodesi'");

												$db->query("UPDATE inv_barang set stok = stok + '$qtypaket', stok_so = stok_so + '$qtypaket' 
                                    					where kodeitem = '$kodeitem_paket' and kdgudang = '$gudang' and kdcbg = '$cabang'");

												$db->query("UPDATE inv_barang_gudang set qty = qty + '$qtypaket' 
                                   							 where gudang = '$gudang' and kodeitem = '$kodeitem_paket' and cabang = '$cabang'");
											} else {
												// $datapaket['koderetur'] = $input['nomorbukti'];
												// $builderPaket->insert($datapaket);

												// $data_invTransaksi['nobukti'] = $input['nomorbukti'];
												// $INVtransaksi->insert($data_invTransaksi);

												// $db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo + '$qtypaket', qty_jual = qty_jual -'$qtypaket' 
												// 		where kodeitem = '$kodeitem_paket' and no_batch = '$nobatchPaket'
												// 		and gudang = '$gudang' and cabang = '$cabang'");

												// $db->query("UPDATE ar_sidetail set qtysi = qtysi - '$qtypaket' 
												// 		where kodeitem = '$kode[$i]' and no_batch = '$nobatchPaket' 
												// 		and kodesi = '$kodesi' and gudang = '$gudang'");

												// $db->query("UPDATE inv_barang set stok = stok + '$qty[$i]', stok_so = stok_so + '$qtypaket' 
												// 		kode[$i]' and kdgudang = '$gudang' and kdcbg = '$cabang'");

												// $db->query("UPDATE inv_barang_gudang set qty = qty + '$qtypaket' 
												// 		udang = '$gudang' and kodeitem = '$kode[$i]' and cabang = '$cabang'");
											}
										}
									}
								}

								$data = array(
									'kodecbg' => $cabang,
									'kodecust' => $input['cust'],
									'kodesi'  => $input['kodesi'],
									'tglretur'   => date('Y-m-d', strtotime($input['tanggal'])),
									'kodeuser' => $session->get('username'),
									'keterangan' => $input['keterangan'],
									'totalretur' => $_vvtotal,
									'dpp'     => $_vvdpp,
									'ppn'     => $_vvppn,
									'diskon'  => $_vvdiskon,
									'sppn'    => $input['sppn'],
									'statusid' => 1,
									'gudang'  => $gudang,
								);

								$dataHeaderReturBeli = array(
									'kodecbg' => '01',
									'kodesup' => 'SP.000103',
									'kodepu'  => $DataArKirim->kodepu,
									'tglretur'   => date('Y-m-d', strtotime($input['tanggal'])),
									'kodeuser' => $session->get('username'),
									'keterangan' => 'Retur Auto Barang Pack Packan',
									'totalretur' => 0,
									'dpp'     => 0,
									'ppn'     => 0,
									'diskon'  => 0,
									'sppn'    => 'T',
									'statusid' => 1,
									'gudang' => 1,
								);

								if ($param != 0) {
									$buildSIBiaya->delete(['kodesi' => $input['kodesi'], 'cabang' => $cabang]);
									$model_Global->_hapusjurnal($input['nomorbukti'], 'JJ');
								}

								$profile = $model_Global->_LoadProfile();
								$akun_debet     = $profile->akun_retjual;
								$akun_kredit    = $profile->akun_persediaan;
								$akun_penjualan = $profile->akun_penjualan;
								$akun_kas       = $profile->akun_kas;
								$akun_piutang   = $profile->akun_piutang;
								$akun_ppn       = $profile->akun_ppn;
								$akun_hpp       = $profile->akun_hpp;
								$akun_persediaan = $profile->akun_persediaan;
								$akun_diskon    = $profile->akun_diskonjual;

								$total = $_vvtotal;
								if ($total != '0' || $total != '0.00') {
									$model_Global->_rekamjurnal(
										date('Y-m-d', strtotime($input['tanggal'])),
										$nobukti,
										'JJ',
										$input['kodesi'],
										1,
										$akun_debet,
										'Retur Penjualan ke ' . $DataCs->nama,
										'Retur Penjualan ke ' . $DataCs->nama,
										$total,
										0,
										$cabang
									);

									$model_Global->_rekamjurnal(
										date('Y-m-d', strtotime($input['tanggal'])),
										$nobukti,
										'JJ',
										$input['kodesi'],
										2,
										$akun_kredit,
										'Retur Penjualan ke ' . $DataCs->nama,
										'Retur Penjualan ke ' . $DataCs->nama,
										0,
										$total,
										$cabang
									);
								}

								$data_faktur = $buildSifile->getWhere(['kodesi' => $input['kodesi'], 'kodecbg' => $cabang])->getRow();
								$data_bayar = $buildterimaDetail->getWhere(['nomorfaktur' => $input['kodesi'], 'cabang' => $cabang])->getRow();
								$datalogs = [
									'date' => date('Y-m-d H:i:s'),
									'userid' => $session->get('username'),
								];

								if ($param == 0) {
									$data['koderetur'] = $nobukti;
									$builder->insert($data);
									$dataHeaderReturBeli['koderetur'] = $nobuktiReturBeli;
									$buildReturPembelian->insert($dataHeaderReturBeli);
									$model_Global->_updatecounter1('PR');
									if ($data_faktur->jumlahbayar == 0) {
										$test = $db->query("UPDATE ar_sifile 
										set retur = 2, dpp = dpp -'$tot', ppn = ppn -'$tppn', totalsi = totalsi -'$total' 
										where kodesi = '$kodesi' and kodecbg ='$cabang'");
									} else {
										$db->query("UPDATE ar_sifile 
										set retur = 2, dpp = dpp -'$tot', ppn = ppn -'$tppn', jumlahbayar = jumlahbayar -'$total', totalsi = totalsi -'$total' 
										where kodesi = '$kodesi' and kodecbg ='$cabang'");
									}

									if ($data_bayar != "") {
										$db->query("UPDATE ar_penerimaandetil 
                                    set totalfaktur = totalfaktur -'$total', terhutang = terhutang -'$total', bayar = bayar -'$total'
                                    where nomorfaktur = '$kodesi' and cabang='$cabang'");

										$db->query("UPDATE ar_penerimaan 
                                    set jumlahbayar = jumlahbayar -'$total' 
                                    where kodebyr = '$data_bayar->kodebyr' and kodecbg='$cabang'");

										$DataTerima = $TrTerima->getWhere(['terima_kodebyr' => $data_bayar->kodebyr, ' terima_kodecbg' => $cabang])->getRow();

										$db->query("update tr_penerimaand set terimad_jumlah= terimad_jumlah -'$total' where terimad_nomor = '$DataTerima->terima_nomor'");

										$db->query("update tr_transaksikasbank set pemasukan = pemasukan -'$total' where novoucher = '$DataTerima->terima_nomor' and cabang='$cabang'");
										$db->query("update tr_jurnal set debet = debet -'$total' where kredit='0.00' and novoucher = '$DataTerima->terima_nomor' and kodecbg='$cabang'");
										$db->query("update tr_jurnal set kredit = kredit -'$total' where debet='0.00' and novoucher = '$DataTerima->terima_nomor' and kodecbg='$cabang'");
									}

									if ($total != '0' || $total != '0.00') {
										if ($_vvsubtotal != "") {
											$db->query("UPDATE tr_jurnal 
                                    set debet = debet -'$total' 
                                    where kredit='0.00' and novoucher = '$kodesi' and kodeakun='$akun_piutang' and kodecbg='$cabang'");

											$db->query("UPDATE tr_jurnal 
                                    set kredit = kredit -'$_vvsubtotal' 
                                    where debet='0.00' and novoucher = '$kodesi' and kodeakun='$akun_penjualan' and kodecbg='$cabang'");
										}

										if ($_vvdiskon != "") {
											$db->query("UPDATE tr_jurnal 
                                    set debet = debet -'$_vvdiskon' 
                                    where kredit='0.00' and novoucher = '$kodesi' and kodeakun='$akun_diskon' and kodecbg='$cabang'");
										}

										if ($_vvppn != "") {
											$db->query("UPDATE tr_jurnal 
                                    set kredit = kredit -'$_vvppn' 
                                    where debet='0.00' and novoucher = '$kodesi' and kodeakun='$akun_ppn' and kodecbg='$cabang'");
										}

										//Update jurnal persediaan
										$db->query("UPDATE tr_jurnal 
										set debet = debet -'$tothpp' 
										where kredit='0.00' and novoucher = '$kodesi' and kodeakun='$akun_hpp' and kodecbg='$cabang'");

										$db->query("UPDATE tr_jurnal 
										set kredit = kredit -'$tothpp' 
										where debet='0.00' and novoucher = '$kodesi' and kodeakun='$akun_persediaan' and kodecbg='$cabang'");
									}
									if ($data_faktur->pembayaran == 'K') {
										$db->query("UPDATE ar_customer set saldokredit = saldokredit + '$total' where kode = '" . $input['cust'] . "'");
									}

									$model_Global->_updatecounter1('SR');
									$datalogs['detail'] = 'Tambah data Penjualan Retur dengan kode ' . $nobukti;
									$db->table("logs")->insert($datalogs);
									echo json_encode(['status' => 1, 'message' => $nobukti]);
								} else {
									$builder->where(['koderetur' => $input['nomorbukti'], 'kodecbg' => $cabang])->update($data);
									$datalogs['detail'] = 'Edit data Penjualan Retur dengan kode ' . $input['nomorbukti'];
									$db->table("logs")->insert($datalogs);
									echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
								}
								$db->transComplete();
								if ($db->transStatus() === false) {
									// Jika terjadi kesalahan, batalkan transaksi
									$db->transRollback();
									echo json_encode(['status' => 2, 'message' => 'Transaksi Gagal Mohon Coba lagi']);
								}
								// Transaksi sukses, commit perubahan ke database
								$db->transCommit();
							} else {
								echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
							}
						} catch (\Exception $e) {
							echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
						}
					}

					public function cetak_retur($param)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$alamat1  = $profile->alamat1;
							$alamat2  = $profile->alamat2;
							$hp 	  = $profile->hp;
							$email 	  = $profile->email;
							$taxes 		= $profile->taxes_desimal;
							$taxespersen = $profile->taxes_persen;

							$queryh = "select ar_retur.*, ar_customer.*, ar_sifile.kodeso, ar_sifile.faktur_pajak, ar_sifile.nama_sales from ar_retur inner join ar_customer on ar_retur.kodecust=ar_customer.kode INNER JOIN ar_sifile ON ar_sifile.kodesi = ar_retur.kodesi where koderetur = '$param'";
							$queryd = "select inv_barang.namabarang, ar_returdetil.* from ar_returdetil inner join inv_barang on ar_returdetil.kodeitem=inv_barang.kodeitem where ar_returdetil.koderetur = '$param' and inv_barang.kdcbg='$unit'";

							$detil  = $db->query($queryd)->getResult();
							$header = $db->query($queryh)->getRow();
							$pdf = new simkeu_nota_helper();
							$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
							$pdf->setjudul('');
							$pdf->setsubjudul('');
							$pdf->addpage("P", "A4");
							$pdf->setsize("P", "A4");
							$pdf->SetWidths(array(70, 30, 90));
							$border = array('TB', '', 'BT');
							$size   = array('0', '0', '0');
							$pdf->setfont('Arial', 'B', 18);
							$pdf->SetAligns(array('C', 'C', 'C'));
							$align = array('L', 'C', 'L');
							$style = array('B', '', 'B');
							$size  = array('15', '0', '18');
							$max   = array(5, 5, 20);
							$judul = array('Dari :', '', 'Retur Penjualan');
							$fc     = array('0', '0', '0');
							$pdf->image('./assets/images/logo_paper.jpg', 50, 60, 100, 100);
							$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
							$pdf->ln(1);
							$pdf->setfont('Arial', 'B', 10);
							$pdf->SetWidths(array(70, 30, 30, 5, 55));
							$border = array('', '', '', '', '');
							$fc     = array('0', '0', '0', '0', '0');
							$pdf->SetFillColor(230, 230, 230);
							$pdf->setfont('Arial', '', 9);
							$pdf->FancyRow(array($header->nama, '', 'No. Retur', ':', $header->koderetur), $fc, $border);
							$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglretur))), $fc, $border);
							$pdf->FancyRow(array($header->alamat2, '', 'Pengembalian', ':', $header->kodesi), $fc, $border);
							$pdf->FancyRow(array($header->telp, '', 'No.PO', ':', $header->kodeso), $fc, $border);
							$pdf->FancyRow(array('Marketing  : ' . $header->nama_sales, '', 'Faktur Pajak', ':', $header->faktur_pajak), $fc, $border);
							$pdf->ln(2);

							$pdf->SetWidths(array(20, 46, 18, 20, 14, 14, 18, 15, 25));
							$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
							$align  = array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R');
							$pdf->setfont('Arial', 'B', 9);
							$pdf->SetAligns(array('L', 'C', 'R'));
							$fc = array('0', '0', '0', '0', '0', '0', '0', '0', '0');
							$judul = array('Kode', 'Nama Barang', 'Batch', 'Expired', 'Qty', 'Satuan', 'Harga', 'Disc %', 'Total Harga');
							$pdf->FancyRow2(8, $judul, $fc, $border, $align);
							$border = array('', '', '');
							$pdf->setfont('Arial', '', 9);
							$tot = 0;
							$subtot = 0;
							$tdisc  = 0;
							$border = array('', '', '', '', '', '', '', '', '');
							$align  = array('L', 'L', 'L', 'L', 'R', 'C', 'R', 'C', 'R');
							$fc = array('0', '0', '0', '0', '0', '0', '0', '0', '0');
							$pdf->SetFillColor(0, 0, 139);
							$pdf->settextcolor(0);
							foreach ($detil as $db) {
								$dpp = $db->qtyretur * $db->hargajual;
								$dis = ($db->disc / 100) * $dpp;
								$jum = $dpp - $dis;
								$tot = $tot + $jum;

								$subtot = $subtot + $dpp;
								$tdisc  = $tdisc + $dis;

								$pdf->FancyRow(array(
									$db->kodeitem,
									$db->namabarang,
									$db->no_batch,
									$db->tanggal_exp,
									$db->qtyretur,
									$db->satuan,
									number_format($db->hargajual, 0, '.', ','),
									$db->disc,
									number_format($jum, 0, '.', ',')
								), $fc, $border, $align);
							}


							if ($header->sppn == "Y") {
								$ppn = ($subtot - $tdisc) * $taxes;
							} else {
								$ppn = 0;
							}
							$tot = $tot + $ppn;
							$pdf->SetFillColor(230);
							$border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
							$pdf->FancyRow(array('', '', '', '', '', '', '', '', ''), 0, $border);
							$pdf->ln(2);
							$pdf->SetWidths(array(100, 20, 30, 40));
							$border = array('TB', '', 'T', 'T');
							$align  = array('L', '', 'L', 'R');
							$fc = array('0', '0', '0', '0');
							$pdf->FancyRow(array('Keterangan', '', 'Sub Total', number_format($subtot, 0, '.', ',')), $fc, $border, $align, 0);
							$border = array('', '', '', '');
							$pdf->FancyRow(array($header->keterangan, '', 'Diskon', number_format($tdisc, 0, '.', ',')), $fc, $border, $align);
							$pdf->FancyRow(array('', '', 'DPP', number_format($subtot - $tdisc, 0, '.', ',')), $fc, $border, $align);
							$pdf->FancyRow(array('', '', 'PPN (' . $taxespersen . '%)', number_format($ppn, 0, '.', ',')), $fc, $border, $align);
							$style = array('', '', 'B', 'B');
							$size  = array('0', '0', '0', '0');
							$border = array('T', '', 'BT', 'BT');
							$fc = array('0', '0', '0', '0');
							$pdf->FancyRow(array('', '', 'Total', number_format($tot, 0, '.', ',')), $fc, $border, $align, $style, $size);
							$pdf->settextcolor(0);
							$pdf->ln(5);

							$border = array('', '', '', '');
							$pdf->settextcolor(0);
							$pdf->SetWidths(array(20, 3, 150));
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetAligns(array('C', 'C'));
							$fc = array('0', '0', '0', '0');
							$terbilang = ucwords($model_Global->terbilang($tot));
							$pdf->FancyRow(array('TEBILANG', ':', $terbilang, '', ''), $fc, $border);

							$pdf->settextcolor(0);
							$pdf->SetWidths(array(50, 50, 48, 50));
							$pdf->SetFont('Arial', '', 9);
							$pdf->SetAligns(array('C', 'C', 'C', 'C'));
							$pdf->ln(5);

							$border = array('', '', '', '');
							$align  = array('C', 'C', 'C', 'C');
							$pdf->FancyRow(array('Yang Meretur', 'Diterima Oleh', 'Gudang', 'Accounting'), 0, $border, $align);
							$pdf->ln(1);
							$pdf->ln(15);
							$pdf->SetWidths(array(47, 2, 47, 2, 47, 2, 46));
							$pdf->SetFont('Arial', '', 8);
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
							$border = array('B', '', 'B', '', 'B', '', 'B');
							$pdf->FancyRow(array('', '', '', '', '', '', ''), 0, $border, $align);
							$border = array('', '', '', '', '', '', '');
							$align  = array('L', 'C', 'L');
							$pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.', '', 'Tgl.'), 0, $border, $align);

							$pdf->AliasNbPages();
							$pdf->output($param . '.PDF', 'I');
							exit;
						} else {

							header('location:' . base_url());
						}
					}

					//Laporan Page Start Here
					public function laporan()
					{
						$session = session();
						$db = db_connect();
						$cabang = $db->table("ms_unit")->get()->getResult();
						$DataCust = $db->table('ar_customer')->get()->getResult();
						$barang = $db->table("inv_barang")->getWhere(['kdcbg' => $session->get('cabang')])->getResult();
						$DataSales = $db->table('ar_sales')->get()->getResult();
						$css = [];
						$js = [];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Laporan',
							'body' => 'admin/penjualan/laporan',
							'cbg' => $cabang,
							'cust' => $DataCust,
							'barang' => $barang,
							'sales' => $DataSales,
						];
						return view('layout/layout', $data);
					}

					public function CetakLaporan($x)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek  = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$taxes 		= $profile->taxes_desimal;
							$motto = '';
							$alamat = '';
							$namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
							$data  	= explode("~", $x);
							$idlp  	= $data[0];
							$tgl1  	= $data[1];
							$tgl2  	= $data[2];
							$bln  	= $data[3];
							$thn  	= $data[4];
							$cust  	= $data[5];
							$brg   	= $data[6];
							$cab   	= $data[7];
							$marketing 	= $data[8];

							$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
							$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
							$_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
							$_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
							$_peri2 = 'Periode. ' . $model_Global->_namabulan($bln) . ' ' . $thn;
							$tgl_ppn11 = date('2022-04-01');
							$tgl_ppn12 = date('2025-01-01');

							if ($_tgl1 >= $tgl_ppn12) {
								$h_ppn = $taxes;
							} else if ($_tgl1 >= $tgl_ppn11) {
								$h_ppn = 0.11;
							} else {
								$h_ppn = 0.1;
							}
							// Daftar Pesanan Penjualan
							if ($idlp == 101) {
								$query =
									"select ar_sofile.*, ar_customer.nama as namapelanggan from ar_sofile inner join ar_customer on ar_sofile.kodecust=ar_customer.kode 
			 						where tglso between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and ar_sofile.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_sofile.kodecbg = '$cab'";
								}
								$query .= " order by tglso, kodeso";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Daftar Pesanan Penjualan');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->SetWidths(array(10, 30, 20, 90, 35));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Nomor ', 'Tanggal', 'Pelanggan', 'Total');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 30, 20, 90, 35));
								$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								foreach ($lap as $row) {
									$pdf->row(array($nourut, $row->kodeso, date('d-m-Y', strtotime($row->tglso)), $row->namapelanggan, number_format($row->totalso, 0, '.', ',')));
									$nourut++;
								}
								$pdf->SetTitle('LAPORAN PESANAN PENJUALAN');
								$pdf->AliasNbPages();
								$pdf->output('Penjualan_101.PDF', 'I');
								exit;
							}
							// Daftar Pengiriman Barang
							else if ($idlp == 102) {
								$query =
									"select ar_kirim.*, ar_customer.nama as namapelanggan from ar_kirim inner join ar_customer on ar_kirim.kodecust=ar_customer.kode 
			 						where tglkirim between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and ar_kirim.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_kirim.kodecbg = '$cab'";
								}
								$query .= " order by tglkirim, kodeso";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Daftar Pengiriman Barang');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->SetWidths(array(10, 30, 20, 90, 35));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Nomor ', 'Tanggal', 'Pelanggan', 'Pengiriman');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 30, 20, 90, 35));
								$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								foreach ($lap as $row) {
									$pdf->row(array($nourut, $row->kodekirim, date('d-m-Y', strtotime($row->tglkirim)), $row->namapelanggan, ''));
									$nourut++;
								}

								$pdf->SetTitle('LAPORAN PENGIRIMAN BARANG');
								$pdf->AliasNbPages();
								$pdf->output('Penjualan_102.PDF', 'I');
								exit;
							}
							//LAPORAN PENJUALAN GLOBAL
							else if ($idlp == 103) {
								$query =
									"select * from ar_sifile where tglkirim between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and kodecbg = '$cab'";
								}
								if ($marketing != "") {
									$query .= " and id_sales = '$marketing'";
								}
								$query .= " group by kodesi";


								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('LAPORAN PENJUALAN GLOBAL');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->SetWidths(array(10, 30, 20, 50, 25, 20, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Nomor ', 'Tanggal', 'Pelanggan', 'Bruto', 'Disc', 'Netto', 'PPN', 'DPP+PPN', 'Marketing', 'Kurir');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->setheadertable($judul);
								$pdf->SetWidths(array(10, 30, 20, 50, 25, 20, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;
								$totbruto = 0;
								$totnetto = 0;
								$totdisc  = 0;
								$tothrg  = 0;
								$totppn  = 0;

								foreach ($lap as $value) {
									$NameCs = $db->table('ar_customer')->getWhere(['kode' => $value->kodecust])->getRow()->nama;
									$subtot = $value->dpp;
									$disc = $value->diskon;

									$tdpp = $subtot - $disc;
									if ($value->sppn == "Y") {
										$ppn = $tdpp * $h_ppn;
									} else {
										$ppn = 0;
									}

									$dpp_ppn = $tdpp + $ppn;

									if ($tdpp >= 1) {

										$pdf->row(array(
											$nourut,
											$value->kodesi,
											date('d-m-Y', strtotime($value->tglsi)),
											$NameCs,
											number_format($subtot, 0, '.', ','),
											number_format($disc, 0, '.', ','),
											number_format($tdpp, 0, '.', ','),
											number_format($ppn, 0, '.', ','),
											number_format($dpp_ppn, 0, '.', ','),
											$value->nama_sales,
											$value->kurir
										));
										$nourut++;
										$totbruto 	= $totbruto + $subtot;
										$totnetto 	= $totnetto + $tdpp;
										$totppn 	= $totppn + $ppn;
										$totdisc  	= $totdisc + $disc;
										$tothrg  	= $tothrg + $dpp_ppn;
									}
								}
								$pdf->SetWidths(array(110, 25, 20, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));

								$pdf->setfont('Times', 'B', 9);
								$pdf->Row(array(
									'Total  ',
									number_format($totbruto, 0, ',', '.'),
									number_format($totdisc, 0, ',', '.'),
									number_format($totnetto, 0, ',', '.'),
									number_format($totppn, 0, ',', '.'),
									number_format($tothrg, 0, ',', '.'),
									'',
									''
								));


								$pdf->SetTitle('LAPORAN PENJUALAN GLOBAL');
								$pdf->AliasNbPages();
								$pdf->output('Laporan_Penjualan_Global_103.PDF', 'I');
								exit;
							}
							//Uang Muka Penjualan 
							else if ($idlp == 104) {
								$query =
									"select ar_uangmuka.*, ar_customer.nama as namapelanggan from ar_uangmuka inner join ar_customer on ar_uangmuka.kodecust=ar_customer.kode 
			 						where tglum between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and ar_uangmuka.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_uangmuka.kodecbg = '$cab'";
								}
								$query .= " order by tglum, kodeso";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Uang Muka Penjualan');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->SetWidths(array(10, 30, 20, 60, 35, 30));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Nomor ', 'Tanggal', 'Pelanggan', 'Total', 'No Pesanan');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 30, 20, 60, 35, 30));
								$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'C'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								foreach ($lap as $value) {
									$pdf->row(array($nourut, $value->kodeum, date('d-m-Y', strtotime($value->tglum)), $value->namapelanggan, number_format($value->jumlahum, 0, '.', ','), $value->kodeso));
									$nourut++;
								}

								$pdf->SetTitle('LAPORAN UANG MUKA PENJUALAN');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_104.PDF', 'I');
								exit;
							}
							// Laporan Retur Penjualan Detail
							else if ($idlp == 105) {
								$query =
									"SELECT ar_retur.*, ar_customer.nama, ar_sifile.kodesi from ar_retur inner join ar_customer on ar_retur.kodecust = ar_customer.kode inner join ar_sifile on ar_sifile.kodesi = ar_retur.kodesi
			 						where tglretur between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and ar_retur.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_retur.kodecbg = '$cab'";
								}
								if ($marketing != "") {
									$query .= "and ar_sifile.id_sales = '$marketing'";
								}
								$query .= " group by kodecust order by tglretur,ar_retur.koderetur";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Laporan Retur Penjualan Detail');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");

								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');
								$pdf->SetLeftMargin(3);

								foreach ($lap as $row) {
									$pdf->setfont('Times', 'B', 10);
									$border = array('', '', '');
									$align = array('L', '', 'R');
									$pdf->SetWidths(array(10, 100, 25));
									$pdf->ln(5);
									$pdf->Fancyrow(array('', $row->nama, ''), $border, $align);
									$pdf->ln(1);


									$nourut = 1;
									$totqty  = 0;
									$tothrgjual  = 0;
									$tothrg  = 0;
									$totdisc = 0;
									$per_cust = "SELECT ar_returdetil.*, inv_barang.namabarang, ar_retur.kodesi,  ar_retur.keterangan, ar_retur.tglretur, ar_sifile.nama_sales FROM ar_returdetil INNER JOIN inv_barang ON ar_returdetil.kodeitem = inv_barang.kodeitem INNER JOIN ar_retur ON ar_returdetil.koderetur = ar_retur.koderetur INNER JOIN ar_sifile ON ar_retur.kodesi = ar_sifile.kodesi where ar_retur.kodecust='" . $row->kodecust . "' and tglretur between '$_tgl1' and '$_tgl2' order by koderetur";
									$lap1   = $db->query($per_cust)->getResult();
									$pdf->SetLeftMargin(3);
									$pdf->SetWidths(array(8, 28, 29, 18, 55, 25, 18, 10, 20, 10, 20, 19, 30));
									$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
									$judul = array('No.', 'No Retur', 'No Faktur', 'Tanggal', 'Nama Barang', 'Batch', 'ED', 'Qty', 'Harga', 'Disc', 'Netto', 'Marketing', 'Keterangan');
									$pdf->setfont('Times', 'B', 10);
									$pdf->row($judul);
									if ($cust != "") {
										$pdf->setheadertable($judul);
									}
									$pdf->SetWidths(array(8, 28, 29, 18, 55, 25, 18, 10, 20, 10, 20, 19, 30));
									$pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'C', 'C',  'R', 'R', 'R', 'R', 'R', 'L'));
									$pdf->setfont('Times', '', 9);
									$pdf->SetFillColor(224, 235, 255);
									$pdf->SetTextColor(0);
									$pdf->SetFont('');
									foreach ($lap1 as $value) {
										$tot 			= ($value->qtyretur * $value->hargajual);
										$dis 			= ($value->disc / 100) * $tot;
										$total_harga 	= $tot - $dis;
										if ($value->qtyretur >= 1) {

											$pdf->row(array(
												$nourut,
												$value->koderetur,
												$value->kodesi,
												date('d-m-Y', strtotime($value->tglretur)),
												$value->namabarang,
												$value->no_batch,
												$value->tanggal_exp,
												$value->qtyretur,
												number_format($value->hargajual, 0, '.', ','),
												$value->disc,
												number_format($total_harga, 0, '.', ','),
												$row->nama_sales,
												$db->keterangan
											));
											$totqty = $totqty + $value->qtyretur;
											$tothrgjual = $tothrgjual + $value->hargajual;
											$totdisc = $totdisc + $value->disc;
											$tothrg = $tothrg + $total_harga;
											$nourut++;
										}
									}


									$pdf->SetWidths(array(181, 10, 20, 10, 20, 19, 30));
									$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));
									$pdf->setfont('Times', 'B', 10);
									$pdf->row(array(
										'TOTAL',
										number_format($totqty, 0, ',', '.'),
										number_format($tothrgjual, 0, ',', '.'),
										number_format($totdisc, 0, ',', '.'),
										number_format($tothrg, 0, ',', '.'),
										$row->nama_sales,
										''
									));
								}


								$pdf->SetTitle('LAPORAN RETUR PENJUALAN DETAIL');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_retur_detail_105.PDF', 'I');
								exit;
							}
							// Laporan Penjualan Detail
							else if ($idlp == 106) {
								$query =
									"SELECT ar_customer.nama, ar_sifile.*, ar_sidetail.kodeitem FROM ar_sifile INNER JOIN ar_customer ON ar_sifile.kodecust = ar_customer.kode INNER JOIN ar_sidetail ON ar_sifile.kodesi = ar_sidetail.kodesi
			 						where tglsi between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= "and ar_sifile.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= "and ar_sifile.kodecbg = '$cab'";
								}
								if ($brg != "") {
									$query .= "and ar_sidetail.kodeitem = '$brg'";
								}
								if ($marketing != "") {
									$query .= "and ar_sifile.id_sales = '$marketing'";
								}
								$query .= " group by nama order by ar_sifile.tglsi, ar_sifile.kodesi";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Laporan Penjualan Detail');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->ln(-5);
								$pdf->SetWidths(array(10, 200, 50, 25, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 10);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');
								foreach ($lap as $value) {
									$pdf->setfont('Times', 'B', 10);
									$border = array('', '', '');
									$align = array('L', '', 'R');
									$font  = array('B', '', 'B');
									$pdf->SetWidths(array(10, 100, 25));
									$pdf->ln(5);
									$pdf->Fancyrow(array('', $value->nama, ''), $border, $align);
									$pdf->ln(1);

									$nourut = 1;
									$totqty  = 0;
									$tothrgjual  = 0;
									$tothrg  = 0;
									$totdisc  = 0;
									$per_cust = "SELECT ar_sidetail.*, ar_sifile.kodesi, ar_sifile.tglsi, ar_sifile.nama_sales FROM ar_sidetail INNER JOIN ar_sifile ON ar_sidetail.kodesi = ar_sifile.kodesi where ar_sifile.kodecust='" . $value->kodecust . "' and tglsi between '$_tgl1' and '$_tgl2'";
									if ($brg != "") {
										$per_cust .= " and ar_sidetail.kodeitem = '$brg'";
									}
									$lap1   = $db->query($per_cust)->getResult();
									$pdf->SetWidths(array(8, 29, 18, 85, 20, 18, 15, 25, 15, 25, 20));
									$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
									$judul = array('No.', 'No Faktur', 'Tanggal', 'Nama Barang', 'Batch', 'ED', 'Qty', 'Harga', 'Disc', 'Netto', 'Marketing');
									$pdf->setfont('Times', 'B', 10);
									$pdf->Row($judul);
									if ($cust != "") {
										$pdf->setheadertable($judul);
									}
									$pdf->SetWidths(array(8, 29, 18, 85, 20, 18, 15, 25, 15, 25, 20));
									$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'C', 'R', 'R', 'R', 'R', 'R'));
									$pdf->setfont('Times', '', 9);
									$pdf->SetFillColor(224, 235, 255);
									$pdf->SetTextColor(0);
									$pdf->SetFont('');

									foreach ($lap1 as $row) {
										$tot 			= ($row->qtysi * $row->hargajual);
										$dis 			= ($row->disc / 100) * $tot;
										$total_harga 	= $tot - $dis;

										if ($row->qtysi >= 1) {

											$qinv_barang = $db->query("SELECT * from inv_barang where kodeitem = '" . $row->kodeitem . "'")->getRow();

											$pdf->row(array(
												$nourut,
												$row->kodesi,
												$row->tglsi,
												$qinv_barang->namabarang,
												$row->no_batch,
												$row->tanggal_exp,
												$row->qtysi,
												number_format($row->hargajual, 0, '.', ','),
												$row->disc,
												number_format($total_harga, 0, '.', ','),
												$row->nama_sales
											));

											$totqty = $totqty + $row->qtysi;
											$tothrgjual = $tothrgjual + $row->hargajual;
											$totdisc = $totdisc + $row->disc;
											$tothrg = $tothrg + $total_harga;
											$nourut++;
										}
									}
									$pdf->SetWidths(array(178, 15, 25, 15, 25, 20));
									$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R'));

									$pdf->setfont('Times', 'B', 9);
									$pdf->Row(array(
										'Total  ',
										number_format($totqty, 0, ',', '.'),
										number_format($tothrgjual, 0, ',', '.'),
										number_format($totdisc, 0, ',', '.'),
										number_format($tothrg, 0, ',', '.'),
										$value->nama_sales
									));
								}
								$jumlah = "SELECT sum(qtysi) as jumlah_qty from ar_sidetail inner join ar_sifile on ar_sidetail.kodesi = ar_sifile.kodesi where  tglsi between '$_tgl1' and '$_tgl2'";
								if ($cust != "") {
									$jumlah .= "and ar_sifile.kodecust = '$cust'";
								}
								if ($cab != "") {
									$jumlah .= "and ar_sifile.kodecbg = '$cab'";
								}
								if ($brg != "") {
									$jumlah .= "and ar_sidetail.kodeitem = '$brg'";
								}
								if ($marketing != "") {
									$jumlah .= "and ar_sifile.id_sales = '$marketing'";
								}
								$lap2   = $db->query($jumlah)->getResult();
								foreach ($lap2 as $count) {
									$pdf->setfont('Times', 'B', 10);
									$border = array('', '', '');
									$align = array('L', '', 'L');
									$font  = array('B', '', 'B');
									$pdf->SetWidths(array(10, 30, 25));
									$pdf->ln(5);
									$pdf->Fancyrow(array('', 'Grand Total Qty :', number_format($count->jumlah_qty)), $border, $align);
								}
								$pdf->SetTitle('LAPORAN PENJUALAN DETAIL');
								$pdf->AliasNbPages();
								$pdf->output('Penjulan_Detail_106.PDF', 'I');
								exit;
							}
							// Penjualan per Barang
							else if ($idlp == 107) {
								$query =
									"select inv_barang.namabarang, sum(ar_sidetail.qtysi) as qty, sum(ar_sifile.totalsi) as total 
									from ar_sifile inner join ar_sidetail on ar_sifile.kodesi=ar_sidetail.kodesi inner join
									inv_barang on ar_sidetail.kodeitem=inv_barang.kodeitem
									where ar_sifile.tglsi between '$_tgl1' and '$_tgl2' ";

								if ($cust != "") {
									$query .= " and ar_sifile.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_sifile.kodecbg = '$cab'";
								}

								if ($brg != "") {
									$query .= " and ar_sidetail.kodeitem = '$brg'";
								}

								$query .= " order by tglsi, ar_sifile.kodesi";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Penjualan per Barang');
								$pdf->setsubjudul($_peri);
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->SetWidths(array(10, 100, 20, 60));
								$pdf->SetAligns(array('C', 'C', 'C', 'C'));
								$judul = array('No.', 'Nama Barang', 'Kuantitas', 'Penjualan');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->setheadertable($judul);
								$pdf->SetWidths(array(10, 100, 20, 60));
								$pdf->SetAligns(array('C', 'L', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								foreach ($lap as $row) {
									$pdf->row(array($nourut, $row->namabarang, $row->qty, number_format($row->total, 0, '.', ',')));
									$nourut++;
								}

								$pdf->SetTitle('LAPORAN PENJUALAN PER BARANG');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_107.PDF', 'I');
								exit;
							}
							// Laporan Faktur Belum Lunas
							else if ($idlp == 201) {
								$query =
									"select ar_sifile.*, ar_customer.nama as namapelanggan from ar_sifile inner join ar_customer on ar_sifile.kodecust=ar_customer.kode 
			 						where tglsi between '$_tgl1' and '$_tgl2' and totalsi>jumlahbayar";

								if ($cust != "") {
									$query .= " and ar_sifile.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and ar_sifile.kodecbg = '$cab'";
								}

								$query .= " order by tglsi, kodesi";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Faktur Belum Lunas');
								$pdf->SetMargins(25, 10);
								$pdf->setsubjudul($_peri1);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->SetWidths(array(10, 60, 30, 20, 25, 25, 25, 25, 20));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Customer', 'Nomor', 'Tanggal', 'Jatuh Tempo', 'Total', 'Piutang', 'Piutang Pajak', 'Umur (hr)');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$pdf->setheadertable($judul);
								$pdf->SetWidths(array(10, 60, 30, 20, 25, 25, 25, 25, 20));
								$pdf->SetAligns(array('C', 'L', 'C', 'C', 'C', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;
								$totfaktur  = 0;
								$totpiutang  = 0;
								$totpiutangpjk  = 0;

								foreach ($lap as $row) {
									$piutang    = $row->totalsi - $row->jumlahbayar;
									$piutangpjk = $row->ppn - $row->ppnbayar;
									$date1 = strtotime($tgl2);
									$date2 = strtotime($row->tglsi);
									$umur  = ($date1 - $date2) / (60 * 60 * 24);
									$selisihumurhr = date_diff(date_create($tgl2), date_create($row->tglsi));

									//$umur = 0;
									$pdf->row(array(
										$nourut,
										$row->namapelanggan,
										$row->kodesi,
										date('d-m-Y', strtotime($row->tglsi)),
										date('d-m-Y', strtotime($row->tgljthtempo)),
										number_format($row->totalsi, 0, '.', ','),
										number_format($piutang, 0, '.', ','),
										number_format($piutangpjk, 0, '.', ','),
										$selisihumurhr->days
									));
									$totfaktur = $totfaktur + $row->totalsi;
									$totpiutang = $totpiutang + $piutang;
									$totpiutangpjk = $totpiutangpjk + $piutangpjk;
									$nourut++;
								}
								$pdf->SetWidths(array(145, 25, 25, 25, 20, 20, 35));
								$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', 'B', 10);
								$pdf->row(array(
									'TOTAL',
									number_format($totfaktur, 0, ',', '.'),
									number_format($totpiutang, 0, ',', '.'),
									number_format($totpiutangpjk, 0, ',', '.'),
									''
								));

								$pdf->SetTitle('FAKTUR BELUM LUNAS');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_201.PDF', 'I');
								exit;
							}
							// Laporan Piutang
							else if ($idlp == 202) {
								$query =
									"SELECT b.kode, b.nama as namapelanggan from ar_sifile a inner join ar_customer b on a.kodecust=b.kode 
			 						where year(tglsi) ='$thn' and month(tglsi)='$bln' and a.sppn='Y'";

								if ($cust != "") {
									$query .= " and a.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and a.kodecbg = '$cab'";
								}
								$query .= " GROUP BY a.kodecust";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Laporan Piutang PPN');
								$pdf->setsubjudul($_peri2);
								$pdf->addpage("P", "A4");
								$pdf->setsize("P", "A4");
								$pdf->SetWidths(array(10, 80, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Customer', 'Saldo Awal', 'Penjualan', 'Pembayaran', 'Saldo Akhir');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
								$pdf->setheadertable($judul);
								$pdf->SetWidths(array(10, 80, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'L', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;
								$totsaldoawal  = 0;
								$totpenjualan  = 0;
								$totpembayaran  = 0;
								$totsaldoakhir  = 0;

								foreach ($lap as $row) {
									if ($bln == "1") {
										$bulan = 12;
										$tahun =  $thn - 1;
									} else {
										$bulan = $bln - 1;
										$tahun = $thn;
									}
									$RowBayarBackDate = $db->query("SELECT * FROM ar_penerimaan where kodecust='$row->kode' and year(tglbayar)='$tahun' and month(tglbayar)='$bulan'")->getResult();
									$totBayarBackDate  = 0;
									foreach ($RowBayarBackDate as $DataBayar) {
										$TotRowSifile = $db->query("SELECT sum(totalsi) as saldoawal from ar_sifile where kodecust='$row->kode' and kodebyr='$DataBayar->kodebyr' and year(tglsi) ='$tahun' and month(tglsi)='$bulan'")->getRow();
										$totBayarBackDate += $TotRowSifile->saldoawal;
									}
									$saldoawal = $db->query("SELECT sum(totalsi) as saldoawal from ar_sifile where kodecust='$row->kode' and year(tglsi) ='$tahun' and month(tglsi)='$bulan'")->getRow();
									$totjual = $db->query("SELECT sum(totalsi) as totaljual from ar_sifile where kodecust='$row->kode' and year(tglsi) ='$thn' and month(tglsi)='$bln'")->getRow();
									$totbayar = $db->query("SELECT sum(jumlahbayar) as totalbayar from ar_sifile where kodecust='$row->kode' and year(tglsi) ='$thn' and month(tglsi)='$bln'")->getRow();

									$SaldoAwalBackDate = $saldoawal->saldoawal - $totBayarBackDate;

									$HsaldoAkhir = $totjual->totaljual + $SaldoAwalBackDate - $totbayar->totalbayar;

									//$umur = 0;
									$pdf->row(array(
										$nourut,
										$row->namapelanggan,
										number_format($SaldoAwalBackDate),
										number_format($totjual->totaljual),
										number_format($totbayar->totalbayar),
										number_format($HsaldoAkhir)
									));
									$totsaldoawal = $totsaldoawal + $SaldoAwalBackDate;
									$totpenjualan = $totpenjualan + $totjual->totaljual;
									$totpembayaran = $totpembayaran + $totbayar->totalbayar;
									$totsaldoakhir = $totsaldoakhir + $HsaldoAkhir;
									$nourut++;
								}
								$pdf->SetWidths(array(90, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', 'B', 10);
								$pdf->row(array(
									'TOTAL',
									number_format($totsaldoawal, 0, ',', '.'),
									number_format($totpenjualan, 0, ',', '.'),
									number_format($totpembayaran, 0, ',', '.'),
									number_format($totsaldoakhir, 0, ',', '.'),
									''
								));

								$pdf->SetTitle('Laporan Piutang');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_201.PDF', 'I');
								exit;
							}
							// Laporan Umur Piutang
							else if ($idlp == 203) {
								$query = "select * from ar_customer order by nama";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Umur Piutang');
								$pdf->setsubjudul($_peri1);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->SetWidths(array(10, 60, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Pelanggan', 'Piutang', 'Blm Tempo', '1 - 30', '31 - 60', '61- 90', '91 - 120', '> 120');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 60, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								$totpiutang = 0;
								$totbelum  = 0;
								$tottempo1 = 0;
								$tottempo2 = 0;
								$tottempo3 = 0;
								$tottempo4 = 0;
								$tottempo5 = 0;
								foreach ($lap as $row) {
									$cust = $row->kode;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$piutang  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) < 1 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$blmtempo  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 1 and 30 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$tempo1  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 31 and 60 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$tempo2  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 61 and 90 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$tempo3  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       						where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 91 and 120 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$tempo4  = $data->total;

									$query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       							where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) > 120 and kodecust = '$cust'";
									$data = $db->query($query)->getRow();
									$tempo5  = $data->total;


									$totpiutang = $totpiutang + $piutang;
									$totbelum  = $totbelum + $blmtempo;
									$tottempo1 = $tottempo1 + $tempo1;
									$tottempo2 = $tottempo2 + $tempo2;
									$tottempo3 = $tottempo3 + $tempo3;
									$tottempo4 = $tottempo4 + $tempo4;
									$tottempo5 = $tottempo5 + $tempo5;

									//$umur = 0;
									$pdf->row(array(
										$nourut,
										$row->nama,
										number_format($piutang, 0, '.', ','),
										number_format($blmtempo, 0, '.', ','),
										number_format($tempo1, 0, '.', ','),
										number_format($tempo2, 0, '.', ','),
										number_format($tempo3, 0, '.', ','),
										number_format($tempo4, 0, '.', ','),
										number_format($tempo5, 0, '.', ',')
									));
									$nourut++;
								}
								$pdf->SetWidths(array(70, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', 'B', 9);
								$pdf->row(array(
									'TOTAL',
									number_format($totpiutang, 0, '.', ','),
									number_format($totbelum, 0, '.', ','),
									number_format($tottempo1, 0, '.', ','),
									number_format($tottempo2, 0, '.', ','),
									number_format($tottempo3, 0, '.', ','),
									number_format($tottempo4, 0, '.', ','),
									number_format($tottempo5, 0, '.', ',')
								));


								$pdf->AliasNbPages();
								$pdf->SetTitle('UMUR PIUTANG');
								$pdf->output('penjualan_203.PDF', 'I');
								exit;
							}
							// Laporan piutang penjualan yang sudah bayar/lunas
							elseif ($idlp == 204) {
								$query =
									"select a.*, b.* from ar_penerimaan a
									inner join ar_penerimaandetil b on a.kodebyr = b.kodebyr 
									where tglbayar between '$_tgl1' and '$_tgl2'";

								if ($cust != "") {
									$query .= " and a.kodecust = '$cust'";
								}
								if ($cab != "") {
									$query .= " and a.kodecbg = '$cab'";
								}
								$query .= " order by tglbayar";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Laporan piutang penjualan yang sudah bayar/lunas');
								$pdf->SetMargins(14, 10);
								$pdf->setsubjudul($_peri);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->SetWidths(array(10, 60, 30, 30, 20, 20, 30, 35, 35));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('NO.', 'CUSTOMER', 'NOMOR INPUT', 'NOMOR FAKTUR', 'TANGGAL FAKTUR', 'TANGGAL BAYAR', 'NOMINAL FAKTUR', 'NOMINAL FAKTUR YANG DIBAYARKAN', 'PEMBAYARAN (BCA,BJB,TUNAI)');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								// $pdf->setheadertable($judul);
								$pdf->SetWidths(array(10, 60, 30, 30, 20, 20, 30, 35, 35));
								$pdf->SetAligns(array('C', 'L', 'C', 'C', 'C', 'C', 'R', 'R', 'C'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;
								$totfaktur  = 0;
								$totbayar  = 0;

								foreach ($lap as $view) {
									$DataCustomer = $db->table('ar_customer')->getWhere(['kode' => $view->kodecust])->getRow();
									$DataAkun = $db->table('ms_akun')->getWhere(['kodeakun' => $view->kodebank])->getRow();

									$pdf->row(array(
										$nourut,
										$DataCustomer->nama,
										$view->kodebyr,
										$view->nomorfaktur,
										date('d-m-Y', strtotime($view->tglfaktur)),
										date('d-m-Y', strtotime($view->tglbayar)),
										number_format($view->totalfaktur, 0, '.', ','),
										number_format($view->bayar, 0, '.', ','),
										$DataAkun->namaakun
									));
									$totfaktur = $totfaktur + $view->totalfaktur;
									$totbayar = $totbayar + $view->bayar;
									$nourut++;
								}

								$pdf->SetWidths(array(170, 30, 35, 35));
								$pdf->SetAligns(array('C', 'R', 'R', 'R'));
								$pdf->setfont('Times', 'B', 10);
								$pdf->row(array(
									'TOTAL',
									number_format($totfaktur, 0, ',', '.'),
									number_format($totbayar, 0, ',', '.'),
									'',
								));

								$pdf->SetTitle('Laporan piutang penjualan yang sudah bayar/lunas');
								$pdf->AliasNbPages();
								$pdf->output('penjualan_204.PDF', 'I');
								exit;
							}
							// LAPORAN DATA CUSTOMER
							else if ($idlp == 401) {
								$query =
									"select * from ar_customer where kode is not null";

								if ($cust != "") {
									$query .= " and kode = '$cust'";
								}
								if ($cab != "") {
									$query .= " and kode_cabang = '$cab'";
								}
								if ($marketing != "") {
									$query .= " and kodesales = '$marketing'";
								}
								$query .= " order by nama";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Data Customer');
								$pdf->SetMargins(3, 5);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								
								$pdf->SetWidths(array(8, 50, 50, 32, 45, 20, 20, 10, 25, 30));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('NO', 'NAMA' , 'NAMA NPWP ', 'NO NPWP', 'ALAMAT', 'TELP','KONTAK', 'TOP', 'MARKETING','BATAS KREDIT');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(8, 50, 50, 32, 45, 20, 20, 10, 25, 30));
								$pdf->SetAligns(array('C', 'C', 'L', 'L', 'L', 'L', 'L', 'L', 'L', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								foreach ($lap as $row) {
									$pdf->row(array(
										$nourut, 
										$row->nama, 
										$row->nama_pt, 
										$row->npwp, 
										$row->alamat1,
										$row->telp,
										$row->contactname,
										$row->top,
										$row->nama_marketing,
										number_format($row->bataskredit, 0, '.', ','),
									));
									$nourut++;
								}
								$pdf->SetTitle('Data Customer');
								$pdf->AliasNbPages();
								$pdf->output('Data_Customer.PDF', 'I');
								exit;
							}
						} else {

							header('location:' . base_url());
						}
					}

					public function CetakLaporanexcel($x)
					{
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek  = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$taxes 		= $profile->taxes_desimal;
							$motto = '';
							$alamat = '';
							$namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
							$data  	= explode("~", $x);
							$idlp  	= $data[0];
							$tgl1  	= $data[1];
							$tgl2  	= $data[2];
							$bln  	= $data[3];
							$thn  	= $data[4];
							$cust  	= $data[5];
							$brg   	= $data[6];
							$cab   	= $data[7];
							$marketing 	= $data[8];

							$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
							$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
							$_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
							$_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
							$_peri2 = 'Periode. ' . $model_Global->_namabulan($bln) . ' ' . $thn;
							$tgl_ppn11 = date('2022-04-01');
							$tgl_ppn12 = date('2025-01-01');
							// Cortex Data
							if ($idlp == 301) {
								$query ="SELECT * from ar_sifile where sppn='Y' and retur=1 and tglsi between '$_tgl1' and '$_tgl2' ";

								$lap = $db->query($query)->getResult();
								$spreadsheet = new Spreadsheet();
								$sheet =  $spreadsheet->getActiveSheet();
								$sheet->setTitle('Faktur');
								$sheet->setCellValue('A1', 'NPWP Penjual');
								$sheet->setCellValue('A2', 'Baris');
								$sheet->setCellValue('B2', 'Tanggal Faktur');
								$sheet->setCellValue('C1', $profile->npwp);
								$sheet->setCellValue('C2', 'Jenis Faktur');
								$sheet->setCellValue('D2', 'Kode Transaksi');
								$sheet->setCellValue('E2', 'Keterangan Tambahan');
								$sheet->setCellValue('F2', 'Dokumen Pendukung');
								$sheet->setCellValue('G2', 'Referensi');
								$sheet->setCellValue('H2', 'Cap Fasilitas');
								$sheet->setCellValue('I2', 'ID TKU Penjual');
								$sheet->setCellValue('J2', 'NPWP/NIK Pembeli');
								$sheet->setCellValue('K2', 'Jenis ID Pembeli');
								$sheet->setCellValue('L2', 'Negara Pembeli');
								$sheet->setCellValue('M2', 'Nomor Dokumen Pembeli');
								$sheet->setCellValue('N2', 'Nama Pembeli');
								$sheet->setCellValue('O2', 'Alamat Pembeli');
								$sheet->setCellValue('P2', 'Email Pembeli');
								$sheet->setCellValue('Q2', 'ID TKU Pembeli');

								$column = 3;
								$differentDataCortex = [];
								foreach ($lap as $key => $value) {
									$DataCs = $db->query("SELECT * from ar_customer where kode='$value->kodecust'")->getRow();
									$differentDataCortex[] = [
										'baris' => ($column - 2),
										'kodesi' => $value->kodesi,
										'kodecust' => $value->kodecust,
										'cabang' => $value->kodecbg
									];
									$sheet->setCellValue('A' . $column, ($column - 2));
									$sheet->setCellValue('B' . $column, date('d/m/Y', strtotime($value->tglsi)));
									$sheet->setCellValue('C' . $column, 'Normal');
									$sheet->setCellValue('D' . $column, '04');
									$sheet->setCellValue('E' . $column, '');
									$sheet->setCellValue('F' . $column, '');
									$sheet->setCellValue('G' . $column, $value->kodesi . '-' . $DataCs->nama);
									$sheet->setCellValue('H' . $column, '');
									$sheet->setCellValue('I' . $column, $profile->npwp . '000000');
									$sheet->setCellValue('J' . $column,  str_replace(['.', '-'], '', '0' .$DataCs->npwp));
									$sheet->setCellValue('K' . $column, $DataCs->taxjenisidcs);
									$sheet->setCellValue('L' . $column, $DataCs->taxnegaracs);
									$sheet->setCellValue('M' . $column, '-');
									$sheet->setCellValue('N' . $column, $DataCs->nama_pt);
									$sheet->setCellValue('O' . $column, $DataCs->alamat1);
									$sheet->setCellValue('P' . $column, $DataCs->email);
									$sheet->setCellValue('Q' . $column, '0' . $DataCs->nojenisid . '000000');
									
									$sheet->getStyle('A' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('B' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD); // Format tanggal
									$sheet->getStyle('D' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('E' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('F' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('G' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('H' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('I' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('J' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('K' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('L' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('M' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('P' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									$sheet->getStyle('Q' . $column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
									
									$this->addDataValidation($sheet, 'C'. $column, '"Normal"'); 
									$this->addDataValidation($sheet, 'D'. $column, 'REF!$A$6:$A$15'); 
									$this->addDataValidation($sheet, 'E'. $column, 'REF!$A$17:$A$53'); 
									$this->addDataValidation($sheet, 'F'. $column, 'REF!$A$55'); 
									$this->addDataValidation($sheet, 'H'. $column, 'REF!$A$55:$A$91'); 
									$this->addDataValidation($sheet, 'K'. $column, 'REF!$A$93:$A$95'); 
									$this->addDataValidation($sheet, 'L'. $column, 'REF!$A$130:$A$381'); 
									$column++;
								}
								$sheet->mergeCells('A1:B1');
								$sheet->getStyle('A1:B1')->getFont()->setBold(true);
								$sheet->getStyle('A2:Q2')->getFont()->setBold(true);

								$sheet->getColumnDimension('A')->setAutoSize(true);
								$sheet->getColumnDimension('B')->setAutoSize(true);
								$sheet->getColumnDimension('C')->setAutoSize(true);
								$sheet->getColumnDimension('D')->setAutoSize(true);
								$sheet->getColumnDimension('E')->setAutoSize(true);
								$sheet->getColumnDimension('F')->setAutoSize(true);
								$sheet->getColumnDimension('G')->setAutoSize(true);
								$sheet->getColumnDimension('H')->setAutoSize(true);
								$sheet->getColumnDimension('I')->setAutoSize(true);
								$sheet->getColumnDimension('J')->setAutoSize(true);
								$sheet->getColumnDimension('K')->setAutoSize(true);
								$sheet->getColumnDimension('L')->setAutoSize(true);
								$sheet->getColumnDimension('M')->setAutoSize(true);
								$sheet->getColumnDimension('N')->setAutoSize(true);
								$sheet->getColumnDimension('O')->setAutoSize(true);
								$sheet->getColumnDimension('P')->setAutoSize(true);
								$sheet->getColumnDimension('Q')->setAutoSize(true);

								// Menambahkan sheet kedua
								$sheet2 = $spreadsheet->createSheet();
								$sheet2->setTitle('DetailFaktur');
								$sheet2->setCellValue('A1', 'Baris');
								$sheet2->setCellValue('B1', 'Barang/Jasa');
								$sheet2->setCellValue('C1', 'Kode Barang Jasa');
								$sheet2->setCellValue('D1', 'Nama Barang/Jasa');
								$sheet2->setCellValue('E1', 'Nama Satuan Ukur');
								$sheet2->setCellValue('F1', 'Harga Satuan');
								$sheet2->setCellValue('G1', 'Jumlah Barang Jasa');
								$sheet2->setCellValue('H1', 'Total Diskon');
								$sheet2->setCellValue('I1', 'DPP');
								$sheet2->setCellValue('J1', 'DPP Nilai Lain');
								$sheet2->setCellValue('K1', 'Tarif PPN');
								$sheet2->setCellValue('L1', 'PPN');
								$sheet2->setCellValue('M1', 'Tarif PPnBM');
								$sheet2->setCellValue('N1', 'PPnBM');

								$colshet2 = 2;
								$differentDataCortex;
								$jumdatasts  = count($differentDataCortex);
								for ($i = 0; $i < $jumdatasts; $i++) {
									$DetailFaktur = $db->table('ar_sidetail')->getWhere(['kodesi' => $differentDataCortex[$i]['kodesi']])->getResult();
									foreach ($DetailFaktur as $value) {
										$DataBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $differentDataCortex[$i]['cabang']])->getRow();
										$qty = intval($value->qtysi);
										$hargajual = intval($value->hargajual);
										$JumlahBarangDanHarga = $qty * $hargajual;
										$JumlahDiskon = ($value->disc / 100) * $JumlahBarangDanHarga;
										$DPP = $JumlahBarangDanHarga - $JumlahDiskon;
										$a = $profile->dpplainnya;
										$DPPLain = floatval($profile->dpplainnya) * $DPP;
										// $ppn = $DPPLain * floatval($profile->taxes_desimal);
										$ppn = $DPPLain * 0.12;

										$sheet2->setCellValue('A' . $colshet2, $differentDataCortex[$i]['baris']);
										$sheet2->setCellValue('B' . $colshet2, $DataBarang->taxbarangjasa);
										$sheet2->setCellValue('C' . $colshet2, '000000');
										$sheet2->setCellValue('D' . $colshet2, $DataBarang->namabarang);
										$sheet2->setCellValue('E' . $colshet2, $DataBarang->taxsatuan);
										$sheet2->setCellValue('F' . $colshet2, $value->hargajual);
										$sheet2->setCellValue('G' . $colshet2, $value->qtysi);
										$sheet2->setCellValue('H' . $colshet2, $JumlahDiskon);
										$sheet2->setCellValue('I' . $colshet2, '=G'.$colshet2 .'*F'.$colshet2 .'-H'.$colshet2);
										$sheet2->setCellValue('J' . $colshet2, '=11/12*I'. $colshet2);
										$sheet2->setCellValue('K' . $colshet2, '12');
										$sheet2->setCellValue('L' . $colshet2, '=J'.$colshet2.'*K'.$colshet2.'/100');
										$sheet2->setCellValue('M' . $colshet2, 0);
										$sheet2->setCellValue('N' . $colshet2, '=M'.$colshet2.'*J'.$colshet2.'/100');
										
										$sheet2->getStyle('G'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
										$sheet2->getStyle('H'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
										$sheet2->getStyle('I'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
										$sheet2->getStyle('J'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
										$sheet2->getStyle('L'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
										$sheet2->getStyle('N'. $colshet2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
										
										$this->addDataValidation($sheet2, 'B'. $colshet2, 'REF!$A$3:$A$4'); 
										$this->addDataValidation($sheet2, 'E'. $colshet2, 'REF!$A$97:$A$128'); 
										$colshet2++;
									}
									
								}

								$sheet2->getStyle('A1:N1')->getFont()->setBold(true);

								$sheet2->getColumnDimension('A')->setAutoSize(true);
								$sheet2->getColumnDimension('B')->setAutoSize(true);
								$sheet2->getColumnDimension('C')->setAutoSize(true);
								$sheet2->getColumnDimension('D')->setAutoSize(true);
								$sheet2->getColumnDimension('E')->setAutoSize(true);
								$sheet2->getColumnDimension('F')->setAutoSize(true);
								$sheet2->getColumnDimension('G')->setAutoSize(true);
								$sheet2->getColumnDimension('H')->setAutoSize(true);
								$sheet2->getColumnDimension('I')->setAutoSize(true);
								$sheet2->getColumnDimension('J')->setAutoSize(true);
								$sheet2->getColumnDimension('K')->setAutoSize(true);
								$sheet2->getColumnDimension('L')->setAutoSize(true);
								$sheet2->getColumnDimension('M')->setAutoSize(true);
								$sheet2->getColumnDimension('N')->setAutoSize(true);

								// Menambahkan sheet ketiga
								$sheet3 = $spreadsheet->createSheet();
								$sheet3->setTitle('REF');
								$sheet3->setCellValue('A1', 'Kode');
								$sheet3->setCellValue('A2', 'Barang/Jasa');
								$sheet3->mergeCells('A2:B2');
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A2')->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$sheet3->setCellValue('A3', 'A');
								$sheet3->setCellValue('A4', 'B');
								$sheet3->setCellValue('B1', 'Keterangan');
								$sheet3->setCellValue('B3', 'Barang');
								$sheet3->setCellValue('B4', 'Jasa');
								$sheet3->setCellValue('A5', 'Kode Transaksi');
								$sheet3->mergeCells('A5:B5');
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A5')->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								
								$Ano = 6;
								$DataCortaxTransaksi = $db->table('coretax_kodetransaksi')->get()->getResult();
								foreach ($DataCortaxTransaksi as $value) {
									$sheet3->setCellValue('A' . $Ano, $value->kode);
									$sheet3->setCellValue('B' . $Ano, $value->kode .' - '.$value->nama);
									$Ano++;
								}

								$sheet3->setCellValue('A'. $Ano, 'Keterangan Tambahan');
								$sheet3->mergeCells('A'.$Ano.':B'.$Ano);
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A'.$Ano)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A'.$Ano)->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A'.$Ano)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$DataCortaxKetTambah = $db->table('coretax_kettambah')->get()->getResult();
								$sheet3->setCellValue('B' . ($Ano+1), 'Tidak Ada');
								$Ano++;
								foreach ($DataCortaxKetTambah as $value) {
									$sheet3->setCellValue('A' . ($Ano+1), $value->kode);
									$sheet3->setCellValue('B' . ($Ano+1), $value->nama);
									$Ano++;
								}

								$sheet3->setCellValue('A'. $Ano, 'Cap Fasilitas');
								$sheet3->mergeCells('A'.$Ano.':B'.$Ano);
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A'.$Ano)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A'.$Ano)->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A'.$Ano)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$DataCortaxKetTambah = $db->table('coretax_capfas')->get()->getResult();
								$sheet3->setCellValue('B' . ($Ano+1), 'Tidak Ada');
								$Ano++;
								foreach ($DataCortaxKetTambah as $value) {
									$sheet3->setCellValue('A' . ($Ano+1), $value->kode);
									$sheet3->setCellValue('B' . ($Ano+1), $value->nama);
									$Ano++;
								}

								$sheet3->setCellValue('A'. $Ano, 'Jenis ID Pembeli');
								$sheet3->mergeCells('A'.$Ano.':B'.$Ano);
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A'.$Ano)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A'.$Ano)->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A'.$Ano)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$DataCortaxKetTambah = $db->table('coretax_jenisidcs')->get()->getResult();
								foreach ($DataCortaxKetTambah as $value) {
									$sheet3->setCellValue('A' . ($Ano+1), $value->kode);
									$sheet3->setCellValue('B' . ($Ano+1), $value->nama);
									$Ano++;
								}

								$sheet3->setCellValue('A'. $Ano, 'Satuan Ukur');
								$sheet3->mergeCells('A'.$Ano.':B'.$Ano);
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A'.$Ano)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A'.$Ano)->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A'.$Ano)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$DataCortaxKetTambah = $db->table('coretax_satuan')->get()->getResult();
								foreach ($DataCortaxKetTambah as $value) {
									$sheet3->setCellValue('A' . ($Ano+1), $value->kode);
									$sheet3->setCellValue('B' . ($Ano+1), $value->nama);
									$Ano++;
								}

								
								$sheet3->setCellValue('A'. $Ano, 'Kode Negara');
								$sheet3->mergeCells('A'.$Ano.':B'.$Ano);
								// Mewarnai sel A1 dengan warna latar belakang kuning
								$sheet3->getStyle('A'.$Ano)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
								$sheet3->getStyle('A'.$Ano)->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
								$sheet3->getStyle('A'.$Ano)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
								$DataCortaxKetTambah = $db->table('coretax_negara')->get()->getResult();
								foreach ($DataCortaxKetTambah as $value) {
									$sheet3->setCellValue('A' . ($Ano+1), $value->kode);
									$sheet3->setCellValue('B' . ($Ano+1), $value->nama);
									$Ano++;
								}

								$sheet3->getColumnDimension('A')->setAutoSize(true);
								$sheet3->getColumnDimension('B')->setAutoSize(true);

								// Tentukan nama file
								$fileName = 'IMPORT CORTEX.xlsx';

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
	
					// Fungsi untuk menambahkan data validation
					private function addDataValidation($sheet, $cell, $optionsRange) {
							$dataValidation = new DataValidation();
							$dataValidation->setType(DataValidation::TYPE_LIST);
							$dataValidation->setAllowBlank(true);
							$dataValidation->setFormula1($optionsRange);
							$dataValidation->setShowDropDown(true);
							$sheet->getCell($cell)->setDataValidation($dataValidation);
					}
	
					public function ExportCoretax($x){
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$db = db_connect();
						$session = session();
						$cek  = $session->get('level');
						$unit = $session->get('cabang');
						if (!empty($cek)) {
							$profile = $model_Global->_LoadProfile();
							$nama_usaha = $profile->nama_usaha;
							$taxes 		= $profile->taxes_desimal;
							$motto = '';
							$alamat = '';
							$namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
							$data  	= explode("~", $x);
							$idlp  	= $data[0];
							$tgl1  	= $data[1];
							$tgl2  	= $data[2];
							$bln  	= $data[3];
							$thn  	= $data[4];
							$cust  	= $data[5];
							$brg   	= $data[6];
							$cab   	= $data[7];
							$marketing 	= $data[8];

							$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
							$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
							// Cortex Data
							if ($idlp == 301) {
								$query ="SELECT * from ar_sifile where sppn='Y' and retur=1 and tglsi between '$_tgl1' and '$_tgl2' ";

								$lap = $db->query($query)->getResult();
								// Load template Excel
								$inputFileName = WRITEPATHCORETAX .'/templatev2.xlsx';
								$spreadsheet = IOFactory::load($inputFileName);

								// Mengisi data di Sheet 1
								$sheet = $spreadsheet->getSheet(0);
								$sheet->setCellValue('C1', $profile->npwp);
								$column = 4;
								$differentDataCortex = [];
								foreach ($lap as $key => $value) {
									$DataCs = $db->query("SELECT * from ar_customer where kode='$value->kodecust'")->getRow();
									$differentDataCortex[] = [
										'baris' => ($column - 3),
										'kodesi' => $value->kodesi,
										'kodecust' => $value->kodecust,
										'cabang' => $value->kodecbg
									];
									$sheet->setCellValue('A' . $column, ($column - 3));
									$sheet->setCellValue('B' . $column, date('d/m/Y', strtotime($value->tglsi)));
									$sheet->setCellValue('C' . $column, 'Normal');
									$sheet->setCellValue('D' . $column, '04');
									$sheet->setCellValue('E' . $column, '');
									$sheet->setCellValue('F' . $column, '');
									$sheet->setCellValue('G' . $column, '');
									$sheet->setCellValue('H' . $column, $value->kodesi . '-' . $DataCs->nama);
									$sheet->setCellValue('I' . $column, '');
									$sheet->setCellValue('J' . $column, $profile->npwp . '000000');
									$sheet->setCellValue('K' . $column, str_replace(['.', '-'], '', '0' .$DataCs->npwp));
									$sheet->setCellValue('L' . $column, $DataCs->taxjenisidcs);
									$sheet->setCellValue('M' . $column, $DataCs->taxnegaracs);
									$sheet->setCellValue('N' . $column, '-');
									$sheet->setCellValue('O' . $column, $DataCs->nama_pt);
									$sheet->setCellValue('P' . $column, $DataCs->alamat1);
									$sheet->setCellValue('Q' . $column, $DataCs->email);
									$sheet->setCellValue('R' . $column, "0" . $DataCs->nojenisid . '000000');
									$column++;
								}

								// Mengisi data di Sheet 2
								$sheet2 = $spreadsheet->getSheet(1);
								$colshet2 = 2;
								$differentDataCortex;
								$jumdatasts  = count($differentDataCortex);
								for ($i = 0; $i < $jumdatasts; $i++) {
									$DetailFaktur = $db->table('ar_sidetail')->getWhere(['kodesi' => $differentDataCortex[$i]['kodesi']])->getResult();
									foreach ($DetailFaktur as $value) {
										$DataBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => 	$differentDataCortex[$i]['cabang']])->getRow();
										$qty = intval($value->qtysi);
										$hargajual = intval($value->hargajual);
										$JumlahBarangDanHarga = $qty * $hargajual;
										$JumlahDiskon = ($value->disc / 100) * $JumlahBarangDanHarga;
										$DPP = $JumlahBarangDanHarga - $JumlahDiskon;
										$a = $profile->dpplainnya;
										$DPPLain = floatval($profile->dpplainnya) * $DPP;
										// $ppn = $DPPLain * floatval($profile->taxes_desimal);
										$ppn = $DPPLain * 0.12;

										$sheet2->setCellValue('A' . $colshet2, $differentDataCortex[$i]['baris']);
										$sheet2->setCellValue('B' . $colshet2, $DataBarang->taxbarangjasa);
										$sheet2->setCellValue('C' . $colshet2, "'000000");
										$sheet2->setCellValue('D' . $colshet2, $DataBarang->namabarang);
										$sheet2->setCellValue('E' . $colshet2, $DataBarang->taxsatuan);
										$sheet2->setCellValue('F' . $colshet2, $value->hargajual);
										$sheet2->setCellValue('G' . $colshet2, $value->qtysi);
										$sheet2->setCellValue('H' . $colshet2, $JumlahDiskon);
										$sheet2->setCellValue('I' . $colshet2, '=G'.$colshet2 .'*F'.$colshet2 .'-H'.$colshet2);
										$sheet2->setCellValue('J' . $colshet2, '=11/12*I'. $colshet2);
										$sheet2->setCellValue('K' . $colshet2, '12');
										$sheet2->setCellValue('L' . $colshet2, '=J'.$colshet2.'*K'.$colshet2.'/100');
										$sheet2->setCellValue('M' . $colshet2, 0);
										$sheet2->setCellValue('N' . $colshet2, '=M'.$colshet2.'*J'.$colshet2.'/100');
										$colshet2++;
									}
									
								}

								// Simpan file baru
								$outputFileName = 'Coretax.xlsx';
								// Set header untuk download
								header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
								header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
								header('Cache-Control: max-age=0');
								$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
								$writer->save('php://output');
							}
						}
					}

					//Faktur Pajak Page Start Here
					public function faktur_pajak()
					{
						$css = [
							'public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
							'public/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
						];
						$js = [
							'public/assets/libs/jquery/jquery.min.js',
							'public/assets/libs/metismenu/metisMenu.min.js',
							'public/assets/libs/simplebar/simplebar.min.js',
							'public/assets/libs/node-waves/waves.min.js',
							'public/assets/libs/datatables.net/js/jquery.dataTables.min.js',
							'public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'public/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
							'public/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js',
							'public/assets/libs/jszip/jszip.min.js',
							'public/assets/libs/pdfmake/build/pdfmake.min.js',
							'public/assets/libs/pdfmake/build/vfs_fonts.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.html5.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.print.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js',
							'public/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
							'public/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
						];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Faktur Pajak',
							'body' => 'admin/penjualan/exportData/fakturpajak',
						];
						return view('layout/layout', $data);
					}

					public function fakturpajak_table($tgl1, $tgl2)
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$profile = $model_Global->_LoadProfile();
						$taxes 		= $profile->taxes_desimal;
						$session = session();
						$cabang = $session->get('cabang');
						$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
						$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
						$db = db_connect();
						$query = "SELECT a.faktur_pajak, a.sppn, a.tglsi, b.nama, b.nama_pt, b.alamat1, b.npwp, c.* 
									FROM ar_sifile a, ar_customer b, ar_sidetail c WHERE a.kodecust = b.kode AND a.kodesi = c.kodesi ";

						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and a.tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(a.tglsi)= (select periode_tahun from ms_identity) 
										and month(a.tglsi)= (select periode_bulan from ms_identity)";
						}
						$query .= " order by a.tglsi, a.faktur_pajak ASC";
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						$nomor = 1;
						foreach ($res as $row) {
							if ($row->sppn == "Y") {

								$tgl_ppn11 = date('2022-04-01');
								$tgl_ppn12 = date('2025-01-01');

								if ($row->tglsi >= $tgl_ppn12) {
									$h_ppn = $taxes;
								} else if ($row->tglsi >= $tgl_ppn11) {
									$h_ppn = 0.11;
								} else {
									$h_ppn = 0.1;
								}
								if ($row->qtysi >= 1) {
									$query_barang = $db->query("SELECT namabarang from inv_barang where kodeitem='" . $row->kodeitem . "' ")->getRow();


									$hitung = $row->qtysi * $row->hargajual;
									$disc = ($row->disc / 100) * $hitung;
									$tdpp = $hitung - $disc;
									if ($row->sppn == "Y") {
										$ppn = $tdpp * $h_ppn;
									} else {
										$ppn = 0;
									}
									$total = $tdpp  + $ppn;
									$data[] = [
										$nomor,
										$row->faktur_pajak,
										$row->kodesi,
										date('d-M-y', strtotime($row->tglsi)),
										$row->npwp,
										$row->nama,
										$row->nama_pt,
										$row->alamat1,
										$query_barang->namabarang,
										number_format($row->qtysi),
										$row->satuan,
										$row->no_batch,
										$row->tanggal_exp,
										$row->disc,
										number_format($row->hargajual),
										number_format($disc),
										number_format($tdpp),
										number_format($ppn),
										number_format($total),
									];
									$nomor++;
								}
							}
						}

						$result = ['data' => $data];
						echo json_encode($result);
					}

					//Data Sub Penjualan Page Start Here
					public function data_sub_penjualan()
					{
						$css = [
							'public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
							'public/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
						];
						$js = [
							'public/assets/libs/jquery/jquery.min.js',
							'public/assets/libs/metismenu/metisMenu.min.js',
							'public/assets/libs/simplebar/simplebar.min.js',
							'public/assets/libs/node-waves/waves.min.js',
							'public/assets/libs/datatables.net/js/jquery.dataTables.min.js',
							'public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'public/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
							'public/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js',
							'public/assets/libs/jszip/jszip.min.js',
							'public/assets/libs/pdfmake/build/pdfmake.min.js',
							'public/assets/libs/pdfmake/build/vfs_fonts.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.html5.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.print.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js',
							'public/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
							'public/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
						];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Data Sub Penjualan',
							'body' => 'admin/penjualan/exportData/datasubpenjualan',
						];
						return view('layout/layout', $data);
					}

					public function subpenjualan_table($tgl1, $tgl2)
					{
						error_reporting(0);
						$session = session();
						$cabang = $session->get('cabang');
						$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
						$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
						$db = db_connect();
						$query = "SELECT a.tglsi, a.kodecust, a.faktur_pajak, a.ket, b.nama, b.npwp, b.nama_pt, b.kode_ereport, c.* 
								from ar_sifile a, ar_customer b, ar_sidetail c where a.kodecust=b.kode and a.kodesi=c.kodesi ";

						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and a.tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(a.tglsi)= (select periode_tahun from ms_identity) 
										and month(a.tglsi)= (select periode_bulan from ms_identity)";
						}
						$query .= " order by a.tglsi, a.faktur_pajak ASC";
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						foreach ($res as $row) {
							$query_barang = $db->query("SELECT namabarang, kode_ereport from inv_barang where kodeitem='$row->kodeitem' ")->getRow();
							$per_cust = $db->query("SELECT * from inv_barang_harga where kode_customer= '$row->kodecust' and kodeitem = '$row->kodeitem' ")->getRow();

							$subjumlah 	= $row->qtysi * $row->hargajual;
							$disc_rp	= ($subjumlah * $row->disc) / 100;
							$Jumlah 	= $subjumlah - $disc_rp;

							$fee1_rp 	= ($per_cust->fee1_pr * $Jumlah) / 100;
							$fee2_rp 	= ($per_cust->fee2_pr * $Jumlah) / 100;
							$fee3_rp 	= ($per_cust->fee3_pr * $subjumlah) / 100;
							$data[] = [
								$row->kodesi,
								$row->tglsi,
								$query_barang->kode_ereport,
								$query_barang->namabarang,
								$row->qtysi,
								$row->satuan,
								$row->no_batch,
								$row->tanggal_exp,
								number_format($row->hargajual),
								$row->disc,
								number_format($subjumlah),
								number_format($disc_rp),
								number_format($Jumlah),

								$per_cust->fee1_pr,
								$per_cust->bagian1,
								number_format($fee1_rp),

								$per_cust->fee2_pr,
								$per_cust->bagian2,
								number_format($fee2_rp),

								$per_cust->fee3_pr,
								$per_cust->bagian3,
								number_format($fee3_rp),
								$row->faktur_pajak,
								$row->kode_ereport,
								$row->nama,
								$row->nama_pt,
								$row->ket,
							];
						}

						$result = ['data' => $data];
						echo json_encode($result);
					}

					//Export Penjualan Page Start Here
					public function export_penjualan()
					{
						$css = [
							'public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
							'public/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
						];
						$js = [
							'public/assets/libs/jquery/jquery.min.js',
							'public/assets/libs/metismenu/metisMenu.min.js',
							'public/assets/libs/simplebar/simplebar.min.js',
							'public/assets/libs/node-waves/waves.min.js',
							'public/assets/libs/datatables.net/js/jquery.dataTables.min.js',
							'public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'public/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
							'public/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js',
							'public/assets/libs/jszip/jszip.min.js',
							'public/assets/libs/pdfmake/build/pdfmake.min.js',
							'public/assets/libs/pdfmake/build/vfs_fonts.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.html5.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.print.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js',
							'public/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
							'public/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
						];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Export Penjualan',
							'body' => 'admin/penjualan/exportData/exportpenjualan',
						];
						return view('layout/layout', $data);
					}

					public function exportpenjualan_table($tgl1, $tgl2)
					{
						error_reporting(0);
						$model_Global =  model('Global_model', 'M_GLOBAL');
						$profile = $model_Global->_LoadProfile();
						$taxes 		= $profile->taxes_desimal;
						$session = session();
						$cabang = $session->get('cabang');
						$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
						$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
						$db = db_connect();
						$query = "SELECT a.*, b.nama, b.alamat1, b.kota, b.telp, b.hp, b.npwp, b.nama_pt, 
									b.top, b.bataskredit, b.saldokredit from ar_sifile a, ar_customer b where a.kodecust=b.kode  ";

						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and a.tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(a.tglsi)= (select periode_tahun from ms_identity) 
										and month(a.tglsi)= (select periode_bulan from ms_identity)";
						}
						$query .= " order by a.kodesi";
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						foreach ($res as $row) {
							$tgl_ppn11 = date('2022-04-01 00:00');
							$tgl_ppn12 = date('2025-01-01 00:00');

							if ($row->tglsi >= $tgl_ppn12) {
								$h_ppn = $taxes;
							} else if ($row->tglsi >= $tgl_ppn11) {
								$h_ppn = 0.11;
							} else {
								$h_ppn = 0.1;
							}

							$query = $db->query("SELECT SUM(fee1_rp) as cn1, SUM(fee2_rp) as cn2, SUM(fee3_rp) as cn3,hargajual,qtysi from ar_sidetail where kodesi= '$row->kodesi'")->getRow();
							$query_retur = $db->query("SELECT * from ar_retur where kodesi= '$row->kodesi'")->getRow();
							$query_bayar = $db->query("SELECT * from ar_penerimaandetil where nomorfaktur= '$row->kodesi'")->getRow();

							$cn1 = $query->cn1;
							$cn2 = $query->cn2;
							$cn3 = $query->cn3;

							$hitung_cn = $cn1 + $cn2 + $cn3;
							$subtot = $row->dpp;
							$disc = $row->diskon;

							$subtot_hitung = $query_retur->dpp + $subtot;

							$tdpp = $subtot_hitung - $disc;
							if ($row->sppn == "Y") {
								$ppn = $tdpp * $h_ppn;
							} else {
								$ppn = 0;
							}

							$dpp_ppn = $tdpp + $ppn + $row->biayalain;
							$sisa_bayar = $dpp_ppn - $query_bayar->bayar;
							if ($row->pembayaran == 'T') {
								$pembayaran =  "Tunai";
							} else {
								$pembayaran =  "Kredit";
							}

							if ($row->statusid == '1') {
								$statusid =  "Belum Lunas";
							} else {
								$statusid =  "Lunas";
							}
							$terbilang = ucwords($model_Global->terbilang($sisa_bayar));
							if ($row->kodesi != $query_retur->kodesi) {
								$data[] = [
									$row->kodesi,
									date('d-M-y', strtotime($row->tglsi)),
									$row->kodecust,
									$row->nama,
									$row->alamat1,
									$row->kota,
									$row->telp,
									$row->hp,
									$row->nama_pt,
									number_format($dpp_ppn),
									$row->top,
									date('d-M-y', strtotime($row->tgljthtempo)),
									'',
									number_format($query_bayar->bayar),
									number_format($subtot_hitung),
									number_format($row->diskon),
									number_format($tdpp),
									number_format($ppn),
									number_format($sisa_bayar),
									$row->nama_sales,
									$row->kodebyr,
									$pembayaran,
									'',
									'',
									number_format($row->bataskredit),
									number_format($row->saldokredit),
									$row->kodeuser,
									$statusid,
									number_format($sisa_bayar),
									$terbilang,
									$row->faktur_pajak,
									number_format($hitung_cn),
									$row->kodeso,
								];
							} else {
								$data[] = [
									$row->kodesi,
									date('d-M-y', strtotime($row->tglsi)),
									$row->kodecust,
									$row->nama,
									$row->alamat1,
									$row->kota,
									$row->telp,
									$row->hp,
									$row->nama_pt,
									number_format($dpp_ppn),
									$row->top,
									date('d-M-y', strtotime($row->tgljthtempo)),
									'',
									number_format($query_bayar->bayar),
									number_format($subtot_hitung),
									number_format($row->diskon),
									number_format($tdpp),
									number_format($ppn),
									number_format($sisa_bayar),
									$row->nama_sales,
									$row->kodebyr,
									$pembayaran,
									'',
									'',
									number_format($row->bataskredit),
									'',
									$row->kodeuser,
									$statusid,
									number_format($sisa_bayar),
									$terbilang,
									$row->faktur_pajak,
									number_format($hitung_cn),
									$row->kodeso,
								];
								//retur
								$data[] = [
									$query_retur->koderetur,
									date('d-M-y', strtotime($query_retur->tglretur)),
									$row->kodecust,
									$row->nama,
									$row->alamat1,
									$row->kota,
									$row->telp,
									$row->hp,
									$row->nama_pt,
									number_format($dpp_ppn),
									$row->top,
									date('d-M-y', strtotime($row->tgljthtempo)),
									'',
									number_format($query_bayar->bayar),
									number_format($subtot_hitung),
									number_format($row->diskon),
									number_format($tdpp),
									number_format($ppn),
									number_format($sisa_bayar),
									$row->nama_sales,
									$row->kodebyr,
									$pembayaran,
									'',
									'',
									number_format($row->bataskredit),
									'',
									$row->kodeuser,
									$statusid,
									number_format($sisa_bayar),
									$terbilang,
									$row->faktur_pajak,
									number_format($hitung_cn),
									$query_retur->kodesi,
								];
							}
						}

						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function datasubpenjualanpack()
					{
						$css = [
							'public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
							'public/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
						];
						$js = [
							'public/assets/libs/jquery/jquery.min.js',
							'public/assets/libs/metismenu/metisMenu.min.js',
							'public/assets/libs/simplebar/simplebar.min.js',
							'public/assets/libs/node-waves/waves.min.js',
							'public/assets/libs/datatables.net/js/jquery.dataTables.min.js',
							'public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'public/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
							'public/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js',
							'public/assets/libs/jszip/jszip.min.js',
							'public/assets/libs/pdfmake/build/pdfmake.min.js',
							'public/assets/libs/pdfmake/build/vfs_fonts.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.html5.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.print.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js',
							'public/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
							'public/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
						];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Data Sub Penjualan Pack',
							'body' => 'admin/penjualan/exportData/datasubpenjualanpack',
						];
						return view('layout/layout', $data);
					}

					public function subpenjualanpacktable($tgl1, $tgl2)
					{
						error_reporting(0);
						$session = session();
						$cabang = $session->get('cabang');
						$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
						$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
						$db = db_connect();
						$query = "SELECT a.tglsi, a.kodecust, a.faktur_pajak, a.ket, b.* 
								from ar_sifile a, ar_sipaket b where a.kodesi=b.kodesi ";

						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and a.tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(a.tglsi)= (select periode_tahun from ms_identity) 
										and month(a.tglsi)= (select periode_bulan from ms_identity)";
						}
						$query .= " order by a.tglsi, a.faktur_pajak ASC";
						$res = $db->query($query)->getResult();
						$result = array();
						$data = [];
						foreach ($res as $row) {
							$query_barang = $db->query("SELECT namabarang, kode_ereport from inv_barang where kodeitem='$row->kodeitem_paket' ")->getRow();
							$per_cust = $db->query("SELECT * from inv_barang_harga where kode_customer= '$row->kodecust' and kodeitem = '$row->kodeitem_paket' ")->getRow();
							$DataCs = $db->query("SELECT * from ar_customer where kode='$row->kodecust'")->getRow();

							$subjumlah 	= $row->qtysi * $row->hargajual;
							$disc_rp	= ($subjumlah * $row->disc) / 100;
							$Jumlah 	= $subjumlah - $disc_rp;

							$fee1_rp 	= ($per_cust->fee1_pr * $Jumlah) / 100;
							$fee2_rp 	= ($per_cust->fee2_pr * $Jumlah) / 100;
							$fee3_rp 	= ($per_cust->fee3_pr * $subjumlah) / 100;
							$data[] = [
								$row->kodesi,
								$row->tglsi,
								$query_barang->kode_ereport,
								$query_barang->namabarang,
								$row->qtyterima_paket,
								$row->satuan,
								$row->nobatch_paket,
								$row->tglexp_paket,
								number_format($row->hargajual),
								$row->disc,
								number_format($subjumlah),
								number_format($disc_rp),
								number_format($Jumlah),

								$per_cust->fee1_pr,
								$per_cust->bagian1,
								number_format($fee1_rp),

								$per_cust->fee2_pr,
								$per_cust->bagian2,
								number_format($fee2_rp),

								$per_cust->fee3_pr,
								$per_cust->bagian3,
								number_format($fee3_rp),
								$row->faktur_pajak,
								$DataCs->kode_ereport,
								$DataCs->nama,
								$DataCs->nama_pt,
								$row->ket,
							];
						}

						$result = ['data' => $data];
						echo json_encode($result);
					}

					public function DataCortex()
					{
						$css = [
							'public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
							'public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
							'public/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'
						];
						$js = [
							'public/assets/libs/jquery/jquery.min.js',
							'public/assets/libs/metismenu/metisMenu.min.js',
							'public/assets/libs/simplebar/simplebar.min.js',
							'public/assets/libs/node-waves/waves.min.js',
							'public/assets/libs/datatables.net/js/jquery.dataTables.min.js',
							'public/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
							'public/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
							'public/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js',
							'public/assets/libs/jszip/jszip.min.js',
							'public/assets/libs/pdfmake/build/pdfmake.min.js',
							'public/assets/libs/pdfmake/build/vfs_fonts.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.html5.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.print.min.js',
							'public/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js',
							'public/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
							'public/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
						];
						$data  = [
							'css' => $css,
							'js' => $js,
							'title' => 'Data Cortex',
							'body' => 'admin/penjualan/exportData/datacortex',
						];
						return view('layout/layout', $data);
					}

					public function DataCortexTable($tgl1, $tgl2, $id)
					{
						error_reporting(0);
						$model_Global = model('Global_model', 'M_GLOBAL');
						$profile = $model_Global->_LoadProfile();
						$session = session();
						$cabang = $session->get('cabang');
						$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
						$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
						$db = db_connect();
						$query = "SELECT * from ar_sifile where sppn='Y' ";

						if ($tgl1 != "1" && $tgl2 !== "1") {
							$query .= " and tglsi between '$_tgl1' and '$_tgl2'";
						} else {
							$query .= " and year(tglsi)= (select periode_tahun from ms_identity) 
                    					and month(tglsi)= (select periode_bulan from ms_identity)";
						}
						$query .= " order by tglsi ASC";
						$res = $db->query($query)->getResult();
						$result = array();
						$baris = 1;
						$differentDataCortex = [];

						if ($id == 1) {
							$data = [];
							foreach ($res as $row) {
								$DataCs = $db->query("SELECT * from ar_customer where kode='$row->kodecust'")->getRow();
								$differentDataCortex[] = [
									'baris' => $baris,
									'kodesi' => $row->kodesi,
									'kodecust' => $row->kodecust,
									'cabang' => $row->kodecbg
								];

								$data[] = [
									$baris++,
									date('d/m/Y', strtotime($row->tglsi)),
									'Normal',
									'04',
									'',
									'',
									$row->kodesi,
									'',
									$profile->npwp . '0000000',
									$DataCs->npwp,
									$DataCs->taxjenisidcs,
									$DataCs->taxnegaracs,
									'-',
									$DataCs->nama,
									$DataCs->alamat1,
									$DataCs->email,
									$DataCs->nojenisid . '0000000',
								];
							}
						} else {
							$data2 = [];
							$jumdatasts  = count($differentDataCortex);
							$index = 1;
							foreach ($res as $result) {

								$DetailFaktur = $db->table('ar_sidetail')->getWhere(['kodesi' => $result->kodesi])->getResult();
								foreach ($DetailFaktur as $value) {
									$DataBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $result->kodecbg])->getRow();
									$qty = intval($value->qtysi);
									$hargajual = intval($value->hargajual);
									$JumlahBarangDanHarga = $qty * $hargajual;
									$JumlahDiskon = ($value->disc / 100) * $JumlahBarangDanHarga;
									$DPP = $JumlahBarangDanHarga - $JumlahDiskon;
									$a = $profile->dpplainnya;
									$DPPLain = $profile->dpplainnya * $DPP;
									$ppn = $DPPLain * 0.12;
									$data2[] = [
										$index,
										$DataBarang->taxbarangjasa,
										'000000',
										$DataBarang->namabarang,
										$DataBarang->taxsatuan,
										number_format($value->hargajual),
										number_format($value->qtysi),
										number_format($JumlahDiskon),
										number_format($DPP),
										number_format($DPPLain, 2),
										$profile->taxes_persen,
										number_format($ppn, 2),
										0,
										0
									];
								}
								$index++;
							}
						}

						if ($id == 1) {
							$result = ['data' => $data];
						} else {
							$result = ['data' => $data2, 'differentDataCortex' => $differentDataCortex]; // Mengembalikan differentDataCortex
						}

						echo json_encode($result);
					}
				}
