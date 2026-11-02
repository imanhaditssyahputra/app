<?php
error_reporting(0);
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_pofile')->where('id', $id)->get()->getRow();
    $table_data_detail = $db->table('ap_podetail')->where('kodepo', $table_data->kodepo)->get()->getResult();
    $table_data_biaya = $db->table('ap_pobiaya')->where('kodepo', $table_data->kodepo)->get()->getResult();
}
?>
<?php if (isset($table_data) && $table_data->statusid == 3) {
    main_header('Pembelian ', ' View Pesanan Pembelian', '', '', 'pembelian/transaksi/pesananpembelian');
} else if (isset($table_data) && $table_data->statusid == 1) {
    main_header('Pembelian ', ' Edit Pesanan Pembelian', '', '', 'pembelian/transaksi/pesananpembelian');
} else {
    main_header('Pembelian ', ' Entri Pesanan Pembelian', '', '', 'pembelian/transaksi/pesananpembelian');
} ?>
<?= main_periode('Daftar Pesanan Pembelian'); ?>

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
            <i class="fa fa-reorder"></i>
            <?php
            if (isset($table_data) && $table_data->statusid == 3) {
                echo 'Data View';
            } elseif (isset($table_data) && $table_data->statusid == 1) {
                echo 'Data Edit';
            } else {
                echo '*Data Baru';
            }  ?>
        </div>

    </div>

    <div class="portlet-body form">
        <form id="submit_form" class="form-horizontal">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->id : 0 ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold fs-6" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Pesanan Pembelian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold fs-6" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Biaya Lain-Lain</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Supplier*</label>
                                            <div class="col-md-7">
                                                <select id="supp" name="supp" class="form-select input-medium select2me" data-placeholder="Pilih..." required>
                                                    <option value="" class="">---- pilih supplier----</option>
                                                    <?php
                                                    $suplier = $db->table('ap_supplier')->get()->getResult();
                                                    foreach ($suplier as $key => $value) { ?>
                                                        <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                                echo "selected=true";
                                                                                            } ?>><?= $value->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="suppErr"></span>
                                            </div>
                                            <div class="col-md-1">
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="ShowDataSupp()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-5">
                                                <select name="sppn" id="sppn" class="form-select select2 input-medium" data-placeholder="Pilih...">
                                                    <?php if (isset($table_data)) { ?>
                                                        <option value="Y" <?php if ($table_data->sppn == "Y") {
                                                                                echo "selected=true";
                                                                            } ?>>
                                                            Ya</option>
                                                        <option value="T" <?php if ($table_data->sppn == "T") {
                                                                                echo "selected=true";
                                                                            } ?>>
                                                            Tidak</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PraPo</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-medium" placeholder="" name="prapo" id="prapo" value="<?= isset($table_data) ? $table_data->kodeprapo : '' ?>" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <?php if (!isset($table_data)) { ?>
                                                    <span class="input-group-btn start-100 top-0">
                                                        <a class="btn blue" id="getrapo1" data-bs-toggle="modal" href="#getrapo" onclick="showmohon()"><i class="fa fa-search"></i></a>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal PO</label>
                                            <div class="col-md-7">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglpo)) : date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-medium" placeholder="" name="ket" id="ket" value="<?= isset($table_data) ? $table_data->ket : '' ?>">
                                            </div>
                                            <span class="ketErr"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal Kirim</label>
                                            <div class="col-md-7">
                                                <input id="tanggalkirim" name="tanggalkirim" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglpo)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Third Row -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Pembayaran</label>
                                            <div class="col-md-6">
                                                <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= isset($table_data) ? $table_data->kodepo : '' ?>" readonly>
                                                <select name="top" id="top" class="form-select select2 input-medium">
                                                    <?php if (isset($table_data)) { ?>
                                                        <option value="K" <?php if ($table_data->pembayaran == "K") {
                                                                                echo "selected=true";
                                                                            } ?>>
                                                            Kredit</option>
                                                        <option value="T" <?php if ($table_data->pembayaran == "T") {
                                                                                echo "selected=true";
                                                                            } ?>>
                                                            Tunai</option>
                                                    <?php } else { ?>
                                                        <option value="">Pilih</option>
                                                        <option value="K">Kredit</option>
                                                        <option value="T">Tunai</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php if (isset($table_data)) { ?>
                                        <div class="mb-3">
                                            <div class="row">
                                                <label class="control-label pt-2 col-md-3">NO BUKTI</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= isset($table_data) ? $table_data->kodepo : '' ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- Four Row -->
                            <!--ROW LIST BARANG-->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">Nama Barang</th>
                                                <th style="text-align: center">Kuantitas</th>
                                                <th style="text-align: center">Satuan</th>
                                                <th style="text-align: center">Harga</th>
                                                <th style="text-align: center">Diskon</th>
                                                <th style="text-align: center">Total Harga</th>
                                                <th></th>

                                            </thead>

                                            <tbody>
                                                <?php
                                                if (isset($table_data_detail) && $table_data->statusid == 1) {
                                                    $no = 0;
                                                    foreach ($table_data_detail as $value) {
                                                        $db = db_connect();
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $no++;
                                                ?>

                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode<?= $no ?>" class="barang_filter form-select input-xlarge">
                                                                    <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= $value->qtyorder; ?>" id="qty<?= $no ?>" type="text" class="form-control input-small rightJustified">
                                                                <input name="stok_min[]" value="<?= $barang->stok_min; ?>" id="stok_min<?= $no ?>" type="hidden" class="form-control rightJustified" required>
                                                            </td>
                                                            <td>
                                                                <input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $value->satuan; ?>" class="form-control input-small" required>
                                                                <input name="checkdata[]" id="checkdata<?= $no ?>" type="hidden" class='form-control' value="0">
                                                            </td>
                                                            <td><input name="harga[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= number_format($value->hargabeli); ?>" id="harga<?= $no ?>" type="text" class="form-control input-small rightJustified" required></td>
                                                            <td><input name="disc[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= $value->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control input-small rightJustified" required></td>
                                                            <td><input name="jumlah[]" id="jumlah<?= $no ?>" type="text" class="form-control rightJustified input-small" onchange="total()" onfocus="total()" required></td>

                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                totalline(<?= $no ?>)
                                                            });
                                                        </script>
                                                    <?php }
                                                } elseif (isset($table_data_detail) && $table_data->statusid == 3) {
                                                    $no = 0;
                                                    foreach ($table_data_detail as $value) {
                                                        $db = db_connect();
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $no++;
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode<?= $no ?>" class="barang_filter form-select input-xlarge">
                                                                    <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= $value->qtyorder - $value->qtykirim; ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small">
                                                                <input name="stok_min[]" value="<?= $barang->stok_min; ?>" id="stok_min<?= $no ?>" type="hidden" class="form-control rightJustified " required>
                                                            </td>
                                                            <td>
                                                                <input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $value->satuan; ?>" class="form-control input-small" required>
                                                                <input name="checkdata[]" id="checkdata<?= $no ?>" type="hidden" class='form-control' value="0">
                                                            </td>
                                                            <td><input name="harga[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= number_format($value->hargabeli); ?>" id="harga<?= $no ?>" type="text" class="form-control input-small rightJustified" required></td>
                                                            <td><input name="disc[]" onfocus="totalline(<?= $no ?>)" onchange="totalline(<?= $no ?>)" value="<?= $value->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control input-small rightJustified" required></td>
                                                            <td><input name="jumlah[]" id="jumlah<?= $no ?>" type="text" class="form-control input-small rightJustified" size="40%" onchange="total()" onfocus="total()" required></td>

                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                totalline(<?= $no ?>)
                                                            });
                                                        </script>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name="kode[]" id="kode1" class="barang_filter form-select input-xlarge">
                                                                <option value="">--- Pilih Barang ---</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input name="qty[]" onchange="totalline(1)" value="1" id="qty1" type="text" class="form-control input-small rightJustified">
                                                            <input name="stok_min[]" value="1" id="stok_min1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="sat[]" id="sat1" type="text" class="form-control input-small">
                                                            <input name="checkdata[]" id="checkdata1" type="hidden" class='form-control'>
                                                        </td>
                                                        <td><input name="harga[]" onchange="totalline(1)" value="0" id="harga1" type="text" class="form-control input-small rightJustified"></td>
                                                        <td><input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control input-small rightJustified "></td>
                                                        <td><input name="jumlah[]" id="jumlah1" ; type="text" class="form-control rightJustified input-small" onchange="total()"></td>

                                                        <td>
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                                                                <select name="kodeakun[]" class="selectakun form-select input-xlarge" id="kodeakun<?= $no ?>">
                                                                    <option value="<?= $value->kodeakun; ?>"><?= $akun->namaakun; ?></option>
                                                                </select>
                                                            </td>
                                                            <td><input name="ljumlah[]" id="ljumlah<?= $no ?>" type="text" class="form-control input-medium rightJustified ljumlah" value="<?= $value->jumlah; ?>" onchange="totalline(<?= $no ?>);total()"></td>
                                                            <td><input name="lket[]" id="lket<?= $no ?>" type="text" class="form-control input-xlarge" value="<?= $value->keterangan; ?>">
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name="kodeakun[]" class="selectakun form-select input-xlarge" id="kodeakun1">
                                                                <option value="">--- Pilih Akun ---</option>
                                                                <option value="Kash & Bank">Kash & Bank</option>
                                                            </select>
                                                        </td>
                                                        <td><input name="ljumlah[]" id="ljumlah1" type="text" class="form-control input-medium rightJustified ljumlah" value="0" onchange="total()"></td>
                                                        <td><input name="lket[]" id="lket1" type="text" class="form-control input-xlarge" onkeypress="return tabE(this,event)">
                                                        </td>
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
                                        <button type="button" class="btn green" onclick="tambah2()"><i class="fa fa-plus"></i> </button>
                                        <button type="button" class="btn red" onclick="deleteRow(this)"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="wells">
                                <button type="submit" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fas fa-edit"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>
                        <div class="col-md-3 invoice-block">
                            <div class="well p-2">
                                <table id="tabeltotal">
                                    <tr>
                                        <td width="40%"><strong>SUB TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vsubtotal">0</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>DISKON</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vdiskon">0</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>DPP</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_dpp">0</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>PPN</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vppn">0</span></strong></td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>BIAYA LAIN</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vbiayalain">0</span></strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%"><strong>TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_vtotal">0</span></strong></td>
                                    </tr>
                                    <input type="hidden" name="all_total" id="all_total">
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="getrapo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="getprapo">
                <div class="modal-header">
                    <button type="button" class="btn" data-bs-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Daftar PRA PO</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="showmohondata">
                        <div class="accordion" id="accordionPanelsStayOpenExample">
                            <?php
                            if (!isset($table_data_detail)) {
                                $no = 1;
                                foreach ($noprapo as $row) {
                            ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-heading<?php echo $no; ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?php echo $no; ?>" aria-expanded="true" aria-controls="panelsStayOpen-collapse<?php echo $no; ?>">
                                                <?php echo $row->kodeprapo; ?>
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapse<?php echo $no; ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?php echo $no; ?>">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="tableprapo table-striped table-hover table-bordered" id="">
                                                                <thead>
                                                                    <tr class="headerx">
                                                                        <th>Kode item</th>
                                                                        <th>Nama Barang</th>
                                                                        <th>qty pra po</th>
                                                                        <th>stock akhir</th>
                                                                        <th>Supplier</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $GetPrapodetail = $db->query("SELECT * FROM ap_prapodetail a inner join inv_barang b on a.kodeitem = b.kodeitem 
                                                                                                    where kodeprapo='$row->kodeprapo' and b.kdcbg='$cabang' and a.cabang='$cabang'  group by b.kodeitem")->getResult();
                                                                    foreach ($GetPrapodetail  as $rowdetail) {
                                                                        if ($rowdetail->qtyprapo > '0' && $rowdetail->qtyprapo - $rowdetail->qtykirim > 0) {
                                                                            $GetDataBrgSupp = $db->query("SELECT * from inv_barang_harga a 
                                                                                                        inner join ap_supplier b on b.kode = a.kode_spl 
                                                                                                        where a.kodeitem='$rowdetail->kodeitem' and a.cabang='$cabang'")->getRow();
                                                                    ?>
                                                                            <tr>
                                                                                <td align="centerx" id="kode_brg">
                                                                                    <?php echo $rowdetail->kodeitem; ?></a>
                                                                                </td>
                                                                                <td id="nama_brg">
                                                                                    <?php echo $rowdetail->namabarang; ?>
                                                                                </td>
                                                                                <td id="qty"><?php echo $rowdetail->qtyprapo - $rowdetail->qtykirim; ?></td>
                                                                                <td id="stok"> <?php echo $rowdetail->stok; ?></td>
                                                                                <td id="supp">
                                                                                    <select class="form-select input-large" data-placeholder="Pilih..." onchange="rubahspl(this.value, '<?= $rowdetail->kodeitem ?>')" required>
                                                                                        <option value="" class="">---- pilih supplier----</option>
                                                                                        <?php
                                                                                        $praposupp = $db->query("SELECT a.kode, a.nama, b.kodeitem, b.namabarang from ap_supplier a INNER JOIN inv_barang b ON a.kode = b.kode_spl1 OR a.kode = b.kode_spl2 OR a.kode = b.kode_spl3 OR a.kode = b.kode_spl4
                                                                        WHERE b.kodeitem = '$rowdetail->kodeitem' AND b.kdcbg='$cabang'")->getResult();
                                                                                        if (!empty($praposupp)) {


                                                                                            foreach ($praposupp as $value) { ?>
                                                                                                <option value="<?= $value->kode ?>" <?php if ($value->kode == $GetDataBrgSupp->kode) {
                                                                                                                                        echo "selected=true";
                                                                                                                                    } ?>><?= $value->nama ?></option>
                                                                                            <?php }
                                                                                        } else {
                                                                                            $supp = $db->table('ap_supplier')->get()->getResult();
                                                                                            foreach ($supp as $value) {
                                                                                            ?>
                                                                                                <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                                                        <?php }
                                                                                        } ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                    <?php }
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" onclick="post_brg('<?php echo $row->kodeprapo; ?>')" class="btn btn-success" data-bs-dismiss="modal"><i class="fa fa-plus"></i> Data Pra Pesanan Pembelian</button>
                                            </div>
                                        </div>
                                    </div>
                            <?php $no++;
                                }
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" data-bs-dismiss="modal">Tutup</button>
                </div>
            </span>
        </div>
    </div>
</div>

<script>
    var cabang = "<?= $cabang ?>";
    var gudang = <?= $gudang ?>;

    //Element add data pesanan pembelian
    var idrow = 2;

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
        var nourut = idrow;
        var akun = "<select name='kode[]' id=kode" + idrow + " class='barang_filter form-control' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]' onchange='stok_min(this.value," + idrow + " )'    id=qty" + idrow +
            " onkeyup='totalline(" + idrow +
            ")' value='1'  type='text' class='form-control rightJustified'  > <input name='stok_min[]'    id=stok_min" +
            idrow + " value='1'  type='hidden' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' > <input name='checkdata[]' id=checkdata" + idrow + " type='hidden' class='form-control' >";
        td4.innerHTML = "<input name='harga[]'  id=harga" + idrow + " onkeyup='totalline(" + idrow +
            ") value='0'  type='text' class='form-control rightJustified'>";
        td5.innerHTML = "<input name='disc[]'   id=disc" + idrow + " onkeyup='totalline(" + idrow +
            ")' value='0'  type='text' class='form-control rightJustified'  >";
        td6.innerHTML = "<input name='jumlah[]' onchange='total()' id=jumlah" + idrow +
            " type='text' class='form-control rightJustified' size='40%'>";
        td7.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
        initailizeSelect2_barang_filter(cabang, gudang);
    }

    //Element Add Data Biaya lain - lain                                                
    var idrow2 = 2;

    function tambah2() {
        var table = document.getElementById("datatable2");
        var idrowx = table.rows.length;
        var x = document.getElementById('datatable2').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);

        var akun =
            "<select name='kodeakun[]' class='selectakun form-control' id=kodeakun" + idrow2 + " ><option value=''>--- Pilih Akun ---</option></select>";

        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ljumlah[]' id=ljumlah" + idrow2 + " onchange='total()' value='0'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='lket[]'    id=lket" + idrow2 + " type='text' class='form-control' >";
        initailizeSelect2_akun();
        idrow2++;
    }

    //Delete Table Element Data Biaya lain - lain   
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        total();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var checkdata = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            hitungcheckdata = row.cells[2].children[1].value;
            // Cari elemen select di dalam baris
            var selectElement = row.querySelector('select');

            // Dapatkan nilai yang dipilih dari elemen select
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            // Dapatkan teks dari opsi yang dipilih
            var selectedName = selectedOption.text;
            cekdata(i, kode, selectedName);
            checkdata += parseInt(hitungcheckdata);
        }
        if (checkdata > 1) {
            $('#save_btn').attr('disabled', true);
        }
    }

    //Get data supplier
    function ShowDataSupp() {
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var str = $('[name=supp]').val();
        if (str == "") {
            swal('PESANAN PEMBELIAN', 'Pilih Supplier Terlebih Dahulu ...', '');
            $('#save_btn').attr('disabled', true);
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/getinfosupplier/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    if (data.pkp == 'Y') {
                        var option = $("<option selected></option>").val(data.pkp).text('Ya');
                        $('#sppn').append(option).trigger('change');
                    } else {
                        var option = $("<option selected></option>").val(data.pkp).text('Tidak');
                        $('#sppn').append(option).trigger('change');
                    }

                    // if (data.kredit == 'T') {
                    //     var option2 = $("<option selected></option>").val(data.Kredit).text('Tunai');
                    //     $('#top').append(option2).trigger('change');
                    // } else {
                    //     var option3 = $("<option selected></option>").val(data.Kredit).text('Kredit');
                    //     $('#top').append(option3).trigger('change');

                    // }

                    if (data.pkp == 'T') {
                        $('#nomorbukti').val(nomorbukti);
                    } else {
                        $('#nomorbukti').val(nomorbukti_ppn);
                    }

                }
            });
        }
    }

    function rubahspl(supp, kode) {
        $.ajax({
            url: "<?= base_url('pembelian/transaksi/pochangesupphargapercust/') ?>" + supp + "/" + kode,
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    swal('PESANAN PEMBELIAN', 'Barang ' + nama + ' tidak sesuai dengan referensi supplier', '');
                    $('#save_btn').attr('disabled', true);
                }
            }
        });
    }

    // Posting dari prapo
    function post_brg(str) {
        var supp = $('[name=supp]').val();
        if (supp == "") {
            swal('PESANAN PEMBELIAN', 'Pilih Supplier Terlebih Dahulu ...', '');
            $('#save_btn').attr('disabled', true);
        }
        var supp = $('[name=supp]').val();
        if (str == "") {
            swal('PESANAN PEMBELIAN', 'Kode PRA PO tidak ditemukan', '');
            $('#save_btn').attr('disabled', true);
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/pesananpembeliangetprapo/') ?>" + str + "/" + supp,
                type: "GET",
                dataType: "JSON",
                success: function(data) {

                    for (i = 0; i <= data.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;
                        if (data[i] == null) {
                            swal('PESANAN PEMBELIAN', 'Tidak ada Barang Dengan Supplier tersebut ...', '');
                            $('#save_btn').attr('disabled', true);
                        }
                        document.getElementById("prapo").value = data[i].kodeprapo;
                        if (data[i].kode_spl1 == supp) {

                            var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i]
                                .namabarang);
                            $('#kode' + x).append(option).trigger('change');
                            document.getElementById("qty" + x).value = data[i].qtyprapo - data[i].qtykirim;
                            $('#stok_min' + x).val(data[i].stok_min);
                            $('#sat' + x).val(data[i].satuan);
                            document.getElementById("harga" + x).value = number_format(data[i].bruto_spl1, 2);
                            $('#disc' + x).val(data[i].disc_spl1);
                            totalline(x);
                            cekdata(x, data[i].kodeitem, data[i].namabarang);

                        } else if (data[i].kode_spl2 == supp) {
                            var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i]
                                .namabarang);
                            $('#kode' + x).append(option).trigger('change');
                            document.getElementById("qty" + x).value = data[i].qtyprapo - data[i].qtykirim;
                            $('#stok_min' + x).val(data[i].stok_min);
                            $('#sat' + x).val(data[i].satuan);
                            document.getElementById("harga" + x).value = number_format(data[i].bruto_spl2, 2);
                            $('#disc' + x).val(data[i].disc_spl2);
                            totalline(x);
                            cekdata(x, data[i].kodeitem, data[i].namabarang);

                        } else if (data[i].kode_spl3 == supp) {
                            var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i]
                                .namabarang);
                            $('#kode' + x).append(option).trigger('change');
                            document.getElementById("qty" + x).value = data[i].qtyprapo - data[i].qtykirim;
                            $('#stok_min' + x).val(data[i].stok_min);
                            $('#sat' + x).val(data[i].satuan);
                            document.getElementById("harga" + x).value = number_format(data[i].bruto_spl3, 2);
                            $('#disc' + x).val(data[i].disc_spl3);
                            totalline(x);
                            cekdata(x, data[i].kodeitem, data[i].namabarang);

                        } else if (data[i].kode_spl4 == supp) {
                            var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i]
                                .namabarang);
                            $('#kode' + x).append(option).trigger('change');
                            document.getElementById("qty" + x).value = data[i].qtyprapo - data[i].qtykirim;
                            $('#stok_min' + x).val(data[i].stok_min);
                            $('#sat' + x).val(data[i].satuan);
                            document.getElementById("harga" + x).value = number_format(data[i].bruto_spl4, 2);
                            $('#disc' + x).val(data[i].disc_spl4);
                            totalline(x);
                            cekdata(x, data[i].kodeitem, data[i].namabarang);
                        }
                    }
                }
            });
        }
    }

    function cekdata(no, str, nama) {
        var supp = $('[name=supp]').val();
        $.ajax({
            url: "<?= base_url('pembelian/transaksi/pocekdatabarangsupp/') ?>" + str + "/" + supp,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    swal({
                        title: "PESANAN PEMBELIAN",
                        html: "Barang " + nama + " tidak sesuai dengan referensi supplier, silahkan ganti supplier",
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        $('#save_btn').attr('disabled', true);
                        $('#checkdata' + no).val(1);
                    });
                } else {
                    $('#save_btn').attr('disabled', false);
                    $('#checkdata' + no).val(0);
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


            harga = row.cells[3].children[0].value;
            diskon = row.cells[4].children[0].value;
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
            tppn = (tjumlah - tdiskon) * <?=$taxes?>;
        } else {
            tppn = 0;
        }


        document.getElementById("_vsubtotal").innerHTML = number_format(tjumlah);
        document.getElementById("_vdiskon").innerHTML = number_format(tdiskon);
        document.getElementById("_dpp").innerHTML = number_format(tjumlah - tdiskon);
        document.getElementById("_vbiayalain").innerHTML = number_format(tbiaya);
        document.getElementById("_vppn").innerHTML = number_format(tppn);
        document.getElementById("_vtotal").innerHTML = number_format(tjumlah - tdiskon + tbiaya + tppn);


    }

    function totalline(id) {
        var table = document.getElementById('datatable');
        var row = table.rows[id];

        tjumlah = 0;
        tdiskon = 0;

        jumlah = row.cells[1].children[0].value;
        harga = row.cells[3].children[0].value;
        diskon = row.cells[4].children[0].value;
        jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
        harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));
        diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));
        tjumlah = tjumlah + eval(jumlah1 * harga1);
        diskon = eval((diskon1 / 100) * jumlah1 * harga1);
        tdiskon = tdiskon + diskon;
        tot = number_format(tjumlah - tdiskon, 2);
        row.cells[5].children[0].value = number_format(tjumlah - tdiskon);
        total();

    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PESANAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PESANAN PEMBELIAN', 'Gunakan Tab untuk Next dan Mouse klik pada simpan ...', '');
        }
    });

    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var ppn = $('#sppn').val();
        var subtotal = $('#_vsubtotal').val();
        var bayar = $('#top').val();
        var nobukti = $('#nomorbukti').val();
        var tanggal = $('[name="tanggal"]').val();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var idData = '<?= $id ?>';
        var Status = '<?= $table_data->statusid ?>';
        var checkdata = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            qty = row.cells[1].children[0].value;
            satuan = row.cells[2].children[0].value;
            hitungcheckdata = row.cells[2].children[1].value;
            harga = row.cells[3].children[0].value;
            totharga = row.cells[5].children[0].value;
            if (idData != "" && Status == 1) {
                if (kode == "" || qty == "0" || satuan == "" || harga == "0" || totharga == "") {
                    swal('PENJUALAN PEMBELIAN', 'Data di baris ke ' + i + ' Tidak Boleh kosong', '');
                    $('#save_btn').attr('disabled', true);
                }
            } else if (Status == "") {
                if (kode == "" || qty == "0" || satuan == "" || harga == "0" || totharga == "") {
                    swal('PENJUALAN PEMBELIAN', 'Data di baris ke ' + i + ' Tidak Boleh kosong', '');
                    $('#save_btn').attr('disabled', true);
                }
            }
            var selectElement = row.querySelector('select');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var selectedName = selectedOption.text;
            cekdata(i, kode, selectedName);
            checkdata += parseInt(hitungcheckdata);
        }

        if (checkdata > 1) {
            $('#save_btn').attr('disabled', true);
        }
        if (ppn == "" || nobukti == "" || bayar == "") {
            swal('PESANAN PEMBELIAN', 'ppn atau pembayaran harus di isi ...', '');
        } else if (subtotal == "0") {
            swal('PESANAN PEMBELIAN', 'Subtotal harga tidak boleh 0 ...', '');
        } else if ($('#save_btn').prop('disabled') == true) {} else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/pesananpembelianadd') ?>",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    if (data.status == 1) {
                        $('#save_btn').attr('disabled', true);
                        swal({
                            title: "PESANAN PEMBELIAN",
                            html: "<p> Nomor Bukti   : <b>" + data.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then(function(value) {
                            //window.open(_urlcetak(data));
                            window.location.href = "<?= base_url('pembelian/transaksi/pesananpembelian') ?>";
                        });
                    } else {
                        swal('PESANAN PEMBELIAN', 'Data gagal disimpan ... ' + data.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PESANAN PEMBELIAN', 'Data gagal disimpan ...', '');
                }
            });
        }

    });

    $(document).ready(function() {
        initailizeSelect2_barang_filter(cabang, gudang);
        $('.tableprapo').DataTable({});
    });

    function _urlcetakPenerimaan() {
        var baseurl = "<?php echo base_url() ?>";
        var param = $('[name="nomorbukti"]').val();
        return baseurl + 'pembelian/transaksi/cetakpenerimaanpo/' + param;
    }
</script>