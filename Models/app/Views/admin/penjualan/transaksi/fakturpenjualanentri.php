<?php
$db = db_connect();
if ($data != []) {
    main_header('Penjualan', 'Faktur', 'Daftar Faktur Penjualan', 'View Faktur', 'penjualan/transaksi/fakturpenjualan');
    $DataOrder = 'Data View';
    $CSRow = $db->table("ar_customer")->getWhere(['kode' => $data->kodecust])->getRow();
} else {
    main_header('Penjualan', 'Faktur', 'Daftar Faktur Penjualan', 'Entri Faktur', 'penjualan/transaksi/fakturpenjualan');
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
        <div class="caption">
            <i class="fa fa-reorder"></i>*<?= $DataOrder ?>
        </div>
    </div>

    <div class="portlet-body form">
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <input type="hidden" value="<?= ($data != []) ? $data->id : 0 ?>" name="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Faktur</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Biaya Lainnya</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Pelanggan <font color="red">*</font></label>
                                            <div class="col-md-9">
                                                <select id="cust" name="cust" class="form-control input-medium select2" <?= ($data != []) ? 'disabled' : '' ?> data-placeholder="Pilih..." onchange="getcs()">
                                                    <option>---Pilih Customer</option>
                                                    <?php if ($data != []) {
                                                        foreach ($arCust as $key) { ?>
                                                            <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kodecust) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                        <?php }
                                                    } else {
                                                        foreach ($res as $key) {  ?>
                                                            <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kodecust) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="showsd()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">No. Faktur # <font color="red">*</font></label>
                                            <div class="col-md-7">
                                                <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= ($data != []) ? $data->kodesi : '' ?>" readonly>
                                                <input type="text" class="form-control input-medium" readonly placeholder="Nomor Auto" value="<?= ($data != []) ? $data->kodesi : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal <font color="red">*</font></label>
                                            <div class="col-md-6">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tglsi)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">No. Kirim</label>
                                            <div class="col-md-7">
                                                <select name="kodesd" id="kodesd" class="form-control input-medium select2" <?= ($data != []) ? 'disabled' : '' ?> onchange="getHeader()">
                                                    <option value="">---Tanpa SD---</option>
                                                    <?php if ($data != []) { ?>
                                                        <option value="<?= $data->kodesd ?>" selected><?= $data->kodesd ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn blue" onclick="getsd()"><i class="fa fa-download"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Third Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-6">
                                                <input type="hidden" id="sppn" name="sppn" class="form-control" value="<?= ($data != []) ? (($data->sppn == 'Y') ? 'Ya' : 'Tidak') : ''  ?> " readonly>
                                                <input type="text" id="sppnmiror" name="sppnmiror" class="form-control" value="<?= ($data != []) ? (($data->sppn == 'Y') ? 'Ya' : 'Tidak') : ''  ?> " readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">No Faktur Pajak</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" value="<?= ($data != []) ? $data->faktur_pajak : $NoFakturPajak ?>" name="no-pajak" <?= ($data != []) ? 'readonly' : '' ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Four Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No Pesanan</label>
                                            <div class="col-md-6">
                                                <input type="text" id="no-po" class="form-control" name="no-po" value="<?= ($data != []) ? $data->kodeso : '' ?>" readonly>
                                                <input type="hidden" id="kodepo" class="form-control" value="<?= ($data != []) ? $data->kodeso : '' ?> " name="kodepo" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Pembayaran</label>
                                            <div class="col-md-7">
                                                <input type="hidden" id="pembayaran" name="pembayaran" class="form-control" value="<?= ($data != []) ? (($data->pembayaran == 'T') ? 'Tunai' : 'Kredit') : ''  ?> " readonly>
                                                <input type="text" id="pembayaranmiror" name="pembayaranmiror" class="form-control" value="<?= ($data != []) ? (($data->pembayaran == 'T') ? 'Tunai' : 'Kredit') : ''  ?> " readonly>
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
                                                <th style="text-align: center"></th>
                                                <th style="text-align: center">Diskon</th>
                                                <th style="text-align: center">Total Harga</th>
                                            </thead>

                                            <tbody>
                                                <?php if ($DetailItem != []) {
                                                    $no = 0;
                                                    foreach ($DetailItem as $key) {
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $Hpercust = $db->query("SELECT * FROM inv_barang_harga where kodeitem='$key->kodeitem' and cabang='$cabang' and kode_customer='$data->kodecust'")->getRow();
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode<?= $no ?>" class=" form-control input-xlarge">
                                                                    <option value="<?= $key->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                                <input name="no_batch[]" value="<?= $key->no_batch; ?>" id="no_batch<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                <input name="tgl_exp[]" value="<?= $key->tanggal_exp; ?>" id="tgl_exp<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td><input name="qty[]" readonly value="<?= $key->qtysi; ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified"></td>
                                                            <td>
                                                                <input name="sat[]" readonly id="sat<?= $no ?>" type="text" class="form-control" value="<?= $key->satuan; ?>">
                                                            </td>
                                                            <td>
                                                                <input name="harga[]" value="<?= $key->hargajual; ?>" id="harga<?= $no ?>" type="text" readonly class="form-control rightJustified">
                                                                <input name="cn1[]" value="<?= $key->fee1_rp; ?>" id="a-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                <input name="hargabruto[]" placeholder='Harga Percust' id="hargabruto<?= $no ?>" value="<?= $Hpercust != null ? $Hpercust->bruto_cust : ''; ?>" type="text" readonly class="form-control rightJustified">
                                                            </td>
                                                            <td><a class="btn default" id="lupharga<?= $no ?>" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                            <td>
                                                                <input name="disc[]" readonly value="<?= $key->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified ">
                                                                <input name="cn2[]" value="<?= $key->fee2_rp; ?>" id="b-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="jumlah[]" readonly id="jumlah<?= $no ?>" ; type="text" class="form-control rightJustified" size="40%">
                                                                <input name="cn3[]" value="<?= $key->fee3_rp; ?>" id="c-cn<?= $no ?>" type="hidden" class="form-control rightJustified">
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
                                                            <select name="kode[]" id="kode1" class=" form-control input-xlarge">
                                                                <option value="">--- Pilih Barang ---</option>
                                                            </select>
                                                            <input name="no_batch[]" onchange="totalline(1)" value="" id="no_batch1" type="hidden" class="form-control  rightJustified">
                                                            <input name="tgl_exp[]" onchange="totalline(1)" value="" id="tgl_exp1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td><input name="qty[]" readonly onchange="totalline(1);total()" value="1" id="qty1" type="text" class="form-control input-small rightJustified"></td>
                                                        <td>
                                                            <input name="sat[]" readonly id="sat1" type="text" class="form-control input-xsmall" onkeypress="return tabE(this,event)">
                                                        </td>
                                                        <td>
                                                            <input name="harga[]" onchange="totalline(1)" value="0" id="harga1" type="text" readonly class="form-control input-small rightJustified">
                                                            <input name="cn1[]" onchange="totalline(1)" value="0" id="a-cn1" type="hidden" class="form-control rightJustified">
                                                            <input name="hargabruto[]" placeholder='Harga Percust' id="hargabruto1" type="text" readonly class="form-control input-small rightJustified">
                                                        </td>
                                                        <td>
                                                            <a class="btn default" id="lupharga1" data-toggle="modal" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a>
                                                        </td>
                                                        <td>
                                                            <input name="disc[]" readonly onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control input-xsmall rightJustified ">
                                                            <input name="cn2[]" onchange="totalline(1)" value="0" id="b-cn1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="jumlah[]" readonly id="jumlah1" ; type="text" class="form-control input-small rightJustified" onchange="total()">
                                                            <input name="cn3[]" onchange="totalline(1)" value="0" id="c-cn1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-9">
                                            <div class="wells">
                                                <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button>
                                                <button type="button" onclick="deleteLastRow('datatable')" class="btn red"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Kirim</label>
                                            <div class="col-md-9">
                                                <div class="input-icon">
                                                    <i class="fa fa-calendar"></i>
                                                    <input id="tanggalkirim" name="tanggalkirim" class="form-control date-picker input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tglkirim)) : date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Alamat</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" name="alamat" id="alamat"><?= ($data != []) ? $data->alamat1 : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Sales</label>
                                            <div class="col-md-7">
                                                <select id="sales1" name="sales" class="form-control input-medium select2" data-placeholder="Pilih...">
                                                    <option>---- Pilih -----</option>
                                                    <?php if ($data != []) { ?>
                                                        <option value="<?= $data->id_sales ?>" selected><?= $data->nama_sales ?></option>
                                                    <?php } ?>
                                                </select>
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
                                            <tbody>
                                                <?php if ($BiayaLain != []) {
                                                    $no = 0;
                                                    foreach ($BiayaLain as $value) {
                                                        $akun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->kodeakun'")->getRow();
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <select name="lkode[]" id="lkode<?= $no ?>" class="selectakun form-control input-xlarge">
                                                                    <option value="<?= $value->kodeakun; ?>"><?= $akun->namaakun; ?></option>
                                                                </select>
                                                            </td>
                                                            <td><input name="ljumlah[]" id="ljumlah1" type="text" class="form-control input-medium rightJustified" value="<?= $value->jumlah; ?>"></td>
                                                            <td><input name="lket[]" id="lket1" type="text" class="form-control input-medium" value="<?= $value->keterangan; ?>"></td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name="lkode[]" id="lkode1" class="selectakun form-control input-xlarge">
                                                                <option value="">--- Pilih Akun ---</option>
                                                            </select>
                                                        </td>
                                                        <td><input name="ljumlah[]" id="ljumlah1" type="text" class="form-control input-medium rightJustified" onchange="total()" value="0"></td>
                                                        <td><input name="lket[]" id="lket1" type="text" class="form-control input-medium " onkeypress="return tabE(this,event)"></td>
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
                                        <button type="button" onclick="deleteLastRow('datatable2')" class="btn red"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="wells">
                                <?php if ($data == []) { ?>
                                    <button id="btnsimpan" type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                    <?php } else {
                                    if ($PrintFaktur == 1) { ?>
                                        <a class="btn blue print_laporan" onclick="javascript:window.open(_urlcetaksj('<?= $data->kodesi ?>'),'_blank');"><i class="fa fa-print"></i> Delivery Order</a>
                                        <a class="btn yellow print_laporan" onclick="javascript:window.open(_urlcetak('<?= $data->kodesi ?>'),'_blank');"><i class="fa fa-print"></i> Cetak Faktur</a>
                                <?php }
                                } ?>

                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>
                        <div class="col-md-4 invoice-block">
                            <div class="well p-3">
                                <table>
                                    <tr>
                                        <td width="40%"><strong>SUB TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vsubtotal"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>DISKON</strong>
                                            <input type="hidden" name="vdiskon" id="_vvdiskon">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vdiskon"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>DPP</strong>
                                            <input type="hidden" name="vdpp" id="_vvdpp">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_dpp"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>PPN</strong>
                                            <input type="hidden" name="vppn" id="_vvppn">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vppn"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>BIAYA LAIN</strong>
                                            <input type="hidden" name="vbiayalain" id="_vvbiayalain">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vbiayalain"></span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>TOTAL</strong>
                                            <input type="hidden" name="vtotal" id="_vvtotal">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vtotal"></span></strong></td>
                                    </tr>
                                    <input type="hidden" id="tersimpan">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    var cbg = '<?= $cabang ?>';
    var idrow = 2;
    var idrow2 = 2;

    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrow);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var td6 = x.insertCell(5);
        var td7 = x.insertCell(6);

        var akun = "<select name='kode[]' id=kode" + idrowx + " class='form-control' ><option value=''>--- Pilih Barang ---</option></select> <input name='no_batch[]' readonly   id=no_batch" + idrowx + " onchange='totalline(" + idrowx + ")' value=''  type='hidden' class='form-control rightJustified'  > <input name='tgl_exp[]' readonly   id=tgl_exp" + idrowx + " onchange='totalline(" + idrowx + ")' value=''  type='hidden' class='form-control rightJustified'  >";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]' readonly   id=qty" + idrowx + " onchange='totalline(" + idrowx + ")' value='1'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='sat[]'  readonly  id=sat" + idrowx + " type='text' class='form-control' >";
        td4.innerHTML = "<input name='harga[]' readonly id=harga" + idrowx + " oninput='totalline(" + idrowx + ") value='0'  type='text'  class='form-control rightJustified'> <input name='cn1[]'  id=a-cn" + idrowx + " onchange='totalline(" + idrowx + ") value='0'  type='hidden' class='form-control rightJustified'>   <input name='hargabruto[]'  id=hargabruto" + idrowx +
            " placeholder='Harga Percust'  type='text' readonly class='form-control rightJustified'>";
        td5.innerHTML = "<a class='btn default' id=lupharga" + idrowx + " data-toggle='modal' onclick='getidharga(this.id)'><i class='fa fa-search'></i></a>";
        td6.innerHTML = "<input name='disc[]'  readonly id=disc" + idrowx + " onchange='totalline(" + idrowx + ")' value='0'  type='text' class='form-control rightJustified'  > <input name='cn2[]'  id=b-cn" + idrowx + " onchange='totalline(" + idrowx + ") value='0'  type='hidden' class='form-control rightJustified'>";
        td7.innerHTML = "<input name='jumlah[]' readonly id=jumlah" + idrowx + " type='text' class='form-control rightJustified' size='40%'> <input name='cn3[]'  id=c-cn" + idrowx + " onchange='totalline(" + idrowx + ") value='0'  type='hidden' class='form-control rightJustified'>";
        initailizeSelect2_barang_filterCabang(cbg);
        idrow++;
    }

    function tambah2() {
        var table = document.getElementById("datatable2");
        var idrowx2 = table.rows.length;
        var x = document.getElementById('datatable2').insertRow(idrowx2);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);

        var akun = "<select name='lkode[]' id=lkode" + idrowx2 + " class='select2_el form-control' ><option value=''>--- Pilih Akun ---</option></select>";

        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ljumlah[]' id=ljumlah" + idrowx2 + " onchange='totalline(" + idrowx2 + ")' value='0'  onchange='total()' type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='lket[]'    id=lket" + idrowx2 + " type='text' class='form-control' >";
        initailizeSelect2();
        idrow2++;
    }

    function showsd() {
        var xhttp;
        var str = $('[name="cust"]').val();

        if (str == "") {
            document.getElementById("kodesd").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodesd").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/fakturpenjualan/showsd/') ?>" + str, true);
        xhttp.send();
    }

    function getcs() {
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var str = $('[name=cust]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/fakturpenjualan/getcs/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    var option2 = $("<option selected></option>").val(data.kodesales).text(data.nama_sales);
                    $('#sales1').append(option2).trigger('change');

                    if (data.top == null) {
                        swal('FAKTUR PENJUALAN', 'TOP null harap di isi di data customer ...', '');
                    }
                    $('#pembayaran').val(data.kredit);
                    $('#sppn').val(data.ppn);
                    var optionBayar = data.kredit == 'T' ? 'Tunai' : 'Kredit';
                    var optionPPN = data.ppn == 'Y' ? 'Ya' : 'Tidak';
                    $('#pembayaranmiror').val(optionBayar);
                    $('#sppnmiror').val(optionPPN);
                    if (data.ppn == "") {
                        swal('Penjualan Faktur', 'PPN tidak terdeteksi silahkan reload', '');
                    }
                    data.ppn == 'Y' ? $('#nomorbukti').val(nomorbukti_ppn) : $('#nomorbukti').val(nomorbukti);

                }
            });
        }
    }

    function getHeader() {
        var str = $('[name=kodesd]').val();
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/fakturpenjualan/getsd3/') ?>" + str,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                for (i = 0; i <= data.length - 1; i++) {
                    x = i + 1;
                    document.getElementById("kodepo").value = data[i].kodeso;
                    document.getElementById("no-po").value = data[i].no_po;
                    document.getElementById("keterangan").value = data[i].keterangan;
                }
            }
        });
    }

    function getsd() {
        var xhttp;
        var cust = $('[name="cust"]').val();
        var str = $('[name=kodesd]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/fakturpenjualan/getsd/') ?>" + str + '/' + cust,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    for (i = 0; i <= data.DataOne.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;
                        var option = $("<option selected></option>").val(data.DataOne[i].kodeitem).text(data.DataOne[i].namabarang);
                        $('#kode' + x).append(option).trigger('change');

                        document.getElementById("no_batch" + x).value = data.DataOne[i].no_batch;
                        document.getElementById("tgl_exp" + x).value = data.DataOne[i].tgl_exp;
                        document.getElementById("qty" + x).value = data.DataOne[i].qtykirim;
                        document.getElementById("sat" + x).value = data.DataOne[i].satuan;
                    }
                    for (i = 0; i <= data.DataTwo.length - 1; i++) {
                        x = i + 1;
                        var hargaFaktur = document.getElementById("harga" + x).value = data.DataTwo[i].bruto_cust;
                        document.getElementById("disc" + x).value = data.DataTwo[i].diskon_pr;
                        var hargapercust = document.getElementById("hargabruto" + x).value = data.DataTwo[i].bruto_cust;
                        if (data.DataTwo[i].bruto_cust == 0) {
                            swal('Penjualan Faktur', 'Harga Tidak Boleh 0', '');
                            document.getElementById("btnsimpan").disabled = true;
                        }
                        if (hargaFaktur != hargapercust) {
                            swal('Penjualan Faktur', 'Harga Tidak sesuai dengan harga percust', '');
                            document.getElementById("btnsimpan").disabled = true;
                        }
                        totalline(x);
                    }
                }
            });
        }
    }

    function total() {

        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            jumlah = row.cells[1].children[0].value;
            harga = row.cells[3].children[0].value;
            diskon = row.cells[5].children[0].value;
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
        if (cppn == "Y" || cppn.trim() == "Ya") {
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

        document.getElementById("_vvdiskon").value = number_format(tdiskon);
        document.getElementById("_vvdpp").value = number_format(tjumlah - tdiskon);
        document.getElementById("_vvbiayalain").value = number_format(tbiaya);
        document.getElementById("_vvppn").value = number_format(tppn);
        document.getElementById("_vvtotal").value = number_format(tjumlah - tdiskon + tbiaya + tppn);

        if (tjumlah > 0) {
            document.getElementById("btnsimpan").disabled = false;
        } else {
            document.getElementById("btnsimpan").disabled = true;
        }

    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        jumlah = row.cells[1].children[0].value * row.cells[3].children[0].value;
        diskon = (row.cells[5].children[0].value / 100) * jumlah;
        tot = jumlah - diskon;
        row.cells[6].children[0].value = number_format(tot);
        total();

    }

    function getidharga(id) {
        var vid = id.substring(8);
        document.getElementById("nopilihharga").value = vid;
        var cust = document.getElementById("cust").value;
        var item = document.getElementById("kode" + vid).value;
        var param = cust + '~' + item;
        var xhttp;
        if (cust == "" || item == "") {
            document.getElementById("dafhargabeli").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupharga').modal('show');

                document.getElementById("dafhargabeli").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('gethargacs/') ?>" + cust + "/" + item, true);
        xhttp.send();
    }

    //post harga
    function post_harga(v1, v2, v3, v4) {

        id = document.getElementById("nopilihharga").value;
        var kode = $('#kode' + id).val();
        var item = v1.trim();
        if (item == kode) {
            document.getElementById("harga" + id).value = v3;
            document.getElementById("hargabruto" + id).value = v3;
            document.getElementById("disc" + id).value = v4;
            $('#lupharga').modal('hide');
            totalline(id);
        } else {
            swal('PENJUALAN FAKTUR', 'barang tidak Sesuai  ...', '');
        }
    }

    $("#btnsimpan").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('FAKTUR PENJUALAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('FAKTUR PENJUALAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var tanggal = $('[name="tanggal"]').val();
        var nomor = $('[name="nomorbukti"]').val();
        var total = $('#_vtotal').text();
        var kodesd = $('[name="kodesd"]').val();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            harga = row.cells[3].children[0].value;
            hargabruto = row.cells[3].children[2].value;

            var a = Number(harga.replace(/[^0-9\.]+/g, ""));

            var b = Number(hargabruto.replace(/[^0-9\.]+/g, ""));
            if (a != b) {
                swal('FAKTUR PENJUALAN', 'Harga di baris ke ' + i + ' tidak Sesuai dengan Harga percust ...', '');
                $('#btnsimpan').attr('disabled', true);
            }
        }

        if (nomor == "" || kodesd == "") {
            swal('PENJUALAN', 'Data Belum Lengkap/Belum ada transaksi ...', '');
        } else if ($('#btnsimpan').prop('disabled') == true) {

        } else {
            document.getElementById("btnsimpan").disabled = true;
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/fakturpenjualan/save') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    var parse = JSON.parse(data);

                    if (parse.status == 1) {
                        document.getElementById("btnsimpan").disabled = true;
                        swal({
                            title: "FAKTUR PENJUALAN",
                            html: "<p> No. Bukti   : <b>" + parse.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.open(_urlcetak(parse.message));
                        });
                    } else {
                        swal('FAKTUR PENJUALAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }

                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('FAKTUR PENJUALAN', 'Data gagal disimpan ... ', '');
                }
            });
        }
    }

    function _urlcetak(data) {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'penjualan/transaksi/fakturpenjualancetak/' + data;
    }

    function _urlcetaksj(data) {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'penjualan/transaksi/fakturpenjualancetaksj/' + data;
    }
</script>
<!-- Modal Harga -->
<div class="modal fade" id="lupharga" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihharga">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory Nobatch/ED</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="dafhargabeli">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" onclick="reloadBatch()" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
                </div>
        </div>
    </div>
</div>