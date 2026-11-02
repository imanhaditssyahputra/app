<?php

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_faktur_helper;
use App\Helpers\simkeu_rpt_helper;


class Penjualan extends BaseController
{
	//Data Customer Page Start Here
	public function data_customer()
	{

		$session = session();
		$level = $session->get('level');
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$dataLogin = $model_Global->_Autono('CS');
		$akses = $model_Global->cek_menu_akses($level, 412);
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
			'tambah' => $tambah
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
				$data = [
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
					'bataskredit' => $input['bataskredit'],
					'saldokredit' => $input['bataskredit'],
					'kodesales' => $input['sales'],
					'nama_marketing' => $GetMarketing,
					'ppn' => $input['sppn']
				];
				$datalogs = [
					'detail' => 'tambah data Customer dengan kode ' . $input['kode'],
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
		if ($input['bataskredit'] == '0' || empty($input['bataskredit'])) {
			$saldokredit		= 0;
		} else if ($data_customer->bataskredit > $input['bataskredit']) {
			$hitung_bts_kredit 	= $data_customer->bataskredit - $input['bataskredit'];
			$saldokredit		= $data_customer->saldokredit - $hitung_bts_kredit;
		} else if ($data_customer->bataskredit < $input['bataskredit']) {
			$hitung_bts_kredit 	= $input['bataskredit'] - $data_customer->bataskredit;
			$saldokredit		= $data_customer->saldokredit + $hitung_bts_kredit;
		} else {
			$saldokredit		= $data_customer->saldokredit;
		}

		if ($validation->withRequest($this->request)->run()) {
			try {
				$db->transStart();
				$data = [
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
					'bataskredit' => $input['bataskredit'],
					'saldokredit' => $saldokredit,
					'kodesales' => $input['sales'],
					'nama_marketing' => $GetMarketing,
					'ppn' => $input['sppn']

				];

				$datalogs = [
					'detail' => 'Update data Customer dengan kode ' . $input['kode'],
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
			$data[] = [
				$value->nama_sales,
				$value->telpon,
				$value->area,
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
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 421);
		$db = db_connect();
		$query = "SELECT * FROM ar_sofile where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglso between '$tgl1' and '$tgl2'";
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
					$ppn = $totdpp * 0.11;
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
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('penjualan/transaksi/pesananmasuk/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
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
				$data[] = [
					$nomor,
					$value->kodeso,
					date('d-m-y H:i:s', strtotime($value->tglso)),
					$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
					$value->ket,
					$status,
					$akses->uharga == 1 ? number_format($totalSoD) : '0',
					$editview,
					$delete,
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

		public function syncdata($kode, $cust)
		{
			$db = db_connect();
			$session = session();
			$cabang = $session->get('cabang');
			$builderINVBarangHarga  = $db->table('inv_barang_harga');
			$hargaPercust = $builderINVBarangHarga->getWhere(array('kode_customer' => $cust, 'kodeitem' => $kode, 'cabang' => $cabang))->getRow();

			echo json_encode($hargaPercust);
		}

		public function pesananmasuksave()
		{
			error_reporting(0);
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$input = $this->request->getPost();
			$param = $input['id'];
			$session = session();
			$level = $session->get('level');
			$akses = $model_Global->cek_menu_akses($level, 421);
			// Table
			$builder            = $db->table('ar_sofile');
			$builderDetail      = $db->table('ar_sodetail');
			$builderBayar       = $db->table('ar_sobiaya');
			$builderINVBarangHarga  = $db->table('inv_barang_harga');
			try {
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
							}
						} else {
							$builderDetail->delete(['kodeso' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
						}
					}

					if ($DataSoFile->statusid != 3 || $param == 0) {
						$kode  = $input['kode'];
						$qty   = $input['qty'];
						$sat   = $input['sat'];
						$harga = $input['harga'];
						$disc  = $input['disc'];

						$jumdata  = count($kode);

						$tot = 0;
						$tdisc = 0;
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
							if ($kode[$i] != "") {
								if ($param == 0) {
									$datad['kodeso'] = $nobukti;
									$builderDetail->insert($datad);
								} else {
									if ($DataSoFile->statusid != 3) {
										$datad['kodeso'] = $input['nomorbukti'];
										$builderDetail->insert($datad);
									}
								}
							}
						}
					}

					if ($input['sppn'] == "Y") {
						$tppn = ($tot - $tdisc) * 0.1;
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
						'tglso'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
						'tglkirim' => date('Y-m-d', strtotime($input['tanggal_kirim'])),
						'tglentry' => date('Y-m-d H:i:s'),
						'kodeuser' => $session->get('username'),
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
						'statusid' => 1,
						'no_get' => 1,
						'id_sales'  => $kode_sales,
						'nama_sales' => $nama_sales,
					);

					$datalogs = [
						'date' => date('Y-m-d H:i:s'),
						'userid' => $session->get('username'),
					];

					if ($param == 0) {
						$data['kodeso'] = $nobukti;
						$builder->insert($data);
						$datalogs['detail'] = 'Tambah data Penjualan Pesanan Masuk dengan kode ' . $nobukti;
						$db->table("logs")->insert($datalogs);
						if ($input['sppn'] == 'Y') {
							$model_Global->_updatecounter1('SOP');
						} else {
							$model_Global->_updatecounter1('SO');
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
								'tglentry' => date('Y-m-d H:i:s'),
								'kodeuser' => $session->get('username'),
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
							$builder->where(['kodeso' => $input['nomorbukti'], 'kodecbg' => $session->get('cabang')])->update($data);
						}
						$datalogs['detail'] = 'Update data Penjualan Pesanan Masuk dengan kode ' . $input['nomorbukti'];
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
			if ($akses->udel == 1) {
				$sofile = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();
				$datalogs = [
					'detail' => 'Hapus data Penjualan Pesanan Masuk dengan Kode ' . $sofile->kodeso,
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				$delDetailso    = $builderDetail->delete(['kodeso' => $sofile->kodeso, 'cabang' => $cabang]);
				$delBayarso     = $builderBayar->delete(['kodeso' => $sofile->kodeso, 'cabang' => $cabang]);
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
				$queryd = "select ar_sodetail.*, inv_barang.namabarang from ar_sodetail inner join inv_barang on ar_sodetail.kodeitem=inv_barang.kodeitem where ar_sodetail.kodeso = '$param' and kdcbg='$CabangSo'";
				$queryb = "select sum(jumlah) as total from ar_sobiaya  where ar_sobiaya.kodeso = '$param'";

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
				$queryd = "select ar_sodetail.*, inv_barang.namabarang from ar_sodetail inner join inv_barang on ar_sodetail.kodeitem=inv_barang.kodeitem where ar_sodetail.kodeso = '$param' and kdcbg='$CabangSo'";
				$queryb = "select sum(jumlah) as total from ar_sobiaya  where ar_sobiaya.kodeso = '$param'";

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
					$count++;
					$data[] = [
						$count,
						$value->kodekirim,
						date('d-m-y H:i:s', strtotime($value->tglkirim)),
						$db->table('ar_customer')->where('kode', $value->kodecust)->get()->getRow()->nama ?? '',
						$value->keterangan,
						$status,
						$akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/pesanandisiapkan/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>' : '',
						$akses->udel == 1 ?  '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : ''
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
				$DataSo     = $db->table('ar_sofile')->where('kodeso', $data->kodeso)->get()->getRow();
			}
			$res = $db->query("SELECT * FROM ar_customer a inner join ar_sofile b on a.kode = b.kodecust where b.statusid != 4 and b.no_get != 3 and a.kode_cabang='$cabang' and b.kodecbg='$cabang' group by b.kodecust")->getResult();
			$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
			$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
			$data  = [
				'data' => $data ?? [],
				'DetailItem' => $DetailItem ?? [],
				'BiayaLain' => $BiayaLain ?? [],
				'DataSo' => $DataSo ?? [],
				'cabang' => $session->get('cabang'),
				'nomor' => $model_Global->_Autonomor('SD'),
				'nomor_ppn' => $model_Global->_Autonomor('SDP'),
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
							where a.kodeso='$so' and a.cabang='$cabang' and b.kdcbg='$cabang' and a.qtyorder > a.qtykirim")->getResult();
			echo json_encode($query);
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
			echo json_encode($query);
		}

		public function pesanandisiapkansave()
		{
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
							$db->query("UPDATE ar_sodetail set qtykirim = qtykirim - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kodeso = '$kodeso' and cabang='$sescabang'");
						}
						$builderDetail->delete(['kodekirim' => $input['nomorbukti'], 'gudang' => $input['gudang']]);
					}

					$kode  		= $input['kode'];
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
								$db->query("UPDATE ar_sodetail set qtykirim = qtykirim + '$qty[$i]' where kodeitem = '$kode[$i]' and kodeso = '$kodeso' and cabang='$sescabang'");
							}
						}
					}

					$data = array(
						'kodecbg' => $session->get('cabang'),
						'kodecust' => $input['cust'],
						'kodeso'  => $input['kodeso'],
						'gudang'  => $input['gudang'],
						'tglkirim'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
						'kodeuser' => $session->get('username'),
						'keterangan' => $input['keterangan'],
						'alamat' => $input['alamat'],
					);

					//Table 2
					$buildarSofile   = $db->table('ar_sofile');
					$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ar_sodetail where kodeso = '$kodeso' and cabang='$sescabang'")->getRow()->total;

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
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$db->transStart();
			$session = session();
			$level = $session->get('level');
			$cabang = $session->get('cabang');
			$akses = $model_Global->cek_menu_akses($level, 422);
			$builder            = $db->table('ar_kirim');
			$builderDetail      = $db->table('ar_kirimdetil');
			$buildSofile        = $db->table('ar_sofile');
			if ($akses->udel == 1) {
				$arKirim = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();
				if ($arKirim->kodeso != "") {
					$arKirimDetail = $builderDetail->getWhere(['kodekirim' => $arKirim->kodekirim, 'gudang' => $arKirim->gudang])->getResult();
					foreach ($arKirimDetail as $row) {
						$db->query("UPDATE ar_sodetail set qtykirim = qtykirim - '$row->qtykirim' where kodeitem = '$row->kodeitem' and kodeso = '$arKirim->kodeso'");
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
				$delDetail    = $builderDetail->delete(['kodekirim' => $arKirim->kodekirim, 'gudang' => $arKirim->gudang]);
				$DataKirim    = $builder->delete(['id' => $nomor, 'kodecbg' => $cabang]);
				if ($DataKirim == true && $delDetail == true) {
					$db->table("logs")->insert($datalogs);
					echo json_encode(['message' => 'Deleted']);
				}
			}
			$db->transComplete();
		}

		// =========================== FAKTUR PESANAN PENJUALAN SELECTION START ======================//
		//Faktur Penjualan Page Start Here
		public function faktur_penjualan()
		{
			$session = session();
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
				'tambah' => $tambah
			];
			return view('layout/layout', $data);
		}

		public function fakturpenjualan_table($tgl1, $tgl2)
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$session = session();
			$cabang = $session->get('cabang');
			$level = $session->get('level');
			$akses = $model_Global->cek_menu_akses($level, 423);
			$db = db_connect();
			$query = "SELECT * FROM ar_sifile where kodecbg='$cabang'";
			if ($tgl1 != "1" && $tgl2 !== "1") {
				$query .= " and tglsi between '$tgl1' and '$tgl2'";
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
					$akses->uedit == 1 ? '<a href="' . base_url('penjualan/transaksi/fakturpenjualan/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-eye text-white"></i> View</a>' : '',
					'',
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
			$level = $session->get('level');
			$akses = $model_Global->cek_menu_akses($level, 423);
			$data = [];
			if ($id > 0) {

				$data       = $db->table('ar_sifile')->where('id', $id)->get()->getRow();
				$DataKirim  = $db->table("ar_kirim")->where(['kodekirim' => $data->kodesd, 'kodecbg' => $data->kodecbg])->get()->getRow();
				$DetailItem = $db->table('ar_sidetail')->where(['kodesi' => $data->kodesi, 'gudang' => $DataKirim->gudang])->get()->getResult();
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
			$DataKe1 = $db->query("SELECT a.*,  b.namabarang 
                                    FROM ar_kirimdetil a
                                    INNER JOIN inv_barang b on b.kodeitem = a.kodeitem 
                                    where a.kodekirim = '$po' group by id")->getResult();

			$DataKe2 = $db->query("SELECT * FROM inv_barang_harga a INNER JOIN ar_kirimdetil b ON a.kodeitem = b.kodeitem 
		                        WHERE a.kode_customer='$cust' AND b.kodekirim='$po'")->getResult();
			$response = ['DataOne' => $DataKe1, 'DataTwo' => $DataKe2];
			echo json_encode($response);
		}

		public function getsd3($po)
		{
			$db = db_connect();
			$data = $db->query("SELECT a.*, b.keterangan
									FROM ar_kirimdetil a
									INNER JOIN ar_kirim b ON a.kodekirim = b.kodekirim
									where a.kodekirim = '$po' group by a.kodekirim")->getResult();
			echo json_encode($data);
		}

		public function getsd4($kode, $cust)
		{
			$db = db_connect();
			$data = $db->query("SELECT * FROM inv_barang_harga a INNER JOIN ar_kirimdetil b ON a.kodeitem = b.kodeitem WHERE a.kode_customer='$cust' AND b.kodekirim='$kode'")->getResult();
			echo json_encode($data);
		}

		public function fakturpenjualansave()
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$input = $this->request->getPost();
			$param = $input['id'];
			$session = session();
			$level = $session->get('level');
			$akses = $model_Global->cek_menu_akses($level, 423);
			// Table
			$builder            = $db->table('ar_sifile');
			$builderDetail      = $db->table('ar_sidetail');
			$buildKirim         = $db->table('ar_kirim');
			$builderINVBarang   = $db->table('inv_barang');
			$INVhargapercust    = $db->table('inv_barang_harga');
			$INVtransaksi       = $db->table('inv_transaksi');
			$buildCs            = $db->table("ar_customer");
			try {
				$db->transStart(); // Mulai transaksi
				if ($akses->uadd == 1 || $akses->uedit == 1) {
					$cabang   	= $session->get('cabang');
					$tanggal  	= $input['tanggal'];
					$cs 		= $input['cust'];
					$_vvdiskon	= str_replace(",", "", $input['vdiskon']);
					$_vvdpp		= str_replace(",", "", $input['vdpp']);
					$_vvppn		= str_replace(",", "", $input['vppn']);
					$_vvbiayadll = str_replace(",", "", $input['vbiayalain']);
					$_vvtotal	= str_replace(",", "", $input['vtotal']);

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
							$nobukti = $model_Global->_counter1('SIP');
							$faktur_pajak = $model_Global->_AutonomorFakturPenjualan('FP');
						} else {
							$nobukti = $model_Global->_counter1('SI');
						}
					}

					if ($param != 0) {
						$builderDetail->delete(['kodesi' => $input['nomorbukti']]);
					}
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

					$tothpp = 0;
					for ($i = 0; $i < $jumdata; $i++) {
						$_kode   = $kode[$i];

						$vjum  = $qty[$i] * $harga[$i];
						$vdisc = $vjum * ($disc[$i] / 100);
						$tot   = $tot + $vjum;
						$tdisc = $tdisc + $vdisc;

						$DataBarang = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->getRow();
						$tothpp = $tothpp + ($DataBarang->hargabeli * $qty[$i]);

						$DataInvBarangHarga = $INVhargapercust->getWhere(['kodeitem' => $kode[$i], 'cabang' => $cabang, 'kode_customer' =>  $input['cust']])->getRow();
						if ($DataInvBarangHarga) {
							$fee1_rp = $DataInvBarangHarga->fee1_rp;
							$fee2_rp = $DataInvBarangHarga->fee2_rp;
							$fee3_rp = $DataInvBarangHarga->fee3_rp;
						} else {
							$fee1_rp = 0;
							$fee2_rp = 0;
							$fee3_rp = 0;
						}

						$datad = array(
							'kodeitem' 		=> $kode[$i],
							'kodesd'   		=> $input['kodesd'],
							'kodeso'   		=> $nomorso,
							'qtysi' 		=> $qty[$i],
							'satuan' 		=> $sat[$i],
							'no_batch'  	=> $no_batch[$i],
							'tanggal_exp'  	=> $tgl_exp[$i],
							'hpp' 			=> $DataBarang->hargabeli,
							'hargajual' 	=> $harga[$i],
							'fee1_rp' 		=> $fee1_rp,
							'fee2_rp' 		=> $fee2_rp,
							'fee3_rp' 		=> $fee3_rp,
							'disc' 			=> $disc[$i],
							'gudang' 		=> $kodegudang,
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
								$INVtransaksi->insert($data_inv);
							}
							$db->query("UPDATE inv_nobatch set qty_saldo = qty_saldo - '$qty[$i]', qty_jual = qty_jual + '$qty[$i]' 
								where kodeitem = '$kode[$i]' and no_batch='$no_batch[$i]' and gudang = '$kodegudang' and cabang = '$cabang'");

							if ($nomorso != "") {
								$db->query("UPDATE ar_sodetail set qtyfaktur = qtyfaktur + '$qty[$i]' where kodeitem = '$kode[$i]' and kodeso = '$nomorso'");
							}

							$db->table("inv_barang_gudang")
								->where(['kodeitem' => $kode[$i], 'cabang' => $cabang, 'gudang' => $kodegudang])
								->update(['qty' => $DataBarang->stok - $qty[$i]]);
						}
					}

					//table 2
					$buildBiaya     = $db->table("ar_sibiaya");
					$buildSofile     = $db->table("ar_sofile");

					if ($input['sppn'] == "Y") {
						$tppn = ($tot - $tdisc) * 0.11;
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
									'Penjualan',
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
									'Penjualan',
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

					$data = array(
						'kodecbg' => $cabang,
						'kodecust' => $input['cust'],
						'pembayaran'  => $input['pembayaran'],
						'kodeso'  => $nomorso,
						'kodesd'  => $input['kodesd'],
						'faktur_pajak'  => $faktur_pajak,
						'tglsi'   => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
						'tglkirim' => date('Y-m-d', strtotime($input['tanggalkirim'])),
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
						$akun_debet = $akun_kas;
					} else {
						$akun_debet = $akun_piutang;
						$update = $db->query("UPDATE ar_customer set saldokredit = saldokredit - '$_vvtotal' where kode='$cs' ");
					}

					if ($param == 0) {

						$model_Global->_rekamjurnal(

							date('Y-m-d', strtotime($input['tanggal'])),
							$nobukti,
							'JJ',
							$input['kodesd'],
							1,
							$akun_debet,
							'Penjualan',
							'Penjualan',
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
								'Penjualan',
								'Diskon Penjualan',
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
								'Penjualan',
								'PPN Penjualan',
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
							'Penjualan',
							'Penjualan',
							0,
							$_vvdpp + $_vvbiayadll,
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
							'Penjualan',
							'HPP Penjualan',
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
							'Penjualan',
							'HPP Penjualan',
							0,
							$tothpp,
							$cabang
						);
					} else {
						$model_Global->_rekamjurnal(

							date('Y-m-d', strtotime($input['tanggal'])),
							$input['nomorbukti'],
							'JJ',
							$input['kodesd'],
							1,
							$akun_debet,
							'Penjualan',
							'Penjualan',
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
								'Penjualan',
								'Diskon Penjualan',
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
								'Penjualan',
								'PPN Penjualan',
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
							'Penjualan',
							'Penjualan',
							0,
							$_vvdpp + $_vvbiayadll,
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
							'Penjualan',
							'HPP Penjualan',
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
							'Penjualan',
							'HPP Penjualan',
							0,
							$tothpp,
							$cabang
						);
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
			} catch (\Exception $e) {
				echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
			}
		}

		public function cetak_faktur($param1)
		{
			$param = str_replace('"', '', $param1);
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

				$queryh = "SELECT ar_sifile.*, ar_customer.nama, ar_customer.nama_pt, ar_customer.npwp, ar_customer.telp, ar_sofile.no_po  FROM ar_sifile INNER JOIN ar_customer ON ar_sifile.kodecust = ar_customer.kode INNER JOIN ar_sofile ON ar_sifile.kodeso = ar_sofile.kodeso WHERE ar_sifile.kodesi = '$param'";
				$queryd = "SELECT ar_sidetail.*, ar_kirimdetil.tgl_exp, inv_barang.namabarang from ar_sidetail inner join inv_barang on ar_sidetail.kodeitem=inv_barang.kodeitem inner join ar_kirimdetil on ar_kirimdetil.kodekirim=ar_sidetail.kodesd where ar_sidetail.kodesi = '$param' group by ar_sidetail.id";
				$queryb = "SELECT sum(jumlah) as total from ar_sibiaya  where ar_sibiaya.kodesi = '$param'";
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
				$pdf->SetWidths(array(16, 71, 18, 18, 8, 12, 16, 12, 19));
				$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
				$align  = array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R');
				$pdf->setfont('Arial', 'B', 7.5);
				$pdf->SetAligns(array('L', 'C', 'R'));
				$fc = array('0', '0', '0', '0', '0', '0', '0', '0', '0');
				$judul = array('Kode', 'Nama Barang', 'Batch', 'Expired', 'Qty', 'Satuan', 'Harga', 'Disc %', 'Total Harga');
				$pdf->FancyRow2(5, $judul, $fc, $border, $align);
				$border = array('', '', '');
				$pdf->setfont('Arial', '', 7.5);
				$tot = 0;
				$subtot = 0;
				$tdisc  = 0;
				$border = array('', '', '', '', '', '', '', '', '');
				$align  = array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'C', 'R');
				$fc = array('0', '0', '0', '0', '0', '0', '0');
				$pdf->SetFillColor(0, 0, 139);
				$pdf->settextcolor(0);
				foreach ($detil as $db) {
					$harga_brang = number_format($db->hargajual, 0, '', '');
					$dpp = ($db->qtysi * $harga_brang);
					$dis = ($db->disc / 100) * $dpp;
					$jum = $dpp - $dis;
					$tot = $tot + $jum;

					$subtot = $subtot + $dpp;
					$tdisc  = $tdisc + $dis;

					$tdpp = $subtot - $tdisc;

					if ($db->qtysi >= 1 && $jum >= 0) {

						$pdf->FancyRow(array(
							$db->kodeitem,
							$db->namabarang,
							$db->no_batch,
							$db->tanggal_exp,
							$db->qtysi,
							$db->satuan,
							number_format($db->hargajual, 0, '.', ','),
							$db->disc,
							number_format($jum, 0, '.', ',')
						), $fc, $border, $align);
						$pdf->ln(-1.5);
					}
				}


				if ($header->sppn == "Y") {
					$ppn = $tdpp * 0.11;
				} else {
					$ppn = 0;
				}
				$biayalain = $biaya->total;
				$tot = $tdpp + $ppn + $biayalain;
				$tot_terbilang = number_format($tot, 0, '', '');
				$pdf->SetFillColor(230);
				$pdf->ln(-4);
				$border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
				$pdf->FancyRow(array('', '', '', '', '', '', '', '', ''), 0, $border);
				$pdf->ln(1);
				$pdf->SetWidths(array(100, 20, 30, 40));

				$border = array('TB', '', 'T', 'T');
				$align  = array('L', '', 'L', 'R');
				$fc = array('0', '0', '0', '0');
				$pdf->FancyRow(array('Keterangan', '', 'Sub Total', number_format($subtot, 2, '.', ',')), $fc, $border, $align, 0);
				$border = array('', '', '', '');
				$pdf->ln(-2);
				$pdf->FancyRow(array('', '', 'Diskon', number_format($tdisc, 2, '.', ',')), $fc, $border, $align);
				$pdf->ln(-2);
				$pdf->FancyRow(array($header->ket, '', 'DPP', number_format($tdpp, 2, '.', ',')), $fc, $border, $align);
				$pdf->ln(-2);
				$pdf->FancyRow(array('', '', 'PPN (11%)', number_format($ppn, 2, '.', ',')), $fc, $border, $align);
				$pdf->ln(-2);
				$pdf->FancyRow(array('', '', 'Biaya Lain-lain', number_format($biayalain, 2, '.', ',')), $fc, $border, $align);
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
				$pdf->ln(5);


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
					date('d-m-y', strtotime($value->tglcek)),
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
			}
			$res = $db->query("SELECT ar_sifile.*, ar_customer.* FROM ar_customer INNER JOIN ar_sifile ON ar_sifile.kodecust = ar_customer.kode WHERE ar_sifile.kodecust = ar_customer.kode AND ar_sifile.statusid = '1' GROUP by ar_sifile.kodecust")->getResult();
			$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
			$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
			$css = [];
			$js = [];
			$data  = [
				'data' => $data ?? [],
				'DetailItem' => $DetailItem ?? [],
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

			$data = $db->query("SELECT ar_sifile.*, totalsi as total, (totalsi-jumlahbayar-uangmuka) as hutang from ar_sifile where kodecust='$data' and totalsi>jumlahbayar+uangmuka")->getResult();
			$vdata = array();
			foreach ($data as $row) {
				$duangmuka = $db->query("SELECT * from ar_uangmuka where kodeso = '$row->kodeso' and jumlahum>jumlahbayar")->getRow();
				if ($duangmuka) {
					$uangmuka = $duangmuka->jumlahum;
				} else {
					$uangmuka = 0;
				}


				$vdata[] = array(
					'kodesi' => $row->kodesi,
					'tglsi' => date('d-m-Y', strtotime($row->tglsi)),
					'totalsi' => $row->totalsi,
					'uangmuka' => $uangmuka,
					'hutang' => $row->hutang,
					'total' => $row->total,
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
			try {
				if ($akses->uadd == 1 || $akses->uedit == 1) {
					$db->transStart();
					$cs       = $input['cust'];
					$cabang = $session->get('cabang');

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
						$model_Global->_hapusjurnal($input['nomorbukti'], 'JT');
					}

					$faktur  = $input['faktur'];
					$tglfaktur   = $input['tglfaktur'];
					$totfaktur   = $input['totfaktur'];
					$hutang = $input['hutang'];
					$bayar = $input['bayar'];
					$disc  = $input['disc'];
					$uangmuka  = $input['uangmuka'];

					$jumdata  = count($faktur);

					$nourut = 1;

					$profile = $model_Global->_LoadProfile();
					$akun_debet     = $profile->akun_piutang;
					$akun_kredit    = $input['kasbank'];

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
								$db->query("UPDATE ar_sifile set jumlahbayar = jumlahbayar + '$_bayar' , uangmuka = uangmuka + '$_uangmuka' where kodesi='$faktur[$i]'");

								if ($_uangmuka > 0) {
									$dfaktur = $buildSifile->getWhere(['kodesi' => $faktur[$i], 'kodecbg' => $cabang])->getRow();
									$kodeso  = $dfaktur->kodeso;
									$db->query("UPDATE ar_uangmuka set jumlahbayar=jumlahbayar+$_uangmuka where kodeso = '$kodeso'");
								}
							}

							$db->query("UPDATE ar_sifile set statusid=2 where jumlahbayar+uangmuka>=totalsi and kodesi = '$faktur[$i]'");
							$db->query("UPDATE ar_sifile set kodebyr='$nobukti' where kodesi = '$faktur[$i]'");


							$model_Global->_rekamjurnal(
								date('Y-m-d', strtotime($input['tanggal'])),
								$nobukti,
								'JJ',
								$faktur[$i],
								1,
								$akun_debet,
								'Pembayaran Piutang',
								'Pembayaran Piutang Faktur ' . $faktur[$i],
								$_total,
								0,
								$cabang
							);

							$model_Global->_rekamjurnal(
								date('Y-m-d', strtotime($input['tanggal'])),
								$nobukti,
								'JJ',
								$faktur[$i],
								2,
								$akun_kredit,
								'Pembayaran Piutang',
								'Pembayaran Piutang Faktur ' . $faktur[$i],
								0,
								$_total,
								$cabang
							);
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
					$buildjurnal      = $db->table('tr_jurnal');
					$buildCustomer    = $db->table('ar_customer');
					// data inputan keuangan masuk

					$tahun 		= date('Y', strtotime($input['tanggal']));
					$bulan 		= date('m', strtotime($input['tanggal']));
					$kodebukti 	= $buildakun->getWhere(['kodeakun' => $input['kasbank']])->getRow()->kodek;
					$vbukti 	= $kodebukti . '%';
					$likeTerima = $buildTerima->like(['terima_nomor' => $vbukti])->getWhere(['terima_kodecbg' => $cabang, 'year(terima_tanggal)' => $tahun, 'month(terima_tanggal)' => $bulan]);
					$max        = $likeTerima->getNumRows();
					$nourut  	        = $max + 1;
					$nomorbukti   = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);

					$data_customer 	= $buildCustomer->getWhere(['kode' => $cs])->getRow();

					if ($param != 0) {
						$tr_masuk 	    = $buildTerima->getWhere(['terima_kodebyr' => $input['nomorbukti'], 'terima_kodecbg' => $cabang])->getRow();
						$register 		= $tr_masuk->terima_register;
						$nomorbukti  	= $tr_masuk->terima_nomor;
					}

					//data input table tr_penerimaan
					$data = array(
						'terima_nomor' 		=> $nomorbukti,
						'terima_tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
						'terima_kasbank' 	=> $input['kasbank'],
						'terima_kodecbg' 	=> $cabang,
						'terima_penerima' 	=> $data_customer->nama,
						'terima_status' 	=> 2,
						'terima_userentry' 	=> $session->get('username')

					);

					if ($param == 0) {
						$data['terima_kodebyr'] = $nobukti;
						$data['terima_uraian'] = 'Penerimaan ' . $nobukti;
						$buildTerima->insert($data);
						$register = $db->insertID();
					} else {
						$buildTerima->where(['terima_kodebyr' => $input['nomorbukti'], 'terima_register' => $register, 'terima_kodecbg' => $cabang])->update($data);
						$buildTerimad->delete(['terimad_register' => $register, 'cabang' => $cabang]);
					}

					$buildjurnal->delete(['novoucher' => $nomorbukti, 'jenis' => 'JK', 'wbs' => $kodebukti]);
					$nourut = 1;

					//data input table tr_penerimaand
					$datad = array(
						'terimad_register' 	=> $register,
						'terimad_nomor' 	=> $nomorbukti,
						'terimad_nourut' 	=> $nourut,
						'terimad_akun' 		=> $akun_debet,
						'terimad_uraian' 	=> 'Penerimaan ' . $nobukti,
						'terimad_jumlah' 	=> $_jumlahbayar,
						'cabang'			=> $cabang

					);
					if ($akun_kredit != "") {
						$buildTerimad->insert($datad);

						$data_jurnal = array(
							'novoucher' 	=> $nomorbukti,
							'noref' 		=> $register,
							'tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
							'keterangan' => 'Penerimaan dari Customer ' . $data_customer->nama,
							'nourut' 	=> $nourut + 1,
							'jenis' 		=> 'JK',
							'kodecbg' 	=> $cabang,
							'wbs' 		=> $kodebukti,
							'kodeakun' 	=> $akun_kredit,
							'debet' 		=> 0,
							'kredit' 	=> $_jumlahbayar,
							'userid' 	=> $session->get('username'),
						);
						$buildjurnal->insert($data_jurnal);
						$nourut++;
					}

					$data_jurnal = array(
						'novoucher' 	=> $nomorbukti,
						'noref' 		=> $register,
						'tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
						'keterangan' => 'Penerimaan dari Customer ' . $data_customer->nama,
						'nourut' 	=> 1,
						'jenis' 		=> 'JK',
						'kodecbg' 	=> $cabang,
						'wbs' 		=> $kodebukti,
						'kodeakun' 	=> $akun_debet,
						'debet' 		=> $_jumlahbayar,
						'kredit' 	=> 0,
						'userid' 	=> $session->get('username'),
					);
					$buildjurnal->insert($data_jurnal);
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
			$data = [];
			if ($id > 0) {
				$data       = $db->table('ar_retur')->where('idretur', $id)->get()->getRow();
				$DetailItem = $db->table('ar_returdetil')->where(['koderetur' => $data->koderetur])->get()->getResult();
			}
			$res = $db->query("SELECT * FROM ar_customer a inner join ar_sifile b on a.kode = b.kodecust where b.retur = 1 and b.kodecbg='$cabang' group by b.kodecust")->getResult();
			$arCust = $db->query("SELECT * FROM ar_customer")->getResult();
			$gudang = $db->table("inv_gudang")->getWhere(['cabang' => $session->get('cabang')])->getResult();
			$css = [];
			$js = [];
			$data  = [
				'data' => $data ?? [],
				'DetailItem' => $DetailItem ?? [],
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

			$response = ['DataHeader' => $DataKe1, 'Dataitem' => $DataKe2];
			echo json_encode($response);
		}

		public function retur_save()
		{
			$model_Global =  model('Global_model', 'M_GLOBAL');
			$db = db_connect();
			$input = $this->request->getPost();
			$param = $input['id'];

			$session = session();
			$level = $session->get('level');
			$akses = $model_Global->cek_menu_akses($level, 426);
			// Table
			$builder            = $db->table('ar_retur');
			$builderDetail      = $db->table('ar_returdetil');
			$builderINVBarang   = $db->table('inv_barang');
			$INVtransaksi       = $db->table('inv_transaksi');
			try {
				$db->transStart();
				if ($akses->uadd == 1 || $akses->uedit == 1) {
					$cabang   = $session->get('cabang');
					$kodesi   = $input['kodesi'];
					$gudang   = $input['gudang'];
					$_vvdiskon	= str_replace(",", "", $input['vdiskon']);
					$_vvdpp		= str_replace(",", "", $input['vdpp']);
					$_vvppn		= str_replace(",", "", $input['vppn']);
					$_vvtotal	= str_replace(",", "", $input['vtotal']);

					$DataRetur = $builder->getWhere(['koderetur' => $input['nomorbukti'], 'kodecbg' => $cabang])->getRow();
					if (empty($DataRetur)) {
						$nobukti = $input['nomorbukti'];
					} else {
						$nobukti = $model_Global->_counter1('SR');
					}

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

					$jumdata  = count($kode);

					$tot = 0;
					$tdisc = 0;

					for ($i = 0; $i < $jumdata; $i++) {
						$hpp = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->getRow();
						$vjum  = $qty[$i] * $harga[$i];
						$vdisc = $vjum * ($disc[$i] / 100);
						$tot   = $tot + $vjum;
						$tdisc = $tdisc + $vdisc;

						$datad = array(
							'kodeitem' => $kode[$i],
							'no_batch' => $no_batch[$i],
							'tanggal_exp' => $tgl_exp[$i],
							'qtyretur' => $qty[$i],
							'satuan' => $sat[$i],
							'hargajual' => $harga[$i],
							'disc' => $disc[$i],
							'kodegudang' => $gudang,
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

								$data_inv['nobukti'] = $nobukti;
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
						}
					}

					//TABLE 2
					$buildSIBiaya       = $db->table("ar_sibiaya");
					$buildSifile        = $db->table('ar_sifile');
					$buildterimaDetail  = $db->table('ar_penerimaandetil');
					if ($input['sppn'] == "Y") {
						$tppn = ($tot - $tdisc) * 0.11;
					} else {
						$tppn = 0;
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

					if ($param != 0) {
						$buildSIBiaya->delete(['kodesi' => $input['kodesi'], 'cabang' => $cabang]);
						$model_Global->_hapusjurnal($input['nomorbukti'], 'JJ');
					}

					$profile = $model_Global->_LoadProfile();
					$akun_debet     = $profile->akun_retjual;
					$akun_kredit    = $profile->akun_persediaan;

					$total = $_vvtotal;

					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$input['nomorbukti'],
						'JJ',
						$input['kodesi'],
						1,
						$akun_debet,
						'Retur Penjualan ' . $input['kodesi'],
						'Retur Penjualan ' . $input['kodesi'],
						$total,
						0,
						$cabang
					);

					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$input['nomorbukti'],
						'JJ',
						$input['kodesi'],
						2,
						$akun_kredit,
						'Retur Penjualan ' . $input['kodesi'],
						'Retur Penjualan ' . $input['kodesi'],
						0,
						$total,
						$cabang
					);

					$data_faktur = $buildSifile->getWhere(['kodesi' => $input['kodesi'], 'kodecbg' => $cabang])->getRow();
					$data_bayar = $buildterimaDetail->getWhere(['nomorfaktur' => $input['kodesi'], 'cabang' => $cabang])->getRow();
					$datalogs = [
						'date' => date('Y-m-d H:i:s'),
						'userid' => $session->get('username'),
					];

					if ($param == 0) {
						$data['koderetur'] = $nobukti;
						$builder->insert($data);
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
						}


						$db->query("UPDATE tr_jurnal 
                                    set debet = debet -'$total' 
                                    where kredit='0.00' and novoucher = '$kodesi' and kodecbg='$cabang'");

						$db->query("UPDATE tr_jurnal 
                                    set kredit = kredit -'$total' 
                                    where debet='0.00' and novoucher = '$kodesi' and kodecbg='$cabang'");

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
					$ppn = ($subtot - $tdisc) * 0.11;
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
				$pdf->FancyRow(array('', '', 'PPN (11%)', number_format($ppn, 0, '.', ',')), $fc, $border, $align);
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
				$motto = '';
				$alamat = '';
				$namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
				$data  = explode("~", $x);
				$idlp  = $data[0];
				$tgl1  = $data[1];
				$tgl2  = $data[2];
				$cust  = $data[3];
				$brg   = $data[4];
				$cab   = $data[5];
				$marketing 	= $data[6];

				$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
				$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
				$_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
				$_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
				$tgl_ppn = date('2022-04-01 00:00');

				if ($_tgl1 >= $tgl_ppn) {
					$h_ppn = 0.11;
				} else {
					$h_ppn = 0.1;
				}

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
				} else 
		  if ($idlp == 102) {
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
				} else 
          if ($idlp == 103) {
					$query =
						"select ar_sifile.*, ar_customer.nama as namapelanggan from ar_sifile inner join ar_customer on ar_sifile.kodecust=ar_customer.kode 
			 where tglkirim between '$_tgl1' and '$_tgl2' ";

					if ($cust != "") {
						$query .= " and ar_sifile.kodecust = '$cust'";
					}
					if ($cab != "") {
						$query .= " and ar_sifile.kodecbg = '$cab'";
					}
					if ($marketing != "") {
						$query .= " and ar_sifile.id_sales = '$marketing'";
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
					$pdf->SetWidths(array(10, 30, 20, 50, 25, 30, 30, 30, 30, 25));
					$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
					$judul = array('No.', 'Nomor ', 'Tanggal', 'Pelanggan', 'Bruto', 'Disc', 'Netto', 'PPN', 'DPP+PPN', 'Marketing');
					$pdf->setfont('Times', 'B', 10);
					$pdf->row($judul);
					$pdf->SetWidths(array(10, 30, 20, 50, 25, 30, 30, 30, 30, 25));
					$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R'));
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
								$value->namapelanggan,
								number_format($subtot, 0, '.', ','),
								number_format($disc, 0, '.', ','),
								number_format($tdpp, 0, '.', ','),
								number_format($ppn, 0, '.', ','),
								number_format($dpp_ppn, 0, '.', ','),
								$value->nama_sales
							));
							$nourut++;
							$totbruto 	= $totbruto + $subtot;
							$totnetto 	= $totnetto + $tdpp;
							$totppn 	= $totppn + $ppn;
							$totdisc  	= $totdisc + $disc;
							$tothrg  	= $tothrg + $dpp_ppn;
						}
					}
					$pdf->SetWidths(array(110, 25, 30, 30, 30, 30, 25));
					$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));

					$pdf->setfont('Times', 'B', 9);
					$pdf->Row(array(
						'Total  ',
						number_format($totbruto, 0, ',', '.'),
						number_format($totdisc, 0, ',', '.'),
						number_format($totnetto, 0, ',', '.'),
						number_format($totppn, 0, ',', '.'),
						number_format($tothrg, 0, ',', '.'),
						''
					));


					$pdf->SetTitle('LAPORAN PENJUALAN GLOBAL');
					$pdf->AliasNbPages();
					$pdf->output('Laporan_Penjualan_Global_103.PDF', 'I');
					exit;
				} else
					//uang muka penjualan  
					if ($idlp == 104) {
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
					} else 	  
		  if ($idlp == 105) {
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
							$pdf->SetWidths(array(10, 30, 30, 18, 65, 15, 20, 15, 20, 20, 35));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
							$judul = array('No.', 'No Retur', 'No Faktur', 'Tanggal', 'Nama Barang', 'Qty', 'Harga', 'Disc', 'Netto', 'Marketing', 'Keterangan');
							$pdf->setfont('Times', 'B', 10);
							$pdf->row($judul);
							$pdf->SetWidths(array(10, 30, 30, 18, 65, 15, 20, 15, 20, 20, 35));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'L'));
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
										$value->koderetur, $value->kodesi,
										date('d-m-Y', strtotime($value->tglretur)),
										$value->namabarang, $value->qtyretur,
										number_format($value->hargajual, 0, '.', ','),
										$value->disc, number_format($total_harga, 0, '.', ','),
										$row->nama_sales, $db->keterangan
									));
									$totqty = $totqty + $value->qtyretur;
									$tothrgjual = $tothrgjual + $value->hargajual;
									$totdisc = $totdisc + $value->disc;
									$tothrg = $tothrg + $total_harga;
									$nourut++;
								}
							}


							$pdf->SetWidths(array(153, 15, 20, 15, 20, 20, 35));
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
					} else
						//PENJUALAN per pelanggan	  
						if ($idlp == 106) {
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
								$pdf->SetWidths(array(10, 30, 25, 90, 20, 25, 20, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'No Faktur', 'Tanggal', 'Nama Barang', 'Qty', 'Harga', 'Disc', 'Netto', 'Marketing');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 30, 25, 90, 20, 25, 20, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'R'));
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
											$row->qtysi,
											number_format($row->hargajual, 0, '.', ','),
											$row->disc,
											number_format($total_harga, 0, '.', ','),
											$row->nama_sales
										));

										$totqty = $totqty + $db->qtysi;
										$tothrgjual = $tothrgjual + $db->hargajual;
										$totdisc = $totdisc + $db->disc;
										$tothrg = $tothrg + $total_harga;
										$nourut++;
									}
								}
								$pdf->SetWidths(array(155, 20, 25, 20, 25, 25));
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
						} else
							//penjualan per barang
							if ($idlp == 107) {
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
							} else
								//Faktur belum lunas
								if ($idlp == 201) {
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
											$nourut, $row->namapelanggan,
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
								} else
									//Umur Piutang
									if ($idlp == 203) {
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
												$nourut, $row->nama,
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
			} else {

				header('location:' . base_url());
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

					$tgl_ppn = date('2022-04-01 00:00');

					if ($_tgl1 >= $tgl_ppn) {
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
			$query = "SELECT a.tglsi, a.kodecust, a.faktur_pajak, b.nama, b.npwp, b.nama_pt, c.* 
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
				$query_barang = $db->query("SELECT namabarang from inv_barang where kodeitem='$row->kodeitem' ")->getRow();
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
					$query_barang->namabarang,
					$row->qtysi,
					$row->satuan,
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
					$row->nama,
					$row->nama_pt,
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
				$tgl_ppn = date('2022-04-01 00:00');

				if ($_tgl1 >= $tgl_ppn) {
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
	}
