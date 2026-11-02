<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Buku Besar
 **/

namespace App\Controllers;

use App\Helpers\simkeu_rpt_helper;

class Bukubesar extends BaseController
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
    // =========================== KODE AKUN BUKU BESAR SELECTION START ======================//
    //Kode Akun Buku Besar Page Start Here
    public function kode_akun()
    {
        $db = db_connect();
        $tipeakun = $db->table('ms_akun_kelompok')->get()->getResult();
        $kodeakun = $db->table('ms_akun')->get()->getResult();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/data_master/kode_akun',
            'tipeakun' => $tipeakun,
            'kodeakun' => $kodeakun
        ];
        return view('layout/layout', $data);
    }

    public function kodeakun_add()
    {
        $validation =  \Config\Services::validation();
        $session = session();
        $db = db_connect();
        $builder = $db->table('ms_akun');
        $input = $this->request->getPost();
        $validation->setRule('kodeakun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('namaakun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('jenis', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('akuntx', 'The Please fill in requirment field is required', 'required');
        $id = $input['id'];



        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'kodeakun' => $input['kodeakun'],
                'namaakun' => $input['namaakun'],
                'kelompok' => $input['jenis'],
                'akuninduk' => $input['akuninduk'],
                'kodem' => $input['kodem'],
                'kodek' => $input['kodek'],
                'tx' => $input['akuntx'],
            ];

            $datalogs = [
                'date' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
            ];

            if ($id == 0) {
                $result = $builder->insert($data);
                if ($result) {
                    $datalogs['detail'] = 'Tambah data Kode Akun dengan kodeakun ' . $input['kodeakun'];
                    $db->table("logs")->insert($datalogs);
                    echo json_encode(['message' => 'Data Berhasil Di Tambah']);
                }
            } else {
                $result = $builder->where('id', $id)->update($data);
                if ($result) {
                    $datalogs['detail'] = 'Update data Kode Akun dengan kodeakun ' . $input['kodeakun'];
                    $db->table("logs")->insert($datalogs);
                    echo json_encode(['message' => 'Data Berhasil Di Update']);
                }
            }
        } else {
            $arr = [
                'error' => true,
                'oneErr' => $validation->showError('kodeakun'),
                'twoErr' => $validation->showError('namaakun'),
                'threeErr' => $validation->showError('jenis'),
                'sevenErr' => $validation->showError('akuntx'),


            ];
            echo json_encode($arr);
        }
    }

    public function kodeakun_table()
    {
        $db = db_connect();
        $res = $db->table('ms_akun')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $rowAkun = $db->table('ms_akun_kelompok')->where('kode', $value->kelompok)->get()->getRow();
            $data[] = [
                $value->kodeakun,
                $value->namaakun,
                $rowAkun->nama,
                $value->tx,


                // $value->status == null ? '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '">Edit</a>' : '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '">View</a>',

                '<button type="btn" onclick="kode_edit(' . $value->id . ')" class="btn btn-primary me-2 mb-2" ><i class="fa-solid fa-edit text-white"></i> edit</button>
                <button type="btn" onclick="kode_delete(' . $value->id . ')" class="btn btn-danger me-2 mb-2" ><i class="fa-solid fa-edit text-white"></i> Delete</button>'

            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function kode_akun_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('ms_akun')->where('id', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function kodedelete($id = '')
    {
        $db = db_connect();
        $session = session();
        $builder = $db->table('ms_akun');
        $RowKode  = $builder->getWhere(['id' => $id])->getRow();
        $datalogs = [
            'detail' => 'Hapus data Kode Akun dengan Kodeakun ' . $RowKode->kodeakun,
            'date' => date('Y-m-d H:i:s'),
            'userid' => $session->get('username'),
        ];
        $delete = $builder->delete(['id' => $id]);
        if ($delete) {
            $db->table("logs")->insert($datalogs);
            echo json_encode(['message' => 'Deleted']);
        }
    }

    // =========================== SALDO AWAL BUKU BESAR SELECTION START ======================//
    //Saldo Awal Buku Besar Page Start Here
    public function saldo_awal_akun()
    {

        $css = [];
        $js = [];
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 212);
        if ($akses->uadd == 1) {
            $tambah = 'Data Baru';
        } else {
            $tambah = '';
        }
        $kodeakun = $db->table('ms_akun')->getWhere(['kodeakun < ' => '4', 'akuninduk != ' => ''])->getResult();
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/data_master/saldo_awal_akun',
            'tambah' => $tambah,
            'kodeakun' => $kodeakun,
            'bulan' => $model_Global->_LoadProfile()->periode_bulan,
            'tahun' => $model_Global->_LoadProfile()->periode_tahun,
        ];
        return view('layout/layout', $data);
    }

    public function saldo_awal_add()
    {
        $validation =  \Config\Services::validation();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $builder = $db->table('ms_akunsaldo');
        $input = $this->request->getPost();
        $validation->setRule('kodeakun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('debet', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('kredit', 'The Please fill in requirment field is required', 'required');
        $id = $input['id'];

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'kodeakun' => $input['kodeakun'],
                'debet' => $input['debet'],
                'kredit' => $input['kredit'],
                'userid' => $session->get('username'),
                'tahun' => $model_Global->_LoadProfile()->periode_tahun,
                'bulan' => $model_Global->_LoadProfile()->periode_bulan,
            ];

            $datalogs = [
                'date' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
            ];

            if ($id == 0) {
                $result = $builder->insert($data);
                if ($result) {
                    $idData = $db->insertID();
                    $datalogs['detail'] = 'Tambah data Saldo Awal dengan id ' . $idData;
                    $db->table("logs")->insert($datalogs);
                    echo json_encode(['message' => 'Data Berhasil Di Tambah']);
                }
            } else {
                $dataupdate = [
                    'kodeakun' => $input['kodeakun'],
                    'debet' => $input['debet'],
                    'kredit' => $input['kredit'],
                    'userid' => $session->get('username'),
                ];
                $result = $builder->where('id', $id)->update($dataupdate);
                if ($result) {
                    $datalogs['detail'] = 'Update data Saldo Awal dengan id ' . $id;
                    $db->table("logs")->insert($datalogs);
                    echo json_encode(['message' => 'Data Berhasil Di Update']);
                }
            }
        } else {
            $arr = [
                'error' => true,
                'oneErr' => $validation->showError('kodeakun'),
                'twoErr' => $validation->showError('debet'),
                'threeErr' => $validation->showError('kredit'),



            ];
            echo json_encode($arr);
        }
    }

    public function saldo_table($bln, $thn)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 212);
        $tahun = $model_Global->_LoadProfile()->periode_tahun;
        $bulan = $model_Global->_LoadProfile()->periode_bulan;
        $db = db_connect();
        $query = "SELECT a.kodeakun, a.namaakun, b.debet, b.kredit, b.id
        from ms_akun a
        inner join ms_akunsaldo b on b.kodeakun=a.kodeakun";
        if ($bln != $bulan  || $thn != $tahun) {
            $query .= " where b.tahun = '$thn' and b.bulan='$bln' ";
        } else {
            $query .= " where b.tahun = '$tahun' and b.bulan='$bulan' ";
        }

        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            if ($akses->udel == 1 && $akses->uedit == 1) {
                $HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="SaldoAwalEdit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>
                <button class="btn btn-danger mb-2" onclick="SaldoAwalDelete(' . $value->id . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $HakAkses = '<button class="btn btn-danger mb-2" onclick="SaldoAwalDelete(' . $value->id . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="SaldoAwalEdit(' . $value->id . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
            } else {
                $HakAkses = '';
            }
            if ($level == 5) {
                if ($value->kodeakun == '110101' || $value->kodeakun == '110104' || $value->kodeakun == '110103') {
                    $data[] = [
                        $value->kodeakun,
                        $value->namaakun,
                        number_format($value->debet),
                        number_format($value->kredit),
                        $HakAkses,
                    ];
                }
            } else {
                $data[] = [
                    $value->kodeakun,
                    $value->namaakun,
                    number_format($value->debet),
                    number_format($value->kredit),
                    $HakAkses,
                ];
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function SaldoAwalEdit($id)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $builder = $db->table("ms_akunsaldo")->getWhere(['id' => $id])->getRow();
        echo json_encode($builder);
    }

    public function SaldoAwalDelete($id)
    {
        $db = db_connect();
        $session = session();
        $builder = $db->table('ms_akunsaldo');
        $delete = $builder->delete(['id' => $id]);
        $datalogs = [
            'detail' => 'Hapus data Saldo Awal dengan id ' . $id,
            'date' => date('Y-m-d H:i:s'),
            'userid' => $session->get('username'),
        ];
        $db->table("logs")->insert($datalogs);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }
    // =========================== ENTRY BUKU BESAR SELECTION START ======================//
    //Entry Jurnal Buku Besar Page Start Here
    public function entri_jurnal($id = '')
    {
        $session = session();
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 221);
        $ResultCab = $db->table("ms_unit")->get()->getResult();
        $ResJenisJurnal = $db->table("ms_jurnal")->get()->getResult();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/transaksi/entri_jurnal',
            'id' => $id ?? '',
            'nomor' => $model_Global->_Autonomor('JU'),
            'cabang' => $cabang,
            'akses' => $akses,
            'unit' => $ResultCab,
            'JenisJurnal' => $ResJenisJurnal,
            'leveluser' => $level
        ];
        return view('layout/layout', $data);
    }

    public function jurnal_save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db         = db_connect();
        $input      = $this->request->getPost();
        $param      = $input['id'];
        $session    = session();
        $level      = $session->get('level');
        $builder    = $db->table('tr_jurnal');
        $akses = $model_Global->cek_menu_akses($level, 221);
        try {
            if ($akses->uadd == 1 || $akses->uedit == 1) {
                // $db->transStart();
                $ketglobal = $input['keterangan'];
                $kodeakun = $input['akun'];
                $ket      = $input['ket'];
                $debet    = $input['debet'];
                $kredit   = $input['kredit'];
                $tanggal  = $input['tanggal'];
                $noref    = $input['noref'];
                $nobukti  = $input['nomorbukti'];
                $jenis    = $input['jenis'];
                $cabang   = $input['unit'];
                $DataJurnal = $builder->getWhere(['novoucher' => $nobukti])->getRow();
                if (empty($DataJurnal)) {
                    $nomorbukti = $nobukti;
                } else {
                    $nomorbukti = $model_Global->_Autonomor('JU');
                }
                $jumdata  = count($kodeakun);
                $_tanggal = date('Y-m-d', strtotime($tanggal));

                $builder->delete(['novoucher' => $nobukti]);
                $nourut = 1;
                for ($i = 0; $i <= $jumdata; $i++) {
                    $_akun   = $kodeakun[$i];
                    $_ket    = $ket[$i];
                    $_debet  =  str_replace(',', '', $debet[$i]);
                    $_kredit =  str_replace(',', '', $kredit[$i]);

                    if ($_akun != "") {
                        if ($param == 0) {
                            $model_Global->_rekamjurnal($tanggal, $nomorbukti, $jenis, $noref, $nourut, $_akun, $ketglobal, $_ket, $_debet, $_kredit);
                        } else {
                            $model_Global->_rekamjurnal($tanggal, $nobukti, $jenis, $noref, $nourut, $_akun, $ketglobal, $_ket, $_debet, $_kredit);
                        }
                        $nourut++;
                    }
                }
                $model_Global->_updatecounter1('JU');
                if ($param == 0) {
                    echo json_encode(['status' => 1, 'message' => $nomorbukti]);
                } else {
                    echo json_encode(['status' => 1, 'message' => $nobukti]);
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
    // =========================== ENTRY BUKU BESAR SELECTION START ======================//
    //Daftar Jurnal Buku Besar Page Start Here
    public function daftar_jurnal()
    {
        $css = [];
        $js = [];
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 222);
        if ($akses->uadd == 1) {
            $tambah = 'Data Baru';
        } else {
            $tambah = '';
        }
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJf-Corp',
            'body' => 'admin/buku_besar/transaksi/daftar_jurnal',
            'tambah' => $tambah,
        ];
        return view('layout/layout', $data);
    }

    public function DaftarJurnalTable($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 222);
        $db = db_connect();
        $query = "SELECT * FROM tr_jurnal ";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " where tanggal between '$tgl1' and '$tgl2' ";
        } else {
            $query .= " where year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity) ";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        foreach ($res as $value) {
            if ($akses->udel == 1 && $akses->uedit == 1) {
                $HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="JurnalEdit(' . "'" . $value->novoucher . "'" . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>
                <button class="btn btn-danger mb-2" onclick="JurnalDelete(' . $value->nomor . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $HakAkses = '<button class="btn btn-danger mb-2" onclick="JurnalDelete(' . $value->nomor . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $HakAkses = '<button class="btn btn-primary me-2 mb-2" onclick="JurnalEdit(' . "'" . $value->novoucher . "'" . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
            } else {
                $HakAkses = '';
            }
            $count++;
            $data[] = [
                $count,
                $value->novoucher,
                $value->noref,
                date('d-m-Y', strtotime($value->tanggal)),
                $value->kodeakun,
                $value->keterangan,
                number_format($value->debet),
                number_format($value->kredit),
                $value->jenis,
                $HakAkses,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function daftar_jurnal_entri()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/transaksi/daftar_jurnal_entri',
        ];
        return view('layout/layout', $data);
    }

    // =========================== TUTUP BUKU BUKU BESAR SELECTION START ======================//
    //Tutup Buku Besar Page Start Here
    public function tutup_buku()
    {
        $css = [];
        $js = [];
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $bulan = $model_Global->_LoadProfile()->periode_bulan;
        $tahun = $model_Global->_periodetahun();

        if ($bulan == 12) {
            $bulan_buka = 1;
            $tahun_buka = $tahun + 1;
        } else {
            $bulan_buka = $bulan + 1;
            $tahun_buka = $tahun;
        }
        $d['bulan'] = $bulan;
        $d['tahun'] = $tahun;
        $d['bulan_buka'] = $bulan_buka;
        $d['tahun_buka'] = $tahun_buka;
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/transaksi/tutup_buku',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulan_buka' => $bulan_buka,
            'tahun_buka' => $tahun_buka
        ];
        return view('layout/layout', $data);
    }

    public function tutupbuku_save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $input = $this->request->getPost();
        $session = session();
        $level = $session->get('level');
        $userid = $session->get('username');
        $cabang = $session->get('cabang');
        $akses = $model_Global->cek_menu_akses($level, 224);
        // Table
        $builder            = $db->table('ap_lpb');
        $builderDetail      = $db->table('ap_lpbdetil');
        $builderPO          = $db->table('ap_pofile');
        $builderPODetail    = $db->table('ap_podetail');
        try {
            $db->transStart();
            if ($akses->uadd == 1 || $akses->uedit == 1) {
                $thntutup       = trim($input['thntutup']);
                $thnbuka        = trim($input['thnbuka']);
                $blntutup       = trim($input['blntutup']);
                $blnbuka        = trim($input['blnbuka']);


                $tgl_proses     = date("Y-m-d");

                $query = "select count(*) as jumdata from ms_akunsaldo where tahun = '$thnbuka' and bulan= '$blnbuka' and cabang='$cabang'";
                $data  = $db->query($query)->getRow();

                if ($data->jumdata > 0) {
                    echo 'not ok';
                } else {

                    $bulan_awal = $blntutup;
                    $tahun_awal = $thntutup;
                    $query1 = "select kodeakun, jenis from ms_akun where kodeakun < '4'";
                    $hasil = $db->query($query1)->getResult();
                    foreach ($hasil as $rowD) {
                        $jenisakun = $rowD->jenis;
                        $akun      = $rowD->kodeakun;

                        $query2 = "select debet,kredit from ms_akunsaldo where kodeakun = '$akun' and tahun = '$tahun_awal' and bulan = '$bulan_awal' and cabang='$cabang'";
                        $data2  = $db->query($query2)->getRow();
                        if ($data2 == null || empty($data2)) {
                            $saldo_awal = 0;
                        } else {
                            if ($jenisakun == 'D') {
                                $saldo_awal = intval($data2->debet - $data2->kredit);
                            } else {
                                $saldo_awal = intval($data2->kredit - $data2->debet);
                            }
                        }


                        $query3 = "select sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where year(tanggal)='$thntutup' and month(tanggal)='$blntutup' and kodeakun = '$akun'";
                        $data3   = $db->query($query3)->getRow();
                        if ($data3 == null || empty($data3)) {
                            $debet = 0;
                            $kredit = 0;
                        } else {
                            $debet = $data3->debet;
                            $kredit = $data3->kredit;
                        }

                        if ($jenisakun == 'D') {
                            $saldo_akhir = ($saldo_awal + $debet - $kredit);
                        } else {
                            $saldo_akhir = $saldo_awal - $debet + $kredit;
                        }

                        if ($jenisakun == 'D') {
                            $saldo_debet  = $saldo_akhir;
                            $saldo_kredit = 0;
                        } else {
                            $saldo_debet   = 0;
                            $saldo_kredit  = $saldo_akhir;

                            if (substr($akun, 0, 1) == '1') {
                                $saldo_debet  = $saldo_akhir * -1;
                                $saldo_kredit = 0;
                            }
                        }

                        $saldo = $saldo_debet + $saldo_kredit;
                        if ($saldo != 0) {
                            $qtutup = "insert into ms_akunsaldo(kodeakun, tahun, bulan, debet, kredit, userid, tanggal, cabang)
					                    values('$akun','$thnbuka','$blnbuka','$saldo_debet','$saldo_kredit','$userid','$tgl_proses','$cabang')";

                            $db->query($qtutup);
                        }
                    }
                    //update lr berjalan
                    //update status jurnal

                    $query4 = "update tr_jurnal set posted='Y' where posted<>'Y' and year(tanggal)='$thntutup' and month(tanggal)='$blntutup'";
                    $db->query($query4);

                    $queryakunlrlalu = "select akunlrlalu from ms_identity";
                    $rowakunlrlalu   = $db->query($queryakunlrlalu)->getRow();
                    $akunlrlalu = $rowakunlrlalu->akunlrlalu;

                    $queryakunlr = "select akunlrberjalan from ms_identity";
                    $rowakunlr   = $db->query($queryakunlr)->getRow();
                    $akunlr = $rowakunlr->akunlrberjalan;

                    $querysaldoberjalanlalu = "select kredit from ms_akunsaldo where kodeakun = '$akunlr' and tahun = '$thntutup' and bulan = '$blntutup' and cabang='$cabang'";
                    $rowsaldoberjalanlalu   = $db->query($querysaldoberjalanlalu)->getRow();
                    if ($rowsaldoberjalanlalu == null || empty($rowsaldoberjalanlalu)) {
                        $saldoberjalanlalu = 0;
                    } else {
                        $saldoberjalanlalu = $rowsaldoberjalanlalu->kredit;
                    }

                    $querysaldolalu = "select kredit from ms_akunsaldo where kodeakun = '$akunlrlalu' and tahun = '$thntutup' and bulan = '$blntutup' and cabang='$cabang'";
                    $rowsaldolalu   = $db->query($querysaldolalu)->getRow();
                    if ($rowsaldoberjalanlalu == null || empty($rowsaldoberjalanlalu)) {
                        $saldolalu = 0;
                    } else {
                        $saldolalu = $rowsaldolalu->kredit;
                    }


                    $queryDebetKredit = "select sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where year(tanggal)='$thntutup' and month(tanggal)='$blntutup' and kodeakun = '$akunlrlalu'";
                    $RowDebetKredit   = $db->query($queryDebetKredit)->getRow();
                    if ($rowsaldoberjalanlalu == null || empty($rowsaldoberjalanlalu)) {
                        $debet = 0;
                        $kredit = 0;
                    } else {
                        $debet = $RowDebetKredit->debet;
                        $kredit = $RowDebetKredit->kredit;
                    }


                    $saldolalu = $saldolalu + $kredit - $debet;


                    if ($blntutup == 12) {
                        $saldo = $saldoberjalanlalu + $saldolalu;
                    } else {
                        $saldo = $saldoberjalanlalu;
                    }


                    $query5 = "delete from ms_akunsaldo where tahun = '$thnbuka' and bulan= '$blnbuka' and kodeakun='$akunlrlalu' and cabang='$cabang'";
                    $db->query($query5);

                    $query6 = "delete from ms_akunsaldo where tahun = '$thnbuka' and bulan= '$blnbuka' and kodeakun='$akunlr' and cabang='$cabang'";
                    $db->query($query6);

                    //
                    $querypendapatan = "select sum(kredit-debet) as jumlah from tr_jurnal a,ms_akun b where a.kodeakun=b.kodeakun and year(a.tanggal)='$thntutup' and  month(a.tanggal)='$blntutup' and a.kodeakun like '4%'";
                    $rowpendapatan   = $db->query($querypendapatan)->getRow();
                    $pendapatan = $rowpendapatan->jumlah;

                    $querybiaya = "select sum(debet-kredit) as jumlah from tr_jurnal a,ms_akun b where a.kodeakun=b.kodeakun and year(a.tanggal)='$thntutup' and  month(a.tanggal)='$blntutup' and a.kodeakun like '5%'";
                    $rowbiaya   = $db->query($querybiaya)->getRow();
                    $biaya = $rowbiaya->jumlah;

                    $querypendapatanL = "select sum(kredit-debet) as jumlah from tr_jurnal a,ms_akun b where a.kodeakun=b.kodeakun and year(a.tanggal)='$thntutup' and  month(a.tanggal)='$blntutup' and a.kodeakun like '61%'";
                    $rowpendapatanL   = $db->query($querypendapatanL)->getRow();
                    $pendapatanL = $rowpendapatanL->jumlah;

                    $querybiayaL = "select sum(debet-kredit) as jumlah from tr_jurnal a,ms_akun b where a.kodeakun=b.kodeakun and year(a.tanggal)='$thntutup' and  month(a.tanggal)='$blntutup' and a.kodeakun like '62%'";
                    $rowbiayaL   = $db->query($querybiayaL)->getRow();
                    $biayaL = $rowbiayaL->jumlah;

                    $querypajak = "select sum(kredit-debet) as jumlah from tr_jurnal a,ms_akun b where a.kodeakun=b.kodeakun and year(a.tanggal)='$thntutup' and  month(a.tanggal)='$blntutup' and a.kodeakun like '9%'";
                    $rowpajak   = $db->query($querypajak)->getRow();
                    $pajak = $rowpajak->jumlah;

                    $lrj   = $pendapatan - $biaya + $pendapatanL - $biayaL + $pajak;

                    $debetlr = 0;
                    $kreditlr = $lrj + $saldo;


                    if ($blntutup == 12) {
                        $qlr = "insert into ms_akunsaldo(kodeakun, tahun, bulan, debet, kredit, userid, tanggal, cabang)
					 values('$akunlrlalu','$thnbuka','$blnbuka','$debetlr','$kreditlr','$userid','$tgl_proses','$cabang')";
                    } else {
                        $qlr2 = "insert into ms_akunsaldo(kodeakun, tahun, bulan, debet, kredit, userid, tanggal, cabang)
					 values('$akunlrlalu','$thnbuka','$blnbuka',0,'$saldolalu','$userid','$tgl_proses','$cabang')";
                        $db->query($qlr2);

                        $qlr = "insert into ms_akunsaldo(kodeakun, tahun, bulan, debet, kredit, userid, tanggal, cabang)
					 values('$akunlr','$thnbuka','$blnbuka','$debetlr','$kreditlr','$userid','$tgl_proses','$cabang')";
                    }

                    $db->query($qlr);

                    echo "ok";

                    $qtutup = "update ms_identity set periode_tahun= '$thnbuka', periode_bulan= '$blnbuka'";
                    $db->query($qtutup);
                    $db->query("UPDATE ms_counter1 SET cdno='000001' WHERE kdtr='AMC' OR kdtr='KT' OR kdtr='MC' OR kdtr='MMC' OR kdtr='PB' OR kdtr='PBP'
                                OR kdtr='PM' OR kdtr='PO' OR kdtr='POP' OR kdtr='PP' OR kdtr='PPO' OR kdtr='PR' OR kdtr='PU' OR kdtr='PUP'
                                OR kdtr='RMC' OR kdtr='SD' OR kdtr='SDP' OR kdtr='SI' OR kdtr='SIP' OR kdtr='SM' OR kdtr='SO' OR kdtr='SOB'
                                OR kdtr='SOP' OR kdtr='SR' OR kdtr='ST' OR kdtr='JU' OR kdtr='MG' OR kdtr='KK' OR kdtr='KM' OR kdtr='KTB'");
                    $datalogs = [
                        'date' => date('Y-m-d H:i:s'),
                        'userid' => $session->get('username'),
                        'detail' => 'Perbaruan tutup buku dari bulan ' . $blntutup . '-' . $thntutup . ' dan di buka bulan ' .  $blnbuka . '-' . $thnbuka,
                    ];
                    $db->table("logs")->insert($datalogs);
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

    // =========================== LAPORAN BUKU BESAR SELECTION START ======================//
    //Laporan Buku Besar Page Start Here
    public function laporan()
    {
        $session = session();
        $db = db_connect();
        $cabang = $db->table("ms_unit")->get()->getResult();
        $akun = $db->table('ms_akun')->get()->getResult();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/laporan',
            'cbg' => $cabang,
            'bank' => $akun,
        ];
        return view('layout/layout', $data);
    }

    public function CetakLaporan($x)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $cek = $session->get('level');
        $unit = $session->get('cabang');
        if (!empty($cek)) {
            $profile = $model_Global->_LoadProfile();
            $nama_usaha = $profile->nama_usaha;
            $motto = '';
            $alamat = '';

            $data  = explode("~", $x);
            $idlp  = $data[0];
            $tgl1  = $data[1];
            $tgl2  = $data[2];
            $akun  = $data[3];
            $cab   = $data[4];
            if ($cab != "") {
                $namaunit =  $db->table('ms_unit')->getWhere(['kode' => $cab])->getRow()->nama;
            } else {
                $namaunit = "";
            }
            $_tgl1 = date('Y-m-d', strtotime($tgl1));
            $_tgl2 = date('Y-m-d', strtotime($tgl2));
            $_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
            $_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));

            //LAPORAN DAFTAR JURNAL
            if ($idlp == 101) {
                $query =
                    "SELECT 
                        a.debet, a.kredit, a.tanggal, a.novoucher, a.kodeakun, b.namaakun, a.keterangan
                    from 
                        tr_jurnal a 
                    inner join 
                        ms_akun b on a.kodeakun = b.kodeakun
			        where 
                        a.tanggal between '$_tgl1' and '$_tgl2' ";

                if ($akun != "") {
                    $query .= "and a.kodeakun = '$akun'";
                }
                if ($cab != "") {
                    $query .= "and a.kodecbg = '$cab'";
                }
                $query .= "order by a.tanggal, a.novoucher";

                $judul = array('NO', 'TANGGAL', 'NO. BUKTI', 'KODE AKUN', 'NAMA AKUN', 'URAIAN', 'DEBET', 'KREDIT');

                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Jurnal');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetWidths(array(10, 19, 30, 18, 30, 40, 24, 24));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 9);
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 19, 30, 18, 30, 40, 24, 24));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'L', 'R', 'R'));

                $nourut = 1;
                $totdb  = 0;
                $totkr  = 0;
                foreach ($lap as $value) {
                    $DataJurnal = $db->query("SELECT SUM(debet - kredit) AS total from tr_jurnal where novoucher = '$value->novoucher'")->getRow();
                    if ($DataJurnal->total == 0) {
                        $pdf->setfont('Times', '', 9);
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetFont('');
                        $pdf->SetTextColor(0); // Warna hitam
                    } else {
                        $pdf->setfont('Times', '', 9);
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetFont('');
                        $pdf->SetTextColor(255, 0, 0); // Warna merah
                    }
                    $pdf->Row(array(
                        $nourut,
                        date('d-m-Y', strtotime($value->tanggal)),
                        $value->novoucher,
                        $value->kodeakun,
                        $value->namaakun,
                        $value->keterangan,
                        number_format($value->debet, 2, '.', ','),
                        number_format($value->kredit, 2, '.', ',')
                    ));
                    $totdb = $totdb + $value->debet;
                    $totkr = $totkr + $value->kredit;
                    $nourut++;
                }
                $pdf->SetWidths(array(147, 24, 24));
                $pdf->SetAligns(array('R', 'R', 'R'));

                $pdf->setfont('Times', 'B', 9);
                $pdf->Row(array(
                    'JUMLAH  ',
                    number_format($totdb, 2, '.', ','),
                    number_format($totkr, 2, '.', ',')
                ));
                $pdf->ln();

                $pdf->AliasNbPages();
                $pdf->SetTitle('LAPORAN JURNAL');
                $pdf->output('gl_jurnal.PDF', 'I');
                exit;
            }
            // LAPORAN BUKU BESAR
            else if ($idlp == 102) {
                if ($akun != "") {
                    $bulan  = date('n', strtotime($tgl1));
                    $tahun  = date('Y', strtotime($tgl1));
                    $qsaldo = $db->table('ms_akunsaldo')->select('debet, kredit')->getWhere(['kodeakun' => $akun, 'bulan' => $bulan, 'tahun' => $tahun])->getRow();
                    if ($qsaldo) {
                        $saldo_db = $qsaldo->debet;
                        $saldo_kr = $qsaldo->kredit;
                    } else {
                        $saldo_db = 0;
                        $saldo_kr = 0;
                    }
                    $jenis_akun = $model_Global->_jenisakun($akun);

                    $saldo  = $saldo_db + $saldo_kr;

                    $query =
                        "select * from tr_jurnal inner join ms_akun on tr_jurnal.kodeakun = ms_akun.kodeakun
                where tanggal between '$_tgl1' and '$_tgl2' and tr_jurnal.kodeakun = '$akun' order by tanggal, nourut";

                    $judul = array('NO', 'TANGGAL', 'NO. BUKTI', 'URAIAN', 'DEBET', 'KREDIT', 'SALDO');
                    $akunbb = ' (' . $model_Global->_namaakun($akun) . ' - ' . $akun . ')';
                    $lap = $db->query($query)->getResult();
                    $pdf = new simkeu_rpt_helper();
                    $pdf->setID($nama_usaha, $motto, $alamat);
                    $pdf->setunit($namaunit);
                    $pdf->setjudul('Buku Besar ' . $akunbb);
                    $pdf->setsubjudul($_peri);
                    $pdf->addpage("P", "A4");
                    $pdf->setsize("P", "A4");


                    $border = array('BT', 'BT', 'BT', 'BT', 'BT', 'BT', 'BT');
                    $align = array('C', 'C', 'C', 'C', 'R', 'R', 'R');

                    $pdf->SetWidths(array(10, 19, 25, 70, 24, 24, 24));
                    $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'R', 'R', 'R'));
                    $pdf->setfont('Times', 'B', 9);
                    $pdf->FancyRow($judul, $border, $align);
                    $pdf->SetWidths(array(124, 24, 24, 24));
                    $pdf->SetAligns(array('L', 'R', 'R', 'R'));

                    $pdf->setfont('Times', 'B', 9);
                    $pdf->RowB(array(
                        'Saldo Awal  ',
                        '', '',
                        number_format($saldo, 2, '.', ',')
                    ));

                    $pdf->SetWidths(array(10, 19, 25, 70, 24, 24, 24));
                    $pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'R', 'R'));
                    $pdf->setfont('Times', '', 9);
                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');

                    $nourut = 1;
                    $totdb  = 0;
                    $totkr  = 0;
                    foreach ($lap as $db) {
                        if ($jenis_akun == "D") {
                            $saldo = $saldo + $db->debet - $db->kredit;
                        } else {
                            $saldo = $saldo - $db->debet + $db->kredit;
                        }

                        $pdf->RowB(array(
                            $nourut,
                            date('d-m-Y', strtotime($db->tanggal)),
                            $db->novoucher,
                            $db->keterangan,
                            number_format($db->debet, 2, '.', ','),
                            number_format($db->kredit, 2, '.', ','),
                            number_format($saldo, 2, '.', ',')
                        ));
                        $totdb = $totdb + $db->debet;
                        $totkr = $totkr + $db->kredit;

                        $nourut++;
                    }
                    $pdf->SetWidths(array(124, 24, 24, 24));
                    $pdf->SetAligns(array('R', 'R', 'R', 'R'));
                    $align = array('R', 'R', 'R', 'R');
                    $border = array('BT', 'BT', 'BT', 'TB');
                    $pdf->setfont('Times', 'B', 9);
                    $pdf->FancyRow(array(
                        'JUMLAH  ',
                        number_format($totdb, 2, '.', ','),
                        number_format($totkr, 2, '.', ','),
                        number_format($saldo, 2, '.', ',')
                    ), $border, $align);
                    $pdf->ln();

                    $pdf->SetTitle('LAPORAN BUKU BESAR');
                    $pdf->AliasNbPages();
                    $pdf->output('gl_bukubesar.PDF', 'I');
                    exit;
                }
            }
            //LAPORAN NERACA SALDO
            else if ($idlp == 103) {
                $bulan  = date('n', strtotime($tgl1));
                $tahun  = date('Y', strtotime($tgl1));

                $query =
                    "select ms_akunsaldo.*, ms_akun.kodeakun as kodeakun1, ms_akun.namaakun from ms_akunsaldo right outer join ms_akun on ms_akunsaldo.kodeakun = ms_akun.kodeakun
                    and tahun = '$tahun' where ms_akun.tx='Y' order by ms_akun.kodeakun";


                $judul0 = array('', '', '', 'SALDO AWAL', 'MUTASI', 'NERACA', 'LABA RUGI');
                $judul = array('NO', 'KODE AKUN', 'NAMA AKUN', 'DEBET', 'KREDIT', 'DEBET', 'KREDIT', 'DEBET', 'KREDIT', 'DEBET', 'KREDIT');
                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Neraca Saldo ');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("L", "A4");
                $pdf->setsize("L", "A4");

                $pdf->SetWidths(array(10, 20, 50, 50, 50, 50, 50));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 9);
                $pdf->row($judul0);
                $pdf->setheadertable($judul0);
                $pdf->SetWidths(array(10, 20, 50, 25, 25, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 9);
                $pdf->row($judul);

                $pdf->setheadertable($judul);
                $pdf->SetWidths(array(10, 20, 50, 25, 25, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;
                $totdba  = 0;
                $totkra  = 0;
                $totdbm  = 0;
                $totkrm  = 0;
                $totdbn  = 0;
                $totkrn  = 0;
                $totdbl  = 0;
                $totkrl  = 0;

                foreach ($lap as $db) {
                    $akun = $db->kodeakun1;
                    $namaakun = $db->namaakun;
                    $jenis_akun = $model_Global->_jenisakun($akun);
                    $lap_akun   = $model_Global->_lapakun($akun);
                    if ($db->debet == 0) {
                        $dba = 0;
                        if ($db->kredit == 0) {
                            $kra = 0;
                        } else {
                            $kra = $db->kredit;
                        }
                    } else {
                        $dba = $db->debet;
                        $kra = $db->kredit;
                    }
                    $db = db_connect();
                    $jur = $db->query("select sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where kodeakun='$akun' and tanggal between '$_tgl1' and '$_tgl2'")->getRow();
                    $dbm = $jur->debet;
                    $krm = $jur->kredit;
                    $dbakhir = 0;
                    $krakhir = 0;
                    if ($jenis_akun == "D") {
                        $dbsaldo = ($dba + $kra) + $dbm - $krm;
                        if ($dbsaldo > 0) {
                            $dbakhir = abs($dbsaldo);
                            $krakhir = 0;
                        } else {
                            $dbakhir = 0;
                            $krakhir = abs($dbsaldo);
                        }
                    } else {
                        $dbsaldo = ($dba + $kra) - $dbm + $krm;
                        if ($dbsaldo > 0) {
                            $dbakhir = 0;
                            $krakhir = abs($dbsaldo);
                        } else {
                            $dbakhir = abs($dbsaldo);
                            $krakhir = 0;
                        }
                    }

                    if ($lap_akun == "N") {
                        $dbn = $dbakhir;
                        $krn = $krakhir;
                        $dbl = 0;
                        $krl = 0;
                    } else {
                        $dbl = $dbakhir;
                        $krl = $krakhir;
                        $dbn = 0;
                        $krn = 0;
                    }
                    $pdf->Row(array(
                        $nourut,
                        $akun,
                        $namaakun,
                        number_format($dba, 2, '.', ','),
                        number_format($kra, 2, '.', ','),
                        number_format($dbm, 2, '.', ','),
                        number_format($krm, 2, '.', ','),
                        number_format($dbn, 2, '.', ','),
                        number_format($krn, 2, '.', ','),
                        number_format($dbl, 2, '.', ','),
                        number_format($krl, 2, '.', ',')

                    ));
                    $totdba = $totdba + $dba;
                    $totkra = $totkra + $kra;
                    $totdbm = $totdbm + $dbm;
                    $totkrm = $totkrm + $krm;
                    $totdbn = $totdbn + $dbn;
                    $totkrn = $totkrn + $krn;
                    $totdbl = $totdbl + $dbl;
                    $totkrl = $totkrl + $krl;

                    $nourut++;
                }
                $pdf->SetWidths(array(80, 25, 25, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));

                $pdf->setfont('Times', 'B', 9);
                $pdf->Row(array(
                    'JUMLAH  ',
                    number_format($totdba, 2, '.', ','),
                    number_format($totkra, 2, '.', ','),
                    number_format($totdbm, 2, '.', ','),
                    number_format($totkrm, 2, '.', ','),
                    number_format($totdbn, 2, '.', ','),
                    number_format($totkrn, 2, '.', ','),
                    number_format($totdbl, 2, '.', ','),
                    number_format($totkrl, 2, '.', ',')
                ));

                $pdf->ln();

                $pdf->SetTitle('LAPORAN TRIAL BALANCE');
                $pdf->AliasNbPages();
                $pdf->output('gl_neracasaldo.PDF', 'I');
                exit;
            }
            // LAPORAN NERACA
            else if ($idlp == 104) {
                $bulan  = date('n', strtotime($tgl1));
                $tahun  = date('Y', strtotime($tgl1));

                $query0 = "select distinct kelompok1, nokel1 from ms_akun_kelompok where lap='N' order by nokel1";


                $lap = $db->query($query0)->getResult();

                $judul = array('Deskripsi', '', 'Nilai');
                $border = array('B', '', 'B');
                $align = array('L', '', 'R');

                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Neraca');
                $pdf->setsubjudul($_peri1);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");

                $pdf->SetWidths(array(120, 10, 60));
                $pdf->SetAligns(array('C', 'C', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->Fancyrow($judul, $border, $align);
                $pdf->SetWidths(array(120, 10, 60));
                $pdf->SetAligns(array('L', 'C', 'R'));
                $pdf->setfont('Times', 'B', 9);

                $pdf->SetWidths(array(10, 20, 50, 25, 25, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;

                $pdf->SetWidths(array(120, 10, 60));
                foreach ($lap as $Row1) {
                    $pdf->setfont('Times', 'B', 10);
                    $border = array('', '', '');
                    $align = array('L', '', 'R');
                    $font  = array('B', '', 'B');
                    $pdf->Fancyrow(array($Row1->kelompok1, '', ''), $border, $align);

                    $db = db_connect();
                    $query2 = "select distinct kelompok from ms_akun_kelompok where kelompok1 = '$Row1->kelompok1' order by nokel";
                    $lap1   = $db->query($query2)->getResult();
                    $tot1   = 0;
                    $totk   = 0;
                    foreach ($lap1 as $db1) {
                        $pdf->setfont('Times', 'B', 10);
                        $border = array('', '', '');
                        $align = array('L', '', 'R');
                        $font  = array('B', '', 'B');
                        $pdf->Fancyrow(array('     ' . $db1->kelompok, '', ''), $border, $align);

                        $query3 = "select nama, kode from ms_akun_kelompok where kelompok = '$db1->kelompok' order by nu";
                        $lap2   = $db->query($query3)->getResult();

                        $tot2 = 0;
                        foreach ($lap2 as $db2) {
                            $pdf->setfont('Times', '', 10);
                            $pdf->Fancyrow(array('     ' . '     ' . $db2->nama, '', ''), $border, $align);

                            $query4 = "select kodeakun, namaakun from ms_akun where kelompok = '$db2->kode' order by kodeakun";
                            $lap3   = $db->query($query4)->getResult();

                            $tot3   = 0;
                            foreach ($lap3 as $db3) {
                                $pdf->setfont('Times', '', 10);
                                $jenis_akun = $model_Global->_jenisakun($db3->kodeakun);
                                $saldo_awal = $model_Global->_saldoakun($db3->kodeakun, $bulan, $tahun);
                                $mutasi     = $model_Global->_totjurnal($db3->kodeakun, $jenis_akun, $tgl1, $tgl2);
                                $nilai      = $saldo_awal + $mutasi;
                                $tot3       = $tot3 + $nilai;

                                if ($db3->kodeakun == $profile->akunlrberjalan) {
                                    $lrj = $model_Global->_labarugiberjalan($tgl1, $tgl2);
                                    $tot3 = $tot3 + $lrj;
                                    $pdf->Fancyrow(array('     ' . '     ' . '     ' . $db3->namaakun, '', number_format($lrj, 2, ',', '.')), $border, $align);
                                }

                                if ($nilai != 0) {
                                    $pdf->Fancyrow(array('     ' . '     ' . '     ' . $db3->namaakun, '', number_format($nilai, 2, ',', '.')), $border, $align);
                                }
                            }


                            $pdf->setfont('Times', 'B', 10);
                            $border = array('', '', 'B');
                            $pdf->Fancyrow(array('     ' . '     ' . 'Jumlah ' . $db2->nama, '', number_format($tot3, 2, ',', '.')), $border, $align);
                            $pdf->ln(5);
                            $tot2 = $tot2 + $tot3;
                        }

                        $pdf->setfont('Times', 'B', 10);

                        $pdf->Fancyrow(array('     ' . 'Jumlah ' . $db1->kelompok, '', number_format($tot2, 2, ',', '.')), $border, $align);
                        $tot1 = $tot1 + $tot2;

                        if ($db->nokel1 != 1) {
                            $totk = $totk + $tot2;
                        }
                    }
                    $pdf->setfont('Times', 'B', 10);

                    $pdf->Fancyrow(array('Jumlah   ' . $db->kelompok1, '', number_format($tot1, 2, ',', '.')), $border, $align);



                    $nourut++;
                }
                $pdf->Fancyrow(array('JUMLAH KEWAJIBAN DAN EKUITAS   ', '', number_format($totk, 2, ',', '.')), $border, $align);

                $pdf->ln();
                $pdf->SetTitle('LAPORAN NERACA');
                $pdf->AliasNbPages();
                $pdf->output('gl_neraca.PDF', 'I');
                exit;
            }
            //LAPORAN LABA RUGI
            else if ($idlp == 105) {
                $bulan  = date('n', strtotime($tgl1));
                $tahun  = date('Y', strtotime($tgl1));

                $query0 = "select distinct kelompok1, nokel1 from ms_akun_kelompok where lap='L' order by nokel1";


                $lap = $db->query($query0)->getResult();

                $judul = array('Deskripsi', '', 'Nilai');
                $border = array('B', '', 'B');
                $align = array('L', '', 'R');

                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Laba/Rugi');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");

                $pdf->SetWidths(array(120, 10, 60));
                $pdf->SetAligns(array('C', 'C', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->Fancyrow($judul, $border, $align);
                $pdf->SetWidths(array(120, 10, 60));
                $pdf->SetAligns(array('L', 'C', 'R'));
                $pdf->setfont('Times', 'B', 9);

                $pdf->SetWidths(array(10, 20, 50, 25, 25, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;

                $pdf->SetWidths(array(120, 10, 60));
                foreach ($lap as $Row) {
                    $pdf->setfont('Times', 'B', 10);
                    $border = array('', '', '');
                    $align = array('L', '', 'R');
                    $font  = array('B', '', 'B');
                    $pdf->Fancyrow(array($Row->kelompok1, '', ''), $border, $align);

                    $db = db_connect();
                    $query2 = "select distinct kelompok from ms_akun_kelompok where kelompok1 = '" . $Row->kelompok1 . "' order by nokel";
                    $lap1   = $db->query($query2)->getResult();
                    $tot1   = 0;
                    $totp   = 0;
                    $totb   = 0;
                    foreach ($lap1 as $db1) {
                        $pdf->setfont('Times', 'B', 10);
                        $border = array('', '', '');
                        $align = array('L', '', 'R');
                        $font  = array('B', '', 'B');
                        $pdf->Fancyrow(array('     ' . $db1->kelompok, '', ''), $border, $align);

                        $query3 = "select nama, kode from ms_akun_kelompok where kelompok = '" . $db1->kelompok . "' order by nu";
                        $lap2   = $db->query($query3)->getResult();

                        $tot2 = 0;
                        foreach ($lap2 as $db2) {
                            $pdf->setfont('Times', '', 10);
                            $pdf->Fancyrow(array('     ' . '     ' . $db2->nama, '', ''), $border, $align);

                            $query4 = "select kodeakun, namaakun from ms_akun where kelompok = '" . $db2->kode . "' order by kodeakun";
                            $lap3   = $db->query($query4)->getResult();

                            $tot3   = 0;
                            foreach ($lap3 as $db3) {
                                $pdf->setfont('Times', '', 10);
                                $jenis_akun = $model_Global->_jenisakun($db3->kodeakun);
                                $mutasi     = $model_Global->_totjurnal($db3->kodeakun, $jenis_akun, $tgl1, $tgl2);
                                $nilai      = $mutasi;
                                $tot3       = $tot3 + $nilai;

                                if ($nilai != 0) {
                                    $pdf->Fancyrow(array('     ' . '     ' . '     ' . $db3->namaakun, '', number_format($nilai, 2, ',', '.')), $border, $align);
                                }
                            }


                            $pdf->setfont('Times', 'B', 10);
                            $border = array('', '', 'B');
                            $pdf->Fancyrow(array('     ' . '     ' . 'Jumlah ' . $db2->nama, '', number_format($tot3, 2, ',', '.')), $border, $align);
                            $pdf->ln(5);
                            $tot2 = $tot2 + $tot3;
                        }

                        $pdf->setfont('Times', 'B', 10);

                        $pdf->Fancyrow(array('     ' . 'Jumlah ' . $db1->kelompok, '', number_format($tot2, 2, ',', '.')), $border, $align);
                        $tot1 = $tot1 + $tot2;
                        $pdf->ln(5);
                    }
                    $pdf->setfont('Times', 'B', 10);

                    $pdf->Fancyrow(array('Jumlah   ' . $db->kelompok1, '', number_format($tot1, 2, ',', '.')), $border, $align);
                    $pdf->ln(5);


                    $nourut++;
                }
                $pdf->Fancyrow(array('LABA BERSIH   ', '', number_format($model_Global->_labarugiberjalan($tgl1, $tgl2), 2, ',', '.')), $border, $align);

                $pdf->ln();
                $pdf->SetTitle('LAPORAN LABA RUGI');
                $pdf->AliasNbPages();
                $pdf->output('gl_labarugi.PDF', 'I');
                exit;
            }
        } else {

            header('location:' . base_url());
        }
    }
}
