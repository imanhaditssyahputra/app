<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Export xls Penjualan
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

class ExportxlsPenjualan extends BaseController
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

    public function ExportPenjualan($x)
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
            $data      = explode("~", $x);
            $idlp      = $data[0];
            $tgl1      = $data[1];
            $tgl2      = $data[2];
            $bln      = $data[3];
            $thn      = $data[4];
            $cust      = $data[5];
            $brg       = $data[6];
            $cab       = $data[7];
            $marketing     = $data[8];

            $_tgl1 = date('Y-m-d 00:00', strtotime($tgl1));
            $_tgl2 = date('Y-m-d 23:00', strtotime($tgl2));
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
            // Daftar Pesanan Penjualan
            if ($idlp == 101) {
            }
            // Daftar Pengiriman Barang
            else if ($idlp == 102) {
            }
            //LAPORAN PENJUALAN GLOBAL
            else if ($idlp == 103) {
                $query =
                    "select ar_sifile.*, ar_customer.nama as namapelanggan from ar_sifile inner join ar_customer on ar_sifile.kodecust=ar_customer.kode 
			 						where tglkirim between '$_tgl1' and '$_tgl2' ";

                if ($cust != "") {
                    $query .= " and ar_sifile.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and ar_sifile.kodecbg = '$cab'";
                }
                if ($marketing != "") {
                    $query .= " and ar_sifile.id_sales = '$marketing'";
                }
                $query .= " group by kodesi";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN PENJUALAN GLOBAL');
                $sheet->setCellValue('A3', 'No');
                $sheet->setCellValue('B3', 'Nomor');
                $sheet->setCellValue('C3', 'Tanggal');
                $sheet->setCellValue('D1', 'LAPORAN PENJUALAN GLOBAL');
                $sheet->setCellValue('D2', $_peri);
                $sheet->setCellValue('D3', 'Customer');
                $sheet->setCellValue('E3', 'Bruto');
                $sheet->setCellValue('F3', 'Disc');
                $sheet->setCellValue('G3', 'Netto');
                $sheet->setCellValue('H3', 'PPN');
                $sheet->setCellValue('I3', 'DPP + PPN');
                $sheet->setCellValue('J3', 'Marketing');
				$sheet->setCellValue('K3', 'Nama Pengirim / Kurir');

                $column = 4;
                $totbruto = 0;
                $totnetto = 0;
                $totdisc  = 0;
                $tothrg  = 0;
                $totppn  = 0;
                foreach ($lap as $key => $value) {
                    $subtot = $value->dpp;
                    $disc = $value->diskon;

                    $tdpp = $subtot - $disc;
                    if ($value->sppn == "Y") {
                        $ppn = $tdpp * $h_ppn;
                    } else {
                        $ppn = 0;
                    }

                    $dpp_ppn = $tdpp + $ppn;
                    $ConvIntDDPP = intval($tdpp);
                    if ($ConvIntDDPP >= 1) {

                        $sheet->setCellValue('A' . $column, ($column - 3));
                        $sheet->setCellValue('B' . $column, $value->kodesi);
                        $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($value->tglsi)));
                        $sheet->setCellValue('D' . $column, $value->namapelanggan);
                        $sheet->setCellValue('E' . $column, number_format($subtot, 0, '.', ','));
                        $sheet->setCellValue('F' . $column, number_format($disc, 0, '.', ','));
                        $sheet->setCellValue('G' . $column, number_format($tdpp, 0, '.', ','));
                        $sheet->setCellValue('H' . $column, number_format($ppn, 0, '.', ','));
                        $sheet->setCellValue('I' . $column, number_format($dpp_ppn, 0, '.', ','));
                        $sheet->setCellValue('J' . $column, $value->nama_sales);
                        $sheet->setCellValue('K' . $column, $value->kurir);
                        $column++;
                        $totbruto     = $totbruto + $subtot;
                        $totnetto     = $totnetto + $tdpp;
                        $totppn     = $totppn + $ppn;
                        $totdisc      = $totdisc + $disc;
                        $tothrg      = $tothrg + $dpp_ppn;
                    }
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('E' . ($column), number_format($totbruto, 0, ',', '.'));
                $sheet->setCellValue('F' . ($column), number_format($totdisc, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($totnetto, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($totppn, 0, ',', '.'));
                $sheet->setCellValue('I' . ($column), number_format($tothrg, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':D' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:K3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->getColumnDimension('J')->setAutoSize(true);
                $sheet->getColumnDimension('K')->setAutoSize(true);

                // Tentukan nama file
                $fileName = 'LAPORAN PENJUALAN GLOBAL.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            //Uang Muka Penjualan 
            else if ($idlp == 104) {
            }
            // Laporan Retur Penjualan Detail
            else if ($idlp == 105) {
                $query =
                    "SELECT ar_retur.*, ar_customer.nama, ar_sifile.kodesi from ar_retur inner join ar_customer on ar_retur.kodecust = ar_customer.kode inner join ar_sifile on ar_sifile.kodesi = ar_retur.kodesi
			 						where tglretur between '$_tgl1' and '$_tgl2' ";

                if ($cust != "") {
                    $query .= " and ar_retur.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and ar_retur.kodecbg = '$cab'";
                }
                if ($marketing != "") {
                    $query .= "and ar_sifile.id_sales = '$marketing'";
                }
                $query .= " group by kodecust order by tglretur,ar_retur.koderetur";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('Laporan Retur Penjualan Detail');
                $sheet->setCellValue('D1', 'Laporan Retur Penjualan Detail');
                $sheet->setCellValue('D2', $_peri);

                $column = 3;
                $totbruto = 0;
                $totnetto = 0;
                $totdisc  = 0;
                $tothrg  = 0;
                $totppn  = 0;
                foreach ($lap as $key => $value) {
                    if ($key == 0) {
                        $sheet->setCellValue('B' . $column, $value->nama);
                        $sheet->getStyle('A' . $column)->getFont()->setBold(true);
                        $colLine = ($column + 1);
                        $colLineD = ($column + 2);
                    } else {
                        $colCS = ($column + 2);
                        $colLine = ($column + 3);
                        $colLineD = ($column + 4);
                        $sheet->setCellValue('B' . $colCS, $value->nama);
                        $sheet->getStyle('A' . $colCS)->getFont()->setBold(true);
                    }

                    $totqty  = 0;
                    $tothrgjual  = 0;
                    $tothrg  = 0;
                    $totdisc = 0;
                    $per_cust = "SELECT ar_returdetil.*, 
                                    inv_barang.namabarang, 
                                    ar_retur.kodesi,  
                                    ar_retur.keterangan, 
                                    ar_retur.tglretur, 
                                    ar_sifile.nama_sales 
                                FROM ar_returdetil 
                                INNER JOIN inv_barang ON ar_returdetil.kodeitem = inv_barang.kodeitem 
                                INNER JOIN ar_retur ON ar_returdetil.koderetur = ar_retur.koderetur 
                                INNER JOIN ar_sifile ON ar_retur.kodesi = ar_sifile.kodesi 
                                where ar_retur.kodecust='" . $value->kodecust . "' and tglretur between '$_tgl1' and '$_tgl2' order by koderetur";
                    $lap1   = $db->query($per_cust)->getResult();
                    $sheet->setCellValue('A' . $colLine, 'NO');
                    $sheet->setCellValue('B' . $colLine, 'NO RETUR');
                    $sheet->setCellValue('C' . $colLine, 'NO FAKTUR');
                    $sheet->setCellValue('D' . $colLine, 'TANGGAL');
                    $sheet->setCellValue('E' . $colLine, 'NAMA BARANG');
                    $sheet->setCellValue('F' . $colLine, 'BATCH');
                    $sheet->setCellValue('G' . $colLine, 'EXPIRE DATE');
                    $sheet->setCellValue('H' . $colLine, 'QUANTITY');
                    $sheet->setCellValue('I' . $colLine, 'HARGA');
                    $sheet->setCellValue('J' . $colLine, 'DISC');
                    $sheet->setCellValue('K' . $colLine, 'NETTO');
                    $sheet->setCellValue('L' . $colLine, 'MARKETING');
                    $sheet->setCellValue('M' . $colLine, 'KETERANGAN');
                    $sheet->getStyle('A' . $colLine . ':M' . $colLine)->getFont()->setBold(true);
                    foreach ($lap1 as $index => $valueline) {
                        $tot             = ($valueline->qtyretur * $valueline->hargajual);
                        $dis             = ($valueline->disc / 100) * $tot;
                        $total_harga     = $tot - $dis;
                        if ($valueline->qtyretur >= 1) {
                            $sheet->setCellValue('A' . $colLineD, ($index + 1));
                            $sheet->setCellValue('B' . $colLineD, $valueline->koderetur);
                            $sheet->setCellValue('C' . $colLineD, $valueline->kodesi);
                            $sheet->setCellValue('D' . $colLineD, date('d-m-Y', strtotime($valueline->tglretur)));
                            $sheet->setCellValue('E' . $colLineD, $valueline->namabarang);
                            $sheet->setCellValue('F' . $colLineD, $valueline->no_batch);
                            $sheet->setCellValue('G' . $colLineD, $valueline->tanggal_exp);
                            $sheet->setCellValue('H' . $colLineD, $valueline->qtyretur);
                            $sheet->setCellValue('I' . $colLineD, number_format($valueline->hargajual, 0, '.', ','));
                            $sheet->setCellValue('J' . $colLineD, $valueline->disc);
                            $sheet->setCellValue('K' . $colLineD, number_format($total_harga, 0, '.', ','));
                            $sheet->setCellValue('L' . $colLineD, $valueline->nama_sales);
                            $sheet->setCellValue('M' . $colLineD, $db->keterangan);

                            $totqty = $totqty + $valueline->qtyretur;
                            $tothrgjual = $tothrgjual + $valueline->hargajual;
                            $totdisc = $totdisc + $valueline->disc;
                            $tothrg = $tothrg + $total_harga;
                            $column++;
                        }
                    }
                }
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:E2')->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->getColumnDimension('J')->setAutoSize(true);
                $sheet->getColumnDimension('K')->setAutoSize(true);
                $sheet->getColumnDimension('L')->setAutoSize(true);
                $sheet->getColumnDimension('M')->setAutoSize(true);

                // Tentukan nama file
                $fileName = 'Laporan Retur Penjualan Detail.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan Penjualan Detail
            else if ($idlp == 106) {
                $query =
                    "SELECT ar_customer.nama, ar_sifile.*, ar_sidetail.kodeitem FROM ar_sifile INNER JOIN ar_customer ON ar_sifile.kodecust = ar_customer.kode INNER JOIN ar_sidetail ON ar_sifile.kodesi = ar_sidetail.kodesi
                    where tglsi between '$_tgl1' and '$_tgl2' ";

                if ($cust != "") {
                    $query .= "and ar_sifile.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= "and ar_sifile.kodecbg = '$cab'";
                }
                if ($brg != "") {
                    $query .= "and ar_sidetail.kodeitem = '$brg'";
                }
                if ($marketing != "") {
                    $query .= "and ar_sifile.id_sales = '$marketing'";
                    }
                    $query .= " group by nama order by ar_sifile.tglsi, ar_sifile.kodesi";

                // Execute query and initialize spreadsheet
                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Laporan Penjualan Detail');
                $sheet->setCellValue('A1', 'LAPORAN PENJUALAN DETAIL');
                $sheet->setCellValue('A2', $_peri);
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // Starting line for data entry
                $column = 4;

                foreach ($lap as $Penjualan) {
                    // Add Supplier Name
                    $sheet->setCellValue('A' . $column, $Penjualan->nama);
                    $sheet->mergeCells('A' . $column . ':M' . $column);
                    $sheet->getStyle('A' . $column)->getFont()->setBold(true);
                    $column++;
                    
                    $per_cust = "SELECT ar_sidetail.*, ar_sifile.kodesi, ar_sifile.tglsi, ar_sifile.nama_sales 
                                FROM ar_sidetail INNER JOIN ar_sifile ON ar_sidetail.kodesi = ar_sifile.kodesi 
                                where ar_sifile.kodecust='" . $Penjualan->kodecust . "' and tglsi between '$_tgl1' and '$_tgl2'";
                    if ($brg != "") {
                        $per_cust .= " and ar_sidetail.kodeitem = '$brg'";
                    }
                    $lap1   = $db->query($per_cust)->getResult();
                    // Column Headers
                    $headers = ['NO', 'NO FAKTUR', 'TANGGAL', 'NAMA BARANG', 'BATCH', 'ED', 'QTY', 'HARGA', 'DISC', 'NETTO', 'MARKETING'];
                    $sheet->fromArray($headers, NULL, 'A' . $column);
                    $sheet->getStyle('A' . $column . ':M' . $column)->getFont()->setBold(true);
                    $column++;

                    $totqty  = 0;
                    $tothrgjual  = 0;
                    $tothrg  = 0;
                    $totdisc = 0;
                    foreach ($lap1 as $index => $returnItem) {
                        $tot 			= ($returnItem->qtysi * $returnItem->hargajual);
                        $dis 			= ($returnItem->disc / 100) * $tot;
                        $total_harga 	= $tot - $dis;

                        if ($returnItem->qtysi >= 1) {
                            $qinv_barang = $db->query("SELECT * from inv_barang where kodeitem = '" . $returnItem->kodeitem . "'")->getRow();

                            $sheet->setCellValue('A' . $column, $index + 1);
                            $sheet->setCellValue('B' . $column, $returnItem->kodesi);
                            $sheet->setCellValue('C' . $column, date('d-m-Y', strtotime($returnItem->tglsi)));
                            $sheet->setCellValue('D' . $column, $qinv_barang->namabarang);
                            $sheet->setCellValue('E' . $column, $returnItem->no_batch);
                            $sheet->setCellValue('F' . $column, $returnItem->tanggal_exp);
                            $sheet->setCellValue('G' . $column, $returnItem->qtysi);
                            $sheet->setCellValue('H' . $column, number_format($returnItem->hargajual, 0, '.', ','));
                            $sheet->setCellValue('I' . $column, $returnItem->disc);
                            $sheet->setCellValue('J' . $column, number_format($total_harga, 0, '.', ','));
                            $sheet->setCellValue('K' . $column, $returnItem->nama_sales);


                            //calculate total
                            $totqty = $totqty + $returnItem->qtysi;
                            $tothrgjual = $tothrgjual + $returnItem->hargajual;
                            $totdisc = $totdisc + $returnItem->disc;
                            $tothrg = $tothrg + $total_harga;
                            // Increment row count
                            $column++;
                        }
                    }

                    // Add total row for the customer
                    $sheet->setCellValue('A' . $column, 'TOTAL');
                    $sheet->setCellValue('G' . $column, number_format($totqty, 0, '.', ','));
                    $sheet->setCellValue('H' . $column, number_format($tothrgjual, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, number_format($totdisc, 0, '.', ','));
                    $sheet->setCellValue('J' . $column, number_format($tothrg, 0, '.', ','));
                    $sheet->mergeCells('A' . $column . ':F' . $column);
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
                $fileName = 'LAPORAN PENJUALAN DETAIL.xlsx';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Save and output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Prevent further output

            }
            // Penjualan per Barang
            else if ($idlp == 107) {
            }
            // Laporan Faktur Belum Lunas
            else if ($idlp == 201) {
                $query =
                    "select ar_sifile.*, ar_customer.nama as namapelanggan from ar_sifile inner join ar_customer on ar_sifile.kodecust=ar_customer.kode 
			 			where tglsi between '$_tgl1' and '$_tgl2' and totalsi>jumlahbayar";

                if ($cust != "") {
                    $query .= " and ar_sifile.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and ar_sifile.kodecbg = '$cab'";
                }

                $query .= " order by tglsi, kodesi";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('FAKTUR BELUM LUNAS');
                $sheet->setCellValue('A3', 'NO');
                $sheet->setCellValue('B3', 'CUSTOMER');
                $sheet->setCellValue('C3', 'NOMOR');
                $sheet->setCellValue('D1', 'FAKTUR BELUM LUNAS');
                $sheet->setCellValue('D2', $_peri);
                $sheet->setCellValue('D3', 'TANGGAL');
                $sheet->setCellValue('E3', 'JATUH TEMPO');
                $sheet->setCellValue('F3', 'TOTAL');
                $sheet->setCellValue('G3', 'PIUTANG');
                $sheet->setCellValue('H3', 'PIUTANG PAJAK');
                $sheet->setCellValue('I3', 'UMUR (HR)');

                $column = 4;
                $totfaktur  = 0;
                $totpiutang  = 0;
                $totpiutangpjk  = 0;
                foreach ($lap as $key => $value) {
                    $piutang    = $value->totalsi - $value->jumlahbayar;
                    $piutangpjk = $value->ppn - $value->ppnbayar;
                    $date1 = strtotime($tgl2);
                    $date2 = strtotime($value->tglsi);
                    $umur  = ($date1 - $date2) / (60 * 60 * 24);
                    $selisihumurhr = date_diff(date_create($tgl2), date_create($value->tglsi));

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->namapelanggan);
                    $sheet->setCellValue('C' . $column, $value->kodesi);
                    $sheet->setCellValue('D' . $column, date('d-m-Y', strtotime($value->tglsi)));
                    $sheet->setCellValue('E' . $column, date('d-m-Y', strtotime($value->tgljthtempo)));
                    $sheet->setCellValue('F' . $column, number_format($value->totalsi, 0, '.', ','));
                    $sheet->setCellValue('G' . $column, number_format($piutang, 0, '.', ','));
                    $sheet->setCellValue('H' . $column, number_format($piutangpjk, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, $selisihumurhr->days);
                    $column++;
                    $totfaktur = $totfaktur + $value->totalsi;
                    $totpiutang = $totpiutang + $piutang;
                    $totpiutangpjk = $totpiutangpjk + $piutangpjk;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('F' . ($column), number_format($totfaktur, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($totpiutang, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($totpiutangpjk, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':E' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':H' . ($column))->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);

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
            // Laporan Piutang
            else if ($idlp == 202) {
                $query =
                    "SELECT b.kode, b.nama as namapelanggan from ar_sifile a inner join ar_customer b on a.kodecust=b.kode 
			 						where year(tglsi) ='$thn' and month(tglsi)='$bln' and a.sppn='Y'";

                if ($cust != "") {
                    $query .= " and a.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and a.kodecbg = '$cab'";
                }
                $query .= " GROUP BY a.kodecust";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN PIUTANG PPN');
                $sheet->setCellValue('A3', 'NO');
                $sheet->setCellValue('B3', 'CUSTOMER');
                $sheet->setCellValue('C3', 'SALDO AWAL');
                $sheet->setCellValue('D1', 'LAPORAN PIUTANG PPN');
                $sheet->setCellValue('D2', $_peri2);
                $sheet->setCellValue('D3', 'PENJUALAN');
                $sheet->setCellValue('E3', 'PEMBAYARAN');
                $sheet->setCellValue('F3', 'SALDO AKHIR');

                $column = 4;
                $totsaldoawal  = 0;
                $totpenjualan  = 0;
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
                    $RowBayarBackDate = $db->query("SELECT * FROM ar_penerimaan where kodecust='$value->kode' and year(tglbayar)='$tahun' and month(tglbayar)='$bulan'")->getResult();
                    $totBayarBackDate  = 0;
                    foreach ($RowBayarBackDate as $DataBayar) {
                        $TotRowSifile = $db->query("SELECT sum(totalsi) as saldoawal from ar_sifile where kodecust='$value->kode' and kodebyr='$DataBayar->kodebyr' and year(tglsi) ='$tahun' and month(tglsi)='$bulan'")->getRow();
                        $totBayarBackDate += $TotRowSifile->saldoawal;
                    }
                    $saldoawal = $db->query("SELECT sum(totalsi) as saldoawal from ar_sifile where kodecust='$value->kode' and year(tglsi) ='$tahun' and month(tglsi)='$bulan'")->getRow();
                    $totjual = $db->query("SELECT sum(totalsi) as totaljual from ar_sifile where kodecust='$value->kode' and year(tglsi) ='$thn' and month(tglsi)='$bln'")->getRow();
                    $totbayar = $db->query("SELECT sum(jumlahbayar) as totalbayar from ar_sifile where kodecust='$value->kode' and year(tglsi) ='$thn' and month(tglsi)='$bln'")->getRow();

                    $SaldoAwalBackDate = $saldoawal->saldoawal - $totBayarBackDate;

                    $HsaldoAkhir = $totjual->totaljual + $SaldoAwalBackDate - $totbayar->totalbayar;

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->namapelanggan);
                    $sheet->setCellValue('C' . $column, intval($SaldoAwalBackDate));
                    $sheet->setCellValue('D' . $column, intval($totjual->totaljual));
                    $sheet->setCellValue('E' . $column, intval($totbayar->totalbayar));
                    $sheet->setCellValue('F' . $column, intval($HsaldoAkhir));

                    $totsaldoawal = $totsaldoawal + $SaldoAwalBackDate;
                    $totpenjualan = $totpenjualan + $totjual->totaljual;
                    $totpembayaran = $totpembayaran + $totbayar->totalbayar;
                    $totsaldoakhir = $totsaldoakhir + $HsaldoAkhir;
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('C' . ($column), number_format($totsaldoawal));
                $sheet->setCellValue('D' . ($column), number_format($totpenjualan));
                $sheet->setCellValue('E' . ($column), number_format($totpembayaran));
                $sheet->setCellValue('F' . ($column), number_format($totsaldoakhir));
                $sheet->mergeCells('A' . ($column) . ':B' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':F' . ($column))->getFont()->setBold(true);
                foreach (range('A', 'F') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
                // foreach (range('C', 'F') as $columnID) {
                //     $sheet->getStyle($columnID . ':' . $columnID)
                //     ->getNumberFormat()->setFormatCode('#,##0');
                // }

                // Tentukan nama file
                $fileName = 'LAPORAN PIUTANG.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan Umur Piutang
            else if ($idlp == 203) {
                $query = "select * from ar_customer order by nama";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('UMUR PIUTANG');
                $sheet->setCellValue('A3', 'NO');
                $sheet->setCellValue('B3', 'CUSTOMER');
                $sheet->setCellValue('C3', 'PIUTANG');
                $sheet->setCellValue('D1', 'UMUR PIUTANG');
                $sheet->setCellValue('D2', $_peri);
                $sheet->setCellValue('D3', 'BLM TEMPO');
                $sheet->setCellValue('E3', '1 - 30');
                $sheet->setCellValue('F3', '31 - 60');
                $sheet->setCellValue('G3', '61- 90');
                $sheet->setCellValue('H3', '91 - 120');
                $sheet->setCellValue('I3', '> 120');

                $column = 4;
                $totpiutang = 0;
                $totbelum  = 0;
                $tottempo1 = 0;
                $tottempo2 = 0;
                $tottempo3 = 0;
                $tottempo4 = 0;
                $tottempo5 = 0;
                foreach ($lap as $key => $value) {
                    $cust = $value->kode;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $piutang  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) < 1 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $blmtempo  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 1 and 30 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $tempo1  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 31 and 60 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $tempo2  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 61 and 90 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $tempo3  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       						where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) between 91 and 120 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $tempo4  = $data->total;

                    $query = "select sum(totalsi-jumlahbayar) as total from ar_sifile
				       		where totalsi>jumlahbayar and datediff('$_tgl2',tglsi) > 120 and kodecust = '$cust'";
                    $data = $db->query($query)->getRow();
                    $tempo5  = $data->total;


                    $totpiutang = $totpiutang + $piutang;
                    $totbelum  = $totbelum + $blmtempo;
                    $tottempo1 = $tottempo1 + $tempo1;
                    $tottempo2 = $tottempo2 + $tempo2;
                    $tottempo3 = $tottempo3 + $tempo3;
                    $tottempo4 = $tottempo4 + $tempo4;
                    $tottempo5 = $tottempo5 + $tempo5;

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $value->nama);
                    $sheet->setCellValue('C' . $column, number_format($piutang, 0, '.', ','));
                    $sheet->setCellValue('D' . $column, number_format($blmtempo, 0, '.', ','));
                    $sheet->setCellValue('E' . $column, number_format($tempo1, 0, '.', ','));
                    $sheet->setCellValue('F' . $column, number_format($tempo2, 0, '.', ','));
                    $sheet->setCellValue('G' . $column, number_format($tempo3, 0, '.', ','));
                    $sheet->setCellValue('H' . $column, number_format($tempo4, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, number_format($tempo5, 0, '.', ','));
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('C' . ($column), number_format($totpiutang, 0, ',', '.'));
                $sheet->setCellValue('D' . ($column), number_format($totbelum, 0, ',', '.'));
                $sheet->setCellValue('E' . ($column), number_format($tottempo1, 0, ',', '.'));
                $sheet->setCellValue('F' . ($column), number_format($tottempo2, 0, ',', '.'));
                $sheet->setCellValue('G' . ($column), number_format($tottempo3, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($tottempo4, 0, ',', '.'));
                $sheet->setCellValue('I' . ($column), number_format($tottempo5, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':B' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);

                // Tentukan nama file
                $fileName = 'UMUR PIUTANG.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Laporan piutang penjualan yang sudah bayar/lunas
            elseif ($idlp == 204) {
                $query =
                    "select a.*, b.* from ar_penerimaan a
						inner join ar_penerimaandetil b on a.kodebyr = b.kodebyr 
					    where tglbayar between '$_tgl1' and '$_tgl2'";

                if ($cust != "") {
                    $query .= " and a.kodecust = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and a.kodecbg = '$cab'";
                }
                $query .= " order by tglbayar";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN SUDAH BAYAR');
                $sheet->setCellValue('A3', 'NO');
                $sheet->setCellValue('B3', 'CUSTOMER');
                $sheet->setCellValue('C3', 'NOMOR INPUT');
                $sheet->setCellValue('D1', 'LAPORAN PIUTANG PENJUALAN YANG SUDAH BAYAR/LUNAS');
                $sheet->setCellValue('D2', $_peri);
                $sheet->setCellValue('D3', 'NOMOR FAKTUR');
                $sheet->setCellValue('E3', 'TANGGAL FAKTUR');
                $sheet->setCellValue('F3', 'TANGGAL BAYAR');
                $sheet->setCellValue('G3', 'NOMINAL FAKTUR');
                $sheet->setCellValue('H3', 'NOMINAL FAKTUR YANG DIBAYARKAN');
                $sheet->setCellValue('I3', 'PEMBAYARAN (BCA,BJB,TUNAI)');

                $column = 4;
                $totfaktur  = 0;
                $totbayar  = 0;
                foreach ($lap as $key => $value) {
                    $DataCustomer = $db->table('ar_customer')->getWhere(['kode' => $value->kodecust])->getRow();
									$DataAkun = $db->table('ms_akun')->getWhere(['kodeakun' => $value->kodebank])->getRow();

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, $DataCustomer->nama);
                    $sheet->setCellValue('C' . $column, $value->kodebyr);
                    $sheet->setCellValue('D' . $column, $value->nomorfaktur);
                    $sheet->setCellValue('E' . $column, date('d-m-Y', strtotime($value->tglfaktur)));
                    $sheet->setCellValue('F' . $column, date('d-m-Y', strtotime($value->tglbayar)));
                    $sheet->setCellValue('G' . $column, number_format($value->totalfaktur, 0, '.', ','));
                    $sheet->setCellValue('H' . $column, number_format($value->bayar, 0, '.', ','));
                    $sheet->setCellValue('I' . $column, $DataAkun->namaakun);
                    $totfaktur = $totfaktur + $value->totalfaktur;
									$totbayar = $totbayar + $value->bayar;
                    $column++;
                }
                $sheet->setCellValue('A' . ($column), 'TOTAL');
                $sheet->setCellValue('G' . ($column), number_format($totfaktur, 0, ',', '.'));
                $sheet->setCellValue('H' . ($column), number_format($totbayar, 0, ',', '.'));
                $sheet->mergeCells('A' . ($column) . ':F' . ($column));
                $sheet->getStyle('A' . ($column))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('D2:E2');
                $sheet->getStyle('A1:J3')->getFont()->setBold(true);
                $sheet->getStyle('A' . ($column) . ':I' . ($column))->getFont()->setBold(true);
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);

                // Tentukan nama file
                $fileName = 'LAPORAN PIUTANG PENJUALAN YANG SUDAH BAYAR/LUNAS.xlsx';

                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                // Simpan spreadsheet ke output
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit; // Hentikan script setelah download
            }
            // Cortex Data
            else if ($idlp == 301) {
                $query = "SELECT * from ar_sifile where sppn='Y' and retur=1 and tglsi between '$_tgl1' and '$_tgl2' ";

                $lap = $db->query($query)->getResult();
                // Load template Excel
                $inputFileName = WRITEPATHCORETAX . '/templatev2.xlsx';
                $spreadsheet = IOFactory::load($inputFileName);

                // Mengisi data di Sheet 1
                $sheet = $spreadsheet->getSheet(0);
                $sheet->setCellValue('C1', $profile->npwp);
                $column = 4;
                $differentDataCortex = [];
                foreach ($lap as $key => $value) {
                    $DataCs = $db->query("SELECT * from ar_customer where kode='$value->kodecust'")->getRow();
                    $differentDataCortex[] = [
                        'baris' => ($column - 3),
                        'kodesi' => $value->kodesi,
                        'kodecust' => $value->kodecust,
                        'cabang' => $value->kodecbg
                    ];

                    $sheet->setCellValue('A' . $column, ($column - 3));
                    $sheet->setCellValue('B' . $column, date('d/m/Y', strtotime($value->tglsi)));
                    $sheet->setCellValue('C' . $column, 'Normal');
                    $sheet->setCellValue('D' . $column, '04');
                    $sheet->setCellValue('E' . $column, '');
                    $sheet->setCellValue('F' . $column, '');
                    $sheet->setCellValue('G' . $column, '');
                    $sheet->setCellValue('H' . $column, $value->kodesi . '-' . $DataCs->nama);
                    $sheet->setCellValue('I' . $column, '');
                    $sheet->setCellValue('J' . $column, $profile->npwp . '000000');
                    $sheet->setCellValue('K' . $column, str_replace(['.', '-'], '', '0' . $DataCs->npwp));
                    $sheet->setCellValue('L' . $column, $DataCs->taxjenisidcs);
                    $sheet->setCellValue('M' . $column, $DataCs->taxnegaracs);
                    $sheet->setCellValue('N' . $column, '-');
                    $sheet->setCellValue('O' . $column, $DataCs->nama_pt);
                    $sheet->setCellValue('P' . $column, $DataCs->alamat1);
                    $sheet->setCellValue('Q' . $column, $DataCs->email);
                    $sheet->setCellValue('R' . $column, "0" . $DataCs->nojenisid . '000000');
                    $column++;
                }

                // Mengisi data di Sheet 2
                $sheet2 = $spreadsheet->getSheet(1);
                $colshet2 = 2;
                $differentDataCortex;
                $jumdatasts  = count($differentDataCortex);
                for ($i = 0; $i < $jumdatasts; $i++) {
                    $DetailFaktur = $db->table('ar_sidetail')->getWhere(['kodesi' => $differentDataCortex[$i]['kodesi']])->getResult();
                    foreach ($DetailFaktur as $value) {
                        $DataBarang = $db->table('inv_barang')->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' =>     $differentDataCortex[$i]['cabang']])->getRow();
                        $qty = intval($value->qtysi);
                        $hargajual = intval($value->hargajual);
                        $JumlahBarangDanHarga = $qty * $hargajual;
                        $JumlahDiskon = ($value->disc / 100) * $JumlahBarangDanHarga;
                        $DPP = $JumlahBarangDanHarga - $JumlahDiskon;
                        $a = $profile->dpplainnya;
                        $DPPLain = floatval($profile->dpplainnya) * $DPP;
                        // $ppn = $DPPLain * floatval($profile->taxes_desimal);
                        $ppn = $DPPLain * 0.12;

                        $sheet2->setCellValue('A' . $colshet2, $differentDataCortex[$i]['baris']);
                        $sheet2->setCellValue('B' . $colshet2, $DataBarang->taxbarangjasa);
                        $sheet2->setCellValue('C' . $colshet2, "'000000");
                        $sheet2->setCellValue('D' . $colshet2, $DataBarang->namabarang);
                        $sheet2->setCellValue('E' . $colshet2, $DataBarang->taxsatuan);
                        $sheet2->setCellValue('F' . $colshet2, $value->hargajual);
                        $sheet2->setCellValue('G' . $colshet2, $value->qtysi);
                        $sheet2->setCellValue('H' . $colshet2, $JumlahDiskon);
                        $sheet2->setCellValue('I' . $colshet2, '=G' . $colshet2 . '*F' . $colshet2 . '-H' . $colshet2);
                        $sheet2->setCellValue('J' . $colshet2, '=11/12*I' . $colshet2);
                        $sheet2->setCellValue('K' . $colshet2, '12');
                        $sheet2->setCellValue('L' . $colshet2, '=J' . $colshet2 . '*K' . $colshet2 . '/100');
                        $sheet2->setCellValue('M' . $colshet2, 0);
                        $sheet2->setCellValue('N' . $colshet2, '=M' . $colshet2 . '*J' . $colshet2 . '/100');
                        $colshet2++;
                    }
                }

                // Simpan file baru
                $outputFileName = 'Coretax.xlsx';
                // Set header untuk download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $outputFileName . '"');
                header('Cache-Control: max-age=0');
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
            }
            //LAPORAN DATA CUSTOMER
            else if ($idlp == 401) {
                $query =
                    "select * from ar_customer where kode is not null";

                if ($cust != "") {
                    $query .= " and kode = '$cust'";
                }
                if ($cab != "") {
                    $query .= " and kode_cabang = '$cab'";
                }
                if ($marketing != "") {
                    $query .= " and kodesales = '$marketing'";
                }
                $query .= " order by nama";

                $lap = $db->query($query)->getResult();
                $spreadsheet = new Spreadsheet();
                $sheet =  $spreadsheet->getActiveSheet();
                $sheet->setTitle('LAPORAN DATA CUSTOMER');
                $sheet->setCellValue('A1', 'DATA CUSTOMER');
                $headers = ['NO', 'NAMA' , 'NAMA NPWP ', 'NO NPWP', 'ALAMAT', 'TELP','KONTAK', 'TOP', 'MARKETING','BATAS KREDIT'];
                $sheet->fromArray($headers, NULL, 'A2');
                $sheet->getStyle('A1:J2')->getFont()->setBold(true);
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $column = 3;
                foreach ($lap as $key => $value) {
                    $sheet->setCellValue('A' . $column, ($column - 2));
                    $sheet->setCellValue('B' . $column, $value->nama);
                    $sheet->setCellValue('C' . $column, $value->nama_pt);
                    $sheet->setCellValue('D' . $column, $value->npwp);
                    $sheet->setCellValue('E' . $column, $value->alamat1);
                    $sheet->setCellValue('F' . $column, $value->telp);
                    $sheet->setCellValue('G' . $column, $value->contactname);
                    $sheet->setCellValue('H' . $column, $value->top);
                    $sheet->setCellValue('I' . $column, $value->nama_marketing);
                    $sheet->setCellValue('J' . $column, number_format($value->bataskredit, 0, '.', ','));
                    $column++;
                }

                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Tentukan nama file
                $fileName = 'LAPORAN DATA CUSTOMER.xlsx';

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
