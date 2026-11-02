<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;
use Config\Services;

class Barang_model extends Model
{
    protected $table = 'inv_barang';
    protected $column_order = ['image_1920','kodeitem', 'namabarang', 'hargabeli', 'stok', 'stok_min', 'stok_max', 'nie', 'tglexpnie', 'satuan', null];
    protected $column_search = ['image_1920','kodeitem', 'namabarang', 'hargabeli','stok',  'stok_min', 'stok_max', 'nie', 'tglexpnie', 'satuan'];
    protected $order = ['kodeitem' => 'asc'];
    protected $request;
    protected $db;
    protected $dt;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = Services::request();
        $this->dt = $this->db->table($this->table);
    }

    private function getDatatablesQuery()
    {

        $db = \Config\Database::connect();
        $request = Services::request();
        $gudang = $this->request->getPost('gudang');
        if ($gudang > 0 && $gudang != 1000) {
            $dataunit = $db->table("inv_gudang")->where(['kode' => $gudang])->get()->getRow();
            $wheregudang = $gudang;
            $wherecabang = $dataunit->cabang;
            $this->dt->where(['kdcbg' => $wherecabang, 'kdgudang' => $wheregudang]);
        } else if ($gudang == 0) {
            $wheregudang = "";
            $wherecabang = "";
        } else {
            $wheregudang = "";
            $wherecabang = "";
            $this->dt->where(['kdcbg' => $wherecabang, 'kdgudang' => $wheregudang]);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->dt->groupStart();
                    $this->dt->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->dt->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->dt->groupEnd();
            }
            $i++;
        }

        if ($this->request->getPost('order')) {
            $this->dt->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->dt->orderBy(key($order), $order[key($order)]);
        }
    }

    public function getDatatables()
    {
        $this->getDatatablesQuery();
        if ($this->request->getPost('length') != -1)
            $this->dt->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->dt->get();
        return $query->getResult();
    }

    public function countFiltered()
    {
        $this->getDatatablesQuery();
        return $this->dt->countAllResults();
    }

    public function countAll()
    {
        $tbl_storage = $this->db->table($this->table);
        return $tbl_storage->countAllResults();
    }
}
