<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;
use Config\Services;

class Harga_barang_model extends Model
{
    protected $table = 'inv_barang_harga';
    protected $column_order = ['kodeitem', 'nama_barang', 'satuan','nama_customer', 'bruto_cust', 'diskon_pr', 'hargajual_net', 'fee1_pr', 'fee2_pr', 'fee3_pr', 'profit_pr', null];
    protected $column_search = ['kodeitem', 'nama_barang', 'satuan','nama_customer', 'bruto_cust', 'diskon_pr', 'hargajual_net', 'fee1_pr', 'fee2_pr', 'fee3_pr', 'profit_pr'];
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
        $cabang = $this->request->getPost('cabang');
        if ($cabang > 0 && $cabang != 0) {
            $wherecabang = $cabang;
            $this->dt->where(['cabang' => $wherecabang]);
        } else if ($cabang == 0) {
            $wherecabang = "";
        } else {
            $wherecabang = "";
            $this->dt->where(['cabang' => $wherecabang]);
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
