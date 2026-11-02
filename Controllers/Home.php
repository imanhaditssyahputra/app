<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Home Page
 **/

namespace App\Controllers;

use Error;

class Home extends BaseController
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
	
    public function dashboard()
    {

        $session = session();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $M_Dashboard =  model('Dashboard_model', 'M_Dasboard');

        if (empty(session()->get('username'))) {
            return redirect()->to('/');
        } else {
            error_reporting(0);
            $date = date('Y-m-d');
            $dateAwal = date('Y-m-01', strtotime($date));
            $dateAkhir = date('Y-m-t', strtotime($date));
            $db = db_connect();
            $cabang = $session->get('cabang');
            $DataUser = $db->table("userlogin")->getWhere(['uidlogin' => $session->get('username')])->getRow();
            $DataUserPerSales = $db->table("ar_sales")->getWhere(['usersales' => $session->get('username')])->getRow();
            $DataSofileUser = $db->query("SELECT count(id) as jumlah FROM ar_sofile
                                        WHERE kodeuser='$DataUser->uidlogin' 
                                        and YEAR(tglso)= (select periode_tahun from ms_identity) 
                                        and MONTH(tglso)= (select periode_bulan from ms_identity)")->getRow();
            $DataSofile = $db->query("SELECT count(id) as jumlah FROM ar_sofile
                                        WHERE STATUSid != 4 and kodecbg='$cabang'
                                        and YEAR(tglso)= (select periode_tahun from ms_identity) 
                                        and MONTH(tglso)= (select periode_bulan from ms_identity)")->getRow();
            $DataKirim = $db->query("SELECT count(id) as jumlah FROM ar_kirim
                                        WHERE STATUSid != 3 and kodecbg='$cabang'
                                        and YEAR(tglkirim)= (select periode_tahun from ms_identity) 
                                        and MONTH(tglkirim)= (select periode_bulan from ms_identity)")->getRow();
            $DataSifileGetUser = $db->query("SELECT sum(a.totalsi) as jumlah FROM ar_sifile a
                                                INNER JOIN ar_sofile b ON a.kodeso = b.kodeso
                                                WHERE b.id_sales='$DataUserPerSales->id' 
                                                and YEAR(a.tglsi)= (select periode_tahun from ms_identity) 
                                                and MONTH(a.tglsi)= (select periode_bulan from ms_identity)")->getRow();

            $DataCountSifileGetUser = $db->query("SELECT count(a.id) as jumlah FROM ar_sifile a
                                                INNER JOIN ar_sofile b ON a.kodeso = b.kodeso
                                                WHERE b.id_sales='$DataUserPerSales->id' 
                                                and YEAR(a.tglsi)= (select periode_tahun from ms_identity) 
                                                and MONTH(a.tglsi)= (select periode_bulan from ms_identity)")->getRow();
            $DataCountSifile = $db->query("SELECT count(a.id) as jumlah FROM ar_sifile a
                                                WHERE a.kodecbg='$cabang'
                                                and YEAR(a.tglsi)= (select periode_tahun from ms_identity) 
                                                and MONTH(a.tglsi)= (select periode_bulan from ms_identity)")->getRow();

            $DataPufile = $db->query("SELECT sum(dpp-diskon) AS jumlah FROM ap_pufile WHERE YEAR(tglpu)= (select periode_tahun from ms_identity) 
                                    and MONTH(tglpu)= (select periode_bulan from ms_identity)")->getRow();

            $DataSifile = $db->query("SELECT sum(dpp-diskon) AS jumlah FROM ar_sifile WHERE kodecbg='$cabang' and  YEAR(tglsi)= (select periode_tahun from ms_identity) 
                                    and MONTH(tglsi)= (select periode_bulan from ms_identity) AND (dpp - diskon) >= 1")->getRow();

            $prevMonth = (int) $db->query("select periode_bulan from ms_identity")->getRow()->periode_bulan - 1;
            if ($prevMonth == 0) {
                $prevMonth = 12;
                $prevYear = (int) $db->query("select periode_tahun from ms_identity")->getRow()->periode_tahun - 1;
            } else {
                $prevYear = (int) $db->query("select periode_tahun from ms_identity")->getRow()->periode_tahun;
            }

            $DataSifilePrev = $db->query("SELECT sum(dpp-diskon) AS jumlah FROM ar_sifile 
                             WHERE kodecbg='$cabang' 
                             AND YEAR(tglsi)= $prevYear 
                             AND MONTH(tglsi)= $prevMonth")->getRow();
            $jumlahCurrent = $DataSifile->jumlah;
            $jumlahPrev = $DataSifilePrev->jumlah;
            if($jumlahCurrent > $jumlahPrev){
                $percentageChange = (($jumlahCurrent - $jumlahPrev) / $jumlahPrev) * 100;
            }else{
                $percentageChange = (($jumlahPrev - $jumlahCurrent) / $jumlahPrev) * 100;
            }
            

            $DatareturGetUser = $db->query("SELECT COUNT(a.idretur) AS jumlah FROM ar_retur a
                                            INNER JOIN ar_sifile b ON a.kodesi = b.kodesi
                                            INNER JOIN  ar_sofile c ON b.kodeso = c.kodeso
                                            WHERE c.id_sales='$DataUserPerSales->id' 
                                            and YEAR(a.tglretur)= (select periode_tahun from ms_identity) 
                                            and MONTH(a.tglretur)= (select periode_bulan from ms_identity)")->getRow();

            $DataRetur = $db->query("SELECT count(idretur) as jumlah FROM ar_retur
                                        WHERE kodecbg='$cabang'
                                        and YEAR(tglretur)= (select periode_tahun from ms_identity) 
                                        and MONTH(tglretur)= (select periode_bulan from ms_identity)")->getRow();

            $DataSaldoAwalKas = $db->query("SELECT * FROM ms_akunsaldo
                                    WHERE cabang='$cabang'
                                    and tahun= (select periode_tahun from ms_identity) 
                                    and bulan= (select periode_bulan from ms_identity)")->getRow();



            $ResultAkun = $db->query("SELECT * from ms_akunsaldo right outer join ms_akun on ms_akunsaldo.kodeakun=ms_akun.kodeakun 
                and ms_akunsaldo.tahun = (select periode_tahun from ms_identity)  and ms_akunsaldo.bulan = (select periode_bulan from ms_identity)
                where ms_akun.kelompok = 'BANK' and ms_akun.tx='Y' and ms_akunsaldo.cabang = '$cabang' order by ms_akun.kodeakun")->getResult();
            $SaldoKasAlhir = 0;
            foreach ($ResultAkun as $db) {
                $akun = $db->kodeakun;
                if ($akun == '110101') {
                    $saldoawal = $db->debet + $db->kredit;

                    $db2 = db_connect();
                    $jur = $db2->query("SELECT sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where kodeakun='110101' and tanggal between '$dateAwal' and '$dateAkhir'")->getRow();
                    $tdb = $jur->debet;
                    $tkr = $jur->kredit;
                    $mutasi = $tdb - $tkr;

                    $akhir  = $saldoawal + $mutasi;
                    if ($akhir > 0) {
                        $akhirdb = abs($akhir);
                        $akhirkr = 0;
                    } else {
                        $akhirkr = abs($akhir);
                        $akhirdb = 0;
                    }

                    $SaldoKasAlhir =  $SaldoKasAlhir + $akhirdb;
                }
            }

            $css = [];
            $js = [];
            $data  = [
                'css' => $css,
                'js' => $js,
                'title' => 'Ajf-Corp',
                'body' => 'admin/dashboard',
                'username' => $session->get('username'),
                'level' => $session->get('level'),
                'cabang' => $session->get('cabang'),
                'asset' => $M_Dashboard->dsaset(),
                'hutang' => $M_Dashboard->dshutang(),
                'piutang' => $M_Dashboard->dspiutang(),
                'GetBulan' => $M_Dashboard->months(),
                'reportcbgpst' => $M_Dashboard->reportcbgpst(),
                'reportChartPurchase' => $M_Dashboard->reportChartPurchase(),
                'reportAllMarketing' => $M_Dashboard->reportAllMarketing(), 
                'reportJumDataPenjualan' => $M_Dashboard->HitungJumlahPenjualan(),
                'RpFKPenjualan' => $M_Dashboard->HitungRpFKPenjualanPeruser(),
                'RpMarketingPenjualan' => $M_Dashboard->HitungRpMarketingPenjualanPeruser(),
                'profile' => $model_Global->_LoadProfile(),
                'lr' => $model_Global->_labarugiberjalan_tahun(),
                'tahun' => 'Periode Tahun ' . $model_Global->_periodetahun(),
                'HitungFaktur' => $DataSifileGetUser->jumlah,
                'CountFakturUser' => $DataCountSifileGetUser->jumlah,
                'CountFakturToMonth' => $DataCountSifile->jumlah,
                'HitungFKPembelian' => $DataPufile->jumlah,
                'HitungFKPenjualan' => $DataSifile->jumlah,
                'HitungFKPenjualanPercen' => $percentageChange,
                'jumlahCurrentjual' => $jumlahCurrent,
                'jumlahPrevjual' => $jumlahPrev,
                'HitungSofile' => $DataSofileUser->jumlah,
                'CountOrder' => $DataSofile->jumlah,
                'CountKirim' => $DataKirim->jumlah,
                'HitungRetur' => $DatareturGetUser->jumlah,
                'CountRetur' => $DataRetur->jumlah,
                'SaldoAwalKas' => $DataSaldoAwalKas->debet,
                'SaldoAkhirKas' => $SaldoKasAlhir,
                'DataUser' => $DataUser,
                'DataSalesPerCS' => $M_Dashboard->SalesCustomer()
            ];
            return view('layout/layout', $data);
        }
    }

    public function profile()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'Ajf-Corp',
            'body' => 'admin/profile',
        ];
        return view('layout/layout', $data);
    }

    //Show Barang Data
    public function showbarangdata($kode, $gudang)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $result = $model_Global->showbarangdata($kode, $gudang);
        echo json_encode($result);
    }

    //All Search 

    //Search Barang Filter
    function search_barang_filter($cbg, $gudang)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getbarang_filter($cek, $cbg, $gudang);
        echo json_encode($result);
    }

    //Search Barang Filter
    function search_barang_filtercabang($cbg)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getbarang_filtercabang($cek, $cbg);
        echo json_encode($result);
    }

    //Search Gudang
    function search_gudang()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $return = $model_Global->getgudang($cek);
        echo json_encode($return);
    }

    //Search Gudang Filter
    function search_gudang_filter($cbg)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $return = $model_Global->getgudangfilter($cek, $cbg);
        echo json_encode($return);
    }

    //Search Supplier
    function search_supplier()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $return = $model_Global->getsupp($cek);
        echo json_encode($return);
    }

    //Search Customer Filter
    function search_cust_filter($cbg)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getcust($cek, $cbg);
        echo json_encode($result);
    }

    //Search Akun
    function search_akun()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getakun($cek);
        echo json_encode($result);
    }

    //Search Akun RL
    function search_akun_RL()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getakunRL($cek);
        echo json_encode($result);
    }

    function searchbatch($kode, $cabang)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        if (!empty($input)) {
            $cek = $input['searchTerm'];
        } else {
            $cek = "";
        }
        $result = $model_Global->getbatch($cek, $kode, $cabang);
        echo json_encode($result);
    }

    // Data Barang Filter
    public function DataBarang($kode, $cabang, $gudang)
    {
        $db = db_connect();
        $data = $db->query("SELECT * FROM inv_barang where kodeitem='$kode' and kdcbg='$cabang' and kdgudang='$gudang'")->getRow();
        echo json_encode($data);
    }

    // Data Barang Filter Cabang
    public function DataBarangCabang($kode, $cabang)
    {
        $db = db_connect();
        $data = $db->query("SELECT * FROM inv_barang where kodeitem='$kode' and kdcbg='$cabang' group by kodeitem")->getRow();
        echo json_encode($data);
    }

    //Get Per id Data Nobatch 
    public function getbatchData($kode, $gudang)
    {
        $session = session();
        $db = db_connect();
        $cabang = $session->get('cabang');
        if (!empty($kode)) {

            $query = "SELECT a.*, b.namabarang 
						from inv_nobatch a
						inner join inv_barang b on b.kodeitem=a.kodeitem 
						where b.kodeitem = '$kode' and a.cabang='$cabang' and a.gudang='$gudang'
						and b.kdcbg='$cabang' and b.kdgudang='$gudang' GROUP BY no_batch order by a.no_batch asc";
            $data  = $db->query($query)->getResult();
?>
            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered">
                    <tr class="headerx">
                        <th style="width:20%;">Kode item</th>
                        <th style="width:20%;">Nama Barang</th>
                        <th style="width:15%;">Nobatch</th>
                        <th style="width:15%;">Tanggal exp</th>
                        <th style="width:10%;">stok Batch</th>
                        <th style="width:10%;"></th>
                    </tr>

                    <?php
                    foreach ($data  as $row) {
                        if ($row->qty_saldo > '0') {
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
                                <td> <button type="button" onclick="post_batch('<?php echo $row->kodeitem; ?>','<?php echo $row->no_batch; ?>','<?php echo $row->tanggal_exp; ?>','<?php echo $row->qty_saldo; ?>')" class="btn green" id="btntutup" data-dismiss="modal"><i class="fa fa-plus"></i> </button></td>
                            </tr>
                    <?php }
                    } ?>

                <?php
            } else {
                echo "</table>
		  </div>";
            }
        }

        public function getbatchDataSo($kode, $gudang)
        {
            $session = session();
            $db = db_connect();
            $cabang = $session->get('cabang');
            if (!empty($kode)) {

                $querySo = "SELECT a.*, b.namabarang 
						from inv_nobatch a
						inner join inv_barang b on b.kodeitem=a.kodeitem 
						where b.kodeitem = '$kode' and a.cabang='$cabang' and a.gudang='$gudang'
						and b.kdcbg='$cabang' and b.kdgudang='$gudang' GROUP BY a.no_batch order by a.no_batch asc";
                $dataSo  = $db->query($querySo)->getResult();
                ?>
                    <div class="table-responsive">

                        <table class="table table-striped table-hover table-bordered">
                            <tr class="headerx">
                                <th style="width:20%;">Kode item</th>
                                <th style="width:20%;">Nama Barang</th>
                                <th style="width:15%;">Nobatch</th>
                                <th style="width:15%;">Tanggal exp</th>
                                <th style="width:10%;">stok Batch</th>
                                <th style="width:10%;"></th>
                            </tr>

                            <?php
                            foreach ($dataSo  as $row) {
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
                                    <td> <button type="button" onclick="post_batch('<?php echo $row->kodeitem; ?>','<?php echo $row->no_batch; ?>','<?php echo $row->tanggal_exp; ?>','<?php echo $row->qty_saldo; ?>')" class="btn green" id="btntutup" data-dismiss="modal"><i class="fa fa-plus"></i> </button></td>
                                </tr>
                            <?php } ?>

                        <?php
                    } else {
                        echo "</table>
		  </div>";
                    }
                }

                //Get Per id Data Harga Customer 
                public function gethargaData($kode, $item)
                {
                    $session = session();
                    $db = db_connect();
                    $cabang = $session->get('cabang');
                    if (!empty($kode)) {

                        $query = "SELECT * FROM inv_barang_harga where kodeitem='$item' and kode_customer='$kode' and cabang='$cabang'";
                        $data  = $db->query($query)->getResult();
                        ?>
                            <div class="table-responsive">

                                <table class="table table-striped table-hover table-bordered">
                                    <tr class="headerx">
                                        <th style="width:20%;">Kode item</th>
                                        <th style="width:20%;">Nama Barang</th>
                                        <th style="width:15%;">Customer</th>
                                        <th style="width:15%;">Harga Jual</th>
                                        <th style="width:10%;"></th>
                                    </tr>

                                    <?php
                                    foreach ($data  as $row) {
                                    ?>
                                        <tr>
                                            <td width="20" align="centerx" id="kode_brg">
                                                <?php echo $row->kodeitem; ?></a>
                                            </td>
                                            <td id="nama_brg">
                                                <?php echo $row->nama_barang; ?>
                                            </td>
                                            <td id="batch_brg"><?php echo $row->nama_customer; ?></td>
                                            <td id="tgl_exp_brg"> <?php echo number_format($row->bruto_cust); ?></td>
                                            <td> <button type="button" onclick="post_harga('<?php echo $row->kodeitem; ?>','<?php echo $row->kode_customer; ?>','<?php echo $row->bruto_cust; ?>','<?php echo $row->diskon_pr; ?>')" class="btn green" id="btntutup" data-dismiss="modal"><i class="fa fa-plus"></i> </button></td>
                                        </tr>
                                    <?php } ?>

                        <?php
                    } else {
                        echo "</table>
		  </div>";
                    }
                }

                public function pingdata()
                {
                    $db = db_connect();
                    $session = session();
                    $GetIp = $db->table('ci_sessions')->getWhere(['user_data' => $session->get('username')])->getRow();
                    $ipAddress = $GetIp->ip_address;
                    $pingResult = exec("ping -n 1 $ipAddress", $output, $result);
                    if ($result == 0) {
                        // Ping berhasil
                        $pingTime = $this->getAveragePingTime($output);
                        $colorClass = $this->getColorClass($output[5]);
                        $pingTime = explode(',', trim($pingResult));
                        $time = explode("=", trim($pingTime[2]));
                        $timedata = explode("ms", trim($time[1]));
                        echo json_encode([
                            'ipAddress' => $ipAddress,
                            'pingResult' => $output[2],
                            'pingResultdata' => $output[5][5],
                            'pingData' => implode("\n", $output),
                            'colorClass' => $pingResult,
                            'pingTime' => $timedata[0],
                            'status' => $result,
                        ]);
                    } else {
                        // Ping gagal
                        return "Ping gagal. Alamat IP mungkin tidak dapat dijangkau.";
                    }
                }

                // Fungsi untuk menentukan warna berdasarkan hasil ping
                private function getColorClass($pingResult)
                {
                    $pingTime = $this->extractPingTime($pingResult);
                    if ($pingTime <= 50) {
                        return 'text-success'; // Jika ping bagus (<= 50 ms), warna ijo
                    } elseif ($pingTime <= 100) {
                        return 'text-warning'; // Jika ping kurang (<= 100 ms), warna kuning
                    } else {
                        return 'text-primary'; // Jika ping tidak stabil (> 100 ms), warna biru
                    }
                }

                // Fungsi untuk mengekstrak waktu ping dari hasil ping
                private function extractPingTime($pingResult)
                {
                    // Anda perlu menyesuaikan kode ini dengan format keluaran ping pada sistem Anda
                    // Contoh, jika format keluaran adalah "time=50.123 ms", maka Anda dapat menggunakan ekstraksi berikut
                    if (preg_match('/time=(\d+\.\d+)/', $pingResult, $matches)) {
                        return (float)$matches[1];
                    }

                    return 0;
                }

                private function getAveragePingTime($output)
                {
                    $pingTimes = [];
                    foreach ($output as $line) {
                        $data = preg_match('/time=([\d.]+) ms/', $line, $matches);
                        if (preg_match('/time=([\d.]+) ms/', $line, $matches)) {
                            $pingTimes[] = (float) $matches[1];
                        }
                    }

                    if (count($pingTimes) > 0) {
                        $averagePingTime = array_sum($pingTimes) / count($pingTimes);
                        return $averagePingTime;
                    } else {
                        return -1; // Tidak ada data ping yang valid
                    }
                }
            }
