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

    function reportcbgpst()
    {

        $db = db_connect();
        $session = session();
        $q = "
            SELECT 
                CASE 
                    WHEN MONTH(tglsi) = 1 THEN 'Jan'
                    WHEN MONTH(tglsi) = 2 THEN 'Peb'
                    WHEN MONTH(tglsi) = 3 THEN 'Mar'
                    WHEN MONTH(tglsi) = 4 THEN 'Apr'
                    WHEN MONTH(tglsi) = 5 THEN 'Mei'
                    WHEN MONTH(tglsi) = 6 THEN 'Jun'
                    WHEN MONTH(tglsi) = 7 THEN 'Jul'
                    WHEN MONTH(tglsi) = 8 THEN 'Agu'
                    WHEN MONTH(tglsi) = 9 THEN 'Sep'
                    WHEN MONTH(tglsi) = 10 THEN 'Okt'
                    WHEN MONTH(tglsi) = 11 THEN 'Nop'
                    WHEN MONTH(tglsi) = 12 THEN 'Des'
                END AS bulan,
                kodecbg,
                SUM(dpp - diskon) AS jumlah
            FROM 
                ar_sifile 
            WHERE 
                YEAR(tglsi) = (SELECT periode_tahun FROM ms_identity) 
                AND kodecbg IN (SELECT kode FROM ms_unit)
                AND (dpp - diskon) >= 1
            GROUP BY 
                MONTH(tglsi), kodecbg
            ORDER BY 
                MONTH(tglsi), kodecbg";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function reportChartPurchase()
    {

        $db = db_connect();
        $session = session();
        $q = "
            SELECT 
                CASE 
                    WHEN month(tglpu) = 1 THEN 'Jan'
                    WHEN month(tglpu) = 2 THEN 'Peb'
                    WHEN month(tglpu) = 3 THEN 'Mar'
                    WHEN month(tglpu) = 4 THEN 'Apr'
                    WHEN month(tglpu) = 5 THEN 'Mei'
                    WHEN month(tglpu) = 6 THEN 'Jun'
                    WHEN month(tglpu) = 7 THEN 'Jul'
                    WHEN month(tglpu) = 8 THEN 'Agu'
                    WHEN month(tglpu) = 9 THEN 'Sep'
                    WHEN month(tglpu) = 10 THEN 'Okt'
                    WHEN month(tglpu) = 11 THEN 'Nop'
                    WHEN month(tglpu) = 12 THEN 'Des'
                END AS bulan,
                kodecbg,
                SUM(dpp - diskon) AS jumlah
            FROM 
                ap_pufile 
            WHERE 
                year(tglpu) = (SELECT periode_tahun FROM ms_identity) 
                AND kodecbg IN (SELECT kode FROM ms_unit)
            GROUP BY 
                month(tglpu), kodecbg
            ORDER BY 
                month(tglpu), kodecbg";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function reportAllMarketing()
    {

        $db = db_connect();
        $session = session();
        $q = "
            SELECT 
                CASE 
                    WHEN MONTH(a.tglsi) = 1 THEN 'Jan'
                    WHEN MONTH(a.tglsi) = 2 THEN 'Peb'
                    WHEN MONTH(a.tglsi) = 3 THEN 'Mar'
                    WHEN MONTH(a.tglsi) = 4 THEN 'Apr'
                    WHEN MONTH(a.tglsi) = 5 THEN 'Mei'
                    WHEN MONTH(a.tglsi) = 6 THEN 'Jun'
                    WHEN MONTH(a.tglsi) = 7 THEN 'Jul'
                    WHEN MONTH(a.tglsi) = 8 THEN 'Agu'
                    WHEN MONTH(a.tglsi) = 9 THEN 'Sep'
                    WHEN MONTH(a.tglsi) = 10 THEN 'Okt'
                    WHEN MONTH(a.tglsi) = 11 THEN 'Nop'
                    WHEN MONTH(a.tglsi) = 12 THEN 'Des'
                END AS bulan,
                b.id_sales,
                a.kodecbg,
                SUM(a.dpp - a.diskon) AS jumlah
            FROM 
                ar_sifile a 
            INNER JOIN 
                ar_sofile b ON a.kodeso = b.kodeso 
            INNER JOIN 
                ar_sales c ON b.id_sales = c.id
            WHERE 
                YEAR(a.tglsi) = (SELECT periode_tahun FROM ms_identity) 
                AND a.kodecbg IN (SELECT kode FROM ms_unit)
                AND (a.dpp - a.diskon) >= 1
            GROUP BY 
                MONTH(a.tglsi), b.id_sales, a.kodecbg
            ORDER BY 
                MONTH(a.tglsi), b.id_sales, a.kodecbg";

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
        select 'Jan' as bulan, sum(dpp - diskon) as jumlah from ar_sifile 
        WHERE kodeuser='$user' AND (dpp - diskon) >= 1  and YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=1
        UNION
        select 'Peb' as bulan, sum(dpp - diskon) as jumlah from ar_sifile 
        WHERE kodeuser='$user' AND (dpp - diskon) >= 1 and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=2
        UNION
        select 'Mar' as bulan, sum(dpp - diskon) as jumlah from ar_sifile 
        where kodeuser='$user' AND (dpp - diskon) >= 1 and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=3
        UNION
        select 'Apr' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  YEAR(tglsi) = (select periode_tahun from ms_identity) AND kodecbg='$ucabang' and MONTH(tglsi)=4
        UNION
        select 'Mei' as bulan, sum(dpp - diskon) as jumlah from ar_sifile   
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=5
        UNION
        select 'Jun' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=6
        UNION
        select 'Jul' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=7
        UNION
        select 'Agu' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=8
        UNION
        select 'Sep' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=9
        UNION
        select 'Okt' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        WHERE  kodeuser='$user' AND (dpp - diskon) >= 1 and  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=10
        UNION
        select 'Nop' as bulan, sum(dpp - diskon) as jumlah from ar_sifile   
        where  kodeuser='$user' AND (dpp - diskon) >= 1 AND  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=11
        UNION
        select 'Des' as bulan, sum(dpp - diskon) as jumlah from ar_sifile  
        where  kodeuser='$user' AND (dpp - diskon) >= 1 AND  year(tglsi) = (select periode_tahun from ms_identity) and kodecbg='$ucabang' and month(tglsi)=12";

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
        $DataUserPerSales = $db->table("ar_sales")->getWhere(['usersales' => $user])->getRow();
        $q = "
        select 'Jan' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.id_sales='$DataUserPerSales->id' and YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=1
        UNION
        select 'Peb' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        WHERE b.id_sales='$DataUserPerSales->id' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=2
        UNION
        select 'Mar' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso 
        where b.id_sales='$DataUserPerSales->id' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=3
        UNION
        select 'Apr' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  YEAR(a.tglsi) = (select periode_tahun from ms_identity) AND a.kodecbg='$ucabang' and MONTH(a.tglsi)=4
        UNION
        select 'Mei' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=5
        UNION
        select 'Jun' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=6
        UNION
        select 'Jul' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=7
        UNION
        select 'Agu' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=8
        UNION
        select 'Sep' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=9
        UNION
        select 'Okt' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        WHERE  b.id_sales='$DataUserPerSales->id' and  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=10
        UNION
        select 'Nop' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso   
        where  b.id_sales='$DataUserPerSales->id' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=11
        UNION
        select 'Des' as bulan, sum(totalsi) as jumlah from ar_sifile a INNER JOIN ar_sofile b ON a.kodeso = b.kodeso  
        where  b.id_sales='$DataUserPerSales->id' AND  year(a.tglsi) = (select periode_tahun from ms_identity) and a.kodecbg='$ucabang' and month(a.tglsi)=12";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }

    function SalesCustomer()
    {
        $db = db_connect();
        $session = session();
        $q = "
        select b.nama, sum(a.dpp - a.diskon) as total
        from ar_sifile a inner join ar_customer b on a.kodecust=b.kode 
        WHERE YEAR(a.tglsi) = (select periode_tahun from ms_identity) 
        AND MONTH(a.tglsi) = (select periode_bulan from ms_identity)
        AND (a.dpp - a.diskon) >= 1
        group by a.kodecust order by b.nama asc";

        $query = $db->query($q);

        if ($query->getNumRows() > 0) {
            foreach ($query->getResult() as $data) {
                $hasil[] = $data;
            }
            return $hasil;
        }
    }
}