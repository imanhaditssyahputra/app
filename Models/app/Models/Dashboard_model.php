<?php

namespace App\Models;

use CodeIgniter\Model;

class Dashboard_model extends Model
{

    function months()
    {
        return array('Jan', 'Peb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nop', 'Des');
    }


    function jcustomer()
    {
        $db = db_connect();
        $query = $db->query('SELECT * FROM ar_customer');
        return $query->getNumRows();
    }

    function dsaset()
    {
        $db = db_connect();
        return $db->query('select sum(hargabeli*stok) as total from inv_barang')->getRow()->total;
    }

    function dshutang()
    {
        $db = db_connect();
        return $db->query('select sum(totalpu-jumlahbayar) as total from ap_pufile where year(tglpu) = (select periode_tahun from ms_identity)')->getRow()->total;
    }

    function dspiutang()
    {
        $db = db_connect();
        return $db->query('select sum(totalsi-jumlahbayar) as total from ar_sifile where pembayaran="K" and year(tglsi) = (select periode_tahun from ms_identity)')->getRow()->total;
    }

    function report()
    {

        $db = db_connect();
        $session = session();
        $ucabang = $session->get('cabang');
        $q = "
            select 'Jan' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=1
            union
            select 'Peb' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=2
            union
            select 'Mar' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=3
            union
            select 'Apr' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=4
            union
            select 'Mei' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=5
            union
            select 'Jun' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=6
            union
            select 'Jul' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=7
            union
            select 'Agu' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=8
            union
            select 'Sep' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=9
            union
            select 'Okt' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=10
            union
            select 'Nop' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=11
            union
            select 'Des' as bulan, sum(totalsi) as jumlah from ar_sifile where year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=12";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function HitungJumlahPenjualan()
    {

        $db = db_connect();
        $session = session();
        $ucabang = $session->get('cabang');
        $user = $session->get('username');
        $q = "
        select 'Jan' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.kodeuser='$user' and YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=1
        UNION
        select 'Peb' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=2
        UNION
        select 'Mar' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        where b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=3
        UNION
        select 'Apr' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=4
        UNION
        select 'Mei' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=5
        UNION
        select 'Jun' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=6
        UNION
        select 'Jul' as bulan, COUNT(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=7
        UNION
        select 'Agu' as bulan, count(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=8
        UNION
        select 'Sep' as bulan, count(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=9
        UNION
        select 'Okt' as bulan, count(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=10
        UNION
        select 'Nop' as bulan, count(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        where  b.kodeuser='$user' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=11
        UNION
        select 'Des' as bulan, count(a.id) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        where  b.kodeuser='$user' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=12";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function HitungRpFKPenjualanPeruser()
    {

        $db = db_connect();
        $session = session();
        $ucabang = $session->get('cabang');
        $user = $session->get('username');
        $q = "
        select 'Jan' as bulan, sum(totalsi) as jumlah from ar_sifile 
        WHERE kodeuser='$user' and YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=1
        UNION
        select 'Peb' as bulan, sum(totalsi) as jumlah from ar_sifile 
        WHERE kodeuser='$user' and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=2
        UNION
        select 'Mar' as bulan, sum(totalsi) as jumlah from ar_sifile 
        where kodeuser='$user' and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=3
        UNION
        select 'Apr' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=4
        UNION
        select 'Mei' as bulan, sum(totalsi) as jumlah from ar_sifile   
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=5
        UNION
        select 'Jun' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=6
        UNION
        select 'Jul' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=7
        UNION
        select 'Agu' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=8
        UNION
        select 'Sep' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=9
        UNION
        select 'Okt' as bulan, sum(totalsi) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=10
        UNION
        select 'Nop' as bulan, sum(totalsi) as jumlah from ar_sifile   
        where  kodeuser='$user' AND  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=11
        UNION
        select 'Des' as bulan, sum(totalsi) as jumlah from ar_sifile  
        where  kodeuser='$user' AND  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=12";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function HitungRpMarketingPenjualanPeruser()
    {

        $db = db_connect();
        $session = session();
        $ucabang = $session->get('cabang');
        $user = $session->get('username');
        $q = "
        select 'Jan' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.kodeuser='$user' and YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=1
        UNION
        select 'Peb' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=2
        UNION
        select 'Mar' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        where b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=3
        UNION
        select 'Apr' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=4
        UNION
        select 'Mei' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=5
        UNION
        select 'Jun' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=6
        UNION
        select 'Jul' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=7
        UNION
        select 'Agu' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=8
        UNION
        select 'Sep' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=9
        UNION
        select 'Okt' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.kodeuser='$user' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=10
        UNION
        select 'Nop' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        where  b.kodeuser='$user' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=11
        UNION
        select 'Des' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        where  b.kodeuser='$user' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=12";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }
}
