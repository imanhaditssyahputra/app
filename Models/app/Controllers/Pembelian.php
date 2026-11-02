<?php

namespace App\Controllers;

use Config\Validation;
use App\Models\Global_model;
use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_rpt_helper;

class Pembelian extends BaseController
{
	// =========================== Suplier Section Start ======================
	//Suplier Page Start Here
	public function suplier()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$dataLogin = $model_Global->_Autono('SP');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 311);
		if ($akses->uadd == 1) {
			$tambah = 'Supplier Baru';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Suplier',
			'body' => 'admin/pembelian/suplier',
			'nourut' => $dataLogin,
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function suplier_add()
	{
		$model_Global =  new Global_model();
		$validation =  \Config\Services::validation();
		$session = session();
		$db = db_connect();
		$builder = $db->table('ap_supplier');
		$input = $this->request->getPost();
		$validation->setRule('kode', 'Kode', 'required');
		$validation->setRule('telp', 'Telp', 'required');
		$validation->setRule('nama', 'Nama', 'required');
		$validation->setRule('alamat1', 'Alamat 1', 'required');
		$validation->setRule('kota', 'Kota', 'required');
		if ($validation->withRequest($this->request)->run()) {
			$data = [
				'kodecbg' => $session->get('cabang'),
				'kode' => $input['kode'],
				'nama' => $input['nama'],
				'alamat1' => $input['alamat1'],
				'alamat2' => $input['alamat2'],
				'kota' => $input['kota'],
				'kodepos' => $input['kodepos'],
				'pkp' => $input['pkp'],
				'telp' => $input['telp'],
				'npwp' => $input['npwp'],
				'fax' => $input['fax'],
				'hp' => $input['hp'],
				'contactname' => $input['contactname'],
				'email' => $input['email'],
				'top' => $input['top'],
				'kode_user' => $session->get('username'),
				'tglentry' => date('Y-m-d H:i:s'),
			];
			$datalogs = [
				'detail' => 'tambah data supplier dengan kode ' . $input['kode'],
				'date' => date('Y-m-d H:i:s'),
				'userid' => $session->get('username'),
			];
			$result = $builder->insert($data);
			$db->table("logs")->insert($datalogs);
			$model_Global->_updatecounter1('SP');
			if ($result) {
				echo json_encode(['message' => 'inserted']);
			}
		} else {
			$arr = [
				'error' => true,
				'kodeErr' => $validation->showError('kode'),
				'telpErr' => $validation->showError('telp'),
				'namaErr' => $validation->showError('nama'),
				'alamatErr' => $validation->showError('alamat1'),
				'kotaErr' => $validation->showError('kota'),
			];
			echo json_encode($arr);
		}
	}

	public function suplier_table()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 311);
		if ($akses->uadd == 1) {
			$tambah = 'Supplier Baru';
		} else {
			$tambah = '';
		}
		$db = db_connect();
		$res = $db->table('ap_supplier')->get()->getResult();
		$result = array();
		$data = [];
		foreach ($res as $value) {
			if ($akses->udel == 1 && $akses->uedit == 1) {
				$HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="suplier_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>
                <button class="btn btn-danger mb-2" onclick="suplier_delete(' . "'" . $value->id . "'" . ',' . "'" . $value->nama . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				$HakAkses = '<button class="btn btn-danger mb-2" onclick="suplier_delete(' . "'" . $value->id . "'" . ',' . "'" . $value->nama . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="suplier_edit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
			} else {
				$HakAkses = '';
			}
			$data[] = [
				$value->kode,
				$value->nama,
				$value->alamat1,
				$value->telp,
				$value->contactname,
				$HakAkses,
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function suplier_edit($id)
	{
		$db = db_connect();
		$builder = $db->table('ap_supplier')->where('id', $id)->get()->getRow();
		echo json_encode($builder);
	}

	public function suplier_update($id)
	{
		$validation =  \Config\Services::validation();
		$session = session();
		$db = db_connect();
		$builder = $db->table('ap_supplier');
		$input = $this->request->getPost();
		$validation->setRule('kode', 'Kode', 'required');
		$validation->setRule('telp', 'Telp', 'required');
		$validation->setRule('nama', 'Nama', 'required');
		$validation->setRule('alamat1', 'Alamat 1', 'required');
		$validation->setRule('kota', 'Kota', 'required');
		if ($validation->withRequest($this->request)->run()) {
			$data = [
				'kode' => $input['kode'],
				'nama' => $input['nama'],
				'alamat1' => $input['alamat1'],
				'alamat2' => $input['alamat2'],
				'kota' => $input['kota'],
				'kodepos' => $input['kodepos'],
				'pkp' => $input['pkp'],
				'telp' => $input['telp'],
				'npwp' => $input['npwp'],
				'fax' => $input['fax'],
				'hp' => $input['hp'],
				'contactname' => $input['contactname'],
				'email' => $input['email'],
				'top' => $input['top'],
				'kode_user' => $session->get('username'),
				'tglentry' => date('Y-m-d H:i:s'),
			];
			$datalogs = [
				'detail' => 'Update data supplier dengan kode ' . $input['kode'],
				'date' => date('Y-m-d H:i:s'),
				'userid' => $session->get('username'),
			];

			$result = $builder->where('id', $id)->update($data);
			$db->table("logs")->insert($datalogs);
			if ($result) {
				echo json_encode(['message' => 'Updated']);
			}
		} else {
			$arr = [
				'error' => true,
				'kodeErr' => $validation->showError('kode'),
				'telpErr' => $validation->showError('telp'),
				'namaErr' => $validation->showError('nama'),
				'alamatErr' => $validation->showError('alamat1'),
				'kotaErr' => $validation->showError('kota'),
			];
			echo json_encode($arr);
		}
	}

	public function suplier_delete($id = '')
	{
		$db = db_connect();
		$session = session();
		$builder = $db->table('ap_supplier');
		$row = $builder->getWhere(['id' => $id])->getRow();
		$delete = $builder->delete(['id' => $id]);
		$datalogs = [
			'detail' => 'Hapus data supplier dengan kode ' . $row->kode,
			'date' => date('Y-m-d H:i:s'),
			'userid' => $session->get('username'),
		];
		$db->table("logs")->insert($datalogs);
		if ($delete) {
			echo json_encode(['message' => 'Deleted']);
		}
	}

	public function GetinfoSupplier($spl)
	{
		$session = session();
		$db = db_connect();
		$cabang = $session->get('cabang');
		$data = $db->table("ap_supplier")->getWhere(['kode' => $spl, 'kodecbg' => $cabang])->getRow();
		echo json_encode($data);
	}

	// =========================== Suplier Section End ======================
	// =========================== Pra PEMBELIAN Section End ======================
	//Pra Pembelian Page Start Here
	public function pra_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$db = db_connect();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 321);
		if ($akses->uadd == 1) {
			$tambah = 'Pra Purchase Order Baru';
		} else {
			$tambah = '';
		}

		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pra Pembelian',
			'body' => 'admin/pembelian/transaksi/pra_pembelian',
			'tambah' => $tambah,
		];
		return view('layout/layout', $data);
	}

	public function pra_pembelian_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$nobukti = $model_Global->_Autonomor('PPO');

		$builder = $db->query("SELECT * FROM inv_barang where kdcbg='$cabang' and kdgudang !='' group by kdgudang")->getRow();
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pra Pembelian',
			'body' => 'admin/pembelian/transaksi/pra_pembelian_entri',
			'id' => $id ?? '',
			'nomor' => $nobukti,
			'cabang' => $cabang,
			'gudang' => $builder->kdgudang,
		];
		return view('layout/layout', $data);
	}

	public function pra_pembelian_add()
	{
		error_reporting(0);
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$input = $this->request->getPost();
		$param = $input['id'];
		$db = db_connect();

		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 321);
		try {
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$cabang   		= $session->get('cabang');
				$nomorbukti  	= $input['nomorbukti'];
				$date  	  	    = $input['tanggal'];
				$kode  			= $input['kode'];
				$qty   			= $input['qty'];
				$sat   			= $input['sat'];
				$harga1   		= $input['harga'];
				$db->transStart();
				$DataShowPrapofile = $db->query("select * from ap_prapofile where kodeprapo='$nomorbukti' and kodecbg='$cabang'")->getRow();
				if (empty($DataShowPrapofile)) {
					$nobukti = $nomorbukti;
				} else {
					$nobukti = $model_Global->_Autonomor('PPO');
				}
				$dataHeader = array(
					'kodecbg' 		=> $cabang,
					'tglprapo'   	=> date('Y-m-d', strtotime($date)),
					'tglrekam'		=> date('Y-m-d H:i:s', strtotime($date . date('H:i:s'))),
					'keterangan'    => $input['keterangan'],
					'userid'		=> $session->get('username'),
				);
				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				$builder = $db->table('ap_prapofile');
				$builderD = $db->table('ap_prapodetail');
				if ($param == 0) {
					$datalogs['detail'] = 'tambah data Pembelian prapo dengan nobukti ' . $nobukti;
					$dataHeader['kodeprapo'] = $nobukti;
					$dataHeader['statusid'] = 1;
					$builder->insert($dataHeader);
					$db->table("logs")->insert($datalogs);
					$jumdata  = count($kode);
				} else {
					$datalogs['detail'] = 'update data Pembelian prapo dengan nobukti ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					$builder->where(['kodeprapo' => $nomorbukti, 'kodecbg' => $cabang])->update($dataHeader);

					if ($DataShowPrapofile->statusid == 3) {

						$DataDetail = $builderD->getWhere(['kodeprapo' => $input['nomorbukti'], 'cabang' => $session->get('cabang')])->getResult();
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
								$differentData[] = ['kodeitem' => $view->kodeitem, 'qtyprapo' => $view->qtyprapo, 'qtykirim' => $view->qtykirim];
							}
						}
						$differentData;
						$jumdatasts  = count($differentData);

						for ($i = 0; $i < $jumdatasts; $i++) {
							$where = [
								'kodeprapo' => $input['nomorbukti'],
								'kodeitem' => $differentData[$i]['kodeitem'],
								'cabang' => $session->get('cabang'),
							];

							$dataUpdate = [
								'qtyprapo' => $differentData[$i]['qtykirim'],
							];
							$builderD->where($where)->update($dataUpdate);
						}
					} else {
						$jumdata  = count($kode);
						$builderD->delete(['kodeprapo' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
					}
				}
				if ($DataShowPrapofile->statusid == 1 || $param == 0) {
					for ($i = 0; $i < $jumdata; $i++) {
						$harga = str_replace(',', '', $harga1[$i]);
						$datad = array(
							'satuan'        => $sat[$i],
							'hargabeli'     => $harga,
							'cabang'        => $cabang,
						);
						if ($kode[$i] != "") {
							if ($param == 0) {
								$datad['kodeprapo'] = $nobukti;
								$datad['kodeitem'] = $kode[$i];
								$datad['qtyprapo'] = $qty[$i];
								$builderD->insert($datad);
							} else {
								if ($DataShowPrapofile->statusid != 3) {
									$datad['kodeprapo'] = $nomorbukti;
									$datad['kodeitem'] = $kode[$i];
									$datad['qtyprapo'] = $qty[$i];
									$builderD->insert($datad);
								}
							}
						}
					}
				}

				if ($param == 0) {
					$model_Global->_updatecounter1('PPO');
					echo json_encode(['status' => 1, 'message' => $nobukti]);
				} else {
					if ($DataShowPrapofile->statusid == 3) {
						$data_prapodetail = $db->query("SELECT SUM(qtyprapo) AS qtyprapo, SUM(qtykirim) AS qtykirim FROM ap_prapodetail where kodeprapo='$nomorbukti' and cabang='$cabang'")->getRow();
						if ($data_prapodetail->qtykirim >= $data_prapodetail->qtyprapo) {
							$builder->where(['kodeprapo' => $nomorbukti, 'kodecbg' => $cabang])->update(['statusid' => 4]);
						}
					}
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
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

	public function pra_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 321);
		$db = db_connect();
		$query = "SELECT * FROM ap_prapofile where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglprapo between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tglprapo)= (select periode_tahun from ms_identity) and
            month(tglprapo)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->statusid != 4) {
				if ($value->statusid == 1) {
					$status = '<span class="badge badge-primary">Proses Menunggu PO</span>';
				} else if ($value->statusid == 2) {
					$status =  '<span class="badge badge-success">Barang Diterima Lengkap</span>';
				} else if ($value->statusid == 3) {
					$status =  '<span class="badge badge-warning">Barang Pendingan PRA PO</span>';
				}

				if ($akses->udel == 1 && $akses->uedit == 1) {
					if ($value->statusid == 1) {
						$editview = '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
						$delete = '';
					}
				} else if ($akses->udel == 1 && $akses->uedit == 0) {
					$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
					$editview = '';
				} else if ($akses->udel == 0 && $akses->uedit == 1) {
					if ($value->statusid == 1) {
						$editview = '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
						$delete = '';
					}
				} else {
					$editview = '';
					$delete = '';
				}
				$count++;
				$data[] = [
					$count,
					$value->kodeprapo,
					$value->tglprapo,
					$value->keterangan,
					$status,
					$editview,
					$delete
				];
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function pra_pembelian_table_bufferStock()
	{
		error_reporting(0);
		$session = session();
		$cabang = $session->get('cabang');
		$db = db_connect();
		$res = $db->query("SELECT kodeitem, namabarang, tglbeliakhir, stok, stok_min, TIMESTAMPDIFF( 
			MONTH , tglbeliakhir, NOW() ) AS bulan FROM inv_barang where kdcbg='$cabang' and stok <= stok_min group by kodeitem order by namabarang ")->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->stok_min != 0) {
				$count++;
				$data[] = [
					$value->kodeitem,
					$value->namabarang,
					$value->tglbeliakhir,
					$value->stok,
					$value->stok_min,
				];
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function pra_pembelian_delete($id)
	{
		$db = db_connect();
		$session = session();
		$db->transStart();
		$builder = $db->table('ap_prapofile');
		$result = $builder->getWhere(['id' => $id])->getRow();
		$datalogs = [
			'detail' => 'Hapus data Pembelian prapo dengan kode ' . $result->kodeprapo,
			'date' => date('Y-m-d H:i:s'),
			'userid' => $session->get('username'),
		];
		$db->table("logs")->insert($datalogs);

		$builderD = $db->table('ap_prapodetail');
		$delete = $builder->delete(['kodeprapo' => $result->kodeprapo]);
		$delete = $builderD->delete(['kodeprapo' => $result->kodeprapo]);

		if ($delete) {
			echo json_encode(['message' => 'Deleted']);
		}
		$db->transComplete();
	}

	//Pesanan Pembelian Page Start Here
	public function pesanan_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 322);
		if ($akses->uadd == 1) {
			$tambah = 'Purchase Order Baru';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pesanan Pembelian',
			'body' => 'admin/pembelian/transaksi/pasananpembelian',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function pesanan_pembelian_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$detail = $db->query("SELECT * FROM ap_prapofile  where statusid=1 or statusid=3 group by kodeprapo")->getResult();
		$builder = $db->query("SELECT * FROM inv_barang where kdcbg='$cabang' and kdgudang !='' group by kdgudang")->getRow();
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pembelian',
			'body' => 'admin/pembelian/transaksi/pesananpembelianentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PO'),
			'nomor_ppn' => $model_Global->_Autonomor('POP'),
			'cabang' => $cabang,
			'gudang' => $builder->kdgudang,
			'noprapo' => $detail,
		];
		return view('layout/layout', $data);
	}

	public function changesupphargapercust($supp, $kode)
	{
		error_reporting(0);
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$db->transStart();
		$session = session();
		$level = $session->get('level');
		$cabang = $session->get('cabang');
		$akses = $model_Global->cek_menu_akses($level, 322);
		// Table
		$builder 		= $db->table('inv_barang_harga');
		$buildBarang 	= $db->table('inv_barang');
		if ($akses->uadd == 1 || $akses->uedit == 1) {
			$rowBarang1 = $buildBarang->getWhere(['kodeitem' => $kode, 'kdcbg' => $cabang, 'kode_spl1' => $supp])->getRow();
			$rowBarang2 = $buildBarang->getWhere(['kodeitem' => $kode, 'kdcbg' => $cabang, 'kode_spl2' => $supp])->getRow();
			$rowBarang3 = $buildBarang->getWhere(['kodeitem' => $kode, 'kdcbg' => $cabang, 'kode_spl3' => $supp])->getRow();
			$rowBarang4 = $buildBarang->getWhere(['kodeitem' => $kode, 'kdcbg' => $cabang, 'kode_spl4' => $supp])->getRow();
			if ($rowBarang1 != null) {
				$kodespl  = $rowBarang1->kode_spl1;
				$bruto  = $rowBarang1->bruto_spl1;
				$disc  = $rowBarang1->disc_spl1;
				$netto  = $rowBarang1->netto_spl1;
			} else if ($rowBarang2 != null) {
				$kodespl  = $rowBarang2->kode_spl2;
				$bruto  = $rowBarang2->bruto_spl2;
				$disc  = $rowBarang2->disc_spl2;
				$netto  = $rowBarang2->netto_spl2;
			} else if ($rowBarang3 != null) {
				$kodespl  = $rowBarang3->kode_spl3;
				$bruto  = $rowBarang3->bruto_spl3;
				$disc  = $rowBarang3->disc_spl3;
				$netto  = $rowBarang3->netto_spl3;
			} else if ($rowBarang4 != null) {
				$kodespl  = $rowBarang4->kode_spl4;
				$bruto  = $rowBarang4->bruto_spl4;
				$disc  = $rowBarang4->disc_spl4;
				$netto  = $rowBarang4->netto_spl4;
			}

			$dataHupdate = array(
				'kode_spl' => $kodespl,
				'bruto_spl' => $bruto,
				'disc_spl' => $disc,
				'netto_spl' => $netto,
			);
			$builder->where(['kodeitem' => $kode, 'cabang' => $cabang])->update($dataHupdate);
		} else {
			echo json_encode(['message' => 'Gagal']);
		}
		$db->transComplete();
	}

	public function getbarangprapo($kodeprapo, $supp)
	{
		$session = session();
		$db = db_connect();
		$cabang = $session->get('cabang');
		$data = $db->query("SELECT a.*, b.* FROM ap_prapodetail a INNER JOIN inv_barang b ON a.kodeitem = b.kodeitem
							WHERE a.kodeprapo='$kodeprapo' AND a.cabang='$cabang' AND b.kdcbg='$cabang' AND b.kode_spl1='$supp' AND a.qtyprapo > a.qtykirim
							OR a.kodeprapo='$kodeprapo' AND b.kode_spl2='$supp' AND a.cabang='$cabang' AND b.kdcbg='$cabang' AND a.qtyprapo > a.qtykirim
							OR a.kodeprapo='$kodeprapo' AND b.kode_spl3='$supp' AND a.cabang='$cabang' AND b.kdcbg='$cabang' AND a.qtyprapo > a.qtykirim 
							OR a.kodeprapo='$kodeprapo' AND b.kode_spl4='$supp' AND a.cabang='$cabang' AND b.kdcbg='$cabang' AND a.qtyprapo > a.qtykirim group by b.kodeitem")->getResult();
		echo json_encode($data);
	}

	public function pocekdatabarangsupp($kodeitem, $supp)
	{
		$session = session();
		$db = db_connect();
		$cabang = $session->get('cabang');
		$Data = $db->query("SELECT * from inv_barang_harga where kodeitem='$kodeitem' and kode_spl='$supp' and cabang='$cabang'")->getRow();
		echo json_encode($Data);
	}

	public function pesanan_pembelian_add()
	{
		error_reporting(0);
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 322);
		// Table
		$builder            = $db->table('ap_pofile');
		$builderDetail      = $db->table('ap_podetail');
		$builderBayar       = $db->table('ap_pobiaya');
		$builderPraPO       = $db->table('ap_prapofile');
		$builderPraPODetail = $db->table('ap_prapodetail');
		$builderINVBarang   = $db->table('inv_barang');
		try {
			$db->transStart();
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$nomorbukti  	= $input['nomorbukti'];
				$noprapo  		= $input['prapo'];
				$userid   		= $session->get('username');
				$ucabang   		= $session->get('cabang');
				$ppn  	  		= $input['sppn'];

				$no_auto = $builder->getWhere(['kodepo' => $nomorbukti, 'kodecbg' => $ucabang])->getRow();

				if (empty($no_auto)) {
					if ($ppn == 'Y') {
						$nobukti = $nomorbukti;
					} else {
						$nobukti = $nomorbukti;
					}
				} else {
					if ($ppn == 'Y') {
						$nobukti = $model_Global->_Autonomor('POP');
					} else {
						$nobukti = $model_Global->_Autonomor('PO');
					}
				}

				if ($param != 0) {
					if ($no_auto->statusid == 3) {
						$DataDetail = $builderDetail->getWhere(['kodepo' => $nomorbukti])->getResult();
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
								'kodepo' => $nomorbukti,
								'kodeitem' => $differentData[$i]['kodeitem'],
							];

							$dataUpdate = [
								'qtyorder' => $differentData[$i]['qtykirim'],
							];
							$builderDetail->where($where)->update($dataUpdate);
						}
					} else {
						$dataDetailPO = $builderDetail->getWhere(['kodepo' => $nomorbukti])->getResult();
						foreach ($dataDetailPO as $view) {
							$data = $db->query("UPDATE ap_prapodetail set qtykirim = qtykirim - '$view->qtyorder' where kodeprapo='$noprapo' and cabang='$ucabang' and kodeitem='$view->kodeitem'");
						}
						$builderDetail->delete(['kodepo' => $nomorbukti]);
					}
				}
				if ($no_auto->statusid == 1 || $param == 0) {

					$kode   = $input['kode'];
					$qty    = $input['qty'];
					$sat    = $input['sat'];
					$harga1 = $input['harga'];
					$disc   = $input['disc'];

					$jumdata  = count($kode);

					$tot = 0;
					$tdisc = 0;

					for ($i = 0; $i < $jumdata; $i++) {

						$harga = str_replace(',', '', $harga1[$i]);

						$vjum  = $qty[$i] * $harga;
						$vdisc = $vjum * ($disc[$i] / 100);
						$tot   = $tot + $vjum;
						$tdisc = $tdisc + $vdisc;

						$datad = array(
							'kodeitem' => $kode[$i],
							'qtyorder' => $qty[$i],
							'satuan' => $sat[$i],
							'hargabeli' => $harga,
							'disc' => $disc[$i],
						);

						if ($kode[$i] != "") {
							if ($param == 0) {
								$datad['kodepo'] = $nobukti;
								$builderDetail->insert($datad);
								$db->query("UPDATE ap_prapodetail set qtykirim = qtykirim + '$qty[$i]' where kodeprapo = '$noprapo' and kodeitem='$kode[$i]' and cabang='$ucabang'");
							} else {
								if ($no_auto->statusid != 3) {
									$datad['kodepo'] = $nomorbukti;
									$builderDetail->insert($datad);
									$db->query("UPDATE ap_prapodetail set qtykirim = qtykirim + '$qty[$i]' where kodeprapo = '$noprapo' and kodeitem='$kode[$i]' and cabang='$ucabang'");
								}
							}
							$builderINVBarang->where(['kodeitem' => $kode[$i], 'kdcbg' => $ucabang])->update(['tglbeliakhir' => $input['tanggal']]);
						}
					}
				}

				if ($input['sppn'] == "Y") {
					$tppn = ($tot - $tdisc) * 0.11;
				} else {
					$tppn = 0;
				}
				//rincian biaya lain-lain
				$kodeakun   = $input['kodeakun'];
				$jumlah     = $input['ljumlah'];
				$ket        = $input['lket'];
				$jumdata  = count($kodeakun);
				if ($param != 0) {
					$builderBayar->delete(['kodepo' => $nomorbukti]);
				}
				$totbiaya = 0;
				for ($i = 0; $i < $jumdata; $i++) {
					$totbiaya = $totbiaya + $jumlah[$i];
					$datad = array(
						'kodeakun' => $kodeakun[$i],
						'jumlah' => $jumlah[$i],
						'keterangan' => $ket[$i],
					);
					if ($kodeakun[$i] != "") {
						if ($param == 0) {
							$datad['kodepo'] = $nobukti;
							$builderBayar->insert($datad);
						} else {
							$datad['kodepo'] = $nomorbukti;
							$builderBayar->insert($datad);
						}
					}
				}

				$data = array(
					'kodecbg' 		=> $session->get('cabang'),
					'kodesup' 		=> $input['supp'],
					'kodeprapo'  	=> $noprapo,
					'tglpo'   		=> date('Y-m-d', strtotime($input['tanggal'])),
					'tglkirim'		=> date('Y-m-d', strtotime($input['tanggalkirim'])),
					'kodeuser'		=> $userid,
					'ket'     		=> $input['ket'],
					'dpp'     		=> $tot,
					'ppn'     		=> $tppn,
					'pembayaran'    => $input['top'],
					'diskon'  		=> $tdisc,
					'biayalain'		=> $totbiaya,
					'totalpo' 		=> $tot + $tppn + $totbiaya - $tdisc,
					'sppn'    		=> $input['sppn'],
				);

				$sumprapo = $db->query("SELECT SUM(qtyprapo) AS qtyprapo, SUM(qtykirim) AS qtykirim FROM ap_prapodetail where kodeprapo='$noprapo' and cabang='$ucabang'")->getRow();
				if ($sumprapo->qtyprapo > $sumprapo->qtykirim) {
					$builderPraPO->where(['kodeprapo' => $noprapo, 'kodecbg' => $ucabang])->update(['statusid' => 3]);
				} else {
					$builderPraPO->where(['kodeprapo' => $noprapo, 'kodecbg' => $ucabang])->update(['statusid' => 4]);
				}

				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

				if ($param == 0) {
					$data['statusid'] = 1;
					$data['no_get'] = 1;
					$data['kodepo'] = $nobukti;
					$builder->insert($data);
					$datalogs['detail'] = 'Tambah data Pembelian PO dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					if ($input['sppn'] == 'Y') {
						$model_Global->_updatecounter1('POP');
					} else {
						$model_Global->_updatecounter1('PO');
					}
					echo json_encode(['status' => 1, 'message' => $nobukti]);
				} else {
					$datalogs['detail'] = 'Edit data Pembelian PO dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					if ($no_auto->statusid != 3) {
						$data['statusid'] = 1;
						$data['no_get'] = 1;
						$data['kodepo'] = $nomorbukti;
						$builder->where(['kodepo' => $nomorbukti, 'kodecbg' => $ucabang])->update($data);
					} else {
						$dataHupdate = array(
							'ket' => $input['ket'],
							'tglpo'   => date('Y-m-d', strtotime($input['tanggal'])),
							'tglkirim'	=> date('Y-m-d', strtotime($input['tanggalkirim'])),
							'kodeuser'		=> $userid,
						);
						$total = $db->query("SELECT sum(qtyorder-qtykirim) as total from ap_podetail where kodepo = '$nomorbukti'")->getRow()->total;
						if ($total <= 0) {
							$dataHupdate['statusid'] = 4;
							$dataHupdate['no_get'] = 3;
							$builder->where(['kodepo' => $nomorbukti, 'kodecbg' => $ucabang])->update($dataHupdate);
						} else {
							$builder->where(['kodepo' => $nomorbukti, 'kodecbg' => $ucabang])->update($dataHupdate);
						}
					}
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
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

	public function po_pembelian_delete($nomor)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$db->transStart();
		$session = session();
		$level = $session->get('level');
		$cabang = $session->get('cabang');
		$akses = $model_Global->cek_menu_akses($level, 322);
		$builder            = $db->table('ap_pofile');
		$builderDetail      = $db->table('ap_podetail');
		$builderBayar       = $db->table('ap_pobiaya');
		$builderPraPO       = $db->table('ap_prapofile');
		$builderINVBarang   = $db->table('inv_barang');
		if ($akses->udel == 1 || $akses->uedit == 1) {
			$pofile = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();
			$data_podetail = $builderDetail->getWhere(['kodepo' => $pofile->kodepo])->getResult();
			foreach ($data_podetail as $row) {
				$db->query("UPDATE ap_prapodetail set qtykirim = qtykirim - '$row->qtyorder' 
									where kodeitem = '$row->kodeitem' and kodeprapo='$pofile->kodeprapo' and cabang='$cabang'");
			}
			$data_prapo = $db->query("SELECT SUM(qtyprapo) AS qtyprapo, SUM(qtykirim) AS qtykirim FROM ap_prapodetail where kodeprapo='$pofile->kodeprapo' and cabang='$cabang'")->getRow();
			if ($data_prapo->qtykirim < 1) {
				$builderPraPO->where(['kodeprapo' => $pofile->kodeprapo, 'kodecbg' => $cabang])->update(['statusid' => 1]);
			} else {
			}
			$datalogs = [
				'detail' => 'Hapus data Pembelian PO dengan kode ' . $pofile->kodepo,
				'date' => date('Y-m-d H:i:s'),
				'userid' => $session->get('username'),
			];
			$db->table("logs")->insert($datalogs);
			$delDetailpo    = $builderDetail->delete(['kodepo' => $pofile->kodepo]);
			$delBayarpo     = $builderBayar->delete(['kodepo' => $pofile->kodepo]);
			$delpofile      = $builder->delete(['id' => $nomor, 'kodecbg' => $cabang]);
			if ($delDetailpo == true && $delBayarpo == true && $delDetailpo == true) {
				echo json_encode(['message' => 'Deleted']);
			}
		}
		$db->transComplete();
	}

	public function pesanan_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 322);
		$db = db_connect();
		$query = "SELECT * FROM ap_pofile where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglpo between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tglpo)= (select periode_tahun from ms_identity) and
            month(tglpo)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->statusid != '4') {
				$podetail = $db->query("SELECT * from ap_podetail where kodepo='$value->kodepo'")->getResult();
				$tjumlah = 0;
				$tdiskon = 0;
				foreach ($podetail as $rowd) {
					$qtykode    = ($rowd->qtyorder - $rowd->qtykirim);
					$hargakode  = $rowd->hargabeli;
					$disckode   = $rowd->disc;

					$tjumlah = $tjumlah + $qtykode * $hargakode;
					$diskon = ($disckode / 100) * $qtykode * $hargakode;
					$tdiskon = $tdiskon + $diskon;
					$dpptot = $tjumlah - $tdiskon;
				}
				$pofile = $db->query("SELECT * from ap_pofile where kodepo='$value->kodepo'")->getRow();
				$totdpp = $dpptot;
				$biayalain  = $pofile->biayalain;
				$cekppn = $pofile->sppn;
				if ($cekppn == 'Y') {
					$ppn = $totdpp * 0.11;
				} else {
					$ppn = 0;
				}
				$totalPoD = $dpptot + $biayalain + $ppn;
				if ($value->statusid == 1) {
					$status = '<span class="badge badge-primary">Proses Penerimaan</span>';
				} else if ($value->statusid == 2) {
					$status =  '<span class="badge badge-success">Barang Diterima Lengkap</span>';
				} else if ($value->statusid == 3) {
					$status =  '<span class="badge badge-warning">Barang belum di terima lengkap</span>';
				}

				if ($akses->udel == 1 && $akses->uedit == 1) {
					if ($value->statusid == 1) {
						$editview = '<a href="' . base_url('pembelian/transaksi/pesananpembelian/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('pembelian/transaksi/pesananpembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
						$delete = '';
					} else {
						$editview = '';
						$delete = '';
					}
				} else if ($akses->udel == 1 && $akses->uedit == 0) {
					$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
					$editview = '';
				} else if ($akses->udel == 0 && $akses->uedit == 1) {
					if ($value->statusid == 1) {
						$editview = '<a href="' . base_url('pembelian/transaksi/pesananpembelian/entri/' . $value->id) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
						$delete = '';
					} else if ($value->statusid == 3) {
						$editview = '<a href="' . base_url('pembelian/transaksi/pesananpembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
						$delete = '';
					} else {
						$editview = '';
						$delete = '';
					}
				} else {
					$editview = '';
					$delete = '';
				}
				$count++;
				$data[] = [
					$count,
					$value->kodepo,
					$value->tglpo,
					$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
					$value->ket,
					$status,
					$akses->uharga == 1 ? number_format($totalPoD) : '0',
					$editview,
					$delete,
					$akses->ucetak == 1 ? '<a href="' . base_url('pembelian/transaksi/pesananpembeliancetak/' . $value->kodepo) . '" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print text-white"></i> Cetak</a>' : '',
				];
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function cetakpo($param)
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

			$queryh = "select * from ap_pofile inner join ap_supplier on ap_pofile.kodesup=ap_supplier.kode where kodepo = '$param'";
			$queryd = "select ap_podetail.*, inv_barang.namabarang from ap_podetail inner join inv_barang on ap_podetail.kodeitem=inv_barang.kodeitem where ap_podetail.kodepo = '$param' and kdcbg='$unit'";
			$queryb = "select sum(jumlah) as total from ap_pobiaya  where ap_pobiaya.kodepo = '$param'";

			$detil  = $db->query($queryd)->getResult();
			$header = $db->query($queryh)->getRow();
			$biaya  = $db->query($queryb)->getRow();
			$pdf = new simkeu_nota_helper();
			$pdf->setID($nama_usaha, $alamat1, $alamat2, '085156991869', $email);
			$pdf->setjudul('');
			$pdf->setsubjudul('');
			$pdf->addpage("P", "A4");
			$pdf->setsize("P", "A4");
			$pdf->SetWidths(array(70, 30, 90));
			$border = array('T', '', 'BT');
			$size   = array('0', '0', '0');
			$pdf->setfont('Arial', 'B', 18);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$align = array('L', 'C', 'L');
			$style = array('', '', 'B');
			$size  = array('12', '0', '18');
			$max   = array(5, 5, 20);
			$judul = array('Kepada :', '', 'Pesanan Pembelian');
			$fc     = array('0', '0', '0');
			$hc     = array('20', '20', '20');
			$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
			$pdf->ln(1);
			$pdf->setfont('Arial', 'B', 10);
			$pdf->SetWidths(array(70, 30, 30, 5, 55));
			$border = array('', '', '', '', '');
			$fc     = array('0', '0', '0', '0', '0');
			//$pdf->SetFillColor(230,230,230);			
			$pdf->setfont('Arial', '', 9);
			$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodepo), $fc, $border);
			$pdf->FancyRow(array('Alamat  :  ' . $header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglpo))), $fc, $border);
			$pdf->FancyRow(array($header->alamat2, '', 'Tgl. Kirim', ':', date('d M Y', strtotime($header->tglkirim))), $fc, $border);
			$pdf->FancyRow(array('Contact Person :', '', '', '', ''), $fc, $border);
			$fc     = array('0', '0', '0', '0', '0');
			$pdf->FancyRow(array('telp                    : ' . $header->telp, '', '', '', ''), $fc, $border);
			$pdf->ln(2);

			$pdf->SetWidths(array(30, 60, 20, 25, 25, 30));
			$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R');
			$pdf->setfont('Arial', 'B', 10);
			$pdf->SetAligns(array('L', 'C', 'R'));
			$fc = array('0', '0', '0', '0', '0', '0');
			$judul = array('Kode Barang', 'Nama Barang', 'Qty', 'Harga', 'Diskon', 'Total Harga');
			$pdf->FancyRow2(8, $judul, $fc, $border, $align);
			$border = array('', '', '');
			$pdf->setfont('Arial', '', 10);
			$tot = 0;
			$subtot = 0;
			$tdisc  = 0;
			$border = array('', '', '', '', '', '');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R');
			$fc = array('0', '0', '0', '0', '0', '0');
			$pdf->SetFillColor(0, 0, 139);
			$pdf->settextcolor(0);
			foreach ($detil as $db) {
				$dpp = $db->qtyorder * $db->hargabeli;
				$dis = ($db->disc / 100) * $dpp;
				$jum = $dpp - $dis;
				$tot = $tot + $jum;

				$subtot = $subtot + $dpp;
				$tdisc  = $tdisc + $dis;
				$tdpp = $subtot - $tdisc;

				$pdf->FancyRow(array(
					$db->kodeitem,
					$db->namabarang,
					$db->qtyorder,
					number_format($db->hargabeli, 0, '.', ','),
					$db->disc,
					number_format($jum, 0, '.', ',')
				), $fc, $border, $align);
			}


			if ($header->sppn == "Y") {
				$ppn = $tdpp * 0.11;
			} else {
				$ppn = 0;
			}
			$biayalain = $biaya->total;
			$tot = $tot + $ppn + $biayalain;
			$pdf->SetFillColor(230);
			$border = array('B', 'B', 'B', 'B', 'B', 'B');
			$pdf->FancyRow(array('', '', '', '', '', ''), 0, $border);
			$pdf->ln(2);
			$pdf->SetWidths(array(100, 20, 30, 40));
			$border = array('TB', '', 'T', 'T');
			$align  = array('L', '', 'L', 'R');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->settextcolor(0);
			$fc = array('0', '0', '0', '0');
			$pdf->FancyRow(array('Keterangan', '', 'Sub Total', number_format($subtot, 0, '.', ',')), $fc, $border, $align, 0);
			$border = array('', '', '', '');
			$pdf->FancyRow(array($header->ket, '', 'Diskon', number_format($tdisc, 0, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'DPP', number_format($tdpp, 0, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'PPN (11%)', number_format($ppn, 0, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'Biaya Lain-lain', number_format($biayalain, 0, '.', ',')), $fc, $border, $align);
			$style = array('', '', 'B', 'B');
			$size  = array('0', '0', '0', '0');
			$border = array('T', '', 'BT', 'BT');
			$fc = array('0', '0', '0', '');
			$pdf->FancyRow(array('', '', 'Total', number_format($tot, 0, '.', ',')), $fc, $border, $align, $style, $size);
			$pdf->ln(2);

			$pdf->settextcolor(0);
			$pdf->SetWidths(array(20, 3, 80));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C'));
			$fc1 = array('0', '0', '0', '0');
			$border1 = array('', '', '', '');
			$terbilang = ucwords($model_Global->terbilang($tot));
			$pdf->FancyRow(array('TEBILANG', ':', $terbilang, '', ''), $fc1, $border1);

			$pdf->settextcolor(0);
			$pdf->SetWidths(array(130, 50));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C'));
			$pdf->ln(5);

			$border = array('', '',);
			$align  = array('C', 'C');
			$pdf->FancyRow(array('', 'Bagian Pembelian'), 0, $border, $align);
			$pdf->ln(1);
			$pdf->ln(15);
			$pdf->SetWidths(array(130, 2, 49));
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$border = array('', '', 'B');
			$pdf->FancyRow(array('', '', ''), 0, $border, $align);
			$border = array('', '', '');
			$align  = array('L', 'C', 'L');
			$pdf->FancyRow(array('', '', 'Tgl.'), 0, $border, $align);
			$pdf->AliasNbPages();
			$pdf->output($param . '.PDF', 'I');
			exit;
		} else {
			header('location:' . base_url());
		}
	}

	public function cetakPenerimaan($param)
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

			$queryh = "select * from ap_pofile a inner join ap_supplier b on a.kodesup=b.kode where a.kodepo = '$param'";
			$queryd = "select * from ap_podetail a inner join inv_barang b on a.kodeitem=b.kodeitem where a.kodepo = '$param' and b.kdcbg='$unit'";

			$detil  = $db->query($queryd)->getResult();
			$header = $db->query($queryh)->getRow();
			$pdf = new simkeu_nota_helper();
			$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
			$pdf->setjudul('');
			$pdf->setsubjudul('');
			$pdf->addpage("P", "A4");
			$pdf->setsize("P", "A4");
			$pdf->SetWidths(array(70, 30, 90));
			$border = array('T', '', 'BT');
			$size   = array('0', '0', '0');
			$pdf->setfont('Arial', 'B', 18);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$align = array('L', 'C', 'L');
			$style = array('', '', 'B');
			$size  = array('11', '0', '15');
			$max   = array(5, 5, 20);
			$judul = array('Kepada :', '', 'Form Penerimaan Barang');
			$fc     = array('0', '0', '0');
			$hc     = array('20', '20', '20');
			$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
			$pdf->ln(1);
			$pdf->setfont('Arial', 'B', 9);
			$pdf->SetWidths(array(70, 30, 30, 5, 55));
			$border = array('', '', '', '', '');
			$fc     = array('0', '0', '0', '0', '0');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->setfont('Arial', '', 8);


			$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodepo), $fc, $border);
			$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d-m-Y', strtotime($header->tglkirim))), $fc, $border);
			$pdf->FancyRow(array($header->alamat2, '', '', '', ''), $fc, $border);
			$fc     = array('0', '0', '0', '0', '0');
			$pdf->FancyRow(array($header->telp, '', '', '', ''), $fc, $border);

			$pdf->SetAligns(array('L', 'C', 'R'));
			$fc = array('0', '0', '0', '0', '0', '0', '0');
			$pdf->SetWidths(array(20, 51, 15, 17, 35, 20, 17, 15));
			$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
			$align  = array('C', 'L', 'R', 'C', 'C', 'C', 'C', 'C');
			$pdf->setfont('Arial', 'B', 8);

			$judul = array('Kode Barang', 'Nama Barang', 'Qty Po', 'Qty Terima', 'NIE', 'No Batch', 'ED', 'Satuan');
			$pdf->FancyRow2(8, $judul, $fc, $border, $align);
			$border = array('', '', '');
			$pdf->setfont('Arial', '', 8);
			$border = array('', '', '', '', '', '', '', '');
			$align  = array('C', 'L', 'R', 'C', 'C', 'L', 'R', 'C');
			$fc = array('0', '0', '0', '0', '0', '0', '0');
			$pdf->SetFillColor(0, 0, 139);
			$pdf->settextcolor(0);
			$tot = 0;
			$qty = 0;
			foreach ($detil as $db) {
				$qty = $qty + $db->qtyorder;

				$pdf->FancyRow(array(
					$db->kodeitem,
					$db->namabarang,
					$db->qtyorder,
					'',
					$db->nie,
					'',
					'',
					$db->satuan
				), $fc, $border, $align);
				$tot++;
			}
			$border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
			$pdf->FancyRow(array('', '', '', '', '', '', '', ''), 0, $border);
			$pdf->ln(2);
			$pdf->SetWidths(array(100, 20, 30, 40));
			$border = array('TB', '', 'T', 'T');
			$align  = array('L', '', 'L', 'R');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->settextcolor(0);
			$fc = array('0', '0', '0', '0', '0', '0', '0');
			$border = array('TB', '', 'T', 'T');
			$align  = array('L', '', 'L', 'R');
			$pdf->FancyRow(array('Keterangan', '', 'Total Kuantitas', $qty), $fc, $border, $align);
			$border = array('', '', 'B', 'B');
			$pdf->FancyRow(array($header->keterangan, '', 'Jumlah Barang', $tot), $fc, $border, $align);
			$border = array('B', '', '', '');
			$pdf->FancyRow(array('', '', '', ''), $fc, $border, $align);
			$pdf->SetWidths(array(50, 210, 50));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$pdf->ln(5);
			$border = array('', '', '');
			$align  = array('C', 'C', 'C');
			$fc = array('0', '0', '0');
			$pdf->FancyRow(array('Diterima Oleh,', 'Disetujui Oleh,', ''), $fc, $border, $align);
			$pdf->ln(1);
			$pdf->ln(15);
			$pdf->SetWidths(array(49, 80, 49, 2, 49));
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
			$border = array('B', '', 'B', '', '');
			$pdf->FancyRow(array('', '', '', '', ''), 0, $border, $align);
			$border = array('', '', '', '', '');
			$align  = array('L', 'C', 'L', 'C', 'L');
			$pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', ''), 0, $border, $align);

			$pdf->AliasNbPages();
			$pdf->output($param . '.PDF', 'I');
			exit;
		} else {

			header('location:' . base_url());
		}
	}

	//***********BUKTI PENERIMAAN BARANG PAGE START HERE*************//
	public function buktipenerimaan_barang()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 3231);
		if ($akses->uadd == 1) {
			$tambah = 'Bukti Penerimaan Barang';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Bukti Penerimaan Barang',
			'body' => 'admin/pembelian/transaksi/bukti_penerimaan_barang',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function bukti_penerimaan_baranag_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 3231);
		$db = db_connect();
		$query = "SELECT * FROM ap_pofile where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglpo between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tglpo)= (select periode_tahun from ms_identity) and
            month(tglpo)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->statusid != '4') {
				$podetail = $db->query("SELECT * from ap_podetail where kodepo='$value->kodepo'")->getResult();
				$tjumlah = 0;
				$tdiskon = 0;
				foreach ($podetail as $rowd) {
					$qtykode    = ($rowd->qtyorder - $rowd->qtykirim);
					$hargakode  = $rowd->hargabeli;
					$disckode   = $rowd->disc;

					$tjumlah = $tjumlah + $qtykode * $hargakode;
					$diskon = ($disckode / 100) * $qtykode * $hargakode;
					$tdiskon = $tdiskon + $diskon;
					$dpptot = $tjumlah - $tdiskon;
				}
				$pofile = $db->query("SELECT * from ap_pofile where kodepo='$value->kodepo'")->getRow();
				$totdpp = $dpptot;
				$biayalain  = $pofile->biayalain;
				$cekppn = $pofile->sppn;
				if ($cekppn == 'Y') {
					$ppn = $totdpp * 0.11;
				} else {
					$ppn = 0;
				}
				$totalPoD = $dpptot + $biayalain + $ppn;
				if ($value->statusid == 1) {
					$status = '<span class="badge badge-primary">Proses Penerimaan</span>';
				} else if ($value->statusid == 2) {
					$status =  '<span class="badge badge-success">Barang Diterima Lengkap</span>';
				} else if ($value->statusid == 3) {
					$status =  '<span class="badge badge-warning">Barang belum di terima lengkap</span>';
				}


				$count++;
				$data[] = [
					$count,
					$value->kodepo,
					$value->tglpo,
					$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
					$value->ket,
					$status,
					$akses->uharga == 1 ? number_format($totalPoD) : '0',
					'<a href="' . base_url('pembelian/transaksi/buktipenerimaanbarang/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>'
				];
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function bukti_penerimaan_barang_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$detail = $db->query("SELECT * FROM ap_prapofile  where statusid=1 or statusid=3 group by kodeprapo")->getResult();
		$builder = $db->query("SELECT * FROM inv_barang where kdcbg='$cabang' and kdgudang !='' group by kdgudang")->getRow();
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'View Bukti Pembelian',
			'body' => 'admin/pembelian/transaksi/buktipenerimaanbarangentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PO'),
			'nomor_ppn' => $model_Global->_Autonomor('POP'),
			'cabang' => $cabang,
			'gudang' => $builder->kdgudang,
			'noprapo' => $detail,
		];
		return view('layout/layout', $data);
	}

	//***********Penerimaan Barang Page Start Here*************//
	public function penerimaan_barang()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 3232);
		if ($akses->uadd == 1) {
			$tambah = 'Receiving';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Penerimaan Barang',
			'body' => 'admin/pembelian/transaksi/penerimaan_barang',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function penerimaan_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 3232);
		$db = db_connect();
		$query = "SELECT * FROM ap_lpb where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tgllpb between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tgllpb)= (select periode_tahun from ms_identity) and
            month(tgllpb)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->no_terima == 1) {
				$status = '<span class="badge badge-primary">Surat Jalan</span>';
			} else if ($value->no_terima == 2) {
				$status =  '<span class="badge badge-success">Siap input</span>';
			} else if ($value->no_terima == 3) {
				$status =  '<span class="badge badge-dark">sudah difakturkan</span>';
			}

			if ($akses->udel == 1 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/penerimaanbarang/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
				if ($value->no_terima != 3) {
					$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
				} else {
					$delete = '';
				}
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				if ($value->no_terima != 3) {
					$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
				} else {
					$delete = '';
				}
				$editview = '';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/penerimaanbarang/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
				$delete = '';
			} else {
				$editview = '';
				$delete = '';
			}
			$count++;
			$data[] = [
				$count,
				$value->nolpb,
				$value->noterima,
				date('d-m-Y', strtotime($value->tgllpb)),
				$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
				$value->keterangan,
				$status,
				$editview,
				$delete,
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function penerimaan_barang_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$detail = $db->query("SELECT * FROM ap_prapofile  where statusid=1 or statusid=3 group by kodeprapo")->getResult();
		$builder = $db->query("SELECT * FROM inv_barang where kdcbg='$cabang' and kdgudang !='' group by kdgudang")->getRow();
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Penerimaan Barang',
			'body' => 'admin/pembelian/transaksi/penerimaanbarangentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PB'),
			'nomor_ppn' => $model_Global->_Autonomor('PBP'),
			'cabang' => $cabang,
			'gudang' => $builder->kdgudang,
			'noprapo' => $detail,
		];
		return view('layout/layout', $data);
	}

	public function listpo($kode)
	{
		$db = db_connect();
		if (!empty($kode)) {
			$po  = $db->query("SELECT * from ap_pofile where kodesup = '$kode' and no_get='1'")->getResult();
?>
			<select name="kodepo" id="kodepo" class="form-control  input-medium select2">
				<option value="">-- PILIH NO BUKTI ---</option>
				<?php
				foreach ($po  as $row) {
				?>
					<option value="<?php echo $row->kodepo; ?>"><?php echo $row->kodepo; ?></option>
				<?php
				}
				?>
			</select>

		<?php

		} else {
			echo "";
		}
	}

	public function postdatapo($kode, $gudang)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$data = $db->query("SELECT a.*, b.namabarang, a.qtyorder-a.qtykirim as sisa FROM ap_podetail a
        INNER JOIN inv_barang b ON a.kodeitem= b.kodeitem  
        where a.kodepo='$kode' and a.qtyorder > a.qtykirim and  b.kdgudang='$gudang' and b.kdcbg='$cabang'")->getResult();
		echo json_encode($data);
	}

	public function postdatapoheader($kode)
	{
		$db = db_connect();
		$session = session();
		$data = $db->table("ap_pofile")->getWhere(['kodepo' => $kode, 'kodecbg' => $session->get('cabang')])->getRow();
		echo json_encode($data);
	}

	public function data_barang_list_po($id, $no, $gudang)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$query = $db->query("SELECT a.*, b.disc, b.hargabeli, sum(qtyorder - qtykirim) as qtypo FROM inv_barang a
        inner join ap_podetail b on a.kodeitem = b.kodeitem  
        where a.kodeitem='$id' and b.kodepo='$no' and a.kdcbg='$cabang' and a.kdgudang='$gudang'")->getRow();
		echo json_encode($query);
	}

	public function terima_pembelian_add()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];

		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 3232);
		// Table
		$builder            = $db->table('ap_lpb');
		$builderDetail      = $db->table('ap_lpbdetil');
		$builderPO          = $db->table('ap_pofile');
		$builderPODetail    = $db->table('ap_podetail');
		try {
			$db->transStart();
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$nomorbukti  	= $input['nomorbukti'];
				$kodepo  		= $input['kodepo'];
				$ucabang   		= $session->get('cabang');

				$Datalpb = $builder->getWhere(['nolpb' => $nomorbukti, 'kodecbg' => $ucabang])->getRow();

				if (empty($Datalpb)) {
					if ($input['sppn'] == 'Y') {
						$nobukti = $nomorbukti;
					} else {
						$nobukti = $nomorbukti;
					}
				} else {
					if ($input['sppn'] == 'Y') {
						$nobukti = $model_Global->_Autonomor('PBP');
					} else {
						$nobukti = $model_Global->_Autonomor('PB');
					}
				}

				if ($param != 0) {

					$dataD = $builderDetail->where(['nolpb' => $nomorbukti, 'gudang' => $input['gudang']])->get()->getResult();
					foreach ($dataD as $row) {
						$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$row->qtyterima' where kodepo = '$kodepo' and kodeitem='$row->kodeitem'");
					}
					$builderDetail->delete(['nolpb' => $nomorbukti]);
				}
				$kode   = $input['kode'];
				$qty    = $input['qty'];
				$batch  = $input['no_batch'];
				$exp    = $input['tgl_exp'];
				$sat    = $input['sat'];
				$harga1 = $input['hargabeli'];
				$disc   = $input['disc'];

				$jumdata  = count($kode);

				$tot = 0;
				$tdisc = 0;

				for ($i = 0; $i < $jumdata; $i++) {

					$harga = str_replace(',', '', $harga1[$i]);

					$vjum  = $qty[$i] * $harga;
					$vdisc = $vjum * ($disc[$i] / 100);
					$tot   = $tot + $vjum;
					$tdisc = $tdisc + $vdisc;

					$DatapoDetail = $builderPODetail->getWhere(['kodepo' => $kodepo, 'kodeitem' => $kode[$i]])->getRow();

					$datad = array(
						'kodeitem' => $kode[$i],
						'qtyterima' => $qty[$i],
						'qtyorder' => $DatapoDetail->qtyorder,
						'satuan' => $sat[$i],
						'disc' => $disc[$i],
						'hargabeli' => $harga,
						'gudang' => $input['gudang'],
						'no_batch'   => $batch[$i],
						'tanggal_exp' => $exp[$i],
					);

					if ($kode[$i] != "") {
						if ($param == 0) {
							$datad['nolpb'] = $nobukti;
							$builderDetail->insert($datad);
							$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$qty[$i]' where kodepo = '$kodepo' and kodeitem='$kode[$i]'");
						} else {
							$datad['nolpb'] = $nomorbukti;
							$builderDetail->insert($datad);
							$db->query("UPDATE ap_podetail set qtykirim = qtykirim + '$qty[$i]' where kodepo = '$kodepo' and kodeitem='$kode[$i]'");
						}
					}
				}

				$data = array(
					'kodecbg'       => $ucabang,
					'kodesup'       => $input['supp'],
					'kodepo'        => $kodepo,
					'noterima'      => $input['noterima'],
					'gudang'        => $input['gudang'],
					'tgllpb'        => date('Y-m-d', strtotime($input['tanggal'])),
					'tglkirim'      => date('Y-m-d', strtotime($input['tanggalkirim'])),
					'kodeuser'      => $session->get('username'),
					'keterangan'    => $input['keterangan'],
					'alamat'        => $input['alamat'],
					'statusid'      => 1,
					'no_terima'     => $input['opsi_terima'],
				);
				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];


				if ($param == 0) {
					$data['nolpb'] = $nobukti;
					$builder->insert($data);
					$datalogs['detail'] = 'Tambah data Pembelian PO Terima dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					$sumpo = $db->query("SELECT sum(qtyorder-qtykirim) as total FROM ap_podetail where kodepo='$kodepo' ")->getRow();
					if ($sumpo->total <= 0) {
						$builderPO->where(['kodepo' => $kodepo, 'kodecbg' => $ucabang])->update(['statusid' => 2, 'no_get' => 2]);
					} else {
						$builderPO->where(['kodepo' => $kodepo, 'kodecbg' => $ucabang])->update(['statusid' => 3, 'no_get' => 1]);
					}
					if ($input['sppn'] == 'Y') {
						$model_Global->_updatecounter1('PBP');
					} else {
						$model_Global->_updatecounter1('PB');
					}
					echo json_encode(['status' => 1, 'message' => $nobukti]);
				} else {
					$data['nolpb'] = $nomorbukti;
					$builder->where(['nolpb' => $nomorbukti, 'kodecbg' => $ucabang])->update($data);
					$sumpo = $db->query("SELECT sum(qtyorder-qtykirim) as total FROM ap_podetail where kodepo='$kodepo' ")->getRow();
					if ($sumpo->total <= 0) {
						$builderPO->where(['kodepo' => $kodepo, 'kodecbg' => $ucabang])->update(['statusid' => 2, 'no_get' => 2]);
					} else {
						$builderPO->where(['kodepo' => $kodepo, 'kodecbg' => $ucabang])->update(['statusid' => 3, 'no_get' => 1]);
					}
					$datalogs['detail'] = 'Edit data Pembelian PO Terima dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
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

	public function terima_pembelian_delete($nomor)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$db->transStart();
		$session = session();
		$level = $session->get('level');
		$cabang = $session->get('cabang');
		$akses = $model_Global->cek_menu_akses($level, 3232);
		$builder            = $db->table('ap_lpb');
		$builderDetail      = $db->table('ap_lpbdetil');
		$builderPO          = $db->table('ap_pofile');
		if ($akses->udel == 1 || $akses->uedit == 1) {
			$lpb = $builder->getWhere(['id' => $nomor, 'kodecbg' => $cabang])->getRow();
			$dataD = $builderDetail->where(['nolpb' => $lpb->nolpb])->get()->getResult();
			foreach ($dataD as $row) {
				$db->query("UPDATE ap_podetail set qtykirim = qtykirim - '$row->qtyterima' where kodepo = '$lpb->kodepo' and kodeitem='$row->kodeitem'");
			}
			$data_po = $db->query("SELECT SUM(qtyorder) AS qtyorder, SUM(qtykirim) AS qtykirim FROM ap_podetail where kodepo='$lpb->kodepo'")->getRow();
			if ($data_po->qtykirim < 1) {
				$builderPO->where(['kodepo' => $lpb->kodepo, 'kodecbg' => $cabang])->update(['statusid' => 1]);
			} else {
			}
			$datalogs = [
				'detail' => 'Hapus data Pembelian PO Terima dengan kode ' . $lpb->nolpb,
				'date' => date('Y-m-d H:i:s'),
				'userid' => $session->get('username'),
			];
			$db->table("logs")->insert($datalogs);
			$delDetail  = $builderDetail->delete(['nolpb' => $lpb->nolpb]);
			$dellpb     = $builder->delete(['id' => $nomor, 'kodecbg' => $cabang]);
			if ($delDetail == true && $dellpb == true) {
				echo json_encode(['message' => 'Deleted']);
			}
		}
		$db->transComplete();
	}

	//***********FAKTUR PEMBELIAN Barang Page Start Here*************//
	//Faktur Pembelian Page Start Here
	public function faktur_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 324);
		if ($akses->uadd == 1) {
			$tambah = 'DAFTAR PENERIMAAN BARANG BARU';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Faktur Pembelian',
			'body' => 'admin/pembelian/transaksi/faktur_pembelian',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function faktur_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 324);
		$db = db_connect();
		$query = "SELECT * FROM ap_pufile where kodecbg='$cabang'";
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
			$hitung = ($value->totalpu - $value->jumlahbayar);
			if ($value->statusid == '1' && $value->pembayaran === 'K' && $hitung != 0 && $hitung != 0.00) {
				$status = '<span class="badge badge-warning">Belum Lunas</span>';
			} else if ($value->statusid == '2' && $hitung <= 0 && $hitung <= 0.00) {
				$status =  '<span class="badge badge-success">Lunas</span>';
			} else if ($value->statusid == '1' && $value->pembayaran === 'T' && $hitung != 0 && $hitung != 0.00) {
				$status =  '<span class="badge badge-primary">Tunai</span>';
			} else {
				$status = '';
			}
			if ($akses->udel == 1 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/fakturpembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
				$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
				$editview = '';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/fakturpembelian/entri/' . $value->id) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
				$delete = '';
			} else {
				$editview = '';
				$delete = '';
			}
			$count++;
			$data[] = [
				$count,
				$value->kodepu,
				date('d-m-Y', strtotime($value->tglkirim)),
				$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
				$value->ket,
				$status,
				$value->retur == 1 ? '<span class="badge badge-primary">Tidak ada retur</span>' : '<span class="badge badge-success">Barang Teretur</span>',
				date('d-m-Y', strtotime($value->tglpu)),
				date('d-m-Y', strtotime($value->tgljthtempo)),
				$akses->uharga == 1 ? number_format($value->totalpu) : '0',
				$akses->uharga == 1 ? number_format($value->jumlahbayar + $value->uangmuka) : '0',
				$editview,
				'',
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function faktur_pembelian_entri($id = '')
	{
		$session = session();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 324);
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Faktur Pembelian',
			'body' => 'admin/pembelian/transaksi/fakturpembelianentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PU'),
			'nomor_ppn' => $model_Global->_Autonomor('PUP'),
			'cabang' => $cabang,
			'akses' => $akses,
		];
		return view('layout/layout', $data);
	}

	public function listpb($kode)
	{
		$db = db_connect();
		if (!empty($kode)) {
			$pb  = $db->query("select * from ap_lpb where nolpb not in(select kodepb from ap_pufile where kodesup='$kode') and kodesup='$kode' and no_terima = 2")->getResult();
		?>
			<select name="kodepb" id="kodepb" class="form-control  input-medium select2">
				<option value="">-- PILIH NO BUKTI ---</option>
				<?php
				foreach ($pb  as $row) {
				?>
					<option value="<?php echo $row->nolpb; ?>"><?php echo $row->nolpb; ?></option>
				<?php
				}
				?>
			</select>

		<?php

		} else {
			echo "";
		}
	}

	public function postdatapb($kode)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$data = $db->query("SELECT a.*, b.namabarang, b.kodeitem, c.noterima, c.keterangan, c.alamat, c.kodepo
        FROM ap_lpbdetil a INNER JOIN inv_barang b ON b.kodeitem = a.kodeitem 
        INNER JOIN ap_lpb c on c.nolpb = a.nolpb where a.nolpb = '$kode' and b.kdcbg='$cabang'  GROUP BY a.id")->getResult();
		echo json_encode($data);
	}

	public function faktur_pembelian_save()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];

		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 324);
		// Table
		$builder            = $db->table('ap_pufile');
		$builderDetail      = $db->table('ap_pudetail');
		$builderlpb         = $db->table('ap_lpb');
		$builderINVBarang   = $db->table('inv_barang');
		$INVBarangBatch     = $db->table('inv_nobatch');
		$INVhargapercust    = $db->table('inv_barang_harga');
		$INVtransaksi       = $db->table('inv_transaksi');
		try {
			$db->transStart();
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$nomorbukti = $input['nomorbukti'];
				$cabang   	= $session->get('cabang');
				$tanggal  	= $input['tanggal'];
				$_vvdiskon	= str_replace(",", "", $input['vdiskon']);
				$_vvdpp		= str_replace(",", "", $input['vdpp']);
				$_vvppn		= str_replace(",", "", $input['vppn']);
				$_vvbiayadll = str_replace(",", "", $input['vbiayalain']);
				$_vvtotal	= str_replace(",", "", $input['vtotal']);

				$pufile = $builder->getWhere(['kodepu' => $nomorbukti, 'kodecbg' => $cabang])->getRow();
				if (empty($pufile)) {
					if ($input['sppn'] == 'Y') {
						$nobukti = $nomorbukti;
					} else {
						$nobukti = $nomorbukti;
					}
				} else {
					if ($input['sppn'] == 'Y') {
						$nobukti = $model_Global->_Autonomor('PUP');
					} else {
						$nobukti = $model_Global->_Autonomor('PU');
					}
				}

				if ($param != 0) {
					$builderDetail->delete(['kodepu' => $nomorbukti]);
				}
				$kode       = $input['kode'];
				$qty        = $input['qty'];
				$sat        = $input['sat'];
				$no_batch   = $input['no_batch'];
				$tgl_exp    = $input['tgl_exp'];
				$harga1     = $input['harga'];
				$disc       = $input['disc'];

				$jumdata  = count($kode);

				$tot = 0;
				$tdisc = 0;

				for ($i = 0; $i < $jumdata; $i++) {
					$harga = str_replace(',', '', $harga1[$i]);
					$vjum  = $qty[$i] * $harga;
					$vdisc = $vjum * ($disc[$i] / 100);
					$netto_brg = $harga - ($disc[$i] / 100) * $harga;
					$tot   = $tot + $vjum;
					$tdisc = $tdisc + $vdisc;

					$DataInv = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $input['gudang']])->getRow();

					$datad = array(
						'kodeitem' => $kode[$i],
						'qtypu' => $qty[$i],
						'satuan' => $sat[$i],
						'no_batch' => $no_batch[$i],
						'tanggal_exp' => $tgl_exp[$i],
						'hargabeli' => $harga,
						'disc' => $disc[$i],
						'gudang' => $input['gudang'],
					);

					$data_batch = array(
						'no_batch'   => $no_batch[$i],
						'kd_jenis'   => 'Pembelian',
						'kodeitem' => $kode[$i],
						'qty_terima' => $qty[$i],
						'qty_saldo' => $qty[$i],
						'gudang' => $input['gudang'],
						'cabang' => $cabang,
						'tanggal_exp' => $tgl_exp[$i],
					);

					$data_inv = array(
						'tanggal'   => date('Y-m-d'),
						'kodeitem' => $kode[$i],
						'penerimaan' => $qty[$i],
						'pengeluaran' => 0,
						'satuan' => $sat[$i],
						'jenis' => 'Pembelian',
						'nobukti' => $nobukti,
						'tglrekam'   => date('Y-m-d H:i:s'),
						'hpp'   => $harga,
						'hargajual'   => 0,
						'gudang' => $input['gudang'],
						'no_batch'   => $no_batch[$i],
						'tanggal_exp' => $tgl_exp[$i],
						'cabang' => $cabang,
					);

					$data_harga_percustomer = array(
						'kodeitem' => $kode[$i],
						'nama_barang' => $DataInv->namabarang,
						'hargamodal_spl' => $harga,
						'kode_spl' => $input['supp'],
						'bruto_spl' => $harga,
						'disc_spl' => $disc[$i],
						'netto_spl' => $harga - ($disc[$i] / 100) * $harga,
						'tanggal' => $input['tanggal'],
						'cabang' => $cabang,
						'gudang' => $input['gudang'],
						'tglrekam' => date('Y-m-d H:i:s'),
						'userid' => $session->get('username'),
					);

					$update_hargapercs = array(
						'kode_spl'   => $input['supp'],
						'bruto_spl' => $harga,
						'disc_spl' => $disc[$i],
						'netto_spl' => $harga - ($disc[$i] / 100) * $harga,
					);

					$update_spl_barang = array(
						'kode_spl1'   => $input['supp'],
						'bruto_spl1' => $harga,
						'disc_spl1' => $disc[$i],
						'netto_spl1' => $harga - ($disc[$i] / 100) * $harga,
					);

					$view_batch = $INVBarangBatch->getWhere(['no_batch' => $no_batch[$i], 'kodeitem' => $kode[$i],  'cabang' => $cabang, 'gudang' => $input['gudang']])->getRow();
					$view_percustomer = $db->query("SELECT * FROM inv_barang_harga where kodeitem='$kode[$i]' and cabang='$cabang' group by kodeitem");
					if ($kode[$i] != "") {
						if ($param == 0) {
							$datad['kodepu'] = $nobukti;
							$builderDetail->insert($datad);
							$INVtransaksi->insert($data_inv);
							if (empty($view_batch)) {
								$INVBarangBatch->insert($data_batch);
							} else {
								$ResultUpdateStokBarangBatch = [
									'qty_terima ' => $view_batch->qty_terima + $qty[$i],
									'qty_saldo ' => $view_batch->qty_saldo + $qty[$i],
									'tanggal_exp' => $tgl_exp[$i]
								];
								$INVBarangBatch->where(['no_batch' => $no_batch[$i], 'kodeitem' => $kode[$i],  'cabang' => $cabang, 'gudang' => $input['gudang']])->update($ResultUpdateStokBarangBatch);
							}

							if (empty($view_percustomer)) {
								$INVhargapercust->insert($data_harga_percustomer);
							} else {
								$INVhargapercust->where(['kodeitem' => $kode[$i]])->update($update_hargapercs);
							}

							if ($DataInv->kode_spl1 == $input['supp'] || empty($DataInv->kode_spl1)) {
								$builderINVBarang->where(['kodeitem' => $kode[$i]])->update($update_spl_barang);
							} else if ($DataInv->kode_spl2 == $input['supp'] || empty($DataInv->kode_spl2)) {
								$spl2InvBarang = array(
									'kode_spl2'   => $input['supp'],
									'bruto_spl2' => $harga,
									'disc_spl2' => $disc[$i],
									'netto_spl2' => $harga - ($disc[$i] / 100) * $harga
								);
								$builderINVBarang->where(['kodeitem' => $kode[$i]])->update($spl2InvBarang);
							} else if ($DataInv->kode_spl3 == $input['supp'] || empty($DataInv->kode_spl3)) {
								$spl3InvBarang = array(
									'kode_spl3'   => $input['supp'],
									'bruto_spl3' => $harga,
									'disc_spl3' => $disc[$i],
									'netto_spl3' => $harga - ($disc[$i] / 100) * $harga
								);
								$builderINVBarang->where(['kodeitem' => $kode[$i]])->update($spl3InvBarang);
							} else if ($DataInv->kode_spl4 == $input['supp'] || empty($DataInv->kode_spl4)) {
								$spl4InvBarang = array(
									'kode_spl4'   => $input['supp'],
									'bruto_spl4' => $harga,
									'disc_spl4' => $disc[$i],
									'netto_spl4' => $harga - ($disc[$i] / 100) * $harga
								);
								$builderINVBarang->where(['kodeitem' => $kode[$i]])->update($spl4InvBarang);
							}
						} else {
							$datad['kodepu'] = $nomorbukti;
							$builderDetail->insert($datad);
						}
					}
				}

				//Table 2
				$buildApUangMuka    = $db->table('ap_uangmuka');
				$buildApPuBiaya     = $db->table('ap_pubiaya');
				$buildApBayar       = $db->table('ap_bayar');
				$buildApBayarD      = $db->table('ap_bayardetil');
				$builderPO          = $db->table('ap_pofile');
				$buildersupplier    = $db->table('ap_supplier');
				//rincian biaya lain-lain
				$kodeakun  = $input['lkode'];
				$jumlah = $input['ljumlah'];
				$ket   = $input['lket'];
				$jumdata  = count($kodeakun);

				if ($param != 0) {
					$buildApPuBiaya->delete(['kodepu' => $nomorbukti]);
				}
				$totbiaya = 0;
				for ($i = 0; $i < $jumdata; $i++) {
					$totbiaya = $totbiaya + $jumlah[$i];
					$datad = array(
						'kodeakun' => $kodeakun[$i],
						'jumlah' => $jumlah[$i],
						'keterangan' => $ket[$i],

					);
					if ($kodeakun[$i] != "") {
						if ($param == 0) {
							$datad['kodepu'] = $nobukti;
							$buildApPuBiaya->insert($datad);
						} else {
							$datad['kodepu'] = $nomorbukti;
							$buildApPuBiaya->insert($datad);
						}
					}
				}


				if ($param == 0) {
					if ($input['pembayaran'] == 'T') {
						$tgljthtempo = date('Y-m-d', strtotime($input['tanggal']));
					} else {
						$top = $buildersupplier->getWhere(['kode' => $input['supp'], 'kodecbg' => $cabang])->getRow()->top;
						$tgljthtempo = date('Y-m-d', strtotime($input['tanggal'] . ' + ' . $top . ' days'));
					}
				} else {
					if ($input['pembayaran'] == 'T') {
						$tgljthtempo = date('Y-m-d', strtotime($input['tanggal']));
					} else {
						$tgljthtempo = date('Y-m-d', strtotime($input['tanggal']));
					}
				}

				$profile = $model_Global->_LoadProfile();
				$akun_debet     = $profile->akun_hutangbeli;
				$akun_kredit    = $profile->akun_hutang;
				$akun_kas    	= $profile->akun_kas;

				$datapo = $builderlpb->getWhere(['nolpb' => $input['kodepb'], 'kodecbg' => $cabang,  'gudang' => $input['gudang']])->getRow();
				if ($datapo) {
					$nomorpo = $datapo->kodepo;
				} else {
					$nomorpo = '';
				}

				$data_uang_muka = $buildApUangMuka->getWhere(['kodepo' => $datapo->kodepo, 'kodecbg' => $cabang])->getRow();
				!empty($data_uang_muka) ? $jumum = $data_uang_muka->jumlahum : $jumum = '0';
				if ($_vvtotal == '0' || $_vvtotal == '0.00') {


					$nomor_kode = $model_Global->_Autonomor('PP');

					//data input table ap_bayardetil
					$datad_bayar = array(
						'kodebyr'   	=> $nomor_kode,
						'nomorfaktur' 	=> $nobukti,
						'tglfaktur' 	=> date('Y-m-d', strtotime($input['tanggal'])),
						'totalfaktur' 	=> $_vvtotal,
						'terhutang' 	=> $_vvtotal,
						'bayar' 		=> $_vvtotal,
						'diskon' 		=> $_vvdiskon,
						'uangmuka' 		=> $jumum,
						'cabang' 		=> $cabang,
					);

					$buildApBayarD->insert($datad_bayar);

					// data input table ap_bayar
					$data_bayar = array(
						'kodecbg' 		=> $cabang,
						'kodesup' 		=> $input['supp'],
						'kodebyr'  		=> $nomor_kode,
						'kodebank'  	=> $akun_kas,
						'metodebayar'  	=> $input['pembayaran'],
						'tglbayar'   	=> date('Y-m-d', strtotime($input['tanggalkirim'])),
						'tglcek'   		=> date('Y-m-d', strtotime($input['tanggal'])),
						'jumlahbayar'  	=> $_vvtotal,
						'kodeuser'		=> $session->get('username'),
						'keterangan'    => $input['keterangan'],
						'statusid'		=> 1
					);

					$buildApBayar->insert($data_bayar);
					$model_Global->_updatecounter1('PP');
					$status = 2;
				} else {
					$status = 1;
				}

				$data = array(
					'kodecbg' 			=> $cabang,
					'kodesup' 			=> $input['supp'],
					'nomorfaktur'  		=> $input['nomorfaktur'],
					'nomorfaktur_pajak' => $input['no-pajak'],
					'pembayaran'  		=> $input['pembayaran'],
					'kodepo'  			=> $nomorpo,
					'kodepb'  			=> $input['kodepb'],
					'tglpu'   			=> date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
					'tglkirim'			=> date('Y-m-d', strtotime($input['tanggalkirim'])),
					'tgljthtempo'		=> $tgljthtempo,
					'kodeuser'			=> $session->get('username'),
					'ket'     			=> $input['keterangan'],
					'dpp'     			=> $_vvdpp,
					'ppn'     			=> $_vvppn,
					'diskon'  			=> $_vvdiskon,
					'biayalain'			=> $_vvbiayadll,
					'totalpu' 			=> $_vvtotal,
					'uangmuka' 			=> $jumum,
					'sppn'    			=> $input['sppn'],
					'statusid'			=> $status,
					'retur'				=> '1',
					'alamat1' 			=> $input['alamat'],
				);

				if ($param != 0) {
					$model_Global->_hapusjurnal($nomorbukti, 'JP');
				}

				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

				if ($param == 0) {
					$datalogs['detail'] = 'Tambah data Pembelian Faktur dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$nobukti,
						'JP',
						$input['kodepb'],
						1,
						$akun_debet,
						'Faktur Pembelian',
						'Faktur Pembelian',
						$_vvtotal,
						0
					);

					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$nobukti,
						'JP',
						$input['kodepb'],
						2,
						$akun_kredit,
						'Faktur Pembelian',
						'Faktur Pembelian',
						0,
						$_vvtotal
					);
				} else {
					$datalogs['detail'] = 'Edit data Pembelian Faktur dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$nomorbukti,
						'JP',
						$input['kodepb'],
						1,
						$akun_debet,
						'Faktur Pembelian',
						'Faktur Pembelian',
						$_vvtotal,
						0
					);

					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$nomorbukti,
						'JP',
						$input['kodepb'],
						2,
						$akun_kredit,
						'Faktur Pembelian',
						'Faktur Pembelian',
						0,
						$_vvtotal
					);
				}

				$data_pesanan = $builderPO->getWhere(['kodepo' => $input['no-po'], 'kodecbg' => $cabang])->getRow();
				if ($data_pesanan->statusid == '2') {
					$builderPO->where(['kodepo' => $input['no-po'], 'kodecbg' => $cabang])->update(['statusid' => 4, 'no_get' => 3]);
				}
				if ($param == 0) {
					$data['kodepu'] = $nobukti;
					$builder->insert($data);
					$builderlpb->where(['nolpb' => $input['kodepb'], 'kodecbg' => $cabang])->update(['no_terima' => 3]);

					if ($input['sppn'] == 'Y') {
						$model_Global->_updatecounter1('PUP');
					} else {
						$model_Global->_updatecounter1('PU');
					}
					$CountKode  = count($kode);
					for ($i = 0; $i < $CountKode; $i++) {
						$harga = str_replace(',', '', $harga1[$i]);
						$vjum  = $qty[$i] * $harga;
						$vdisc = $vjum * ($disc[$i] / 100);
						$netto_brg = $harga - ($disc[$i] / 100) * $harga;
						$tot   = $tot + $vjum;
						$tdisc = $tdisc + $vdisc;
						$DataInv = $builderINVBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $input['gudang']])->getRow();
						$ResultUpdateStokBarang = [
							'stok ' => $DataInv->stok + $qty[$i],
							'stok_so ' => $DataInv->stok_so + $qty[$i]
						];
						$builderINVBarang->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $input['gudang']])->update($ResultUpdateStokBarang);
						$HrgBarang = [
							'hargabeli' => $netto_brg,
							'tglbeliakhir' => $tanggal
						];
						$builderINVBarang->where(['kodeitem' => $kode[$i]])->update($HrgBarang);
					}
					echo json_encode(['status' => 1, 'message' => $nobukti]);

					// insert pembayaran jika pembayaran tunai
					// table 3
					$builderbyr         = $db->table('ap_bayar');
					$builderbyrDetail   = $db->table('ap_bayardetil');
					if ($input['pembayaran'] == 'T') {
						$noantri = $model_Global->_Autonomor('PP');
						$bayarfile = $builderbyr->getWhere(['kodebyr' => $noantri, 'kodecbg' => $cabang])->getRow();
						if (empty($bayarfile)) {
							$nobuktibyr = $noantri;
						} else {
							$nobuktibyr = $model_Global->_Autonomor('PP');
						}
						$datadbyr = array(
							'kodebyr'   	=> $nobuktibyr,
							'nomorfaktur' 	=> $nobukti,
							'tglfaktur' 	=> date('Y-m-d', strtotime($input['tanggal'])),
							'totalfaktur' 	=> $_vvtotal,
							'terhutang' 	=> $_vvtotal,
							'bayar' 		=> $_vvtotal,
							'uangmuka' 		=> $jumum,
							'diskon' 		=> $_vvdiskon,
							'cabang' 		=> $cabang,
						);
						$builderbyrDetail->insert($datadbyr);
						//update faktur		
						$db->query("UPDATE ap_pufile set jumlahbayar = jumlahbayar + '$_vvtotal', uangmuka= uangmuka +'$jumum' where kodepu='$nobukti' and kodecbg='$cabang'");

						if ($jumum > 0) {
							$db->query("UPDATE ap_uangmuka set jumlahbayar=jumlahbayar+$jumum
						where kodepo = '$nomorpo'");
						}

						$db->query("UPDATE ap_pufile set statusid=2 where jumlahbayar+uangmuka>=totalpu and kodepu = '$nobukti'");

						$profilebyr = $model_Global->_LoadProfile();
						$akun_debetbyr     = $profilebyr->akun_hutang;
						$akun_uangmukabyr  = $profilebyr->akun_uangmuka;

						$model_Global->_rekamjurnal(
							date('Y-m-d', strtotime($input['tanggal'])),
							$nobuktibyr,
							'JP',
							$input['supp'],
							1,
							$akun_debetbyr,
							'Pembayaran ke Supplier ' . $input['supp'],
							'Pembayaran ke Supplier',
							$_vvtotal + $jumum,
							0
						);

						$model_Global->_rekamjurnal(
							date('Y-m-d', strtotime($input['tanggal'])),
							$nobuktibyr,
							'JP',
							$input['supp'],
							2,
							'110101',
							'Pembayaran ke Supplier ' . $input['supp'],
							'Pembayaran ke Supplier',
							0,
							$_vvtotal
						);

						if ($jumum > 0) {
							$model_Global->_rekamjurnal(
								date('Y-m-d', strtotime($input['tanggal'])),
								$nobuktibyr,
								'JP',
								$input['supp'],
								3,
								$akun_uangmukabyr,
								'Pembayaran ke Supplier ' . $input['supp'],
								'Pembayaran ke Supplier',
								0,
								$jumum
							);
						}
						// data input table ap_biaya
						$databyr = array(
							'kodecbg' 		=> $cabang,
							'kodesup' 		=> $input['supp'],
							'kodebyr'  		=> $nobuktibyr,
							'kodebank'  	=> '110101',
							'metodebayar'  	=> $input['pembayaran'],
							'tglbayar'   	=> date('Y-m-d', strtotime($input['tanggal'])),
							'tglcek'   		=> date('Y-m-d', strtotime($input['tanggal'])),
							'jumlahbayar'  	=> $_vvtotal,
							'kodeuser'		=> $session->get('username'),
							'keterangan'    => $input['keterangan'],
							'statusid'		=> 1,
						);
						$builderbyr->insert($databyr);
						$model_Global->_updatecounter1('PP');
						// insert keuangan keluar
						// Table keuangan
						$buildakun        = $db->table('ms_akun');
						$buildkeluar      = $db->table('tr_pengeluaran');
						$buildkeluard     = $db->table('tr_pengeluarand');
						$buildjurnal      = $db->table('tr_jurnal');
						$buildsupplier    = $db->table('ap_supplier');

						// input keuangan keluar
						$tahun 		= date('Y', strtotime($input['tanggal']));
						$bulan 		= date('m', strtotime($input['tanggal']));
						$kodebukti 	= $buildakun->getWhere(['kodeakun' => '110101'])->getRow()->kodek;
						$vbukti 	= $kodebukti . '%';
						$likeKeluar = $buildkeluar->like(['keluar_nomor' => $vbukti])->getWhere(['year(keluar_tanggal)' => $tahun, 'month(keluar_tanggal)' => $bulan]);
						$max        = $likeKeluar->getNumRows();
						$nourut  	        = $max + 1;
						$nomorbuktikeluar   = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);

						$data_supplier = $buildsupplier->getWhere(['kode' => $input['supp'], 'kodecbg' => $cabang])->getRow();

						//data input table tr_pengeluaran
						$datakeuangan = array(
							'keluar_nomor' 			=> $nomorbuktikeluar,
							'keluar_tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
							'keluar_kasbank' 		=> '110101',
							'keluar_cekgiro' 		=> $input['pembayaran'],
							'keluar_cekgirotanggal' => date('Y-m-d', strtotime($input['tanggal'])),
							'keluar_penerima' 		=> $data_supplier->nama,
							'keluar_kodecbg' 		=> $cabang,
							'keluar_userentry' 		=> $session->get('username'),
							'keluar_status' 		=> 2,
						);


						if ($param == 0) {
							$datakeuangan['keluar_kodebyr'] = $nobuktibyr;
							$datakeuangan['keluar_uraian'] = 'Pembayaran ' . $nobuktibyr;
							$buildkeluar->insert($datakeuangan);
						}

						$nourut = 1;
						$buildjurnal->delete(['novoucher' => $nomorbuktikeluar, 'jenis' => 'JK', 'wbs' => 'KK']);

						//data input table tr_pengeluarand
						$RowKeluar = $buildkeluar->getWhere(['keluar_nomor' => $nomorbuktikeluar, 'keluar_kodecbg' => $cabang])->getRow();
						$datadkeluar = array(
							'keluard_register' 	=> $RowKeluar->keluar_register,
							'keluard_nomor' 	=> $nomorbuktikeluar,
							'keluard_nourut' 	=> $nourut,
							'keluard_akun' 		=> $akun_debetbyr,
							'keluard_uraian' 	=> 'Pembayaran ke Supplier ' . $data_supplier->nama,
							'keluard_jumlah' 	=> $_vvtotal + $jumum

						);

						if ($akun_debetbyr != "") {
							$buildkeluard->insert($datadkeluar);

							//data input debet table tr_jurnal 
							$data_jurnaldebet = array(
								'novoucher' 	=> $nomorbuktikeluar,
								'noref' 		=> $nobuktibyr,
								'tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
								'keterangan' 	=> 'Pembayaran ke Supplier ' . $data_supplier->nama,
								'nourut' 		=> $nourut,
								'jenis' 		=> 'JK',
								'wbs' 			=> 'KK',
								'kodeakun' 		=> $akun_debetbyr,
								'debet' 		=> $_vvtotal + $jumum,
								'kredit' 		=> 0,
								'userid' 		=> $session->get('username'),
							);
							$buildjurnal->insert($data_jurnaldebet);
							$nourut++;
						}

						//data input kredit di table tr_jurnal
						$data_jurnalkredit = array(
							'novoucher' 	=> $nomorbuktikeluar,
							'noref' 		=> $nobuktibyr,
							'tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
							'keterangan' => 'Pembayaran ke Supplier ' . $data_supplier->nama,
							'nourut' 	=> $nourut,
							'jenis' 		=> 'JK',
							'wbs' 		=> 'KK',
							'kodeakun' 	=> '110101',
							'debet' 		=> 0,
							'kredit' 	=> $_vvtotal,
							'userid' 	=> $session->get('username'),
						);
						$buildjurnal->insert($data_jurnalkredit);
					}
				} else {
					$data['kodepu'] = $nomorbukti;
					$builder->where(['kodepu' => $nomorbukti, 'kodecbg' => $cabang])->update($data);
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
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

	public function cetak_faktur($param)
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
			$dataSifile = $db->table("ap_pufile")->where(['id' => $param])->get()->getRow();

			$queryh = "select * from ap_pufile inner join ap_supplier on ap_pufile.kodesup=ap_supplier.kode where kodepu = '$dataSifile->kodepu'";
			$queryd = "select ap_pudetail.*, inv_barang.namabarang from ap_pudetail inner join inv_barang on ap_pudetail.kodeitem=inv_barang.kodeitem where ap_pudetail.kodepu = '$dataSifile->kodepu' and kdcbg='$unit'";
			$queryb = "select sum(jumlah) as total from ap_pubiaya  where ap_pubiaya.kodepu = '$dataSifile->kodepu'";

			$detil  = $db->query($queryd)->getResult();
			$header = $db->query($queryh)->getRow();
			$biaya  = $db->query($queryb)->getRow();
			$pdf = new simkeu_nota_helper();
			$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
			$pdf->addpage("P", "A4");
			$pdf->setsize("P", "A4");
			$pdf->SetWidths(array(70, 30, 90));
			$border = array('T', '', 'BT');
			$size   = array('', '', '');
			$pdf->setfont('Arial', 'B', 18);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$align = array('L', 'C', 'L');
			$style = array('', '', 'B');
			$size  = array('12', '1', '18');
			$max   = array(5, 5, 20);
			$judul = array('Dari :', '', 'Faktur Pembelian');
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
			$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodepu), $fc, $border);
			$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglpu))), $fc, $border);
			$pdf->FancyRow(array($header->alamat2, '', 'Tgl. Kirim', ':', date('d M Y', strtotime($header->tglkirim))), $fc, $border);
			$pdf->FancyRow(array('', '', 'Nomor Faktur', ':', $header->nomorfaktur), $fc, $border);
			$pdf->FancyRow(array($header->telp, '', '', '', ''), $fc, $border);

			$pdf->ln(2);

			$pdf->SetWidths(array(30, 60, 20, 25, 25, 30));
			$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R');
			$pdf->setfont('Arial', 'B', 10);
			$pdf->SetAligns(array('L', 'C', 'R'));
			//$pdf->SetFillColor(0,0,139);
			//$pdf->settextcolor(255,255,255);
			$fc = array('0', '0', '0', '0', '0', '0');
			$judul = array('Kode Barang', 'Nama Barang', 'Qty', 'Harga', 'Diskon', 'Total Harga');
			$pdf->FancyRow2(8, $judul, $fc, $border, $align);
			$border = array('', '', '');
			$pdf->setfont('Arial', '', 10);
			$tot = 0;
			$subtot = 0;
			$tdisc  = 0;
			$border = array('', '', '', '', '', '');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R');
			$fc = array('0', '0', '0', '0', '0', '0');
			$pdf->SetFillColor(0, 0, 139);
			$pdf->settextcolor(0);
			foreach ($detil as $db) {
				$dpp = $db->qtypu * $db->hargabeli;
				$dis = ($db->disc / 100) * $dpp;
				$jum = $dpp - $dis;
				$tot = $tot + $jum;

				$subtot = $subtot + $dpp;
				$tdisc  = $tdisc + $dis;

				$tdpp = $subtot - $tdisc;
				$pdf->FancyRow(array(
					$db->kodeitem,
					$db->namabarang,
					$db->qtypu,
					number_format($db->hargabeli, 2, '.', ','),
					$db->disc,
					number_format($jum, 0, '.', ',')
				), $fc, $border, $align);
			}


			if ($header->sppn == "Y") {
				$ppn = $tdpp * 0.11;
			} else {
				$ppn = 0;
			}
			$biayalain = $biaya->total;
			$tot = $tot + $ppn + $biayalain;
			$pdf->SetFillColor(230);
			$border = array('B', 'B', 'B', 'B', 'B', 'B');
			$pdf->FancyRow(array('', '', '', '', '', ''), 0, $border);
			$pdf->ln(2);
			$pdf->SetWidths(array(100, 20, 30, 40));
			$border = array('TB', '', 'T', 'T');
			$align  = array('L', '', 'L', 'R');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->settextcolor(0);
			$fc = array('0', '0', '0', '0');
			$pdf->FancyRow(array('Keterangan', '', 'Sub Total', number_format($subtot, 2, '.', ',')), $fc, $border, $align, 0);
			$border = array('', '', '', '');
			$pdf->FancyRow(array($header->ket, '', 'Diskon', number_format($tdisc, 0, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'DPP', number_format($tdpp, 2, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'PPN (11%)', number_format($ppn, 2, '.', ',')), $fc, $border, $align);
			$pdf->FancyRow(array('', '', 'Biaya Lain-lain', number_format($biayalain, 2, '.', ',')), $fc, $border, $align);
			$style = array('', '', 'B', 'B');
			$size  = array('0', '0', '0');
			$border = array('T', '', 'BT', 'BT');
			//$pdf->SetFillColor(0,0,139);
			//$pdf->settextcolor(255,255,255);
			$fc = array('0', '0', '0', '0');
			$pdf->FancyRow(array('', '', 'Total', number_format($tot, 2, '.', ',')), $fc, $border, $align, $style, $size);
			$pdf->settextcolor(0);
			$pdf->SetWidths(array(50, 50));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C'));
			$pdf->ln(5);

			$border = array('', '');
			$align  = array('C', 'C');
			$pdf->FancyRow(array('Bagian Pembelian', ''), 0, $border, $align);
			$pdf->ln(1);
			$pdf->ln(15);
			$pdf->SetWidths(array(49, 2, 49));
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$border = array('B', '', '');
			$pdf->FancyRow(array('', '', ''), 0, $border, $align);
			$border = array('', '', '');
			$align  = array('L', 'C', 'L');
			$pdf->FancyRow(array('Tgl.', '', ''), 0, $border, $align);
			$pdf->ln(5);

			$pdf->settextcolor(0);
			$pdf->SetWidths(array(20, 3, 150));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C'));
			$fc = array('0', '0', '0', '0');
			$terbilang = $model_Global->terbilang($tot);
			$pdf->FancyRow(array('TEBILANG', ':', $terbilang, '', ''), $fc, $border);

			$pdf->output($dataSifile->kodepu . ".pdf", 'I');
			exit;
		} else {

			header('location:' . base_url());
		}
	}

	//***********UANG MUKA PEMBELIAN Barang Page Start Here*************//
	//Uang Muka Pembelian Page Start Here
	public function uang_muka_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 325);
		if ($akses->uadd == 1) {
			$tambah = 'Data Uang Muka';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Uang Muka Pembelian',
			'body' => 'admin/pembelian/transaksi/uangmukapembelian',
			'tambah' => $tambah,
		];
		return view('layout/layout', $data);
	}

	public function um_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 325);
		$db = db_connect();
		$query = "SELECT * FROM ap_uangmuka where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglum between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tglum)= (select periode_tahun from ms_identity) and
            month(tglum)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			if ($value->statusid == 1) {
				$status = '<span class="badge badge-warning">Belum Lunas</span>';
			} else if ($value->statusid == 2) {
				$status =  '<span class="badge badge-success">Lunas</span>';
			}

			if ($akses->udel == 1 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/uangmukapembelian/entri/' . $value->idum) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
				$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
			} else if ($akses->udel == 1 && $akses->uedit == 0) {
				$delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>';
				$editview = '';
			} else if ($akses->udel == 0 && $akses->uedit == 1) {
				$editview = '<a href="' . base_url('pembelian/transaksi/uangmukapembelian/entri/' . $value->idum) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>';
				$delete = '';
			} else {
				$editview = '';
				$delete = '';
			}
			$count++;
			$data[] = [
				$count,
				$value->kodeum,
				date('d-m-Y', strtotime($value->tglum)),
				$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
				$value->keterangan,
				$status,
				$akses->uharga == 1 ? number_format($value->jumlahum) : '0',
				$editview,
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function uang_muka_pembelian_entri($id = '')
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Uang Muka Pembelian',
			'body' => 'admin/pembelian/transaksi/uangmukapembelianentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PM'),
		];
		return view('layout/layout', $data);
	}

	public function postpofile($data)
	{
		$session = session();
		$cabang = $session->get('cabang');
		$db = db_connect();
		$RowData = $db->table("ap_pofile")->getWhere(['kodepo' => $data, 'kodecbg' => $cabang])->getRow();
		echo json_encode($RowData);
	}

	public function uangmuka_pembelian_save()
	{
		$model_Global   =  model('Global_model', 'M_GLOBAL');
		$session        = session();
		$userid         = $session->get('username');
		$cabang         = $session->get('cabang');
		$level          = $session->get('level');
		$akses          = $model_Global->cek_menu_akses($level, 325);
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];

		// Table
		$builder            = $db->table('ap_uangmuka');
		try {
			$db->transStart();
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$nomorbukti  = $input['nomorbukti'];

				$umfile = $builder->getWhere(['kodeum' => $nomorbukti, 'kodecbg' => $cabang])->getRow();
				if (empty($umfile)) {
					$nobukti = $nomorbukti;
				} else {
					$check_no = $model_Global->_Autonomor('PM');
				}

				$data = array(
					'kodecbg'       => $cabang,
					'kodesup'       => $input['supp'],
					'nomorfaktur'   => $input['nomorfaktur'],
					'kodepo'        => $input['kodepo'],
					'tglum'         => date('Y-m-d', strtotime($input['tanggal'])),
					'kodeuser'      => $userid,
					'keterangan'    => $input['keterangan'],
					'jumlahum'      => $input['uangmuka'],
					'jumlahpo'      => $input['jumlahpo'],
					'kodebank'      => $input['kasbank'],
					'norektujuan'   => $input['norek'],
					'statusid'      => 1,
				);

				$profile = $model_Global->_LoadProfile();
				$akun_debet    = $profile->akun_uangmuka;
				$akun_kredit   = $input['kasbank'];
				if ($param != 0) {
					$model_Global->_hapusjurnal($nomorbukti, 'UB');
				}

				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'UB',
					$input['kodepo'],
					1,
					$akun_debet,
					'UM Pembelian ',
					'UM Pembelian ' . $input['kodepo'],
					$input['uangmuka'],
					0,
					$cabang
				);

				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'UB',
					$input['kodepo'],
					2,
					$akun_kredit,
					'UM Pembelian',
					'UM Pembelian ' . $input['kodepo'],
					0,
					$input['uangmuka'],
					$cabang
				);

				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

				if ($param == 0) {
					$data['kodeum'] = $nobukti;
					$builder->insert($data);
					$datalogs['detail'] = 'Tambah data Pembelian Uang Muka dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					$model_Global->_updatecounter1('PM');
					echo json_encode($nobukti);
				} else {
					$data['kodeum'] = $nomorbukti;
					$builder->where(['kodeum' => $nomorbukti, 'kodecbg' => $cabang])->update($data);
					$datalogs['detail'] = 'Edit data Pembelian Uang Muka dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					echo json_encode($nomorbukti);
				}
				$db->transComplete();
			} else {
				echo json_encode(['message' => 'Gagal']);
			}
		} catch (\Exception $e) {
			echo json_encode(['message' => $e->getMessage()]);
		}
	}

	//***********PEMBAYARAN PEMBELIAN Barang Page Start Here*************//
	//Pembayaran Pembelian Page Start Here
	public function pembayaran_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 326);
		if ($akses->uadd == 1) {
			$tambah = 'Data Pembayaran';
		} else {
			$tambah = '';
		}
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pembayaran Pembelian',
			'body' => 'admin/pembelian/transaksi/pembayaran_pembelian',
			'tambah' => $tambah
		];
		return view('layout/layout', $data);
	}

	public function pembayaran_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 326);
		$db = db_connect();
		$query = "SELECT * FROM ap_bayar where kodecbg='$cabang'";
		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and tglbayar between '$tgl1' and '$tgl2'";
		} else {
			$query .= " and year(tglbayar)= (select periode_tahun from ms_identity) and
            month(tglbayar)= (select periode_bulan from ms_identity)";
		}
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$count = 0;
		foreach ($res as $value) {
			$count++;
			$data[] = [
				$count,
				$value->kodebyr,
				date('d-m-Y', strtotime($value->tglbayar)),
				$value->nomorcek,
				date('d-m-Y', strtotime($value->tglcek)),
				$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
				$db->table('ms_akun')->where('kodeakun', $value->kodebank)->get()->getRow()->namaakun ?? '',
				$akses->uharga == 1 ? number_format($value->jumlahbayar) : '0',
				$value->statusid == 1 && $akses->uedit == 1 ? '<a href="' . base_url('pembelian/transaksi/pembayaranpembelian/entri/' . $value->idbayar) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>' : '',
				$value->statusid == 5  ? '<button class="btn btn-danger" onclick="data_delete(' . $value->idbayar . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : '',
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function pembayaran_pembelian_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 326);
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Pembayaran Pembelian',
			'body' => 'admin/pembelian/transaksi/pembayaranpembelianentri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PP'),
			'cabang' => $cabang,
			'akses' => $akses,
		];
		return view('layout/layout', $data);
	}

	public function getfaktur($supp)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$data = $db->query("select ap_pufile.*, totalpu as total, (totalpu-jumlahbayar-uangmuka) as hutang from ap_pufile where kodesup='$supp' and kodecbg='$cabang' and totalpu>jumlahbayar+uangmuka")->getResult();
		$vdata = array();
		foreach ($data as $row) {
			//
			$kodepo = $row->kodepo;
			$duangmuka = $db->query("select * from ap_uangmuka where kodepo = '$kodepo' and jumlahum>jumlahbayar")->getRow();
			if ($duangmuka) {
				$uangmuka = $duangmuka->jumlahum;
			} else {
				$uangmuka = 0;
			}

			$vdata[] = array(
				'nomorfaktur' => $row->nomorfaktur,
				'kodepu' => $row->kodepu,
				'tglpu' => date('d-m-Y', strtotime($row->tglpu)),
				'totalpu' => $row->totalpu,
				'uangmuka' => $uangmuka,
				'hutang' => $row->hutang,
				'total' => $row->total,
			);
		}
		echo json_encode($vdata);
	}

	public function bayar_pembelian_save()
	{
		$model_Global   =  model('Global_model', 'M_GLOBAL');
		$session        = session();
		$userid         = $session->get('username');
		$cabang         = $session->get('cabang');
		$level          = $session->get('level');
		$akses          = $model_Global->cek_menu_akses($level, 326);
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];
		// Table
		$builder            = $db->table('ap_bayar');
		$builderDetail      = $db->table('ap_bayardetil');
		$buildPufile        = $db->table('ap_pufile');
		$buildlpb           = $db->table('ap_lpb');
		try {
			$db->transStart();
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$nomorbukti  = $input['nomorbukti'];

				$bayarfile = $builder->getWhere(['kodebyr' => $nomorbukti, 'kodecbg' => $cabang])->getRow();
				if (empty($bayarfile)) {
					$nobukti = $nomorbukti;
				} else {
					$nobukti = $model_Global->_Autonomor('PP');
				}
				if ($param != 0) {

					//update faktur				
					$bayardetil = $builderDetail->getWhere(['kodebyr' => $nomorbukti, 'cabang' => $cabang])->getResult();
					foreach ($bayardetil as $row) {
						$_bayar 		= $row->bayar;
						$_uangmuka 	= $row->uangmuka;
						$faktur 		= $row->nomorfaktur;
						$db->query("update ap_pufile set jumlahbayar = jumlahbayar - '$_bayar', uangmuka= uangmuka -'$_uangmuka' where kodepu='$faktur' and kodecbg='$cabang'");

						$dfaktur =  $buildPufile->getWhere(['kodepu' => $faktur, 'kodecbg' => $cabang])->getRow();
						$kodepo  = $dfaktur->kodepo;

						$db->query("update ap_uangmuka set jumlahbayar=jumlahbayar-$_uangmuka
					 where kodepo = '$kodepo'");
					}
					$builderDetail->delete(['kodebyr' => $nomorbukti, 'cabang' => $cabang]);
				}

				$faktur  	= $input['faktur'];
				$tglfaktur  = $input['tglfaktur'];

				$jumdata  = count($faktur);

				$nourut = 1;
				$_totuangmuka = 0;

				for ($i = 0; $i < $jumdata; $i++) {
					$_totfaktur = str_replace(',', '', $input['totfaktur']);
					$_hutang    = str_replace(',', '', $input['hutang']);
					$_bayar     = str_replace(',', '', $input['bayar']);
					$_disc      = str_replace(',', '', $input['disc']);
					$_uangmuka  = str_replace(',', '', $input['uangmuka']);

					$_total = $_bayar[$i] - $_disc[$i] + $_uangmuka[$i];

					$_totuangmuka = $_totuangmuka + $_uangmuka[$i];

					//data input table ap_biayadetil
					$datad = array(
						'kodebyr'   	=> $nobukti,
						'nomorfaktur' 	=> $faktur[$i],
						'tglfaktur' 	=> date('Y-m-d', strtotime($tglfaktur[$i])),
						'totalfaktur' 	=> $_totfaktur[$i],
						'terhutang' 	=> $_hutang[$i],
						'bayar' 		=> $_bayar[$i],
						'uangmuka' 		=> $_uangmuka[$i],
						'diskon' 		=> $_disc[$i],
						'cabang' 		=> $cabang,
					);
					if ($_total != "0") {
						//update faktur		
						$db->query("update ap_pufile set jumlahbayar = jumlahbayar + '$_bayar[$i]', uangmuka= uangmuka +'$_uangmuka[$i]' where kodepu='$faktur[$i]' and kodecbg='$cabang'");

						if ($_uangmuka > 0) {
							$dfaktur =  $buildPufile->getWhere(['kodepu' => $faktur[$i], 'kodecbg' => $cabang])->getRow();
							$kodepo  = $dfaktur->kodepo;

							$db->query("update ap_uangmuka set jumlahbayar=jumlahbayar+$_uangmuka[$i]
						where kodepo = '$kodepo'");
						}
						if ($param == 0) {
							$datad['kodebyr'] = $nobukti;
							$builderDetail->insert($datad);
						} else {
							$datad['kodebyr'] = $nomorbukti;
							$builderDetail->insert($datad);
						}
						$RowPufile = $db->query("SELECT * FROM ap_pufile where jumlahbayar+uangmuka>=totalpu and kodepu = '$faktur[$i]'")->getRow();
						if ($RowPufile == null) {
							$buildPufile->where(['kodepu' => $faktur[$i]])->update(['statusid' => 1]);
						} else {
							$buildPufile->where(['kodepu' => $faktur[$i]])->update(['statusid' => 2]);
						}
					}
				}

				$_jumlahbayar = str_replace(',', '', $input['jumlahbayar']);

				// data input table ap_biaya
				$data = array(
					'kodecbg' 		=> $cabang,
					'kodesup' 		=> $input['supp'],
					'kodebyr'  		=> $input['nomorbukti'],
					'kodebank'  	=> $input['kasbank'],
					'metodebayar'  	=> $input['metodebayar'],
					'tglbayar'   	=> date('Y-m-d', strtotime($input['tanggal'])),
					'nomorcek'  	=> $input['nomorcek'],
					'tglcek'   		=> date('Y-m-d', strtotime($input['tanggalcek'])),
					'jumlahbayar'  	=> $_jumlahbayar,
					'kodeuser'		=> $userid,
					'keterangan'    => $input['keterangan'],
					'statusid'		=> 1,
				);
				if ($param != 0) {
					$model_Global->_hapusjurnal($nomorbukti, 'JP');
				}

				$profile = $model_Global->_LoadProfile();
				$akun_debet     = $profile->akun_hutang;
				$akun_uangmuka  = $profile->akun_uangmuka;
				$akun_kredit    = $input['kasbank'];


				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'JP',
					$input['supp'],
					1,
					$akun_debet,
					'Pembayaran ke Supplier ' . $input['supp'],
					'Pembayaran ke Supplier',
					$_jumlahbayar + $_totuangmuka,
					0
				);

				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'JP',
					$input['supp'],
					2,
					$akun_kredit,
					'Pembayaran ke Supplier ' . $input['supp'],
					'Pembayaran ke Supplier',
					0,
					$_jumlahbayar
				);

				if ($_totuangmuka > 0) {
					$model_Global->_rekamjurnal(
						date('Y-m-d', strtotime($input['tanggal'])),
						$nobukti,
						'JP',
						$input['supp'],
						3,
						$akun_uangmuka,
						'Pembayaran ke Supplier ' . $input['supp'],
						'Pembayaran ke Supplier',
						0,
						$_totuangmuka
					);
				}

				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

				if ($param == 0) {
					$data['kodebyr'] = $nobukti;
					$builder->insert($data);
					$datalogs['detail'] = 'Tambah data Pembelian Pembayaran dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					$model_Global->_updatecounter1('PP');
					echo json_encode(['status' => 1, 'message' => $nobukti]);
				} else {
					$data['kodebyr'] = $nomorbukti;
					$builder->where(['kodebyr' => $nomorbukti, 'kodecbg' => $cabang])->update($data);
					$datalogs['detail'] = 'Edit data Pembelian Pembayaran dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
				}

				// Table 2
				$buildakun        = $db->table('ms_akun');
				$buildkeluar      = $db->table('tr_pengeluaran');
				$buildkeluard     = $db->table('tr_pengeluarand');
				$buildjurnal      = $db->table('tr_jurnal');
				$buildsupplier    = $db->table('ap_supplier');

				// input keuangan keluar
				$tahun 		= date('Y', strtotime($input['tanggal']));
				$bulan 		= date('m', strtotime($input['tanggal']));
				$kodebukti 	= $buildakun->getWhere(['kodeakun' => $input['kasbank']])->getRow()->kodek;
				$vbukti 	= $kodebukti . '%';
				$likeKeluar = $buildkeluar->like(['keluar_nomor' => $vbukti])->getWhere(['year(keluar_tanggal)' => $tahun, 'month(keluar_tanggal)' => $bulan]);
				$max        = $likeKeluar->getNumRows();
				$nourut  	        = $max + 1;
				$nomorbuktikeluar   = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);

				$data_supplier = $buildsupplier->getWhere(['kode' => $input['supp'], 'kodecbg' => $cabang])->getRow();

				if ($param != 0) {
					$tr_keluar  = $buildkeluar->getWhere(['keluar_kodebyr' => $nomorbukti, 'keluar_kodecbg' => $cabang])->getRow();
					$register   = $tr_keluar->keluar_register;
					$nomorbuktikeluar = $tr_keluar->keluar_nomor;
				}

				//data input table tr_pengeluaran
				$data = array(
					'keluar_nomor' 			=> $nomorbuktikeluar,
					'keluar_tanggal' 		=> date('Y-m-d', strtotime($input['tanggal'])),
					'keluar_kasbank' 		=> $input['kasbank'],
					'keluar_cekgiro' 		=> $input['metodebayar'],
					'keluar_cekgirotanggal' => date('Y-m-d', strtotime($input['tanggalcek'])),
					'keluar_cekgironomor' 	=> $input['nomorcek'],
					'keluar_penerima' 		=> $data_supplier->nama,
					'keluar_kodecbg' 		=> $cabang,
					'keluar_userentry' 		=> $userid,
					'keluar_status' 		=> 2,
				);


				if ($param == 0) {
					$data['keluar_kodebyr'] = $nobukti;
					$data['keluar_uraian'] = 'Pembayaran ' . $nobukti;
					$buildkeluar->insert($data);
					$register = $db->insertID();
				} else {
					$data['keluar_kodebyr'] = $nomorbukti;
					$data['keluar_uraian'] = 'Pembayaran ' . $nomorbukti;
					$buildkeluar->where(['keluar_nomor' => $nomorbuktikeluar, 'keluar_kodecbg' => $cabang])->update($data);
					$buildkeluard->delete(['keluard_nomor' => $nomorbuktikeluar]);
				}

				$nourut = 1;
				$buildjurnal->delete(['novoucher' => $nomorbuktikeluar, 'jenis' => 'JK', 'wbs' => 'KK']);

				//data input table tr_pengeluarand
				$datad = array(
					'keluard_register' 	=> $register,
					'keluard_nomor' 	=> $nomorbuktikeluar,
					'keluard_nourut' 	=> $nourut,
					'keluard_akun' 		=> $akun_debet,
					'keluard_uraian' 	=> 'Pembayaran ke Supplier ' . $data_supplier->nama,
					'keluard_jumlah' 	=> $_jumlahbayar + $_totuangmuka

				);

				if ($akun_debet != "") {
					$buildkeluard->insert($datad);

					//data input debet table tr_jurnal 
					$data_jurnal = array(
						'novoucher' 	=> $nomorbuktikeluar,
						'noref' 		=> $register,
						'tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
						'keterangan' => 'Pembayaran ke Supplier ' . $data_supplier->nama,
						'nourut' 	=> $nourut,
						'jenis' 		=> 'JK',
						'wbs' 		=> 'KK',
						'kodeakun' 	=> $akun_debet,
						'debet' 		=> $_jumlahbayar + $_totuangmuka,
						'kredit' 	=> 0,
						'userid' 	=> $userid,
					);
					$buildjurnal->insert($data_jurnal);
					$nourut++;
				}

				//data input kredit di table tr_jurnal
				$data_jurnal = array(
					'novoucher' 	=> $nomorbuktikeluar,
					'noref' 		=> $register,
					'tanggal' 	=> date('Y-m-d', strtotime($input['tanggal'])),
					'keterangan' => 'Pembayaran ke Supplier ' . $data_supplier->nama,
					'nourut' 	=> $nourut,
					'jenis' 		=> 'JK',
					'wbs' 		=> 'KK',
					'kodeakun' 	=> $akun_kredit,
					'debet' 		=> 0,
					'kredit' 	=> $_jumlahbayar,
					'userid' 	=> $userid,
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

	public function cetakbayar($param)
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
			$email 	  = $profile->email;

			$queryh = "select * from ap_bayar inner join ap_supplier on ap_bayar.kodesup=ap_supplier.kode where kodebyr = '$param'";
			$queryd = "select * from ap_bayardetil where kodebyr = '$param'";

			$detil  = $db->query($queryd)->getResult();
			$header = $db->query($queryh)->getRow();
			$pdf = new simkeu_nota_helper();
			$pdf->setID($nama_usaha, $alamat1, $alamat2, '085156991869', $email);
			$pdf->setjudul('');
			$pdf->setsubjudul('');
			$pdf->addpage("P", "A4");
			$pdf->setsize("P", "A4");
			$pdf->SetWidths(array(70, 30, 90));
			$border = array('T', '', 'BT');
			$size   = array('0', '0', '0');
			$pdf->setfont('Arial', 'B', 18);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$align = array('L', 'C', 'L');
			$style = array('', '', 'B');
			$size  = array('12', '0', '18');
			$max   = array(5, 5, 20);
			$judul = array('Kepada :', '', 'Pembayaran Supllier');
			$fc     = array('0', '0', '0');
			$pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
			$pdf->ln(1);
			$pdf->setfont('Arial', 'B', 10);
			$pdf->SetWidths(array(70, 30, 30, 5, 55));
			$border = array('', '', '', '', '');
			$fc     = array('0', '0', '0', '0', '0');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->setfont('Arial', '', 9);
			$pdf->FancyRow(array($header->nama, '', 'Nomor', ':', $header->kodebyr), $fc, $border);
			$pdf->FancyRow(array($header->alamat1, '', 'Tanggal', ':', date('d M Y', strtotime($header->tglbayar))), $fc, $border);
			$pdf->FancyRow(array($header->alamat2, '', 'Pembayaran', ':', number_format($header->jumlahbayar, 0)), $fc, $border);
			$pdf->FancyRow(array($header->telp, '', '', '', ''), $fc, $border);
			$pdf->ln(2);

			$pdf->SetWidths(array(40, 25, 25, 25, 25, 25, 25));
			$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R', 'R');
			$pdf->setfont('Arial', 'B', 10);
			$pdf->SetAligns(array('L', 'C', 'R'));
			$fc = array('0', '0', '0', '0', '0', '0', '0');
			$judul = array('No. Faktur', 'Tgl. Faktur', 'Total Faktur', 'Diskon', 'PPH 23', 'Uang Muka', 'Pembayaran');
			$pdf->FancyRow2(8, $judul, $fc, $border, $align);
			$border = array('', '', '');
			$pdf->setfont('Arial', '', 10);
			$tot = 0;
			$border = array('', '', '', '', '', '', '');
			$align  = array('L', 'L', 'R', 'R', 'R', 'R', 'R');
			$fc = array('0', '0', '0', '0', '0', '0', '0');
			$pdf->SetFillColor(0, 0, 139);
			$pdf->settextcolor(0);
			foreach ($detil as $db) {
				$tot = $tot + $db->bayar;

				$pdf->FancyRow(array(
					$db->nomorfaktur,
					date('d-m-Y', strtotime($db->tglfaktur)),
					number_format($db->totalfaktur, 0, '.', ','),
					number_format($db->diskon, 0, '.', ','),
					0,
					number_format($db->uangmuka, 0, '.', ','),
					number_format($db->bayar, 0, '.', ',')
				), $fc, $border, $align);
			}

			$pdf->SetWidths(array(190));
			$border = array('T');
			$align  = array('L');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->settextcolor(0);
			$pdf->SetFillColor(230, 230, 230);
			$fc = array('0');
			$pdf->FancyRow(array(''), 0, $border);
			$fc = array('0');

			$fc = array('0', '0', '0', '0');
			$pdf->SetWidths(array(100, 20, 30, 40));
			$style = array('', '', 'B', 'B');
			$size  = array('0', '0', '0', '0');
			$border = array('', '', 'T', 'T');
			$pdf->FancyRow(array('', '', 'Total', number_format($tot, 2, '.', ',')), $fc, $border, $align, $style, $size);
			$pdf->FancyRow(array(ucwords($model_Global->terbilang($tot))), $fc, $border, $align, 0);

			$pdf->SetFillColor(230);
			$border = array('B', 'B', 'B', 'B', 'B', 'B');
			$pdf->ln(2);
			$pdf->SetWidths(array(100, 20, 50, 20));
			$border = array('TB', '', '', '');
			$align  = array('L', '', 'C', '');
			$pdf->SetFillColor(230, 230, 230);
			$pdf->settextcolor(0);
			$fc = array('0', '0', '0', '0');
			$pdf->FancyRow(array('Keterangan', '', 'Mengetahui', ''), $fc, $border, $align, 0);

			$border = array('', '', '', '');
			$pdf->FancyRow(array($header->keterangan, '', '', ''), $fc, $border, $align, 0);
			$pdf->ln(1);
			$pdf->ln(15);
			$border = array('', '', 'B', '');
			$pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, 0);
			$border = array('', '', '', '');
			$align  = array('L', '', 'L', '');
			$pdf->FancyRow(array('', '', 'Tgl.', ''), $fc, $border, $align, 0);

			$pdf->AliasNbPages();
			$pdf->output($param . '.PDF', 'I');
			exit;
		} else {

			header('location:' . base_url());
		}
	}

	//***********RETUR PEMBELIAN Barang Page Start Here*************//
	//Retur Pembelian Page Start Here
	public function retur_pembelian()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 327);
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
			'title' => 'Retur Pembelian',
			'body' => 'admin/pembelian/transaksi/retur_pembelian',
			'tambah' => $tambah,
		];
		return view('layout/layout', $data);
	}

	public function retur_pembelian_table($tgl1, $tgl2)
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 327);
		$db = db_connect();
		$query = "SELECT * FROM ap_retur where kodecbg='$cabang'";
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
			$count++;
			$data[] = [
				$count,
				$value->koderetur,
				date('d-m-Y', strtotime($value->tglretur)),
				$db->table('ap_supplier')->where('kode', $value->kodesup)->get()->getRow()->nama ?? '',
				$value->keterangan,
				$akses->uharga == 1 ? number_format($value->totalretur) : '0',
				$value->statusid == 1  && $akses->uedit == 1 ? '<a href="' . base_url('pembelian/transaksi/returpembelian/entri/' . $value->idretur) . '" class="btn btn-info"><i class="fa-solid fa-eye text-white"></i> View</a>' : '',
				$value->statusid == 5  ? '<button class="btn btn-danger" onclick="data_delete(' . $value->idretur . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : '',
				$akses->ucetak == 1 ? '<a href="' . base_url('pembelian/transaksi/returpembeliancetak/' . $value->koderetur) . '" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-print text-white"></i> Cetak</a>' : '',
			];
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	public function retur_pembelian_entri($id = '')
	{
		$session = session();
		$db = db_connect();
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$cabang = $session->get('cabang');
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 327);
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Retur Pembelian',
			'body' => 'admin/pembelian/transaksi/retur-pembelian-entri',
			'id' => $id ?? '',
			'nomor' => $model_Global->_Autonomor('PR'),
			'cabang' => $cabang,
			'akses' => $akses,
		];
		return view('layout/layout', $data);
	}

	public function listpu($kode)
	{
		$db = db_connect();
		if (!empty($kode)) {
			$pb  = $db->query("select * from ap_pufile where kodesup='$kode' and retur='1'")->getResult();
		?>
			<select name="kodepu" id="kodepu" class="form-control  input-medium select2">
				<option value="">-- PILIH NO BUKTI ---</option>
				<?php
				foreach ($pb  as $row) {
				?>
					<option value="<?php echo $row->kodepu; ?>"><?php echo $row->kodepu; ?></option>
				<?php
				}
				?>
			</select>

<?php

		} else {
			echo "";
		}
	}

	public function postdatapu($kode)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$data = $db->query("SELECT a.*, b.namabarang, c.sppn , d.nama
        FROM ap_pudetail a INNER JOIN inv_barang b ON b.kodeitem = a.kodeitem 
        INNER JOIN ap_pufile c on c.kodepu = a.kodepu 
        INNER JOIN inv_gudang d ON a.gudang =  d.kode
        where a.kodepu ='$kode' and b.kdcbg='$cabang' group by a.id ")->getResult();
		echo json_encode($data);
	}

	public function retur_pembelian_save()
	{
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$db = db_connect();
		$input = $this->request->getPost();
		$param = $input['id'];

		$session = session();
		$level = $session->get('level');
		$akses = $model_Global->cek_menu_akses($level, 327);
		// Table
		$builder            = $db->table('ap_retur');
		$builderDetail      = $db->table('ap_returdetil');
		$INVtransaksi       = $db->table('inv_transaksi');
		try {
			if ($akses->uadd == 1 || $akses->uedit == 1) {
				$db->transStart();
				$nomorbukti     = $input['nomorbukti'];
				$cabang         = $session->get('cabang');
				$gudang         = $input['gudang'];
				$kodepu         = $input['kodepu'];
				$_vvdiskon	= str_replace(",", "", $input['vdiskon']);
				$_vvdpp		= str_replace(",", "", $input['vdpp']);
				$_vvppn		= str_replace(",", "", $input['vppn']);
				$_vvtotal	= str_replace(",", "", $input['vtotal']);

				$returfile = $builder->getWhere(['koderetur' => $nomorbukti, 'kodecbg' => $cabang])->getRow();
				if (empty($returfile)) {
					if ($input['sppn'] == 'Y') {
						$nobukti = $nomorbukti;
					} else {
						$nobukti = $nomorbukti;
					}
				} else {
					if ($input['sppn'] == 'Y') {
						$nobukti = $model_Global->_Autonomor('PR');
					} else {
						$nobukti = $model_Global->_Autonomor('PR');
					}
				}

				if ($param != 0) {
					$dataretur = $builderDetail->getWhere(['koderetur' => $nomorbukti, 'kodecbg' => $cabang, 'kodegudang' => $input['gudang']])->getResult();
					foreach ($dataretur as $row) {
						$db->query('update inv_barang set stok = stok + ' . $row->qtyretur . ', stok_so = stok_so + ' . $row->qtyretur . ' where kodeitem = "' . $row->kodeitem . '"');

						$db->query('update inv_nobatch set qty_terima = qty_terima + ' . $row->qtyretur . ', qty_saldo = qty_saldo + ' . $row->qtyretur . ' where kodeitem = "' . $row->kodeitem . '" and no_batch = "' . $row->no_batch . '" ');

						$db->query('update inv_barang_supplier set qty_masuk = qty_masuk + ' . $row->qtyretur . ', stok = stok + ' . $row->qtyretur . ' where kodeitem = "' . $row->kodeitem . '" and no_batch = "' . $row->no_batch . '"');

						$db->query('update ap_pudetail set qtypu = qtypu + ' . $row->qtyretur . ' where kodeitem = "' . $row->kodeitem . '" and no_batch = "' . $row->no_batch . '" and kodepu = "' . $kodepu . '"');

						$db->query('update inv_barang_gudang set qty = qty + ' . $row->qtyretur . ' where gudang = "' . $gudang . '" and kodeitem = "' . $row->kodeitem . '"');

						$INVtransaksi->delete(['nobukti' => $nomorbukti, 'kodeitem' => $row->kodeitem]);
					}
					$builderDetail->delete(['koderetur' => $nomorbukti, 'kodecbg' => $cabang, 'kodegudang' => $input['gudang']]);
				}

				$kode       = $input['kode'];
				$qty        = $input['qty'];
				$sat        = $input['sat'];
				$no_batch   = $input['no_batch'];
				$tgl_exp    = $input['tgl_exp'];
				$harga1     = $input['harga'];
				$disc       = $input['disc'];

				$jumdata  = count($kode);

				$tot = 0;
				$tdisc = 0;

				for ($i = 0; $i < $jumdata; $i++) {

					$harga = str_replace(',', '', $harga1[$i]);
					$vjum  = $qty[$i] * $harga;
					$vdisc = $vjum * ($disc[$i] / 100);
					$tot   = $tot + $vjum;
					$tdisc = $tdisc + $vdisc;

					$datad = array(
						'kodeitem' => $kode[$i],
						'qtyretur' => $qty[$i],
						'satuan' => $sat[$i],
						'no_batch' => $no_batch[$i],
						'tanggal_exp' => $tgl_exp[$i],
						'hargabeli' => $harga,
						'disc' => $disc[$i],
						'kodegudang' => $gudang,
					);

					$data_inv = array(
						'tanggal'   => date('Y-m-d'),
						'kodeitem' => $kode[$i],
						'penerimaan' => 0,
						'pengeluaran' => $qty[$i],
						'satuan' => $sat[$i],
						'jenis' => 'Retur Pembelian',
						'nobukti' => $nobukti,
						'tglrekam'   => date('Y-m-d H:i:s'),
						'hpp'   => $harga,
						'hargajual'   => 0,
						'gudang' => $gudang,
						'no_batch' => $no_batch[$i],
						'tanggal_exp' => $tgl_exp[$i],
						'cabang' => $cabang,
					);

					if ($kode[$i] != "") {
						if ($param == 0) {
							$datad['koderetur'] = $nobukti;
							$builderDetail->insert($datad);
							$INVtransaksi->insert($data_inv);

							$db->query("update inv_nobatch set qty_terima = qty_terima - '$qty[$i]', qty_saldo = qty_saldo - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and cabang='$cabang' and gudang='$gudang'");

							$db->query("update ap_pudetail set qtypu = qtypu - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]' and kodepu = '$kodepu' and gudang='$gudang'");

							$db->query("update inv_barang_supplier set qty_masuk = qty_masuk - '$qty[$i]', stok = stok - '$qty[$i]' where kodeitem = '$kode[$i]' and no_batch = '$no_batch[$i]'");

							$db->query("update inv_barang set stok = stok - '$qty[$i]', stok_so = stok_so - '$qty[$i]' where kodeitem = '$kode[$i]' and kdcbg='$cabang' and kdgudang='$gudang'");

							$db->query("update inv_barang_gudang set qty = qty - '$qty[$i]' where gudang = '$gudang' and kodeitem = '$kode[$i]' and cabang='$cabang' ");
						} else {
							$datad['koderetur'] = $nomorbukti;
							$builderDetail->insert($datad);
						}
					}
				}

				//Table 2
				$BayarDetail   = $db->table('ap_bayardetil');
				$ApPufile     = $db->table('ap_pufile');

				if ($input['sppn'] == "Y") {
					$tppn = ($tot - $tdisc) * 0.11;
				} else {
					$tppn = 0;
				}

				$data = array(
					'kodecbg' => $session->get('cabang'),
					'kodesup' => $input['supp'],
					'kodepu'  => $input['kodepu'],
					'tglretur'   => date('Y-m-d', strtotime($input['tanggal'])),
					'kodeuser' => $session->get('username'),
					'keterangan' => $input['keterangan'],
					'totalretur' => $_vvtotal,
					'dpp'     => $_vvdpp,
					'ppn'     => $_vvppn,
					'diskon'  => $_vvdiskon,
					'sppn'    => $input['sppn'],
					'statusid' => 1,
					'gudang' => $input['gudang'],
				);


				if ($param != 0) {
					$model_Global->_hapusjurnal($nomorbukti, 'JP');
				}

				$profile = $model_Global->_LoadProfile();
				$akun_debet     = $profile->akun_hutang;
				$akun_kredit    = $profile->akun_persediaan;

				$total = $_vvtotal;

				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'JP',
					$input['kodepu'],
					1,
					$akun_debet,
					'Retur Pembelian ' . $input['kodepu'],
					'Retur Pembelian ' . $input['kodepu'],
					$total,
					0
				);

				$model_Global->_rekamjurnal(
					date('Y-m-d', strtotime($input['tanggal'])),
					$nobukti,
					'JP',
					$input['kodepu'],
					2,
					$akun_kredit,
					'Retur Pembelian ' . $input['kodepu'],
					'Retur Pembelian ' . $input['kodepu'],
					0,
					$total
				);

				$total_seluruh = $_vvtotal;
				$data_bayar = $BayarDetail->getWhere(['nomorfaktur' => $input['kodepu'], 'cabang' => $session->get('cabang')])->getRow();
				$data_faktur = $ApPufile->getWhere(['kodepu' => $input['kodepu'], 'kodecbg' => $session->get('cabang')])->getRow();

				$datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
				if ($param == 0) {
					$data['koderetur'] = $nobukti;
					$builder->insert($data);
					$datalogs['detail'] = 'Tambah data Pembelian Retur dengan kode ' . $nobukti;
					$db->table("logs")->insert($datalogs);
					if ($data_faktur->jumlahbayar == 0) {
						$db->query("update ap_pufile set retur=2, dpp = dpp -'$tot', ppn = ppn -'$tppn', totalpu = totalpu -'$total_seluruh' where kodepu = '$kodepu'");
					} else {
						$db->query("update ap_pufile set retur=2, dpp = dpp -'$tot', ppn = ppn -'$tppn', jumlahbayar = jumlahbayar -'$total_seluruh', totalpu = totalpu -'$total_seluruh' where kodepu = '$kodepu'");
					}

					if ($data_bayar != null) {
						$db->query("update ap_bayardetil set totalfaktur= totalfaktur -'$total_seluruh', terhutang = terhutang -'$total_seluruh', bayar = bayar -'$total_seluruh' where nomorfaktur = '$kodepu'");
						$db->query("update ap_bayar set jumlahbayar = jumlahbayar -'$total_seluruh' where kodebyr = '$data_bayar->kodebyr' and kodecbg='$cabang'");
					}

					$db->query("update tr_jurnal set debet = debet -'$total_seluruh' where kredit='0.00' and novoucher = '$kodepu' and kodecbg='$cabang'");

					$db->query("update tr_jurnal set kredit = kredit -'$total_seluruh' where debet='0.00' and novoucher = '$kodepu' and kodecbg='$cabang'");

					$model_Global->_updatecounter1('PR');
					echo json_encode(['status' => 1, 'message' => $nobukti]);
				} else {
					$data['koderetur'] = $nomorbukti;
					$builder->where(['koderetur' => $nomorbukti, 'kodecbg' => $cabang])->update($data);
					$datalogs['detail'] = 'Edit data Pembelian Retur dengan kode ' . $nomorbukti;
					$db->table("logs")->insert($datalogs);
					echo json_encode(['status' => 1, 'message' => $nomorbukti]);
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

	public function cetakretur($param)
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

			$queryh = "select ap_retur.*, ap_supplier.*, ap_pufile.kodepo, ap_pufile.nomorfaktur_pajak from ap_retur inner join ap_supplier on ap_retur.kodesup=ap_supplier.kode INNER JOIN ap_pufile ON ap_pufile.kodepu = ap_retur.kodepu where koderetur = '$param'";
			$queryd = "select ap_returdetil.*, inv_barang.namabarang from ap_returdetil inner join inv_barang on ap_returdetil.kodeitem=inv_barang.kodeitem where ap_returdetil.koderetur = '$param' and inv_barang.kdcbg='$unit'";

			$detil  = $db->query($queryd)->getResult();
			$header = $db->query($queryh)->getRow();
			$pdf = new simkeu_nota_helper();
			$pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
			$pdf->setjudul('');
			$pdf->setsubjudul('');
			$pdf->addpage("P", "A4");
			$pdf->setsize("P", "A4");
			$pdf->SetWidths(array(70, 30, 90));
			$border = array('T', '', 'BT');
			$size   = array('0', '0', '0');
			$pdf->setfont('Arial', 'B', 18);
			$pdf->SetAligns(array('C', 'C', 'C'));
			$align = array('L', 'C', 'L');
			$style = array('', '', 'B');
			$size  = array('12', '0', '18');
			$max   = array(5, 5, 20);
			$judul = array('Kepada :', '', 'Retur Pembelian');
			$fc     = array('0', '0', '0');
			$hc     = array('20', '20', '20');
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
			$pdf->FancyRow(array($header->alamat2, '', 'No. Ref', ':', $header->kodepu), $fc, $border);
			$pdf->FancyRow(array($header->telp, '', 'No. PO', ':', $header->kodepo), $fc, $border);
			$pdf->FancyRow(array('', '', 'Faktur Pajak', ':', $header->nomorfaktur_pajak), $fc, $border);


			$pdf->ln(2);

			$pdf->SetWidths(array(20, 46, 18, 19, 14, 15, 18, 15, 25));
			$border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB', 'TB');
			$align  = array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R', 'R');
			$pdf->setfont('Arial', 'B', 9);
			$pdf->SetAligns(array('L', 'C', 'R'));
			//$pdf->SetFillColor(0,0,139);
			//$pdf->settextcolor(255,255,255);
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
				$dpp = $db->qtyretur * $db->hargabeli;
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
					number_format($db->hargabeli, 0, '.', ','),
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
			$pdf->SetWidths(array(50, 50));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', 'C'));
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
			$pdf->SetWidths(array(50, 20, 50, 20, 50));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetAligns(array('C', '', 'C', '', 'C'));
			$pdf->ln(5);
			$border = array('', '', '', '', '');
			$align  = array('C', 'C', 'C', 'C', 'C');
			$pdf->FancyRow(array('Bagian Pembelian', '', 'Gudang', '', 'Accounting'), 0, $border, $align);
			$pdf->ln(1);
			$pdf->ln(15);
			$pdf->SetWidths(array(50, 20, 50, 18, 50));
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
			$border = array('B', '', 'B', '', 'B', '', 'B');
			$pdf->FancyRow(array('', '', '', '', '', '', ''), 0, $border, $align);
			$border = array('', '', '', '', '', '', '');
			$align  = array('L', 'C', 'L');
			$pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.'), 0, $border, $align);

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
		$DataSupp = $db->table('ap_supplier')->get()->getResult();
		$barang = $db->table("inv_barang")->getWhere(['kdcbg' => $session->get('cabang')])->getResult();
		$css = [];
		$js = [];
		$data  = [
			'css' => $css,
			'js' => $js,
			'title' => 'Laporan Pembelian',
			'body' => 'admin/pembelian/laporan',
			'cbg' => $cabang,
			'supp' => $DataSupp,
			'barang' => $barang,
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
			$supp  = $data[3];
			$brg   = $data[4];
			$cab   = $data[5];

			$_tgl1 = date('Y-m-d', strtotime($tgl1));
			$_tgl2 = date('Y-m-d', strtotime($tgl2));
			$_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
			$_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
			$tgl_ppn = date('2022-04-01');

			if ($_tgl1 >= $tgl_ppn) {
				$h_ppn = 0.11;
			} else {
				$h_ppn = 0.1;
			}

			if ($idlp == 101) {
				$query =
					"select ap_pufile.*, ap_supplier.nama as namapemasok from ap_pufile inner join ap_supplier on ap_pufile.kodesup=ap_supplier.kode 
			 where tglkirim between '$_tgl1' and '$_tgl2' ";

				if ($supp != "") {
					$query .= "and ap_pufile.kodesup = '$supp'";
				}
				if ($cab != "") {
					$query .= "and ap_pufile.kodecbg = '$cab'";
				} else {
					$query .= "and ap_pufile.kodecbg = '01'";
				}
				$query .= "order by tglkirim, kodepu";

				$lap = $db->query($query)->getResult();
				$pdf = new simkeu_rpt_helper();
				$pdf->setID($nama_usaha, $motto, $alamat);
				$pdf->setunit($namaunit);
				$pdf->setjudul('Laporan Pembelian Global');
				$pdf->setsubjudul($_peri);
				$pdf->addpage("L", "A4");
				$pdf->setsize("L", "A4");
				$pdf->SetWidths(array(10, 30, 20, 60, 20, 28, 28, 28, 28, 28));
				$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
				$judul = array('No.', 'Nomor ', 'Tgl Input', 'Pelanggan', 'Tgl Faktur', 'Bruto', 'Disc', 'Netto',  'PPN', 'DPP+PPN');
				$pdf->setfont('Times', 'B', 10);
				$pdf->row($judul);
				$pdf->setheadertable($judul);
				$pdf->SetWidths(array(10, 30, 20, 60, 20, 28, 28, 28, 28, 28));
				$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'R', 'R', 'R', 'R', 'R'));
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

				foreach ($lap as $row) {
					$per_item = "SELECT * from ap_pudetail WHERE kodepu='$row->kodepu'";
					$lap1   = $db->query($per_item)->getResult();
					$subtotalItem = 0;
					$discItem = 0;
					foreach ($lap1 as $row2) {
						$qtypu = str_replace(',', '', $row2->qtypu);
						$hargabeli = str_replace(',', '', $row2->hargabeli);
						$subtotalItem = $subtotalItem + $qtypu * $hargabeli;
						$convdisc = ($row2->disc / 100) * $qtypu * $hargabeli;
						$discItem = $discItem +  $convdisc;
					}

					$tdpp = $subtotalItem - $discItem;
					if ($row->sppn == "Y") {
						$ppn = ($subtotalItem - $discItem) * 0.11;
					} else {
						$ppn = 0;
					}
					$dpp_ppn = $tdpp + $ppn;

					if ($tdpp >= 1) {
						$pdf->row(array(
							$nourut,
							$row->kodepu,
							date('d-m-Y', strtotime($row->tglkirim)),
							$row->namapemasok,
							date('d-m-Y', strtotime($row->tglpu)),
							number_format($subtotalItem, 0, '.', ','),
							number_format($discItem, 0, '.', ','),
							number_format($tdpp, 0, '.', ','),
							number_format($ppn, 0, '.', ','),
							number_format($dpp_ppn, 0, '.', ',')
						));
						$nourut++;
						$totbruto 	= $totbruto + $subtotalItem;
						$totnetto 	= $totnetto + $tdpp;
						$totppn 	= $totppn + $ppn;
						$totdisc  	= $totdisc + $discItem;
						$tothrg  	= $tothrg + $dpp_ppn;
					}
				}
				$pdf->SetWidths(array(140, 28, 28, 28, 28, 28));
				$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));

				$pdf->setfont('Times', 'B', 9);
				$pdf->Row(array(
					'Total  ',
					number_format($totbruto, 0, ',', '.'),
					number_format($totdisc, 0, ',', '.'),
					number_format($totnetto, 0, ',', '.'),
					number_format($totppn, 0, ',', '.'),
					number_format($tothrg, 0, ',', '.')
				));

				$pdf->SetTitle('LAPORAN PEMBELIAN GLOBAL');
				$pdf->AliasNbPages();
				$pdf->output('pembelian_101.PDF', 'I');
				exit;
			} else
				// Laporan pembelian (DETAIL)
				if ($idlp == 102) {
					$query =
						"select ap_retur.*, ap_supplier.nama as namapemasok from ap_retur inner join ap_supplier on ap_retur.kodesup=ap_supplier.kode 
			 where tglretur between '$_tgl1' and '$_tgl2' ";

					if ($supp != "") {
						$query .= "and ap_retur.kodesup = '$supp'";
					}
					if ($cab != "") {
						$query .= "and ap_retur.kodecbg = '$cab'";
					} else {
						$query .= "and ap_retur.kodecbg = '01'";
					}
					$query .= "order by tglretur, koderetur";

					$lap = $db->query($query)->getResult();
					$pdf = new simkeu_rpt_helper();
					$pdf->setID($nama_usaha, $motto, $alamat);
					$pdf->setunit($namaunit);
					$pdf->setjudul('Laporan Retur Pembelian ( Detail )');
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
						$pdf->Fancyrow(array('', $row->namapemasok, ''), $border, $align);
						$pdf->ln(1);


						$nourut = 1;
						$totqty  = 0;
						$tothrgjual  = 0;
						$tothrg  = 0;
						$totdisc = 0;
						$per_sup = "SELECT ap_returdetil.*, inv_barang.namabarang, ap_retur.kodepu, ap_retur.keterangan, ap_retur.tglretur FROM ap_returdetil INNER JOIN inv_barang ON ap_returdetil.kodeitem = inv_barang.kodeitem INNER JOIN ap_retur ON ap_returdetil.koderetur = ap_retur.koderetur where ap_retur.kodesup='" . $row->kodesup . "' and tglretur between '$_tgl1' and '$_tgl2' order by koderetur";
						$lap1   = $db->query($per_sup)->getResult();
						$pdf->SetWidths(array(10, 30, 30, 18, 70, 15, 20, 15, 20, 50));
						$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
						$judul = array('No.', 'No Retur', 'No Faktur', 'Tanggal', 'Nama Barang', 'Qty', 'Harga', 'Disc', 'Netto', 'Keterangan');
						$pdf->setfont('Times', 'B', 10);
						$pdf->row($judul);
						$pdf->SetWidths(array(10, 30, 30, 18, 70, 15, 20, 15, 20, 50));
						$pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'L'));
						$pdf->setfont('Times', '', 9);
						$pdf->SetFillColor(224, 235, 255);
						$pdf->SetTextColor(0);
						$pdf->SetFont('');
						foreach ($lap1 as $row2) {
							$tot 			= ($row2->qtyretur * $row2->hargabeli);
							$dis 			= ($row2->disc / 100) * $tot;
							$total_harga 	= $tot - $dis;
							if ($row2->qtyretur >= 1) {

								$pdf->row(array($nourut, $row2->koderetur, $row2->kodepu, date('d-m-Y', strtotime($row2->tglretur)), $row2->namabarang, $row2->qtyretur, number_format($row2->hargabeli, 0, '.', ','), $row2->disc, number_format($total_harga, 0, '.', ','), $db->keterangan));
								$totqty = $totqty + $row2->qtyretur;
								$tothrgjual = $tothrgjual + $row2->hargabeli;
								$totdisc = $totdisc + $row2->disc;
								$tothrg = $tothrg + $total_harga;
								$nourut++;
							}
						}


						$pdf->SetWidths(array(158, 15, 20, 15, 20, 50));
						$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R'));
						$pdf->setfont('Times', 'B', 10);
						$pdf->row(array(
							'TOTAL',
							number_format($totqty, 0, ',', '.'),
							number_format($tothrgjual, 0, ',', '.'),
							number_format($totdisc, 0, ',', '.'),
							number_format($tothrg, 0, ',', '.'),
							''
						));
					}

					$pdf->SetTitle('LAPORAN RETUR PEMBELIAN');
					$pdf->AliasNbPages();
					$pdf->output('pembelian_103.PDF', 'I');
					exit;
				} else
					//pembelian per supplier 	  
					if ($idlp == 103) {
						$query =
							"SELECT ap_supplier.nama, ap_pufile.*, ap_pudetail.kodeitem FROM ap_pufile INNER JOIN ap_supplier ON ap_pufile.kodesup = ap_supplier.kode INNER JOIN ap_pudetail ON ap_pufile.kodepu = ap_pudetail.kodepu
			 where tglkirim between '$_tgl1' and '$_tgl2' ";

						if ($supp != "") {
							$query .= "and ap_pufile.kodesup = '$supp'";
						}
						if ($cab != "") {
							$query .= "and ap_pufile.kodecbg = '$cab'";
						} else {
							$query .= "and ap_pufile.kodecbg = '01'";
						}
						if ($brg != "") {
							$query .= "and ap_pudetail.kodeitem = '$brg'";
						}
						$query .= "group by nama order by tglpu, kodepo";

						$lap = $db->query($query)->getResult();
						$pdf = new simkeu_rpt_helper();
						$pdf->setID($nama_usaha, $motto, $alamat);
						$pdf->setunit($namaunit);
						$pdf->setjudul('Laporan Pembelian Detail');
						$pdf->setsubjudul($_peri);
						$pdf->addpage("L", "A4");
						$pdf->setsize("L", "A4");
						$pdf->ln(-5);
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
							$totdisc  = 0;
							$per_sup = "SELECT ap_pudetail.*, inv_barang.namabarang, ap_pufile.kodepu, ap_pufile.tglpu, ap_pufile.tglkirim FROM ap_pudetail INNER JOIN inv_barang ON ap_pudetail.kodeitem = inv_barang.kodeitem INNER JOIN ap_pufile ON ap_pudetail.kodepu = ap_pufile.kodepu where ap_pufile.kodesup='" . $row->kodesup . "' and tglkirim between '$_tgl1' and '$_tgl2' and inv_barang.kdcbg='$unit'";
							if ($brg != "") {
								$per_sup .= " and ap_pudetail.kodeitem = '$brg'";
							}
							$lap1   = $db->query($per_sup)->getResult();
							$pdf->SetWidths(array(10, 30, 20, 90, 20, 20, 28, 28, 35));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
							$judul = array('No.', 'No Faktur', 'tgl Input', 'Nama Barang', 'tgl Faktur', 'Qty', 'Harga', 'Disc', 'Netto');
							$pdf->setfont('Times', 'B', 10);
							$pdf->row($judul);
							$pdf->SetWidths(array(10, 30, 20, 90, 20, 20, 28, 28, 35));
							$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'R', 'R', 'R', 'R'));
							$pdf->setfont('Times', '', 9);
							$pdf->SetFillColor(224, 235, 255);
							$pdf->SetTextColor(0);
							$pdf->SetFont('');

							foreach ($lap1 as $row2) {
								$tot 			= ($row2->qtypu * $row2->hargabeli);
								$dis 			= ($row2->disc / 100) * $tot;
								$total_harga 	= $tot - $dis;

								if ($row2->qtypu >= 1) {
									$pdf->row(array(
										$nourut,
										$row2->kodepu,
										date('d-m-Y', strtotime($row2->tglkirim)),
										$row2->namabarang,
										date('d-m-Y', strtotime($row2->tglpu)),
										$row2->qtypu,
										number_format($row2->hargabeli, 0, '.', ','),
										$row2->disc,
										number_format($total_harga, 0, '.', ',')
									));

									$totqty = $totqty + $row2->qtypu;
									$tothrgjual = $tothrgjual + $row2->hargabeli;
									$totdisc = $totdisc + $row2->disc;
									$tothrg = $tothrg + $total_harga;
									$nourut++;
								}
							}
							$pdf->SetWidths(array(170, 20, 28, 28, 35));
							$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R'));

							$pdf->setfont('Times', 'B', 9);
							$pdf->Row(array(
								'Total  ',
								number_format($totqty, 0, ',', '.'),
								number_format($tothrgjual, 0, ',', '.'),
								number_format($totdisc, 0, ',', '.'),
								number_format($tothrg, 0, ',', '.')
							));
						}
						$jumlah = "SELECT sum(qtypu) as jumlah_qty from ap_pudetail inner join ap_pufile on ap_pudetail.kodepu = ap_pufile.kodepu where  tglpu between '$_tgl1' and '$_tgl2'";
						if ($supp != "") {
							$jumlah .= "and ap_pufile.kodesup = '$supp'";
						}
						if ($cab != "") {
							$jumlah .= "and ap_pufile.kodecbg = '$cab'";
						}
						if ($brg != "") {
							$jumlah .= "and ap_pudetail.kodeitem = '$brg'";
						}
						$lap2   = $db->query($jumlah)->getResult();
						foreach ($lap2 as $count) {
							$pdf->setfont('Times', 'B', 10);
							$border = array('', '', '');
							$align = array('L', '', 'L');
							$pdf->SetWidths(array(10, 30, 25));
							$pdf->ln(5);
							$pdf->Fancyrow(array('', 'Grand Total Qty :', number_format($count->jumlah_qty)), $border, $align);
						}
						$pdf->SetTitle('LAPORAN PEMBELIAN DETAIL');
						$pdf->AliasNbPages();
						$pdf->output('pembelian_106.PDF', 'I');
						exit;
					} else
						//Faktur belum lunas
						if ($idlp == 201) {
							$query =
								"select ap_pufile.*, ap_supplier.nama as namapemasok from ap_pufile inner join ap_supplier on ap_pufile.kodesup=ap_supplier.kode 
			 where tglkirim between '$_tgl1' and '$_tgl2' and totalpu>jumlahbayar ";

							if ($supp != "") {
								$query .= " and ap_pufile.kodesup = '$supp'";
							}
							if ($cab != "") {
								$query .= " and ap_pufile.kodecbg = '$cab'";
							}
							$query .= " order by tglkirim, kodepo";

							$lap = $db->query($query)->getResult();
							$pdf = new simkeu_rpt_helper();
							$pdf->setID($nama_usaha, $motto, $alamat);
							$pdf->setunit($namaunit);
							$pdf->setjudul('Faktur Belum Lunas');
							$pdf->SetMargins(25, 10);
							$pdf->setsubjudul($_peri1);
							$pdf->addpage("L", "A4");
							$pdf->setsize("L", "A4");
							$pdf->SetWidths(array(10, 60, 35, 20, 25, 25, 25, 25, 20));
							$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
							$judul = array('No.', 'Supplier', 'Nomor', 'Tanggal', 'Jatuh Tempo', 'Total', 'Hutang', 'Hutang Pajak', 'Umur (hr)');
							$pdf->setfont('Times', 'B', 10);
							$pdf->row($judul);
							$pdf->setheadertable($judul);
							$pdf->SetWidths(array(10, 60, 35, 20, 25, 25, 25, 25, 20));
							$pdf->SetAligns(array('C', 'L', 'C', 'C', 'C', 'R', 'R', 'R', 'R'));
							$pdf->setfont('Times', '', 9);
							$pdf->SetFillColor(224, 235, 255);
							$pdf->SetTextColor(0);
							$pdf->SetFont('');

							$nourut = 1;
							$totfaktur  = 0;
							$tothutang  = 0;
							$tothutangpjk  = 0;

							foreach ($lap as $db) {
								$hutang    = $db->totalpu - $db->jumlahbayar;
								$hutangpjk = $db->ppn - $db->ppnbayar;
								$date1 = strtotime($tgl2);
								$date2 = strtotime($db->tglpu);
								$umur  = ($date1 - $date2) / (60 * 60 * 24);
								$selisihumurhr = date_diff(date_create($tgl2), date_create($db->tglpu));
								//$umur = 0;
								$pdf->row(array(
									$nourut, $db->namapemasok,
									$db->kodepu,
									date('d-m-Y', strtotime($db->tglpu)),
									date('d-m-Y', strtotime($db->tgljthtempo)),
									number_format($db->totalpu, 0, '.', ','),
									number_format($hutang, 0, '.', ','),
									number_format($hutangpjk, 0, '.', ','),
									$selisihumurhr->days
								));
								$totfaktur = $totfaktur + $db->totalpu;
								$tothutang = $tothutang + $hutang;
								$tothutangpjk = $tothutangpjk + $hutangpjk;
								$nourut++;
							}

							$pdf->SetWidths(array(150, 25, 25, 25, 20, 20, 35));
							$pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));
							$pdf->setfont('Times', 'B', 10);
							$pdf->row(array(
								'TOTAL',
								number_format($totfaktur, 0, ',', '.'),
								number_format($tothutang, 0, ',', '.'),
								number_format($tothutangpjk, 0, ',', '.'),
								''
							));

							$pdf->SetTitle('LAPORAN FAKTUR BELUM LUNAS');
							$pdf->AliasNbPages();
							$pdf->output('pembelian_201.PDF', 'I');
							exit;
						} else
							//Umur Hutang
							if ($idlp == 203) {
								$query = "select * from ap_supplier order by nama";

								$lap = $db->query($query)->getResult();
								$pdf = new simkeu_rpt_helper();
								$pdf->setID($nama_usaha, $motto, $alamat);
								$pdf->setunit($namaunit);
								$pdf->setjudul('Umur Hutang');
								$pdf->setsubjudul($_peri1);
								$pdf->addpage("L", "A4");
								$pdf->setsize("L", "A4");
								$pdf->SetWidths(array(10, 60, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
								$judul = array('No.', 'Pemasok', 'Hutang', 'Blm Tempo', '1 - 30', '31 - 60', '61- 90', '91 - 120', '> 120');
								$pdf->setfont('Times', 'B', 10);
								$pdf->row($judul);
								$pdf->SetWidths(array(10, 60, 25, 25, 25, 25, 25, 25, 25));
								$pdf->SetAligns(array('C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
								$pdf->setfont('Times', '', 9);
								$pdf->SetFillColor(224, 235, 255);
								$pdf->SetTextColor(0);
								$pdf->SetFont('');

								$nourut = 1;

								$tothutang = 0;
								$totbelum  = 0;
								$tottempo1 = 0;
								$tottempo2 = 0;
								$tottempo3 = 0;
								$tottempo4 = 0;
								$tottempo5 = 0;
								foreach ($lap as $row) {
									$supp = $row->kode;

									$query2 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and kodesup = '$supp'";
									$data2 = $db->query($query2)->getRow();
									$hutang  = $data2->total;

									$query3 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) < 1 and kodesup = '$supp'";
									$data3 = $db->query($query3)->getRow();
									$blmtempo  = $data3->total;

									$query4 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 1 and 30 and kodesup = '$supp'";
									$data4 = $db->query($query4)->getRow();
									$tempo1  = $data4->total;

									$query5 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 31 and 60 and kodesup = '$supp'";
									$data5 = $db->query($query5)->getRow();
									$tempo2  = $data5->total;

									$query6 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 61 and 90 and kodesup = '$supp'";
									$data6 = $db->query($query6)->getRow();
									$tempo3  = $data6->total;

									$query7 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 91 and 120 and kodesup = '$supp'";
									$data7 = $db->query($query7)->getRow();
									$tempo4  = $data7->total;

									$query8 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) > 120 and kodesup = '$supp'";
									$data8 = $db->query($query8)->getRow();
									$tempo5  = $data8->total;


									$tothutang = $tothutang + $hutang;
									$totbelum  = $totbelum + $blmtempo;
									$tottempo1 = $tottempo1 + $tempo1;
									$tottempo2 = $tottempo2 + $tempo2;
									$tottempo3 = $tottempo3 + $tempo3;
									$tottempo4 = $tottempo4 + $tempo4;
									$tottempo5 = $tottempo5 + $tempo5;

									//$umur = 0;
									$pdf->row(array(
										$nourut, $row->nama,
										number_format($hutang, 0, '.', ','),
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
									number_format($tothutang, 0, '.', ','),
									number_format($totbelum, 0, '.', ','),
									number_format($tottempo1, 0, '.', ','),
									number_format($tottempo2, 0, '.', ','),
									number_format($tottempo3, 0, '.', ','),
									number_format($tottempo4, 0, '.', ','),
									number_format($tottempo5, 0, '.', ',')
								));

								$pdf->SetTitle('LAPORAN UMUR HUTANG');
								$pdf->AliasNbPages();
								$pdf->output('pembelian_203.PDF', 'I');
								exit;
							}
		} else {
			header('location:' . base_url());
		}
	}

	//Data Submit
	public function submit_data($id)
	{
		// $data = [
		//     'nama_laporan' => $this->request->getPost('nama_laporan'),
		//     'suplier'  => $this->request->getPost('suplier'),
		//     'mulai'  => $this->request->getPost('mulai'),
		//     'sd'  => $this->request->getPost('sd'),
		//     'barang'  => $this->request->getPost('barang'),
		//     'cabang'  => $this->request->getPost('cabang'),
		// ];

		echo json_encode(true);
	}

	//Data Sub Pembeliad Page Start Here
	public function data_sub_pembelian()
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
			'title' => 'Data Sub Pembelian',
			'body' => 'admin/pembelian/exportData/dataSubPembelian',
		];
		return view('layout/layout', $data);
	}

	public function datasubpembelian_table($tgl1, $tgl2)
	{
		error_reporting(0);
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$cabang = $session->get('cabang');
		$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
		$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
		$db = db_connect();
		$query = "SELECT a.kodepu, a.tglentry, a.tglpu, a.nomorfaktur_pajak, a.nomorfaktur, a.kodesup, c.namabarang, c.kdmerk, c.kdkategori, b.qtypu, c.hargajual, 
				    b.disc, a.diskon, a.dpp, a.sppn, a.ppn, a.totalpu, b.hargabeli, a.statusid, b.kodeitem, c.stok, b.tanggal_exp, b.satuan, a.kodepo 
				    FROM ap_pufile a, ap_pudetail b, inv_barang c 
				    WHERE a.kodepu = b.kodepu AND b.kodeitem = c.kodeitem and c.kdcbg='$cabang'";

		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and a.tglkirim between '$_tgl1' and '$_tgl2'";
		} else {
			$query .= " and year(a.tglkirim)= (select periode_tahun from ms_identity) and
            month(a.tglkirim)= (select periode_bulan from ms_identity)";
		}
		$query .= " order by a.kodepu";
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$nomor = 1;
		foreach ($res as $row) {
			$subtot = $row->dpp;
			$pajak = $row->nomorfaktur_pajak;

			$tgl_ppn = date('2022-04-01');

			if ($_tgl1 >= $tgl_ppn) {
				$h_ppn = 0.11;
			} else {
				$h_ppn = 0.1;
			}

			$query_retur = $db->query("SELECT * from ap_retur where kodepu= '$row->kodepu'")->getRow();

			$query_retur_detail = $db->query("SELECT * from ap_returdetil where koderetur= '$query_retur->koderetur' and kodeitem ='$row->kodeitem'")->getRow();



			$subjumlah_pu = $row->hargabeli * $row->qtypu;
			$disc_rp_pu = ($row->disc / 100) * $subjumlah_pu;
			$hpp_pu = $subjumlah_pu - $disc_rp_pu;
			if ($row->sppn == "Y") {
				$ppn_pu = $hpp_pu * $h_ppn;
			} else {
				$ppn_pu = 0;
			}
			$jumlah_tot_pu = $hpp_pu + $ppn_pu;

			$subjumlah_retur = $query_retur_detail->hargabeli * $query_retur_detail->qtyretur;
			$disc_rp_retur = ($query_retur_detail->disc / 100) * $subjumlah_retur;
			$hpp_retur = $subjumlah_retur - $disc_rp_retur;
			if ($query_retur->sppn == "Y") {
				$ppn_retur = $hpp_retur * $h_ppn;
			} else {
				$ppn_retur = 0;
			}
			$jumlah_tot_retur = $hpp_retur + $ppn_retur;

			$kategori = $db->query("SELECT * from inv_kategori where kode='" . $row->kdkategori . "'")->getRow();
			$supplier = $db->query("SELECT * from ap_supplier where kode='" . $row->kodesup . "'")->getRow();
			$merk = $db->query("SELECT * from inv_merk where kode='" . $row->kdmerk . "'")->getRow();
			if ($row->statusid == '1') {
				$status = "Belum Lunas";
			} else {
				$status = "Lunas";
			}
			if ($query_retur->kodepu != $row->kodepu) {
				$data[] = [
					$row->kodepu,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($row->tglpu)),
					$row->kodepo,
					$row->namabarang,
					$row->satuan,
					number_format($row->qtypu),
					number_format($row->hargabeli),
					number_format($row->disc),
					number_format($subjumlah_pu),
					number_format($disc_rp_pu),
					number_format($hpp_pu),
					number_format($ppn_pu),
					number_format($jumlah_tot_pu),
					date('d-M-y', strtotime($row->tanggal_exp)),
					$pajak,
					$merk->nama,
					$kategori->nama,
					$supplier->nama,
					$row->nomorfaktur,
					$status,

				];
			} else {
				$data[] = [
					$row->kodepu,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($row->tglpu)),
					$row->kodepo,
					$row->namabarang,
					$row->satuan,
					number_format($row->qtypu),
					number_format($row->hargabeli),
					number_format($row->disc),
					number_format($subjumlah_pu),
					number_format($disc_rp_pu),
					number_format($hpp_pu),
					number_format($ppn_pu),
					number_format($jumlah_tot_pu),
					date('d-M-y', strtotime($row->tanggal_exp)),
					$pajak,
					$merk->nama,
					$kategori->nama,
					$supplier->nama,
					$row->nomorfaktur,
					$status,

				];
				$data[] = [
					$query_retur->koderetur,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($query_retur->tglretur)),
					$row->kodepu,
					$row->namabarang,
					$row->satuan,
					number_format($query_retur_detail->qtyretur),
					number_format($row->hargabeli),
					number_format($row->disc),
					number_format($subjumlah_retur),
					number_format($disc_rp_retur),
					number_format($hpp_retur),
					number_format($ppn_retur),
					number_format($jumlah_tot_retur),
					date('d-M-y', strtotime($row->tanggal_exp)),
					$pajak,
					$merk->nama,
					$kategori->nama,
					$supplier->nama,
					$row->nomorfaktur,
					$status,
				];
				$nomor++;
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}

	//Data Pembeliad Page Start Here
	public function data_pembelian()
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
			'title' => 'Data Pembelian',
			'body' => 'admin/pembelian/exportData/dataPembelian',
		];
		return view('layout/layout', $data);
	}

	public function datapembelian_table($tgl1, $tgl2)
	{
		error_reporting(0);
		$model_Global =  model('Global_model', 'M_GLOBAL');
		$session = session();
		$_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
		$_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
		$db = db_connect();
		$query = "SELECT a.kodepu, a.tglentry, a.tglpu, b.kode, b.nama, b.alamat1, b.kota, b.telp, b.hp, b.npwp, a.totalpu, b.top, a.tgljthtempo, 
		a.jumlahbayar, a.diskon, a.dpp, a.ppn,  a.sppn,  a.pembayaran, b.bataskredit, a.kodeuser, a.dpp, a.statusid, a.nomorfaktur, a.kodepo  
		FROM ap_pufile a, ap_supplier b WHERE a.kodesup = b.kode ";

		if ($tgl1 != "1" && $tgl2 !== "1") {
			$query .= " and a.tglkirim between '$_tgl1' and '$_tgl2'";
		} else {
			$query .= " and year(a.tglpu)= (select periode_tahun from ms_identity) and month(a.tglpu)= (select periode_bulan from ms_identity)";
		}
		$query .= " order by a.kodepu";
		$res = $db->query($query)->getResult();
		$result = array();
		$data = [];
		$nomor = 1;
		foreach ($res as $row) {
			$subtot = $row->dpp;
			$disc = $row->diskon;
			$tgl_ppn = date('2022-04-01');

			if ($_tgl1 >= $tgl_ppn) {
				$h_ppn = 0.11;
			} else {
				$h_ppn = 0.1;
			}

			$query_retur = $db->query("SELECT * from ap_retur where kodepu= '$row->kodepu'")->getRow();
			$subtot_hitung = $query_retur->dpp + $subtot;

			$tdpp = $subtot_hitung - $disc;
			if ($row->sppn == "Y") {
				$ppn = $tdpp * $h_ppn;
			} else {
				$ppn = 0;
			}

			$dpp_ppn = $tdpp + $ppn;

			$bayar = $dpp_ppn - $row->jumlahbayar;
			$tothpp = $row->hargabeli * $row->qtypu;

			$terbilang = ucwords($model_Global->terbilang($bayar));

			if ($subtot == 0) {
				$total = '0';
			} else {
				$total = number_format($dpp_ppn);
			}

			if ($row->pembayaran == 'T') {
				$stsByr =  "Tunai";
			} else {
				$stsByr = "Kredit";
			}

			if ($row->statusid == '1') {
				$statusfp = "Belum Lunas";
			} else {
				$statusfp = "Lunas";
			}

			if ($row->kodepu != $query_retur->kodepu) {
				$data[] = [
					$row->kodepu,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($row->tglpu)),
					$row->kode,
					$row->nama,
					$row->alamat1,
					$row->kota,
					$row->telp,
					$row->hp,
					$row->npwp,
					$total,
					number_format($row->top),
					date('d-M-y', strtotime($row->tgljthtempo)),
					number_format($row->jumlahbayar),
					number_format($subtot_hitung),
					number_format($row->diskon),
					number_format($tdpp),
					number_format($ppn),
					number_format($bayar),
					$stsByr,
					'',
					number_format($row->bataskredit),
					'',
					$row->kodeuser,
					$statusfp,
					number_format($bayar),
					$terbilang,
					$row->nomorfaktur,
					date('d-M-y', strtotime($row->tglpu)),
					$row->kodepo
				];
			} else {
				$data[] = [
					$row->kodepu,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($row->tglpu)),
					$row->kode,
					$row->nama,
					$row->alamat1,
					$row->kota,
					$row->telp,
					$row->hp,
					$row->npwp,
					$total,
					number_format($row->top),
					date('d-M-y', strtotime($row->tgljthtempo)),
					number_format($row->jumlahbayar),
					number_format($subtot_hitung),
					number_format($row->diskon),
					number_format($tdpp),
					number_format($ppn),
					number_format($bayar),
					$stsByr,
					'',
					number_format($row->bataskredit),
					'',
					$row->kodeuser,
					$statusfp,
					number_format($bayar),
					$terbilang,
					$row->nomorfaktur,
					date('d-M-y', strtotime($row->tglpu)),
					$row->kodepo
				];
				$data[] = [
					$row->kodepu,
					date('d-M-y', strtotime($row->tglentry)),
					date('d-M-y', strtotime($row->tglpu)),
					$row->kode,
					$row->nama,
					$row->alamat1,
					$row->kota,
					$row->telp,
					$row->hp,
					$row->npwp,
					$total,
					number_format($row->top),
					date('d-M-y', strtotime($row->tgljthtempo)),
					number_format($row->jumlahbayar),
					number_format($subtot_hitung),
					number_format($row->diskon),
					number_format($tdpp),
					number_format($ppn),
					number_format($bayar),
					$stsByr,
					'',
					number_format($row->bataskredit),
					'',
					$row->kodeuser,
					$statusfp,
					number_format($bayar),
					$terbilang,
					$row->nomorfaktur,
					date('d-M-y', strtotime($row->tglpu)),
					$row->kodepo
				];
				$nomor++;
			}
		}

		$result = ['data' => $data];
		echo json_encode($result);
	}
}
