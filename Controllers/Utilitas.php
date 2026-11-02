<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Utilitas
 **/

namespace App\Controllers;

class Utilitas extends BaseController
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
    //Pesanan Aplikasi Page Start Here
    public function pengguna()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/utilitas/pengguna',
        ];
        return view('layout/layout', $data);
    }

    public function pengguna_save()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('userlogin');
        $input = $this->request->getPost();
        $id = $input['id'];
        $validation->setRule('uidlogin', 'user', 'required');
        $validation->setRule('password', 'password', 'required');
        $validation->setRule('karyawan', 'karyawan', 'required');
        $validation->setRule('unit', 'unit', 'required');
        $validation->setRule('grup', 'grup', 'required');

        if ($validation->withRequest($this->request)->run()) {

            if(!empty($input['debuguser']) && $input['debuguser'] == 'on') {
                $debuguser = 1;
            } else {
                $debuguser = 0;
            }
            $data = [
                'uidlogin' => $input['uidlogin'],
                'username' => $input['karyawan'],
                'pwdlogin' => md5($input['password']),
                'cabang' => $input['unit'],
                'level' => $input['grup'],
                'pegawai_id' => $input['karyawan'],
                'debug' => $debuguser,
            ];
            if ($id == 0) {
                $dataRow = $builder->getWhere(['uidlogin' => $input['uidlogin']])->getRow();
                if ($dataRow == "") {
                    $result = $builder->insert($data);
                    if ($result) {
                        echo json_encode(['message' => 'Data Berhasil Di Tambah']);
                    }
                } else {
                    echo json_encode(['errorinsert' => true, 'message' => 'Userid sudah digunakan',]);
                }
            } else {
                $result = $builder->where('uidlogin', $id)->update($data);
                if ($result) {
                    echo json_encode(['message' => 'Data Berhasil Di Edit']);
                }
            }
        } else {
            $arr = [
                'error' => true,
                'uidErr' => $validation->showError('uidlogin'),
                'passErr' => $validation->showError('password'),
                'karyawanErr' => $validation->showError('karyawan'),
                'unitErr' => $validation->showError('unit'),
                'grupErr' => $validation->showError('grup'),
            ];
            echo json_encode($arr);
        }
    }

    public function pengguna_table()
    {
        $db = db_connect();
        $res = $db->table('userlogin')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $rowCabang = $db->table('ms_unit')->where('kode', $value->cabang)->get()->getRow();
            $data[] = [
                $value->uidlogin,
                $value->username,
                $rowCabang->nama,
                '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . "'" . $value->uidlogin . "'" . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button><button class="btn btn-danger mb-2" onclick="data_delete(' . "'" . $value->uidlogin . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>',
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function pengguna_view($id)
    {
        $db = db_connect();
        $builder = $db->table('userlogin')->where('uidlogin', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function pengguna_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('userlogin');
        $delete = $builder->delete(['uidlogin' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //Group Pemakai Page Start Here
    public function grup_pemakai()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/utilitas/grup_pemakai',
        ];
        return view('layout/layout', $data);
    }

    public function grup_pemakai_table()
    {
        $db = db_connect();
        $res = $db->table('ms_modul_grup')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                $value->nomor,
                $value->nmgrup,
                '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->nomor . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>
                <button class="btn btn-danger mb-2" onclick="data_delete(' . $value->nomor . ')"><i class="fa-solid fa-trash-can"></i> Delete </button>  
                <button class="btn btn-info mb-2" onclick="edit_menu(' . $value->nomor . ')"><i class="fa-solid fa-search"></i> Menu</button>'
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function save_gruppemakai()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 902);
        $builder = $db->table('ms_modul_grup');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $data = array(
                'nmgrup' => $input['nama'],
            );
            if ($param == 0) {
                $builder->insert($data);
            } else {
                $builder->where(['nomor' => $input['kode']])->update($data);
            }
            echo json_encode($input['nama']);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function viewmodulgroup($kode)
    {
        $db = db_connect();
        $data = $db->table("ms_modul_grup")->getWhere(['nomor' => $kode])->getRow();
        echo json_encode($data);
    }

    public function menumodulgroup($x)
    {
        $db = db_connect();

        $qmodul = "select a.*, b.nama from
					 ms_modul_grupd a inner join ms_modul b on b.kode=a.nomor_modul
					 where a.nomor_grup = '$x' order by a.nomor_modul";

        $qmodulapp = "select kode, nama  from ms_modul
                    where
                    kode not in (select nomor_modul from  ms_modul_grupd where nomor_grup='$x')";
        $qgrup = "select nmgrup from  ms_modul_grup where nomor = '$x'";
        $grup = $db->query($qgrup)->getRow();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'namagrup' => $grup->nmgrup,
            'modul' => $db->query($qmodul)->getResult(),
            'modulapp' => $db->query($qmodulapp)->getResult(),
            'idgrup' => $x
        ];
        return view('admin/utilitas/menugrup', $data);
    }

    public function tambah_itemmenu()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $input = $this->request->getPost();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $cek = $session->get('level');
        if (!empty($cek)) {
            $grup = $input['nomor'];
            $jumdata = $input['jumdata'];

            for ($i = 0; $i <= $jumdata; $i++) {
                $notrans = $input['notrans' . $i];

                if (!empty($notrans)) {
                    $datad = array(
                        'nomor_grup' => $grup,
                        'nomor_modul' => $notrans
                    );
                    $insert = $db->table("ms_modul_grupd")->insert($datad);
                    echo json_encode($insert);
                }
            }
        } else {
            header('location:' . base_url());
        }
    }

    public function save_frmmenu()
    {
        error_reporting(0);
        $db = db_connect();
        $input = $this->request->getPost();
        $session = session();
        $cek = $session->get('level');
        if (!empty($cek)) {
            $nomor = $input['nomorG'];
            $jumdata = $input['jumdataG'];


            for ($i = 0; $i <= $jumdata; $i++) {
                $notrans = $input['notransG' . $i];

                $uadd               = $input['uadd' . $i];
                $uedit              = $input['uedit' . $i];
                $ueditpercust       = $input['ueditpercust' . $i];
				$ukurir             = $input['ukurir' . $i];
                $udel               = $input['udel' . $i];
                $ucetak             = $input['ucetak' . $i];
                $uharga             = $input['uharga' . $i];
                $uhargaprofit         = $input['uhargaprofit' . $i];

                $vadd = 0;
                $vedit = 0;
                $veditpercust = 0;
				$vkurir = 0;
                $vdel = 0;
                $vcetak = 0;
                $vharga = 0;
                $vhargaprofit = 0;

                if (!empty($uadd)) $vadd = 1;
                if (!empty($uedit)) $vedit = 1;
                if (!empty($ueditpercust)) $veditpercust = 1;
				if (!empty($ukurir)) $vkurir = 1;
                if (!empty($udel)) $vdel = 1;
                if (!empty($ucetak)) $vcetak = 1;
                if (!empty($uharga)) $vharga = 1;
                if (!empty($uhargaprofit)) $vhargaprofit = 1;
                if (!empty($notrans))
                    $query = $db->query("UPDATE ms_modul_grupd set uadd = '$vadd', uedit = '$vedit', ueditpercust = '$veditpercust',
                        ukurir = '$vkurir',udel = '$vdel', ucetak = '$vcetak', uharga = '$vharga', uhargaprofit = '$vhargaprofit'
                        where nomor_grup = '$nomor' and nomor_modul = '$notrans'");
                echo json_encode($query);
            }
        } else {
            header('location:' . base_url());
        }
    }

    public function deldatagrup($kode)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $input = $this->request->getPost();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $cek = $session->get('level');
        if (!empty($cek)) {
            $builder = $db->table('ms_modul_grupd');
            $delete = $builder->delete(['nomor' => $kode]);
            echo json_encode($delete);
        } else {
            header('location:' . base_url());
        }
    }

    public function deldata($data)
    {
        $session = session();
        $db = db_connect();
        $cek = $session->get('level');
        if (!empty($cek)) {
            $builder = $db->table('ms_modul_grup');
            $delete = $builder->delete(['nomor' => $data]);
            $builder2 = $db->table('ms_modul_grupd');
            $delete2 = $builder2->delete(['nomor_grup' => $data]);
            echo json_encode($data);
        } else {
            header('location:' . base_url());
        }
    }

    //Kode Cabang Page Start Here
    public function kode_cabang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/utilitas/kode_cabang',
        ];
        return view('layout/layout', $data);
    }

    public function kode_cabang_table()
    {
        $db = db_connect();
        $res = $db->table('ms_unit')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                $value->kode,
                $value->nama,
                $value->alamat,
                $value->pimpinan,
                $value->telpon,
                '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button><button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>'
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function kode_cabang_view($id)
    {
        $db = db_connect();
        $kode = '0' . $id;
        $builder = $db->table('ms_unit')->where('kode', $kode)->get()->getRow();
        echo json_encode($builder);
    }

    public function kode_cabang_save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 903);
        $builder = $db->table('ms_unit');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $data = array(
                'kode' => $input['kode'],
                'nama' => $input['nama'],
                'alamat' => $input['alamat'],
                'pimpinan' => $input['pimpinan'],
                'telpon' => $input['telpon'],
            );
            if ($param == 0) {
                $builder->insert($data);
            } else {
                $builder->where(['kode' => $input['kode']])->update($data);
            }
            echo json_encode($input['nama']);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function kode_cabang_delete($id = '')
    {
        $db = db_connect();
        $kode = '0' . $id;
        $builder = $db->table('ms_unit');
        $delete = $builder->delete(['kode' => $kode]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //Profile Page Start Here
    public function profile_edit()
    {
        $session = session();
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $cabang = $session->get('cabang');
        $builder = $db->query("SELECT * FROM ms_identity where kodecbg='$cabang'")->getRow();
        $GetDataCbg = $db->table("ms_unit")->get()->getResult();
        $periodecek = $model_Global->_periodebulan() . '-' . $model_Global->_periodetahun();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/utilitas/profile_edit',
            'data' => $builder,
            'cabang' => $cabang,
            'cbg' => $GetDataCbg,
            'identitas' => $model_Global->_LoadProfile(),
            'periodeData' => $periodecek,
        ];
        return view('layout/layout', $data);
    }

    public function update_profile($idx)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $cabang = $session->get('cabang');
        $akses = $model_Global->cek_menu_akses($level, 904);
        $builder = $db->table('ms_identity');
        if ($akses->uadd == 1 || $akses->uedit == 1) {

            if ($idx == 1) {
                $data = array(
                    'kodecbg' => $input['_kodecbg'],
                    'nama_usaha' => $input['_nama'],
                    'telpon' => $input['_telpon'],
                    'fax' => $input['_fax'],
                    'hp' => $input['_hp'],
                    'email' => $input['_email'],
                    'pwdemail' => $input['_pwdemail'],
                    'smtp_host' => $input['_smtp_host'],
                    'smtp_port' => $input['_smtp_port'],
                    'website' => $input['_website'],
                    'alamat1' => $input['_alamat1'],
                    'alamat2' => $input['_alamat2'],
                    'kota' => $input['_kota'],
                    'kodepos' => $input['_kodepos'],
                    'npwp' => $input['_npwp'],
                );

                $builder->where(['kodecbg' => $cabang])->update($data);
            } else			
			if ($idx == 3) {
                $data = array(
                    'periode_tahun' => $input['_tahun'],
                    'periode_bulan' => $input['_bulan']
                );
                $builder->where(['kodecbg' => $cabang])->update($data);
            } else
			if ($idx == 4) {
                $data = array(
                    'akunlrberjalan' => $input['_akunlrberjalan'],
                    'akunlrlalu' => $input['_akunlrlalu'],
                    'akun_persediaan_transit' => $input['_akunpersediaantransit'],
                    'akun_persediaan' => $input['_akunpersediaan'],
                    'akun_biaya_kerugian_lain' => $input['_akunbiayakerugianlain'],
                    'akun_pendapatan_lain' => $input['_akunpendapatanlain'],
                    'akun_penjualan' => $input['_akunpenjualan'],
                    'akun_ppn' => $input['_akunppn'],
                    'akun_ongkir' => $input['_akunongkir'],
                    'akun_uangmuka' => $input['_akunuangmuka'],
                    'akun_hpp' => $input['_akunhpp'],
                    'akun_kas' => $input['_akunkas'],
                    'akun_uangmukajual' => $input['_akunuangmukajual'],
                    'akun_piutang' => $input['_akunpiutang'],
                    'akun_ongkirjual' => $input['_akunongkirjual'],
                    'akun_retjual' => $input['_akunretjual'],
                    'akun_hutang' => $input['_akunhutang'],
                    'akun_hutangbeli' => $input['_akunhutangbeli'],
                    'akun_diskonjual' => $input['_akundiskonjual']
                );

                $builder->where(['kodecbg' => $cabang])->update($data);
            } else
			if ($idx == 5) {
                $desimal = $input['taxes'] / 100;
                $data = array(
                    'taxes_persen' => $input['taxes'],
                    'taxes_desimal' => $desimal,
                    'dpplainnya' => $input['dpplainya'],
                );
                $builder->where(['kodecbg' => $cabang])->update($data);
            };
            echo json_encode(['message' => 'Success']);
        }
        $db->transComplete();
    }

    //Nomor Bukti Page Start Here
    public function nomor_bukti()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/utilitas/nomor_bukti',
        ];
        return view('layout/layout', $data);
    }

    public function nomor_bukti_table()
    {
        $db = db_connect();
        $session = session();
        $res = $db->table('ms_counter1')->getWhere(['kodecbg' => $session->get('cabang')])->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                $value->kdtr,
                $value->cdno,
                $value->ket,
                '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . "'" . $value->kdtr . "'" . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button><button class="btn btn-danger mb-2" onclick="data_delete(' . "'" . $value->kdtr . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>'
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function data_nobukti($kode)
    {
        $db = db_connect();
        $session = session();
        $data = $db->table('ms_counter1')->getWhere(['kdtr' => $kode, 'kodecbg' => $session->get('cabang')])->getRow();
        echo json_encode($data);
    }

    public function nomor_bukti_Fpajak_table()
    {
        error_reporting(0);
        $db1 = db_connect();
        $session = session();
        $res1 = $db1->table('ms_counter_fpajak')->get()->getResult();
        $result1 = array();
        $data1 = [];
        foreach ($res1 as $value) {
            $data1[] = [
                $value->kdtr,
                $value->no_date,
                $value->cdno,
                $value->ket,
                '<button class="btn btn-primary me-2 mb-2" onclick="data_editfp(' . "'" . $value->kdtr . "'" . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button><button class="btn btn-danger mb-2" onclick="data_delete(' . "'" . $value->kdtr . "'" . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>'
            ];
        }

        $result1 = ['data' => $data1];
        echo json_encode($result1);
    }

    public function data_nobuktifp($kode)
    {
        $db = db_connect();
        $session = session();
        $data = $db->table('ms_counter_fpajak')->getWhere(['kdtr' => $kode])->getRow();
        echo json_encode($data);
    }

    public function savenobukti()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['kode'];
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 905);
        $builder = $db->table('ms_counter1');
        $builder2 = $db->table('ms_counter_fpajak');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $data = array(
                'kdtr' => $input['kode'],
                'cdno' => $input['nomor'],
                'ket' => $input['ket'],
            );

            if ($param !== 'FP') {
                $builder->where(['kdtr' => $input['kode']])->update($data);
            } else {
                $data['no_date'] = $input['no_date'];
                $builder2->where(['kdtr' => $input['kode']])->update($data);
            }
            echo json_encode($data);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function del_datanobukti($kode)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 905);
        $builder = $db->table('ms_counter1');
        $builder2 = $db->table('ms_counter_fpajak');
        if ($akses->udel == 1) {
            if ($kode !== 'FP') {
                $delete = $builder->delete(['kdtr' => $kode]);
            } else {
                $delete = $builder2->delete(['kdtr' => $kode]);
            }
            echo json_encode($delete);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }
}
