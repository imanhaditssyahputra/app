<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_pufile')->where('id', $id)->get()->getRow();
    $table_dataD = $db->table('ap_pudetail')->where('kodepu', $table_data->kodepu);
    $table_data_detail = $table_dataD->get()->getResult();
    $table_dataDRow = $table_dataD->get()->getRow();
    $table_data_biaya = $db->table('ap_pubiaya')->where('kodepu', $table_data->kodepu)->get()->getResult();
}
isset($table_data) ? main_header('Pembalian', 'Faktur', 'Daftar Faktur Pembelian', 'View Faktur', 'pembelian/transaksi/fakturpembelian') : main_header('Pembalian', 'Faktur', 'Daftar Faktur Pembelian', 'Entri Faktur', 'pembelian/transaksi/fakturpembelian')
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
            <i class="fa fa-reorder"></i><?php if (isset($table_data)) {
                                                echo 'Data View';
                                            } else {
                                                echo '*Data Baru';
                                            }  ?>
        </div>
    </div>


    <div class="portlet-body form">
        <form id="frmpembelian" class="form-horizontal">
            <input type="hidden" name="id" id="id" value="<?= isset($table_data) ? $table_data->id : 0 ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active fs-5" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Faktur</button>
                            <button class="nav-link fs-5" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Info</button>
                            <button class="nav-link fs-5" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Biaya Lainnya</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Supplier *</label>
                                            <div class="col-md-9">
                                                <select id="supp" name="supp" class="form-select input-medium select2" data-placeholder="Pilih..." required>
                                                    <option value="" class="">---- pilih supplier----</option>
                                                    <?php
                                                    if (isset($table_data)) {
                                                        $suplier = $db->table('ap_supplier')->getWhere(['kode' => $table_data->kodesup])->getResult();
                                                        foreach ($suplier as $key => $value) { ?>
                                                            <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                                    echo "selected=true";
                                                                                                } ?>><?= $value->nama ?></option>
                                                        <?php }
                                                    } else {
                                                        $suplier = $db->table("ap_supplier a")->join("ap_lpb b", 'a.kode = b.kodesup')->getWhere(['b.no_terima' => 2])->getResult();
                                                        foreach ($suplier as $key => $value) {
                                                        ?>
                                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="showpb()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Nomor #</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-medium" name="nomorbukti" readonly id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->kodepu : 'Nomor Auto' ?>" value="<?= isset($table_data) ? $table_data->kodepu : '' ?>">
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
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglpu)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Faktur</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control input-medium" placeholder="" name="nomorfaktur" id="nomorfaktur1" value="<?= isset($table_data) ? $table_data->nomorfaktur : '' ?>">
                                                <input type="hidden" class="form-control input-medium" placeholder="" name="gudang" id="gudang1" value="<?= isset($table_data) ? $table_dataDRow->gudang : '' ?>">
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
                                            <label class="control-label pt-2 col-md-3">No. Terima</label>
                                            <div class="col-md-9">
                                                <?php if (isset($table_data)) { ?>
                                                    <input type="text" name="kodepb" id="kodepb" value="<?= isset($table_data) ? $table_data->kodepb : '' ?>" class="form-control input-medium" readonly>
                                                <?php } else { ?>
                                                    <select name="kodepb" id="kodepb" class="form-select input-medium select2">
                                                        <option value="">---Tanpa PB---</option>
                                                    </select>
                                                    <a class="btn blue" onclick="getpb()"><i class="fa fa-download"></i></a>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-9">
                                                <?php if (isset($table_data)) { ?>
                                                    <input type="text" name="sppn" id="sppn" value="<?= $table_data->sppn == 'Y' ? 'Ya' : 'Tidak' ?>" class="form-control input-medium" readonly>
                                                <?php } else { ?>
                                                    <select name="sppn" id="sppn" class="form-select select2 input-medium" onchange="total()">
                                                        <option value="Y" selected="true">Ya</option>
                                                        <option value="T">Tidak</option>
                                                    </select>
                                                <?php } ?>
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
                                            <label class="control-label pt-2 col-md-3">Pembayaran</label>
                                            <div class="col-md-9">
                                                <?php if (isset($table_data)) { ?>
                                                    <input type="text" name="pembayaran" id="pembayaran" value="<?= $table_data->pembayaran == 'T' ? 'Tunai' : 'Kredit' ?>" class="form-control input-medium" readonly>
                                                <?php } else { ?>
                                                    <select name="pembayaran" id="pembayaran" class="form-select select2 input-medium" onchange="total()">
                                                        <option value="">Pilih</option>
                                                        <option value="T">Tunai</option>
                                                        <option value="K">Kredit</option>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No Faktur Pajak</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control input-medium" name="no-pajak" value="<?= isset($table_data) ? $table_data->nomorfaktur_pajak : '' ?>">
                                                <input type="hidden" id="no-po1" class="form-control" name="no-po" value="<?= isset($table_data) ? $table_data->kodepo : '' ?>" readonly>
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
                                                <tr>
                                                    <th style="text-align: center">Nama Barang</th>
                                                    <th style="text-align: center">Kuantitas</th>
                                                    <th style="text-align: center">Satuan</th>
                                                    <th style="text-align: center">No Batch</th>
                                                    <th style="text-align: center">Harga</th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center">Diskon</th>
                                                    <th style="text-align: center">Total Harga</th>
                                                </tr>
                                                <thead>
                                                <tbody>
                                                    <?php
                                                    if (isset($table_data_detail)) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang' and kdgudang='$value->gudang'")->getRow();
                                                            $no++;
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $no ?>" readonly class="form-select input-xlarge">
                                                                        <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                    </select>
                                                                </td>

                                                                <td><input name="qty[]" readonly value="<?= $value->qtypu; ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small"></td>
                                                                <td><input name="sat[]" id="sat<?= $no ?>" type="text" class="form-control input-xsmall" value="<?= $value->satuan; ?>"></td>
                                                                <td>
                                                                    <input name="no_batch[]" id="no_batch<?= $no ?>" type="text" class="form-control input-small" value="<?= $value->no_batch; ?>" readonly>
                                                                    <input name="tgl_exp[]" id="tgl_exp<?= $no ?>" type="hidden" class="form-control input-small" value="<?= $value->tanggal_exp; ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input name="harga[]" value="<?= $value->hargabeli; ?>" id="harga<?= $no ?>" type="text" class="form-control rightJustified input-small" <?= $akses->nomor_grup != 1 ? 'readonly' : '' ?>>
                                                                </td>
                                                                <td><a class="btn default" id="lupharga<?= $no ?>" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                                <td><input name="disc[]" value="<?= $value->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified input-xsmall"></td>
                                                                <td><input name="jumlah[]" id="jumlah<?= $no ?>" ; type="text" class="form-control rightJustified input-small" readonly></td>
                                                            </tr>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    totalline(<?= $no ?>)
                                                                });
                                                            </script>
                                                        <?php }
                                                    } else {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode1" readonly class="form-select input-xlarge">
                                                                    <option value="">--- Pilih Barang ---</option>
                                                                </select>
                                                            </td>

                                                            <td><input name="qty[]" onchange="totalline(1)" readonly value="0" id="qty1" type="text" class="form-control input-small rightJustified"></td>
                                                            <td><input name="sat[]" id="sat1" type="text" class="form-control input-xsmall" onkeypress="return tabE(this,event)"></td>
                                                            <td>
                                                                <input name="no_batch[]" id="no_batch1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)" readonly>
                                                                <input name="tgl_exp[]" id="tgl_exp1" type="hidden" class="form-control input-small" readonly>

                                                            </td>
                                                            <td>
                                                                <input name="harga[]" harga onchange="totalline(1)" value="0" id="harga1" type="text" class="form-control rightJustified input-small" <?= $akses->nomor_grup != 1 ? 'readonly' : '' ?>>
                                                            </td>
                                                            <td><a class="btn default" id="lupharga1" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                            <td><input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control rightJustified input-xsmall"></td>
                                                            <td><input name="jumlah[]" id="jumlah1" ; type="text" class="form-control rightJustified input-small" onchange="total()" readonly></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <div class="wells">
                                                    <!-- <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button> -->
                                                    <!-- <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Kirim</label>
                                            <div class="col-md-9">
                                                <div class="input-icon">
                                                    <i class="fa fa-calendar"></i>
                                                    <input id="tanggalkirim" name="tanggalkirim" class="form-control date-picker input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglkirim)) : date('Y-m-d') ?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->ket : '' ?>">
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
                                            <label class="control-label pt-2 col-md-3">Alamat</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" name="alamat" id="alamat"><?= isset($table_data) ? $table_data->alamat1 : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                            <div class="tab-pane fade show" id="tab3">
                                <!-- First Row -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="datatable2" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: center">#Kode/ Nama Biaya</th>
                                                        <th style="text-align: center">Jumlah</th>
                                                        <th style="text-align: center">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (isset($table_data_biaya) && $table_data->biayalain != 0) {
                                                        $no = 0;
                                                        foreach ($table_data_biaya as $value) {
                                                            $db = db_connect();
                                                            $akun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->kodeakun'")->getRow();
                                                            $no++;
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="lkode[]" id="lkode<?= $no ?>" class="selectakun form-select input-xlarge">
                                                                        <option value="<?= $value->kodeakun; ?>"><?= $akun->namaakun; ?></option>
                                                                    </select>
                                                                </td>
                                                                <td><input name="ljumlah[]" id="ljumlah<?= $no ?>" type="text" class="form-control rightJustified input-medium" onchange="total()" value="0"></td>
                                                                <td><input name="lket[]" id="lket<?= $no ?>" type="text" class="form-control input-large" onkeypress="return tabE(this,event)"></td>

                                                            </tr>
                                                        <?php }
                                                    } else { ?>
                                                        <tr>
                                                            <td>
                                                                <select name="lkode[]" id="lkode1" class="selectakun form-select input-xlarge">
                                                                    <option value="">--- Pilih Akun ---</option>
                                                                </select>
                                                            </td>
                                                            <td><input name="ljumlah[]" id="ljumlah1" type="text" class="form-control rightJustified input-medium" onchange="total()" value="0"></td>
                                                            <td><input name="lket[]" id="lket1" type="text" class="form-control input-large" onkeypress="return tabE(this,event)"></td>

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
                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="wells">
                                <?php if (!isset($table_data)) { ?>
                                    <button type="button" id="save_btn" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                <?php } else { ?>
                                    <a class="btn yellow print_laporan" onclick="javascript:window.open(_urlcetak(),'_blank');"><i class="fa fa-print"></i>
                                        Cetak</a>
                                <?php } ?>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-pencil-square"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>

                        <div class="col-md-4 invoice-block">
                            <div class="well">
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
                                        <td width="59" align="right"><strong><span id="_vbiayalain"></span></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">
                                            <strong>TOTAL</strong>
                                            <input type="hidden" name="vtotal" id="_vvtotal">
                                        </td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vtotal"></span></strong></td>
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

<script>
    var cabang = "<?= $cabang ?>";
    var idrow = 2;
    var idrow2 = 2;

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

        var akun = "<select name='kode[]' id=kode" + idrow + " class='form-select input-largex' readonly><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]'  readonly  id=qty" + idrow + " onchange='totalline(" + idrow +
            ")' value='1'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' >";
        td4.innerHTML = "<input name='no_batch[]' readonly id=no_batch" + idrow + " type='text' class='form-control' > <input name='tgl_exp[]' readonly id=tgl_exp" + idrow + " type='hidden' class='form-control' >";
        td5.innerHTML = "<input name='harga[]' id=harga" + idrow + " onchange='totalline(" + idrow + ")' onchange='totalline(" + idrow +
            ") value='0'  type='text' class='form-control rightJustified' <?= $akses->nomor_grup != 1 ? 'readonly' : '' ?>>";
        td6.innerHTML = "<a class='btn default' id=lupharga" + idrow +
            " data-toggle='modal' href='#lupharga' onclick='getidharga(this.id)'><i class='fa fa-search'></i></a>";
        td7.innerHTML = "<input name='disc[]'   id=disc" + idrow + " onchange='totalline(" + idrow +
            ")' value='0'  type='text' class='form-control rightJustified'  >";
        td8.innerHTML = "<input name='jumlah[]' id=jumlah" + idrow +
            " type='text' class='form-control rightJustified' size='40%' readonly>";
        idrow++;
    }

    function tambah2() {
        var x = document.getElementById('datatable2').insertRow(idrow2);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);

        var akun =
            "<select name='lkode[]' class='select2_el form-control' ><option value=''>--- Pilih Akun ---</option></select>";

        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ljumlah[]' id=ljumlah" + idrow2 + " onchange='totalline(" + idrow2 +
            ")' value='0'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='lket[]'    id=lket" + idrow2 + " type='text' class='form-control' >";
        initailizeSelect2();

        idrow2++;
    }

    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        document.getElementById("datatable2").deleteRow(i);
    }

    //show data PB Pembelian
    function showpb() {
        var xhttp;
        var str = $('[name="supp"]').val();

        if (str == "") {
            document.getElementById("kodepb").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodepb").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('pembelian/transaksi/fakturpembelian/getlistpb/') ?>" + str, true);
        xhttp.send();
    }

    function getpb() {
        var xhttp;
        var str = $('[name=kodepb]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/fakturpembelian/postpb/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {

                    for (i = 0; i <= data.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;
                        var barang = $("<option selected></option>").val(data[i].kodeitem).text(data[i].namabarang);
                        $('#kode' + x).append(barang).trigger('change');

                        document.getElementById("qty" + x).value = data[i].qtyterima;
                        document.getElementById("sat" + x).value = data[i].satuan;
                        document.getElementById("no_batch" + x).value = data[i].no_batch;
                        document.getElementById("tgl_exp" + x).value = data[i].tanggal_exp;
                        document.getElementById("harga" + x).value = number_format(data[i].hargabeli, 2);
                        document.getElementById("disc" + x).value = data[i].disc;
                        document.getElementById("no-po1").value = data[i].kodepo;
                        document.getElementById("gudang1").value = data[i].gudang;
                        document.getElementById("nomorfaktur1").value = data[i].noterima;
                        //Header
                        $('[name="keterangan"]').val(data[i].keterangan);
                        $('[name="alamat"]').val(data[i].alamat);
                        totalline(x);

                    }

                }
            });
        }
    }

    function total() {
        <?php if (isset($table_data)) { ?>
            var nomorbukti = '<?= $table_data->kodepu ?>';
            var nomorbukti_ppn = '<?= $table_data->kodepu ?>';
        <?php } else { ?>
            var nomorbukti = '<?= $nomor ?>';
            var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        <?php } ?>
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            jumlah = row.cells[1].children[0].value;
            harga = row.cells[4].children[0].value;
            diskon = row.cells[6].children[0].value;
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
        if (cppn == "Y" || cppn == "Ya") {
            tppn = (tjumlah - tdiskon) * 0.11;
            $('#nomorbukti').val(nomorbukti_ppn);
        } else {
            tppn = 0;
            $('#nomorbukti').val(nomorbukti);
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

    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        tjumlah = 0;
        tdiskon = 0;

        jumlah = row.cells[1].children[0].value;
        harga = row.cells[4].children[0].value;
        diskon = row.cells[6].children[0].value;
        jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
        harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));
        diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));
        tjumlah = tjumlah + eval(jumlah1 * harga1);
        diskon = eval((diskon1 / 100) * jumlah1 * harga1);
        tdiskon = tdiskon + diskon;
        row.cells[7].children[0].value = number_format(tjumlah - tdiskon);
        total();

    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('FAKTUR PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('FAKTUR PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Save Data
    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var nofaktur = $('[name="nomorfaktur"]').val();
        var bayar = $('#top').val();
        var stsbayar = $('#pembayaran').val();
        var tanggal = $('[name="tanggal"]').val();
        if (nofaktur == "" || bayar == "" || stsbayar == "") {
            swal('FAKTUR PEMBELIAN', 'Data Belum Lengkap/Belum ada transaksi ...', '');
        } else if (noform == "") {
            swal('FAKTUR PEMBELIAN', 'Nomor Bukti tidak ditemukan ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/fakturpembeliansave') ?>",
                type: "post",
                data: $('#frmpembelian').serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    if (data.status == 1) {
                        $('#save_btn').attr('disabled', true);
                        swal({
                            title: "FAKTUR PEMBELIAN",
                            html: "<p> No. Faktur   : <b>" + nofaktur + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then(function(value) {
                            //window.open(_urlcetak(data));
                            window.location.href = "<?= base_url('pembelian/transaksi/fakturpembelian') ?>";
                        });
                    } else {
                        swal('FAKTUR PEMBELIAN', 'Data gagal disimpan ... ' + data.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('FAKTUR PEMBELIAN', 'Data gagal disimpan ...' + data.message, '');
                }
            });
        }

    }

    function _urlcetak() {
        var baseurl = "<?php echo base_url() ?>";
        var param = $('[name="id"]').val();
        return baseurl + 'pembelian/transaksi/fakturpembeliancetak/' + param;
    }
</script>