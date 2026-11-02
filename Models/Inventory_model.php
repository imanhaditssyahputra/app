<?php

namespace App\Models;

use CodeIgniter\Model;

class Inventory_model extends Model
{

	//Update Barang Harga & Stock Opname
	public function UpdateBarangHarga($kodeitem, $cabang, $namebarang)
	{
		$db = db_connect();
		$builder = $db->table('inv_barang_harga');
        $builder2 = $db->table('inv_stockopname');
		$result = $builder->where('kodeitem', $kodeitem)->update(['nama_barang' => $namebarang]);
		$result2 = $builder2->where('kodeitem', $kodeitem)->update(['namabarang' => $namebarang]);
	}

	function DataStokBarangPercabang($cabang)
	{
		$db = db_connect();
		$query = $db->query("SELECT * from inv_barang where kdcbg='$cabang' order by kodeitem asc")->getResult();
		return $query;
	}

	function DataSOBarang($kode, $gudang, $cabang)
	{
		$db = db_connect();
		$query = $db->query("SELECT tanggal, nobukti, so, tglrekam
		FROM inv_transaksi AS a
		WHERE kodeitem='$kode' and gudang='$gudang' and cabang='$cabang' AND jenis='Stock Opname' and tanggal = (
			SELECT MAX(tgl)
			FROM inv_stockopname AS b
			WHERE b.kodeitem='$kode' and b.gudang='$gudang' and b.cabang='$cabang'
		)");
		return $query->getRow();
	}

	function dataInvTransaksiPerbarang($kode, $cabang, $gudang, $tglrekam)
	{
		$db = db_connect();
		$query = $db->query("SELECT sum(penerimaan) as terima, sum(pengeluaran) as keluar from inv_transaksi 
                                where kodeitem='$kode' AND tglrekam >='$tglrekam' and cabang='$cabang' and gudang='$gudang' ");
		return $query->getRow();
	}

	function ValiadasiStok($kode, $cabang, $gudang, $jumlah)
	{
		$db = db_connect();
		$query = $db->query("UPDATE inv_barang set stok='$jumlah', stok_so='$jumlah' where kodeitem='$kode' and kdcbg='$cabang' and kdgudang='$gudang'");
		return $query;
	}

	function dataInvTransaksiPerNobatch($kode, $cabang, $gudang, $tglrekam)
	{
		$db = db_connect();
		$query = $db->query("SELECT a.no_batch FROM inv_nobatch a 
									INNER JOIN inv_transaksi b ON b.no_batch = a.no_batch 
									WHERE b.kodeitem = '$kode' 
									AND a.kodeitem='$kode' 
									AND b.tglrekam >='$tglrekam' 
									AND b.jenis !='Stock Opname'
									AND a.gudang = '$gudang'
									AND a.cabang = '$cabang' 
									AND b.gudang = '$gudang'
									AND b.cabang = '$cabang' 
									GROUP BY a.no_batch");
		return $query->getResult();
	}

	function DataSODetailBarang($kode, $gudang, $cabang, $NobatchTransaksi, $tanggal)
	{
		$db = db_connect();
		$query = $db->query("SELECT * FROM inv_sodetail 
									where tanggal='$tanggal' 
									AND kodeitem='$kode' AND no_batch='$NobatchTransaksi' 
									and gudang='$gudang' and cabang='$cabang'");
		return $query->getRow();
	}

	function JumlahNobatchSo($kode, $cabang, $gudang, $NobatchSo, $tanggal)
	{
		$db = db_connect();
		$query = $db->query("SELECT SUM(qty_stok) AS qty FROM inv_sodetail 
									WHERE kodeitem='$kode' 
									and tanggal='$tanggal' 
									AND no_batch='$NobatchSo'
									and cabang='$cabang' 
									and gudang='$gudang'");
		return $query->getRow();
	}

	function KeluarMasukNobatch($kode, $cabang, $gudang, $nobatch, $tglrekam)
	{
		$db = db_connect();
		$query = $db->query("SELECT sum(penerimaan) as terima, sum(pengeluaran) as keluar from inv_transaksi 
									where kodeitem='$kode' 
									AND tglrekam >='$tglrekam' 
									AND no_batch = '$nobatch'
									and cabang='$cabang' 
									and gudang='$gudang'");
		return $query->getRow();
	}

	function ValiadasiStokNobatch($kode, $cabang, $gudang, $batch, $qtyterima, $qtykeluar, $jumlah)
	{
		$db = db_connect();
		$query = $db->query("UPDATE inv_nobatch 
									set qty_terima='$qtyterima', qty_jual='$qtykeluar', qty_saldo='$jumlah' 
									where kodeitem='$kode' and no_batch='$batch' and cabang='$cabang' and gudang='$gudang' ");
		return $query;
	}

	function DataNobatch($_kode, $batch, $cabang)
	{
		$db = db_connect();
		$query = $db->query("SELECT * FROM inv_nobatch WHERE kodeitem='$_kode' and no_batch='$batch' AND cabang='$cabang'");
		return $query->getRow();
	}
}
