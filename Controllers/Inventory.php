<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Inventory
 **/

namespace App\Controllers;

use CodeIgniter\CLI\Console;
use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_rpt_helper;
use Config\Services;
use App\Models\Barang_model;
use App\Models\Harga_barang_model;


class Inventory extends BaseController
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
	
    //***********KATEGORI Barang Page Start Here*************//
    //Kategori Page Start Here
    public function kategori()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 511);
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
            'title' => 'Kategori',
            'body' => 'admin/inventory/data_master/kategori',
            'username' => $session->get('username'),
            'level' => $level,
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function kategori_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_kategori');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->insert($data);
            if ($result) {
                echo json_encode(['message' => 'Inserted']);
            }
        } else {
            $arr = [
                'error' => true,
                'namaErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function kategori_table()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 511);
        $db = db_connect();
        $res = $db->table('inv_kategori')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="kategori_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '<button class="btn btn-danger mb-2" onclick="kategori_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger mb-2" onclick="kategori_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="kategori_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $data[] = [
                $value->kode,
                $value->nama,
                $editview . $delete,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function kategori_edit($id)
    {
        $db = db_connect();
        $row = $db->table('inv_kategori')->where('kode', $id)->get()->getRow();
        echo json_encode($row);
    }

    public function kategori_update($id)
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_kategori');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->where('kode', $id)->update($data);
            if ($result) {
                echo json_encode(['message' => 'Update']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function kategori_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_kategori');
        $delete = $builder->delete(['kode' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //***********RAK Barang Page Start Here*************//
    //Rak Page Start Here
    public function rak()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 514);
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
            'title' => 'Rak',
            'body' => 'admin/inventory/data_master/rak',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function rak_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_rak');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->insert($data);
            if ($result) {
                echo json_encode(['message' => 'Inserted']);
            }
        } else {
            $arr = [
                'error' => true,
                'namaErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function rak_table()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 514);
        $db = db_connect();
        $res = $db->table('inv_rak')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="rak_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '<button class="btn btn-danger mb-2" onclick="rak_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger mb-2" onclick="rak_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="rak_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $data[] = [
                $value->kode,
                $value->nama,
                $editview . $delete,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function rak_edit($id)
    {
        $Commom_model =  model('Commom_modal', 'M_Commom');
        $Commom_model->data_edit($id, 'inv_rak');
        // $db = db_connect();
        // $row = $db->table('inv_rak')->where('kode', $id)->get()->getRow();
        // echo json_encode($row);
    }

    public function rak_update($id)
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_rak');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->where('kode', $id)->update($data);
            if ($result) {
                echo json_encode(['message' => 'Update']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function rak_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_rak');
        $delete = $builder->delete(['kode' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //***********GUDANG Barang Page Start Here*************//
    //Gudang Page Start Here
    public function gudang()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 515);
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
            'title' => 'Gudang',
            'body' => 'admin/inventory/data_master/gudang',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function gudang_data_submit()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_gudang');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        $validation->setRule('cabang', 'cabang', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
                'cabang' => $input['cabang'],
            ];
            $result = $builder->insert($data);
            if ($result) {
                echo json_encode(['message' => 'inserted']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
                'cabangErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function gudang_data_get()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 515);
        $db = db_connect();
        $res = $db->table('inv_gudang')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $RowCabang = $db->table('ms_unit')->where('kode', $value->cabang)->get()->getRow();
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $data[] = [
                $value->kode,
                $value->nama,
                $RowCabang->nama,
                $editview . $delete,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function gudang_data_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_gudang')->where('kode', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function gudang_data_update($id)
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_gudang');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        $validation->setRule('cabang', 'cabang', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
                'cabang' => $input['cabang'],
            ];
            $result = $builder->where('kode', $id)->update($data);
            if ($result) {
                echo json_encode(['message' => 'Update']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
                'cabangErr' => $validation->showError('cabang'),
            ];
            echo json_encode($arr);
        }
    }

    public function gudang_data_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_gudang');
        $delete = $builder->delete(['kode' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //***********SATUAN Barang Page Start Here*************//
    //Satuan Page Start Here ===================
    public function satuan()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 516);
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
            'title' => 'Satuan',
            'body' => 'admin/inventory/data_master/satuan',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function satuan_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_satuan');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->insert($data);
            if ($result) {
                echo json_encode(['message' => 'Inserted']);
            }
        } else {
            $arr = [
                'error' => true,
                'namaErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function satuan_table()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 516);
        $db = db_connect();
        $res = $db->table('inv_satuan')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $data[] = [
                $value->kode,
                $value->nama,
                $editview . $delete,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function satuan_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_satuan')->where('kode', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function satuan_update($id)
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_satuan');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->where('kode', $id)->update($data);
            if ($result) {
                echo json_encode(['message' => 'Update']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function satuan_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_satuan');
        $delete = $builder->delete(['kode' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //***********MERK Barang Page Start Here*************//
    //Merk Page Start Here
    public function merk()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 517);
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
            'title' => 'Merk',
            'body' => 'admin/inventory/data_master/merk',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function merk_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_merk');
        $input = $this->request->getPost();
        $validation->setRule(
            'nama',
            'Nama',
            'required'
        );

        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->insert($data);
            if ($result) {
                echo json_encode(['message' => 'Inserted']);
            }
        } else {
            $arr = [
                'error' => true,
                'namaErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function merk_table()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 517);
        $db = db_connect();
        $res = $db->table('inv_merk')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->kode . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<button class="btn btn-primary me-2 mb-2" onclick="data_edit(' . $value->kode . ')"><i class="fa-solid fa-edit text-white"></i> Edit</button>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $data[] = [
                $value->kode,
                $value->nama,
                $editview . $delete,
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function merk_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_merk')->where('kode', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function merk_update($id)
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('inv_merk');
        $input = $this->request->getPost();
        $validation->setRule('nama', 'Nama', 'required');
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nama' => $input['nama'],
            ];
            $result = $builder->where('kode', $id)->update($data);
            if ($result) {
                echo json_encode(['message' => 'Update']);
            }
        } else {
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
            ];
            echo json_encode($arr);
        }
    }

    public function merk_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_merk');
        $delete = $builder->delete(['kode' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //************Barang Page Start Here*************//
    //Barang Page Start Here
    public function barang()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $nourut = $model_Global->_Autono('KB');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 519);
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
            'title' => 'Barang',
            'body' => 'admin/inventory/data_master/barang',
            'nourut' => $nourut,
            'cbg' => $session->get('cabang'),
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function barang_entri($id = '')
    {

        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $nourut = $model_Global->_Autono('KB');
        $session = session();
        $level = $session->get('level');
        $SesCabang = $session->get('cabang');
        $akses = $model_Global->cek_menu_akses($level, 519);
        $kategori = $db->table('inv_kategori')->get()->getResult();
        $satuan = $db->table('inv_satuan')->get()->getResult();
        $gudang = $db->table('inv_gudang')->get()->getResult();
        $rak = $db->table('inv_rak')->get()->getResult();
        $cabang = $db->table('ms_unit')->get()->getResult();
        $merk = $db->table('inv_merk')->get()->getResult();
        $GudangGetCabang = $db->table('inv_gudang')->getWhere(['cabang' => $SesCabang])->getRow();
        $DataSatuanTax = $db->table('coretax_satuan')->get()->GetResult();

        $data = [];
        if ($id > 0) {
            $data = $db->table('inv_barang')->where('id', $id)->get()->getRow();
            $DataPaket = $db->table('inv_barang_paket')->getWhere(['kodeitem_barang' => $data->kodeitem, 'cabang' => $session->get('cabang')])->getResult();
        }
        $css = [
            'public/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
        ];
        $js = [
            'public/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js'
        ];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Barang',
            'body' => 'admin/inventory/data_master/barangentri',
            'data' => $data ?? [],
            'DataPaket' => $DataPaket ?? [],
            'nourut' => $nourut,
            'cbg' => $session->get('cabang'),
            'kategori' => $kategori,
            'satuan' => $satuan,
            'gudang' => $gudang,
            'rak' => $rak,
            'MasterCabang' => $cabang,
            'merk' => $merk,
            'level' => $level,
            'GudangGetCabang' => $GudangGetCabang,
            'DataSatuanTax' => $DataSatuanTax,
        ];
        return view('layout/layout', $data);
    }

    public function barang_table()
    {
        error_reporting(0);
        $request = Services::request();
        $cekdata = $request->getPost('draw');
        $gudang = $request->getPost('gudang');
        $cari = $request->getPost('search')['value'];
        $datatable = new Barang_model($request);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $db = db_connect();
        if ($gudang > 0) {
            $dataunit = $db->table("inv_gudang")->where(['kode' => $gudang])->get()->getRow();
            $wheredataBarang = " and kdgudang='$gudang'";
            $andcabang = " and kdcbg='$dataunit->cabang'";
            $todatabatch = $gudang;
        } else if ($gudang == 0) {
            $wheredataBarang = "";
            $andcabang = "";
            $todatabatch = 0;
        }
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 519);

        $lists = $datatable->getDatatables();
        $data = [];
        foreach ($lists as $value) {
            $GetStok = $db->query("SELECT sum(stok) AS stok, stok_min, stok_max FROM inv_barang WHERE kodeitem = '$value->kodeitem' $andcabang $wheredataBarang")->getRow();
            if ($akses->uharga == 1) {
                $hargabeli = number_format($value->hargabeli, 0, ',', '.');
            } else {
                $hargabeli = 0;
            }
            if ($akses->uedit == 1 && $akses->udel == 1) {
                $editview = '<a href="' . base_url('inventory/datamaster/barang/entri/' . $value->id) . '" class="btn btn-default"><i class="fa-solid fa-edit"></i></a>';
                $delete = '<button class="btn btn-default" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash"></i></button>';
            } else if ($akses->uedit == 1 && $akses->udel == 0) {
                $editview = '<a href="' . base_url('inventory/datamaster/barang/entri' . $value->id) . '" class="btn btn-default"><i class="fa-solid fa-edit"></i></a>';
                $delete = '';
            } else if ($akses->uedit == 0 && $akses->udel == 1) {
                $delete = '<button class="btn btn-default" onclick="data_delete(' . $value->id . ')"><i class="fa-solid fa-trash"></i></button>';
                $editview = '';
            } else {
                $editview = '';
                $delete = '';
            }

            if ($value->nie !== '-') {
                //Check Expire NIE
                $tanggalSaatIni = date("Y-m-d");
                // Menghitung selisih bulan antara tanggal saat ini dan tanggal tertentu
                $selisihBulan = date_diff(date_create($tanggalSaatIni), date_create($value->tglexpnie));
                // Memeriksa jika selisih bulan kurang dari 6 bulan
                if ($selisihBulan->y == 0 && $selisihBulan->m < 6) {
                    $kodeitem = '<span style="color: red;">' . $value->kodeitem . '</span>';
                    $namabarang = '<span style="color: red;">' . $value->namabarang . '</span>';
                } else {
                    $kodeitem = $value->kodeitem;
                    $namabarang = $value->namabarang;
                }
            } else {
                $kodeitem = $value->kodeitem;
                $namabarang = $value->namabarang;
            }

            if($value->image_1920){
                $imgUrl = base_url('assets/product/') . $value->image_1920;
                $GambarBarang = '<a href="#" class="preview-image" data-src="'.$imgUrl.'"><img src="'.$imgUrl.'" alt="" width="80" height="80"/></a>';
            }else{
                $placeholder = base_url('assets/images/placeholder.png');
               $GambarBarang = '<a href="#" class="preview-image" data-src="'.$placeholder.'"><img src="'.$placeholder.'" alt="" width="80" height="80"/></a>'; 
            }

            $data[] = [
                $GambarBarang,
                $kodeitem,
                $namabarang,
                $hargabeli,
                $GetStok->stok,
                $GetStok->stok_min,
				$GetStok->stok_max,
                $value->nie,
                $value->tglexpnie,
                $value->satuan,
                '<button class="btn btn-default me-2 mb-2" onclick="searchBatch(' . "'" . $value->kodeitem . "'" . ',' . "'" . $value->kdcbg . "'" . ',' . "'" . $todatabatch . "'" . ')"><i class="fa-solid fa-search"></i></button>',
                '<button class="btn btn-default me-2 mb-2" onclick="validasi(' . $value->id . ')"><i class="fa-solid fa-refresh"></i></button>',
                $editview . ' ' . $delete,

            ];
        }

        $output = [
            'draw' => $request->getPost('draw'),
            'recordsTotal' => $datatable->countAll(),
            'recordsFiltered' => $datatable->countFiltered(),
            'data' => $data
        ];

        echo json_encode($output);
    }

	public function barang_add()
    {
        error_reporting(0);
        $validation =  \Config\Services::validation();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $builder = $db->table('inv_barang');
        $buildPaket = $db->table('inv_barang_paket');

        // $validation->setRule('kategori', 'Kategori', 'required');
        $validation->setRule('namebarang', 'Name Barang', 'required');
        $validation->setRule('gudang', 'Gudang', 'required');
        // $validation->setRule('rak', 'Rak', 'required');
        $validation->setRule('nie', 'nie', 'required');
        $validation->setRule('ppn', 'PPN', 'required');
        // $validation->setRule('merk', 'Merk', 'required');
        $validation->setRule('satuan', 'Satuan', 'required');
        $validation->setRule('cabang', 'Cabang', 'required');
        $rules = [
            'filefoto' => 'uploaded[filefoto]|max_size[filefoto,1024]|ext_in[filefoto,jpg,jpeg,png,gif]',
        ];
        if ($validation->withRequest($this->request)->run() && $this->validate($rules)) {

            $input = $this->request->getPost();
            $image = $this->request->getFile('filefoto');

            // Direktori tempat Anda ingin menyimpan gambar
            $uploadPath = WRITEPATHBARANG;

            // Nama file baru dengan timestamp
            $newName = $image->getRandomName();

            // Pindahkan gambar ke direktori yang ditentukan
            $moveImg = $image->move($uploadPath, $newName);

            // Hapus gambar lama jika ada
            $oldImage = $this->request->getPost('filefoto_old');
            if (!empty($oldImage)) {
                unlink($uploadPath . $oldImage);
            }
            if ($moveImg == true) {
                $NewNameBarang = $newName;
            }

            $data = [
                'kode_ereport' => $input['kodeereport'],
                'kodeitem' => $input['kodeitem'],
                // 'kdcbg' => $input['cabang'],
                'kdkategori' => $input['kategori'],
                'image_1920' => $NewNameBarang,
                'namabarang' => $input['namebarang'],
                'hargajual' => $input['hargajual'],
                'nie' => $input['nie'],
                'tglexpnie' => $input['tglexpnie'],
                'stok_min' => $input['stokmin'],
                'stok_max' => $input['stokmax'],
				'moq' => $input['moq'],
                'satuan' => $input['satuan'],
                // 'kdgudang' => $input['gudang'],
                'kdrak' => $input['rak'],
                'kdmerk' => $input['merk'],
                'kode_user' => $session->get('username'),
                'tglentry' => date('Y-m-d H:i:s'),
                'ppn' => $input['ppn'],
                'taxbarangjasa' => $input['barangjasa'],
                'taxsatuan' => $input['taxsatuan'],
            ];
            $db->transStart();
            $ArrayGudang = $input['gudang'];
            foreach ($ArrayGudang as $res) {
                $NameCabang = $db->table('inv_gudang')->getWhere(['kode' => $res])->getRow();
                //Insert Barang Paketan
                $kodeitem  = $input['NamaBrgPaket'];
                $satuan = $input['satPaketan'];
                $qty = $input['qtyPaket'];
                $jumdata  = count($kodeitem);
                for ($i = 0; $i < $jumdata; $i++) {
                    $SelectBarang = $builder->getWhere(['kodeitem' => $kodeitem[$i], 'kdcbg' => $input['cabang']])->getRow()->namabarang;
                    $datad = array(
                        'kodeitem_paket'   => $kodeitem[$i],
                        'namabarang_paket'   => $SelectBarang,
                        'satuan_paket' => $satuan[$i],
                        'qty_Paket' => $qty[$i],
                        'kodeitem_barang' => $input['kodeitem'],
                    );
                    if ($kodeitem[$i] != "") {
                        $datad['cabang'] = $NameCabang->cabang;
                        $datad['gudang'] = $res;
                        $buildPaket->insert($datad);
                    }
                }
            }

            $countGudang = count($ArrayGudang);
            $CekDataInsert = 0;
            for ($i = 0; $i < $countGudang; $i++) {
                $kodeGudang = $ArrayGudang[$i];
                $GudangGetCabang = $db->table('inv_gudang')->getWhere(['kode' => $kodeGudang])->getRow();

                $datainv = $builder->getWhere(['kodeitem' => $input['kodeitem'], 'kdcbg' => $GudangGetCabang->cabang])->getRow();
                if ($datainv == "") {
                    $data['kdgudang'] = $kodeGudang;
                    $data['kdcbg'] = $GudangGetCabang->cabang;
                    $result = $builder->insert($data);
                    if ($result) {
                        $CekDataInsert = 0;
                    }
                } else {
                    $CekDataInsert++;
                }
            }
            if ($CekDataInsert == 0) {
                $model_Global->_updatecounter1('KB');
                echo json_encode(['message' => 'Data Berhasil di simpan']);
            } else {
                echo json_encode(['message' => 'kodeitem sudah ada, silakan ganti nomor kode']);
            }
            $db->transComplete();
            if ($db->transStatus() === false) {
                // Jika terjadi kesalahan, batalkan transaksi
                $db->transRollback();
                echo json_encode(['message' => 'Transaksi Gagal Mohon Coba lagi']);
            }
        } else {
            $arr = [
                'error' => true,
                // 'kategori' => $validation->showError('kategori'),
                'namebarang' => $validation->showError('namebarang'),
                'gudang' => $validation->showError('gudang'),
                // 'rak' => $validation->showError('rak'),
                'nie' => $validation->showError('nie'),
                'ppn' => $validation->showError('ppn'),
                // 'merk' => $validation->showError('merk'),
                'satuan' => $validation->showError('satuan'),
                'cabang' => $validation->showError('cabang'),
                'filefoto' => $validation->listErrors()
            ];
            echo json_encode($arr);
        }
    }
    
    public function SelectBarangPaket($kode)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $builder = $db->table('inv_barang')->getWhere(['kodeitem' => $kode, 'kdcbg' => $cabang])->getRow();
        echo json_encode($builder);
    }

    public function barang_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_barang')->where('id', $id)->get()->getRow();
        $response = ['DataBarang' => $builder];
        echo json_encode($response);
    }

    public function barang_update($id)
    {
        error_reporting(0);
        $validation =  \Config\Services::validation();
        $model_inventory =  model('Inventory_model', 'M_INVENTORY');
        $session = session();
        $db = db_connect();
        $builder = $db->table('inv_barang');
        $buildPaket = $db->table('inv_barang_paket');
        $input = $this->request->getPost();
        $image = $this->request->getFile('filefoto');
        // $validation->setRule('kategori', 'Kategori', 'required');
        $validation->setRule('namebarang', 'Name Barang', 'required');
        $validation->setRule('gudang', 'Gudang', 'required');
        // $validation->setRule('rak', 'Rak', 'required');
        $validation->setRule('nie', 'nie', 'required');
        $validation->setRule('ppn', 'PPN', 'required');
        // $validation->setRule('merk', 'Merk', 'required');
        $validation->setRule('satuan', 'Satuan', 'required');
        $validation->setRule('cabang', 'Cabang', 'required');
        if ($validation->withRequest($this->request)->run()) {
            !empty($input['kategori']) ? $kategori = $input['kategori'] : $kategori = '0';
            !empty($input['rak']) ? $rak = $input['rak'] : $rak = '0';
            !empty($input['merk']) ? $merk = $input['merk'] : $merk = '0';
            // Jika ada file foto yang diupload
            if ($image != null && $image->isValid() && !empty($image->getName())) {
                // Direktori tempat Anda ingin menyimpan gambar
                $uploadPath = WRITEPATHBARANG;
                // Nama file baru dengan timestamp
                $newName = $image->getRandomName();
                // Pindahkan gambar ke direktori yang ditentukan
                $moveImg = $image->move($uploadPath, $newName);
                // Hapus gambar lama jika ada
                $oldImage = $this->request->getPost('filefoto_old');
                if (!empty($oldImage)) {
                    unlink($uploadPath . $oldImage);
                }
                if ($moveImg == true) {
                    $NewNameBarang = $newName;
                }
            } else {
                $oldImage = $this->request->getPost('filefoto_old');
                $NewNameBarang = $oldImage;
            }
            $data = [

                'kode_ereport' => $input['kodeereport'],
                'kodeitem' => $input['kodeitem'],
                // 'kdcbg' => $input['cabang'],
                'kdkategori' => $kategori,
                'image_1920' => $NewNameBarang,
                'namabarang' => $input['namebarang'],
                'hargajual' => $input['hargajual'],
                'nie' => $input['nie'],
                'tglexpnie' => $input['tglexpnie'],
                'stok_min' => $input['stokmin'],
                'stok_max' => $input['stokmax'],
				'moq' => $input['moq'],
                'satuan' => $input['satuan'],
                //'kdgudang' => $input['gudang'],
                'kdrak' => $rak,
                'kdmerk' => $merk,
                'kode_user' => $session->get('username'),
                'tglentry' => date('Y-m-d H:i:s'),
                'ppn' => $input['ppn'],
                'taxbarangjasa' => $input['barangjasa'],
                'taxsatuan' => $input['taxsatuan'],
            ];
            $db->transStart();
            $ArrayGudang = $input['gudang'];
            foreach ($ArrayGudang as $res) {
                $GudangGetCabang = $db->table('inv_gudang')->getWhere(['kode' => $res])->getRow();
                $result = $builder->where('kodeitem', $input['kodeitem'])->update($data);
                $kodeitem  = $input['NamaBrgPaket'];
                $satuan = $input['satPaketan'];
                $qty = $input['qtyPaket'];
                $jumdata  = count($kodeitem);
                $buildPaket->delete(['kodeitem_barang' => $input['kodeitem']]);
                for ($i = 0; $i < $jumdata; $i++) {
                    $hjualpaket    = str_replace(",", "", $input['hjualpaket'][$i]);
                    $SelectBarang = $builder->getWhere(['kodeitem' => $kodeitem[$i]])->getRow()->namabarang;
                    $datad = array(
                        'kodeitem_paket'   => $kodeitem[$i],
                        'namabarang_paket'   => $SelectBarang,
                        'satuan_paket' => $satuan[$i],
                        'qty_Paket' => $qty[$i],
                        'kodeitem_barang' => $input['kodeitem'],
                        'cabang' => $GudangGetCabang->cabang,
                        'gudang' => $res,
                    );
                    if ($kodeitem[$i] != "") {
                        $NumRowsBarang = $builder->getWhere(['kodeitem' => $kodeitem[$i]])->getResult();
                        foreach ($NumRowsBarang as $DataBarang) {
                            $datad['cabang'] = $DataBarang->kdcbg;
                            $datad['gudang'] = $DataBarang->kdgudang;
                            $buildPaket->insert($datad);
                        }
                    }
                }
            }

            $countGudang = count($ArrayGudang);
            $CekDataInsert = 0;
            for ($i = 0; $i < $countGudang; $i++) {
                $kodeGudang = $ArrayGudang[$i];
                $GudangGetCabang = $db->table('inv_gudang')->getWhere(['kode' => $kodeGudang])->getRow();

                $datainv = $builder->getWhere(['kodeitem' => $input['kodeitem'], 'kdcbg' => $GudangGetCabang->cabang])->getRow();
                if ($datainv == "") {
                    $data['kdgudang'] = $kodeGudang;
                    $data['kdcbg'] = $GudangGetCabang->cabang;
                    $result = $builder->insert($data);
                    if ($result) {
                        $CekDataInsert = 0;
                    }
                    $NameCabang = $db->table('inv_gudang')->getWhere(['kode' => $res])->getRow();
                } else {
                    $CekDataInsert++;
                }
            }
            if ($result) {
                $model_inventory->UpdateBarangHarga($input['kodeitem'], $input['cabang'], $input['namebarang']);
                echo json_encode(['message' => 'Update']);
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
            $arr = [
                'error' => true,
                'nameErr' => $validation->showError('nama'),
                'filefoto' => $validation->listErrors()
            ];
            echo json_encode($arr);
        }
    }

    public function barang_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_barang');
        $buildPaket = $db->table('inv_barang_paket');
        $RowData = $builder->getWhere(['id' => $id])->getRow();
        $builder2 = $db->table('inv_barang_harga');
        $delete = $builder->delete(['id' => $id]);
        // Direktori tempat Anda ingin menyimpan gambar
        $uploadPath = WRITEPATHBARANG;
        $Image = $RowData->image_1920;
        $builder->delete(['kodeitem' => $RowData->kodeitem]);
        $builder2->delete(['kodeitem' => $RowData->kodeitem]);
        $buildPaket->delete(['kodeitem_paket' => $RowData->kodeitem]);
        $buildPaket->delete(['kodeitem_barang' => $RowData->kodeitem]);
        if ($delete) {
            unlink($uploadPath . $Image);
            echo json_encode(['message' => 'Deleted']);
        }
    }

    public function barang_table_batch($kode, $cabang, $gudang)
    {
        $db = db_connect();
        if (!empty($kode)) {
            if ($gudang != 0) {
                $datagudang = $db->table("inv_gudang")->where(['kode' => $gudang])->get()->getRow();
                $wheredataBatch = " and cabang='$datagudang->cabang' and gudang='$gudang' ";
            } else {
                $wheredataBatch = "";
            }
            $data  = $db->query("SELECT inv_nobatch.*, inv_barang.namabarang from inv_nobatch 
            inner join inv_barang on inv_barang.kodeitem=inv_nobatch.kodeitem 
            where inv_barang.kodeitem = '$kode' $wheredataBatch
            GROUP by no_batch, gudang order by inv_nobatch.no_batch asc")->getResult();

            foreach ($data  as $row) {
                if ($row->qty_saldo > '0') {
                    $datacabang = $db->table("ms_unit")->where(['kode' => $row->cabang])->get()->getRow();
?>
                    <tr>
                        <td width="20" align="centerx" id="kode_brg">
                            <?php echo $row->kodeitem; ?></a>
                        </td>
                        <td id="nama_brg">
                            <!-- <a href="" onclick="post_batch('<?php echo $row->no_batch; ?>','<?php echo $row->tanggal_exp; ?>')"> -->
                            <?php echo $row->namabarang; ?>
                        </td>
                        <td id="batch_brg"><?php echo $row->no_batch; ?></td>
                        <td id="tgl_exp_brg"> <?php echo $row->tanggal_exp; ?></td>
                        <td id="saldo_brg"><?php echo $row->qty_saldo; ?></td>
                        <td id="nama_cbg"><?php echo $datacabang->nama; ?></td>
                    </tr>
            <?php }
            } ?>

        <?php
        }
    }

    public function validasi_perbarang($id)
    {
        error_reporting(0);
        $db = db_connect();
        $db->transStart();

        $DataBarang = $db->table('inv_barang')->getWhere(['id' => $id])->getRow();
        $kode = $DataBarang->kodeitem;
        $cabang = $DataBarang->kdcbg;
        $gudang = $DataBarang->kdgudang;

        $DataSOBarang = $db->query("SELECT tanggal, nobukti, so, tglrekam
            FROM inv_transaksi AS a
            WHERE kodeitem='$kode' and gudang='$gudang' and cabang='$cabang' AND jenis='Stock Opname' and tanggal = (
                SELECT MAX(tgl)
                FROM inv_stockopname AS b
                WHERE b.kodeitem='$kode' and b.gudang='$gudang' and b.cabang='$cabang'
            )")->getRow();

        $dataInvTransaksiPerbarang = $db->query("SELECT sum(penerimaan) as terima, sum(pengeluaran) as keluar from inv_transaksi 
            where kodeitem='$kode' AND tglrekam >='$DataSOBarang->tglrekam' and cabang='$cabang' and gudang='$gudang' ")->getRow();

        $qtySO     = $DataSOBarang->so;
        $keluar = $dataInvTransaksiPerbarang->keluar;
        $terima = $dataInvTransaksiPerbarang->terima;
        $jumlah = ($qtySO + $terima - $keluar);
        $builder = $db->table("inv_barang");
        $builder->where(['kodeitem' => $kode, 'kdcbg' => $cabang, 'kdgudang' => $gudang])->update(['stok' => $jumlah]);

        // VALIDASI BATCH
        $dataInvTransaksiPerNobatch = $db->query("SELECT a.no_batch FROM inv_nobatch a 
									INNER JOIN inv_transaksi b ON b.no_batch = a.no_batch 
									WHERE b.kodeitem = '$kode' 
									AND a.kodeitem='$kode' 
									AND b.tglrekam >='$DataSOBarang->tglrekam' 
									AND b.jenis !='Stock Opname'
									AND a.gudang = '$gudang'
									AND a.cabang = '$cabang' 
									AND b.gudang = '$gudang'
									AND b.cabang = '$cabang' 
									GROUP BY a.no_batch")->getResult();

        foreach ($dataInvTransaksiPerNobatch as $data) {
            $DataSODetailBarang = $db->query("SELECT * FROM inv_sodetail 
									where tanggal='$DataSOBarang->tanggal' 
									AND kodeitem='$kode' AND no_batch='$data->no_batch' 
									and gudang='$gudang' and cabang='$cabang'")->getRow();

            $JumlahNobatchSo = $db->query("SELECT SUM(qty_stok) AS qty FROM inv_sodetail 
                                    WHERE kodeitem='$kode' 
                                    and tanggal='$DataSOBarang->tanggal' 
                                    AND no_batch='$data->no_batch'
                                    and cabang='$cabang' 
                                    and gudang='$gudang'")->getResult();
            foreach ($JumlahNobatchSo as $data3) {
                $KeluarMasukNobatch = $db->query("SELECT sum(penerimaan) as terima, sum(pengeluaran) as keluar from inv_transaksi 
									where kodeitem='$kode' 
									AND tglrekam >='$DataSOBarang->tglrekam' 
									AND no_batch = '$data->no_batch'
									and cabang='$cabang' 
									and gudang='$gudang'")->getRow();
                if (!empty($DataSODetailBarang)) {
                    $batch      = $data->no_batch;
                    $qtyterima     = $data3->qty + $KeluarMasukNobatch->terima;
                    $qtykeluar     = $KeluarMasukNobatch->keluar;
                    $jumlah     = ($data3->qty + $KeluarMasukNobatch->terima - $KeluarMasukNobatch->keluar);
                } else {
                    $batch      = $data->no_batch;
                    $qtyterima     = $KeluarMasukNobatch->terima;
                    $qtykeluar     = $KeluarMasukNobatch->keluar;
                    $jumlah     = ($qtyterima - $qtykeluar);
                }
                $builder2 = $db->table("inv_nobatch");
                $builder2->where(['kodeitem' => $kode, 'no_batch' => $batch, 'cabang' => $cabang, 'gudang' => $gudang])->update(['qty_terima' => $qtyterima, 'qty_jual' => $qtykeluar, 'qty_saldo' => $jumlah]);
            }
        }
        $response = ['DataOne' => $builder];

        echo json_encode($response);
        $db->transComplete();
    }

    public function validasiallbarang($cabang)
    {
        try {
            $model_inventory =  model('Inventory_model', 'M_INVENTORY');
            $db = db_connect();
            $db->transStart();
            $DataSoItem = [];
            $DataSoItemBatch = [];
            $DataNoSoItem = [];
            $DataNoSoItemBatch = [];
            $DataStokBarangPerCabang = $model_inventory->DataStokBarangPercabang($cabang);
            foreach ($DataStokBarangPerCabang as $row) {
				// $this->validatestockminmax($cabang);
                $_kode   = $row->kodeitem;
                $_gudang   = $row->kdgudang;
                $_stock   = $row->stok;
                $_StockSO   = $row->stok_so;
                $DataSOBarang = $model_inventory->DataSOBarang($_kode, $_gudang, $cabang);
                if ($DataSOBarang != null) {
                    $tglrekam = $DataSOBarang->tglrekam;
                    $tanggal = $DataSOBarang->tanggal;
                    $qtySO     = $DataSOBarang->so;
                    //Validasi Stock Barang
                    $dataInvTransaksiPerbarang = $model_inventory->dataInvTransaksiPerbarang($_kode, $cabang, $_gudang, $tglrekam);
                    $keluar = $dataInvTransaksiPerbarang->keluar;
                    $terima = $dataInvTransaksiPerbarang->terima;
                    $jumlah = ($qtySO + $terima - $keluar);
                    if ($_stock != $jumlah) {
                        $dataValiadsiStokPerbarang = $model_inventory->ValiadasiStok($_kode, $cabang, $_gudang, $jumlah);
                        $DataSoItem[] = ['kodeitem' => $_kode, 'namabarang' => $row->namabarang];
                    }

                    // Validasi Stock Batch 
                    //cari nobatch di inv transaksi
                    $dataInvTransaksiPerNobatch = $model_inventory->dataInvTransaksiPerNobatch($_kode, $cabang, $_gudang, $tglrekam);
                    foreach ($dataInvTransaksiPerNobatch as $data) {
                        $NobatchTransaksi = $data->no_batch;
                        $DataSODetailBarang = $model_inventory->DataSODetailBarang($_kode, $_gudang, $cabang, $NobatchTransaksi, $tanggal);
                        //hitung qty barang dari so per nobatch
                        $JumlahNobatchSo = $model_inventory->JumlahNobatchSo($_kode, $cabang, $_gudang, $NobatchTransaksi, $tanggal);
                        //hitung kerluar masuk nobatch di inv transaksi
                        $KeluarMasukNobatch = $model_inventory->KeluarMasukNobatch($_kode, $cabang, $_gudang, $NobatchTransaksi, $tglrekam);
                        $keluar = $KeluarMasukNobatch->keluar;
                        $terima = $KeluarMasukNobatch->terima;
                        $_NobatchTransaksi = $NobatchTransaksi;
                        $_qtySO = $JumlahNobatchSo->qty;
                        $_terima = $terima;
                        $_keluar = $keluar;
                        if (!empty($DataSODetailBarang)) {
                            $batch      = $_NobatchTransaksi;
                            $qtyterima     = $_qtySO + $_terima;
                            $qtykeluar     = $_keluar;
                            $jumlah     = ($_qtySO + $_terima - $_keluar);
                        } else {
                            $batch      = $_NobatchTransaksi;
                            $qtyterima     = $_terima;
                            $qtykeluar     = $_keluar;
                            $jumlah     = ($qtyterima - $qtykeluar);
                        }
                        $DataNobatch = $model_inventory->DataNobatch($_kode, $batch, $cabang);
                        $qtySaldoBatch = $DataNobatch->qty_saldo;
                        if ($qtySaldoBatch != $jumlah) {
                            $dataValiadsiStokPerNobatch = $model_inventory->ValiadasiStokNobatch($_kode, $cabang, $_gudang, $batch, $qtyterima, $qtykeluar, $jumlah);
                            $DataSoItemBatch[] = ['kodeitembatch' => $_kode, 'namabarang' => $row->namabarang, 'batch' => $batch];
                        }
                    }
                } else {
                    $tglrekam = "1900-01-01";
                    $dataInvTransaksiPerbarang = $model_inventory->dataInvTransaksiPerbarang($_kode, $cabang, $_gudang, $tglrekam);
                    $keluar = $dataInvTransaksiPerbarang->keluar;
                    $terima = $dataInvTransaksiPerbarang->terima;
                    $jumlah = ($terima - $keluar);
                    if ($_stock != $jumlah) {
                        $dataValiadsiStokPerbarang = $model_inventory->ValiadasiStok($_kode, $cabang, $_gudang, $jumlah);
                        $DataNoSoItem[] = ['kodeitemTidakAdaSo' => $_kode, 'namabarang' => $row->namabarang];
                    }

                    // Validasi Stock Batch 
                    //cari nobatch di inv transaksi
                    $dataInvTransaksiPerNobatch = $model_inventory->dataInvTransaksiPerNobatch($_kode, $cabang, $_gudang, $tglrekam);
                    foreach ($dataInvTransaksiPerNobatch as $data) {
                        $NobatchTransaksi = $data->no_batch;
                        //hitung kerluar masuk nobatch di inv transaksi
                        $KeluarMasukNobatch = $model_inventory->KeluarMasukNobatch($_kode, $cabang, $_gudang, $NobatchTransaksi, $tglrekam);
                        $keluar = $KeluarMasukNobatch->keluar;
                        $terima = $KeluarMasukNobatch->terima;
                        $jumlah = ($terima - $keluar);
                        $DataNobatch = $model_inventory->DataNobatch($_kode, $NobatchTransaksi, $cabang);
                        $qtySaldoBatch = $DataNobatch->qty_saldo;
                        if ($qtySaldoBatch != $jumlah) {
                            $dataValiadsiStokPerNobatch = $model_inventory->ValiadasiStokNobatch($_kode, $cabang, $_gudang, $NobatchTransaksi, $terima, $keluar, $jumlah);
                            $DataNoSoItemBatch[] = ['kodeitembatch' => $_kode, 'namabarang' => $row->namabarang, 'batch' => $NobatchTransaksi];
                        }
                    }
                }
            }
            $db->transComplete();
            if ($db->transStatus() === false) {
                // Jika terjadi kesalahan, batalkan transaksi
                echo json_encode('Data Gagal di validasi');
                $db->transRollback();
            } else {
                echo json_encode('Data Berhasil Di Validasi');
            }
            // Transaksi sukses, commit perubahan ke database
            $db->transCommit();
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
	
	public function validatestockminmax($cabang)
    {
        try {
            error_reporting(0);
            
            $db = db_connect();
            $db->transStart();
                $builder = $db->table('inv_barang')->get()->getResult();
                foreach ($builder as $row) {
                    $isPaket = $db->table('inv_barang_paket')
                          ->where(['kodeitem_barang' => $row->kodeitem, 'cabang' => $row->kdcbg])
                          ->countAllResults();
                    if ($isPaket == 0) {
                        $DateNow = date('Y-m-d');
                
                        // Tentukan range hari berdasarkan cabang
                        if ($row->kdcbg == '01') {
                            $daysMin = '-120 days';
                            $daysMax = '-160 days';
                        } else {
                            $daysMin = '-30 days';
                            $daysMax = '-60 days';
                        }

                        $dateMin = date('Y-m-d', strtotime($daysMin));
                        $dateMax = date('Y-m-d', strtotime($daysMax));

                        // 3. Hitung Stok Min (Total pengeluaran range Min)
                        $stokMin = $db->table('inv_transaksi')
                            ->selectSum('pengeluaran', 'total')
                            ->where('kodeitem', $row->kodeitem)
                            ->where('cabang', $row->kdcbg)
                            ->where('tanggal >=', $dateMin)
                            ->where('tanggal <=', $DateNow)
                            ->get()->getRow()->total ?? 0;

                        // 4. Hitung Stok Max (Total pengeluaran range Max)
                        $stokMax = $db->table('inv_transaksi')
                            ->selectSum('pengeluaran', 'total')
                            ->where('kodeitem', $row->kodeitem)
                            ->where('cabang', $row->kdcbg)
                            ->where('tanggal >=', $dateMax)
                            ->where('tanggal <=', $DateNow)
                            ->get()->getRow()->total ?? 0;

                        // 5. Update data barang
                        $db->table('inv_barang')
                            ->where(['kodeitem' => $row->kodeitem, 'kdcbg' => $row->kdcbg, 'kdgudang' => $row->kdgudang])
                            ->update([
                                'stok_min' => $stokMin,
                                'stok_max' => $stokMax,
                            ]);
                    }
                }
                
            $db->transComplete();
            if ($db->transStatus() === false) {
                // Jika terjadi kesalahan, batalkan transaksi
                echo json_encode('Data Gagal di validasi');
                $db->transRollback();
            } else {
                echo json_encode('Data Berhasil Di Validasi');
            }
            // Transaksi sukses, commit perubahan ke database
            $db->transCommit();
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }

    }
	
    //***********HARGA Barang PERCUSTOMER Page Start Here*************//
    //Harga Barang Per Customer Page Start Here
    public function harga_barang_per_customer()
    {
        $session = session();
        $level = $session->get('level');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $akses = $model_Global->cek_menu_akses($level, 518);
        if ($akses->uadd == 1) {
            $tambah = 'Harga Barang Customer Baru';
        } else {
            $tambah = '';
        }
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Harga Barang Per Customer',
            'body' => 'admin/inventory/data_master/harga-barang',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function hargabarang_entri($id = '')
    {
        $session = session();
        $db = db_connect();
        $data = [];
        if ($id != "") {
            $data       = $db->table('inv_barang_harga')->where('id_harga', $id)->get()->getRow();
            $DetailItem = $db->table('inv_barang_harga')->where(['kodeitem' => $data->kodeitem, 'kode_spl' => $data->kode_spl, 'cabang' => $data->cabang, 'gudang' => $data->gudang])->get()->getResult();
            $ResGudang = $db->table('inv_gudang')->getWhere()->getResult();
        } else {
            $ResGudang = $db->table('inv_gudang')->getWhere()->getResult();
        }

        $ResSupp = $db->table('ap_supplier')->get()->getResult();

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'data' => $data ?? [],
            'DetailItem' => $DetailItem ?? [],
            'title' => 'Harga Barang Per Customer',
            'body' => 'admin/inventory/data_master/add-harga-barang',
            'supp' => $ResSupp,
            'gudang' => $ResGudang,
            'kdcbg' => $session->get('cabang')
        ];
        return view('layout/layout', $data);
    }

    public function getnamecust($kode, $kodeitem, $cabang)
    {
        $db = db_connect();
        $data1 = $db->query("SELECT * FROM ar_customer where kode='$kode'")->getRow();
        $builder = $db->table('inv_barang_harga');
        $data2 = $builder->getWhere(['kode_customer' => $kode, 'kodeitem' => $kodeitem, 'cabang' => $cabang])->getRow();
        $respone = ['datacs' => $data1, 'datahbpc' => $data2];
        echo json_encode($respone);
    }

    public function getcabang($gudang)
    {
        $db = db_connect();
        $data = $db->query("SELECT * FROM inv_gudang where kode='$gudang'")->getRow();
        echo json_encode($data);
    }

    public function getkodespl1($kode, $cabang, $gudang)
    {
        $db = db_connect();
        $data1 = $db->query("SELECT a.*,b.nama,c.kode_spl,c.bruto_spl,c.disc_spl,c.netto_spl FROM inv_barang a
		INNER JOIN ap_supplier b on b.kode = a.kode_spl1 
		INNER JOIN inv_barang_harga c on c.kodeitem = a.kodeitem
		where a.kodeitem='$kode' and a.kdcbg='$cabang'and a.kdgudang='$gudang' GROUP BY c.kode_spl")->getRow();

        $data2 = $db->query("SELECT a.*,b.nama,c.kode_spl,c.bruto_spl,c.disc_spl,c.netto_spl FROM inv_barang a
        INNER JOIN ap_supplier b on b.kode = a.kode_spl2 
        INNER JOIN inv_barang_harga c on c.kodeitem = a.kodeitem
        where a.kodeitem='$kode' and a.kdcbg='$cabang' and a.kdgudang='$gudang' GROUP BY c.kode_spl ")->getRow();

        $data3 = $db->query("SELECT a.*,b.nama,c.kode_spl,c.bruto_spl,c.disc_spl,c.netto_spl FROM inv_barang a
        INNER JOIN ap_supplier b on b.kode = a.kode_spl3
        INNER JOIN inv_barang_harga c on c.kodeitem = a.kodeitem
        where a.kodeitem='$kode' and a.kdcbg='$cabang' and a.kdgudang='$gudang' GROUP BY c.kode_spl ")->getRow();

        $data4 = $db->query("SELECT a.*,b.nama,c.kode_spl,c.bruto_spl,c.disc_spl,c.netto_spl FROM inv_barang a
        INNER JOIN ap_supplier b on b.kode = a.kode_spl4
        INNER JOIN inv_barang_harga c on c.kodeitem = a.kodeitem
        where a.kodeitem='$kode' and a.kdcbg='$cabang' and a.kdgudang='$gudang' GROUP BY c.kode_spl  ")->getRow();

        $response = ['spl1' => $data1, 'spl2' => $data2, 'spl3' => $data3, 'spl4' => $data4];
        echo json_encode($response);
    }

    public function harga_barang_add()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $db->transStart();

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 518);
        $input = $this->request->getPost();
        $param = $input['id'];
        $jumdata = count($input['kode']);

        if ($akses->uadd == 1) {

            for ($i = 0; $i < $jumdata; $i++) {
                $kodeItem = $input['barang'];
                $cabang = $input['cabang'];
                $gudang = $input['gudang'];
                $hargasendiri = $input['hargasendiri'];

                //str replace supplier
                $_jml_netto_supp  =  str_replace(',', '', $input['jumlah_netto']);
                $_bruto_spl  =  str_replace(',', '', $input['bruto_spl']);
                $_disc_spl  =  str_replace(',', '', $input['disc_spl']);

                //str replace Customer
                $_bruto_cust  =  str_replace(',', '', $input['bruto']);
                $_netto_cust   =  str_replace(',', '', $input['netto']);
                $_discrpcust   =  str_replace(',', '', $input['rpcust']);
                $_fee1rp   =  str_replace(',', '', $input['fee1rp']);
                $_fee2rp   =  str_replace(',', '', $input['fee2rp']);
                $_fee3rp   =  str_replace(',', '', $input['fee3rp']);
                $_profitrp   =  str_replace(',', '', $input['profitrp']);
                $_marginrp   =  str_replace(',', '', $input['netmarginrp']);

                // name replace
                $nama_brg = $db->query("SELECT * from inv_barang where kodeitem = '$kodeItem' and kdcbg='$cabang' and kdgudang ='$gudang'")->getRow();

                $data = [
                    'kode_customer' => $input['kode'][$i],
                    'nama_customer' => $input['namecust'][$i],
                    'bruto_cust' => $_bruto_cust[$i],
                    'hargamodal_spl' => $_jml_netto_supp,
                    'netto_cust' => $_netto_cust[$i],
                    'diskon_pr' => $input['disc'][$i],
                    'diskon_rp' => $_discrpcust[$i],
                    'hargajual_net' => $_netto_cust[$i],
                    'fee1_pr' => $input['fee1'][$i],
                    'bagian1' => $input['bagian1'][$i],
                    'fee1_rp' => $_fee1rp[$i],
                    'fee2_pr' => $input['fee2'][$i],
                    'bagian2' => $input['bagian2'][$i],
                    'fee2_rp' => $_fee2rp[$i],
                    'fee3_pr' => $input['fee3'][$i],
                    'bagian3' => $input['bagian3'][$i],
                    'fee3_rp' => $_fee3rp[$i],
                    'profit_pr' => $input['Profit'][$i],
                    'profit_rp' => $_profitrp[$i],
                    'netmargin_rp' => $_marginrp[$i],
                    'kode_spl' => $input['kd_spl'],
                    'bruto_spl' => $_bruto_spl,
                    'disc_spl' => $_disc_spl,
                    'netto_spl' => $_jml_netto_supp,
                    'tanggal' => $input['tanggal'],
                    'tglrekam' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                ];

                // Not to Running
                $data_barang = array(
                    'hargabeli' => $_jml_netto_supp,
                    'kode_spl1' => $input['supp1'],
                    'bruto_spl1' => $input['bruto1'],
                    'disc_spl1' => $input['disc_sup1'],
                    'netto_spl1' => str_replace(',', '', $input['netto1']),

                    'kode_spl2' => $input['supp2'],
                    'bruto_spl2' => $input['bruto2'],
                    'disc_spl2' => $input['disc_sup2'],
                    'netto_spl2' => str_replace(',', '', $input['netto2']),

                    'kode_spl3' => $input['supp3'],
                    'bruto_spl3' => $input['bruto3'],
                    'disc_spl3' => $input['disc_sup3'],
                    'netto_spl3' => str_replace(',', '', $input['netto3']),

                    'kode_spl4' => $input['supp4'],
                    'bruto_spl4' => $input['bruto4'],
                    'disc_spl4' => $input['disc_sup4'],
                    'netto_spl4' => str_replace(',', '', $input['netto4']),
                    'kode_user' => $session->get('username'),
                );

                $HrgSupplier = [
                    'kode_spl' => $input['kd_spl'],
                    'bruto_spl' => $_bruto_spl,
                    'disc_spl' => $_disc_spl,
                    'netto_spl' => $_jml_netto_supp,
                ];
				
				$datalogs = [
                    'date' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                ];

                $builder = $db->table('inv_barang_harga');
                $buildbrg   = $db->table('inv_barang');
                if ($param == 0) {
                    $data['kodeitem'] = $kodeItem;
                    $data['nama_barang'] = $nama_brg->namabarang;
					$data['satuan'] = $nama_brg->satuan;
                    $data['cabang'] = $cabang;
                    $data['gudang'] = $gudang;
                    $DataBarangHargaCSNull = $builder->getWhere(['kodeitem' => $kodeItem, 'kode_customer' => '', 'gudang' => $gudang, 'cabang' => $cabang])->getRow();
                    if ($DataBarangHargaCSNull) {
                        $result = $builder->where(['kodeitem' => $kodeItem, 'kode_customer' => '', 'gudang' => $gudang, 'cabang' => $cabang])->update($data);
						$datalogs['detail'] = 'Update data Harga Barang kodeitem ' . $kodeItem . ' customer ' . $input['kode'][$i] .' Bruto '. $_bruto_cust[$i] .' Disc '. $input['disc'][$i] .' cabang ' . $cabang . ' gudang ' . $gudang;
					    $db->table("logs_harga_barang")->insert($datalogs);
                    } else {
                        $result = $builder->insert($data);
						$datalogs['detail'] = 'Tambah data Harga Barang kodeitem ' . $kodeItem . ' customer ' . $input['kode'][$i] .' Bruto '. $_bruto_cust[$i] .' Disc '. $input['disc'][$i] .' cabang ' . $cabang . ' gudang ' . $gudang;
					    $db->table("logs_harga_barang")->insert($datalogs);
                    }
                    $UBarang = $buildbrg->where(['kodeitem' => $kodeItem])->update($data_barang);
                    echo json_encode($result);
                } else {
					$WhereDataBarangHarga = [
                        'kodeitem' => $kodeItem,
                        'kode_customer' => $input['kode'][$i],
                        'bruto_cust' => $_bruto_cust[$i],
                        'diskon_pr' => $input['disc'][$i],
                        'netto_cust' => $_netto_cust[$i],
                        'gudang' => $gudang,
                        'cabang' => $cabang
                    ];
					$DataBarangHarga = $builder->getWhere($WhereDataBarangHarga)->getRow();
                    if(empty($DataBarangHarga)){
                       $datalogs['detail'] = 'Update data Harga Barang kodeitem ' . $kodeItem . ' customer ' . $input['kode'][$i] .' Bruto '. $_bruto_cust[$i] .' Disc '. $input['disc'][$i] .' cabang ' . $cabang . ' gudang ' . $gudang;
					    $db->table("logs_harga_barang")->insert($datalogs);
                    }
                    $result = $builder->where(['kodeitem' => $kodeItem, 'kode_customer' => $input['kode'][$i], 'gudang' => $gudang, 'cabang' => $cabang])->update($data);
                    $UBarang = $buildbrg->where(['kodeitem' => $kodeItem])->update($data_barang);
					$this->UpdateNotInSessCabang($HrgSupplier,$cabang,$kodeItem,$hargasendiri);
                    echo json_encode($result);
                }
            }
        } else {
            echo json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }
	
	public function UpdateNotInSessCabang($data,$cabang,$kodeitem,$hargasendiri)
    {
		error_reporting(0);
        $db = db_connect();
        $builder = $db->table('inv_barang_harga');
        $DataHargaSupplier = $builder->getWhere(['kodeitem' => $kodeitem])->getResult();
        foreach($DataHargaSupplier as $row){
            if($row->cabang != $cabang){
                $vhargamodal = $hargasendiri;
                $vjumlah_netto = $data['netto_spl'];
                $hitungprofitrp = $row->netto_cust - $row->fee1_rp - $row->fee2_rp - $row->fee3_rp - $vhargamodal - $vjumlah_netto;
                $hitungprofitpr = ($row->netmargin_rp != 0) ? ($hitungprofitrp / $row->netmargin_rp) * 100 : 0;
                    $dataupdate = [
                        'kode_spl' => $data['kode_spl'],
                        'bruto_spl' => $data['bruto_spl'],
                        'disc_spl' => $data['disc_spl'],
                        'netto_spl' => $data['netto_spl'],
                        'hargamodal_spl' => $vjumlah_netto,
                        'profit_pr' => $hitungprofitpr,
                        'profit_rp' => $hitungprofitrp
                    ];
                $result = $builder->where(['kodeitem' => $kodeitem, 'kode_customer' => $row->kode_customer, 'cabang' => $row->cabang])->update($dataupdate);

            }
        }
        
        return $result;
    }
	
	public function revisidata($cabang){
        error_reporting(0);
        $db = db_connect();
        $builder = $db->table('inv_barang_harga');
        $dbbarang = $db->table('inv_barang');
        $DataHargaSupplier = $builder->get()->getResult();
        
        try {
            $db->transStart();
            foreach($DataHargaSupplier as $row){
                if($row->kodeitem  > 'KB.000172 '){
                    $DataBarang = $dbbarang->getWhere(['kdcbg' => '01', 'kodeitem' => $row->kodeitem])->getRow();
                    if($row->kode_spl == $DataBarang->kode_spl1){
                        $vjumlah_netto = $DataBarang->netto_spl1;
                    }
                    else if($row->kode_spl == $DataBarang->kode_spl2){
                        $vjumlah_netto = $DataBarang->netto_spl2;
                    }
                    else if($row->kode_spl == $DataBarang->kode_spl3){
                        $vjumlah_netto = $DataBarang->netto_spl3;
                    }
                    else if($row->kode_spl == $DataBarang->kode_spl4){
                        $vjumlah_netto = $DataBarang->netto_spl4;
                    }
                    else{
                        $vjumlah_netto =  $row->hargamodal_spl;
                    }
                        
                    $hitungprofitrp = $row->netto_cust - $row->fee1_rp - $row->fee2_rp - $row->fee3_rp - $vjumlah_netto;
                    $hitungprofitpr = ($row->netmargin_rp != 0) ? round(($hitungprofitrp / $row->netmargin_rp) * 100) : 0;
                    if ($row->profit_pr != $hitungprofitpr || $row->profit_rp != $hitungprofitrp) {
                        $dataupdate = [
                            'profit_pr' => $hitungprofitpr,
                            'profit_rp' => $hitungprofitrp
                        ];
                        $result = $builder->where(['kodeitem' => $row->kodeitem, 'kode_customer' => $row->kode_customer, 'cabang' => $row->cabang])->update($dataupdate);

                    }
                }
            }
            $db->transComplete();
            if ($db->transStatus() === false) {
                // Jika terjadi kesalahan, batalkan transaksi
                 //return $this->response->setJSON(['status' => 'error', 'message' => 'Data Gagal di revisi']);
                echo json_encode('Data Gagal di validasi');
                $db->transRollback();
            } else {
                echo json_encode('Data Berhasil Di Validasi');
                  //return $this->response->setJSON(['status' => 'success', 'message' => 'Data Berhasil Di revisi']);
            }
            // Transaksi sukses, commit perubahan ke database
            $db->transCommit();
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }

    }

    public function harga_barang_table()
    {
        $request = Services::request();
        $cekdata = $request->getPost('draw');
        $cabang = $request->getPost('cabang');
        $cari = $request->getPost('search')['value'];
        $datatable = new Harga_barang_model($request);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $level = $session->get('level');

        $akses = $model_Global->cek_menu_akses($level, 518);
        $lists = $datatable->getDatatables();
        $data = [];
        foreach ($lists as $value) {
            $DataBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $value->kodeitem])->getRow();
            if ($akses->ueditpercust == 1) {
                //$RowPerCust = '<button class="btn btn-warning me-2 mb-2" onclick="editPerCust('."'".$value->kodeitem."'".','."'".$value->kode_customer."'".','."'".$value->cabang."'".','."'".$value->gudang."'".')"><i class="fa-solid fa-edit text-white"></i></button>';	
                $RowPerCust = '<a href="' . base_url('inventory/datamaster/hargabarang/editpercust/' . $value->kodeitem . '/' . $value->kode_customer . '/' . $value->cabang . '/' . $value->gudang) . '" class="btn btn-warning"><i class="fa-solid fa-edit text-white"></i></a>';
            } else {
                $RowPerCust = '';
            }
            if ($akses->uadd == 1 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('inventory/datamaster/hargabarang/entri/' . $value->id_harga) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $del = ' <button class="btn btn-danger" onclick="data_delete(' . $value->id_harga . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else if ($akses->uedit == 1 && $akses->udel == 0) {
                $editview = '<a href="' . base_url('inventory/datamaster/hargabarang/entri/' . $value->id_harga) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $del = '';
            } else if ($akses->uedit == 0 && $akses->udel == 1) {
                $editview = '';
                $del = ' <button class="btn btn-danger" onclick="data_delete(' . $value->id_harga . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else {
                $editview = '';
                $del = '';
            }
            if ($akses->uharga == 1) {
                $brutocs = number_format($value->bruto_cust);
                $disc = $value->diskon_pr . '%';
                $netto = number_format($value->hargajual_net);
            } else {
                $brutocs = '0';
                $disc = '0%';
                $netto = '0';
            }

            if ($akses->uhargaprofit == 1) {
                $fee1 = $value->fee1_pr . '%';
                $fee2 = $value->fee2_pr . '%';
                $fee3 = $value->fee3_pr . '%';
                $profit = number_format($value->profit_pr) . '%';
            } else {
                $fee1 = '0%';
                $fee2 = '0%';
                $fee3 = '0%';
                $profit = '0%';
            }

            $data[] = [
                $value->kodeitem,
                $value->nama_barang,
                $DataBarang->satuan,
                $value->nama_customer,
                $brutocs,
                $disc,
                $netto,
                $fee1,
                $fee2,
                $fee3,
                $profit,
                $RowPerCust,
                $editview,
                $del
            ];
        }

        $output = [
            'draw' => $request->getPost('draw'),
            'recordsTotal' => $datatable->countAll(),
            'recordsFiltered' => $datatable->countFiltered(),
            'data' => $data
        ];

        echo json_encode($output);
    }

    public function harga_barang_edit($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_barang_harga')->where('id_harga', $id)->get()->getRow();
        echo json_encode($builder);
    }

    public function vieweditpercust($brg, $cust, $cbg, $gudang)
    {
        $session = session();
        $db = db_connect();
        $data = [];

        $ResInvBarangHarga = $db->table('inv_barang_harga')->getWhere(['kodeitem' => $brg, 'kode_customer' => $cust, 'cabang' => $cbg])->getResult();
        $rowGudang = $db->table('inv_gudang')->getWhere(['kode' => $gudang])->getRow();
        $rowCust = $db->table('ar_customer')->getWhere(['kode' => $cust])->getRow();

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Harga Barang Per Customer',
            'body' => 'admin/inventory/data_master/edit-harga-percust',
            'RowGudang' => $rowGudang,
            'RowCustomer' => $rowCust,
            'cust' => $cust,
            'gudang' => $gudang,
            'RowCabang' => $cbg,
        ];
        return view('layout/layout', $data);
    }

    public function saveeditpercust()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $db->transStart();

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 518);
        $input = $this->request->getPost();

        if ($akses->ueditpercust == 1) {
            $gudang = $input['gudang'];
            $filcab = $db->query("SELECT * from inv_gudang where kode = '$gudang'")->getRow();
            $cabang = $filcab->cabang;
            $kode = $input['kode'];
            $bruto_cust = $input['bruto'];
            $disc_cust = $input['disc'];
            $netto_cust = $input['netto'];
            $fee1 = $input['fee1'];
            $bagian1 = $input['bagian1'];
            $fee1rp = $input['fee1rp'];
            $fee2 = $input['fee2'];
            $bagian2 = $input['bagian2'];
            $fee2rp = $input['fee2rp'];
            $fee3 = $input['fee3'];
            $bagian3 = $input['bagian3'];
            $fee3rp = $input['fee3rp'];
            $profit_pr = $input['Profit'];
            $profit_rp = $input['profitrp'];
            $netmargin_rp = $input['netmarginrp'];
            $tanggal = $input['tanggal'];
            $cust   = $input['kodecust'];
            $jml_netto_supp   = $input['jumlah_netto'];

            $_jml_netto_supp  =  str_replace(',', '', $jml_netto_supp);

            $jumdata  = count($kode);
            $_tanggal = date('Y-m-d', strtotime($tanggal));

            for ($i = 0; $i < $jumdata; $i++) {
                $_kode   = $kode[$i];
                $_cust   = $cust;
                $_bruto_cust  =  str_replace(',', '', $bruto_cust[$i]);
                $_disc_cust   =  str_replace(',', '', $disc_cust[$i]);
                $_netto_cust   =  str_replace(',', '', $netto_cust[$i]);
                $_fee1   =  str_replace(',', '', $fee1[$i]);
                $_fee1rp   =  str_replace(',', '', $fee1rp[$i]);
                $_fee2   =  str_replace(',', '', $fee2[$i]);
                $_fee2rp   =  str_replace(',', '', $fee2rp[$i]);
                $_fee3   =  str_replace(',', '', $fee3[$i]);
                $_fee3rp   =  str_replace(',', '', $fee3rp[$i]);
                $_profitpr   =  str_replace(',', '', $profit_pr[$i]);
                $_profitrp   =  str_replace(',', '', $profit_rp[$i]);
                $_marginrp   =  str_replace(',', '', $netmargin_rp[$i]);

                if ($_kode != "") {
                    $data = array(
                        'bruto_cust' => $_bruto_cust,
                        'hargamodal_spl' => $_jml_netto_supp,
                        'netto_cust' => $_netto_cust,
                        'diskon_pr' => $_disc_cust,
                        'diskon_rp' => $_bruto_cust * ($_disc_cust / 100),
                        'hargajual_net' => $_netto_cust,
                        'fee1_pr' => $_fee1,
                        'bagian1' => $bagian1[$i],
                        'fee1_rp' => $_fee1rp,
                        'fee2_pr' => $_fee2,
                        'bagian2' => $bagian2[$i],
                        'fee2_rp' => $_fee2rp,
                        'fee3_pr' => $_fee3,
                        'bagian3' => $bagian3[$i],
                        'fee3_rp' => $_fee3rp,
                        'profit_pr' => $_profitpr,
                        'profit_rp' => $_profitrp,
                        'netmargin_rp' => $_marginrp,
                        'tanggal' => $_tanggal,
                        'cabang' => $cabang,
                        'gudang' => $gudang,
                        'tglrekam' => date('Y-m-d H:i:s'),
                        'userid' => $session->get('username'),
                    );
                    $builder = $db->table("inv_barang_harga");
                    $builder->where(['kode_customer' => $_cust, 'kodeitem' => $_kode, 'cabang' => $cabang])->update($data);
                }
            }

            echo json_encode(array("status" => TRUE));
        } else {
            echo json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function harga_barang_delete($id)
    {
        $db = db_connect();
        $builder = $db->table('inv_barang_harga');
        $delete = $builder->delete(['id_harga' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }

    //***********STOCK OPNAME Barang Page Start Here*************//
    //Stok Opname Page Start Here
    public function stok_opname()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $aksesModul = $model_Global->akses_modul($level);
        $profileidentity = $model_Global->_LoadProfile();
        if($aksesModul->nomor == 1 || $aksesModul->nomor == 8){
            $AksesPraSO = true;
        }else{
            $AksesPraSO = false;
        }
        if($cabang == '01'){
            $profile = $profileidentity->setpraso;
        }else{
            $profile = $profileidentity->setprasocbg;
        }
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Stok Opname',
            'body' => 'admin/inventory/transaksi/stock-opname',
            'cabang' => $cabang,
			'AksesPraSO' => $AksesPraSO,
            'setpraso' => $profile
        ];
        return view('layout/layout', $data);
    }

    public function stok_opname_table($tgl1)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $db = db_connect();
        $query = "SELECT * FROM inv_stockopname where cabang='$cabang'";
        if ($tgl1 != "1") {
            $query .= " and tgl='$tgl1'";
        } else {
            $RowLast = $db->query("SELECT * FROM `inv_stockopname` where cabang='$cabang' ORDER BY id DESC LIMIT 1")->getRow();
            $query .= " and tgl='$RowLast->tgl'";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        foreach ($res as $value) {
            $akses = $model_Global->cek_menu_akses($level, 530);
            $count++;
            $data[] = [
                $count,
                $value->nama_gudang,
                date('d-m-y', strtotime($value->tglentry)),
                $value->kodeitem,
                $value->namabarang,
                $value->sat,
                $value->qty_opname,
                $akses->udel == 1 ? '<button class="btn btn-danger" onclick="delete_data(' . "'" . $value->kode_so . "'" . ')"><i class="fa-solid fa-trash text-white"></i> Hapus</button>' : ''
            ];
        }
        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function praso()
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $db->transStart();
        $db->query("DELETE from inv_nobatchso where cabang='$cabang'");
        $db->query("update inv_nobatch set qty_saldo=0 where qty_saldo < 0 and cabang='$cabang'");
        $db->query("INSERT into inv_nobatchso (no_batch, kd_jenis, kodeitem, qty_terima, qty_jual, qty_saldo, gudang, cabang, tanggal_exp)
            SELECT no_batch, kd_jenis, kodeitem, qty_terima, qty_jual, qty_saldo, gudang, cabang, tanggal_exp FROM inv_nobatch where cabang='$cabang'");
        $db->query("update inv_barang set stok_so = stok where kodeitem=kodeitem and kdcbg='$cabang' ");
        $db->query("update inv_barang_gudang set qty = 0 where kodeitem=kodeitem and cabang='$cabang'");
        $db->query("update inv_nobatch set qty_terima=0 , qty_jual=0 , qty_saldo=0 where cabang='$cabang'");
        $db->query("update inv_barang set stok=0 where kdcbg='$cabang'");
		if($cabang == '01'){
            $db->query("update ms_identity set setpraso=1 ");
        }else{
            $db->query("update ms_identity set setprasocbg=1 ");
        }
        $db->transComplete();
        echo json_encode($cabang);
    }

    public function pascaso()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        //$db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 530);
        // Table
        $builder            = $db->table('inv_stockopname');
        $builderDetail      = $db->table('inv_sodetail');
        $builderGudang      = $db->table('inv_gudang');
        $INVBarangBatch     = $db->table('inv_nobatch');
        $INVtransaksi       = $db->table('inv_transaksi');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            date_default_timezone_set('Asia/Jakarta');
            $nomorbukti = $model_Global->_Autonomor('SOB');
            $userid   = $session->get('username');
            $uid   = $session->get('cabang');
            $tglNow    = date('Y-m-d');
            $tanggal   = date('Y-m-d', strtotime($tglNow));

            $NotInKode = $db->query("SELECT *
										FROM inv_barang a
										WHERE kdcbg='$uid' and kodeitem NOT IN
											(SELECT kodeitem 
											FROM inv_stockopname b WHERE tgl='$tanggal'AND cabang='$uid') ORDER BY namabarang")->getResult();

            $no_auto = $builder->getWhere(['kode_so' => $nomorbukti, 'cabang' => $uid])->getRow();
            if (empty($no_auto)) {
                $nobukti = $nomorbukti;
            } else {
                $nobukti = $model_Global->_Autonomor('SOB');
            }

            foreach ($NotInKode as $row) {
                $data_gudang = $builderGudang->getWhere(['kode' => $row->kdgudang, 'cabang' => $uid])->getRow();

                $data_detail = array(
                    'kode_so' => $nobukti,
                    'kodeitem' => $row->kodeitem,
                    'no_batch'   => '-',
                    'pic' => $session->get('username'),
                    'gudang' => $row->kdgudang,
                    'cabang' => $uid,
                    'qty_stok' => '0',
                    'qty_adjust' => 0,
                    'tanggal' => date('Y-m-d', strtotime($tanggal)),
                );

                $data_batch = array(
                    'no_batch'   => '-',
                    'kd_jenis'   => 'Stok Opname',
                    'kodeitem' => $row->kodeitem,
                    'qty_terima' => '0',
                    'qty_saldo' => '0',
                    'gudang' => $row->kdgudang,
                    'cabang' => $uid,
                );

                $datad = array(
                    'kode_so' => $nobukti,
                    'tgl' => date('Y-m-d', strtotime($tanggal)),
                    'kodeitem' => $row->kodeitem,
                    'namabarang' => $row->namabarang,
                    'gudang' => $row->kdgudang,
                    'cabang' => $uid,
                    'nama_gudang' => $data_gudang->nama,
                    'qty_opname' => '0',
                    'sat' => $row->satuan,
                    'hpp' => $row->hargabeli,
                    'qty' => $row->stok_so,
                    'selisih' => $row->stok_so - '0',
                    'kodeuser' => $session->get('username'),
                    'pic' => $session->get('username'),
                    'tglentry' => date('Y-m-d H:i:s', strtotime($tanggal)),
                );
                $DataTransaksi = array(
                    'tanggal' => date('Y-m-d', strtotime($tanggal)),
                    'kodeitem' => $row->kodeitem,
                    'jenis' => 'Stock Opname',
                    'penerimaan' => '0',
                    'pengeluaran' => '0',
                    'so' => '0',
                    'userid' => $session->get('username'),
                    'hpp' => $row->hargabeli,
                    'hargajual' => '0',
                    'nobukti' => $nobukti,
                    'satuan' => $row->satuan,
                    'tglrekam' => date('Y-m-d H:i:s', strtotime($tanggal)),
                    'gudang' => $row->kdgudang,
                    'cabang' => $uid,
                );

                $view_batch = $INVBarangBatch->getWhere($data_batch)->getRow();
                if ($row->kodeitem != "") {
                    if ($view_batch != "") {
                        $INVBarangBatch->where($data_batch)->update($data_batch);
                    } else {
                        $INVBarangBatch->insert($data_batch);
                    }
                    $builderDetail->insert($data_detail);

                    $so_detil = $db->query('SELECT *,sum(qty_stok) as tot from inv_sodetail 
				  								where kodeitem="' . $row->kodeitem . '" and tanggal="' . $tanggal . '" 
												and gudang="' . $row->kdgudang . '" and cabang="' . $uid . '"')->getRow();

                    $db->query("UPDATE inv_barang set stok = '$so_detil->tot' where kodeitem = '$row->kodeitem' and kdgudang='$row->kdgudang' and kdcbg='$uid'");

                    $stok_gudang = $db->query("SELECT kodeitem from inv_barang_gudang where kodeitem = '$row->kodeitem' and gudang='$row->kdgudang'  and cabang='$uid' ")->getRow();
                    if (!empty($stok_gudang->kodeitem)) {
                        $db->query("UPDATE inv_barang_gudang set qty = '$so_detil->tot' where kodeitem='$row->kodeitem' and gudang = '$row->kdgudang' and cabang='$uid'");
                    } else {
                        $db->query("INSERT into inv_barang_gudang(kodeitem, gudang, qty, cabang) values('$row->kodeitem', '$row->kdgudang', '$so_detil->tot', '$uid')");
                    }
                    $builder->insert($datad);
                    $INVtransaksi->insert($DataTransaksi);
                    $model_Global->_updatecounter1('SOB');
                }
            }
			if($uid == '01'){
                $db->query("update ms_identity set setpraso=0 ");
            }else{
                $db->query("update ms_identity set setprasocbg=0 ");
            }
            //$db->transComplete();
            echo json_encode(array("status" => TRUE));
        } else {
            header('location:' . base_url());
        }
    }

    public function stok_opname_entri($id = '')
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $data = [];
        if ($id > 0) {
            $data       = $db->table('inv_stockopname')->where('id', $id)->get()->getRow();
            $DetailItem = $db->table('inv_sodetail')->where(['kode_so' => $data->kode_so])->get()->getResult();
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
            'nomor' => $model_Global->_Autonomor('SOB'),
            'gudang' => $gudang,
            'arCust' => $arCust,
            'res' => $res,
            'css' => $css,
            'js' => $js,
            'title' => 'Stok Opname',
            'body' => 'admin/inventory/transaksi/stock-opname-entri',
        ];
        return view('layout/layout', $data);
    }

    public function SO_ShowBarang($kode, $gudang)
    {
        $db = db_connect();
        $session = session();
        $data = $db->table("inv_barang")->where(['kodeitem' => $kode, 'kdcbg' => $session->get('cabang'), 'kdgudang' => $gudang])->get()->getRow();
        echo json_encode($data);
    }

    public function SO_Save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $input = $this->request->getPost();
        $param = $input['id'];
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 530);
        // Table
        $builder         = $db->table('inv_stockopname');
        $builderDetail   = $db->table('inv_sodetail');
        $buildGudang     = $db->table('inv_gudang');
        $buildBatch      = $db->table('inv_nobatch');
        $buildBarang     = $db->table('inv_barang');
        $buildBarangGudang  = $db->table('inv_barang_gudang');
        $buildTransaksi  = $db->table('inv_transaksi');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang   = $session->get('cabang');
            $pic      = $input['pic'];
            $tanggal  = $input['tanggal'];
            $kode  = $input['barang'];
            $no_batch  = $input['no_batch'];
            $tgl_exp  = $input['tgl_exp'];
            $qty   = $input['qty'];
            $total_qty   = $input['total'];
            $gudang  = $input['gudang'];
            $tglNow    = date('Y-m-d');

            $no_auto = $builder->getWhere(['kode_so' => $input['nomorbukti'], 'cabang' => $session->get('cabang')])->getRow();

            if (empty($no_auto)) {
                $nobukti = $input['nomorbukti'];
            } else {
                $nobukti = $model_Global->_Autonomor('SOB');
            }

            $data_gudang = $buildGudang->getWhere(['kode' => $input['gudang']])->getRow();

            $ViewSoBarang = $builder->getWhere(
                [
                    'kodeitem' => $input['barang'],
                    'tgl' => date('Y-m-d', strtotime($input['tanggal'])),
                    'gudang' => $input['gudang'],
                    'cabang' => $session->get('cabang')
                ]
            )->getRow();
            $ViewSoBarang != null ? $kodeSo = $ViewSoBarang->kode_so : $kodeSo = '';


            $jumdata  = count($no_batch);

            for ($i = 0; $i < $jumdata; $i++) {
                $_no_batch   = $no_batch[$i];

                $data_detail = array(
                    'kodeitem' => $input['barang'],
                    'no_batch'   => $no_batch[$i],
                    'tanggal_exp' => $tgl_exp[$i],
                    'pic' => $input['pic'],
                    'gudang' => $input['gudang'],
                    'cabang' => $session->get('cabang'),
                    'qty_stok' => $qty[$i],
                    'qty_adjust' => 0,
                    'tanggal' => date('Y-m-d H:i:s'),
                );

                $data_batch = array(
                    'no_batch'   => $no_batch[$i],
                    'kd_jenis'   => 'Stok Opname',
                    'kodeitem' => $input['barang'],
                    'qty_terima' => $qty[$i],
                    'qty_saldo' => $qty[$i],
                    'gudang' => $input['gudang'],
                    'cabang' => $session->get('cabang'),
                    'tanggal_exp' => $tgl_exp[$i],
                );

                $view_batch = $buildBatch->getWhere(['no_batch' => $no_batch[$i], 'kodeitem' => $input['barang'], 'gudang' => $input['gudang'], 'cabang' => $session->get('cabang')])->getRow();
                $viewbatchSo = $builderDetail->getWhere(['no_batch' => $no_batch[$i], 'kodeitem' => $input['barang'], 'gudang' => $input['gudang'], 'cabang' => $session->get('cabang'), 'tanggal' => date('Y-m-d'), 'kode_so' => $kodeSo])->getRow();
                if ($input['barang'] != "") {
                    if ($param == 0) {
                        if (!empty($ViewSoBarang)) {
                            if (!empty($viewbatchSo)) {
                                $db->query("UPDATE inv_sodetail set qty_stok= qty_stok + '$qty[$i]'  
													where kodeitem = '$kode' and no_batch = '$no_batch[$i]' 
													and kode_so='$kodeSo' and gudang='$gudang' and cabang='$cabang'");
                            } else {
                                $data_detail['kode_so'] = $kodeSo;
                                $builderDetail->insert($data_detail);
                            }

                            if (!empty($view_batch)) {
                                $db->query("UPDATE inv_nobatch 
													set kd_jenis='Stok Opname', qty_terima = qty_terima +'$qty[$i]', 
													qty_saldo = qty_saldo + '$qty[$i]', tanggal_exp = '$tgl_exp[$i]'  
													where kodeitem = '$kode' and no_batch = '$no_batch[$i]'
													and gudang='$gudang' and cabang='$cabang'");
                            } else {
                                $buildBatch->insert($data_batch);
                            }
                        } else {
                            if (empty($view_batch)) {
                                $buildBatch->insert($data_batch);
                            } else {
                                $db->query("UPDATE inv_nobatch 
													set kd_jenis='Stok Opname', qty_terima = qty_terima +'$qty[$i]', 
													qty_saldo =qty_saldo + '$qty[$i]', tanggal_exp = '$tgl_exp[$i]'  
													where kodeitem = '$kode' and no_batch = '$no_batch[$i]'
													and gudang='$gudang' and cabang='$cabang'");
                            }
                            $data_detail['kode_so'] = $nobukti;
                            $builderDetail->insert($data_detail);
                        }
                    } else {
                        $builderDetail->where(['kode_so' => $input['nomorbukti'], 'no_batch' => $no_batch[$i], 'kodeitem' => $kode])->update($data_detail);
                        $db->query("UPDATE inv_nobatch set qty_terima = qty_terima + '$qty[$i]', qty_saldo = qty_saldo + '$qty[$i]', 
                                    tanggal_exp = '$tgl_exp[$i]'  where kodeitem = '$kode' and no_batch = '$_no_batch'");

                        $db->query("UPDATE inv_barang_supplier set qty_masuk = qty_masuk + '$qty[$i]', stok = stok +'$qty[$i]', tgl_exp = '$tgl_exp[$i]'  
                                    where kodeitem = '$kode' and no_batch = '$_no_batch'");
                        if (empty($view_batch)) {
                            $buildBatch->insert($data_batch);
                        }
                    }
                    $so_detil = $db->query("SELECT *,sum(qty_stok) as tot from inv_sodetail 
				  								where kodeitem='$kode' and tanggal='$tglNow' 
												and gudang='$gudang' and cabang='$cabang'")->getRow();

                    $db->query("UPDATE inv_barang set stok = '$so_detil->tot' where kodeitem = '$kode' and kdgudang='$gudang' and kdcbg='$cabang'");


                    $stok_gudang = $buildBarangGudang->getWhere(['kodeitem' => $kode, 'gudang' => $gudang, 'cabang' => $cabang])->getRow();
                    if (!empty($stok_gudang->kodeitem)) {
                        $db->query("UPDATE inv_barang_gudang set qty = '$so_detil->tot' where kodeitem='$kode' and gudang = '$gudang' and cabang='$cabang'");
                    } else {
                        $gudangInsert = array(
                            'kodeitem' => $kode, 'gudang' => $gudang,
                            'qty' => $so_detil->tot, 'cabang' => $cabang
                        );
                        $buildBarangGudang->insert($gudangInsert);
                    }
                }
            }

            $barang = $buildBarang->getWhere(['kodeitem' => $kode, 'kdgudang' => $gudang, 'kdcbg' => $cabang])->getRow();

            $datad = array(
                'kode_so' => $nobukti,
                'tgl' => date('Y-m-d', strtotime($input['tanggal'])),
                'kodeitem' => $kode,
                'namabarang' => $barang->namabarang,
                'gudang' => $gudang,
                'cabang' => $cabang,
                'nama_gudang' => $data_gudang->nama,
                'qty_opname' => $so_detil->tot,
                'sat' => $barang->satuan,
                'hpp' => $barang->hargabeli,
                'qty' => $barang->stok_so,
                'selisih' => $barang->stok_so - $so_detil->tot,
                'kodeuser' => $session->get('username'),
                'pic' => $pic,
                'tglentry' => date('Y-m-d H:i:s'),
            );
            $DataTransaksi = array(
                'tanggal' => date('Y-m-d', strtotime($tanggal)),
                'kodeitem' => $kode,
                'jenis' => 'Stock Opname',
                'penerimaan' => '0',
                'pengeluaran' => '0',
                'userid' => $session->get('username'),
                'hpp' => $barang->hargabeli,
                'hargajual' => '0',
                'nobukti' => $nobukti,
                'satuan' => $barang->satuan,
                'tglrekam' => date('Y-m-d H:i:s'),
                'gudang' => $gudang,
                'cabang' => $cabang,
            );


            if ($kode != "") {
                if ($param == 0) {
                    if (!empty($ViewSoBarang)) {
                        $datad['qty_opname'] = $ViewSoBarang->qty_opname + $total_qty;
                        $builder->where(['kodeitem' => $kode, 'gudang' => $gudang, 'cabang' => $cabang, 'kode_so' => $kodeSo])->update($datad);
                        $DataTransaksi['so'] = $ViewSoBarang->qty_opname + $total_qty;
                        $buildTransaksi->where(['kodeitem' => $kode, 'gudang' => $gudang, 'cabang' => $cabang, 'nobukti' => $kodeSo])->update($DataTransaksi);
                        echo json_encode($kodeSo);
                    } else {
                        $datad['kode_so'] = $nobukti;
                        $datad['qty_opname'] = $so_detil->tot;
                        $builder->insert($datad);
                        $DataTransaksi['nobukti'] = $nobukti;
                        $DataTransaksi['so'] = $so_detil->tot;
                        $buildTransaksi->insert($DataTransaksi);
                        $model_Global->_updatecounter1('SOB');
                        echo json_encode($nobukti);
                    }
                } else {
                    $builder->where(['kodeitem' => $kode, 'gudang' => $gudang, 'cabang' => $cabang, 'kode_so' => $input['nomorbukti']])->update($datad);
                    echo json_encode($input['nomorbukti']);
                }
            }
        } else {
            header('location:' . base_url());
        }
        $db->transComplete();
    }

    public function delso($id)
    {
        $db = db_connect();
        $db->transStart();
        $getSoDetail     = $db->query("SELECT * from inv_sodetail where kode_so = '$id'")->getResult();
        foreach ($getSoDetail as $row) {
            $db->table("inv_transaksi")->delete(['nobukti' => $id]);
            $db->query("UPDATE inv_barang set stok = stok - '$row->qty_stok' where kodeitem='$row->kodeitem' and kdcbg='$row->cabang' and kdgudang='$row->gudang'");
            $db->query("UPDATE inv_barang_gudang set qty = qty - '$row->qty_stok' where kodeitem='$row->kodeitem' and cabang='$row->cabang' and gudang='$row->gudang'");
            $db->query("update inv_nobatch set qty_terima = qty_terima - '$row->qty_stok', qty_saldo = qty_saldo - '$row->qty_stok' where kodeitem='$row->kodeitem' and cabang='$row->cabang' and gudang='$row->gudang' and no_batch='$row->no_batch'");
        }

        $datadel =  $db->table("inv_stockopname")->delete(['kode_so' => $id]);
        $db->table("inv_sodetail")->delete(['kode_so' => $id]);
        echo json_encode($datadel);
        $db->transComplete();
    }

    public function cetakformulir($cabang, $tgl)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $cek = $session->get('level');
        if (!empty($cek)) {
            $query = "SELECT a.kodeitem, a.namabarang, a.stok, a.satuan, b.kodeitem, b.qty_saldo , b.no_batch 
            FROM inv_barang a INNER JOIN inv_nobatch b ON a.kodeitem = b.kodeitem";
            if ($cabang != '0') {
                $dataUnit = $db->table("ms_unit")->where(['kode' => $cabang])->get()->getRow();
                $namaCabang = $dataUnit->nama;
                $query .= " WHERE a.kdcbg = '$cabang' AND b.cabang ='$cabang' ";
            } else {
                $namaCabang = 'All';
            }
            $query .= " GROUP BY a.kodeitem, b.no_batch ORDER BY a.namabarang";
            $data = $db->query($query)->getResult();


            $pdf = new simkeu_nota_helper();
            $pdf->setunit('');
            $pdf->setjudul('DAFTAR STOK OPNAME');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->setfont('Times', 'B', 10);
            $pdf->SetWidths(array(30, 5, 40, 35, 30, 5, 30));
            $pdf->SetAligns(array('L', 'C', 'L', 'C', 'L', 'C', 'L'));
            $judul0 = array('Cabang', ':', $namaCabang, '', 'Tanggal', ':', $tgl);
            $border = array('', '', '', '', '', '', '');
            $align = array('L', 'C', 'L', 'C', 'L', 'C', 'L');
            $pdf->FancyRow($judul0, $border, $align);

            $pdf->ln(10);
            $pdf->SetWidths(array(10, 75, 15, 15, 40, 35));
            $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
            $judul = array('No.', 'Nama Item', 'Qty', 'Satuan', 'Batch', 'Expired');

            $pdf->row($judul);
            $pdf->setheadertable($judul);
            $pdf->SetWidths(array(10, 75, 15, 15, 40, 35));
            $pdf->SetAligns(array('C', 'L', 'C', 'C', 'C', 'C'));
            $pdf->setfont('Times', '', 10);
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');

            $nourut = 1;

            foreach ($data as $db) {
                $qty_saldo = $db->qty_saldo;
                $stok = $db->stok;

                // if ($qty_saldo < 1 && $stok < 1) {
                    $namabarang = $db->namabarang;
                    $satuan = $db->satuan;
                    $qty = '';
                    $batch =$db->no_batch ;
                    $exp = '';
                    $pdf->row(array($nourut, $namabarang, $qty, $satuan, $batch, $exp));
                // }
                // if ($qty_saldo > 0 && $stok > 0) {
                //     $namabarang = $db->namabarang;
                //     $satuan = $db->satuan;
                //     $qty = '';
                //     $batch = '';
                //     $exp = '';
                //     $pdf->row(array($nourut, $namabarang, $qty, $satuan, $batch, $exp));
                // }

                $nourut++;
            }

            $pdf->ln(10);
            $pdf->SetWidths(array(40, 40, 30, 40, 40));
            $border = array('', '', '', '', '');
            $align = array('C', 'C', 'C', 'C', 'C');
            $foot1 = array('', 'PIC', '', 'Saksi', '');
            $foot2 = array('', '', '', '', '');

            $pdf->FancyRow($foot1, $border, $align);
            $pdf->ln(10);
            $border = array('', 'B', '', 'B', '');
            $pdf->FancyRow($foot2, $border, $align);


            $pdf->AliasNbPages();
            $pdf->output('FORMULIRSO.PDF', 'I');
            exit;
        } else {
            header('location:' . base_url());
        }
    }

    public function hasilso($cabang, $tgl)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $cek = $session->get('level');
        if (!empty($cek)) {

            $_tanggal = date('Y-m-d', strtotime($tgl));
            $query = "SELECT a.*, b.namabarang, b.stok_so from inv_stockopname a inner join inv_barang b on a.kodeitem = b.kodeitem where tgl='$_tanggal'";
            if ($cabang != '0') {
                $dataUnit = $db->table("ms_unit")->where(['kode' => $cabang])->get()->getRow();
                $namaCabang = $dataUnit->nama;
                $query .= " and a.cabang='$cabang' and b.kdcbg='$cabang'";
            } else {
                $namaCabang = 'All';
            }
            $query .= " order by a.kodeitem asc";

            $npic = $db->table("inv_stockopname")->where(['cabang' => $cabang, 'tgl' => $_tanggal])->get()->getRow();

            $data = $db->query($query)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setunit('');
            $pdf->setjudul('HASIL STOK OPNAME');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->setfont('Times', 'B', 10);
            $pdf->SetWidths(array(30, 5, 50, 35, 30, 5, 30));
            $pdf->SetAligns(array('L', 'C', 'L', 'C', 'L', 'C', 'L'));
            $judul0 = array('Cabang', ':', $namaCabang, '', 'Tanggal', ':', $tgl);
            $judul1 = array('PIC', ':', $npic->pic, '', '', '', '');

            $border = array('', '', '', '', '', '', '');
            $align = array('L', 'C', 'L', 'C', 'L', 'C', 'L');
            $pdf->FancyRow($judul0, $border, $align);
            $pdf->FancyRow($judul1, $border, $align);

            $pdf->ln(10);
            $pdf->SetWidths(array(10, 25, 80, 20, 20, 20));
            $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
            $judul = array('No.', 'Kode Item', 'Nama Item', 'Qty', 'Qty Opm', 'Satuan');

            $pdf->row($judul);
            $pdf->SetWidths(array(10, 25, 80, 20, 20, 20));
            $pdf->SetAligns(array('C', 'L', 'L', 'R', 'R', 'C'));
            $pdf->setfont('Times', '', 10);
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');

            $nourut = 1;

            foreach ($data as $db) {
                $pdf->row(array($nourut, $db->kodeitem, $db->namabarang, $db->stok_so, $db->qty_opname, $db->sat));
                $nourut++;
            }


            $pdf->AliasNbPages();
            $pdf->output('HASILSO.PDF', 'I');
            exit;
        } else {
            header('location:' . base_url());
        }
    }

    public function hasilsosel($cabang, $tgl)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $cek = $session->get('level');
        $unit = $session->get('cabang');
        if (!empty($cek)) {
            $_tanggal = date('Y-m-d', strtotime($tgl));

            $query = "SELECT a.*, sum(a.qty_opname) as total, b.namabarang, b.stok_so, b.hargabeli 
                    from inv_stockopname a inner join inv_barang b on a.kodeitem = b.kodeitem where a.tgl='$_tanggal'";
            if ($cabang != '0') {
                $dataUnit = $db->table("ms_unit")->where(['kode' => $cabang])->get()->getRow();
                $namaCabang = $dataUnit->nama;
                $query .= " and a.cabang='$cabang' and b.kdcbg='$cabang'";
            } else {
                $namaCabang = 'All';
            }
            $query .= " group by a.kodeitem, a.cabang order by a.namabarang asc";

            $data = $db->query($query)->getResult();

            $npic = $db->table("inv_stockopname")->where(['cabang' => $cabang, 'tgl' => $_tanggal])->get()->getRow();

            $pdf = new simkeu_nota_helper();
            $pdf->setunit('');
            $pdf->setjudul('HASIL STOCK OPNAME SELISIH');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->setfont('Times', 'B', 9);
            $pdf->SetWidths(array(30, 5, 40, 35, 30, 5, 30));
            $pdf->SetAligns(array('L', 'C', 'L', 'C', 'L', 'C', 'L'));
            $judul0 = array('Cabang', ':', $namaCabang, '', 'Tanggal', ':', $tgl);
            $judul1 = array('PIC', ':', $npic->pic, '', '', '', '');

            $border = array('', '', '', '', '', '', '', '');
            $align = array('L', 'C', 'L', 'C', 'L', 'C', 'L', 'L');
            $pdf->FancyRow($judul0, $border, $align);
            $pdf->FancyRow($judul1, $border, $align);

            $pdf->ln(5);
            $pdf->SetWidths(array(10, 18, 60, 12, 15, 15, 15, 20, 23));
            $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
            $judul = array('No.', 'Kode Item', 'Nama Item', 'Satuan', 'Qty prog', 'Qty Opm', 'selisih', 'Harga', 'Harga selisih');

            $pdf->row($judul);
            $pdf->SetWidths(array(10, 18, 60, 12, 15, 15, 15, 20, 23));
            $pdf->SetAligns(array('C', 'L', 'L', 'C', 'R', 'R', 'R', 'R', 'R'));
            $pdf->setfont('Times', '', 9);
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');

            $nourut = 1;
            $totqty       = 0;
            $totqtyso     = 0;
            $totqtysel    = 0;
            $tothpp       = 0;
            $tothargasel  = 0;

            foreach ($data as $db) {
                $qty_selisih     = $db->qty_opname - $db->stok_so;
                $harga_selisih    = $db->hargabeli * $qty_selisih;
                $pdf->row(array(
                    $nourut,
                    $db->kodeitem,
                    $db->namabarang,
                    $db->sat,
                    number_format($db->stok_so, 0, '.', ','),
                    number_format($db->qty_opname, 0, '.', ','),
                    number_format($qty_selisih, 0, '.', ','),
                    number_format($db->hargabeli, 0, '.', ','),
                    number_format($harga_selisih, 0, '.', ',')
                ));
                $nourut++;
                $totqty       = $totqty + $db->stok_so;
                $totqtyso     = $totqtyso + $db->qty_opname;
                $totqtysel    = $totqtysel + $qty_selisih;
                $tothpp       = $tothpp + $db->hargabeli;
                $tothargasel  = $tothargasel + $harga_selisih;
            }

            $pdf->SetWidths(array(100, 15, 15, 15, 20, 23));
            $pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R'));
            $pdf->setfont('Times', 'B', 10);
            $pdf->row(array(
                'TOTAL',
                number_format($totqty, 0, ',', '.'),
                number_format($totqtyso, 0, ',', '.'),
                number_format($totqtysel, 0, ',', '.'),
                number_format($tothpp, 0, ',', '.'),
                number_format($tothargasel, 0, ',', '.')
            ));


            $pdf->AliasNbPages();
            $pdf->output('HASILSO_SELISIH.PDF', 'I');
            exit;
        } else {
            header('location:' . base_url());
        }
    }

    //*******************Start line Permohonan Mutasi Gudang************************//
    //Permohonan Mutasi GUDANG Page Start Here
    public function permohonan_mutasi_gudang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/permohonan-mutasi-gudang',
        ];
        return view('layout/layout', $data);
    }

    public function permohonan_mutasi_gudang_table_filter($tgl1, $tgl2)
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $query = "SELECT * FROM inv_mohon_mutasi_gudang where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $db = db_connect();
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 526);
        foreach ($res as $value) {
            if ($value->statusid != '4') {
                $namegudangasal = $db->query("SELECT * FROM inv_gudang where kode='$value->gudang_asal'")->getRow();
                $namegudangtujuan = $db->query("SELECT * FROM inv_gudang where kode='$value->gudang_tujuan'")->getRow();
                if ($value->statusid == '1') {
                    $status = '<span class="label label-md label-warning" style="color:white;"> Menunggu Proses </span>';
                } else if ($value->statusid == '2') {
                    $status = '<span class="label label-md label-success" style="color:white;"> Siap Diterima </span>';
                } else if ($value->statusid == '3') {
                    $status = '<span class="label label-md label-primary" style="color:white;"> Barang Belum Terpenuhi </span>';
                }

                if ($akses->uedit == 1 && $akses->udel == 1 && $value->statusid == '1') {
                    $EditDel = '
                        <a class="btn btn-primary me-2 mb-2" href="permohonanmutasigudangedit/edit/' . $value->idmohon . '/' . $value->gudang_tujuan . '">
                            <i class="fa-solid fa-edit text-white"></i> Edit
                        </a>
                    <button class="btn btn-danger mb-2" onclick="data_delete(' . $value->idmohon . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                } else if ($akses->uedit == 1 && $akses->udel == 0 && $value->statusid == '1') {
                    $EditDel = '
                     <a class="btn btn-primary me-2 mb-2" href="permohonanmutasigudangedit/edit/' . $value->idmohon . '/' . $value->gudang_tujuan . '">
                        <i class="fa-solid fa-edit text-white"></i> Edit
                    </a>';
                } else if ($akses->uedit == 0 && $akses->udel == 1 && $value->statusid == '1') {
                    $EditDel = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->idmohon . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                } else if ($value->statusid == '3') {
                    $EditDel = '
                     <a class="btn btn-info me-2 mb-2" href="permohonanmutasigudang/view/' . $value->idmohon . '/' . $value->gudang_tujuan . '">
                        <i class="fa-solid fa-eye text-white"></i> View
                    </a>';
                } else {
                    $EditDel = '';
                }
                $data[] = [
                    $value->nomohon,
                    $value->tanggal,
                    $namegudangasal->nama,
                    $namegudangtujuan->nama,
                    $value->keterangan,
                    $status,
                    $EditDel
                ];
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function permohonan_mutasi_gudang_entri()
    {
        $session = session();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/permohonan-mutasi-gudang-entri',
            'cabang' => $session->get('cabang'),
            'db' => db_connect()
        ];
        return view('layout/layout', $data);
    }

    public function permohonan_mutasi_gudang_edit($id, $gudang)
    {
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 526);
        if ($akses->uedit == 1) {

            //header
            $builder = $db->table('inv_mohon_mutasi_gudang')->getWhere(['idmohon' => $id, 'gudang_tujuan' => $gudang])->getRow();
            //Detail
            $builderd = "select a.namabarang, a.stok, b.* from inv_barang a
			inner join inv_mohond_mutasi_gudang b on a.kodeitem=b.kodeitem 
			where b.nomohon = '$builder->nomohon' and a.kdgudang='$gudang'";


            $css = [];
            $js = [];
            $data  = [
                'header' => $builder,
                'detail' => $db->query($builderd)->getResult(),
                'jumdata' => $db->query($builderd)->getNumRows(),
                'GudangTujuan' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $gudang])->getResult(),
                'GudangAsal' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $builder->gudang_asal])->getResult(),
                'css' => $css,
                'js' => $js,
                'title' => 'Permohonan Mutasi Gudang',
                'body' => 'admin/inventory/transaksi/permohonan-mutasi-gudang-edit',
                'cabang' => $session->get('cabang')
            ];
            return view('layout/layout', $data);
        }
    }

    public function permohonan_mutasi_gudang_view($id, $gudang)
    {
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 526);
        if ($akses->uedit == 1) {

            //header
            $builder = $db->table('inv_mohon_mutasi_gudang')->getWhere(['idmohon' => $id, 'gudang_tujuan' => $gudang])->getRow();
            //Detail
            $builderd = "select a.namabarang, a.stok, b.* from inv_barang a
			inner join inv_mohond_mutasi_gudang b on a.kodeitem=b.kodeitem 
			where b.nomohon = '$builder->nomohon' and a.kdgudang='$gudang'";


            $css = [];
            $js = [];
            $data  = [
                'header' => $builder,
                'detail' => $db->query($builderd)->getResult(),
                'jumdata' => $db->query($builderd)->getNumRows(),
                'GudangTujuan' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $gudang])->getResult(),
                'GudangAsal' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $builder->gudang_asal])->getResult(),
                'css' => $css,
                'js' => $js,
                'title' => 'Permohonan Mutasi Gudang',
                'body' => 'admin/inventory/transaksi/permohonan-mutasi-gudang-view',
                'cabang' => $session->get('cabang')
            ];
            return view('layout/layout', $data);
        }
    }

    public function permohonan_mutasi_gudang_save($param)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 526);
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $gudang_asal      = $input['gudang_asal'];
            $gudang_tujuan  = $input['gudang_tujuan'];
            $kode              = $input['kode'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];

            $data_header = array(
                'gudang_asal' => $gudang_asal,
                'gudang_tujuan' => $gudang_tujuan,
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'statusid' => '1',
                'noget' => '1',
                'userid' => $session->get('username'),
                'cabang' => $session->get('cabang'),
            );
            $builder = $db->table('inv_mohon_mutasi_gudang');
            $builderD = $db->table('inv_mohond_mutasi_gudang');
            if ($param == 1) {
                $nomorbukti = $model_Global->_Autonomor('MMG');
                $data_header['nomohon'] = $nomorbukti;
                $builder->insert($data_header);
            } else {
                $nomorbukti          = $input['nomorbukti'];
                $builder->where(['nomohon' => $nomorbukti])->update($data_header);
                $builderD->delete(['nomohon' => $nomorbukti]);
            }
            $jumdata  = count($kode);
            for ($i = 0; $i < $jumdata; $i++) {

                $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$kode[$i]' and kdcbg='$cabang' and kdgudang='$gudang_tujuan' ")->getRow();

                $datad = array(
                    'nomohon' => $nomorbukti,
                    'kodeitem' => $kode[$i],
                    'qtymohon' => $qty[$i],
                    'satuan' => $sat[$i],
                    'harga' => $barang->hargabeli,
                    'cabang' => $session->get('cabang'),
                    'gudang' => $gudang_asal,
                    'userid' => $session->get('username'),
                );
                if ($kode[$i] != "") {

                    if ($param == 1) {
                        $builderD->insert($datad);
                    } else {
                        $builderD->insert($datad);
                    }
                }
            }
            if ($param == 1) {
                $model_Global->_updatecounter1('MMG');
            }
            echo json_encode($nomorbukti);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function permohonan_mutasi_gudang_save_view($param)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 526);
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $kode              = $input['kode'];
            $qty               = $input['qtyhitung'];

            $data_header = array(
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
            );
            $builder = $db->table('inv_mohon_mutasi_gudang');
            $builderD = $db->table('inv_mohond_mutasi_gudang');
            if ($param == 1) {
                $nomorbukti          = $input['nomorbukti'];
                $builder->where(['nomohon' => $nomorbukti, 'cabang' => $cabang])->update($data_header);
            }
            $jumdata  = count($kode);
            for ($i = 0; $i < $jumdata; $i++) {

                $datad = array(
                    'qtymohon' => $qty[$i],
                    'userid' => $session->get('username'),
                );
                if ($kode[$i] != "") {

                    if ($param == 1) {
                        $builderD->where(['nomohon' => $nomorbukti, 'cabang' => $cabang, 'kodeitem' => $kode[$i]])->update($datad);
                    }
                }
            }
            if ($param == 1) {
                $data_mohon = $db->query("SELECT SUM(qtymohon) AS qtymohon, SUM(qtyterima) AS qtyterima FROM inv_mohond_mutasi_gudang where nomohon='$nomorbukti' and cabang='$cabang'")->getRow();
                if ($data_mohon->qtyterima >= $data_mohon->qtymohon) {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_gudang set statusid='4', noget='2' where nomohon = '$nomorbukti' and cabang='$cabang'");
                } else {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_gudang set statusid='3' where nomohon = '$nomorbukti' and cabang='$cabang'");
                }
            }
            echo json_encode($nomorbukti);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function Delete_Data_Permohonan_Mutasi_Gudang($id)
    {
        $db = db_connect();
        $db->transStart();
        $builder = $db->table('inv_mohon_mutasi_gudang');
        $result = $builder->getWhere(['idmohon' => $id])->getRow();
        $builderD = $db->table('inv_mohond_mutasi_gudang');
        $delete = $builder->delete(['nomohon' => $result->nomohon]);
        $delete = $builderD->delete(['nomohon' => $result->nomohon]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
        $db->transComplete();
    }

    //*******************Start line Mutasi Gudang************************//
    //Mutasi Gudang Page Start Here
    public function mutasi_gudang()
    {

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/mutasi-gudang',
        ];
        return view('layout/layout', $data);
    }

    public function mutasi_gudang_table($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $query = "SELECT * FROM inv_mutasi_gudang where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 521);
        foreach ($res as $value) {
            $hideDate = date('m-Y', strtotime($value->tanggal));
            if ($hideDate != '02-2023') {
                $namegudangasal = $db->query("SELECT * FROM inv_gudang where kode='$value->gudang_asal'")->getRow();
                $namegudangtujuan = $db->query("SELECT * FROM inv_gudang where kode='$value->gudang_tujuan'")->getRow();
                if ($value->statusid == '1') {
                    $status = '<span class="label label-md label-warning" style="color:white;"> Dikirim </span>';
                } else if ($value->statusid == '2') {
                    $status = '<span class="label label-md label-success" style="color:white;"> Diterima </span>';
                } else if ($value->statusid == '3') {
                    $status = '<span class="label label-md label-primary" style="color:white;"> Masih ada pendingan </span>';
                }

                if ($akses->uedit == 1 && $akses->udel == 1) {
                    $EditDel = '
                        <a class="btn btn-primary me-2 mb-2" href="mutasigudang/edit/' . $value->id_mutasi . '/' . $value->gudang_asal . '">
                            <i class="fa-solid fa-edit text-white"></i> Edit
                        </a>
                    <button class="btn btn-danger mb-2" onclick="data_delete(' . $value->id_mutasi . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                } else if ($akses->uedit == 1 && $akses->udel == 0) {
                    $EditDel = '
                     <a class="btn btn-primary me-2 mb-2" href="mutasigudang/edit/' . $value->id_mutasi . '/' . $value->gudang_asal . '">
                        <i class="fa-solid fa-edit text-white"></i> Edit
                    </a>';
                } else if ($akses->uedit == 0 && $akses->udel == 1) {
                    $EditDel = '<button class="btn btn-danger mb-2" onclick="data_delete(' . $value->id_mutasi . ')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
                } else {
                    $EditDel = '';
                }
                $data[] = [
                    $value->nomormutasi,
                    $value->tanggal,
                    $namegudangasal->nama,
                    $namegudangtujuan->nama,
                    $value->keterangan,
                    $status,
                    $EditDel
                ];
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function mutasi_Gudang_entri()
    {
        $session = session();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/mutasi-gudang-entri',
            'cabang' => $session->get('cabang')
        ];
        return view('layout/layout', $data);
    }

    public function mutasigudangedit($id, $gudang)
    {
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 521);
        if ($akses->uedit == 1) {

            //header
            $builder = $db->table('inv_mutasi_gudang')->getWhere(['id_mutasi' => $id, 'gudang_asal' => $gudang, 'cabang' => $cabang])->getRow();
            //Detail
            $builderd = "SELECT a.namabarang, a.stok, b.* from inv_barang a
			inner join inv_mutasi_gudang_detil b on a.kodeitem=b.kodeitem 
			where b.nomormutasi = '$builder->nomormutasi' and a.kdgudang='$gudang' and a.kdcbg='$cabang'";

            $css = [];
            $js = [];
            $data  = [
                'header' => $builder,
                'detail' => $db->query($builderd)->getResult(),
                'jumdata' => $db->query($builderd)->getNumRows(),
                'GudangTujuan' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $builder->gudang_tujuan])->getResult(),
                'GudangAsal' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $gudang])->getRow(),
                'css' => $css,
                'js' => $js,
                'title' => 'Mutasi Gudang',
                'body' => 'admin/inventory/transaksi/mutasi-gudang-edit',
                'cabang' => $session->get('cabang')
            ];
            return view('layout/layout', $data);
        }
    }

    public function mutasi_gudang_getlistmmg($kode)
    {
        $db = db_connect();
        if (!empty($kode)) {
            $mmc  = $db->query("SELECT * from inv_mohon_mutasi_gudang where noget='1' and  gudang_asal='$kode'")->getResult();
        ?>
            <select name="nomohon" id="nomohon" class="form-control  input-medium select2">
                <option value="">-- PILIH NO BUKTI ---</option>
                <?php
                foreach ($mmc  as $row) {
                    $hideDate = date('m-Y', strtotime($row->tanggal));
                    if ($hideDate != '02-2023') {
                        if ($row->statusid == '1') {
                ?>
                            <option value="<?php echo $row->nomohon; ?>"><?php echo $row->nomohon; ?></option>
                        <?php   } else { ?>
                            <option value="<?php echo $row->nomohon; ?>"><?php echo $row->nomohon; ?></option>
                <?php
                        }
                    }
                }
                ?>
            </select>

        <?php

        } else {
            echo "";
        }
    }

    public function mutasi_gudang_GetDataBarang($kode, $gudang)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $data = $db->query("SELECT * FROM inv_barang a
        INNER JOIN inv_mohond_mutasi_gudang b ON a.kodeitem= b.kodeitem  
        where a.kodeitem='$kode' and a.kdgudang='$gudang' and kdcbg='$cabang'")->getResult();
        echo json_encode($data);
    }

    public function getmmgheader($kode)
    {
        $session = session();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $data = $db->query("SELECT a.*,  b.nama as namagudangtujuan from  inv_mohon_mutasi_gudang a
                            inner join inv_gudang b on b.kode=a.gudang_tujuan where a.nomohon='$kode' and a.cabang='$cabang '")->getResult();
        echo json_encode($data);
    }

    public function mutasi_gudang_GetDataAndCheckQty($kode)
    {
        $db = db_connect();
        $rowmohon = $db->query("SELECT * FROM inv_mohon_mutasi_gudang where nomohon='$kode'")->getRow();
        $getDataBarang = $db->query("SELECT a.*,  b.namabarang, b.stok, b.satuan, a.qtymohon-a.qtymutasi as sisa 
                                    from inv_mohond_mutasi_gudang a
                                    inner join inv_barang b on b.kodeitem=a.kodeitem
                                    where a.nomohon='$kode' and a.gudang='$rowmohon->gudang_asal'
                                    and b.kdgudang='$rowmohon->gudang_tujuan' and a.qtymohon > a.qtymutasi")->getResult();
        echo json_encode($getDataBarang);
    }

    public function mutasi_gudang_save($param)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 521);
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $nomohon           = $input['nomohon'];
            $gudangtujuan   = $input['gudang_minta'];
            $gudangasal      = $input['gudang_kirim'];
            $kode              = $input['kode'];
            $no_batch          = $input['no_batch'];
            $tgl_exp          = $input['tgl_exp'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];

            $jumdata  = count($kode);

            $data_header = array(
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
                'gudang_asal' => $gudangasal,
                'gudang_tujuan' => $gudangtujuan,
                'nomohon' => $nomohon,
                'keterangan' => $input['keterangan'],
                'statusid' => '1',
                'cabang' => $cabang,
            );

            $builder = $db->table('inv_mutasi_gudang');
            $builderD = $db->table('inv_mutasi_gudang_detil');

            if ($param == 1) {
                $nomorbukti = $model_Global->_Autonomor('MG');
                $data_header['nomormutasi'] = $nomorbukti;
                $builder->insert($data_header);
            } else {
                $nomorbukti = $input['nomorbukti'];
                $resdatadetail = $builderD->where(['nomormutasi' => $nomorbukti])->get()->getResult();
                foreach ($resdatadetail as $value) {
                    if ($nomohon != "") {
                        $db->query("UPDATE inv_mohond_mutasi_gudang set qtymutasi = qtymutasi - '$value->qty' where kodeitem = '$value->kodeitem' and nomohon = '$nomohon' and cabang='$cabang' and gudang='$gudangtujuan'");
                    }
                }
                $builder->where(['nomormutasi' => $nomorbukti, 'cabang' => $cabang])->update($data_header);
                $builderD->delete(['nomormutasi' => $nomorbukti]);
            }

            for ($i = 0; $i < $jumdata; $i++) {

                $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$kode[$i]' and kdcbg='$cabang' and kdgudang='$gudangasal' ")->getRow();

                $rowmohon = $db->query("SELECT * FROM inv_mohon_mutasi_gudang where nomohon='$nomohon'")->getRow();

                $datad = array(
                    'id_mutasi' => $rowmohon->idmohon,
                    'nomormutasi' => $nomorbukti,
                    'nomohon' => $nomohon,
                    'kodeitem' => $kode[$i],
                    'no_batch' => $no_batch[$i],
                    'tanggal_exp' => $tgl_exp[$i],
                    'qty' => $qty[$i],
                    'satuan' => $sat[$i],
                    'hargabeli_mutasi' => $barang->hargabeli,
                    'cabang' => $cabang,
                );

                if ($kode[$i] != "") {
                    if ($param == 1) {
                        $builderD->insert($datad);
                    } else {
                        $builderD->insert($datad);
                    }
                    if ($nomohon != "") {
                        $db->query("UPDATE inv_mohond_mutasi_gudang set qtymutasi = qtymutasi + '$qty[$i]' where kodeitem = '$kode[$i]' and nomohon = '$nomohon' and cabang='$cabang' and gudang='$gudangtujuan'");
                    }
                }
            }
            if ($param == 1) {
                $model_Global->_updatecounter1('MG');
                $total = $db->query("SELECT sum(qtymohon-qtymutasi) as total from inv_mohond_mutasi_gudang where nomohon = '$nomohon' and gudang='$gudangtujuan' and cabang='$cabang'")->getRow()->total;

                if ($total <= 0) {
                    $db->query("UPDATE inv_mohon_mutasi_gudang set statusid=2, noget=2 where nomohon = '$nomohon' and cabang='$cabang' and gudang_asal='$gudangtujuan'");
                } else {
                    $db->query("UPDATE inv_mohon_mutasi_gudang set statusid=3, noget=1 where nomohon = '$nomohon' and cabang='$cabang' and gudang_asal='$gudangtujuan'");
                }
            }
            $sendData = $nomorbukti;
            echo json_encode($sendData);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function del_mutasi_gudang($nomor)
    {

        $model_Global =  model('Global_model', 'M_GLOBAL');

        $session = session();
        $db = db_connect();
        $db->transStart();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 521);
        $cabang = $session->get('cabang');
        if ($akses->udel == 1) {

            $Mutasi = $db->query("SELECT * from inv_mutasi_gudang where id_mutasi='$nomor' and cabang='$cabang'")->getRow();
            if ($Mutasi != "") {
                $datadetail = $db->table('inv_mutasi_gudang_detil')->getWhere(['nomormutasi' => $Mutasi->nomormutasi])->getResult();
                foreach ($datadetail as $row) {
                    $db->query("UPDATE inv_mohond_mutasi_gudang set qtymutasi = qtymutasi - '$row->qty' where kodeitem = '$row->kodeitem' and nomohon = '$Mutasi->nomohon'");
                }
            }

            $db->query("DELETE FROM inv_mutasi_gudang where nomormutasi='$Mutasi->nomormutasi'");
            $db->query("DELETE FROM inv_mutasi_gudang_detil where nomormutasi='$Mutasi->nomormutasi'");

            $total = $db->query("SELECT sum(qtymohon-qtymutasi) as total from inv_mohond_mutasi_gudang where nomohon = '$Mutasi->nomohon' and gudang='$Mutasi->gudang_tujuan' and cabang='$cabang' ")->getRow()->total;

            if ($total <= 0) {
                $db->query("UPDATE inv_mohon_mutasi_gudang set statusid=2 where nomohon = '$Mutasi->nomohon' and gudang_asal='$Mutasi->gudang_tujuan' and cabang='$cabang'");
            } else {
                $db->query("UPDATE inv_mohon_mutasi_gudang set statusid=1, noget=1 where nomohon = '$Mutasi->nomohon' and gudang_asal='$Mutasi->gudang_tujuan' and cabang='$cabang'");
            }
            $db->transComplete();
            echo json_encode(['message' => 'Success']);
        } else {

            json_encode(['message' => 'Gagal']);
        }
    }

    //*******************Start line Persetujuan Mutasi Gudang************************//
    //Persetujuan Mutasi GUDANG Page Start Here
    public function persetujuan_mutasi_gudang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Persetujuan Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/persetujuan-mutasi-gudang',
        ];
        return view('layout/layout', $data);
    }

    public function acc_mutasi_gudang_table($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $query = "SELECT * FROM inv_acc_mutasi_gudang where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 527);
        foreach ($res as $value) {
            $nameterima = $db->query("SELECT * FROM inv_gudang where kode='$value->gudangterima'")->getRow();
            $namekirim = $db->query("SELECT * FROM inv_gudang where kode='$value->gudangkirim'")->getRow();
            if ($value->statusid !== '1') {
                $status = '<span class="label label-md label-warning" style="color:white;"> Teretur </span>';
            } else if ($value->statusid == '1') {
                $status = '<span class="label label-md label-success" style="color:white;"> Sudah Disetujui </span>';
            }

            if ($akses->uedit == 1 && $akses->udel == 1) {
                $EditDel = '
                        <a class="btn btn-primary me-2 mb-2" href="persetujuanmutasigudang/view/' . $value->idaccmutasigudang . '/' . $value->gudangterima . '">
                            <i class="fa-solid fa-eye text-white"></i> view
                        </a>';
            } else if ($akses->uedit == 1 && $akses->udel == 0) {
                $EditDel = '
                     <a class="btn btn-primary me-2 mb-2" href="persetujuanmutasigudang/view/' . $value->idaccmutasigudang . '/' . $value->gudangterima . '">
                        <i class="fa-solid fa-eye text-white"></i> view
                    </a>';

                //  }else if($akses->uedit==0 && $akses->udel==1){
                //      $EditDel = '<button class="btn btn-danger mb-2" onclick="data_delete('.$value->idaccmutasigudang.')"><i class="fa-solid fa-trash-can"></i> Delete</button>';
            } else {
                $EditDel = '';
            }
            $data[] = [
                $value->noaccmutasi,
                $value->tanggal,
                $namekirim->nama,
                $nameterima->nama,
                $value->keterangan,
                $status,
                $EditDel
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function persetujuan_mutasi_gudang_entri()
    {
        $session = session();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Persetujuan Mutasi Gudang',
            'body' => 'admin/inventory/transaksi/persetujuan-mutasi-gudang-entri',
            'cabang' => $session->get('cabang')
        ];
        return view('layout/layout', $data);
    }

    public function acc_mutasigudang_view($id, $gudang)
    {
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 527);
        if ($akses->uedit == 1) {

            //header
            $builder = $db->table('inv_acc_mutasi_gudang')->getWhere(['idaccmutasigudang' => $id, 'gudangterima' => $gudang, 'cabang' => $cabang])->getRow();
            //Detail
            $builderd = "SELECT a.namabarang, a.stok, b.* from inv_barang a
			inner join inv_accd_mutasi_gudang b on a.kodeitem=b.kodeitem 
			where b.noaccmutasi = '$builder->noaccmutasi' and a.kdgudang='$gudang' and a.kdcbg='$cabang'";

            $css = [];
            $js = [];
            $data  = [
                'header' => $builder,
                'detail' => $db->query($builderd)->getResult(),
                'jumdata' => $db->query($builderd)->getNumRows(),
                'GudangTerima' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $builder->gudangterima])->getResult(),
                'GudangKirim' => $db->table('inv_gudang')->getWhere(['nama !=' => '', 'kode' => $builder->gudangkirim])->getRow(),
                'css' => $css,
                'js' => $js,
                'title' => 'Persetujuan Mutasi Gudang',
                'body' => 'admin/inventory/transaksi/persetujuan-mutasi-gudang-view',
                'cabang' => $session->get('cabang')
            ];
            return view('layout/layout', $data);
        }
    }

    public function persetujuan_mutasi_gudang_getlistmg($kode)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        if (!empty($kode)) {
            $mg  = $db->query("SELECT * from inv_mutasi_gudang where statusid='1' and gudang_tujuan='$kode' and cabang='$cabang'")->getResult();
        ?>
            <select name="nomutasi" id="nomutasi" class="form-control  input-medium select2">
                <option value="">-- PILIH NO MUTASI GUDANG ---</option>
                <?php
                foreach ($mg  as $row) {
                ?>
                    <option value="<?php echo $row->nomormutasi; ?>"><?php echo $row->nomormutasi; ?></option>
                <?php   } ?>
            </select>

        <?php

        } else {
            echo "";
        }
    }

    public function getmgheader($kode)
    {
        $session = session();
        $cabang = $session->get('cabang');
        $db = db_connect();
        $data = $db->query("SELECT a.*,  b.nama from  inv_mutasi_gudang a
                            inner join inv_gudang b on b.kode=a.gudang_asal where a.nomormutasi='$kode' and a.cabang='$cabang '")->getResult();
        echo json_encode($data);
    }

    public function acc_gudang_GetDataAndCheckQty($kode)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $rowmutasi = $db->query("SELECT * FROM inv_mutasi_gudang where nomormutasi='$kode' and cabang='$cabang'")->getRow();
        $getDataBarang = $db->query("SELECT a.*,  b.namabarang, b.stok, b.satuan
                                    from inv_mutasi_gudang_detil a
                                    inner join inv_barang b on b.kodeitem=a.kodeitem
                                    where a.nomormutasi='$kode' and b.kdgudang='$rowmutasi->gudang_asal' and b.kdcbg='$cabang'")->getResult();
        echo json_encode($getDataBarang);
    }

    public function acc_mutasi_gudang_save($param)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 527);
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $nomohon        = $input['nomohon'];
            $nomutasi       = $input['nomutasi'];
            $tanggal          = $input['tanggal'];
            $kode              = $input['kode'];
            $no_batch          = $input['no_batch'];
            $tgl_exp          = $input['tgl_exp'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];
            $gudangkirim    = $db->table('inv_gudang')->where(['kode' => $input['gudang_kirim']])->get()->getRow();
            $gudangterima   = $db->table('inv_gudang')->where(['kode' => $input['gudang_terima']])->get()->getRow();

            $jumdata  = count($kode);

            $data_header = array(
                'nomormutasi' => $input['nomutasi'],
                'nomohon' => $input['nomohon'],
                'gudangterima' => $gudangterima->kode,
                'gudangkirim' => $gudangkirim->kode,
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
                'statusid' => '1',
                'cabang' => $session->get('cabang'),
            );

            $builder = $db->table('inv_acc_mutasi_gudang');
            $builderD = $db->table('inv_accd_mutasi_gudang');
            $tablebarang = $db->table('inv_barang');
            $tablebatch = $db->table('inv_nobatch');
            $tableBarangGudang = $db->table('inv_barang_gudang');
            $tablemohondmutasigudang = $db->table('inv_mohond_mutasi_gudang');
            $tablemutasigudangd = $db->table('inv_mutasi_gudang_detil');
            $tableinvtransaksi = $db->table('inv_transaksi');
            if ($param == 1) {
                $nomorbukti = $model_Global->_Autonomor('AMG');
                $data_header['noaccmutasi'] = $nomorbukti;
                $builder->insert($data_header);
            } else {
                //$nomorbukti = $this->M_global->_Autonomor('MC');
                //    $nomorbukti = $this->input->post('nomorbukti');
                //    $this->db->update('inv_acc_mutasi_cabang',$data_header, array('noacc_mutasi' => $nomorbukti));	  
            }

            $nourut = 1;
            for ($i = 0; $i < $jumdata; $i++) {
                $barang = $tablebarang->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang])->get()->getRow();

                //$id_mutasi = $this->db->get_where('inv_acc_mutasi_cabang', array('noacc_mutasi' => $nomorbukti))->row();

                $datad = array(
                    'noaccmutasi' => $nomorbukti,
                    'kodeitem' => $kode[$i],
                    'nobatch' => $no_batch[$i],
                    'tglexp' => $tgl_exp[$i],
                    'qtyacc' => $qty[$i],
                    'satuan' => $sat[$i],
                    'harga' => $barang->hargabeli,
                    'cabang' => $cabang,
                );

                $inv_barang_gudang = array(
                    'kodeitem' => $kode[$i],
                    'gudang' => $gudangterima->kode,
                    'qty' => $qty[$i],
                    'cabang' => $cabang,
                );

                $inv_nobatch = array(
                    'no_batch' => $no_batch[$i],
                    'kd_jenis' => 'Mutasi Cabang',
                    'kodeitem' => $kode[$i],
                    'qty_terima' => $qty[$i],
                    'qty_saldo' => $qty[$i],
                    'gudang' => $gudangterima->kode,
                    'cabang' => $cabang,
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
                    'kdgudang' => $gudangterima->kode,
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



                if ($kode[$i] != "") {
                    $rowmohon = $tablemohondmutasigudang->where(['kodeitem' => $kode[$i], 'nomohon' => $nomohon, 'cabang' => $cabang])->get()->getRow();
                    $rowmutasi = $tablemutasigudangd->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'nomormutasi' => $nomutasi, 'cabang' => $cabang])->get()->getRow();
                    if ($param == 1) {
                        $builderD->insert($datad);
                        $tablemohondmutasigudang->where(['kodeitem' => $kode[$i], 'nomohon' => $nomohon, 'cabang' => $cabang])->update(['qtyterima' => $rowmohon->qtyterima + $qty[$i]]);
                        $tablemutasigudangd->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'nomormutasi' => $nomutasi, 'cabang' => $cabang])->update(['qtysetuju' => $rowmutasi->qtysetuju + $qty[$i]]);
                    }

                    $cek_barang = $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangterima->kode, 'kdcbg' => $cabang])->get()->getRow();
                    $GudangBarangKirim = $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $cabang])->get()->getRow();

                    $cek_nobatch = $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => $cabang, 'gudang' => $gudangterima->kode])->get()->getRow();
                    $Batchkirim = $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => $cabang, 'gudang' => $gudangkirim->kode])->get()->getRow();

                    $cekBrgGudang = $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->get()->getRow();
                    $BarangGudangKirim = $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $cabang])->get()->getRow();

                    //update dan insert inv_barang
                    if (!empty($cek_barang)) {
                        $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangterima->kode, 'kdcbg' => $cabang])->update(['stok' => $cek_barang->stok + $qty[$i], 'stok_so' => $cek_barang->stok_so + $qty[$i]]);
                        $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $cabang])->update(['stok' => $GudangBarangKirim->stok -  $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so - $qty[$i]]);
                    } else {
                        $tablebarang->insert($inv_barang);
                        $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $cabang])->update(['stok' => $GudangBarangKirim->stok - $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so + $qty[$i]]);
                    }

                    //update dan insert inv_nobatch
                    if (!empty($cek_nobatch)) {

                        $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $cabang])->update(['qty_jual' => $Batchkirim->qty_jual + $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
                        $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->update(['qty_terima' => $cek_nobatch->qty_terima + $qty[$i], 'qty_saldo' => $cek_nobatch->qty_saldo + $qty[$i]]);
                    } else {
                        $tablebatch->insert($inv_nobatch);
                        $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $cabang])->update(['qty_jual' => $Batchkirim->qty_jual - $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
                    }

                    //update dan insert inv_barang_gudang
                    if (!empty($cekBrgGudang)) {
                        $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $cabang])->update(['qty' => $BarangGudangKirim->qty -  $qty[$i]]);
                        $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->update(['qty' => $cekBrgGudang->qty + $qty[$i]]);
                    } else {
                        $tableBarangGudang->insert($inv_barang_gudang);
                        $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $cabang])->update(['qty' => $BarangGudangKirim->qty -  $qty[$i]]);
                    }


                    $data_inv = array(
                        'tanggal'   => $tanggal,
                        'kodeitem' => $kode[$i],
                        'penerimaan' => 0,
                        'pengeluaran' => $qty[$i],
                        'satuan' => $sat[$i],
                        'jenis' => 'Mutasi Gudang',
                        'nobukti' => $nomorbukti,
                        'tglrekam'   => date('Y-m-d H:i:s'),
                        'hpp'   => $barang->hargabeli,
                        'hargajual'   => 0,
                        'gudang' => $gudangkirim->kode,
                        'no_batch' => $no_batch[$i],
                        'tanggal_exp' => $tgl_exp[$i],
                        'cabang' => $cabang,
                    );

                    $tableinvtransaksi->insert($data_inv);

                    $data_inv_terima = array(
                        'tanggal'   => $tanggal,
                        'kodeitem' => $kode[$i],
                        'penerimaan' =>  $qty[$i],
                        'pengeluaran' => 0,
                        'satuan' => $sat[$i],
                        'jenis' => 'Mutasi Gudang',
                        'nobukti' => $nomorbukti,
                        'tglrekam'   => date('Y-m-d H:i:s'),
                        'hpp'   => $barang->hargabeli,
                        'hargajual'   => 0,
                        'gudang' => $gudangterima->kode,
                        'no_batch' => $no_batch[$i],
                        'tanggal_exp' => $tgl_exp[$i],
                        'cabang' => $cabang,
                    );

                    $tableinvtransaksi->insert($data_inv_terima);
                }
            }
            if ($param == 1) {
                //update status tbl mohon
                $data_mohon = $db->query("SELECT SUM(qtymohon) AS qtymohon, SUM(qtyterima) AS qtyterima FROM inv_mohond_mutasi_gudang where nomohon='$nomohon' and cabang='$cabang'")->getRow();
                if ($data_mohon->qtyterima >= $data_mohon->qtymohon) {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_gudang set statusid='4', noget='2' where nomohon = '$nomohon' and cabang='$cabang'");
                } else {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_gudang set statusid='3' where nomohon = '$nomohon' and cabang='$cabang'");
                }
                //update status tbl mutasi
                $data_mutasi = $db->query("SELECT SUM(qty) AS qtykirim, SUM(qtysetuju) AS qtysetuju FROM inv_mutasi_gudang_detil where nomormutasi='$nomutasi' and cabang='$cabang'")->getRow();
                if ($data_mutasi->qtysetuju >= $data_mutasi->qtykirim) {
                    $update_mutasi = $db->query("UPDATE inv_mutasi_gudang set statusid='2' where nomormutasi = '$nomutasi' and cabang='$cabang'");
                } else {
                    $update_mutasi = $db->query("UPDATE inv_mutasi_gudang set statusid='3' where nomormutasi = '$nomutasi' and cabang='$cabang'");
                }
                $model_Global->_updatecounter1('AMG');
            }

            echo json_encode($nomorbukti);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function del_acc_mutasi_gudang($nomor)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');

        $session = session();
        $db = db_connect();
        $TableAccMutasiGudang = $db->table('inv_acc_mutasi_gudang');
        $TableAccdMutasiGudang = $db->table('inv_accd_mutasi_gudang');
        $Tableinvtransaksi = $db->table('inv_transaksi');
        $db->transStart();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 527);
        $cabang = $session->get('cabang');
        if ($akses->udel == 1) {
            $rowAccMutasi = $db->query("SELECT * from inv_acc_mutasi_gudang where idaccmutasigudang='$nomor'")->getRow();
            $rowAccMutasiqty = $db->query("SELECT SUM(qtyacc) AS qtyacc FROM inv_accd_mutasi_gudang  where noaccmutasi='$rowAccMutasi->noaccmutasi'")->getRow();
            $rowAccdMutasiqty = $TableAccdMutasiGudang->where(['noaccmutasi' => $rowAccMutasi->noaccmutasi])->get()->getResult();
            $rowMohonqty = $db->query("SELECT SUM(qtyterima) AS qtyterima FROM inv_mohond_mutasi_gudang where nomohon='$rowAccMutasi->nomohon'")->getRow();
            $rowMutasiqty = $db->query("SELECT SUM(qtysetuju) AS qtysetuju FROM inv_mutasi_gudang_detil where nomormutasi='$rowAccMutasi->nomormutasi'")->getRow();
            foreach ($rowAccdMutasiqty as $view) {
                $db->query("UPDATE inv_mohond_mutasi_gudang set qtyterima= qtyterima - $view->qtyacc where nomohon = '$rowAccMutasi->nomohon' and kodeitem='$view->kodeitem'");
                $db->query("UPDATE inv_mutasi_gudang_detil set qtysetuju= qtysetuju - $view->qtyacc where nomormutasi = '$rowAccMutasi->nomormutasi' and kodeitem='$view->kodeitem'");
            }
            if ($rowAccMutasiqty->qtyacc >= $rowMohonqty->qtyterima) {
                $db->query("update inv_mohon_mutasi_gudang set statusid=1, noget=1 where nomohon = '$rowAccMutasi->nomohon'");
            }
            if ($rowAccMutasiqty->qtyacc >= $rowMutasiqty->qtysetuju) {
                $db->query("update inv_mutasi_gudang set statusid=1 where nomormutasi = '$rowAccMutasi->nomormutasi'");
            }
            $TableAccMutasiGudang->delete(['noaccmutasi' => $nomor]);
            $TableAccdMutasiGudang->delete(['noaccmutasi' => $nomor]);
            $Tableinvtransaksi->delete(['nobukti' => $nomor]);
        } else {

            header('location:' . base_url());
        }
    }

    //*******************Start line Permohonan Mutasi CABANG************************//
    //Permohonan Mutasi Cabang Page Start Here
    public function permohonan_mutasi_cabang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Region',
            'body' => 'admin/inventory/transaksi/permohonan-mutasi-cabang',
        ];
        return view('layout/layout', $data);
    }

    public function permohonan_mutasi_cabang_table_filter($tgl1, $tgl2)
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
		$lvldebug = $session->get('debug');
        $query = "SELECT * FROM inv_mohon_mutasi_cabang where ";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $db = db_connect();
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 522);
        foreach ($res as $value) {
            if ($value->statusid != '4') {
                $namecabangasal = $db->query("SELECT * FROM ms_unit where kode='$value->cabang_asal'")->getRow();
                $namecabangtujuan = $db->query("SELECT * FROM ms_unit where kode='$value->cabang_tujuan'")->getRow();
                if ($value->statusid == '1') {
                    $status = '<span class="label label-md label-primary" style="color:white;"> Menunggu Proses </span>';
                } else if ($value->statusid == '2') {
                    $status = '<span class="label label-md label-success" style="color:white;"> Siap Diterima </span>';
                } else if ($value->statusid == '3') {
                    $status = '<span class="label label-md label-warning" style="color:white;"> Barang Belum Terpenuhi Semua </span>';
                } else {
                    $status = '';
                }

                if ($akses->uedit == 1 && $akses->udel == 1 && $value->statusid == '1') {
                    $EditDel = '
                        <a class="btn" href="' . base_url('inventory/transaksi/permohonanmutasicabang/entri/' . $value->idmohon) . '">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <button class="btn" onclick="data_delete(' . $value->idmohon . ')"><i class="fa-solid fa-trash-can"></i></button>';
                } else if ($akses->uedit == 1 && $akses->udel == 0 && $value->statusid == '1') {
                    $EditDel = '
                     <a href="' . base_url('inventory/transaksi/permohonanmutasicabang/entri/' . $value->idmohon) . '" class="btn"><i class="fa-solid fa-edit"></i></a>';
                } else if ($akses->uedit == 0 && $akses->udel == 1 && $value->statusid == '1') {
                    $EditDel = '<button class="btn" onclick="data_delete(' . $value->idmohon . ')"><i class="fa-solid fa-trash-can"></i></button>';
                } else if ($value->statusid == '3') {
                    $EditDel = '
                    <a href="' . base_url('inventory/transaksi/permohonanmutasicabang/entri/' . $value->idmohon) . '" class="btn"><i class="fa-solid fa-eye"></i></a>';
                } else {
                    $EditDel = '';
                }

                if($lvldebug == 1){
                    $debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->nomohon . '\')"><i class="fas fa-bug"></i></a>';
                }else{
                    $debug = '';
                }
                $data[] = [
                    $value->nomohon,
                    $value->tglrekam,
                    $namecabangasal->nama,
                    $namecabangtujuan->nama,
                    $value->keterangan,
                    $status,
                    $EditDel . $debug
                ];
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function permohonanmutasicabang_entri($id = '')
    {
        $session = session();
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $cabang = $session->get('cabang');
        $builder = $db->query("SELECT * FROM inv_barang where kdcbg='$cabang' group by kdgudang")->getRow();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Region',
            'body' => 'admin/inventory/transaksi/permohonan-mutasi-cabang-entri',
            'id' => $id ?? '',
            'nomor' => $model_Global->_Autonomor('MMC'),
            'cabang' => $cabang,
            'gudang' => $builder->kdgudang,
            'db' => $db
        ];
        return view('layout/layout', $data);
    }

    public function permohonanMutasiCabang_save()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $param = $input['id'];
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 522);
        // Table
        $builder            = $db->table('inv_mohon_mutasi_cabang');
        $builderDetail      = $db->table('inv_mohond_mutasi_cabang');
        $builderGudang      = $db->table('inv_gudang');
        $builderInvBarang   = $db->table('inv_barang');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $kode              = $input['kode'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];

            $DatamohonCabang = $builder->getWhere(['nomohon' => $input['nomorbukti'], 'cabang' => $input['cabang_asal']])->getRow();
            if ($param == 0) {
                if (empty($DatamohonCabang)) {
                    $nobukti = $input['nomorbukti'];
                } else {
                    $nobukti = $model_Global->_Autonomor('MMC');
                }
            }

            $DataGudangAsal     = $builderGudang->getWhere(['cabang' => $input['cabang_asal']])->getRow();
            $DataGudangTujuan   = $builderGudang->getWhere(['cabang' => $input['cabang_tujuan']])->getRow();
            $kode == null ? $_kode = array('0') : $_kode = $kode;

            if ($param != 0) {

                if ($DatamohonCabang->statusid == 3) {
                    $DataDetail = $builderDetail->getWhere(['nomohon' => $input['nomorbukti'], 'cabang' => $input['cabang_asal']])->getResult();

                    $differentData = [];
                    foreach ($DataDetail as $view) {
                        $array = in_array($view->kodeitem, $_kode);
                        if ($array == false) {
                            $differentData[] = ['kodeitem' => $view->kodeitem, 'qtymohon' => $view->qtymohon, 'qtymutasi' => $view->qtymutasi];
                        }
                    }
                    $differentData;
                    $jumdatasts  = count($differentData);

                    for ($i = 0; $i < $jumdatasts; $i++) {
                        $where = [
                            'nomohon' => $input['nomorbukti'],
                            'kodeitem' => $differentData[$i]['kodeitem'],
                            'cabang' => $input['cabang_asal'],
                        ];

                        $dataUpdate = [
                            'qtymohon' => $differentData[$i]['qtymutasi'],
                        ];
                        $builderDetail->where($where)->update($dataUpdate);
                    }
                } else {
                    $builderDetail->delete(['nomohon' => $input['nomorbukti'], 'cabang' => $input['cabang_asal']]);
                }
            }
            $jumdata  = count($_kode);

            for ($i = 0; $i < $jumdata; $i++) {
                $barang = $builderInvBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $input['cabang_tujuan']])->getRow();

                $datad = array(
                    'kodeitem' => $kode[$i],
                    'qtymohon' => $qty[$i],
                    'satuan' => $sat[$i],
                    'harga' => $barang->hargabeli,
                    'cabang' => $input['cabang_asal'],
                    'gudang' => $DataGudangAsal->kode,
                    'userid' => $session->get('username'),
                );
                if ($kode[$i] != "") {

                    if ($param == 0) {
                        $datad['nomohon'] = $nobukti;
                        $builderDetail->insert($datad);
                    } else {
                        if ($DatamohonCabang->statusid != 3) {
                            $datad['nomohon'] = $input['nomorbukti'];
                            $builderDetail->insert($datad);
                        }
                    }
                }
            }

            $data_header = array(
                'cabang_asal' => $input['cabang_asal'],
                'cabang_tujuan' => $input['cabang_tujuan'],
                'gudang_asal' => $DataGudangAsal->kode,
                'gudang_tujuan' => $DataGudangTujuan->kode,
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'statusid' => '1',
                'no_get' => '1',
                'userid' => $session->get('username'),
                'cabang' => $input['cabang_asal'],
            );

            $datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];

            if ($param == 0) {
                $data_header['nomohon'] = $nobukti;
                $builder->insert($data_header);
                $model_Global->_updatecounter1('MMC');
                $datalogs['detail'] = 'Tambah data permintaan mutasi dengan kode ' . $nobukti;
				$db->table("logs")->insert($datalogs);
                echo json_encode($nobukti);
            } else {
                $nomohon = $input['nomorbukti'];
                $cab = $input['cabang_asal'];
                if ($DatamohonCabang->statusid == 3) {
                    $dataHupdate = array(
                        'keterangan' => $input['keterangan'],
                        'tanggal' => $input['tanggal'],
                        'tglrekam' => date('Y-m-d H:i:s'),
                        'userid' => $session->get('username'),
                    );
                    $total = $db->query("SELECT sum(qtymohon-qtymutasi) as total from inv_mohond_mutasi_cabang where nomohon = '$nomohon' and cabang='$cab'")->getRow()->total;
                    if ($total <= 0) {
                        $dataHupdate['statusid'] = 4;
                        $dataHupdate['no_get'] = 2;
                        $builder->where(['nomohon' => $nomohon, 'cabang' => $cab])->update($dataHupdate);
                    } else {
                        $builder->where(['nomohon' => $nomohon, 'cabang' => $cab])->update($dataHupdate);
                    }
                } else {
                    $builder->where(['nomohon' => $input['nomorbukti'], 'cabang' => $input['cabang_asal']])->update($data_header);
                }
                $datalogs['detail'] = 'Edit data permintaan mutasi dengan kode ' . $nomohon;
				$db->table("logs")->insert($datalogs);

                echo json_encode($input['nomorbukti']);
            }
        } else {
            json_encode(['message' => 'Gagal']);
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

    public function permohonanMutasiCabang_delete($id)
    {
        $db = db_connect();
        $db->transStart();
        $builder = $db->table('inv_mohon_mutasi_cabang');
        $result = $builder->getWhere(['idmohon' => $id])->getRow();
        $builderD = $db->table('inv_mohond_mutasi_cabang');
        $delete = $builder->delete(['nomohon' => $result->nomohon]);
        $delete = $builderD->delete(['nomohon' => $result->nomohon]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
        $db->transComplete();
    }

    public function cetak_mohoncabang($param1)
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
            $hp       = $profile->hp;
            $email       = $profile->email;

            $queryh = "SELECT a.*, b.nama AS cabangasal, b.alamat as alamatasal, c.nama AS cabangtujuan, c.alamat as alamattujuan, d.nama AS gudangasal, e.nama AS gudangtujuan 
						from inv_mohon_mutasi_cabang a
						inner join ms_unit b on a.cabang_asal=b.kode 
						inner join ms_unit c on a.cabang_tujuan=c.kode
						inner join inv_gudang d on a.gudang_asal=d.kode
						inner join inv_gudang e on a.gudang_tujuan=e.kode
						WHERE a.nomohon ='$param'  ";
            $header = $db->query($queryh)->getRow();
            $queryd = "SELECT a.namabarang, b.* from inv_barang a
						inner join inv_mohond_mutasi_cabang b on a.kodeitem=b.kodeitem 
						where b.nomohon = '$param' AND a.kdcbg='$header->cabang_tujuan' AND a.kdgudang='$header->gudang_tujuan'";

            $detil  = $db->query($queryd)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('BT', '', 'BT');
            $size   = array('0', '0', '0');
            $pdf->setfont('Arial', 'B', 9);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('B', '', 'B');
            $size  = array('9', '0', '9');
            $max   = array(5, 5, 20);
            $judul = array('Permintaan Dari Cabang :', '', 'Permintaan Ke Cabang :');
            $fc     = array('0', '0', '0');
            $pdf->image('./assets/images/logo_paper.jpg', 50, 60, 100, 100);
            $pdf->FancyRow2(7, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetWidths(array(85, 15, 23, 4, 30, 13, 3, 25));
            $border = array('', '', '', '', '', '', '');
            $fc     = array('0', '0', '0', '0', '0', '0', '0');
            $fc1     = array('0', '0', '0', '0', '0', '0', '0');
            $pdf->SetFillColor(230, 230, 230);
            $pdf->setfont('Arial', '', 7.5);
            $style = array('B', '', '', '', '', '', '');
            $head = array($header->cabangasal, '', 'Cabang Tujuan', ':', $header->cabangtujuan, 'Tanggal', ':', $header->tanggal);
            $pdf->FancyRow2(5, $head, $fc1,  $border, $align, $style);
            $pdf->ln(-2);
            $alamat = array($header->alamatasal, '', 'Alamat Tujuan', ':', $header->alamattujuan, 'Time', ':', date('H:i'));
            $pdf->FancyRow2(5, $alamat, $fc1,  $border, $align);
            $pdf->ln(-1);
            $nomutasi = array('', '', 'Nomor Mutasi', ':', $header->nomohon, '', '', '');
            $pdf->FancyRow2(3, $nomutasi, $fc1,  $border, $align);
            $pdf->ln(2);

            $pdf->SetWidths(array(20, 120, 30, 20));
            $border = array('TB', 'TB', 'TB', 'TB');
            $align  = array('L', 'L', 'L', 'C');
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetAligns(array('L', 'C', 'R'));
            $fc = array('0', '0', '0', '0');
            $judul = array('Kode', 'Nama Barang', 'Qty Permintaan', 'Satuan');
            $pdf->FancyRow2(5, $judul, $fc, $border, $align);
            $border = array('', '', '');
            $pdf->setfont('Arial', '', 7.5);
            $tot = 0;
            $border = array('', '', '', '');
            $align  = array('L', 'L', 'L', 'C');
            $fc = array('0', '0', '0', '0');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(0);
            $tot = 0;
            $qty = 0;
            foreach ($detil as $db) {
                if ($db->qtymohon >= 1) {
                    $qty = $qty + $db->qtymohon;

                    $pdf->FancyRow(array(
                        $db->kodeitem,
                        $db->namabarang,
                        $db->qtymohon,
                        $db->satuan
                    ), $fc, $border, $align);
                    $tot++;
                    $pdf->ln(-1.5);
                }
            }
            $pdf->ln(-4);
            $pdf->SetFillColor(230);
            $border = array('B', 'B', 'B', 'B', 'B', 'B');
            $pdf->FancyRow(array('', '', '', '', '', '', '', ''), 0, $border);
            $pdf->ln(1);
            $pdf->SetWidths(array(100, 20, 30, 40));
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $pdf->FancyRow(array('Keterangan', '', 'Total Kuantitas', $qty), $fc, $border, $align);
            $border = array('', '', 'B', 'B');
            $pdf->ln(0);
            $pdf->FancyRow(array($header->keterangan, '', 'Jumlah Barang', $tot), $fc, $border, $align);
            $border = array('B', '', '', '');
            $pdf->FancyRow(array('', '', '',), $fc, $border, $align);
            $pdf->SetWidths(array(50, 50, 50));
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $pdf->ln(1);
            $border = array('', '', '', '');
            $align  = array('C', 'C', 'C', 'C');
            $pdf->FancyRow(array('', '', '', 'Peminta'), 0, $border, $align);
            $pdf->ln(-1);
            $pdf->SetWidths(array(47, 1.5, 47, 5, 47, 0, 46));
            $pdf->SetFont('Arial', '', 8);
            $border = array('', '', '', '', '', '', '');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $pdf->FancyRow(array('', '', '', '', '', '', 'Tgl. Nama'), 0, $border, $align);
            $pdf->Ln(30);




            foreach ($detil as $db) {
                $DbBase = db_connect();
                $RowPaket = $DbBase->table(['inv_barang_paket'])->getWhere(['kodeitem_barang' => $db->kodeitem])->getResult();
                if (!empty($RowPaket)) {
                    $pdf->setfont('Times', 'B', 10);
                    $border = array('');
                    $align = array('L');
                    $pdf->SetWidths(array(100));
                    $pdf->ln(5);

                    $pdf->Fancyrow(array($db->namabarang), $border, $align);
                    $pdf->ln(1);

                    if ($db->qtymohon >= 1) {
                        $pdf->SetWidths(array(10, 40, 15, 30));
                        $border = array('TB', 'TB', 'TB', 'TB');
                        $pdf->SetAligns(array('L', 'C', 'L'));
                        $pdf->setfont('Arial', 'B', 7.5);
                        $fc = array('0', '0', '0', '0');
                        $align  = array('L', 'L', 'L', 'C');
                        $pdf->SetFillColor(0, 0, 139);
                        $pdf->settextcolor(0);
                        $judul = array('Qty', 'Isi Nama Barang', 'Satuan', 'Nama Barang Paket');
                        $pdf->FancyRow2(5, $judul, $fc, $border, $align);
                        $cabangtuju = $header->cabang_tujuan;
                        $kodeitem = $db->kodeitem;
                        $DbBase = db_connect();
                        $ResultPaket = $DbBase->table(['inv_barang_paket'])->getWhere(['kodeitem_barang' => $db->kodeitem])->getResult();
                        foreach ($ResultPaket as $ResultPaket) {
                            $border = array('', '', '', '');
                            $pdf->SetAligns(array('L', 'C', 'L'));
                            $pdf->setfont('Arial', '', 7.5);
                            $fc = array('0', '0', '0', '0');
                            $align  = array('L', 'L', 'L', 'C');
                            $pdf->SetFillColor(0, 0, 139);
                            $pdf->settextcolor(0);
                            if ($ResultPaket != null) {
                                $qty = $qty + $db->qtymohon;

                                $pdf->FancyRow(array(
                                    $ResultPaket->qty_paket,
                                    $ResultPaket->namabarang_paket,
                                    $ResultPaket->satuan_paket,
                                    $db->namabarang
                                ), $fc, $border, $align);
                                $pdf->ln(0);
                            }
                        }
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

    //*******************Start line Mutasi CABANG************************//
    //Mutasi CABANG Page Start Here
    public function mutasi_cabang()
    {

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Disiapkan',
            'body' => 'admin/inventory/transaksi/mutasi-cabang',
        ];
        return view('layout/layout', $data);
    }

    public function mutasi_cabang_table($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
		$lvldebug = $session->get('debug');
        $db = db_connect();
        $query = "SELECT * FROM inv_mutasi_cabang where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 523);
        foreach ($res as $value) {
            $hideDate = date('m-Y', strtotime($value->tanggal));
            if ($hideDate != '02-2023') {
                $namecabangasal = $db->table("ms_unit")->getWhere(['kode' => $value->cabang_asal])->getRow()->nama;
                $namecabangtujuan = $db->table("ms_unit")->getWhere(['kode' => $value->cabang_tujuan])->getRow()->nama;
                if ($value->statusid == '1') {
                    $status = '<span class="label label-md label-warning" style="color:white;"> Dikirim </span>';
                } else if ($value->statusid == '2') {
                    $status = '<span class="label label-md label-success" style="color:white;"> Diterima </span>';
                } else if ($value->statusid == '3') {
                    $status = '<span class="label label-md label-primary" style="color:white;"> Masih ada pendingan </span>';
                }

                if ($akses->uedit == 1 && $akses->udel == 1) {
                    $EditDel = '
                        <a class="btn" href="' . base_url('inventory/transaksi/mutasicabang/entri/' . $value->id_mutasi_cabang) . '">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <button class="btn" onclick="data_delete(' . $value->id_mutasi_cabang . ')"><i class="fa-solid fa-trash-can"></i></button>';
                } else if ($akses->uedit == 1 && $akses->udel == 0) {
                    $EditDel = '
                        <a class="btn" href="' . base_url('inventory/transaksi/mutasicabang/entri/' . $value->id_mutasi_cabang) . '">
                            <i class="fa-solid fa-edit"></i>
                        </a>';
                } else if ($akses->uedit == 0 && $akses->udel == 1) {
                    $EditDel = '<button class="btn" onclick="data_delete(' . $value->id_mutasi_cabang . ')"><i class="fa-solid fa-trash-can"></i></button>';
                } else {
                    $EditDel = '';
                }
                
                if($lvldebug == 1){
                    $debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->nomutasi_cabang . '\')"><i class="fas fa-bug"></i></a>';
                }else{
                    $debug = '';
                }
                $data[] = [
                    $value->nomutasi_cabang,
                    $value->tglrekam,
                    $namecabangasal,
                    $namecabangtujuan,
                    $value->keterangan,
                    $status,
                    ($EditDel) .
                    ($akses->ucetak == 1 ? '<a href="' . base_url('inventory/transaksi/mutasicabangcetak/' . $value->nomutasi_cabang) . '" target="_blank" class="btn"><i class="fa-solid fa-print"></i></a>' : '') .
                    ($debug),
                ];
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function mutasicabangentri($id = '')
    {
        $session = session();
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $cabang = $session->get('cabang');
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Permohonan Mutasi Disiapkan',
            'body' => 'admin/inventory/transaksi/mutasi-cabang-entri',
            'id' => $id ?? '',
            'nomor' => $model_Global->_Autonomor('MC'),
            'cabang' => $cabang,
            'db' => $db
        ];
        return view('layout/layout', $data);
    }

    public function mutasi_cabang_getlistmmc($kode)
    {
        $db = db_connect();
        if (!empty($kode)) {
            $mmc  = $db->query("SELECT * from inv_mohon_mutasi_cabang where no_get='1' and  cabang_asal='$kode'")->getResult();
        ?>
            <select name="nomohon" id="nomohon" class="form-control  input-medium select2">
                <option value="">-- PILIH NO BUKTI ---</option>
                <?php
                foreach ($mmc  as $row) {
                    $hideDate = date('m-Y', strtotime($row->tanggal));
                    if ($hideDate != '02-2023') {
                        if ($row->statusid == '1') {
                ?>
                            <option value="<?php echo $row->nomohon; ?>"><?php echo $row->nomohon; ?></option>
                        <?php   } else { ?>
                            <option value="<?php echo $row->nomohon; ?>"><?php echo $row->nomohon; ?></option>
                <?php
                        }
                    }
                }
                ?>
            </select>

        <?php

        } else {
            echo "";
        }
    }

    public function mutasi_cabang_GetDataAndCheckQty($kode)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $rowmohon = $db->query("SELECT * FROM inv_mohon_mutasi_cabang where nomohon='$kode'")->getRow();
        $item = $db->query("SELECT a.*,  b.namabarang, b.stok, b.satuan, a.qtymohon-a.qtymutasi as sisa 
                                    from inv_mohond_mutasi_cabang a
                                    inner join inv_barang b on b.kodeitem=a.kodeitem
                                    where a.nomohon='$kode' and a.cabang='$rowmohon->cabang_asal'
                                    and b.kdcbg='$rowmohon->cabang_tujuan' and a.qtymohon > a.qtymutasi GROUP BY idmohond")->getResult();

        $header = $db->query("SELECT a.*,  b.nama as namacabangtujuan from  inv_mohon_mutasi_cabang a
        inner join ms_unit b on b.kode=a.cabang_tujuan where a.nomohon='$kode' and a.cabang_tujuan='$cabang'")->getRow();
        $response = ['datahead' => $header, 'dataitem' => $item];
        echo json_encode($response);
    }

    public function mutasi_cabang_GetDataBarang($kode, $nobukti)
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $data = $db->query("SELECT a.*,b.*, b.qtymohon-b.qtymutasi as sisa  FROM inv_barang a
        INNER JOIN inv_mohond_mutasi_cabang b ON a.kodeitem= b.kodeitem  
        where a.kodeitem='$kode' and b.nomohon='$nobukti' and kdcbg='$cabang' group by idmohond")->getRow();
        echo json_encode($data);
    }

    public function MutasiCabang_save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 523);
        $param = $input['id'];
        // Table
        $builder            = $db->table('inv_mutasi_cabang');
        $builderDetail      = $db->table('inv_mutasi_cabang_detail');
        $builderGudang      = $db->table('inv_gudang');
        $builderInvBarang   = $db->table('inv_barang');
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $nomohon           = $input['nomohon'];
            $kode              = $input['kode'];
            $no_batch          = $input['no_batch'];
            $tgl_exp          = $input['tgl_exp'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];
            $DatamutasiCabang = $builder->getWhere(['nomutasi_cabang' => $input['nomorbukti'], 'cabang' => $cabang])->getRow();
            if ($param == 0) {
                if (empty($DatamutasiCabang)) {
                    $nobukti = $input['nomorbukti'];
                } else {
                    $nobukti = $model_Global->_Autonomor('MC');
                }
            }

            $DataGudangMinta     = $builderGudang->getWhere(['cabang' => $input['cabang_minta']])->getRow();
            $DataGudangKirim   = $builderGudang->getWhere(['cabang' => $input['cabang_pengirim']])->getRow();

            $data_header = array(
                'nomohon' => $input['nomohon'],
                'cabang_asal' => $input['cabang_pengirim'],
                'cabang_tujuan' => $input['cabang_minta'],
                'gudang_asal' => $DataGudangKirim->kode,
                'gudang_tujuan' => $DataGudangMinta->kode,
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
                'statusid' => '1',
                'cabang' => $cabang,
            );

            $datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
            if ($param == 0) {
                $data_header['nomutasi_cabang'] = $nobukti;
                $builder->insert($data_header);
                $datalogs['detail'] = 'Tambah data mutasi cabang dengan kode ' . $nobukti;
                $datalogs['nobukti'] = $nobukti;

                $db->table("logs")->insert($datalogs);
                echo json_encode($nobukti);
            } else {
                $dataUpdate = array(
                    'keterangan' => $input['keterangan'],
                    'tanggal' => $input['tanggal'],
                    'tglrekam' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                );
                $builder->where(['nomutasi_cabang' => $input['nomorbukti'], 'cabang' => $cabang])->update($dataUpdate);
                $resdatadetail = $builderDetail->where(['nomutasi_cabang' => $input['nomorbukti'], 'cabang' => $cabang])->get()->getResult();
                foreach ($resdatadetail as $value) {
                    if ($nomohon != "") {
                        $db->query("UPDATE inv_mohond_mutasi_cabang set qtymutasi = qtymutasi - '$value->qty' where kodeitem = '$value->kodeitem' and nomohon = '$nomohon' and cabang='$DatamutasiCabang->cabang_tujuan' and gudang='$DataGudangMinta->kode'");
                    }
                }
                $builderDetail->delete(['nomutasi_cabang' => $input['nomorbukti'], 'cabang' => $cabang]);
                $datalogs['detail'] = 'Edit data mutasi cabang dengan kode ' . $input['nomorbukti'];
				$db->table("logs")->insert($datalogs);
                echo json_encode($input['nomorbukti']);
            }

            $jumdata  = count($kode);
            for ($i = 0; $i < $jumdata; $i++) {
                $barang = $builderInvBarang->getWhere(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $DataGudangKirim->kode])->getRow();
                if ($param == 0) {
                    $nomor = $nobukti;
                } else {
                    $nomor = $input['nomorbukti'];
                }
                $rowmutasiCabang = $builder->getWhere(['nomutasi_cabang' => $nomor, 'cabang' => $cabang])->getRow();

                $datad = array(
                    'id_mutasi_cabang' => $rowmutasiCabang->id_mutasi_cabang,
                    'kodeitem' => $kode[$i],
                    'nobatch' => $no_batch[$i],
                    'tglexp' => $tgl_exp[$i],
                    'qty' => $qty[$i],
                    'satuan' => $sat[$i],
                    'harga' => $barang->hargabeli,
                    'cabang' => $cabang,
                );

                if ($kode[$i] != "") {
                    if ($param == 0) {
                        $datad['nomutasi_cabang'] = $nobukti;
                        $builderDetail->insert($datad);
                    } else {
                        $datad['nomutasi_cabang'] = $input['nomorbukti'];
                        $builderDetail->insert($datad);
                    }
                    if ($nomohon != "") {
                        $db->query("UPDATE inv_mohond_mutasi_cabang set qtymutasi = qtymutasi + '$qty[$i]' where kodeitem = '$kode[$i]' and nomohon = '$nomohon' and cabang='$rowmutasiCabang->cabang_tujuan' and gudang='$DataGudangMinta->kode'");
                    }
                }
            }
            // Table 2
            $buildMohon            = $db->table('inv_mohon_mutasi_cabang');
            if ($param == 0) {
                $model_Global->_updatecounter1('MC');
            }
            $total = $db->query("SELECT sum(qtymohon-qtymutasi) as total from inv_mohond_mutasi_cabang where nomohon = '$nomohon' and gudang='$DataGudangMinta->kode' and cabang='$rowmutasiCabang->cabang_tujuan'")->getRow()->total;
            if ($total <= 0) {
                $buildMohon->where(['nomohon' => $nomohon, 'cabang' => $rowmutasiCabang->cabang_tujuan, 'gudang_asal' => $DataGudangMinta->kode])->update(['statusid' => 2, 'no_get' => 2]);
            } else {
                $buildMohon->where(['nomohon' => $nomohon, 'cabang' => $rowmutasiCabang->cabang_tujuan, 'gudang_asal' => $DataGudangMinta->kode])->update(['statusid' => 3, 'no_get' => 1]);
            }
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function del_mutasicabang($nomor)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $db->transStart();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 523);
        $cabang = $session->get('cabang');
        if ($akses->udel == 1) {
            $Mutasi = $db->table("inv_mutasi_cabang")->getWhere(['id_mutasi_cabang' => $nomor, 'cabang' => $cabang])->getRow();
            if ($Mutasi != "") {
                $datadetail = $db->table('inv_mutasi_cabang_detail')->getWhere(['nomutasi_cabang' => $Mutasi->nomutasi_cabang])->getResult();
                foreach ($datadetail as $row) {
                    $db->query("UPDATE inv_mohond_mutasi_cabang set qtymutasi = qtymutasi - '$row->qty' where kodeitem = '$row->kodeitem' and nomohon = '$Mutasi->nomohon' and cabang='$Mutasi->cabang_tujuan'");
                }
            }

            $total = $db->query("SELECT sum(qtymohon-qtymutasi) as total from inv_mohond_mutasi_cabang where nomohon = '$Mutasi->nomohon' and gudang='$Mutasi->gudang_tujuan' and cabang='$Mutasi->cabang_tujuan' ")->getRow()->total;

            if ($total <= 0) {
                $db->table("inv_mohon_mutasi_cabang")->where(['nomohon' => $Mutasi->nomohon, 'cabang' => $Mutasi->cabang_tujuan, 'gudang_asal' => $Mutasi->gudang_tujuan])->update(['statusid' => 2]);
            } else {
                $db->table("inv_mohon_mutasi_cabang")->where(['nomohon' => $Mutasi->nomohon, 'cabang' => $Mutasi->cabang_tujuan, 'gudang_asal' => $Mutasi->gudang_tujuan])->update(['statusid' => 1, 'no_get' => 1]);
            }
            $db->table("inv_mutasi_cabang")->delete(['nomutasi_cabang' => $Mutasi->nomutasi_cabang, 'cabang' => $cabang]);
            $db->table("inv_mutasi_cabang_detail")->delete(['nomutasi_cabang' => $Mutasi->nomutasi_cabang, 'cabang' => $cabang]);
            $db->transComplete();
            echo json_encode(['message' => 'Success']);
        } else {

            json_encode(['message' => 'Gagal']);
        }
    }

    public function cetaktracer_mutasicabang($param1)
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
            $hp       = $profile->hp;
            $email       = $profile->email;

            $queryh = "SELECT a.*, b.nama AS cabangasal, b.alamat as alamatasal, c.nama AS cabangtujuan, c.alamat as alamattujuan, d.nama AS gudangasal, e.nama AS gudangtujuan 
						from inv_mohon_mutasi_cabang a
						inner join ms_unit b on a.cabang_asal=b.kode 
						inner join ms_unit c on a.cabang_tujuan=c.kode
						inner join inv_gudang d on a.gudang_asal=d.kode
						inner join inv_gudang e on a.gudang_tujuan=e.kode
						WHERE a.nomohon ='$param'  ";
            $header = $db->query($queryh)->getRow();
            $queryd = "SELECT a.namabarang, b.* from inv_barang a
						inner join inv_mohond_mutasi_cabang b on a.kodeitem=b.kodeitem 
						where b.nomohon = '$param' AND a.kdcbg='$header->cabang_tujuan' AND a.kdgudang='$header->gudang_tujuan'";

            $detil  = $db->query($queryd)->getResult();
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
            $pdf->FancyRow2(5, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(0);
            $pdf->SetWidths(array(70));
            $border = array('');
            $fc     = array('0', '0', '0', '0', '0');
            $pdf->SetFillColor(230, 230, 230);
            $pdf->setfont('Arial', '', 9);
            $pdf->FancyRow(array($header->cabangasal), $fc, $border);
            $pdf->FancyRow(array('No Pesanan : ' . $header->nomohon), $fc, $border);
            $pdf->ln(1);

            $pdf->SetWidths(array(10, 60, 10));
            $border = array('TB', 'TB', 'TB');
            $align  = array('L', 'L', 'R');
            $pdf->setfont('Arial', 'B', 10);
            $pdf->SetAligns(array('L', 'C', 'R'));
            $fc = array('0', '0', '0');
            $judul = array('No', 'Nama Barang', 'Qty');
            $pdf->FancyRow2(8, $judul, $fc, $border, $align);
            $border = array('', '', '');
            $pdf->setfont('Arial', '', 10);
            $tot = 0;
            $border = array('', '', '');
            $align  = array('L', 'L', 'R');
            $fc = array('0', '0', '0');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(0);
            $no = 1;
            $qty = 0;
            foreach ($detil as $db) {
                $hitung_qty = $db->qtymohon - $db->qtyterima;
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

    public function cetak_mutasicabang($param1)
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
            $hp       = $profile->hp;
            $email       = $profile->email;

            $queryh = "SELECT a.*, b.nama AS cabangasal, b.alamat as alamatasal, c.nama AS cabangtujuan, c.alamat as alamattujuan, d.nama AS gudangasal, e.nama AS gudangtujuan from inv_mutasi_cabang a
						inner join ms_unit b on a.cabang_asal=b.kode 
						inner join ms_unit c on a.cabang_tujuan=c.kode
						inner join inv_gudang d on a.gudang_asal=d.kode
						inner join inv_gudang e on a.gudang_tujuan=e.kode
						WHERE a.nomutasi_cabang ='$param'  ";
            $header = $db->query($queryh)->getRow();
            $queryd = "SELECT a.namabarang, b.* from inv_barang a
						inner join inv_mutasi_cabang_detail b on a.kodeitem=b.kodeitem 
						where b.nomutasi_cabang = '$param' AND a.kdcbg='$header->cabang_asal' AND a.kdgudang='$header->gudang_asal'";
            $update = $db->query("UPDATE inv_mutasi_cabang set cetak='1' where nomutasi_cabang='$param'");
            $detil  = $db->query($queryd)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('BT', '', 'BT');
            $size   = array('0', '0', '0');
            $pdf->setfont('Arial', 'B', 9);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('B', '', 'B');
            $size  = array('9', '0', '9');
            $max   = array(5, 5, 20);
            $judul = array('Mutasi Dari Cabang :', '', 'Mutasi Ke Cabang :');
            $fc     = array('0', '0', '0');

            $hc     = array('20', '20', '20');
            $pdf->image('./assets/images/logo_paper.jpg', 50, 60, 100, 100);
            $pdf->FancyRow2(7, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetWidths(array(85, 15, 23, 4, 30, 13, 3, 25));
            $border = array('', '', '', '', '', '', '');
            $fc     = array('0', '0', '0', '0', '0', '0', '0');
            $fc1     = array('0', '0', '0', '0', '0', '0', '0');
            $pdf->SetFillColor(230, 230, 230);
            $pdf->setfont('Arial', '', 7.5);
            $style = array('B', '', '', '', '', '', '');
            $head = array($header->cabangasal, '', 'Cabang Tujuan', ':', $header->cabangtujuan, 'Tanggal', ':', $header->tanggal);
            $pdf->FancyRow2(5, $head, $fc1,  $border, $align, $style);
            $pdf->ln(-2);
            $alamat = array($header->alamatasal, '', 'Alamat Tujuan', ':', $header->alamattujuan, 'Time', ':', date('H:i'));
            $pdf->FancyRow2(5, $alamat, $fc1,  $border, $align);
            $pdf->ln(-1);
            $nama_pt = array('', '', 'Nomor Mutasi', ':', $header->nomutasi_cabang, '', '', '');
            $pdf->FancyRow2(3, $nama_pt, $fc1,  $border, $align);
            $pdf->ln(2);

            $pdf->SetWidths(array(20, 90, 20, 20, 20, 20));
            $border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
            $align  = array('L', 'L', 'L', 'C', 'C', 'C');
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetAligns(array('L', 'C', 'R'));
            $fc = array('0', '0', '0', '0', '0', '0');
            $judul = array('Kode', 'Nama Barang', 'Batch', 'Tanggal EXP', 'Qty', 'Satuan');
            $pdf->FancyRow2(5, $judul, $fc, $border, $align);
            $border = array('', '', '');
            $pdf->setfont('Arial', '', 7.5);
            $tot = 0;
            $subtot = 0;
            $tdisc  = 0;
            $border = array('', '', '', '', '', '');
            $align  = array('L', 'L', 'L', 'C', 'C', 'C');
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(0);
            $tot = 0;
            $qty = 0;
            foreach ($detil as $db) {
                if ($db->qty >= 1) {
                    $qty = $qty + $db->qty;

                    $pdf->FancyRow(array(
                        $db->kodeitem,
                        $db->namabarang,
                        $db->nobatch,
                        $db->tglexp,
                        $db->qty,
                        $db->satuan
                    ), $fc, $border, $align);
                    $tot++;
                    $pdf->ln(-1.5);
                }
            }
            $pdf->ln(-4);
            $pdf->SetFillColor(230);
            $border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
            $pdf->FancyRow(array('', '', '', '', '', '', '', ''), 0, $border);
            $pdf->ln(1);
            $pdf->SetWidths(array(100, 20, 30, 40));
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $pdf->FancyRow(array('Keterangan', '', 'Total Kuantitas', $qty), $fc, $border, $align);
            $border = array('', '', 'B', 'B');
            $pdf->ln(0);
            $pdf->FancyRow(array($header->keterangan, '', 'Jumlah Barang', $tot), $fc, $border, $align);
            $border = array('B', '', '', '');
            $pdf->FancyRow(array('', '', '',), $fc, $border, $align);
            $pdf->SetWidths(array(50, 50, 50));
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $pdf->ln(1);
            $border = array('', '', '', '');
            $align  = array('C', 'C', 'C', 'C');
            $pdf->FancyRow(array('Hormat Kami', 'Disiapkan Oleh', 'Checker Oleh', 'Approved oleh'), 0, $border, $align);
            $pdf->ln(-1);
            $pdf->SetWidths(array(47, 1.5, 47, 5, 47, 0, 46));
            $pdf->SetFont('Arial', '', 8);
            $border = array('', '', '', '', '', '', '');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $pdf->FancyRow(array('Tgl. Nama', '', 'Tgl. Nama', '', 'Tgl. Nama', '', 'Tgl. Nama'), 0, $border, $align);

            $pdf->AliasNbPages();
            $pdf->output($param . '.PDF', 'I');
            exit;
        } else {

            header('location:' . base_url());
        }
    }
    //*******************Start line Persetujuan Mutasi CABANG************************//
    //Persetujuan Mutasi CABANG Page Start Here
    public function persetujuan_mutasi_cabang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Approval Mutasi Masuk Region',
            'body' => 'admin/inventory/transaksi/persetujuan-mutasi-cabang',
        ];
        return view('layout/layout', $data);
    }

    public function acc_mutasi_cabang_table_filter($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
		$lvldebug = $session->get('debug');
        $db = db_connect();
        $query = "SELECT * FROM inv_acc_mutasi_cabang where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];

        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 524);
        foreach ($res as $value) {
            $nameterima = $db->query("SELECT * FROM ms_unit where kode='$value->cabang_terima'")->getRow();
            $namekirim = $db->query("SELECT * FROM ms_unit where kode='$value->cabang_kirim'")->getRow();
            if ($value->statusid !== '1') {
                $status = '<span class="label label-md label-warning" style="color:white;"> Teretur </span>';
            } else if ($value->statusid == '1') {
                $status = '<span class="label label-md label-success" style="color:white;"> Sudah Disetujui </span>';
            }

            if ($akses->uedit == 1 && $akses->udel == 1) {
                $EditDel = '
                        <a class="btn" href="' . base_url('inventory/transaksi/persetujuanmutasicabang/entri/' . $value->idacc_mutasi_cabang) . '">
                            <i class="fa-solid fa-edit"></i>
                        </a>';
            } else if ($akses->uedit == 1 && $akses->udel == 0) {
                $EditDel = '
                        <a class="btn" href="' . base_url('inventory/transaksi/persetujuanmutasicabang/entri/' . $value->idacc_mutasi_cabang) . '">
                            <i class="fa-solid fa-edit"></i>
                        </a>';
            } else {
                $EditDel = '';
            }
            
            if($lvldebug == 1){
                $debug = '<a class="btn" onclick="ShowMessageLogs(\'' . $value->noacc_mutasi . '\')"><i class="fas fa-bug"></i></a>';
            }else{
                $debug = '';
            }
            $data[] = [
                $value->noacc_mutasi,
                $value->tglrekam,
                $namekirim->nama,
                $nameterima->nama,
                $value->keterangan,
                $status,
                $EditDel. $debug
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function persetujuanmutasicabangentri($id = '')
    {
        $session = session();
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $cabang = $session->get('cabang');
        $session = session();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Approval Mutasi Masuk Region',
            'body' => 'admin/inventory/transaksi/persetujuan-mutasi-cabang-entri',
            'cabang' => $session->get('cabang'),
            'id' => $id ?? '',
            'nomor' => $model_Global->_Autonomor('AMC'),
            'db' => $db
        ];
        return view('layout/layout', $data);
    }

    public function AccShowMc($kode)
    {
        $db = db_connect();
        $session = session();
        if (!empty($kode)) {
            $mc  = $db->query("SELECT * from inv_mutasi_cabang where statusid='1' or statusid='3' and cabang_tujuan='$kode'")->getResult();
        ?>
            <select name="nomutasi" id="nomutasi" class="form-control  input-medium select2">
                <option value="">-- PILIH NO MUTASI CABANG ---</option>
                <?php
                foreach ($mc  as $row) {
                ?>
                    <option value="<?php echo $row->nomutasi_cabang; ?>"><?php echo $row->nomutasi_cabang; ?></option>
                <?php   } ?>
            </select>

<?php

        } else {
            echo "";
        }
    }

    public function GetDataHeadMC($kode)
    {
        $db = db_connect();
        $session = session();
        $rowmutasi = $db->query("SELECT * FROM inv_mutasi_cabang where nomutasi_cabang='$kode'")->getRow();
        $getDataBarang = $db->query("SELECT a.*,  b.namabarang, b.stok, b.satuan
                                    from inv_mutasi_cabang_detail a
                                    inner join inv_barang b on b.kodeitem=a.kodeitem
                                    where a.nomutasi_cabang='$kode' and b.kdgudang='$rowmutasi->gudang_asal' and b.kdcbg='$rowmutasi->cabang_asal'")->getResult();
        $response = [
            'DataOne' => $rowmutasi,
            'DataTwo' => $getDataBarang
        ];
        echo json_encode($getDataBarang);
    }

    public function GetDataHeadMMC($kode)
    {
        $session = session();
        $db = db_connect();
        $data = $db->query("SELECT a.*,  b.nama from  inv_mutasi_cabang a
                            inner join ms_unit b on b.kode=a.cabang_asal where a.nomutasi_cabang='$kode'")->getResult();
        echo json_encode($data);
    }

    public function ACCMutasiCabang_save()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $db = db_connect();
        $db->transStart();
        $session = session();
        $param = $input['id'];
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 524);
        if ($akses->uadd == 1 || $akses->uedit == 1) {
            $cabang           = $session->get('cabang');
            $nomohon        = $input['nomohon'];
            $nomutasi       = $input['nomutasi'];
            $tanggal          = $input['tanggal'];
            $kode              = $input['kode'];
            $no_batch          = $input['no_batch'];
            $tgl_exp          = $input['tgl_exp'];
            $qty               = $input['qty'];
            $sat               = $input['sat'];
            //Tabel
            $builder = $db->table('inv_acc_mutasi_cabang');
            $builderD = $db->table('inv_accd_mutasi_cabang');
            $tablebarang = $db->table('inv_barang');
            $tablebatch = $db->table('inv_nobatch');
            $tableBarangGudang = $db->table('inv_barang_gudang');
            $tablemohondmutasicabang = $db->table('inv_mohond_mutasi_cabang');
            $tablemutasicabangd = $db->table('inv_mutasi_cabang_detail');
            $tableinvtransaksi = $db->table('inv_transaksi');
            $gudangkirim    = $db->table('inv_gudang')->where(['cabang' => $input['cabang_kirim']])->get()->getRow();
            $gudangterima   = $db->table('inv_gudang')->where(['cabang' => $input['cabang_terima']])->get()->getRow();

            $DataAccmutasiCabang = $builder->getWhere(['noacc_mutasi' => $input['nomorbukti'], 'cabang' => $cabang])->getRow();

            if (empty($DataAccmutasiCabang)) {
                $nobukti = $input['nomorbukti'];
            } else {
                $nobukti = $model_Global->_Autonomor('AMC');
            }

            $jumdata  = count($kode);

            $data_header = array(
                'nomutasi_cabang' => $input['nomutasi'],
                'nomohon' => $input['nomohon'],
                'cabang_terima' => $input['cabang_terima'],
                'cabang_kirim' => $input['cabang_kirim'],
                'gudang_terima' => $gudangterima->kode,
                'gudang_kirim' => $gudangkirim->kode,
                'keterangan' => $input['keterangan'],
                'tanggal' => $input['tanggal'],
                'tglrekam' => date('Y-m-d H:i:s'),
                'userid' => $session->get('username'),
                'statusid' => '1',
                'cabang' => $session->get('cabang'),
            );

            $datalogs = [
					'date' => date('Y-m-d H:i:s'),
					'userid' => $session->get('username'),
				];
            if ($param == 0) {
                $data_header['noacc_mutasi'] = $nobukti;
                $builder->insert($data_header);
                $datalogs['detail'] = 'Tambah data accept mutasi cabang dengan kode ' . $nobukti;
                $datalogs['nobukti'] = $nobukti;

                $db->table("logs")->insert($datalogs);
            }

            for ($i = 0; $i < $jumdata; $i++) {
                $barang = $tablebarang->where(['kodeitem' => $kode[$i], 'kdcbg' => $input['cabang_kirim']])->get()->getRow();

                $datad = array(
                    'kodeitem' => $kode[$i],
                    'nobatch' => $no_batch[$i],
                    'tglexp' => $tgl_exp[$i],
                    'qtyacc' => $qty[$i],
                    'satuan' => $sat[$i],
                    'harga' => $barang->hargabeli,
                    'cabang' => $cabang,
                );

                $inv_barang_gudang = array(
                    'kodeitem' => $kode[$i],
                    'gudang' => $gudangterima->kode,
                    'qty' => $qty[$i],
                    'cabang' => $cabang,
                );

                $inv_nobatch = array(
                    'no_batch' => $no_batch[$i],
                    'kd_jenis' => 'Mutasi Cabang',
                    'kodeitem' => $kode[$i],
                    'qty_terima' => $qty[$i],
                    'qty_saldo' => $qty[$i],
                    'gudang' => $gudangterima->kode,
                    'cabang' => $cabang,
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
                    'kdgudang' => $gudangterima->kode,
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

                if ($kode[$i] != "") {
                    if ($param == 0) {
                        $rowmohon = $tablemohondmutasicabang->where(['kodeitem' => $kode[$i], 'nomohon' => $nomohon, 'cabang' => $cabang])->get()->getRow();
                        $rowmutasi = $tablemutasicabangd->where(['kodeitem' => $kode[$i], 'nobatch' => $no_batch[$i], 'nomutasi_cabang' => $nomutasi, 'cabang' => $input['cabang_kirim']])->get()->getRow();

                        $datad['noacc_mutasi'] = $nobukti;
                        $builderD->insert($datad);
                        $tablemohondmutasicabang->where(['kodeitem' => $kode[$i], 'nomohon' => $nomohon, 'cabang' => $cabang])->update(['qtyterima' => $rowmohon->qtyterima + $qty[$i]]);
                        $tablemutasicabangd->where(['kodeitem' => $kode[$i], 'nobatch' => $no_batch[$i], 'nomutasi_cabang' => $nomutasi, 'cabang' => $input['cabang_kirim']])->update(['qtysetuju' => $rowmutasi->qtysetuju + $qty[$i]]);


                        $cek_barang = $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangterima->kode, 'kdcbg' => $cabang])->get()->getRow();
                        $GudangBarangKirim = $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $input['cabang_kirim']])->get()->getRow();

                        $cek_nobatch = $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => $cabang, 'gudang' => $gudangterima->kode])->get()->getRow();
                        $Batchkirim = $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'cabang' => $input['cabang_kirim'], 'gudang' => $gudangkirim->kode])->get()->getRow();

                        $cekBrgGudang = $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->get()->getRow();
                        $BarangGudangKirim = $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $input['cabang_kirim']])->get()->getRow();
                        if ($BarangGudangKirim == null) {
                            $qtyBarangGudang = 0;
                        } else {
                            $qtyBarangGudang = $BarangGudangKirim->qty;
                        }

                        //update dan insert inv_barang
                        if (!empty($cek_barang)) {
                            $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangterima->kode, 'kdcbg' => $cabang])->update(['stok' => $cek_barang->stok + $qty[$i], 'stok_so' => $cek_barang->stok_so + $qty[$i]]);
                            $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $input['cabang_kirim']])->update(['stok' => $GudangBarangKirim->stok -  $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so - $qty[$i]]);
                        } else {
                            $tablebarang->insert($inv_barang);
                            $tablebarang->where(['kodeitem' => $kode[$i], 'kdgudang' => $gudangkirim->kode, 'kdcbg' => $input['cabang_kirim']])->update(['stok' => $GudangBarangKirim->stok - $qty[$i], 'stok_so' => $GudangBarangKirim->stok_so + $qty[$i]]);
                        }

                        //update dan insert inv_nobatch
                        if (!empty($cek_nobatch)) {
                            $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $input['cabang_kirim']])->update(['qty_jual' => $Batchkirim->qty_jual + $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
                            $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->update(['qty_terima' => $cek_nobatch->qty_terima + $qty[$i], 'qty_saldo' => $cek_nobatch->qty_saldo + $qty[$i]]);
                        } else {
                            $tablebatch->insert($inv_nobatch);
                            $tablebatch->where(['kodeitem' => $kode[$i], 'no_batch' => $no_batch[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $input['cabang_kirim']])->update(['qty_jual' => $Batchkirim->qty_jual - $qty[$i], 'qty_saldo' => $Batchkirim->qty_saldo - $qty[$i]]);
                        }

                        //update dan insert inv_barang_gudang
                        if (!empty($cekBrgGudang)) {
                            $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $input['cabang_kirim']])->update(['qty' => $qtyBarangGudang -  $qty[$i]]);
                            $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangterima->kode, 'cabang' => $cabang])->update(['qty' => $cekBrgGudang->qty + $qty[$i]]);
                        } else {
                            $tableBarangGudang->insert($inv_barang_gudang);
                            $tableBarangGudang->where(['kodeitem' => $kode[$i], 'gudang' => $gudangkirim->kode, 'cabang' => $input['cabang_kirim']])->update(['qty' => $qtyBarangGudang -  $qty[$i]]);
                        }


                        $data_inv = array(
                            'tanggal'   => $tanggal,
                            'kodeitem' => $kode[$i],
                            'penerimaan' => 0,
                            'pengeluaran' => $qty[$i],
                            'satuan' => $sat[$i],
                            'jenis' => 'Mutasi Cabang',
                            'nobukti' => $nobukti,
                            'tglrekam'   => date('Y-m-d H:i:s'),
                            'hpp'   => $barang->hargabeli,
                            'hargajual'   => 0,
                            'gudang' => $gudangkirim->kode,
                            'no_batch' => $no_batch[$i],
                            'tanggal_exp' => $tgl_exp[$i],
                            'cabang' => $input['cabang_kirim'],
                        );
                        $tableinvtransaksi->insert($data_inv);

                        $data_inv_terima = array(
                            'tanggal'   => $tanggal,
                            'kodeitem' => $kode[$i],
                            'penerimaan' =>  $qty[$i],
                            'pengeluaran' => 0,
                            'satuan' => $sat[$i],
                            'jenis' => 'Mutasi Cabang',
                            'nobukti' => $nobukti,
                            'tglrekam'   => date('Y-m-d H:i:s'),
                            'hpp'   => $barang->hargabeli,
                            'hargajual'   => 0,
                            'gudang' => $gudangterima->kode,
                            'no_batch' => $no_batch[$i],
                            'tanggal_exp' => $tgl_exp[$i],
                            'cabang' => $cabang,
                        );

                        $tableinvtransaksi->insert($data_inv_terima);
                    }
                    $DateNow = date('Y-m-d');
                    $oneHundredTwentyDaysAgoPusat = date('Y-m-d', strtotime('-120 days'));
                    $oneHundredsixtyDaysAgoPusat = date('Y-m-d', strtotime('-160 days'));
                    $SixtyDaysAgoCabang = date('Y-m-d', strtotime('-30 days'));
                    $NinetyDaysAgoCabang = date('Y-m-d', strtotime('-60 days'));
                    //CABANG KIRIM
                    $GetQtyKeluarTwentyDaysAgo = $db->table('inv_transaksi');
                                                $GetQtyKeluarTwentyDaysAgo->select('SUM(pengeluaran) as total');
                                                $GetQtyKeluarTwentyDaysAgo->where('kodeitem', $kode[$i]);
                                                $GetQtyKeluarTwentyDaysAgo->where('cabang', $input['cabang_kirim']);
                                                if($input['cabang_kirim'] == '01'){
                                                    $GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $oneHundredTwentyDaysAgoPusat);
                                                } else {
                                                    $GetQtyKeluarTwentyDaysAgo->where('tanggal >=', $SixtyDaysAgoCabang);
                                                }
                                                $GetQtyKeluarTwentyDaysAgo->where('tanggal <=', $DateNow);
                    $ResultTwentysDayAgo = $GetQtyKeluarTwentyDaysAgo->get()->getRow();

                    $GetQtyKeluarSixtyDaysAgo = $db->table('inv_transaksi');
                                                $GetQtyKeluarSixtyDaysAgo->select('SUM(pengeluaran) as total');
                                                $GetQtyKeluarSixtyDaysAgo->where('kodeitem', $kode[$i]);
                                                $GetQtyKeluarSixtyDaysAgo->where('cabang', $input['cabang_kirim']);
                                                if($input['cabang_kirim'] == '01'){
                                                    $GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $oneHundredsixtyDaysAgoPusat);
                                                } else {
                                                    $GetQtyKeluarSixtyDaysAgo->where('tanggal >=', $NinetyDaysAgoCabang);
                                                }
                                                $GetQtyKeluarSixtyDaysAgo->where('tanggal <=', $DateNow);
                    $ResultSixtyDayAgo = $GetQtyKeluarSixtyDaysAgo->get()->getRow();
                    
                    $buildPaket 	    = $db->table('inv_barang_paket');
                    $DataPaket 		    = $buildPaket->getWhere(['kodeitem_barang' => $kode[$i], 'cabang' => $input['cabang_kirim']])->getRow();
                    $DataPaketDetail    = $buildPaket->getWhere(['kodeitem_paket' => $kode[$i], 'cabang' => $input['cabang_kirim']])->getRow();
                    if(empty($DataPaket)){
                        if(empty($DataPaketDetail)){
                            $db->table("inv_barang")
                                ->where(['kodeitem' => $kode[$i], 'kdcbg' => $input['cabang_kirim'], 'kdgudang' => $gudangkirim->kode])
                                ->update([
                                        'stok_min' => $ResultTwentysDayAgo->total,
                                        'stok_max' => $ResultSixtyDayAgo->total,
                                        ]);
                        }
                    }

                    //CABANG TERIMA
                    $GetQtyKeluarTwentyDaysAgoTerima = $db->table('inv_transaksi');
                                                $GetQtyKeluarTwentyDaysAgoTerima->select('SUM(pengeluaran) as total');
                                                $GetQtyKeluarTwentyDaysAgoTerima->where('kodeitem', $kode[$i]);
                                                $GetQtyKeluarTwentyDaysAgoTerima->where('cabang', $cabang);
                                                if($cabang == '01'){
                                                    $GetQtyKeluarTwentyDaysAgoTerima->where('tanggal >=', $oneHundredTwentyDaysAgoPusat);
                                                } else {
                                                    $GetQtyKeluarTwentyDaysAgoTerima->where('tanggal >=', $SixtyDaysAgoCabang);
                                                }
                                                $GetQtyKeluarTwentyDaysAgoTerima->where('tanggal <=', $DateNow);
                    $ResultTwentysDayAgoTerima = $GetQtyKeluarTwentyDaysAgoTerima->get()->getRow();

                    $GetQtyKeluarSixtyDaysAgoTerima = $db->table('inv_transaksi');
                                                $GetQtyKeluarSixtyDaysAgoTerima->select('SUM(pengeluaran) as total');
                                                $GetQtyKeluarSixtyDaysAgoTerima->where('kodeitem', $kode[$i]);
                                                $GetQtyKeluarSixtyDaysAgoTerima->where('cabang', $cabang);
                                                if($cabang == '01'){
                                                    $GetQtyKeluarSixtyDaysAgoTerima->where('tanggal >=', $oneHundredsixtyDaysAgoPusat);
                                                } else {
                                                    $GetQtyKeluarSixtyDaysAgoTerima->where('tanggal >=', $NinetyDaysAgoCabang);
                                                }
                                                $GetQtyKeluarSixtyDaysAgoTerima->where('tanggal <=', $DateNow);
                    $ResultSixtyDayAgoTerima = $GetQtyKeluarSixtyDaysAgoTerima->get()->getRow();
                    
                    $DataPaketTerima        = $buildPaket->getWhere(['kodeitem_barang' => $kode[$i], 'cabang' => $cabang])->getRow();
                    $DataPaketTerimaDetail  = $buildPaket->getWhere(['kodeitem_paket' => $kode[$i], 'cabang' => $cabang])->getRow();
                    if(empty($DataPaketTerima)){
                        if(empty($DataPaketTerimaDetail)){
                            $db->table("inv_barang")
                                ->where(['kodeitem' => $kode[$i], 'kdcbg' => $cabang, 'kdgudang' => $gudangterima->kode])
                                ->update([
                                        'stok_min' => $ResultTwentysDayAgoTerima->total,
                                        'stok_max' => $ResultSixtyDayAgoTerima->total,
                                        ]);
                        }
                    }
                }
            }
            if ($param == 0) {
                //update status tbl mohon
                $data_mohon = $db->query("SELECT SUM(qtymohon) AS qtymohon, SUM(qtyterima) AS qtyterima FROM inv_mohond_mutasi_cabang where nomohon='$nomohon' and cabang='$cabang'")->getRow();
                if ($data_mohon->qtyterima >= $data_mohon->qtymohon) {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_cabang set statusid='4', no_get='2' where nomohon = '$nomohon' and cabang='$cabang'");
                } else {
                    $update_mohon = $db->query("UPDATE inv_mohon_mutasi_cabang set statusid='3' where nomohon = '$nomohon' and cabang='$cabang'");
                }
                //update status tbl mutasi
                $cbgKirim = $input['cabang_kirim'];
                $data_mutasi = $db->query("SELECT SUM(qty) AS qtykirim, SUM(qtysetuju) AS qtysetuju FROM inv_mutasi_cabang_detail where nomutasi_cabang='$nomutasi' and cabang='$cbgKirim'")->getRow();
                if ($data_mutasi->qtysetuju >= $data_mutasi->qtykirim) {
                    $update_mutasi = $db->query("UPDATE inv_mutasi_cabang set statusid='2' where nomutasi_cabang = '$nomutasi' and cabang='$cbgKirim'");
                } else {
                    $update_mutasi = $db->query("UPDATE inv_mutasi_cabang set statusid='3' where nomutasi_cabang = '$nomutasi' and cabang='$cbgKirim'");
                }
                $model_Global->_updatecounter1('AMC');
            }

            echo json_encode($nobukti);
        } else {
            json_encode(['message' => 'Gagal']);
        }
        $db->transComplete();
    }

    public function cetak_acccabang($param1)
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
            $hp       = $profile->hp;
            $email       = $profile->email;

            $queryh = "SELECT a.*, b.nama AS cabangterima, b.alamat as alamatterima, c.nama AS cabangkirim, c.alamat as alamatkirim, d.nama AS gudangasal, e.nama AS gudangtujuan from inv_acc_mutasi_cabang a
                         inner join ms_unit b on a.cabang_terima=b.kode 
                         inner join ms_unit c on a.cabang_kirim=c.kode
                         inner join inv_gudang d on a.gudang_terima=d.kode
                         inner join inv_gudang e on a.gudang_kirim=e.kode
                         WHERE a.noacc_mutasi ='$param'  ";
            $header = $db->query($queryh)->getRow();
            $queryd = "SELECT a.namabarang, b.* from inv_barang a
                         inner join inv_accd_mutasi_cabang b on a.kodeitem=b.kodeitem 
                         where b.noacc_mutasi = '$param' AND a.kdcbg='$header->cabang_terima' AND a.kdgudang='$header->gudang_terima'";

            $detil  = $db->query($queryd)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('BT', '', 'BT');
            $size   = array('0', '0', '0');
            $pdf->setfont('Arial', 'B', 9);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('B', '', 'B');
            $size  = array('9', '0', '9');
            $max   = array(5, 5, 20);
            $judul = array('Pengirim Dari Cabang :', '', 'Approval dari Cabang :');
            $fc     = array('0', '0', '0');

            $hc     = array('20', '20', '20');
            $pdf->image('./assets/images/logo_paper.jpg', 50, 60, 100, 100);
            $pdf->FancyRow2(7, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetWidths(array(85, 15, 23, 4, 30, 13, 3, 25));
            $border = array('', '', '', '', '', '', '');
            $fc     = array('0', '0', '0', '0', '0', '0', '0');
            $fc1     = array('0', '0', '0', '0', '0', '0', '0');
            $pdf->SetFillColor(230, 230, 230);
            $pdf->setfont('Arial', '', 7.5);
            $style = array('B', '', '', '', '', '', '');
            $head = array($header->cabangkirim, '', 'Cabang Terima', ':', $header->cabangterima, 'Tanggal', ':', $header->tanggal);
            $pdf->FancyRow2(5, $head, $fc1,  $border, $align, $style);
            $pdf->ln(-2);
            $alamat = array($header->alamatkirim, '', 'Alamat Terima', ':', $header->alamatterima, 'Time', ':', date('H:i'));
            $pdf->FancyRow2(5, $alamat, $fc1,  $border, $align);
            $pdf->ln(-1);
            $nama_pt = array('', '', 'Nomor Approval', ':', $header->noacc_mutasi, '', '', '');
            $pdf->FancyRow2(3, $nama_pt, $fc1,  $border, $align);
            $pdf->ln(2);

            $pdf->SetWidths(array(20, 90, 20, 20, 20, 20));
            $border = array('TB', 'TB', 'TB', 'TB', 'TB', 'TB');
            $align  = array('L', 'L', 'L', 'C', 'C', 'C');
            $pdf->setfont('Arial', 'B', 7.5);
            $pdf->SetAligns(array('L', 'C', 'R'));
            $fc = array('0', '0', '0', '0', '0', '0');
            $judul = array('Kode', 'Nama Barang', 'Batch', 'Tanggal EXP', 'Qty', 'Satuan');
            $pdf->FancyRow2(5, $judul, $fc, $border, $align);
            $border = array('', '', '');
            $pdf->setfont('Arial', '', 7.5);
            $tot = 0;
            $border = array('', '', '', '', '', '');
            $align  = array('L', 'L', 'L', 'C', 'C', 'C');
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(0);
            $tot = 0;
            $qty = 0;
            foreach ($detil as $db) {
                if ($db->qtyacc >= 1) {
                    $qty = $qty + $db->qtyacc;

                    $pdf->FancyRow(array(
                        $db->kodeitem,
                        $db->namabarang,
                        $db->nobatch,
                        $db->tglexp,
                        $db->qtyacc,
                        $db->satuan
                    ), $fc, $border, $align);
                    $tot++;
                    $pdf->ln(-1.5);
                }
            }
            $pdf->ln(-4);
            $pdf->SetFillColor(230);
            $border = array('B', 'B', 'B', 'B', 'B', 'B', 'B', 'B');
            $pdf->FancyRow(array('', '', '', '', '', '', '', ''), 0, $border);
            $pdf->ln(1);
            $pdf->SetWidths(array(100, 20, 30, 40));
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $border = array('TB', '', 'T', 'T');
            $align  = array('L', '', 'L', 'R');
            $pdf->FancyRow(array('Keterangan', '', 'Total Kuantitas', $qty), $fc, $border, $align);
            $border = array('', '', 'B', 'B');
            $pdf->ln(0);
            $pdf->FancyRow(array($header->keterangan, '', 'Jumlah Barang', $tot), $fc, $border, $align);
            $border = array('B', '', '', '');
            $pdf->FancyRow(array('', '', '',), $fc, $border, $align);
            $pdf->SetWidths(array(50, 50, 50));
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $pdf->ln(1);
            $border = array('', '', '', '');
            $align  = array('C', 'C', 'C', 'C');
            $pdf->FancyRow(array('', '', '', 'Approved oleh'), 0, $border, $align);
            $pdf->ln(-1);
            $pdf->SetWidths(array(47, 1.5, 47, 5, 47, 0, 46));
            $pdf->SetFont('Arial', '', 8);
            $border = array('', '', '', '', '', '', '');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
            $pdf->FancyRow(array('', '', '', '', '', '', 'Tgl. Nama'), 0, $border, $align);

            $pdf->AliasNbPages();
            $pdf->output($param . '.PDF', 'I');
            exit;
        } else {
            header('location:' . base_url());
        }
    }

    //*******************Start line LAPORAN INVENTORY************************//
    //Laporan Page Start Here
    public function laporan()
    {
        $session = session();
        $db = db_connect();
        $cabang = $db->table("ms_unit")->get()->getResult();
        $kelbrg = $db->table("inv_kategori")->get()->getResult();
        $barang = $db->table("inv_barang")->getWhere(['kdcbg' => $session->get('cabang')])->getResult();
        $BarangHarga = $db->table("inv_barang_harga")
                        ->select('*') // Pilih kolom yang diinginkan
                        ->where(['cabang' => $session->get('cabang')])
                        ->groupBy('profit_pr') // Tambahkan pengelompokan berdasarkan profit_pr
                        ->get()
                        ->getResult();

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Laporan',
            'body' => 'admin/inventory/laporan',
            'cbg' => $cabang,
            'kateg' => $kelbrg,
            'barang' => $barang,
            'BarangHarga' => $BarangHarga
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
            $data       = explode("~", $x);
            $idlp       = $data[0];
            $tgl1       = $data[1];
            $tgl2       = $data[2];
            $kat        = $data[3];
            $brg        = $data[4];
            $cab        = $data[5];
            $gud        = $data[6];
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
                $db = db_connect();
                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Barang');
                $pdf->setsubjudul($_peri1);
                $pdf->addpage("L", "A4");
                $pdf->setsize("L", "A4");
                $pdf->SetWidths(array(20, 5, 100));
                $pdf->SetAligns(array('C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 10);
                $border = array('', '', '');
                if ($cab != "") {
                    $pdf->FancyRow(array('Region ', ':', $db->table('ms_unit')->getWhere(['kode' => $cab])->getRow()->nama), $border);
                } else {
                    $pdf->FancyRow(array('Region ', ':', 'Semua Region'), $border);
                }
                if ($gud != '') {
                    $pdf->FancyRow(array('Gudang ', ':', $db->table('inv_gudang')->getWhere(['kode' => $gud])->getRow()->nama), $border);
                } else {
                    $pdf->FancyRow(array('Gudang ', ':', 'Semua Gudang'), $border);
                }
                $pdf->ln(2);
                $pdf->SetWidths(array(10, 20, 70, 20, 15, 25, 25, 25, 20, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Kode ', 'Nama Barang', 'Kuantitas', 'Satuan','NIE','Tgl Exp NIE','Stok Min','Stok Max', 'Harga Jual', 'Harga Beli');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->setheadertable($judul);
                $pdf->SetWidths(array(10, 20, 70, 20, 15, 25, 25, 25, 20, 25, 25, 25));
                $pdf->SetAligns(array('C', 'L', 'L', 'R', 'L', 'R', 'R', 'C', 'C', 'R', 'R', 'C'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $nourut = 1;

                foreach ($lap as $value) {
                    $db = db_connect();
                    $Vbarang = $db->query("SELECT sum(stok) as totalqty from inv_barang where kodeitem='$value->kodeitem'")->getRow();
                    if ($cab != "") {
                        $stok = $value->stok;
                    } else {
                        $stok = $Vbarang->totalqty;
                    }
                    $pdf->row(array(
                        $nourut, 
                        $value->kodeitem, 
                        $value->namabarang, 
                        $stok, 
                        $value->satuan,
                        $value->nie, 
                        $value->tglexpnie, 
                        number_format($value->stok_min),
                        number_format($value->stok_max),
                        number_format($value->hargajual, 0, '.', ','),
                        number_format($value->hargabeli, 0, '.', ',')
                    ));
                    $nourut++;
                }

                $pdf->SetTitle('DAFTAR BARANG');
                $pdf->AliasNbPages();
                $pdf->output('INV_101.PDF', 'I');
                exit;
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

                $db = db_connect();
                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Kartu Stok Barang');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("L", "A4");
                $pdf->setsize("L", "A4");
                $pdf->SetWidths(array(30, 5, 100));
                $pdf->SetAligns(array('C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 10);
                $border = array('', '', '');
                $pdf->FancyRow(array('Kode Barang ', ':', $brg), $border);
                $pdf->FancyRow(array('Nama Barang ', ':', $db->table('inv_barang')->getWhere(['kodeitem' => $brg])->getRow()->namabarang), $border);
                if ($gud != "") {
                    $pdf->FancyRow(array('Gudang ', ':', $db->table('inv_gudang')->getWhere(['kode' => $gud])->getRow()->nama), $border);
                }
                $pdf->ln(2);
                $pdf->SetWidths(array(10, 30, 32, 65, 13, 13, 13, 25, 30, 25, 20));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Tanggal ', 'Nomor', 'Nama Customer', 'Masuk', 'Keluar', 'SO', 'Saldo', 'Harga', 'Batch', 'ED');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->setheadertable($judul);
                $pdf->SetWidths(array(10, 30, 32, 65, 13, 13, 13, 25, 30, 25, 20));
                $pdf->SetAligns(array('C', 'C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'C', 'C'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $nourut = 1;
                $totkeluar = 0;
                $totmasuk = 0;
                $totso = 0;
                $so = 0;
                $saldo = 0;

                if ($gud != "") {
                    $invgud = " and gudang = '$gud' ";
                }
                if ($cab != "") {
                    $invcab = " and cabang = '$cab' ";
                }

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

                $pdf->row(array(
                    $nourut, date('d-m-Y H:i:s', strtotime($_tgl1)),
                    'SALDO AWAL',
                    '',
                    '',
                    '',
                    '',
                    number_format($saldo, 0, '.', ','),
                    number_format($nilaisaldo, 0, '.', ','),
                    '',
                    ''
                ));

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

                        $pdf->row(array(
                            $nourut, date('d-m-Y H:i:s', strtotime($tglrekam)),
                            $nomor_bukti,
                            $vendor,
                            number_format($terima, 0, '.', ','),
                            number_format($keluar, 0, '.', ','),
                            number_format($so, 0, '.', ','),
                            number_format($saldo, 0, '.', ','),
                            number_format($nilaisaldo, 0, '.', ','),
                            $row->no_batch,
                            $row->tanggal_exp
                        ));
                    }

                    $nourut++;
                    $totkeluar = $totkeluar + $row->pengeluaran;
                    $totmasuk = $totmasuk + $row->penerimaan;
                    $totso = $totso + $row->so;
                }



                $pdf->SetWidths(array(137, 13, 13, 13, 25, 30, 25, 20));
                $pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->row(array(
                    'TOTAL',
                    number_format($totmasuk, 0, ',', '.'),
                    number_format($totkeluar, 0, ',', '.'),
                    number_format($totso, 0, ',', '.'),
                    '',
                    '',
                    '',
                    ''
                ));

                $pdf->SetTitle('KARTU STOK');
                $pdf->AliasNbPages();
                $pdf->output('INV_102.PDF', 'I');
                exit;
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
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Umur Persediaan');
                $pdf->setsubjudul($_peri1);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetWidths(array(10, 20, 50, 15, 15, 15, 15, 15, 15, 15));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Kode Barang ', 'Nama Barang', 'Stok', 'Satuan', ' 1 - 30', '31 - 60', '61 - 90', '91 - 120', '> 120');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 20, 50, 15, 15, 15, 15, 15, 15, 15));
                $pdf->SetAligns(array('C', 'C', 'L', 'R', 'L', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;

                foreach ($lap as $db) {

                    $stok1 = 0;
                    $stok2 = 0;
                    $stok3 = 0;
                    $stok4 = 0;
                    $stok5 = 0;

                    $pdf->row(array(
                        $nourut, $db->kodeitem, $db->namabarang, $db->stok, $db->satuan,
                        $stok1, $stok2, $stok3, $stok4, $stok5
                    ));

                    $nourut++;
                }

                $pdf->SetTitle('UMUR PERSEDIAAN BARANG');
                $pdf->AliasNbPages();
                $pdf->output('INV_103.PDF', 'I');
                exit;
            }
            // LAPORAN MUTASI CABANG
            else if ($idlp == 104) {
                $query =
                    "SELECT * from inv_mutasi_cabang where tanggal between '$_tgl1' and '$_tgl2' ";

                if ($cab != "") {
                    $query .= "and cabang_asal = '$cab'";
                }
                $query .= "group by cabang_asal,nomutasi_cabang order by nomutasi_cabang";
                $db = db_connect();
                $lap = $db->query($query)->getResult();

                $namcab  = $db->query("SELECT * from inv_mutasi_cabang where tanggal between '$_tgl1' and '$_tgl2'")->getRow();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Laporan Mutasi Region');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetFont('');
                foreach ($lap as $value) {
                    $db = db_connect();
                    $pdf->setfont('Times', 'B', 10);
                    $border = array('', '', '');
                    $align = array('L', '', '', 'R');
                    $font  = array('B', '', '', 'B');
                    $pdf->SetWidths(array(23, 30, 15, 30, 30));
                    $pdf->ln(5);

                    $pdf->Fancyrow(array('Mutasi Asal : ', $db->table('ms_unit')->getWhere(['kode' => $value->cabang_asal])->getRow()->nama, '>>>', ' Mutasi Tujuan : ', $db->table('ms_unit')->getWhere(['kode' => $value->cabang_tujuan])->getRow()->nama), $border, $align);
                    $pdf->ln(-4);

                    $nourut = 1;
                    $cabangD = "SELECT * from inv_mutasi_cabang_detail a inner join inv_barang b on a.kodeitem = b.kodeitem where nomutasi_cabang = '$value->nomutasi_cabang' GROUP BY a.nobatch, a.kodeitem";
                    $lap1   = $db->query($cabangD)->getResult();
                    $pdf->SetWidths(array(10, 35, 25, 90, 20));
                    $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
                    $judul = array('No.', 'No Butkti', 'Tanggal', 'Nama Barang', 'Qty');
                    $pdf->setfont('Times', 'B', 10);
                    $pdf->row($judul);
                    $pdf->setheadertable($judul);
                    $pdf->SetWidths(array(10, 35, 25, 90, 20));
                    $pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
                    $pdf->setfont('Times', '', 9);
                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');

                    foreach ($lap1 as $row) {
                        $pdf->row(array(
                            $nourut,
                            $row->nomutasi_cabang,
                            $value->tanggal,
                            $row->namabarang,
                            $row->qty
                        ));
                        $nourut++;
                    }
                }
                $pdf->SetTitle('LAPORAN MUTASI REGION');
                $pdf->AliasNbPages();
                $pdf->output('Laporan_Mutasi_Region.PDF', 'I');
                exit;
            }
            // LAPORAN PROFIT MARGIN
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
                $db = db_connect();
                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Laporan Profit Margin');
                $pdf->setsubjudul($_peri2);
                $pdf->addpage("L", "A4");
                $pdf->setsize("L", "A4");
                $pdf->SetWidths(array(20, 5, 100));
                $pdf->SetAligns(array('C', 'C', 'C'));
                $pdf->setfont('Times', 'B', 10);
                $border = array('', '', '');
                $pdf->ln(2);
                $pdf->SetWidths(array(10, 65, 65, 20, 20, 20, 20, 20, 20, 20));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Nama Barang', 'Customer', 'Bruto', 'Disc', 'Netto', 'Partisi 1', 'Partisi 2', 'Partisi 3', 'Profit %');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->setheadertable($judul);
                $pdf->SetWidths(array(10, 65, 65, 20, 20, 20, 20, 20, 20, 20));
                $pdf->SetAligns(array('C', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $nourut = 1;

                foreach ($lap as $value) {
                    $pdf->row(array(
                        $nourut, 
                        $value->nama_barang, 
                        $value->nama_customer, 
                        number_format($value->bruto_cust, 0, '.', ','),
                        $value->diskon_pr.' %',
                        number_format($value->netto_cust, 0, '.', ','),
                        $value->fee1_pr.' %',
                        $value->fee2_pr.' %',
                        $value->fee3_pr.' %',
                        number_format($value->profit_pr, 0, '.', ',').' %',
                        
                    ));
                    $nourut++;
                }

                $pdf->SetTitle('LAPORAN PROFIT MARGIN');
                $pdf->AliasNbPages();
                $pdf->output('INV_105.PDF', 'I');
                exit;
            }
                
        } else {
            header('location:' . base_url());
        }
    }

    //*******************Start line IMPORT DATA BARANG************************//
    //Import Data Barang Page Start Here
    public function import_data_barang()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Import Data Barang',
            'body' => 'admin/inventory/import-data-barang',
        ];
        return view('layout/layout', $data);
    }

    //Data Stock Page Start Here
    public function data_stock()
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
            'title' => 'Data Stock',
            'body' => 'admin/inventory/exportData/data-steck',
        ];
        return view('layout/layout', $data);
    }

    public function exportstock_table($gudang)
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
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
        if ($gudang != 0) {
            $dataunit = $db->table("inv_gudang")->where(['kode' => $gudang])->get()->getRow();
            $query .= " and b.kdcbg='$dataunit->cabang' and b.kdgudang='$gudang'";
        } else {
            $query .= "";
        }
        $query .= " order by namabarang";
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        foreach ($res as $row) {
            $qty_so    = (float) ($row->qty_so ?? 0);
            $stok      = (float) ($row->stok ?? 0);
            $qty_order = (float) ($row->qty_order ?? 0);
            $qty_jual  = (float) ($row->qty_jual ?? 0);

            $dpp = $qty_jual * ((float) ($row->hpp_jual ?? 0));
            $laba = $dpp * (((float) ($row->disc_jual ?? 0)) / 100);

            $netto_spl_val = (float) ($row->netto_spl ?? 0);
            $nilai_stock   = $netto_spl_val * $stok;
            $data[] = [
                $row->kodeitem,
                $row->kode_ereport,
                $row->namabarang,
                $row->satuan,
                $qty_so,
                $stok,
                $qty_order,
                number_format($row->hargabeli),
                $nilai_stock,
                number_format($row->stok_min),
                number_format($row->stok_max),
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
                number_format($row->bruto_spl1),
                number_format($row->bruto_spl2),
                number_format($row->bruto_spl3),
                number_format($row->bruto_spl4),
                number_format($row->disc_spl1),
                number_format($row->disc_spl2),
                number_format($row->disc_spl3),
                number_format($row->disc_spl4),
                number_format($row->netto_spl1),
                number_format($row->netto_spl2),
                number_format($row->netto_spl3),
                number_format($row->netto_spl4)
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function data_harga_percust()
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
            'title' => 'Data Harga Percustomer',
            'body' => 'admin/inventory/exportData/export_harga_percustomer',
        ];
        return view('layout/layout', $data);
    }

    public function exporthargapercust_table()
    {
        $request = Services::request();
        $cekdata = $request->getPost('draw');
        $cabang = $request->getPost('cabang');
        $cari = $request->getPost('search')['value'];
        $datatable = new Harga_barang_model($request);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $level = $session->get('level');

        $akses = $model_Global->cek_menu_akses($level, 562);
        $db = db_connect();
        $query = "SELECT * FROM inv_barang_harga where kodeitem !=''";
        if ($cabang != 0) {
            $query .= " and cabang='$cabang' ";
        } else {
            $query .= "";
        }
        $query .= " order by nama_barang";
        $res = $db->query($query)->getResult();
        $data = [];
        foreach ($res as $value) {
            if ($akses->uharga == 1) {
                $brutocs = number_format($value->bruto_cust);
                $disc = $value->diskon_pr . '%';
                $netto = number_format($value->hargajual_net);
            } else {
                $brutocs = '0';
                $disc = '0%';
                $netto = '0';
            }

            if ($akses->uhargaprofit == 1) {
                $fee1 = $value->fee1_pr . '%';
                $fee2 = $value->fee2_pr . '%';
                $fee3 = $value->fee3_pr . '%';
                $profit = number_format($value->profit_pr) . '%';
            } else {
                $fee1 = '0%';
                $fee2 = '0%';
                $fee3 = '0%';
                $profit = '0%';
            }

            $data[] = [
                $value->kodeitem,
                $value->nama_barang,
                number_format($value->hargamodal_spl),
                $value->nama_customer,
                $brutocs,
                $disc,
                $netto,
                $fee1,
                $fee2,
                $fee3,
                $profit,
            ];
        }

        $output = [
            'data' => $data
        ];

        echo json_encode($output);
    }
}
