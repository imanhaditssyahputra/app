<?php
error_reporting(0);
$db = db_connect();
if ($data != []) {
    main_header('Inventory', 'Data Barang', ' Daftar Barang', 'Edit Barang', 'inventory/datamaster/barang');
    $DataBarang = 'Data Edit';
} else {
    main_header('Inventory', 'Data Barang', ' Daftar Barang', 'Entri Barang', 'inventory/datamaster/barang');
    $DataBarang = 'Data Baru';
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
            <i class="fa fa-reorder"></i>* <?= $DataBarang; ?>
        </div>
    </div>

    <div class="portlet-body form">
        <form id="submit_form" class="form-horizontal"  enctype="multipart/form-data">
            <input type="hidden" value="<?= ($data != []) ? $data->id : 0 ?>" name="id" id="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Master Barang</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="paket-tab" data-bs-toggle="tab" data-bs-target="#paket" type="button" role="tab" aria-controls="paket" aria-selected="false">Barang Paketan</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="coretax-tab" data-bs-toggle="tab" data-bs-target="#coretax" type="button" role="tab" aria-controls="coretax" aria-selected="false">Coretax Kode</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- Zero Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Gambar *</label>
                                            <div class="col-md-3">
                                                <input type="hidden" name="filefoto_old" id="filefoto_old" value="<?= ($data != []) ? $data->image_1920 : '' ?>">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 100px;">
                                                        <?php 
                                                            if($data != []){
                                                                if(!empty($data->image_1920)){
                                                        ?>
                                                            <img src="<?= BASEURL ?>assets/product/<?=$data->image_1920?>" alt=""/>
                                                        <?php }else{ ?>
                                                            <img src="<?= BASEURL ?>public/assets/images/placeholder.png" alt=""/>
                                                        <?php }
                                                            }else{
                                                        ?>
                                                            <img src="<?= BASEURL ?>public/assets/images/placeholder.png" alt=""/>
                                                        <?php }?>
                                                    </div>
                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                    </div>
                                                    <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new">
                                                                    Pilih Foto
                                                            </span>
                                                            <span class="fileinput-exists">
                                                                    Ganti
                                                            </span>
                                                            <input type="file" name="filefoto" id="filefoto" value="<?= ($data != []) ? $data->image_1920 : '' ?> formenctype="multipart/form-data">
                                                            
                                                        </span>
                                                        <a href="#" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                                Hapus
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="filefoto" style="color: #F00;"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Kode Item *</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control input-medium" name="kodeitem" id="kodeitem" value="<?= ($data != []) ? $data->kodeitem : $nourut ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Kode Ereport</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-medium" name="kodeereport" id="kodeereport" value="<?= ($data != []) ? $data->kode_ereport : "" ?>">
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
                                            <label class="control-label pt-2 col-md-3">Nama Barang</label>
                                            <div class="col-md-6">
                                                <input id="namebarang" name="namebarang" class="form-control input-xlarge" type="text" value="<?= ($data != []) ? htmlspecialchars($data->namabarang) : "" ?>" />
                                                <span id="namebarang"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Kategori</label>
                                            <div class="col-md-7">
                                                <select name="kategori" class="form-select select2">
                                                    <option value="0">--Pilih--</option>
                                                    <?php foreach ($kategori as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kdkategori) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="kategori"></span>
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
                                            <label class="control-label pt-2 col-md-3">Satuan</label>
                                            <div class="col-md-6">
                                                <select name="satuan" class="form-select select2">
                                                    <option value="0">--Pilih--</option>
                                                    <?php foreach ($satuan as $key) { ?>
                                                        <option value="<?= $key->nama ?>" <?= ($data != []) ? (($key->nama == $data->satuan) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="satuan"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Stock</label>
                                            <div class="col-md-7">
                                                <input type="text" name="stok" class="form-control" value="<?= ($data != []) ? $data->stok : '' ?>" readonly>
                                                <span id="stok"></span>
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
                                            <label class="control-label pt-2 col-md-3">Harga Beli</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control input-medium" name="hargabeli" id="hargabeli" value="<?= ($data != []) ? $data->hargabeli : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Harga Jual</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-medium" name="hargajual" id="hargajual" value="<?= ($data != []) ? $data->hargajual : '' ?>">
                                                <span id="hargajual"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Five Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">NIE</label>
                                            <div class="col-md-6">
                                                <input type="text" name="nie" class="form-control" value="<?= ($data != []) ? $data->nie : '' ?>" required>
                                                <span id="nie"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Tgl Exp NIE</label>
                                            <div class="col-md-6">
                                                <input type="date" name="tglexpnie" class="form-control" value="<?= ($data != []) ? $data->tglexpnie : date('d-m-Y') ?>">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- sixth Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">

                                            <label class="control-label pt-2 col-md-3">Region</label>
                                            <div class="col-md-6">
                                                <select name="cabang" class="form-select select2">
                                                    <option value="0">--Pilih Region--</option>
                                                    <?php foreach ($MasterCabang as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kdcbg) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="cabang"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Gudang</label>
                                            <div class="col-md-7">
                                                <select class="select2 form-control select2-multiple" name="gudang[]" multiple="multiple" data-placeholder="Choose ...">
                                                    <?php
                                                    if ($level == 1) {
                                                        foreach ($gudang as $key) {
                                                    ?>
                                                            <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kdgudang) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                        <?php   }
                                                    } else { ?>
                                                        <option value="<?= $GudangGetCabang->kode ?>" <?= ($data != []) ? (($GudangGetCabang->kode == $data->kdgudang) ? "selected" : '') : '' ?>><?= $GudangGetCabang->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="gudang"></span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- seventh Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Rak</label>
                                            <div class="col-md-6">
                                                <select name="rak" class="form-select select2">
                                                    <option value="0">--Pilih--</option>
                                                    <?php foreach ($rak as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kdrak) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="rak"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Merk</label>
                                            <div class="col-md-6">
                                                <select name="merk" class="form-select select2">
                                                    <option value="0">--Pilih--</option>
                                                    <?php foreach ($merk as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kdmerk) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span id="merk"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- eightth Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Stock Min</label>
                                            <div class="col-md-7">
                                                <input type="text" name="stokmin" class="form-control" value="<?= ($data != []) ? $data->stok_min : '' ?>">
                                                <span id="stokmin"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Stock Max</label>
                                            <div class="col-md-6">
                                                <input type="text" name="stokmax" class="form-control" value="<?= ($data != []) ? $data->stok_max : '' ?>">
                                                <span id="stokmin"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- nineth Row -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-4">
                                                <select name="ppn" class="form-select select2">
                                                    <option value="">--Pilih--</option>
                                                    <option value="Y" <?= ($data != []) ? (('Y' == $data->ppn) ? "selected" : '') : '' ?>>YA</option>
                                                    <option value="T" <?= ($data != []) ? (('T' == $data->ppn) ? "selected" : '') : '' ?>>TIDAK</option>
                                                </select>
                                                <span id="ppn"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">MOQ Principle</label>
                                            <div class="col-md-6">
                                                <input type="text" name="moq" class="form-control" value="<?= ($data != []) ? $data->moq : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="paket" role="tabpanel" aria-labelledby="paket-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="tablepaketan" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">Nama Barang</th>
                                                <!-- <th style="text-align: center">stock</th> -->
                                                <th style="text-align: center">qty paket</th>
                                                <!-- <th style="text-align: center">Harga jual isi Paket</th> -->
                                                <th style="text-align: center">Satuan</th>
                                                <th style="text-align: center"></th>
                                            </thead>
                                            <tbody align="center" id="detailpaketbarang">
                                                <?php if ($DataPaket != []) {
                                                    $no = 0;
                                                    foreach ($DataPaket as $key) {
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <select name='NamaBrgPaket[]' id="NamaBrgPaket<?= $no ?>" onchange='showbarangname(this.value,<?= $no ?>)' class='barang_filter_cabang form-control input-xlarge Modal_Barang'>
                                                                    <option value="<?= $key->kodeitem_paket; ?>"><?= $key->namabarang_paket; ?></option>
                                                                </select>
                                                            </td>
                                                            <!-- <td><input name='stokpaket[]' id="stokpaket<?= $no ?>" value='<?= $key->stok_isipaket; ?>' type='text' class='form-control rightJustified input-small' readonly></td> -->
                                                            <td><input name='qtyPaket[]' id="qtyPaket<?= $no ?>" value="<?= $key->qty_paket; ?>" type='text' class='form-control input-small'></td>
                                                            <!-- <td><input name='hjualpaket[]' id="hjualpaket<?= $no ?>" value="<?= number_format($key->hargajual_isipaket); ?>" type='text' class='form-control input-small'></td> -->
                                                            <td><input name='satPaketan[]' id="satPaketan<?= $no ?>" value='<?= $key->satuan_paket; ?>' type='text' class='form-control rightJustified input-small' readonly></td>
                                                            <td><button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button></td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td>
                                                            <select name='NamaBrgPaket[]' id="NamaBrgPaket1" onchange='showbarangname(this.value,1)' class='barang_filter_cabang form-control input-xlarge Modal_Barang'>
                                                                <option value=''>--- Pilih Barang ---</option>
                                                            </select>
                                                        </td>
                                                        <!-- <td><input name='stokpaket[]' id="stokpaket1" value='0' type='text' class='form-control rightJustified input-small' readonly></td> -->
                                                        <td><input name='qtyPaket[]' id="qtyPaket1" type='text' class='form-control input-small'></td>
                                                        <!-- <td><input name='hjualpaket[]' id="hjualpaket1" value="" type='text' class='form-control input-small'></td> -->
                                                        <td><input name='satPaketan[]' id="satPaketan1" value='' type='text' class='form-control rightJustified input-small' readonly></td>
                                                        <td><button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button></td>
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
                                        <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="coretax" role="tabpanel" aria-labelledby="coretax-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Barang / Jasa</label>
                                            <div class="col-md-7">
                                            <select class="form-control input-medium select2" id="barangjasa" name="barangjasa" required>
                                                    <option>---Pilih Barang / Jasa---</option>
                                                    <option value="A" <?= ($data != []) ? (('A' == $data->taxbarangjasa) ? "selected" : '') : '' ?>>Barang</option>
                                                    <option value="B" <?= ($data != []) ? (('B' == $data->taxbarangjasa) ? "selected" : '') : '' ?>>Jasa</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Satuan Ukur</label>
                                            <div class="col-md-7">
                                                <select class="form-control input-medium select2" id="taxsatuan" name="taxsatuan" required>
                                                    <option>---Pilih Satuan--</option>
                                                    <?php foreach ($DataSatuanTax as $key) { ?>
                                                        <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->taxsatuan) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
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
                                <div class="btn-group">
                                    <a class="btn green" href="<?= base_url() ?>inventory/datamaster/barang/entri"><i class="fa fa-edit"></i> Data Baru</a>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var cabang = "<?= $cbg ?>";
    var table;
    var base_url = '<?= base_url('inventory/datamaster/barangtable'); ?>';


    $(document).ready(function() {
        table = $('#table').DataTable({
            "searching": false,
        });
        initailizeSelect2_barang_filterCabang(cabang);
    });

    function tambah() {
        var table = document.getElementById("tablepaketan");
        var idrowx = table.rows.length;

        var x = document.getElementById('tablepaketan').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);

        var akun = "<select name='NamaBrgPaket[]' id=NamaBrgPaket" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control input-xlarge Modal_Barang' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        // td2.innerHTML = "<input name='stokpaket[]'  id=stokpaket" + idrowx + "  value=''  type='text' class='form-control rightJustified input-small' readonly> ";
        td2.innerHTML = "<input name='qtyPaket[]'    id=qtyPaket" + idrowx + " type='text' class='form-control input-small' >";
        // td4.innerHTML = "<input name='hjualpaket[]'  id=hjualpaket" + idrowx + "  value=''  type='text' class='form-control rightJustified input-small' readonly>";
        td3.innerHTML = "<input name='satPaketan[]'  id=satPaketan" + idrowx + "  value=''  type='text' class='form-control rightJustified input-small' readonly>";
        td4.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        initailizeSelect2_barang_filterCabang(cabang);
    }

    //Delete Table Element Data
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("tablepaketan").deleteRow(i);
    }

    function showbarangname(str, id) {
        var xhttp;
        var vid = id;
        var customer = $('#cust').val();
        $.ajax({
            url: "<?= base_url('inventory/datamaster/SelectBarangPaket/') ?>" + str,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#KodeBrgPaket' + vid).val(data.kodeitem);
                $('#satPaketan' + vid).val(data.satuan);
            }
        });

    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('MASTER BARANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('MASTER BARANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Data Barang Add & Update
    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('#id').val();
        var formData = new FormData(this);
        if (id == 0) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/barangadd') ?>",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(res) {
                    $('#process').css('display', 'none');
                    if (!res.error) {
                        swal({
                            title: "MASTER BARANG",
                            html: res.message,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                        });
                    } else {
                        // $('#kategori').html(res.kategori);
                        $('#namebarang').html(res.namebarang);
                        $('#gudang').html(res.gudang);
                        $('#nie').html(res.nie);
                        // $('#rak').html(res.rak);
                        $('#ppn').html(res.ppn);
                        // $('#merk').html(res.merk);
                        $('#satuan').html(res.satuan);
                        $('#cabang').html(res.cabang);
                        $('.filefoto').html(res.filefoto);
                    }
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/barangupdate/') ?>" + id,
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(res) {
                    $('#process').css('display', 'none');
                    if (!res.error) {
                        swal({
                            title: "MASTER BARANG",
                            html: "<p>Data Berhasil di Update </p>",
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                        });
                        $('#modal_form').modal('hide');
                    } else {
                        // $('#kategori').html(res.kategori);
                        $('#namebarang').html(res.namebarang);
                        $('#gudang').html(res.gudang);
                        // $('#rak').html(res.rak);
                        $('#ppn').html(res.ppn);
                        // $('#merk').html(res.merk);
                        $('#satuan').html(res.satuan);
                        $('#cabang').html(res.cabang);
                        $('.filefoto').html(res.filefoto);
                    }
                }
            });
        }
    });
</script>