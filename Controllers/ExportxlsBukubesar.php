<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Export xls Buku Besar
 **/

namespace App\Controllers;

use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportxlsBukubesar extends BaseController
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

    // Fungsi untuk menambahkan data validation
    private function addDataValidation($sheet, $cell, $optionsRange)
    {
        $dataValidation = new DataValidation();
        $dataValidation->setType(DataValidation::TYPE_LIST);
        $dataValidation->setAllowBlank(true);
        $dataValidation->setFormula1($optionsRange);
        $dataValidation->setShowDropDown(true);
        $sheet->getCell($cell)->setDataValidation($dataValidation);
    }

    public function ExportBukuBesar($x)
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

            $data  = explode("~", $x);
            $idlp  = $data[0];
            $tgl1  = $data[1];
            $tgl2  = $data[2];
            $akun  = $data[3];
            $cab   = $data[4];
            if ($cab != "") {
                $namaunit =  $db->table('ms_unit')->getWhere(['kode' => $cab])->getRow()->nama;
            } else {
                $namaunit = "";
            }
            $_tgl1 = date('Y-m-d', strtotime($tgl1));
            $_tgl2 = date('Y-m-d', strtotime($tgl2));
            $_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
            $_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));

            //LAPORAN DAFTAR JURNAL
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
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('DAFTAR JURNAL');
                $sheet->setCellValue('A1', 'DAFTAR JURNAL');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $headers = ['NO', 'TANGGAL', 'NO. BUKTI', 'KODE AKUN', 'NAMA AKUN', 'URAIAN', 'DEBET', 'KREDIT'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J4')->getFont()->setBold(true);
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                $totdb  = 0;
                $totkr  = 0;
                foreach ($lap as $key => $db) {
                    $DataJurnal = $db->query("SELECT SUM(debet - kredit) AS total from tr_jurnal where novoucher = '$db->novoucher'")->getRow();
                    // if ($DataJurnal->total == 0) {
                    //     $pdf->setfont('Times', '', 9);
                    //     $pdf->SetFillColor(224, 235, 255);
                    //     $pdf->SetFont('');
                    //     $pdf->SetTextColor(0); // Warna hitam
                    // } else {
                    //     $pdf->setfont('Times', '', 9);
                    //     $pdf->SetFillColor(224, 235, 255);
                    //     $pdf->SetFont('');
                    //     $pdf->SetTextColor(255, 0, 0); // Warna merah
                    // }
                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column,  date('d-m-Y', strtotime($db->tanggal)));
                    $sheet->setCellValue('C' . $column,  $db->novoucher);
                    $sheet->setCellValue('D' . $column,  $db->kodeakun);
                    $sheet->setCellValue('E' . $column,  $db->namaakun);
                    $sheet->setCellValue('F' . $column,  $db->keterangan);
                    $sheet->setCellValue('G' . $column,  number_format($db->debet, 2, '.', ','));
                    $sheet->setCellValue('H' . $column,  number_format($db->kredit, 0, '.', ','));
                    $totdb = $totdb + $db->debet;
                    $totkr = $totkr + $db->kredit;
                    $column++;
                }

                //CALCULATE TOTAL
                $sheet->setCellValue('A' . $column, 'TOTAL');
                $sheet->setCellValue('D' . $column, number_format($tot1d, 0, ',', '.'));
                $sheet->setCellValue('E' . $column,  number_format($tot1k, 0, ',', '.'));
                $sheet->setCellValue('F' . $column, number_format($tot2d, 0, ',', '.'));
                $sheet->setCellValue('G' . $column, number_format($tot2k, 0, ',', '.'));
                $sheet->setCellValue('H' . $column,  number_format($tot3d, 0, ',', '.'));
                $sheet->setCellValue('I' . $column, number_format($tot3k, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':C' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'DAFTAR KASBANK.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            //LAPORAN DAFTAR PENERIMAAN KAS & BANK
            else if ($idlp == 102) {
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
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('Penerimaan Kas & Bank');
                $sheet->setCellValue('A1', 'DAFTAR PENERIMAAN KAS & BANK');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $headers = ['NO', 'NOMOR', 'TANGGAL', 'KETERANGAN', 'JUMLAH'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:K3')->getFont()->setBold(true);
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                $tot = 0;
                foreach ($lap as $db) {
                    $tot = $tot + $db->jumlah;
                    $sheet->setCellValue('A' . $column, ($column - 4));
                    $sheet->setCellValue('B' . $column, $db->nomor);
                    $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($db->tanggal)));
                    $sheet->setCellValue('D' . $column, $db->ket);
                    $sheet->setCellValue('E' . $column, number_format($db->jumlah, 0, ',', '.'));
                    $column++;
                }
                //SALDO AWAL


                //CALCULATE TOTAL
                $sheet->setCellValue('A' . $column, 'TOTAL');
                $sheet->setCellValue('E' . $column, number_format($tot, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'DAFTAR PENERIMAAN KAS & BANK.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // LAPORAN Pengeluaran Kas & Bank
            else if ($idlp == 103) {
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
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('Pengeluaran Kas & Bank');
                $sheet->setCellValue('A1', 'DAFTAR PENGELUARAN KAS & BANK');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $headers = ['NO', 'NOMOR', 'TANGGAL', 'KETERANGAN', 'JUMLAH'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:K3')->getFont()->setBold(true);
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                $tot = 0;
                foreach ($lap as $db) {
                    $tot = $tot + $db->jumlah;
                    $sheet->setCellValue('A' . $column, ($column - 4));
                    $sheet->setCellValue('B' . $column, $db->nomor);
                    $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($db->tanggal)));
                    $sheet->setCellValue('D' . $column, $db->ket);
                    $sheet->setCellValue('E' . $column, number_format($db->jumlah, 0, ',', '.'));
                    $column++;
                }
                //CALCULATE TOTAL
                $sheet->setCellValue('A' . $column, 'TOTAL');
                $sheet->setCellValue('E' . $column, number_format($tot, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'DAFTAR PENGELUARAN KAS & BANK.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // LAPORAN DAFTAR TRANSAKSI KAS & BANK
            else if ($idlp == 104) {
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
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('TRANSAKSI KAS & BANK');
                $sheet->setCellValue('A1', 'DAFTAR TRANSAKSI KAS & BANK');
                $sheet->setCellValue('A2', $_peri);
                // Column Headers
                $headers = ['NO', 'NOMOR', 'TANGGAL', 'KETERANGAN', 'DARI KAS/BANL', 'KE KAS/BANK', 'JUMLAH'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:K3')->getFont()->setBold(true);
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 4;
                $tot = 0;
                foreach ($lap as $db) {
                    $sumber = $model_Global->_namaakun($db->bank_sumber);
                    $tujuan = $model_Global->_namaakun($db->bank_tujuan);

                    $tot = $tot + $db->jumlah;
                    $sheet->setCellValue('A' . $column, ($column - 4));
                    $sheet->setCellValue('B' . $column, $db->nomor);
                    $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($db->tanggal)));
                    $sheet->setCellValue('D' . $column, $db->uraian);
                    $sheet->setCellValue('E' . $column, $sumber);
                    $sheet->setCellValue('F' . $column, $tujuan);
                    $sheet->setCellValue('G' . $column, number_format($db->jumlah, 0, ',', '.'));
                    $column++;
                }
                //CALCULATE TOTAL
                $sheet->setCellValue('A' . $column, 'TOTAL');
                $sheet->setCellValue('G' . $column, number_format($tot, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':F' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'DAFTAR TRANSAKSI KAS & BANK.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
        }
    }
}
