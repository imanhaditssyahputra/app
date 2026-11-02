<?php

namespace App\Controllers;

class Bukubesar extends BaseController
{
    // =========================== KODE AKUN BUKU BESAR SELECTION START ======================//
    //Kode Akun Buku Besar Page Start Here
    public function kode_akun()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/data_master/kode_akun',
        ];
        return view('layout/layout', $data);
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
                $result = $builder->where('id', $id)->update($data);
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

    public function saldo_table()
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
        inner join ms_akunsaldo b on b.kodeakun=a.kodeakun
        where b.tahun = '$tahun' and b.bulan='$bulan' ";
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
            $data[] = [
                $value->kodeakun,
                $value->namaakun,
                number_format($value->debet),
                number_format($value->kredit),
                $HakAkses,
            ];
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
    // =========================== SALDO AWAL BUKU BESAR SELECTION START ======================//
    //Saldo Awal Buku Besar Page Start Here
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
            'JenisJurnal' => $ResJenisJurnal
        ];
        return view('layout/layout', $data);
    }

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

    public function tutup_buku()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/transaksi/tutup_buku',
        ];
        return view('layout/layout', $data);
    }

    public function laporan()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/buku_besar/laporan',
        ];
        return view('layout/layout', $data);
    }


    public function kodeakun_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('ms_akun');
        $input = $this->request->getPost();
        $validation->setRule('kode_akun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('nama_akun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('tipe_akun', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('akun_induk', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('kode_panerimaan', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('kode_pengeluaran', 'The Please fill in requirment field is required', 'required');
        $validation->setRule('akun_transaksi', 'The Please fill in requirment field is required', 'required');
        $id = $input['id'];



        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'kode_akun' => $input['kode_akun'],
                'nama_akun' => $input['nama_akun'],
                'tipe_akun' => $input['tipe_akun'],
                'akun_induk' => $input['akun_induk'],
                'kode_panerimaan' => $input['kode_panerimaan'],
                'kode_pengeluaran' => $input['kode_pengeluaran'],
                'akun_transaksi' => $input['akun_transaksi'],
            ];

            if ($id == 0) {
                $result = $builder->insert($data);
                if ($result) {
                    // route_to(base_url('kasbank/transaksi/transferbank'));
                    echo json_encode(['message' => 'Inserted']);
                }
            } else {
                $result = $builder->where('id', $id)->update($data);
                if ($result) {
                    echo json_encode(['message' => 'Update']);
                }
            }
        } else {
            $arr = [
                'error' => true,
                'oneErr' => $validation->showError('kode_akun'),
                'twoErr' => $validation->showError('nama_akun'),
                'threeErr' => $validation->showError('tipe_akun'),
                'fourErr' => $validation->showError('akun_induk'),
                'fiveErr' => $validation->showError('kode_panerimaan'),
                'sixErr' => $validation->showError('kode_pengeluaran'),
                'sevenErr' => $validation->showError('akun_transaksi'),


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
        $builder = $db->table('ms_akun');
        $delete = $builder->delete(['id' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }


    // laporan
    public function laporan_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('buku_leporan');
        $input = $this->request->getPost();
        $validation->setRule('idlap', 'The  requirment fillaps', 'required');
        $validation->setRule('cabang', 'The requirment fillaps', 'required');
        $id = $input['id'];

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'idlap' => $input['idlap'],
                'tanggal1' => $input['tanggal1'],
                'tanggal2' => $input['tanggal2'],
                'perk' => $input['perk'],
                'cabang' => $input['cabang'],
            ];
            if ($id == 0) {
                $result = $builder->insert($data);
                if ($result) {
                    echo json_encode(['message' => 'Inserted']);
                }
            } else {
                $result = $builder->where('id', $id)->update($data);
                if ($result) {
                    echo json_encode(['message' => 'Update']);
                }
            }
        } else {
            $arr = [
                'error' => true,
                'oneErr' => $validation->showError('idlap'),
                'twoErr' => $validation->showError('cabang'),
            ];
            echo json_encode($arr);
        }
    }
}
