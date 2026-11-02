<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Export xls Pembelian
 **/

namespace App\Controllers;

use App\Helpers\simkeu_nota_helper;
use App\Helpers\simkeu_faktur_helper;
use App\Helpers\simkeu_rpt_helper;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportxlsPembelian extends BaseController
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

    public function ExportPembelian($x)
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $db = db_connect();
        $session = session();
        $cek  = $session->get('level');
        $unit = $session->get('cabang');
        if (!empty($cek)) {
            $profile = $model_Global->_LoadProfile();
            $nama_usaha = $profile->nama_usaha;
            $taxes         = $profile->taxes_desimal;
            $motto = '';
            $alamat = '';
            $namaunit = $db->table('ms_unit')->getWhere(['kode' => $unit])->getRow()->nama;
            $data  = explode("~", $x);
            $idlp  = $data[0];
            $tgl1  = $data[1];
            $tgl2  = $data[2];
            $bln   = $data[3];
            $thn   = $data[4];
            $supp  = $data[5];
            $brg   = $data[6];
            $cab   = $data[7];

            $_tgl1 = date('Y-m-d', strtotime($tgl1));
            $_tgl2 = date('Y-m-d', strtotime($tgl2));
            $_peri = 'Dari ' . date('d-m-Y', strtotime($tgl1)) . ' s/d ' . date('d-m-Y', strtotime($tgl2));
            $_peri1 = 'Per Tgl. ' . date('d', strtotime($tgl2)) . ' ' . $model_Global->_namabulan(date('n', strtotime($tgl2))) . ' ' . date('Y', strtotime($tgl2));
            $_peri2 = 'Periode. ' . $model_Global->_namabulan($bln) . ' ' . $thn;
            $tgl_ppn11 = date('2022-04-01');
            $tgl_ppn12 = date('2025-01-01');

            if ($_tgl1 >= $tgl_ppn12) {
                $h_ppn = $taxes;
            } else if ($_tgl1 >= $tgl_ppn11) {
                $h_ppn = 0.11;
            } else {
                $h_ppn = 0.1;
            }

            //Laporan pembelian Global
            if ($idlp == 101) {
                $query =
                    "select ap_pufile.*, ap_supplier.nama as namapemasok from ap_pufile inner join ap_supplier on ap_pufile.kodesup=ap_supplier.kode 
			 		where tglkirim between '$_tgl1' and '$_tgl2' ";

                if ($supp != "") {
                    $query .= "and ap_pufile.kodesup = '$supp'";
                }
                if ($cab != "") {
                    $query .= "and ap_pufile.kodecbg = '$cab'";
                } else {
                    $query .= "and ap_pufile.kodecbg = '01'";
                }
                $query .= "order by tglkirim, kodepu";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN PEMBELIAN GLOBAL');
                $sheet->setCellValue('D1', 'LAPORAN PEMBELIAN GLOBAL');
                $sheet->setCellValue('D2', $_peri);
                // Column Headers
                $headers = ['NO', 'NOMOR', 'TGL INPUT', 'CUSTOMER', 'TGL FAKTUR', 'BRUTO', 'DISC', 'NETTO', 'PPN', 'DPP+PPN'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A3:J3')->getFont()->setBold(true);

                $column = 4;
                $totbruto = 0;
                $totnetto = 0;
                $totdisc  = 0;
                $tothrg  = 0;
                $totppn  = 0;
                foreach ($lap as $key => $value) {
                    $per_item = "SELECT * from ap_pudetail WHERE kodepu='$value->kodepu'";
                    $lap1   = $db->query($per_item)->getResult();
                    $subtotalItem = 0;
                    $discItem = 0;
                    foreach ($lap1 as $row2) {
                        $qtypu = str_replace(',', '', $row2->qtypu);
                        $hargabeli = str_replace(',', '', $row2->hargabeli);
                        $subtotalItem = $subtotalItem + $qtypu * $hargabeli;
                        $convdisc = ($row2->disc / 100) * $qtypu * $hargabeli;
                        $discItem = $discItem +  $convdisc;
                    }

                    $tdpp = $subtotalItem - $discItem;
                    if ($value->sppn == "Y") {
                        $ppn = ($subtotalItem - $discItem) * $h_ppn;
                    } else {
                        $ppn = 0;
                    }
                    $dpp_ppn = $tdpp + $ppn;
                    $ConvIntDDPP = intval($tdpp);
                    if ($ConvIntDDPP >= 1) {

                        $sheet->setCellValue('A' . $column, ($column - 3));
                        $sheet->setCellValue('B' . $column, $value->kodepu);
                        $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($value->tglkirim)));
                        $sheet->setCellValue('D' . $column, $value->namapemasok);
                        $sheet->setCellValue('E' . $column, date('d-m-Y', strtotime($value->tglpu)));
                        $sheet->setCellValue('F' . $column, number_format($subtotalItem, 0, '.', ','));
                        $sheet->setCellValue('G' . $column, number_format($discItem, 0, '.', ','));
                        $sheet->setCellValue('H' . $column, number_format($tdpp, 0, '.', ','));
                        $sheet->setCellValue('I' . $column, number_format($ppn, 0, '.', ','));
                        $sheet->setCellValue('J' . $column, number_format($dpp_ppn, 0, '.', ','));
                        $column++;
                        $totbruto     = $totbruto + $subtotalItem;
                        $totnetto     = $totnetto + $tdpp;
                        $totppn     = $totppn + $ppn;
                        $totdisc      = $totdisc + $discItem;
                        $tothrg      = $tothrg + $dpp_ppn;
                    }
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('F' . ($column), number_format($totbruto, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($totdisc, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($totnetto, 0, ',', '.'));
                $sheet->setCellValue('I' . ($column), number_format($totppn, 0, ',', '.'));
                $sheet->setCellValue('J' . ($column), number_format($tothrg, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':E' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'LAPORAN PEMBELIAN GLOBAL.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan Retur pembelian (DETAIL)
            else if ($idlp == 102) {
                $query =
                    "select ap_retur.*, ap_supplier.nama as namapemasok from ap_retur inner join ap_supplier on ap_retur.kodesup=ap_supplier.kode 
			 		where tglretur between '$_tgl1' and '$_tgl2' ";

                if ($supp != "") {
                    $query .= "and ap_retur.kodesup = '$supp'";
                }
                if ($cab != "") {
                    $query .= "and ap_retur.kodecbg = '$cab'";
                } else {
                    $query .= "and ap_retur.kodecbg = '01'";
                }
                $query .= "order by tglretur, koderetur";

                // Execute query and initialize spreadsheet
                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Laporan Retur Pembelian Detail');
                $sheet->setCellValue('A1', 'LAPORAN RETUR PEMBELIAN DETAIL');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:L1');
                $sheet->mergeCells('A2:L2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Starting line for data entry
                $column = 4;

                foreach ($lap as $Pembelian) {
                    // Add Supplier Name
                    $sheet->setCellValue('A' . $column, $Pembelian->namapemasok);
                    $sheet->mergeCells('A' . $column . ':M' . $column);
                    $sheet->getStyle('A' . $column)->getFont()->setBold(true);

                    // Move to the next row after Supplier name
                    $column++;

                    // Query for detailed return items for this Supplier
                    $per_sup = "SELECT ap_returdetil.*, 
                                        inv_barang.namabarang, 
                                        ap_retur.kodepu, 
                                        ap_retur.keterangan, 
                                        ap_retur.tglretur 
                                FROM ap_returdetil 
                                INNER JOIN inv_barang ON ap_returdetil.kodeitem = inv_barang.kodeitem 
                                INNER JOIN ap_retur ON ap_returdetil.koderetur = ap_retur.koderetur 
                                where ap_retur.kodesup='" . $Pembelian->kodesup . "' and tglretur between '$_tgl1' and '$_tgl2' order by koderetur";
                    $lap1 = $db->query($per_sup)->getResult();
                    // Column Headers
                    $headers = ['NO', 'NO RETUR', 'NO FAKTUR', 'TANGGAL', 'NAMA BARANG', 'BATCH', 'ED', 'QTY', 'HARGA', 'DISC', 'NETTO', 'KETERANGAN'];
                    $sheet->fromArray($headers, NULL, 'A' . $column);
                    $sheet->getStyle('A' . $column . ':M' . $column)->getFont()->setBold(true);
                    $column++;

                    $totqty  = 0;
                    $tothrgjual  = 0;
                    $tothrg  = 0;
                    $totdisc = 0;
                    foreach ($lap1 as $index => $returnItem) {
                        $tot             = ($returnItem->qtyretur * $returnItem->hargabeli);
                        $dis             = ($returnItem->disc / 100) * $tot;
                        $total_harga     = $tot - $dis;
                        if ($returnItem->qtyretur >= 1) {

                            $sheet->setCellValue('A' . $column, $index + 1);
                            $sheet->setCellValue('B' . $column, $returnItem->koderetur);
                            $sheet->setCellValue('C' . $column, $returnItem->kodepu);
                            $sheet->setCellValue('D' . $column, date('d-m-Y', strtotime($returnItem->tglretur)));
                            $sheet->setCellValue('E' . $column, $returnItem->namabarang);
                            $sheet->setCellValue('F' . $column, $returnItem->no_batch);
                            $sheet->setCellValue('G' . $column, $returnItem->tanggal_exp);
                            $sheet->setCellValue('H' . $column, $returnItem->qtyretur);
                            $sheet->setCellValue('I' . $column, number_format($returnItem->hargabeli, 0, '.', ','));
                            $sheet->setCellValue('J' . $column, $returnItem->disc);
                            $sheet->setCellValue('K' . $column, number_format($total_harga, 0, '.', ','));
                            $sheet->setCellValue('L' . $column, $returnItem->keterangan);


                            //calculate total
                            $totqty = $totqty + $returnItem->qtyretur;
                            $tothrgjual = $tothrgjual + $returnItem->hargabeli;
                            $totdisc = $totdisc + $returnItem->disc;
                            $tothrg = $tothrg + $total_harga;
                            // Increment row count
                            $column++;
                        }
                    }

                    // Add total row for the customer
                    $sheet->setCellValue('A' . $column, 'TOTAL');
                    $sheet->setCellValue('H' . $column, number_format($totqty, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, number_format($tothrgjual, 0, '.', ','));
                    $sheet->setCellValue('J' . $column, number_format($totdisc, 0, '.', ','));
                    $sheet->setCellValue('K' . $column, number_format($tothrg, 0, '.', ','));
                    $sheet->mergeCells('A' . $column . ':G' . $column);
                    $sheet->getStyle('A' . $column . ':L' . $column)->getFont()->setBold(true);
                    $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Incrementing row for next customer
                    $column++;
                }

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'M') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set header for download
                $fileName = 'LAPORAN RETUR PEMBELIAN DETAIL.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Save and output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Prevent further output
            }
            //pembelian per supplier / Detail
            else if ($idlp == 103) {
                $query =
                    "SELECT ap_supplier.nama, 
                            ap_pufile.*, 
                            ap_pudetail.kodeitem 
                    FROM ap_pufile 
                        INNER JOIN ap_supplier ON ap_pufile.kodesup = ap_supplier.kode 
                        INNER JOIN ap_pudetail ON ap_pufile.kodepu = ap_pudetail.kodepu
			 		where tglkirim between '$_tgl1' and '$_tgl2' ";

                if ($supp != "") {
                    $query .= "and ap_pufile.kodesup = '$supp'";
                }
                if ($cab != "") {
                    $query .= "and ap_pufile.kodecbg = '$cab'";
                } else {
                    $query .= "and ap_pufile.kodecbg = '01'";
                }
                if ($brg != "") {
                    $query .= "and ap_pudetail.kodeitem = '$brg'";
                }
                $query .= "group by nama order by tglpu, kodepo";

                // Execute query and initialize spreadsheet
                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Laporan Pembelian Detail');
                $sheet->setCellValue('A1', 'LAPORAN PEMBELIAN DETAIL');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Starting line for data entry
                $column = 4;

                foreach ($lap as $Pembelian) {
                    // Add Supplier Name
                    $sheet->setCellValue('A' . $column, $Pembelian->nama);
                    $sheet->mergeCells('A' . $column . ':M' . $column);
                    $sheet->getStyle('A' . $column)->getFont()->setBold(true);

                    // Move to the next row after Supplier name
                    $column++;

                    // Query for detailed return items for this Supplier
                    $per_sup = "SELECT ap_pudetail.*, 
                                        inv_barang.namabarang, 
                                        ap_pufile.kodepu, 
                                        ap_pufile.tglpu, 
                                        ap_pufile.tglkirim 
                                FROM ap_pudetail 
                                INNER JOIN inv_barang ON ap_pudetail.kodeitem = inv_barang.kodeitem 
                                INNER JOIN ap_pufile ON ap_pudetail.kodepu = ap_pufile.kodepu 
                                where ap_pufile.kodesup='" . $Pembelian->kodesup . "' and tglkirim between '$_tgl1' and '$_tgl2' and inv_barang.kdcbg='$unit'";
                    if ($brg != "") {
                        $per_sup .= " and ap_pudetail.kodeitem = '$brg'";
                    }
                    $lap1 = $db->query($per_sup)->getResult();
                    // Column Headers
                    $headers = ['NO', 'NO FAKTUR', 'TGL INPUT', 'NAMA BARANG', 'BATCH', 'ED', 'TGL FAKTUR', 'QTY', 'HARGA', 'DISC', 'NETTO'];
                    $sheet->fromArray($headers, NULL, 'A' . $column);
                    $sheet->getStyle('A' . $column . ':M' . $column)->getFont()->setBold(true);
                    $column++;

                    $totqty  = 0;
                    $tothrgjual  = 0;
                    $tothrg  = 0;
                    $totdisc = 0;
                    foreach ($lap1 as $index => $returnItem) {
                        $tot             = ($returnItem->qtypu * $returnItem->hargabeli);
                        $dis             = ($returnItem->disc / 100) * $tot;
                        $total_harga     = $tot - $dis;

                        if ($returnItem->qtypu >= 1) {

                            $sheet->setCellValue('A' . $column, $index + 1);
                            $sheet->setCellValue('B' . $column, $returnItem->kodepu);
                            $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($returnItem->tglkirim)));
                            $sheet->setCellValue('D' . $column, $returnItem->namabarang);
                            $sheet->setCellValue('E' . $column, $returnItem->no_batch);
                            $sheet->setCellValue('F' . $column, $returnItem->tanggal_exp);
                            $sheet->setCellValue('G' . $column, date('d-m-Y', strtotime($returnItem->tglpu)));
                            $sheet->setCellValue('H' . $column, $returnItem->qtypu);
                            $sheet->setCellValue('I' . $column, number_format($returnItem->hargabeli, 0, '.', ','));
                            $sheet->setCellValue('J' . $column, $returnItem->disc);
                            $sheet->setCellValue('K' . $column, number_format($total_harga, 0, '.', ','));


                            //calculate total
                            $totqty = $totqty + $returnItem->qtypu;
                            $tothrgjual = $tothrgjual + $returnItem->hargabeli;
                            $totdisc = $totdisc + $returnItem->disc;
                            $tothrg = $tothrg + $total_harga;
                            // Increment row count
                            $column++;
                        }
                    }

                    // Add total row for the customer
                    $sheet->setCellValue('A' . $column, 'TOTAL');
                    $sheet->setCellValue('H' . $column, number_format($totqty, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, number_format($tothrgjual, 0, '.', ','));
                    $sheet->setCellValue('J' . $column, number_format($totdisc, 0, '.', ','));
                    $sheet->setCellValue('K' . $column, number_format($tothrg, 0, '.', ','));
                    $sheet->mergeCells('A' . $column . ':G' . $column);
                    $sheet->getStyle('A' . $column . ':L' . $column)->getFont()->setBold(true);
                    $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Incrementing row for next customer
                    $column++;
                }

                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'M') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set header for download
                $fileName = 'LAPORAN PEMBELIAN DETAIL.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Save and output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Prevent further output
            }
            // Laporan Faktur belum lunas
            else if ($idlp == 201) {
                $query =
                    "select ap_pufile.*, ap_supplier.nama as namapemasok from ap_pufile inner join ap_supplier on ap_pufile.kodesup=ap_supplier.kode 
			 			where tglkirim between '$_tgl1' and '$_tgl2' and totalpu>jumlahbayar ";

                if ($supp != "") {
                    $query .= " and ap_pufile.kodesup = '$supp'";
                }
                if ($cab != "") {
                    $query .= " and ap_pufile.kodecbg = '$cab'";
                }
                $query .= " order by tglkirim, kodepo";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('FAKTUR BELUM LUNAS');
                $sheet->setCellValue('D1', 'FAKTUR BELUM LUNAS');
                $sheet->setCellValue('D2', $_peri);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                // Column Headers
                $headers = ['NO', 'SUPPLIER', 'NOMOR', 'TANGGAL', 'JATUH TEMPO', 'TOTAL', 'HUTANG', 'HUTANG PAJAK', 'UMUR (hr)'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);

                $column = 4;
                $totfaktur  = 0;
                $tothutang  = 0;
                $tothutangpjk  = 0;
                foreach ($lap as $key => $value) {
                    $hutang    = $value->totalpu - $value->jumlahbayar;
                    $hutangpjk = $value->ppn - $value->ppnbayar;
                    $date1 = strtotime($tgl2);
                    $date2 = strtotime($value->tglpu);
                    $umur  = ($date1 - $date2) / (60 * 60 * 24);
                    $selisihumurhr = date_diff(date_create($tgl2), date_create($value->tglpu));

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->namapemasok);
                    $sheet->setCellValue('C' . $column, $value->kodepu);
                    $sheet->setCellValue('D' . $column, date('d-m-Y', strtotime($value->tglpu)));
                    $sheet->setCellValue('E' . $column, date('d-m-Y', strtotime($value->tgljthtempo)));
                    $sheet->setCellValue('F' . $column, number_format($value->totalpu, 0, '.', ','));
                    $sheet->setCellValue('G' . $column, number_format($hutang, 0, '.', ','));
                    $sheet->setCellValue('H' . $column, number_format($hutangpjk, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, $selisihumurhr->days);

                    //Calculate Total
                    $totfaktur = $totfaktur + $value->totalpu;
                    $tothutang = $tothutang + $hutang;
                    $tothutangpjk = $tothutangpjk + $hutangpjk;
                    // Increment row count
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('F' . ($column), number_format($totfaktur, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($tothutang, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($tothutangpjk, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':E' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'FAKTUR BELUM LUNAS.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan Hutang
            else if ($idlp == 202) {
                $query =
                    "SELECT b.kode, b.nama as namasupplier from ap_pufile a inner join ap_supplier b on a.kodesup=b.kode 
					 where year(tglpu) ='$thn' and month(tglpu)='$bln' and a.sppn='Y'";

                if ($supp != "") {
                    $query .= "and a.kodesup = '$supp'";
                }
                if ($cab != "") {
                    $query .= "and a.kodecbg = '$cab'";
                }
                $query .= " GROUP BY a.kodesup";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN HUTANG PPN');
                $sheet->setCellValue('A1', 'LAPORAN HUTANG PPN');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Column Headers
                $headers = ['NO', 'SUPPLIER', 'SALDO AWAL', 'PEMBELIAN', 'PEMBAYARAN', 'SALDO AKHIR'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);

                $column = 4;
                $totsaldoawal  = 0;
                $totpembelian  = 0;
                $totpembayaran  = 0;
                $totsaldoakhir  = 0;
                foreach ($lap as $key => $value) {
                    if ($bln == "1") {
                        $bulan = 12;
                        $tahun =  $thn - 1;
                    } else {
                        $bulan = $bln - 1;
                        $tahun = $thn;
                    }
                    $RowBayarBackDate = $db->query("SELECT * FROM ap_bayar where kodesup='$value->kode' and year(tglbayar)='$tahun' and month(tglbayar)='$bulan'")->getResult();
                    $totBayarBackDate  = 0;
                    foreach ($RowBayarBackDate as $DataBayar) {
                        $TotRowPufile = $db->query("SELECT sum(bayar) as saldo FROM ap_bayardetil WHERE kodebyr='$DataBayar->kodebyr' AND YEAR(tglfaktur)='$tahun' AND MONTH(tglfaktur)='$bulan'")->getRow();

                        $totBayarBackDate += $TotRowPufile->saldo;
                    }

                    $saldoawal = $db->query("SELECT sum(totalpu) as saldoawal from ap_pufile where kodesup='$value->kode' and year(tglpu) ='$tahun' and month(tglpu)='$bulan'")->getRow();
                    $totbeli = $db->query("SELECT sum(totalpu) as totalbeli from ap_pufile where kodesup='$value->kode' and year(tglpu) ='$thn' and month(tglpu)='$bln'")->getRow();
                    $totbayar = $db->query("SELECT sum(jumlahbayar) as totalbayar from ap_pufile where kodesup='$value->kode' and year(tglpu) ='$thn' and month(tglpu)='$bln'")->getRow();

                    $SaldoAwalBackDate = $saldoawal->saldoawal - $totBayarBackDate;

                    $HsaldoAkhir = $totbeli->totalbeli +  $SaldoAwalBackDate - $totbayar->totalbayar;

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->namasupplier);
                    $sheet->setCellValue('C' . $column, intval($SaldoAwalBackDate));
                    $sheet->setCellValue('D' . $column, intval($totbeli->totalbeli));
                    $sheet->setCellValue('E' . $column, intval($totbayar->totalbayar));
                    $sheet->setCellValue('F' . $column, intval($HsaldoAkhir));

                    //Calculate Total
                    $totsaldoawal = $totsaldoawal + $SaldoAwalBackDate;
                    $totpembelian = $totpembelian + $totbeli->totalbeli;
                    $totpembayaran = $totpembayaran + $totbayar->totalbayar;
                    $totsaldoakhir = $totsaldoakhir + $HsaldoAkhir;
                    // Increment row count
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('C' . ($column), number_format($totsaldoawal, 0, ',', '.'));
                $sheet->setCellValue('D' . ($column), number_format($totpembelian, 0, ',', '.'));
                $sheet->setCellValue('E' . ($column), number_format($totpembayaran, 0, ',', '.'));
                $sheet->setCellValue('F' . ($column), number_format($totsaldoakhir, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':B' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'LAPORAN HUTANG PPN.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan Umur Hutang
            else if ($idlp == 203) {
                $query = "select * from ap_supplier order by nama";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('UMUR HUTANG');
                $sheet->setCellValue('A1', 'UMUR HUTANG');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Column Headers
                $headers = ['NO', 'SUPPLIER', 'HUTANG', 'BLN TEMPO', '1 - 30', '31 - 60', '61- 90', '91 - 120', '> 120'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);

                $column = 4;
                $tothutang = 0;
				$totbelum  = 0;
				$tottempo1 = 0;
				$tottempo2 = 0;
				$tottempo3 = 0;
				$tottempo4 = 0;
				$tottempo5 = 0;
                foreach ($lap as $key => $value) {
                    $supp = $value->kode;

					$query2 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and kodesup = '$supp'";
					$data2 = $db->query($query2)->getRow();
					$hutang  = $data2->total;

					$query3 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) < 1 and kodesup = '$supp'";
					$data3 = $db->query($query3)->getRow();
					$blmtempo  = $data3->total;

					$query4 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 1 and 30 and kodesup = '$supp'";
					$data4 = $db->query($query4)->getRow();
					$tempo1  = $data4->total;

					$query5 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 31 and 60 and kodesup = '$supp'";
					$data5 = $db->query($query5)->getRow();
					$tempo2  = $data5->total;

					$query6 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 61 and 90 and kodesup = '$supp'";
					$data6 = $db->query($query6)->getRow();
					$tempo3  = $data6->total;

					$query7 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) between 91 and 120 and kodesup = '$supp'";
					$data7 = $db->query($query7)->getRow();
					$tempo4  = $data7->total;

					$query8 = "select sum(totalpu-jumlahbayar) as total from ap_pufile
				       where totalpu>jumlahbayar and datediff('$_tgl2',tglpu) > 120 and kodesup = '$supp'";
					$data8 = $db->query($query8)->getRow();
					$tempo5  = $data8->total;


					$tothutang = $tothutang + $hutang;
					$totbelum  = $totbelum + $blmtempo;
					$tottempo1 = $tottempo1 + $tempo1;
					$tottempo2 = $tottempo2 + $tempo2;
					$tottempo3 = $tottempo3 + $tempo3;
					$tottempo4 = $tottempo4 + $tempo4;
					$tottempo5 = $tottempo5 + $tempo5;

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->nama);
                    $sheet->setCellValue('C' . $column, number_format($hutang));
                    $sheet->setCellValue('D' . $column, number_format($blmtempo));
                    $sheet->setCellValue('E' . $column, number_format($tempo1));
                    $sheet->setCellValue('F' . $column, number_format($tempo2));
                    $sheet->setCellValue('G' . $column, number_format($tempo3));
                    $sheet->setCellValue('H' . $column, number_format($tempo4));
                    $sheet->setCellValue('I' . $column, number_format($tempo5));

                    // Increment row count
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('C' . ($column), number_format($tothutang, 0, ',', '.'));
                $sheet->setCellValue('D' . ($column), number_format($totbelum, 0, ',', '.'));
                $sheet->setCellValue('E' . ($column), number_format($tottempo1, 0, ',', '.'));
                $sheet->setCellValue('F' . ($column), number_format($tottempo2, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($tottempo3, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($tottempo4, 0, ',', '.'));
                $sheet->setCellValue('I' . ($column), number_format($tottempo5, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':B' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'UMUR HUTANG.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan hutang supplier yang sudah bayar/lunas
            elseif ($idlp == 204) {
                $query =
					"select a.*, b.* from ap_bayar a
					inner join ap_bayardetil b on a.kodebyr = b.kodebyr where tglbayar between '$_tgl1' and '$_tgl2' ";

				if ($supp != "") {
					$query .= " and a.kodesup = '$supp'";
				}
				if ($cab != "") {
					$query .= " and a.kodecbg = '$cab'";
				}
				$query .= " order by tglbayar";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('sheet1');
                $sheet->setCellValue('A1', 'LAPORAN HUTANG SUPPLIER YANG SUDAH BAYAR/LUNAS');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Column Headers
                $headers = ['NO.', 'VENDOR', 'NOMOR INPUT', 'NOMOR FAKTUR', 'TANGGAL FAKTUR', 'TANGGAL BAYAR', 'NOMINAL FAKTUR', 'NOMINAL FAKTUR YANG DIBAYARKAN', 'PEMBAYARAN (BCA,BJB,TUNAI)'];
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);

                $column = 4;
                $totfaktur  = 0;
				$totbayar  = 0;
                foreach ($lap as $key => $value) {
                    $DataSupplier = $db->table('ap_supplier')->getWhere(['kode' => $value->kodesup])->getRow();
					$DataAkun = $db->table('ms_akun')->getWhere(['kodeakun' => $value->kodebank])->getRow();

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $DataSupplier->nama);
                    $sheet->setCellValue('C' . $column, $value->kodebyr);
                    $sheet->setCellValue('D' . $column, $value->nomorfaktur);
                    $sheet->setCellValue('E' . $column, date('d-m-Y', strtotime($value->tglfaktur)));
                    $sheet->setCellValue('F' . $column, date('d-m-Y', strtotime($value->tglbayar)));
                    $sheet->setCellValue('G' . $column, number_format($value->totalfaktur));
                    $sheet->setCellValue('H' . $column, number_format($value->bayar));
                    $sheet->setCellValue('I' . $column, $DataAkun->namaakun);

                    //Calculate Total
                    $totfaktur = $totfaktur + $value->totalfaktur;
					$totbayar = $totbayar + $value->bayar;
                    // Increment row count
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('G' . ($column), number_format($totfaktur, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($totbayar, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':F' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                // Sheet formatting for auto-sizing columns
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'LAPORAN HUTANG SUPPLIER YANG SUDAH BAYAR ATAU LUNAS.xlsx';

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
