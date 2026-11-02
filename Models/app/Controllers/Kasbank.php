<?php

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_rpt_helper;

class Kasbank extends BaseController
{
    // =========================== PENERIMAAN KAS & BANK SELECTION START ======================//
    //Penerimaan Kas & Bank Page Start Here
    public function penerimaan()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/penerimaan',
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
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $DBAkun = $db->table('ms_akun');
        $DBTerima = $db->table('tr_penerimaan');
        $DBTerimaD = $db->table('tr_penerimaand');
        $DBTrJurnal = $db->table('tr_jurnal');
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

                //HEADER TR PENERIMAAN KAS & BANK
                $data = array(
                    'terima_tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'terima_kasbank' => $input['kasbank'],
                    'terima_kodecbg' => $session->get('cabang'),
                    'terima_penerima' => $input['penerima'],
                    'terima_uraian' => $input['keterangan'],
                    'terima_status' => 2,
                    'terima_userentry' => $session->get('username')
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
                        'keterangan' => $input['keterangan'],
                        'nourut' => $nourut,
                        'jenis' => 'JK',
                        'wbs' => 'KM',
                        'kodeakun' => $kodeakun[$i],
                        'debet' => 0,
                        'kredit' => str_replace(',', '', $jumlah[$i]),
                        'userid' => $session->get('username'),
                        'kodecbg' => $session->get('cabang'),
                    );

                    if ($kodeakun[$i] != "") {
                        if ($param == 0) {
                            $datad['terimad_register'] = $register;
                            $datad['terimad_nomor'] = $nomorbukti;
                            $DBTerimaD->insert($datad);
                            $data_jurnal['novoucher'] = $nomorbukti;
                            $data_jurnal['noref'] = $register;
                            $DBTrJurnal->insert($data_jurnal);
                        } else {
                            $datad['terimad_register'] = $param;
                            $datad['terimad_nomor'] = $input['nomorbukti'];
                            $DBTerimaD->insert($datad);
                            $data_jurnal['novoucher'] = $input['nomorbukti'];
                            $data_jurnal['noref'] = $param;
                            $DBTrJurnal->insert($data_jurnal);
                        }
                    }
                }
                $data_jurnal = array(
                    'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'keterangan' => $input['keterangan'],
                    'nourut' => 1,
                    'jenis' => 'JK',
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
                    $DBTrJurnal->insert($data_jurnal);
                    echo json_encode(['status' => 1, 'message' => $nomorbukti]);
                } else {
                    $data_jurnal['novoucher'] = $input['nomorbukti'];
                    $data_jurnal['noref'] = $param;
                    $DBTrJurnal->insert($data_jurnal);
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

        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/pembayaran',
        ];
        return view('layout/layout', $data);
    }

    public function pembayarantable($tgl1, $tgl2)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $session = session();
        $cabang = $session->get('cabang');
        $level = $session->get('level');
        $akses = $model_Global->cek_menu_akses($level, 122);
        $db = db_connect();
        $query = "SELECT * FROM tr_pengeluaran where keluar_kodecbg='$cabang'";
        if ($tgl1 != "1" && $tgl2 !== "1") {
            $query .= " and keluar_tanggal between '$tgl1' and '$tgl2'";
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
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $input = $this->request->getPost();
        $param = $input['id'];
        $db = db_connect();
        $DBAkun = $db->table('ms_akun');
        $DBKeluar = $db->table('tr_pengeluaran');
        $DBKeluarD = $db->table('tr_pengeluarand');
        $DBTrJurnal = $db->table('tr_jurnal');
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
                    'keluar_status' => 2,
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
                        'keterangan' => $input['keterangan'],
                        'nourut' => $nourut,
                        'jenis' => 'JK',
                        'wbs' => 'KK',
                        'kodeakun' => $kodeakun[$i],
                        'debet' => str_replace(',', '', str_replace(',', '', $jumlah[$i])),
                        'kredit' => 0,
                        'userid' => $session->get('username'),
                    );

                    if ($kodeakun[$i] != "") {
                        if ($param == 0) {
                            $datad['keluard_register'] = $register;
                            $datad['keluard_nomor'] = $nomorbukti;
                            $DBKeluarD->insert($datad);
                            $data_jurnal['novoucher'] = $nomorbukti;
                            $data_jurnal['noref'] = $register;
                            $DBTrJurnal->insert($data_jurnal);
                        } else {
                            $datad['keluard_register'] = $param;
                            $datad['keluard_nomor'] = $input['nomorbukti'];
                            $DBKeluarD->insert($datad);
                            $data_jurnal['novoucher'] = $input['nomorbukti'];
                            $data_jurnal['noref'] = $param;
                            $DBTrJurnal->insert($data_jurnal);
                        }
                    }
                }

                $data_jurnal = array(
                    'tanggal' => date('Y-m-d', strtotime($input['tanggal'])),
                    'keterangan' => $input['keterangan'],
                    'nourut' => $nourut,
                    'jenis' => 'JK',
                    'wbs' => 'KK',
                    'kodeakun' => $input['kasbank'],
                    'debet' => 0,
                    'kredit' => $input['_h_total'],
                    'userid' => $session->get('username'),
                );
                if ($param == 0) {
                    $data_jurnal['novoucher'] = $nomorbukti;
                    $data_jurnal['noref'] = $register;
                    $DBTrJurnal->insert($data_jurnal);
                    echo json_encode(['status' => 1, 'message' => $nomorbukti]);
                } else {
                    $data_jurnal['novoucher'] = $input['nomorbukti'];
                    $data_jurnal['noref'] = $param;
                    $DBTrJurnal->insert($data_jurnal);
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

    public function transfer_entri($id = '')
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/transfer_bank_entri',
            'id' => $id ?? '',
        ];
        return view('layout/layout', $data);
    }

    public function transfer_bank()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/transaksi/transfer_bank',
        ];
        return view('layout/layout', $data);
    }

    public function laporan()
    {
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'body' => 'admin/kas_bank/laporan',
        ];
        return view('layout/layout', $data);
    }

    public function transfer_bank_add()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $builder = $db->table('transfer_bank');
        $input = $this->request->getPost();
        $validation->setRule('information', 'Information', 'required');
        $validation->setRule('from_bank', 'From Bank', 'required');
        $validation->setRule('kas_bank', 'Kas Bank', 'required');
        $id = $input['id'];



        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'nomorbukti' => $input['nomorbukti'],
                'tanggal' => $input['tanggal'],
                'from_bank' => $input['from_bank'],
                'information' => $input['information'],
                'kas_bank' => $input['kas_bank'],
                'transfer_value' => $input['transfer_value'],
            ];

            if ($id == 0) {
                $result = $builder->insert($data);
                if ($result) {
                    // route_to(base_url('kasbank/transaksi/transferbank'));
                    echo json_encode(['message' => 'Inserted']);
                }
            } else {
                $result = $builder->where('id', $id)->update($data);
                if ($result) {
                    echo json_encode(['message' => 'Update']);
                }
            }
        } else {
            $arr = [
                'error' => true,

                'nomorbuktierr' => $validation->showError('from_bank'),
                'informationerr' => $validation->showError('information'),
                'kas_bankerr' => $validation->showError('kas_bank'),


            ];
            echo json_encode($arr);
        }
    }
    public function transfer_table()
    {
        $db = db_connect();
        $res = $db->table('transfer_bank')->get()->getResult();
        $result = array();
        $data = [];
        foreach ($res as $value) {
            $data[] = [
                $value->id,
                $value->nomorbukti,
                $value->tanggal,
                $value->from_bank,
                $value->information,
                $value->kas_bank,
                $value->transfer_value,
                // $value->status == null ? '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '">Edit</a>' : '<a href="' . base_url('pembelian/transaksi/prapembelian/entri/' . $value->id) . '">View</a>',
                '<a href="' . base_url('kasbank/transaksi/transfer_bank/entri' . $value->id) . '" class="btn btn-primary me-2 mb-2" ><i class="fa-solid fa-edit text-white"></i> Edit</a>
                <a href="#" onclick="bank_delete(' . $value->id . ')" class="btn btn-danger me-2 mb-2" ><i class="fa-solid fa-edit text-white"></i> Delete</a>'

            ];
        }

        $result = ['data' => $data];
        echo json_encode($result);
    }
    public function bank_delete($id = '')
    {
        $db = db_connect();
        $builder = $db->table('transfer_bank');
        $delete = $builder->delete(['id' => $id]);
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }
}
