<?php
error_reporting(0);
$db = db_connect();
if ($data != []) {
    if ($data->statusid == 3 || $data->statusid == 5) {
        main_header('Penjualan', 'Data Pesanan Penjualan', ' Daftar Pesanan Penjualan', 'View Pesanan Penjualan', 'penjualan/transaksi/pesananmasuk');
        $DataOrder = 'Data View';
    } else if ($data->statusid == 1) {
        main_header('Penjualan', 'Data Pesanan Penjualan', ' Daftar Pesanan Penjualan', 'Edit Pesanan Penjualan', 'penjualan/transaksi/pesananmasuk');
        $DataOrder = 'Data Edit';
    }
    $CSRow = $db->table("ar_customer")->getWhere(['kode' => $data->kodecust])->getRow();
} else {
    main_header('Penjualan', 'Data Customer', ' Daftar Pesanan Penjualan', 'Entri Pesanan Penjualan', 'penjualan/transaksi/pesananmasuk');
    $DataOrder = 'Data Baru';
}
?>


<div class="form-group" id="process" style="display: none;">
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
</div>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="row">
            <div class="caption col-md-11">
                <i class="fa fa-reorder"></i>* <?= $DataOrder; ?>
            </div>
        </div>
    </div>

    <div class="portlet-body form">
        <form id="submit_form" class="form-horizontal">
            <input type="hidden" value="<?= ($data != []) ? $data->id : 0 ?>" name="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Pesanan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false"> Biaya Lainnya</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Dipesan Oleh *</label>
                                            <div class="col-md-6">
                                                <select id="cust" name="cust" class="form-control input-medium select2" data-placeholder="Pilih...">
                                                    <option>---Pilih Customer</option>
                                                    <?php foreach ($res as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kodecust) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>

                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="getcs()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">No. Pesanan # *</label>
                                            <div class="col-md-7">
                                                <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= ($data != []) ? $data->kodeso : '' ?>" readonly>
                                                <input type="text" class="form-control input-medium" placeholder="Nomor Auto" value="<?= ($data != []) ? $data->kodeso : 'Nomor Auto' ?>" readonly>
                                                <input type="hidden" class="form-control input-medium" name="NoPOBeli" id="NoPOBeli" value="<?= ($data != []) ? $data->kodepo : $NoPOBeli ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal *</label>
                                            <div class="col-md-6">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tglso)) : date('Y-m-d') ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Marketing</label>
                                            <div class="col-md-7">
                                                <input id="marketing" readonly name="sales" class="form-control input-medium" type="text" value="<?= ($data != []) ? $data->nama_sales : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Third Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-6">
                                                <?php if ($data != []) { ?>
                                                    <select name="sppn" id="sppn" class="form-control select2 input-medium">
                                                        <option value="">pilih</option>
                                                        <option value="Y" <?= ($data->sppn == 'Y') ? "selected" : '' ?>>Ya</option>
                                                        <option value="T" <?= ($data->sppn == 'T') ? "selected" : '' ?>>Tidak</option>
                                                    </select>
                                                <?php } else { ?>
                                                    <select name="sppn" id="sppn" class="form-control select2 input-medium" data-placeholder="Pilih...">

                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Pembayaran</label>
                                            <div class="col-md-7">
                                                <?php if ($data != []) { ?>
                                                    <select name="pembayaran" id="pembayaran" class="form-control select2 input-medium" value="<?= ($data != []) ? $data->pembayaran : '' ?>">
                                                        <option value="T" <?= ($data->pembayaran == 'T') ? "selected" : '' ?>>Tunai</option>
                                                        <option value="K" <?= ($data->pembayaran == 'K') ? "selected" : '' ?>>Kredit</option>
                                                    </select>
                                                <?php } else { ?>
                                                    <select name="pembayaran" id="pembayaran" class="form-control select2 input-medium" data-placeholder="Pilih...">

                                                    </select>
                                                <?php } ?>
                                                <input type="hidden" class="form-control input-medium" name="saldokredit" id="saldokredit" value="<?= ($data != []) ? $CSRow->saldokredit : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Four Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. PO</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control input-medium" name="no-po" id="no-po" value="<?= ($data != []) ? $data->no_po : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Five Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">Nama Barang</th>
                                                <th style="text-align: center">Kuantitas</th>
                                                <th style="text-align: center">Satuan</th>
                                                <th style="text-align: center">Harga</th>
                                                <th style="text-align: center">Disc</th>
                                                <th style="text-align: center"></th>
                                                <th style="text-align: center"></th>
                                                <th style="text-align: center">Detail Paket</th>
                                            </thead>
                                            <tbody align="center">
                                                <?php if ($DetailItem != [] && $data->statusid == 1 || $data->statusid == 5) {
                                                    $no = 0;
                                                    foreach ($DetailItem as $key) {
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode<?= $no ?>" class="barang_filter_cabang form-control input-xlarge kode " onchange="showbarangname(this.value, <?= $no ?>)">
                                                                    <option value="<?= $key->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                                <input name="cn1[]" value="<?= $key->fee1_rp; ?>" id="a-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" onchange="totalline(<?= $no ?>)" value="<?= $key->qtyorder - $key->qtykirim ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small" <?php if ($key->qtykirim != 0) {
                                                                                                                                                                                                                                                        echo "readonly";
                                                                                                                                                                                                                                                    } ?>>

                                                                <input name="harga[]" value="<?= $key->hargajual; ?>" id="harga<?= $no ?>" type="hidden" class="form-control rightJustified">

                                                                <input name="disc[]" value="<?= $key->disc; ?>" id="disc<?= $no ?>" type="hidden" class="form-control rightJustified ">

                                                                <input name="cn2[]" value="<?= $key->fee2_rp; ?>" id="b-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $key->satuan ?>" class="form-control input-small">

                                                                <input name="jumlah[]" id="jumlah<?= $no ?>" type="hidden" class="form-control rightJustified" size="40%" readonly>

                                                                <input name="cn3[]" value="<?= $key->fee3_rp; ?>" id="c-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="hargaview[]" onchange="totalline(1)" value="<?= $key->hargajual; ?>" id="harga<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="discview[]" onchange="totalline(1)" value="<?= $key->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>
                                                            </td>
                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="infobrg<?= $no ?>" data-toggle="modal" href="#infobrg" onclick="infobrg(this.id)"><i class="fa fa-info"></i></a>
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                totalline(<?= $no ?>)
                                                            });
                                                        </script>
                                                    <?php
                                                    }
                                                } else if ($DetailItem != [] && $data->statusid == 3) {
                                                    $no = 0;
                                                    foreach ($DetailItem as $key) {
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode<?= $no ?>" class="barang_filter_cabang form-control input-xlarge " onchange="showbarangname(this.value, <?= $no ?>)">
                                                                    <option value="<?= $key->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                                <input name="cn1[]" value="<?= $key->fee1_rp; ?>" id="a-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" value="<?= $key->qtyorder - $key->qtykirim ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>

                                                                <input name="harga[]" value="<?= $key->hargajual; ?>" id="harga<?= $no ?>" type="hidden" class="form-control rightJustified">

                                                                <input name="disc[]" value="<?= $key->disc; ?>" id="disc<?= $no ?>" type="hidden" class="form-control rightJustified ">

                                                                <input name="cn2[]" value="<?= $key->fee2_rp; ?>" id="b-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $key->satuan ?>" class="form-control input-small">

                                                                <input name="jumlah[]" id="jumlah<?= $no ?>" type="hidden" class="form-control rightJustified" size="40%" readonly>

                                                                <input name="cn3[]" value="<?= $key->fee3_rp; ?>" id="c-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="hargaview[]" onchange="totalline(1)" value="<?= $key->hargajual; ?>" id="harga<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>
                                                            </td>
                                                            <td>
                                                                <input name="discview[]" onchange="totalline(1)" value="<?= $key->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>
                                                            </td>    
                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="infobrg<?= $no ?>" data-toggle="modal" href="#infobrg" onclick="infobrg(this.id)"><i class="fa fa-info"></i></a>
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                totalline(<?= $no ?>)
                                                            });
                                                        </script>
                                                    <?php  }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name="kode[]" id="kode1" class="barang_filter_cabang input-xlarge kode" onchange="showbarangname(this.value, 1)">
                                                                <option value="">--- Pilih Barang ---</option>

                                                            </select>
                                                            <input name="cn1[]" onchange="totalline(1)" value="0" id="a-cn1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="qty[]" onchange="totalline(1);total()" value="" id="qty1" type="text" class="form-control qty rightJustified input-small">

                                                            <input name="harga[]" onchange="totalline(1)" value="0" id="harga1" type="hidden" class="form-control rightJustified">

                                                            <input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="hidden" class="form-control rightJustified ">

                                                            <input name="cn2[]" onchange="totalline(1)" value="0" id="b-cn1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="sat[]" id="sat1" type="text" value="" class="form-control input-small">

                                                            <input name="jumlah[]" id="jumlah1" type="hidden" class="form-control rightJustified" size="40%">

                                                            <input name="cn3[]" onchange="totalline(1)" value="0" id="c-cn1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="hargaview[]" onchange="totalline(1)" value="0" id="hargaview1" type="text" class="form-control rightJustified input-small" readonly>
                                                        </td>
                                                        <td>
                                                            <input name="discview[]" onchange="totalline(1)" value="0" id="discview1" type="text" class="form-control rightJustified input-small" readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                        </td>
                                                        <td><a class="btn default" id="infobrg1" data-toggle="modal" href="#infobrg" onclick="infobrg(this.id)"><i class="fa fa-info"></i></a></td>
                                                        <td>
                                                            <a class="btn default" id="ViewDetailPaket1" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-9">
                                            <div class="wells">
                                                <button type="button" onclick="tambah()" id="tambahbarang" class="btn green"><i class="fa fa-plus"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Kirim</label>
                                            <div class="col-md-6">
                                                <input id="tanggalkirim" name="tanggal_kirim" class="form-control date-picker input-medium" type="text" value="<?= ($data != []) ? $data->tglkirim : date('d-m-Y') ?>" placeholder="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Keterangan</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= ($data != []) ? $data->ket : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Alamat</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" name="alamat" id="alamat"><?= ($data != []) ? $data->alamat : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable2" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">#Kode/ Nama Biaya</th>
                                                <th style="text-align: center">Jumlah</th>
                                                <th style="text-align: center">Keterangan</th>
                                            </thead>
                                            <tbody align="center">
                                                <?php if ($BiayaLain != []) {
                                                    foreach ($BiayaLain as $key) {
                                                        $akun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->kodeakun'")->getRow();
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <select name="lkode[]" class="selectakun form-control input-xlarge">
                                                                    <option value="">--- Pilih Akun ---</option>
                                                                </select>
                                                            </td>
                                                            <td><input name="ljumlah[]" id="ljumlah0" type="text" class="form-control rightJustified input-medium" onkeyup="total_sub()" value="<?= $key->jumlah ?>"></td>
                                                            <td><input name="lket[]" id="lket0" type="text" class="form-control input-medium" value=""></td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name="lkode[]" class="selectakun form-control input-xlarge">
                                                                <option value="">--- Pilih Akun ---</option>
                                                            </select>
                                                        </td>
                                                        <td><input name="ljumlah[]" id="ljumlah0" type="text" class="form-control rightJustified input-medium" onkeyup="total_sub()" value="0"></td>
                                                        <td><input name="lket[]" id="lket0" type="text" class="form-control input-medium" value=""></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-9">
                                    <div class="wells">
                                        <button type="button" onclick="tambah2()" class="btn green"><i class="fa fa-plus"></i> </button>
                                        <button type="button" onclick="deleteRow2(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <div class="wells">
                                <button type="submit" id="save_btn" class="btn blue"><i class="fa fa-save"></i>
                                    Simpan</button>
                                <a class="btn yellow print_laporan" onclick="javascript:window.open(_urlcetak(),'_blank');"><i class="fa fa-print"></i> Tanda Terima</a>
                                <a class="btn default print_laporan" onclick="javascript:window.open(_urlcetak_shipping(),'_blank');"><i class="fa fa-print"></i> Cetak Shipping</a>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-block">
                            <div class="well">
                                <table>
                                    <tr>
                                        <td width="40%"><strong>SUB TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vsubtotal"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>DISKON</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vdiskon"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>DPP</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_dpp"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>PPN</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vppn"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>BIAYA LAIN</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vbiayalain"></span></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vtotal"></span></strong></td>
                                        <input type="hidden" id="_h_total" name="_h_total">

                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Message Logs Modal -->
<div class="modal fade" id="messagelogs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- PERBAIKAN: Tag <span> pembungkus yang tidak valid dan tidak ditutup telah dihapus -->
            <div class="modal-header">
                <h4 class="modal-title">Message Logs</h4>
                <!-- Tambahan: Tombol X untuk menutup modal di pojok kanan atas -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tempatkan konten log pesan Anda di sini -->
                <div id="log-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
            </div>
        </div> <!-- /modal-content -->
    </div> <!-- /modal-dialog -->
</div> <!-- /modal-fade -->

<!-- info Barang -->
<div class="modal fade" id="infobrg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="infobarang">
                <div class="modal-header">
                    <h4 class="modal-title">Daftar Info Barang Penjualan</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarbarang"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>

<!-- Modal View Detail Barang Paket -->
<div class="modal fade" id="ViewDetailPaket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbarang">
                <div class="modal-header">
                    <h4 class="modal-title">Barang Paket</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarbarangpaket">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
        </div>
    </div>
</div>

<script>
    var cbg = '<?= $cabang ?>';
    var idrow = 2;
    var idrow2 = 2;
    $(document).ready(function() {
        initailizeSelect2_barang_filterCabang(cbg);
        document.getElementById('nomorbukti').focus();
    });

    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var td6 = x.insertCell(5);
        var td7 = x.insertCell(6);
        var td8 = x.insertCell(7);

        var akun = "<select name='kode[]' id=kode" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control input-xlarge' ><option value=''>--- Pilih Barang ---</option></select>     <input name='cn1[]'  id=a-cn" + idrowx + " onchange='totalline(" + idrowx +
            ") value='0'  type='hidden' class='form-control rightJustified'>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]'  id=qty" + idrowx + " onchange='totalline(" + idrowx +
            ")' value=''  type='text' class='form-control rightJustified input-small'>    <input name='harga[]'  id=harga" + idrowx + " onchange='totalline(" + idrowx +
            ") value='0'  type='hidden' class='form-control rightJustified'>   <input name='disc[]'   id=disc" + idrowx + " onchange='totalline(" + idrowx +
            ")' value='0'  type='hidden' class='form-control rightJustified'>    <input name='cn2[]'  id=b-cn" + idrowx +
            " onchange='totalline(" + idrowx + ") value='0'  type='hidden' class='form-control rightJustified'>";
        td3.innerHTML = "<input name='sat[]'    id=sat" + idrowx +
            " type='text' class='form-control input-small' >     <input name='jumlah[]' id=jumlah" + idrowx +
            " type='hidden' class='form-control rightJustified' size='40%'>     <input name='cn3[]'  id=c-cn" + idrowx +
            " onchange='totalline(" + idrowx + ") value='0'  type='hidden' class='form-control rightJustified'>";
        td4.innerHTML = "<input name='hargaview[]'    id=hargaview" + idrowx +
            " type='text' class='form-control input-small'  readonly> ";
        td5.innerHTML = "<input name='discview[]'    id=discview" + idrowx +
            " type='text' class='form-control input-small' readonly> ";
        td6.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        td7.innerHTML = "<a class='btn default' id=infobrg" + idrowx + " data-toggle='modal' href='#infobrg' onclick='infobrg(this.id)'><i class='fa fa-info'></i></a>";
        td8.innerHTML = "<a class='btn default' id=ViewDetailPaket" + idrowx +
            " data-toggle='modal' onclick='getPaket(this.id)'><i class='fa fa-search'></i></a>";
        initailizeSelect2_barang_filterCabang(cbg);
        idrow++;
    }

    function tambah2() {
        var table = document.getElementById("datatable2");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable2').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);

        var akun =
            "<select name='lkode[]' class='selectakun form-control' ><option value=''>--- Pilih Akun ---</option></select>";

        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ljumlah[]' id=ljumlah" + idrow2 + " onchange='totalline(" + idrow2 +
            ")' value='0'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='lket[]'    id=lket" + idrow2 + " type='text' class='form-control' >";
        initailizeSelect2_akun();
        idrow2++;
    }

    //Delete Table Element Data
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        $('#save_btn').attr('disabled', false);
        $('#tambahbarang').attr('disabled', false);
        total();
    }

    function deleteRow2(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable2").deleteRow(i);
    }

    function getcs() {
        var xhttp;
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var str = $('[name=cust]').val();
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/pesananmasuk/DataCustomer/') ?>" + str,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                $('#marketing').val(data.marketting_data.nama_sales);

                if (data.customer_data.ppn == 'Y') {
                    var sppn = $("<option selected></option>").val(data.customer_data.ppn).text('Ya');
                    $('#sppn').append(sppn).trigger('change');
                    $('#nomorbukti').val(nomorbukti_ppn);
                } else {
                    var sppn = $("<option selected></option>").val(data.customer_data.ppn).text('Tidak');
                    $('#sppn').append(sppn).trigger('change');
                    $('#nomorbukti').val(nomorbukti);
                }

                if (data.customer_data.kredit == 'K') {
                    var bayar = $("<option selected></option>").val(data.customer_data.kredit).text('Kredit');
                    $('#pembayaran').append(bayar).trigger('change');
                    var bayar_a = $("<option ></option>").val('T').text('Tunai');
                    $('#pembayaran').append(bayar_a).trigger('change');
                } else {
                    var bayar = $("<option selected></option>").val(data.customer_data.kredit).text('Tunai');
                    $('#pembayaran').append(bayar).trigger('change');
                    var bayar_a = $("<option ></option>").val('K').text('Kredit');
                    $('#pembayaran').append(bayar_a).trigger('change');

                }
                $('#saldokredit').val(data.customer_data.saldokredit);
                initailizeSelect2_barang_filterCabang(cbg);
            }
        });
    }

    function showbarangname(str, id) {
        var xhttp;
        var vid = id;
        var customer = $('#cust').val();
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/pesananmasuk/ShowBarang/') ?>" + str + "/" + customer + "/" + cbg,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (!$.trim(data.BarangPerCust_data)) {
                    swal('PESANAN PENJUALAN', 'harga barang ini belum tersedia', '');
                    $('#save_btn').attr('disabled', true);
                    $('#tambahbarang').attr('disabled', true);

                } else {
                    //$('#namabarang'+vid).val(data.namabarang);
                    $('#sat' + vid).val(data.Barang_data.satuan);
                    $('#harga' + vid).val(data.BarangPerCust_data.bruto_cust);
                    $('#hargaview' + vid).val(data.BarangPerCust_data.bruto_cust);
                    $('#disc' + vid).val(data.BarangPerCust_data.diskon_pr);
                    $('#discview' + vid).val(data.BarangPerCust_data.diskon_pr);
                    $('#a-cn' + vid).val(data.BarangPerCust_data.fee1_rp);
                    $('#b-cn' + vid).val(data.BarangPerCust_data.fee2_rp);
                    $('#c-cn' + vid).val(data.BarangPerCust_data.fee3_rp);
                    totalline(vid);
                    $('#tambahbarang').attr('disabled', false);
                }
            }
        });

    }

    function total() {

        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            jumlah = row.cells[1].children[0].value;
            harga = row.cells[1].children[1].value;
            diskon = row.cells[1].children[2].value;
            var jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
            var harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));
            var diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));

            tjumlah = tjumlah + eval(jumlah1 * harga1);

            diskon = eval((diskon1 / 100) * jumlah1 * harga1);

            tdiskon = tdiskon + diskon;


        }

        var table2 = document.getElementById('datatable2');
        var rowCount2 = table2.rows.length;

        tbiaya = 0;

        for (var i = 1; i < rowCount2; i++) {
            var row = table2.rows[i];

            biaya = row.cells[1].children[0].value;
            var biaya1 = Number(biaya.replace(/[^0-9\.]+/g, ""));

            tbiaya = tbiaya + eval(biaya1);
        }

        var cppn = document.getElementById("sppn").value;
        if (cppn == "Y") {
            tppn = (tjumlah - tdiskon) * 0.11;
        } else {
            tppn = 0;
        }


        document.getElementById("_vsubtotal").innerHTML = number_format(tjumlah);
        document.getElementById("_vdiskon").innerHTML = number_format(tdiskon);
        document.getElementById("_dpp").innerHTML = number_format(tjumlah - tdiskon);
        document.getElementById("_vbiayalain").innerHTML = number_format(tbiaya);
        document.getElementById("_vppn").innerHTML = number_format(tppn);
        document.getElementById("_vtotal").innerHTML = number_format(tjumlah - tdiskon + tbiaya + tppn);
        document.getElementById("_h_total").value = tjumlah - tdiskon + tbiaya + tppn;

        var total_seluruh = tjumlah - tdiskon + tbiaya + tppn;
        var saldokredit = document.getElementById("saldokredit").value;
        var pembayaran = document.getElementById("pembayaran").value;
        if (total_seluruh > 0) {
            if (total_seluruh > saldokredit && pembayaran == 'K') {
                swal('PESANAN PENJUALAN', 'Saldo Kredit tidak mencukupi', '');
                document.getElementById("save_btn").disabled = true;
            } else {
                document.getElementById("save_btn").disabled = false;
            }
        }

    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        jumlah = row.cells[1].children[0].value * row.cells[1].children[1].value;
        diskon = (row.cells[1].children[2].value / 100) * jumlah;
        tot = jumlah - diskon;
        row.cells[2].children[1].value = number_format(tot);
        total();

    }

    function infobrg(id) {
        var vid = id.substring(7);
        document.getElementById("infobarang").value = vid;
        var cust = document.getElementById("cust").value;
        var item = document.getElementById("kode" + vid).value;
        var qty = document.getElementById("qty" + vid).value;
        var param = cust + '/' + item + '/' + qty;
        if (item == "") {
            document.getElementById("daftarbarang").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#infobrg').modal('show');
                document.getElementById("daftarbarang").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/pesananmasuk/InfoData/') ?>" + param, true);
        xhttp.send();
    }

    //Show Paket perbarang
    function getPaket(id) {
        var vid = id.substring(15);
        var item = document.getElementById("kode" + vid).value;
        var qty = document.getElementById("qty" + vid).value;
        var param = item + '/' + qty;
        if (item == "") {
            swal('PESANAN PENJUALAN', 'barang tidak boleh kosong ...', '');
        } else {

            if (item == "") {
                document.getElementById("daftarbarangpaket").innerHTML = "";
                return;
            }
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    $('#ViewDetailPaket').modal('show');
                    document.getElementById("daftarbarangpaket").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?= base_url('penjualan/transaksi/pesananmasuk/ShowBarangPaket/') ?>" + param, true);
            xhttp.send();
        }

    }

    function TambahFieldPaket() {
        var table = document.getElementById("DatatablePaket");
        var no = table.rows.length;

        var x = document.getElementById('DatatablePaket').insertRow(no);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);

        var akun = "<span id=NamaBarangPaket" + no + "></span>";
        td1.innerHTML = akun;
        td2.innerHTML = "<span id=QtyPaket" + no + "></span>";
        td3.innerHTML = "<span id=SatuanPaket" + no + "></span>";
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PESANAN PENJUALAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PESANAN PENJUALAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Save Data PESANAN PENJUALAN
    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=id]').val();
        var tanggal = $('[name="tanggal"]').val();
        var nomor = $('[name="nomorbukti"]').val();
        var cust = $('[name="cust"]').val();
        var total_seluruh = parseInt(document.getElementById("_h_total").value);
        var saldokredit = parseInt(document.getElementById("saldokredit").value);
        var pembayaran = document.getElementById("pembayaran").value;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var Status = '<?= $data->statusid ?>';

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];
            kode = row.cells[0].children[0].value;
            qty = row.cells[1].children[0].value;
            x = i - 1;
            if (Status == 1) {
                if (kode == "" || qty == "") {
                    swal('PESANAN PENJUALAN', 'qty di baris ke ' + i + ' tidak boleh kosong...', '');
                    $('#save_btn').attr('disabled', true);
                }
            } else if (Status == "") {
                if (kode == "" || qty == "") {
                    swal('PESANAN PENJUALAN', 'qty di baris ke ' + i + ' tidak boleh kosong...', '');
                    $('#save_btn').attr('disabled', true);
                }
            }
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesananmasuk/syncdata/') ?>" + kode + "/" + cust,
                type: "GET",
                dataType: "JSON",
                success: function(data) {

                    if (data == null) {
                        swal('PESANAN PENJUALAN', 'cek kembali harga barang belum tersedia...', '');
                        $('#save_btn').attr('disabled', true);
                    }
                }
            });
        }

        if (nomor == "" || cust == "") {
            swal('PESANAN PENJUALAN', 'Customer Atau Nobukti tidak terbaca, silakan reload ulang..', '');
        }else if(total_seluruh > saldokredit && pembayaran == 'K'){
            swal('PESANAN PENJUALAN', 'Saldo Kredit tidak mencukupi', '');
            document.getElementById("save_btn").disabled = true;
        } else if ($('#save_btn').prop('disabled') == true) {

        } else {

            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesananmasuksave') ?>",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(res) {
                    $('#process').css('display', 'none');
                    if (res.status == 1) {
                        document.getElementById("save_btn").disabled = true;
                        swal({
                            title: "PESANAN PENJUALAN",
                            html: "<p> No. BUKTI   : <b>" + res.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then(function(value) {
                            window.location.href = "<?= base_url('penjualan/transaksi/pesananmasuk') ?>";
                        });
                    } else {
                        swal('PESANAN PENJUALAN', 'Data gagal disimpan ... ' + res.message, '');
                    }

                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PESANAN PENJUALAN', 'Data gagal disimpan ... ', '');
                }
            });
        }

    });

    function _urlcetak() {
        var baseurl = "<?php echo base_url() ?>";
        var param = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/pesananmasuksave') ?>",
            type: "post",
            data: $('#submit_form').serialize(),
            dataType: "json",
            beforeSend: function() {
                $('#process').css('display', 'block');
            },
            success: function(res) {
                $('#process').css('display', 'none');
                window.location.href = "<?= base_url('penjualan/transaksi/pesananmasuk') ?>";
                if (res.status == 1) {
                    document.getElementById("save_btn").disabled = true;
                    swal({
                        title: "PESANAN PENJUALAN",
                        html: "<p> No. BUKTI   : <b>" + res.message + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        window.location.href = "<?= base_url('penjualan/transaksi/pesananmasuk') ?>";
                    });
                } else {
                    swal('PESANAN PENJUALAN', 'Data gagal disimpan ... ' + res.message, '');
                }


            },
            error: function(data) {
                $('#process').css('display', 'none');
                swal('PESANAN PENJUALAN', 'Data gagal disimpan ... ', '');
            }
        });
        return baseurl + 'penjualan/transaksi/pesananmasukcetak/' + param;

    }

    function _urlcetak_shipping() {
        var baseurl = "<?php echo base_url() ?>";
        var param = $('[name="nomorbukti"]').val();
        return baseurl + 'penjualan/transaksi/pesananmasukcetakshipping/' + param;

    }
</script>