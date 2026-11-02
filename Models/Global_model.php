<?php

namespace App\Models;

use CodeIgniter\Model;

class Global_model extends Model
{

	//Profile Perusahaan
	public function _LoadProfile()
	{
		$db = db_connect();
		$query = $db->table('ms_identity')->get()->getRow();
		return $query;
	}

	//Akses Menu Page Start Here
	public function cek_menu($level, $modul)
	{
		$db = db_connect();
		$builder = $db->table('ms_modul_grupd')->where(array('nomor_grup' => $level, 'nomor_modul' => $modul));
		return $builder->countAllResults();
	}

	public function cek_menu_akses($level, $modul)
	{
		$db = db_connect();
		$builder = $db->table('ms_modul_grupd')->where(array('nomor_grup' => $level, 'nomor_modul' => $modul))->get()->getRow();
		return $builder;
	}

	public function akses_modul($nomor)
	{
		$db = db_connect();
		$builder = $db->table('ms_modul_grup')->where(array('nomor' => $nomor));
		return $builder->get()->getRow();
	}

	//Periode page start here
	public function _periodetahun()
	{
		$db = db_connect();
		$builder = $db->table('ms_identity');
		$row = $builder->get()->getRow();
		return $row->periode_tahun;
	}

	public function _periodebulan()
	{
		$db = db_connect();
		$query = $db->table('ms_identity')->get()->getRow();
		$nbln = array('', 'JANUARI', 'PEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOPEMBER', 'DESEMBER');
		return $nbln[$query->periode_bulan];
	}

	public function _namabulan($bln)
	{
		$nbln = array('', 'JANUARI', 'PEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOPEMBER', 'DESEMBER');
		return $nbln[$bln];
	}

	function tgln()
	{
		$hari = date('d');
		$hari1 = date('N');
		$bulan = date('n');
		$tahun = date('Y');
		$nbln = array('', 'Januari', 'Pebruari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'Nopember', 'Desember');
		$nhari = array('', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
		return  $nhari[$hari1] . ", " . $hari . " " . $nbln[$bulan] . " " . $tahun . " ";
	}

	//LABA RUGI Page Start here
	public function _labarugiberjalan_tahun()
	{
		$db = db_connect();
		$tahun = $this->_periodetahun();

		$pend  = 0;
		$biaya = 0;
		$qry =
			"select sum(kredit-debet) as total from tr_jurnal inner join ms_akun
			on tr_jurnal.kodeakun=ms_akun.kodeakun inner join
			ms_akun_kelompok on ms_akun.kelompok=ms_akun_kelompok.kode
			where ms_akun_kelompok.lap='L' and ms_akun_kelompok.nokel=1
			and year(tr_jurnal.tanggal) = '$tahun'";

		$builder = $db->query($qry)->getRow();
		$pend  = $builder->total;

		$qry =
			"select sum(debet-kredit) as total from tr_jurnal inner join ms_akun
			on tr_jurnal.kodeakun=ms_akun.kodeakun inner join
			ms_akun_kelompok on ms_akun.kelompok=ms_akun_kelompok.kode
			where ms_akun_kelompok.lap='L' and ms_akun_kelompok.nokel=2
			and year(tr_jurnal.tanggal) = '$tahun'";

		$builder = $db->query($qry)->getRow();
		$biaya = $builder->total;

		return $pend - $biaya;
	}

	//AutoNo page start here
	public function _Autono($kode)
	{
		$db = db_connect();
		$builder = $db->table('ms_counter1')->where('kdtr', $kode)->get()->getRow();
		$nourut = $builder->cdno;
		$nomor = $kode . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
		return $nomor;
	}

	//Autonomor Transaksi
	public function _Autonomor($kode)
	{
		$db = db_connect();
		$builder = $db->table('ms_counter1')->where('kdtr', $kode)->get()->getRow();
		$nourut = $builder->cdno;
		$nomor = $kode . '.' . date('Y') . '.' . date('m') . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
		return $nomor;
	}

	//Autonomor Faktur Pajak Penjualan
	public function _AutonomorFakturPenjualan($kode)
	{
		$db = db_connect();
		$builder = $db->table('ms_counter_fpajak')->where('kdtr', $kode)->get()->getRow();
		//$nomor = '010.' . $builder->no_date . '.' . $builder->cdno;
		$nomor = '-';
		return $nomor;
	}

	//Update autonomor
	public function _updatecounter1($kode)
	{
		$db = db_connect();
		$builder = $db->query("UPDATE ms_counter1 SET cdno = cdno + 1 WHERE kdtr='$kode'");
	}

	public function _updatecounter_pajak($kode)
	{
		$db = db_connect();
		$builder = $db->query("UPDATE ms_counter_fpajak SET cdno = cdno + 1 WHERE kdtr='$kode'");
	}

	//Get Nama Marketing Page Start Here
	public function GetMarketing($kode)
	{
		$db = db_connect();
		$builder = $db->table('ar_sales')->where('id', $kode)->get()->getRow();
		$name = $builder->nama_sales;
		return $name;
	}

	//********All select2Search*********\\

	//select Barang Filter
	function getbarang_filter($str, $cabang, $gudang)
	{
		$db = db_connect();
		$query = '';
		if ($str != "") {
			$query = $db->query("select kodeitem as id, namabarang as text from inv_barang where kdcbg='$cabang' and kdgudang='$gudang' and namabarang like '%$str%'");
		} else {
			$query = $db->query("select kodeitem as id, namabarang as text from inv_barang where kdcbg='$cabang' and kdgudang='$gudang' limit 5");
		}

		return $query->getResult();
	}

	//select Barang Filter Cabang
	function getbarang_filtercabang($str, $cabang)
	{
		$db = db_connect();
		$query = '';
		if ($str != "") {
			$query = $db->query("select kodeitem as id, namabarang as text from inv_barang where kdcbg='$cabang' and namabarang like '%$str%' group by kodeitem");
		} else {
			$query = $db->query("select kodeitem as id, namabarang as text from inv_barang where kdcbg='$cabang' group by kodeitem limit 5 ");
		}

		return $query->getResult();
	}

	//select gudang
	function getgudang($str)
	{
		$db = db_connect();
		$query = '';
		if ($str != "") {
			$query = $db->query("select kode as id, nama as text from inv_gudang where nama like '%$str%' ");
		} else {
			$query = $db->query("select kode as id, nama as text from inv_gudang ");
		}

		return $query->getResult();
	}

	//select gudang Filter
	function getgudangfilter($str, $cabang)
	{
		$db = db_connect();
		$query = '';
		if ($str != "") {
			$query = $db->query("select kode as id, nama as text from inv_gudang where cabang='$cabang' and nama like '%$str%' ");
		} else {
			$query = $db->query("select kode as id, nama as text from inv_gudang where cabang='$cabang' limit 5");
		}

		return $query->getResult();
	}

	//Select Supplier
	function getsupp($str)
	{
		$db = db_connect();
		if ($str != "") {
			$query = $db->query("select kode as id, concat(nama) as text from ap_supplier where nama like '%$str%' or kode like '$str%'");
		} else {
			$query = $db->query("select kode as id, concat(nama) as text from ap_supplier where nama like '%$str%' or kode like '$str%'");
		}

		return $query->getResult();
	}

	//Select Customer
	function getcust($str, $cabang)
	{
		$db = db_connect();
		if ($str != "") {
			$query = $db->query("select kode as id, concat(nama) as text from ar_customer where kode_cabang='$cabang' and nama like '%$str%' or kode like '$str%'");
		} else {
			$query = $db->query("select kode as id, concat(nama) as text from ar_customer where kode_cabang='$cabang' limit 5");
		}

		return $query->getResult();
	}

	//Select Akun
	function getakun($str)
	{
		$db = db_connect();
		if ($str != "") {
			$query = $db->query("select kodeakun as id, concat(namaakun) as text from ms_akun where namaakun like '%$str%' or kodeakun like '$str%'");
		} else {
			$query = $db->query("select kodeakun as id, concat(namaakun) as text from ms_akun");
		}

		return $query->getResult();
	}

	//Select Akun RL
	function getakunRL($str)
	{
		$db = db_connect();
		if ($str != "") {
			$query = $db->query("select kodeakun as id, concat(kodeakun,'-',namaakun) as text from ms_akun where tx='Y' and (namaakun like '%$str%' or kodeakun like '$str%')");
		} else {
			$query = $db->query("select kodeakun as id, concat(kodeakun,'-',namaakun) as text from ms_akun where tx='Y'");
		}

		return $query->getResult();
	}

	function getbatch($str, $kode, $cabang)
	{
		$db = db_connect();
		$session = session();
		$query = '';
		if ($str != "") {
			$query = $db->query("select no_batch as id, namabarang as text from inv_barang where kodeitem='$kode' and cabang='$cabang' and no_batch like '%$str%'");
		} else {
			$query = $db->query("select no_batch as id, namabarang as text from inv_barang where kodeitem='$kode' and cabang='$cabang' limit 5");
		}

		return $query->getResult();
	}

	//Show Barang Data
	function showbarangdata($kode, $gudang)
	{
		$db = db_connect();
		$session = session();
		$cabang = $session->get('cabang');
		$data = $db->query("SELECT * from inv_barang where kodeitem='$kode' and kdgudang='$gudang' and kdcbg='$cabang'")->getRow();
	}

	//***********************DATA JURNAL***********************//
	//Hapus Jurnal
	public function _hapusjurnal($nobukti, $jenis)
	{
		$db = db_connect();
		$session = session();
		$db->table('tr_jurnal')->delete(['novoucher' => $nobukti, 'jenis' => $jenis, 'kodecbg' => $session->get('cabang')]);
	}

	//insert data jurnal
	public function _rekamjurnal($tanggal, $nobukti, $jenis, $noref, $nourut, $kodeakun, $ketglobal, $ket, $debet, $kredit)
	{
		$db = db_connect();
		$session = session();
		$data = array(
			'tanggal' => date('Y-m-d', strtotime($tanggal)),
			'novoucher' => $nobukti,
			'jenis' => $jenis,
			'posted' => 'Y',
			'noref' => $noref,
			'nourut' => $nourut,
			'kodeakun' => $kodeakun,
			'ket' => $ketglobal,
			'keterangan' => $ket,
			'debet' => str_replace(',', '', $debet),
			'kredit' => str_replace(',', '', $kredit),
			'kodecbg' => $session->get('cabang'),
			'userid' => $session->get('username'),
			'wbs' => '',
			'tgledit' => date('Y-m-d H:i:s'),
		);
		//if ($kodeakun !=""){
		$db->table('tr_jurnal')->insert($data);
		//}  			
	}

	//convert rupiah to text
	function konversi($x)
	{
		$abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		if ($x < 12)
			return " " . $abil[$x];
		elseif ($x < 20)
			return $this->konversi($x - 10) . " belas";
		elseif ($x < 100)
			return $this->konversi($x / 10) . " puluh" . $this->konversi(fmod($x, 10));
		elseif ($x < 200)
			return " seratus" . $this->konversi($x - 100);
		elseif ($x < 1000)
			return $this->konversi($x / 100) . " ratus" . $this->konversi(fmod($x, 100));
		elseif ($x < 2000)
			return " seribu" . $this->konversi($x - 1000);
		elseif ($x < 1000000)
			return $this->konversi($x / 1000) . " ribu" . $this->konversi(fmod($x, 1000));
		elseif ($x < 1000000000)
			return $this->konversi($x / 1000000) . " juta" . $this->konversi(fmod($x, 1000000));
		elseif ($x < 1000000000000)
			return $this->konversi($x / 1000000000) . " milyar" . $this->konversi(fmod($x, 1000000000));
		elseif ($x < 1000000000000000)
			return $this->konversi($x / 1000000000000) . " trilyun" . $this->konversi(fmod($x, 1000000000000));
	}

	function tkoma($x)
	{

		$x = stristr($x, '.');
		if ($x > 0) {

			$string = array("nol", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan",   "sembilan", "sepuluh", "sebelas");
			$temp = "";
			$pjg  = strlen($x);
			$pos = 1;

			while ($pos < $pjg) {
				$char = substr($x, $pos, 1);
				$pos++;
				$temp .= " " . $string[$char];
			}

			return $temp;
		} else {
			return "";
		}
	}

	public function terbilang($x)
	{
		if ($x < 0) {
			$hasil = "minus " . trim($this->konversi($x));
		} else {
			$poin = trim($this->tkoma($x));
			$hasil = trim($this->konversi($x));
		}

		if ($poin) {
			$hasil = $hasil . " koma " . $poin;
		} else {
			$hasil = $hasil;
		}
		return '# ' . $hasil . ' Rupiah #';
	}

	public function _jenisakun($kode)
	{
		$db = db_connect();
		$query = $db->query("SELECT b.sn as jenis from ms_akun a inner join ms_akun_kelompok b on a.kelompok = b.kode where a.kodeakun='$kode'");
		$row = $query->getRow();
		return $row->jenis;
	}

	public function _lapakun($kode)
	{
		$db = db_connect();
		$query = $db->query("SELECT b.sn as lap from ms_akun a inner join ms_akun_kelompok b on a.kelompok = b.kode where a.kodeakun='$kode'");
		$row = $query->getRow();
		return $row->lap;
	}

	public function _namaakun($kode)
	{
		$db = db_connect();
		$query = $db->table("ms_akun")->getWhere(['kodeakun' => $kode]);
		$row = $query->getRow();
		return $row->namaakun;
	}

	public function _saldoakun($akun, $bulan, $tahun)
	{
		$db = db_connect();
		$query = $db->table('ms_akunsaldo')->getWhere(['kodeakun' => $akun, 'tahun' => $tahun, 'bulan' => $bulan]);
		$data  = $query->getRow();
		$jumdata = $query->getNumRows();

		if ($jumdata > 0) {
			$saldo = $data->debet + $data->kredit;
			return $saldo;
		} else {
			return 0;
		}
	}

	public function _totjurnal($akun, $jenis, $tgl1, $tgl2)
	{
		$db = db_connect();
		$_tgl1 = date('Y-m-d', strtotime($tgl1));
		$_tgl2 = date('Y-m-d', strtotime($tgl2));

		$query = $db->query("select sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where kodeakun='$akun' and tanggal between '$_tgl1' and '$_tgl2'");
		$data  = $query->getRow();
		if ($jenis == "D") {
			return $data->debet - $data->kredit;
		} else {
			return $data->kredit - $data->debet;
		}
	}

	public function _labarugiberjalan($tgl1, $tgl2)
	{
		$db = db_connect();
		$_tgl1 = date('Y-m-d', strtotime($tgl1));
		$_tgl2 = date('Y-m-d', strtotime($tgl2));

		$pend  = 0;
		$biaya = 0;
		$qry =
			"select sum(kredit-debet) as total from tr_jurnal inner join ms_akun
			on tr_jurnal.kodeakun=ms_akun.kodeakun inner join
			ms_akun_kelompok on ms_akun.kelompok=ms_akun_kelompok.kode
			where ms_akun_kelompok.lap='L' and ms_akun_kelompok.nokel=1
			and tr_jurnal.tanggal between '$_tgl1' and '$_tgl2'";

		$query = $db->query($qry);
		$data  = $query->getRow();
		$pend  = $data->total;

		$qry =
			"select sum(debet-kredit) as total from tr_jurnal inner join ms_akun
			on tr_jurnal.kodeakun=ms_akun.kodeakun inner join
			ms_akun_kelompok on ms_akun.kelompok=ms_akun_kelompok.kode
			where ms_akun_kelompok.lap='L' and ms_akun_kelompok.nokel=2
			and tr_jurnal.tanggal between '$_tgl1' and '$_tgl2'";

		$query = $db->query($qry);
		$data  = $query->getRow();
		$biaya = $data->total;

		return $pend - $biaya;
	}
}
