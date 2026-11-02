<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function dashboard()
    {

        $session = session();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $M_Dashboard =  model('Dashboard_model', 'M_Dasboard');

        if (empty(session()->get('username'))) {
            return redirect()->to('/');
        } else {
            $db = db_connect();
            $DataUser = $db->table("userlogin")->getWhere(['uidlogin' => $session->get('username')])->getRow();
            $DataSofile = $db->query("SELECT count(id) as jumlah FROM ar_sofile
             WHERE kodeuser='" . $session->get('username') . "' 
            and YEAR(tglso)= (select periode_tahun from ms_identity) 
            and MONTH(tglso)= (select periode_bulan from ms_identity)")->getRow();
            $DataSifileGetUser = $db->query("SELECT sum(a.totalsi) as jumlah FROM ar_sifile a
                                                INNER JOIN ar_sofile b ON a.kodeso = b.kodeso
                                                WHERE b.kodeuser='" . $session->get('username') . "' 
                                                and YEAR(a.tglsi)= (select periode_tahun from ms_identity) 
                                                and MONTH(a.tglsi)= (select periode_bulan from ms_identity)")->getRow();

            $DatareturGetUser = $db->query("SELECT COUNT(a.idretur) AS jumlah FROM ar_retur a
                                            INNER JOIN ar_sifile b ON a.kodesi = b.kodesi
                                            INNER JOIN  ar_sofile c ON b.kodeso = c.kodeso
                                            WHERE c.kodeuser='" . $session->get('username') . "' 
                                            and YEAR(a.tglretur)= (select periode_tahun from ms_identity) 
                                            and MONTH(a.tglretur)= (select periode_bulan from ms_identity)")->getRow();
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
                'report' => $M_Dashboard->report(),
                'reportJumDataPenjualan' => $M_Dashboard->HitungJumlahPenjualan(),
                'RpFKPenjualan' => $M_Dashboard->HitungRpFKPenjualanPeruser(),
                'RpMarketingPenjualan' => $M_Dashboard->HitungRpMarketingPenjualanPeruser(),
                'profile' => $model_Global->_LoadProfile(),
                'lr' => $model_Global->_labarugiberjalan_tahun(),
                'tahun' => 'Periode Tahun ' . $model_Global->_periodetahun(),
                'HitungFaktur' => $DataSifileGetUser->jumlah,
                'HitungSofile' => $DataSofile->jumlah,
                'HitungRetur' => $DatareturGetUser->jumlah,
                'DataUser' => $DataUser
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
