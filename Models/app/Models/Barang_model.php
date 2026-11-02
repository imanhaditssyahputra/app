<?php

namespace App\Models;

use CodeIgniter\Model;

class Barang_model extends Model
{
    var $table = 'inv_barang';
    var $column_order = ['kodeitem', 'namabarang', 'hargabeli', 'stok', null];
    var $column_search = ['kodeitem', 'namabarang', 'hargajual', 'hargabeli', 'stok', 'satuan'];
    var $order = ['kodeitem' => 'asc'];

    public function __construct()
    {
        parent::__construct();
    }

    public function _getDatatablesQuery($gudang, $cari)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
		if($gudang > 0 && $gudang != 1000){
            $dataunit = $db->table("inv_gudang")->where(['kode' => $gudang])->get()->getRow();
            $wheregudang = $gudang;
            $wherecabang = $dataunit->cabang;
            $builder->where(['kdcbg' => $wherecabang, 'kdgudang' => $wheregudang]);
        }else if($gudang == 0){
            $wheregudang = "";
            $wherecabang = "";
        }else{
            $wheregudang = "";
            $wherecabang = "";
            $builder->where(['kdcbg' => $wherecabang, 'kdgudang' => $wheregudang]);
        }
        

        $i = 0;

        foreach ($this->column_search as $item) {
            if ($cari != "undefined") {
                if ($i === 0) {
                    $builder->groupStart();
                    $builder->like($item, $cari);
                } else {
                    $builder->orLike($item, $cari);
                }

                if (count($this->column_search) - 1 === $i) {
                    $builder->groupEnd();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->order)) {
            $order = $this->order;
            $builder->orderBy(key($order), $order[key($order)]);
        }

        return $builder;
    }

    function get_datatables( $gudang, $cari )
	{
        $builder = $this->_getDatatablesQuery($gudang, $cari);
		if ($_POST['length'] != -1) {
			$builder ->limit($_POST['length'], $_POST['start']);
		}
		return $builder->get()->getResult();
	}

	function count_filtered($gudang, $cari)
	{
		$builder = $this->_getDatatablesQuery($gudang, $cari);
        return $builder->countAllResults();
	}

	public function count_all()
	{
		return $this->countAllResults();
	}
}
