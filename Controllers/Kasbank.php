<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Kas / Bank
 **/

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_rpt_helper;

class Kasbank extends BaseController
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
    // =========================== PENERIMAAN KAS & BANK SELECTION START ======================//
    //Penerimaan Kas & Bank Page Start Here
    public function penerimaan()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 121);
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
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/penerimaan',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function penerimaantable($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 121);
        $db = db_connect();
        $query = "SELECT * FROM tr_penerimaan where terima_kodecbg='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and terima_tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(terima_tanggal)= (select periode_tahun from ms_identity) and
            month(terima_tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        foreach ($res as $value) {
            $JmlTerima = $db->query("SELECT sum(terimad_jumlah) as jumlah from tr_penerimaand where terimad_nomor = '$value->terima_nomor'")->getRow();
            if ($value->terima_status == 1) {
                $status = '<span class="badge badge-warning">Belum Posting</span>';
            } else if ($value->terima_status == 2) {
                $status =  '<span class="badge badge-success">Posting</span>';
            } else if ($value->terima_status == 4) {
                $status =  '<span class="badge badge-danger">Posting</span>';
            }

            if ($akses->udel == 1 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/penerimaan/entri/' . $value->terima_register) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->terima_register . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->terima_register . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/penerimaan/entri/' . $value->terima_register) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $count++;
            $data[] = [
                $count,
                $value->terima_nomor,
                date('d-m-Y', strtotime($value->terima_tanggal)),
                $value->terima_uraian,
                $akses->uharga == 1 ? number_format($JmlTerima->jumlah) : '',
                $status,
                $editview,
                $delete,
                $akses->ucetak == 1 ? '<a href="' . base_url('kasbank/transaksi/penerimaan/cetak/' . $value->terima_register) . '" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print text-white"></i> Cetak</a>' : '',
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function penerimaan_entri($id = '')
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/penerimaan_entri',
            'id' => $id ?? '',
            'bank' => $db->table("ms_akun")->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult(),
            'unit' => $db->table("ms_unit")->get()->getResult()
        ];
        return view('layout/layout', $data);
    }

    public function penerimaansave()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $DBAkun = $db->table('ms_akun');
        $DBTerima = $db->table('tr_penerimaan');
        $DBTerimaD = $db->table('tr_penerimaand');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 121);
        try {
            if ($akses->uadd == 1 || $akses->uedit == 1) {
                $db->transStart();
                //MENDAPATKAN NO URUT
                $tahun = date('Y', strtotime($input['tanggal']));
                $bulan = date('m', strtotime($input['tanggal']));
                $kodebukti = $DBAkun->getWhere(['kodeakun' => $input['kasbank']])->getRow()->kodem;
                $WhereAkun = ['year(terima_tanggal)' => $tahun, 'month(terima_tanggal)' => $bulan];
                $QTerima = $DBTerima->like('terima_nomor', $kodebukti);
                $QTerima = $DBTerima->where($WhereAkun);
                $max = $QTerima->get()->getNumRows();
                $nourut  = $max + 1;
                $nobukti  = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
                $RowTrTerima = $DBTerima->getWhere(['terima_nomor' => $nobukti, 'terima_kodecbg' => $session->get('cabang')])->getRow();
                if (empty($RowTrTerima)) {
                    $nomorbukti = $nobukti;
                } else {
                    $QTerima = $DBTerima->like('terima_nomor', $kodebukti);
                    $QTerima = $DBTerima->where($WhereAkun);
                    $max = $QTerima->get()->getNumRows();
                    $nourut  = $max + 1;
                    $nomorbukti  = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
                }

                if ($input['postingju'] == 'on') {
                    $SttsJurnal = 2;
                    $tglpost = $input['tglposting'];
                    $userpost = $session->get('username');
                } else {
                    $SttsJurnal = 1;
                    $tglpost = '';
                    $userpost = '';
                }

                //HEADER TR PENERIMAAN KAS & BANK
                $data = array(
                    'terima_tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'terima_kasbank' => $input['kasbank'],
                    'terima_kodecbg' => $session->get('cabang'),
                    'terima_penerima' => $input['penerima'],
                    'terima_uraian' => $input['keterangan'],
                    'terima_status' => $SttsJurnal,
                    'terima_userentry' => $session->get('username'),
                    'terima_byrcekgiro' => $input['metodebyr'],
                    'terima_tglcekgiro' => $input['tanggalcek'],
                    'terima_nocekgiro' => $input['nomorcek'],
                    'terima_posted' => $SttsJurnal,
                    'terima_tanggalposted' => $tglpost,
                    'terima_userposted' => $userpost,
                );
                $datalogs = [
                    'date' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                ];

                if ($param == 0) {
                    $data['terima_nomor'] = $nomorbukti;
                    $DBTerima->insert($data);
                    $register =  $db->insertID();
                    $datalogs['detail'] = 'tambah data Penerimaan KAS & BANK dengan nobukti ' . $nomorbukti;
                    $db->table("logs")->insert($datalogs);
                } else {
                    $DBTerima->where(['terima_register' => $param, 'terima_kodecbg' => $session->get('cabang')])->update($data);
                    $datalogs['detail'] = 'Update data Penerimaan KAS & BANK dengan nobukti ' . $input['nomorbukti'];
                    $db->table("logs")->insert($datalogs);
                    $DBTerimaD->delete(['terimad_register' => $param]);
                    $DBTrJurnal->delete(['novoucher' => $input['nomorbukti'], 'jenis' => 'JK', 'wbs' => 'KM']);
                    $DBTrkasbank->delete(['novoucher' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
                }

                //DETAIL TR PENERIMAAN KAS & BANK
                $kodeakun = $input['akun'];
                $ket      = $input['ket'];
                $jumlah   = $input['jumlah'];

                $jumdata  = count($kodeakun);
                $nourut = 0;
                for ($i = 0; $i < $jumdata; $i++) {
                    $nourut++;

                    $datad = array(
                        'terimad_nourut' => $nourut,
                        'terimad_akun' => $kodeakun[$i],
                        'terimad_uraian' => $ket[$i],
                        'terimad_jumlah' => str_replace(',', '', $jumlah[$i]),
                        'cabang' => $session->get('cabang')
                    );

                    $data_jurnal = array(
                        'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                        'keterangan' => $ket[$i],
                        'nourut' => $nourut,
                        'jenis' => 'JK',
                        'posted' => 'Y',
                        'wbs' => 'KM',
                        'kodeakun' => $kodeakun[$i],
                        'debet' => 0,
                        'kredit' => str_replace(',', '', $jumlah[$i]),
                        'userid' => $session->get('username'),
                        'kodecbg' => $session->get('cabang'),
                    );

                    $datatr = array(
                        'tanggal'         => date('Y-m-d', strtotime($input['tanggal'])),
                        'kasbank'         => $input['kasbank'],
                        'metodebyr'     => $input['metodebyr'],
                        'penerima'         => $input['penerima'],
                        'pemasukan'     => str_replace(',', '', $jumlah[$i]),
                        'pengeluaran'     => 0,
                        'tglentry'        => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                        'userid'        => $session->get('username'),
                        'cabang'        => $session->get('cabang'),
                    );

                    if ($kodeakun[$i] != "") {
                        if ($param == 0) {
                            $datad['terimad_register'] = $register;
                            $datad['terimad_nomor'] = $nomorbukti;
                            $DBTerimaD->insert($datad);
                            $data_jurnal['novoucher'] = $nomorbukti;
                            $data_jurnal['noref'] = $register;
                            if ($SttsJurnal == 2) {
                                $DBTrJurnal->insert($data_jurnal);
                            }
                            $datatr['novoucher'] = $nomorbukti;
                            $datatr['uraian'] = $ket[$i] . ' Pembayaran '  . $nomorbukti;
                            $DBTrkasbank->insert($datatr);
                        } else {
                            $datad['terimad_register'] = $param;
                            $datad['terimad_nomor'] = $input['nomorbukti'];
                            $DBTerimaD->insert($datad);
                            $data_jurnal['novoucher'] = $input['nomorbukti'];
                            $data_jurnal['noref'] = $param;
                            if ($SttsJurnal == 2) {
                                $DBTrJurnal->insert($data_jurnal);
                            }

                            $RowDataTerima = $DBTerima->getWhere(['terima_nomor' => $input['nomorbukti'], 'terima_kodecbg' => $session->get('cabang')])->getRow();
                            if ($RowDataTerima->terima_kodebyr != '') {
                                $kodebyr = $RowDataTerima->terima_kodebyr;
                            } else {
                                $kodebyr = $input['nomorbukti'];
                            }
                            $datatr['novoucher'] = $input['nomorbukti'];
                            $datatr['uraian'] = $ket[$i] . ' Pembayaran '  . $kodebyr;
                            $DBTrkasbank->insert($datatr);
                        }
                    }
                }
                $data_jurnal = array(
                    'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'keterangan' => $input['keterangan'],
                    'nourut' => 1,
                    'jenis' => 'JK',
                    'posted' => 'Y',
                    'wbs' => 'KM',
                    'kodeakun' => $input['kasbank'],
                    'debet' => $input['_h_total'],
                    'kredit' => 0,
                    'userid' => $session->get('username'),
                    'kodecbg' => $session->get('cabang'),
                );
                if ($param == 0) {
                    $data_jurnal['novoucher'] = $nomorbukti;
                    $data_jurnal['noref'] = $register;
                    if ($SttsJurnal == 2) {
                        $DBTrJurnal->insert($data_jurnal);
                    }
                    echo json_encode(['status' => 1, 'message' => $nomorbukti]);
                } else {
                    $data_jurnal['novoucher'] = $input['nomorbukti'];
                    $data_jurnal['noref'] = $param;
                    if ($SttsJurnal == 2) {
                        $DBTrJurnal->insert($data_jurnal);
                    }
                    echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
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
                echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
        }
    }

    public function PenerimaanDel($id)
    {
        $db = db_connect();
        $db->transStart();
        $DBTerima = $db->table('tr_penerimaan');
        $DBTerimaD = $db->table('tr_penerimaand');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $KodeTerima = $DBTerima->getWhere(['terima_register' => $id])->getRow()->terima_nomor;
        $delete = $DBTrkasbank->delete(['novoucher' => $KodeTerima]);
        $delete = $DBTerima->delete(['terima_register' => $id]);
        $delete = $DBTerimaD->delete(['terimad_register' => $id]);
        $delete = $DBTrJurnal->delete(['noref' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
        $db->transComplete();
    }

    public function PenerimaanCetak($param)
    {
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

            $queryh = "select * from tr_penerimaan where terima_register = '$param'";
            $queryd = "select * from tr_penerimaand inner join ms_akun on tr_penerimaand.terimad_akun=ms_akun.kodeakun where terimad_register = '$param'";

            $header = $db->query($queryh)->getRow();
            $detil  = $db->query($queryd)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('B', '', 'BT');
            $size   = array('', '', '');
            $pdf->setfont('Arial', 'B', 18);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('', '', 'B');
            $size  = array('12', '0', '18');
            $max   = array(5, 5, 20);
            $judul = array('Dari', '', 'Penerimaan');
            $fc     = array('0', '0', '0');
            $hc     = array('20', '20', '20');
            $pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 10);
            $pdf->SetWidths(array(70, 30, 30, 5, 55));
            $border = array('', '', '', '', '');
            $fc     = array('0', '0', '0', '0', '0');
            $pdf->SetFillColor(230, 230, 230);
            //$pdf->settextcolor(10,20,200);
            $pdf->setfont('Arial', '', 9);

            $pdf->FancyRow(array($header->terima_penerima, '', 'Nomor', ':', $header->terima_nomor), $fc, $border);
            $pdf->FancyRow(array('', '', 'Tanggal', ':', date('d M Y', strtotime($header->terima_tanggal))), $fc, $border);
            $pdf->FancyRow(array('', '', 'No. Cek', ':', ''), $fc, $border);
            $fc     = array('1', '0', '0', '0', '0');
            $pdf->ln(3);

            $pdf->SetWidths(array(70, 80, 5, 35));
            $border = array('TB', 'TB', 'TB', 'TB');
            $align  = array('L', 'L', 'L', 'R');
            $pdf->setfont('Arial', 'B', 10);
            //$pdf->SetFillColor(0,0,139);
            //$pdf->settextcolor(255,255,255);
            $fc = array('0', '0', '0', '0');
            $judul = array('Nama Pendapatan', 'Uraian', '', 'Nilai');
            $pdf->FancyRow2(8, $judul, $fc, $border, $align);
            $border = array('', '', '', '');
            $pdf->setfont('Arial', '', 10);
            $tot = 0;
            $subtot = 0;
            $tdisc  = 0;
            $border = array('', '', '', '');
            $align  = array('L', 'L', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(0);
            $tot = 0;
            foreach ($detil as $db) {
                $tot = $tot + $db->terimad_jumlah;
                $pdf->FancyRow(array($db->namaakun, $db->terimad_uraian, '', number_format($db->terimad_jumlah, 0, '.', ',')), $fc, $border, $align);
            }


            $pdf->SetFillColor(230);
            $border = array('B', 'B', 'B', 'B');
            $pdf->FancyRow(array('', '', '', ''), 0, $border);
            $pdf->ln(2);
            $pdf->SetWidths(array(100, 20, 35, 35));
            $border = array('', '', 'TB', 'TB');
            $align  = array('L', 'C', 'L', 'R');
            //$pdf->SetFillColor(0,0,139);
            //$pdf->settextcolor(255,255,255);
            $fc = array('0', '0', '0', '0');
            $pdf->FancyRow2(7, array('', '', 'Total', number_format($tot, 0, '.', ',')), $fc, $border, $align, 0);
            $pdf->settextcolor(0);
            $fc = array('0', '0', '0', '0');
            $border = array('', '', '', '');
            $border = array('', '', '', '');
            $pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, 0);
            $border = array('', '', '', '', '', '');
            $pdf->SetWidths(array(50, 10, 50, 10, 60, 10));
            $border = array('', '', '', '', '', '');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C');
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->FancyRow(array('Diserahkan Oleh,', '', 'Diterima Oleh,', '', 'Diketahui Oleh, ', ''), $fc, $border, $align);
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align);
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align);

            $style = array('', '', '', '', '', '');
            $size  = array('0', '0', '0', '0', '0', '0');
            $border = array('T', '', 'B', '', '', '');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(255, 255, 255);
            $pdf->settextcolor(0);
            $fc = array('0', '0', '0', '0', '0', '0');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C');
            $pdf->setfont('Arial', '', 10);
            $pdf->SetWidths(array(50, 10, 50, 10, 60, 10));
            $pdf->ln(5);
            $border = array('', '', '', '', '', '');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align, $style, $size);
            $border = array('B', '', 'B', '', 'B', '');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align, $style, $size);
            $border = array('', '', '', '', '', '');
            $align  = array('L', 'C', 'L', 'C', 'L', 'C');
            $pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.', ''), $fc, $border, $align, $style, $size);
            $pdf->settextcolor(0);
            $pdf->AliasNbPages();
            $pdf->output($param . '.PDF', 'I');
            exit;
        } else {

            header('location:' . base_url());
        }
    }

    // =========================== PEMBAYARAN KAS & BANK SELECTION START ======================//
    //Pembayaran Kas & Bank Page Start Here
    public function pembayaran()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 122);
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
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/pembayaran',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function pembayarantable()
    {
        $input = $this->request->getPost();
        $tgl1 = $input['tgl1'];
        $tgl2 = $input['tgl2'];
        $akun = $input['akun'];
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 122);
        $db = db_connect();
        $query = "SELECT * FROM tr_pengeluaran where keluar_kodecbg='$cabang'";
        if ($tgl1 !== "1" && $tgl2 !== "1" && $akun !== "") {
            $query .= " and keluar_kasbank='$akun' and keluar_tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(keluar_tanggal)= (select periode_tahun from ms_identity) and
            month(keluar_tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        foreach ($res as $value) {
            $JmlKeluar = $db->query("SELECT sum(keluard_jumlah) as jumlah from tr_pengeluarand where keluard_nomor = '$value->keluar_nomor'")->getRow();
            if ($value->keluar_status == 1) {
                $status = '<span class="badge badge-warning">Belum Posting</span>';
            } else if ($value->keluar_status == 2) {
                $status =  '<span class="badge badge-success">Posting</span>';
            } else if ($value->keluar_status == 4) {
                $status =  '<span class="badge badge-danger">Posting</span>';
            }

            if ($akses->udel == 1 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/pembayaran/entri/' . $value->keluar_register) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->keluar_register . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . $value->keluar_register . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/pembayaran/entri/' . $value->keluar_register) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $count++;
            $data[] = [
                $count,
                $value->keluar_nomor,
                date('d-m-Y', strtotime($value->keluar_tanggal)),
                $value->keluar_cekgirotanggal,
                $value->keluar_uraian,
                '<button class="btn btn-dark" onclick="ShowDetail(' . $value->keluar_register . ')"><i class="fa-solid fa-search text-white"></i></button>',
                $akses->uharga == 1 ? number_format($JmlKeluar->jumlah) : '',
                $status,
                $editview,
                $delete,
                $akses->ucetak == 1 ? '<a href="' . base_url('kasbank/transaksi/pembayaran/cetak/' . $value->keluar_register) . '" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print text-white"></i> Cetak</a>' : '',
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function pembayarantableDetailData($data)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $cabang = $session->get('cabang');
        $db = db_connect();
        $query = $db->query("SELECT * from tr_pengeluarand where keluard_register='$data'")->getResult();
        $result = array();
        $count = 0;
        foreach ($query as $value) {
            $count++;
            $RowAkun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->keluard_akun'")->getRow();
?>
            <tr>
                <td align="centerx"><?php echo $count; ?></a></td>
                <td id="kodeakun"><?php echo $value->keluard_akun; ?></td>
                <td id="namaakun"><?php echo $RowAkun->namaakun; ?></td>
                <td id="uraian"><?php echo $value->keluard_uraian; ?></td>
                <td id="jumlah"> <?php echo number_format($value->keluard_jumlah); ?></td>
            </tr>
<?php }
    }

    public function pembayaran_entri($id = '')
    {
        $db = db_connect();
        $session = session();
        $cabang = $session->get('cabang');
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/pembayaran_entri',
            'id' => $id ?? '',
            'bank' => $db->table("ms_akun")->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult(),
            'unit' => $db->table("ms_unit")->get()->getResult()
        ];
        return view('layout/layout', $data);
    }

    public function pembayaransave()
    {
        error_reporting(0);
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $DBAkun = $db->table('ms_akun');
        $DBKeluar = $db->table('tr_pengeluaran');
        $DBKeluarD = $db->table('tr_pengeluarand');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 122);
        try {
            if ($akses->uadd == 1 || $akses->uedit == 1) {
                $db->transStart();
                //MENDAPATKAN NO URUT
                $tahun = date('Y', strtotime($input['tanggal']));
                $bulan = date('m', strtotime($input['tanggal']));
                $kodebukti = $DBAkun->getWhere(['kodeakun' => $input['kasbank']])->getRow()->kodek;
                $WhereAkun = ['year(keluar_tanggal)' => $tahun, 'month(keluar_tanggal)' => $bulan];
                $QKeluar = $DBKeluar->like('keluar_nomor', $kodebukti);
                $QKeluar = $DBKeluar->where($WhereAkun);
                $max = $QKeluar->get()->getNumRows();
                $nourut  = $max + 1;
                $nobukti  = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
                $RowTrKeluar = $DBKeluar->getWhere(['keluar_nomor' => $nobukti, 'keluar_kodecbg' => $input['unit']])->getRow();
                if (empty($RowTrKeluar)) {
                    $nomorbukti = $nobukti;
                } else {
                    $QKeluar = $DBKeluar->like('keluar_nomor', $kodebukti);
                    $QKeluar = $DBKeluar->where($WhereAkun);
                    $max = $QKeluar->get()->getNumRows();
                    $nourut  = $max + 1;
                    $nomorbukti  = $kodebukti . '' . $tahun . '.' . $bulan . '.' . str_pad($nourut, 4, '0', STR_PAD_LEFT);
                }

                if ($input['postingju'] == 'on') {
                    $SttsJurnal = 2;
                    $tglpost = $input['tglposting'];
                    $userpost = $session->get('username');
                } else {
                    $SttsJurnal = 1;
                    $tglpost = '';
                    $userpost = '';
                }

                $data = array(
                    'keluar_tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'keluar_kasbank' => $input['kasbank'],
                    'keluar_cekgiro' => $input['pembayaran'],
                    'keluar_cekgirotanggal' => date('Y-m-d', strtotime($input['tanggalcek'])),
                    'keluar_cekgironomor' => $input['nomorcek'],
                    'keluar_penerima' => $input['penerima'],
                    'keluar_uraian' => $input['keterangan'],
                    'keluar_kodecbg' => $input['unit'],
                    'keluar_userentry' => $session->get('username'),
                    'keluar_status' => $SttsJurnal,
                    'keluar_posted' => $SttsJurnal,
                    'keluar_tanggalposted' => $tglpost,
                    'keluar_userposted' => $userpost,
                );

                $datalogs = [
                    'date' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                ];

                if ($param == 0) {
                    $data['keluar_nomor'] = $nomorbukti;
                    $DBKeluar->insert($data);
                    $register =  $db->insertID();
                    $datalogs['detail'] = 'tambah data Pengeluaran KAS & BANK dengan nobukti ' . $nomorbukti;
                    $db->table("logs")->insert($datalogs);
                } else {
                    $DBKeluar->where(['keluar_register' => $param, 'keluar_kodecbg' => $input['unit']])->update($data);
                    $datalogs['detail'] = 'Update data Pengeluaran KAS & BANK dengan nobukti ' . $input['nomorbukti'];
                    $db->table("logs")->insert($datalogs);
                    $DBKeluarD->delete(['keluard_register' => $param]);
                    $DBTrJurnal->delete(['novoucher' => $input['nomorbukti'], 'jenis' => 'JK', 'wbs' => 'KK']);
                    $DBTrkasbank->delete(['novoucher' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
                }

                //DETAIL TR PENERIMAAN KAS & BANK
                $kodeakun = $input['akun'];
                $ket      = $input['ket'];
                $jumlah   = $input['jumlah'];

                $jumdata  = count($kodeakun);


                $nourut = 0;
                for ($i = 0; $i < $jumdata; $i++) {
                    $nourut++;
                    $datad = array(
                        'keluard_nourut' => $nourut,
                        'keluard_akun' => $kodeakun[$i],
                        'keluard_uraian' => $ket[$i],
                        'keluard_jumlah' => str_replace(',', '', $jumlah[$i])

                    );

                    $data_jurnal = array(
                        'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                        'keterangan' => $ket[$i],
                        'nourut' => $nourut,
                        'jenis' => 'JK',
                        'posted' => 'Y',
                        'wbs' => 'KK',
                        'kodeakun' => $kodeakun[$i],
                        'debet' => str_replace(',', '', str_replace(',', '', $jumlah[$i])),
                        'kredit' => 0,
                        'userid' => $session->get('username'),
                    );

                    $datatr = array(
                        'tanggal'         => date('Y-m-d', strtotime($input['tanggal'])),
                        'kasbank'         => $input['kasbank'],
                        'metodebyr'     => $input['pembayaran'],
                        'penerima'         => $input['penerima'],
                        'pemasukan'     => 0,
                        'pengeluaran'     => str_replace(',', '', $jumlah[$i]),
                        'tglentry'        => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                        'userid'        => $session->get('username'),
                        'cabang'        => $session->get('cabang'),
                    );

                    if ($kodeakun[$i] != "") {
                        if ($param == 0) {
                            $datad['keluard_register'] = $register;
                            $datad['keluard_nomor'] = $nomorbukti;
                            $DBKeluarD->insert($datad);
                            $data_jurnal['novoucher'] = $nomorbukti;
                            $data_jurnal['noref'] = $register;
                            if ($SttsJurnal == 2) {
                                $DBTrJurnal->insert($data_jurnal);
                            }

                            $datatr['novoucher'] = $nomorbukti;
                            $datatr['uraian'] = $ket[$i] . ' Pembayaran '  . $nomorbukti;
                            $DBTrkasbank->insert($datatr);
                        } else {
                            $datad['keluard_register'] = $param;
                            $datad['keluard_nomor'] = $input['nomorbukti'];
                            $DBKeluarD->insert($datad);
                            $data_jurnal['novoucher'] = $input['nomorbukti'];
                            $data_jurnal['noref'] = $param;
                            if ($SttsJurnal == 2) {
                                $DBTrJurnal->insert($data_jurnal);
                            }
                            $RowDataKeluar = $DBKeluar->getWhere(['keluar_nomor' => $input['nomorbukti'], 'keluar_kodecbg' => $input['unit']])->getRow();
                            if ($RowDataKeluar->keluar_kodebyr != '') {
                                $kodebyr = $RowDataKeluar->keluar_kodebyr;
                            } else {
                                $kodebyr = $input['nomorbukti'];
                            }
                            $datatr['novoucher'] = $input['nomorbukti'];
                            $datatr['uraian'] = $ket[$i] . ' Pembayaran '  . $kodebyr;
                            $DBTrkasbank->insert($datatr);
                        }
                    }
                }

                $data_jurnal = array(
                    'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'keterangan' => $input['keterangan'],
                    'nourut' => $nourut,
                    'jenis' => 'JK',
                    'posted' => 'Y',
                    'wbs' => 'KK',
                    'kodeakun' => $input['kasbank'],
                    'debet' => 0,
                    'kredit' => $input['_h_total'],
                    'userid' => $session->get('username'),
                );
                if ($param == 0) {
                    $data_jurnal['novoucher'] = $nomorbukti;
                    $data_jurnal['noref'] = $register;
                    if ($SttsJurnal == 2) {
                        $DBTrJurnal->insert($data_jurnal);
                    }
                    echo json_encode(['status' => 1, 'message' => $nomorbukti]);
                } else {
                    $data_jurnal['novoucher'] = $input['nomorbukti'];
                    $data_jurnal['noref'] = $param;
                    if ($SttsJurnal == 2) {
                        $DBTrJurnal->insert($data_jurnal);
                    }
                    echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
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
                echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
        }
    }

    public function PembayaranDel($id)
    {
        $db = db_connect();
        $db->transStart();
        $DBKeluar = $db->table('tr_pengeluaran');
        $DBKeluarD = $db->table('tr_pengeluarand');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $KodeKeluar = $DBKeluar->getWhere(['keluar_register' => $id])->getRow()->keluar_nomor;
        $delete = $DBTrkasbank->delete(['novoucher' => $KodeKeluar]);
        $delete = $DBKeluar->delete(['keluar_register' => $id]);
        $delete = $DBKeluarD->delete(['keluard_register' => $id]);
        $delete = $DBTrJurnal->delete(['noref' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
        $db->transComplete();
    }

    public function PembayaranCetak($param)
    {
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

            $queryh = "select * from tr_pengeluaran where keluar_register = '$param'";
            $queryd = "select * from tr_pengeluarand inner join ms_akun on tr_pengeluarand.keluard_akun=ms_akun.kodeakun where keluard_register = '$param'";

            $header = $db->query($queryh)->getRow();
            $detil  = $db->query($queryd)->getResult();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('B', '', 'BT');
            $size   = array('', '', '');
            $pdf->setfont('Arial', 'B', 18);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('', '', 'B');
            $size  = array('12', '0', '18');
            $max   = array(5, 5, 20);
            $judul = array('Penerima', '', 'Pembayaran');
            $fc     = array('0', '0', '0');
            $hc     = array('20', '20', '20');
            $pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 10);
            $pdf->SetWidths(array(70, 30, 30, 5, 55));
            $border = array('', '', '', '', '');
            $fc     = array('0', '0', '0', '0', '0');
            $pdf->setfont('Arial', '', 9);

            $pdf->FancyRow(array($header->keluar_penerima, '', 'Nomor', ':', $header->keluar_nomor), $fc, $border);
            $pdf->FancyRow(array('', '', 'Tanggal', ':', date('d M Y', strtotime($header->keluar_tanggal))), $fc, $border);
            $pdf->FancyRow(array('', '', 'No. Cek', ':', $header->keluar_cekgironomor), $fc, $border);
            $fc     = array('1', '0', '0', '0', '0');
            $pdf->ln(3);

            $pdf->SetWidths(array(70, 80, 5, 35));
            $border = array('TB', 'TB', 'TB', 'TB');
            $align  = array('L', 'L', 'L', 'R');
            $pdf->setfont('Arial', 'B', 10);
            $fc = array('0', '0', '0', '0');
            $judul = array('Nama Beban / Biaya', 'Uraian', '', 'Nilai');
            $pdf->FancyRow2(8, $judul, $fc, $border, $align);
            $border = array('', '', '', '');
            $pdf->setfont('Arial', '', 10);
            $tot = 0;
            $subtot = 0;
            $tdisc  = 0;
            $border = array('', '', '', '');
            $align  = array('L', 'L', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $tot = 0;
            foreach ($detil as $db) {
                $tot = $tot + $db->keluard_jumlah;
                $pdf->FancyRow(array($db->namaakun, $db->keluard_uraian, '', number_format($db->keluard_jumlah, 0, '.', ',')), $fc, $border, $align);
            }


            $pdf->SetFillColor(230);
            $border = array('B', 'B', 'B', 'B');
            $pdf->FancyRow(array('', '', '', ''), 0, $border);
            $pdf->ln(2);
            $pdf->SetWidths(array(100, 20, 35, 35));
            $border = array('', '', 'TB', 'TB');
            $align  = array('L', 'C', 'L', 'R');
            $fc = array('0', '0', '0', '0');
            $pdf->FancyRow2(7, array('', '', 'Total', number_format($tot, 0, '.', ',')), $fc, $border, $align, 0);
            $pdf->settextcolor(0);
            $fc = array('0', '0', '0', '0');
            $border = array('', '', '', '');
            $border = array('', '', '', '');
            $pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, 0);
            $border = array('', '', '', '', '', '');
            $pdf->SetWidths(array(50, 10, 50, 10, 60, 10));
            $border = array('', '', '', '', '', '');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C');
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->FancyRow(array('Dibuat Oleh,', '', 'Diterima Oleh,', '', 'Disetujui Oleh, ', ''), $fc, $border, $align);
            $fc = array('0', '0', '0', '0', '0', '0');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align);
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align);

            $style = array('', '', '', '', '', '');
            $size  = array('0', '0', '0', '0', '0', '0');
            $border = array('T', '', 'B', '', '', '');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(255, 255, 255);
            $pdf->settextcolor(0);
            $fc = array('0', '0', '0', '0', '0', '0');
            $align  = array('C', 'C', 'C', 'C', 'C', 'C');
            $pdf->setfont('Arial', '', 10);
            $pdf->SetWidths(array(50, 10, 50, 10, 60, 10));
            $pdf->ln(5);
            $border = array('', '', '', '', '', '');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align, $style, $size);
            $border = array('B', '', 'B', '', 'B', '');
            $pdf->FancyRow(array('', '', '', '', '', ''), $fc, $border, $align, $style, $size);
            $border = array('', '', '', '', '', '');
            $align  = array('L', 'C', 'L', 'C', 'L', 'C');
            $pdf->FancyRow(array('Tgl.', '', 'Tgl.', '', 'Tgl.', ''), $fc, $border, $align, $style, $size);
            $pdf->settextcolor(0);
            $pdf->AliasNbPages();
            $pdf->output($param . '.PDF', 'I');
            exit;
        } else {

            header('location:' . base_url());
        }
    }

    // =========================== TRANSFER BANK KAS & BANK SELECTION START ======================//
    //Transfer Bank Kas & Bank Page Start Here
    public function TransferKasBank()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 123);
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
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/transfer_bank',
            'tambah' => $tambah
        ];
        return view('layout/layout', $data);
    }

    public function TransferKasBankEntri($id = '')
    {
        $db = db_connect();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/transfer_bank_entri',
            'id' => $id ?? '',
            'bank' => $db->table("ms_akun")->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult(),
            'bankTujuan' => $db->table("ms_akun")->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult(),
        ];
        return view('layout/layout', $data);
    }

    public function TransferKasBankSave()
    {

        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $DBAkun = $db->table('ms_akun');
        $DBTransfer = $db->table('tr_transfer');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $session = session();
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 123);
        try {
            if ($akses->uadd == 1 || $akses->uedit == 1) {
                $db->transStart();
                $nomorbukti = $model_Global->_Autonomor('KT');
                //MENDAPATKAN NO URUT
                $DataTransfer = $DBTransfer->getWhere(['nomor' => $nomorbukti, 'cabang' => $session->get('cabang')])->getRow();
                if (empty($DataTransfer)) {
                    $nobukti = $nomorbukti;
                } else {
                    $nobukti = $model_Global->_Autonomor('KT');
                }

                $data = array(
                    'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'bank_sumber' => $input['sumber'],
                    'bank_tujuan' => $input['tujuan'],
                    'uraian' => $input['keterangan'],
                    'jumlah' => str_replace(',', '', $input['jumlah']),
                    'cabang' => $session->get('cabang'),
                    'userid' => $session->get('username'),
                );

                $datalogs = [
                    'date' => date('Y-m-d H:i:s'),
                    'userid' => $session->get('username'),
                ];

                if ($param == 0) {
                    $data['nomor'] = $nobukti;
                    $DBTransfer->insert($data);
                    $datalogs['detail'] = 'tambah data Transfer KAS & BANK dengan nobukti ' . $nobukti;
                    $db->table("logs")->insert($datalogs);
                } else {
                    $DBTransfer->where(['nomor' => $input['nomorbukti'], 'cabang' => $session->get('cabang')])->update($data);
                    $datalogs['detail'] = 'Update data Transfer KAS & BANK dengan nobukti ' . $input['nomorbukti'];
                    $db->table("logs")->insert($datalogs);
                    $DBTrkasbank->delete(['novoucher' => $input['nomorbukti'], 'cabang' => $session->get('cabang')]);
                }
                $Terima = $DBAkun->getWhere(['kodeakun' => $input['tujuan']])->getRow();
                $Keluar = $DBAkun->getWhere(['kodeakun' => $input['sumber']])->getRow();
                //DETAIL TR KAS & BANK
                // OUT Saldo
                $datatrout = array(
                    'tanggal'         => date('Y-m-d', strtotime($input['tanggal'])),
                    'kasbank'         => $input['sumber'],
                    'metodebyr'     => 'F',
                    'penerima'         => $Terima->namaakun,
                    'pemasukan'     => 0,
                    'pengeluaran'     => str_replace(',', '', $input['jumlah']),
                    'tglentry'        => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                    'userid'        => $session->get('username'),
                    'cabang'        => $session->get('cabang'),
                );
                // Jurnal Kredit
                $JurnalKredit = array(
                    'novoucher' => $model_Global->_Autonomor('JU'),
                    'tanggal' => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                    'keterangan' => $input['keterangan'],
                    'nourut' => 2,
                    'jenis' => 'KT',
                    'posted' => 'Y',
                    'wbs' => $Keluar->kodek,
                    'kodeakun' => $input['sumber'],
                    'kredit' => str_replace(',', '', $input['jumlah']),
                    'debet' => 0,
                    'userid' => $session->get('username'),
                );
                // IN Saldo
                $datatrin = array(
                    'tanggal'         => date('Y-m-d', strtotime($input['tanggal'])),
                    'kasbank'         => $input['tujuan'],
                    'metodebyr'     => 'F',
                    'penerima'         => $Terima->namaakun,
                    'pemasukan'     => str_replace(',', '', $input['jumlah']),
                    'pengeluaran'     => 0,
                    'tglentry'        => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                    'userid'        => $session->get('username'),
                    'cabang'        => $session->get('cabang'),
                );
                // Jurnal Debet
                $JurnalDebet = array(
                    'novoucher' => $model_Global->_Autonomor('JU'),
                    'tanggal' => date('Y-m-d H:i:s', strtotime($input['tanggal'] . date('H:i:s'))),
                    'keterangan' => $input['keterangan'],
                    'nourut' => 2,
                    'jenis' => 'KT',
                    'posted' => 'Y',
                    'wbs' => $Terima->kodem,
                    'kodeakun' => $input['tujuan'],
                    'kredit' => 0,
                    'debet' => str_replace(',', '', $input['jumlah']),
                    'userid' => $session->get('username'),
                );
                if ($param == 0) {
                    $datatrout['novoucher'] = $nobukti;
                    $datatrout['uraian'] = $input['keterangan'] . ' Transfer Kas/Bank '  . $nobukti;
                    $DBTrkasbank->insert($datatrout);

                    $datatrin['novoucher'] = $nobukti;
                    $datatrin['uraian'] = $input['keterangan'] . ' Transfer Kas/Bank '  . $nobukti;
                    $DBTrkasbank->insert($datatrin);
                    //Insert Jurnal Kredit
                    $JurnalKredit['noref'] = $nobukti;
                    $DBTrJurnal->insert($JurnalKredit);
                    //Insert Jurnal Debet
                    $JurnalDebet['noref'] = $nobukti;
                    $DBTrJurnal->insert($JurnalDebet);
                    $model_Global->_updatecounter1('KT');
                    $model_Global->_updatecounter1('JU');
                    echo json_encode(['status' => 1, 'message' => $nobukti]);
                } else {
                    //Insert DataOut
                    $datatrout['novoucher'] = $input['nomorbukti'];
                    $datatrout['uraian'] = $input['keterangan'] . ' Transfer Kas/Bank '  . $input['nomorbukti'];
                    $DBTrkasbank->insert($datatrout);
                    //Insert DataIn
                    $datatrin['novoucher'] = $input['nomorbukti'];
                    $datatrin['uraian'] = $input['keterangan'] . ' Transfer Kas/Bank '  . $input['nomorbukti'];
                    $DBTrkasbank->insert($datatrin);
                    //Insert Jurnal Kredit
                    $JurnalKredit['noref'] = $input['nomorbukti'];
                    $DBTrJurnal->insert($JurnalKredit);
                    //Insert Jurnal Debet
                    $JurnalDebet['noref'] = $input['nomorbukti'];
                    $DBTrJurnal->insert($JurnalDebet);
                    echo json_encode(['status' => 1, 'message' => $input['nomorbukti']]);
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
                echo json_encode(['status' => 2, 'message' => 'Akses Di tolak']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 2, 'message' => $e->getMessage()]);
        }
    }

    public function TransferKasBankTable($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 122);
        $db = db_connect();
        $query = "SELECT * FROM tr_transfer where cabang='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and tanggal between '$tgl1' and '$tgl2'";
        } else {
            $query .= " and year(tanggal)= (select periode_tahun from ms_identity) and
            month(tanggal)= (select periode_bulan from ms_identity)";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        foreach ($res as $value) {

            if ($akses->udel == 1 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/transferbank/entri/' . $value->nomor) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . "'" . $value->nomor . "'" . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
            } else if ($akses->udel == 1 && $akses->uedit == 0) {
                $delete = '<button class="btn btn-danger" onclick="data_delete(' . "'" . $value->nomor . "'" . ')"><i class="fa-solid fa-trash text-white"></i> Delete</button>';
                $editview = '';
            } else if ($akses->udel == 0 && $akses->uedit == 1) {
                $editview = '<a href="' . base_url('kasbank/transaksi/transferbank/entri/' . $value->nomor) . '" class="btn btn-primary"><i class="fa-solid fa-edit text-white"></i> Edit</a>';
                $delete = '';
            } else {
                $editview = '';
                $delete = '';
            }
            $count++;
            $data[] = [
                $value->nomor,
                date('d-m-Y', strtotime($value->tanggal)),
                $db->table("ms_akun")->getWhere(['kodeakun' => $value->bank_sumber])->getRow()->namaakun,
                $db->table("ms_akun")->getWhere(['kodeakun' => $value->bank_tujuan])->getRow()->namaakun,
                $value->uraian,
                $akses->uharga == 1 ? number_format($value->jumlah) : '',
                $editview,
                $delete,
                $akses->ucetak == 1 ? '<a href="' . base_url('kasbank/transaksi/transferbank/cetak/' . $value->nomor) . '" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print text-white"></i> Cetak</a>' : '',
            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function TransferKasBankDel($id)
    {
        $db = db_connect();
        $db->transStart();
        $DBTransfer = $db->table('tr_transfer');
        $DBTrJurnal = $db->table('tr_jurnal');
        $DBTrkasbank = $db->table('tr_transaksikasbank');
        $delete = $DBTrkasbank->delete(['novoucher' => $id]);
        $delete = $DBTransfer->delete(['nomor' => $id]);
        $delete = $DBTrJurnal->delete(['noref' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
        $db->transComplete();
    }

    public function TransferKasBankCetak($param)
    {
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

            $queryh = "select * from  tr_transfer where nomor = '$param'";

            $header = $db->query($queryh)->getRow();
            $pdf = new simkeu_nota_helper();
            $pdf->setID($nama_usaha, $alamat1, $alamat2, $hp, $email);
            $pdf->setjudul('');
            $pdf->setsubjudul('');
            $pdf->addpage("P", "A4");
            $pdf->setsize("P", "A4");
            $pdf->SetWidths(array(70, 30, 90));
            $border = array('', '', 'BT');
            $size   = array('', '', '');
            $pdf->setfont('Arial', 'B', 18);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $align = array('L', 'C', 'L');
            $style = array('', '', 'B');
            $size  = array('12', '0', '18');
            $max   = array(5, 5, 20);
            $judul = array('', '', 'Transfer Bank');
            $fc     = array('0', '0', '0');
            $hc     = array('20', '20', '20');
            $pdf->FancyRow2(10, $judul, $fc,  $border, $align, $style, $size, $max);
            $pdf->ln(1);
            $pdf->setfont('Arial', 'B', 10);
            $pdf->SetWidths(array(70, 30, 30, 5, 55));
            $border = array('', '', '', '', '');
            $fc     = array('0', '0', '1', '1', '1');
            $pdf->SetFillColor(230, 230, 230);
            //$pdf->settextcolor(10,20,200);
            $pdf->setfont('Arial', '', 10);

            $pdf->FancyRow(array('', '', 'Nomor', ':', $header->nomor), $fc, $border);
            $pdf->FancyRow(array('', '', 'Tanggal', ':', date('d-m-Y', strtotime($header->tanggal))), $fc, $border);
            $fc     = array('1', '0', '0', '0', '0');
            $pdf->ln(3);
            $border = array('T', 'T', 'T', 'T', 'T');
            $fc     = array('0', '0', '0', '0', '0');
            $pdf->SetLineWidth(1);
            $pdf->FancyRow(array('', '', '', '', ''), $fc, $border);
            $pdf->SetLineWidth(0);

            $pdf->SetWidths(array(90, 10, 90));
            $border = array('TB', '', 'TB');
            $fc     = array('0', '0', '0');
            $pdf->FancyRow(array('Dari Kas/Bank', '', 'Ke Kas/Bank'), $fc, $border);
            //$pdf->ln(3);
            $pdf->SetWidths(array(30, 60, 10, 30, 60));
            $align  = array('L', 'L', '', 'L', 'L');
            $border = array('', '', '', '', '');
            $fc     = array('1', '1', '0', '1', '1');
            $pdf->SetFillColor(230, 230, 230);
            $terbilang = ucwords($model_Global->terbilang($header->jumlah)) . ' Rupiah';
            $banks     = $db->table("ms_akun")->getWhere(['kodeakun' => $header->bank_sumber])->getRow()->namaakun;
            $bankt     = $db->table("ms_akun")->getWhere(['kodeakun' => $header->bank_tujuan])->getRow()->namaakun;
            $pdf->FancyRow(array('Kas/Bank', $banks, '', 'Kas/Bank', $bankt), $fc, $border);
            $pdf->FancyRow(array('Nilai Transfer', number_format($header->jumlah, 0, '.', ','), '', 'Nilai Transfer', number_format($header->jumlah, 0, '.', ',')), $fc, $border);
            $pdf->FancyRow(array('Terbilang', $terbilang, '', 'Terbilang', $terbilang), $fc, $border);
            $pdf->FancyRow(array('', '', '', '', ''), $fc, $border);

            $pdf->ln(5);
            $pdf->SetWidths(array(90, 10, 30, 60));
            $border = array('TB', '', 'TB', 'TB');
            $fc     = array('0', '0', '1', '1');
            $align  = array('L', '', 'L', 'R');
            $pdf->SetFillColor(230, 230, 230);
            $pdf->settextcolor(0);

            $pdf->FancyRow(array('Keterangan', '', 'Biaya Transfer', number_format(0, 0, '.', ',')), $fc, $border, $align, 0);
            $border = array('', '', '', '');
            $pdf->FancyRow(array($header->uraian, '', '', ''), $fc, $border, $align);
            $fc = array('0', '0', '0', '0');
            $pdf->FancyRow(array('', '', '', ''), $fc, $border, $align);
            $pdf->FancyRow(array('', '', '', ''), $fc, $border, $align);

            $style = array('', '', 'B', 'B');
            $size  = array('0', '0', '0', '0');
            $border = array('T', '', '', '');
            $pdf->SetFillColor(0, 0, 139);
            $pdf->settextcolor(255, 255, 255);
            $fc = array('0', '0', '0', '0');
            $pdf->FancyRow(array('', '', '', ''), $fc, $border, $align, $style, $size);
            $pdf->settextcolor(0);
            $pdf->AliasNbPages();
            $pdf->output($param . '.PDF', 'I');
            exit;
        } else {

            header('location:' . base_url());
        }
    }

    // =========================== BUKU KAS & BANK SELECTION START ======================//
    //Buku Kas & Bank Page Start Here
    public function bukukas()
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
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/bukukas',
        ];
        return view('layout/layout', $data);
    }

    public function bukukastable($bln, $thn, $akun)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 124);
        $db = db_connect();
        $tahun = $thn;
        $bulan = $bln;

        if ($thn != "1" && $thn !== "1" && $thn !== "1") {
            $qsaldo = $db->table('ms_akunsaldo')->select('debet, kredit')->getWhere(['kodeakun' => $akun, 'bulan' => $bulan, 'tahun' => $tahun])->getRow();
            if ($qsaldo) {
                $saldo_db = $qsaldo->debet;
                $saldo_kr = $qsaldo->kredit;
            } else {
                $saldo_db = 0;
                $saldo_kr = 0;
            }
            $jenis_akun = $model_Global->_jenisakun($akun);
            $saldo  = $saldo_db + $saldo_kr;
            $query =
                "SELECT tr_jurnal.novoucher from tr_jurnal inner join ms_akun on tr_jurnal.kodeakun = ms_akun.kodeakun
                where YEAR(tanggal) ='$tahun' and month(tanggal) ='$bulan' and tr_jurnal.kodeakun = '$akun' GROUP BY novoucher order by tanggal, novoucher, nourut";
        } else {
            $query = "select * from tr_jurnal inner join ms_akun on tr_jurnal.kodeakun = ms_akun.kodeakun
            where tanggal='0000-00-00' order by tanggal, nourut";
            $saldo = 0;
            $jenis_akun = "";
        }
        $res = $db->query($query)->getResult();
        $result = array();
        $data = [];
        $count = 0;
        $totdb  = 0;
        $totkr  = 0;
        $data[] = [
            $count,
            'SALDO',
            '',
            '',
            '',
            '',
            $akses->uharga == 1 ? number_format($saldo) : '0',
            '',
        ];
        foreach ($res as $value) {
            $db = db_connect();
            $builder = $db->table('tr_jurnal a');
            $builder->select('a.novoucher, a.debet , a.kredit, a.kodeakun, a.wbs, a.keterangan, a.tanggal, a.jenis, b.namaakun');
            $builder->join('ms_akun b', 'a.kodeakun = b.kodeakun');
            $builder->where(['a.novoucher' => $value->novoucher, 'a.kodeakun !=' => $akun]);
            $ResultDataList = $builder->get()->getResult();
            foreach ($ResultDataList as $val) {
                if ($val->kodeakun !== $akun) {
                    $ResultDataKodekKodem = $db->query("SELECT * FROM ms_akun where kodek='$val->wbs'")->getRow();
                    if (empty($ResultDataKodekKodem)) {
                        $pemasukan = $val->kredit;
                        $pengeluaran = $val->debet;
                    } else {
                        if ($val->jenis == 'KT') {
                            $pemasukan = $val->kredit;
                            $pengeluaran = 0;
                        } else {
                            $pemasukan = 0;
                            $pengeluaran = $val->debet;
                        }
                    }
                    if ($jenis_akun == "D") {
                        $saldo = $saldo + $pemasukan - $pengeluaran;
                    } else {
                        $saldo = $saldo - $pengeluaran + $pemasukan;
                    }
                    $count++;
                    $data[] = [
                        $count,
                        $val->novoucher,
                        date('d-m-Y', strtotime($val->tanggal)),
                        $val->keterangan,
                        $akses->uharga == 1 ? number_format($pemasukan) : '0',
                        $akses->uharga == 1 ? number_format($pengeluaran) : '0',
                        $akses->uharga == 1 ? number_format($saldo) : '0',
                        $val->namaakun,
                    ];
                }
            }
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }

    public function laporan()
    {
        $session = session();
        $db = db_connect();
        $cabang = $db->table("ms_unit")->get()->getResult();
        $akun = $db->table('ms_akun')->get()->getResult();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/laporan',
            'cbg' => $cabang,
            'bank' => $akun,
        ];
        return view('layout/layout', $data);
    }

    public function CetakLaporan($x)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $db = db_connect();
        $cek = $session->get('level');
        $unit = $session->get('cabang');
        if (!empty($cek)) {
            $profile = $model_Global->_LoadProfile();
            $nama_usaha = $profile->nama_usaha;
            $motto = '';
            $alamat = '';
            $namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
            $data  = explode("~", $x);
            $idlp  = $data[0];
            $tgl1  = $data[1];
            $tgl2  = $data[2];
            $bank  = $data[3];
            $cab   = $data[4];

            $_tgl1 = date('Y-m-d', strtotime($tgl1));
            $_tgl2 = date('Y-m-d', strtotime($tgl2));
            $_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
            $_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));

            if ($idlp == 101) {
                $bulan = date('n', strtotime($tgl1));
                $tahun = date('Y', strtotime($tgl2));
                $query =
                    "select * from ms_akunsaldo right outer join ms_akun on ms_akunsaldo.kodeakun=ms_akun.kodeakun 
                    and ms_akunsaldo.tahun = '$tahun' and ms_akunsaldo.bulan = '$bulan'
                    where ms_akun.kelompok = 'BANK' and ms_akun.tx='Y'
                    ";

                if ($cab != "") {
                    $query .= "and ms_akun.kodecbg = '$cab'";
                }
                $query .= "order by ms_akun.kodeakun";

                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Kas');
                $pdf->setsubjudul($_peri1);
                $pdf->addpage("L", "A4");
                $pdf->setsize("L", "A4");
                $pdf->SetWidths(array(10, 30, 70, 50, 50, 50));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Kode Perkiraan ', 'Nama Perkiraan', 'Saldo Awal', 'Mutasi', 'Saldo Akhir');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);

                $pdf->SetWidths(array(10, 30, 70, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('', ' ', '', 'Debet', 'Kredit', 'Debet', 'Kredit', 'Debet', 'Kredit');
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 30, 70, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'C', 'L', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->setfont('Times', '', 9);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;
                $tot1d = $tot1k = $tot2d = $tot2k = $tot3d = $tot3k = 0;
                foreach ($lap as $db) {
                    $tot1d += $db->debet;
                    $tot1k += $db->kredit;


                    $saldoawal = $db->debet + $db->kredit;
                    $akun = $db->kodeakun;
                    $db2 = db_connect();
                    $jur = $db2->query("select sum(debet) as debet, sum(kredit) as kredit from tr_jurnal where kodeakun='$akun' and tanggal between '$_tgl1' and '$_tgl2'")->getRow();
                    $tdb = $jur->debet;
                    $tkr = $jur->kredit;
                    $mutasi = $tdb - $tkr;

                    $tot2d += $tdb;
                    $tot2k += $tkr;

                    $akhir  = $saldoawal + $mutasi;
                    if ($akhir > 0) {
                        $akhirdb = abs($akhir);
                        $akhirkr = 0;
                    } else {
                        $akhirkr = abs($akhir);
                        $akhirdb = 0;
                    }

                    $tot3d += $akhirdb;
                    $tot3k += $akhirkr;

                    $pdf->row(array(
                        $nourut,
                        $db->kodeakun,
                        $db->namaakun,
                        number_format($db->debet, 0, '.', ','),
                        number_format($db->kredit, 0, '.', ','),
                        number_format($tdb, 0, '.', ','),
                        number_format($tkr, 0, '.', ','),
                        number_format($akhirdb, 0, '.', ','),
                        number_format($akhirkr, 0, '.', ',')
                    ));

                    $nourut++;
                }
                $pdf->SetWidths(array(110, 25, 25, 25, 25, 25, 25));
                $pdf->SetAligns(array('C', 'R', 'R', 'R', 'R', 'R', 'R'));
                $pdf->row(array(
                    'TOTAL',
                    number_format($tot1d, 0, '.', ','),
                    number_format($tot1k, 0, '.', ','),
                    number_format($tot2d, 0, '.', ','),
                    number_format($tot2k, 0, '.', ','),
                    number_format($tot3d, 0, '.', ','),
                    number_format($tot3k, 0, '.', ',')
                ));

                $pdf->SetTitle('LAPORAN DAFTAR KAS/BANK');
                $pdf->AliasNbPages();
                $pdf->output('Kas_101.PDF', 'I');
                exit;
            } else 
            if ($idlp == 102) {
                $query =
                    "select a.terima_tanggal as tanggal, a.terima_nomor as nomor, a.terima_uraian as ket, sum(b.terimad_jumlah) as jumlah  from tr_penerimaan a,
               tr_penerimaand b where a.terima_nomor=b.terimad_nomor
               and a.terima_tanggal between '$_tgl1' and '$_tgl2'";


                if ($bank != "") {
                    $query .= "and a.terima_kasbank = '$bank'";
                }
                if ($cab != "") {
                    $query .= "and a.terima_kodecbg = '$cab'";
                }
                $query .= " group by a.terima_nomor ";
                $query .= " order by a.terima_tanggal, a.terima_nomor";

                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Penerimaan Kas & Bank');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetWidths(array(10, 30, 20, 90, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Nomor ', 'Tanggal', 'Keterangan', 'Jumlah');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 30, 20, 90, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
                $pdf->setfont('Times', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;
                $tot = 0;
                foreach ($lap as $db) {
                    $tot = $tot + $db->jumlah;
                    $pdf->row(array($nourut, $db->nomor, date('d-m-Y', strtotime($db->tanggal)), $db->ket, number_format($db->jumlah, 0, ',', '.')));
                    $nourut++;
                }
                $pdf->SetWidths(array(150, 35));
                $pdf->SetAligns(array('C', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->row(array('TOTAL', number_format($tot, 0, ',', '.')));

                $pdf->SetTitle('LAPORAN PENERIMAAN KAS/BANK');
                $pdf->AliasNbPages();
                $pdf->output('kasbank_penerimaan.PDF', 'I');
                exit;
            } else 
            if ($idlp == 103) {
                $query =
                    "select a.keluar_tanggal as tanggal, a.keluar_nomor as nomor, a.keluar_uraian as ket, sum(b.keluard_jumlah) as jumlah  from tr_pengeluaran a,
               tr_pengeluarand b where a.keluar_nomor=b.keluard_nomor
               and a.keluar_tanggal between '$_tgl1' and '$_tgl2'";


                if ($bank != "") {
                    $query .= "and a.keluar_kasbank = '$bank'";
                }
                if ($cab != "") {
                    $query .= "and a.keluar_kodecbg = '$cab'";
                }
                $query .= " group by a.keluar_nomor ";
                $query .= " order by a.keluar_tanggal, a.keluar_nomor";

                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Pengeluaran Kas & Bank');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetWidths(array(10, 30, 20, 90, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Nomor ', 'Tanggal', 'Keterangan', 'Jumlah');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 30, 20, 90, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
                $pdf->setfont('Times', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;
                $tot = 0;
                foreach ($lap as $db) {
                    $tot = $tot + $db->jumlah;
                    $pdf->row(array($nourut, $db->nomor, date('d-m-Y', strtotime($db->tanggal)), $db->ket, number_format($db->jumlah, 0, ',', '.')));
                    $nourut++;
                }
                $pdf->SetWidths(array(150, 35));
                $pdf->SetAligns(array('C', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->row(array('TOTAL', number_format($tot, 0, ',', '.')));

                $pdf->SetTitle('LAPORAN PENGELUARAN KAS/BANK');
                $pdf->AliasNbPages();
                $pdf->output('kasbank_pengeluaran.PDF', 'I');
                exit;
            } else 
            if ($idlp == 104) {
                $query =
                    "select * from tr_transfer where tanggal between '$_tgl1' and '$_tgl2'";

                if ($bank != "") {
                    $query .= "and bank_sumber = '$bank'";
                }
                if ($cab != "") {
                    $query .= "and cabang = '$cab'";
                }
                $query .= " order by tanggal, nomor";

                $lap = $db->query($query)->getResult();
                $pdf = new simkeu_rpt_helper();
                $pdf->setID($nama_usaha, $motto, $alamat);
                $pdf->setunit($namaunit);
                $pdf->setjudul('Daftar Transfer Kas & Bank');
                $pdf->setsubjudul($_peri);
                $pdf->addpage("P", "A4");
                $pdf->setsize("P", "A4");
                $pdf->SetWidths(array(10, 30, 20, 50, 20, 20, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
                $judul = array('No.', 'Nomor ', 'Tanggal', 'Keterangan', 'Dari Kas/Bank', 'Ke Kas/Bank', 'Jumlah');
                $pdf->setfont('Times', 'B', 10);
                $pdf->row($judul);
                $pdf->SetWidths(array(10, 30, 20, 50, 20, 20, 35));
                $pdf->SetAligns(array('C', 'C', 'C', 'L', 'L', 'L', 'R'));
                $pdf->setfont('Times', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $nourut = 1;
                $tot = 0;
                foreach ($lap as $db) {
                    $sumber = $model_Global->_namaakun($db->bank_sumber);
                    $tujuan = $model_Global->_namaakun($db->bank_tujuan);

                    $tot = $tot + $db->jumlah;
                    $pdf->row(array($nourut, $db->nomor, date('d-m-Y', strtotime($db->tanggal)), $db->uraian, $sumber, $tujuan, number_format($db->jumlah, 0, ',', '.')));
                    $nourut++;
                }
                $pdf->SetWidths(array(150, 35));
                $pdf->SetAligns(array('R', 'R'));
                $pdf->setfont('Times', 'B', 10);
                $pdf->row(array('TOTAL', number_format($tot, 0, ',', '.')));

                $pdf->SetTitle('LAPORAN TRANSFER KAS/BANK');
                $pdf->AliasNbPages();
                $pdf->output('kasbank_transfer.PDF', 'I');
                exit;
            }
        } else {

            header('location:' . base_url());
        }
    }
}
