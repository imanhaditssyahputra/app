<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_lpb')->where('id', $id)->get()->getRow();
    $table_data_detail = $db->table('ap_lpbdetil')->where('nolpb', $table_data->nolpb)->get()->getResult();
}
?>
<?= isset($table_data) ? main_header('Pembelian', 'Penerimaan Barang', 'Data Penerimaan Barang', 'Edit Penerimaan Barang', 'pembelian/transaksi/penerimaanbarang') : main_header('Pembelian', 'Penerimaan Barang', 'Data Penerimaan Barang', 'Entri Penerimaan Barang', 'pembelian/transaksi/penerimaanbarang') ?>
<?= main_periode('Daftar Penerimaan Pembelian'); ?>

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
            <i class="fa fa-reorder"></i><?= isset($table_data) ? 'Data Edit' : '*Data Baru' ?>
        </div>
    </div>
    <div class="portlet-body form">
        <form id="submit_form" class="form-horizontal">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->id : 0 ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold fs-6" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Penerimaan Pembelian</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold fs-6" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Info</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Supplier*</label>
                                            <div class="col-md-9">
                                                <select id="supp" name="supp" class="form-select input-medium select2" data-placeholder="Pilih..." required>
                                                    <option value="" class="">---- pilih supplier----</option>
                                                    <?php
                                                    if (isset($table_data)) {
                                                        $suplier = $db->table("ap_supplier")->getWhere(['kode' => $table_data->kodesup])->getResult();
                                                        foreach ($suplier as $key => $value) { ?>
                                                            <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                                    echo "selected=true";
                                                                                                } ?>><?= $value->nama ?></option>
                                                        <?php
                                                        }
                                                    } else {
                                                        $suplier = $db->query("SELECT * FROM ap_supplier a 
                                                            INNER JOIN ap_pofile b ON a.kode = b.kodesup WHERE b.statusid=3 or b.statusid=1 GROUP BY b.kodesup")->getResult();
                                                        foreach ($suplier as $key => $value) {
                                                        ?>
                                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="showpo()" style="width: 70px;"><i class="fa fa-refresh"></i> PO</a>
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
                                                <input type="text" class="form-control input-medium" name="nomorbukti" readonly id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->nolpb : 'Nomor Auto' ?>" value="<?= isset($table_data) ? $table_data->nolpb : '' ?>">
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
                                            <label class="control-label pt-2 col-md-3">Gudang</label>
                                            <div class="col-md-9">
                                                <select id="gudang" name="gudang" class="form-control input-medium select_gudang_filter" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    $InvGudang = $db->table('inv_gudang')->get()->getResult();
                                                    foreach ($InvGudang as $key => $value) { ?>
                                                        <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->gudang) {
                                                                                                echo "selected=true";
                                                                                            } ?>><?= $value->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Pesanan</label>
                                            <div class="col-md-9">
                                                <?php if (isset($table_data)) { ?>
                                                    <input type="text" name="kodepo" id="kodepo" value="<?= isset($table_data) ? $table_data->kodepo : '' ?>" class="form-control input-medium" readonly>
                                                <?php } else { ?>
                                                    <select id="kodepo" name="kodepo" class="form-control input-medium select2">
                                                        <option value="">---Tanpa PO---</option>
                                                    </select>
                                                    <span class="input-group-btn start-100 top-0">
                                                        <a class="btn green" onclick="post_brg()"><i class="fa fa-refresh"></i></a>
                                                    </span>
                                                <?php } ?>
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
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-6">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tgllpb)) : date('Y-m-d') ?>" />
                                                <input type="hidden" name="sppn" id="sppn" class="form-control input-medium" value="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Terima</label>
                                            <div class="col-md-3 mb-3">
                                                <SELECT name="opsi_terima" id="opsi_terima" class=" form-control select2 input-small">
                                                    <?php if (isset($table_data) && $table_data->no_terima == 1) { ?>
                                                        <option value="1" selected="true">Surat Jalan</option>
                                                        <option value="2">Faktur</option>
                                                    <?php } elseif (isset($table_data) && $table_data->no_terima == 2) { ?>
                                                        <option value="1">Surat Jalan</option>
                                                        <option value="2" selected="true">Faktur</option>
                                                    <?php } elseif (isset($table_data) && $table_data->no_terima == 3) { ?>
                                                        <option value="1">Surat Jalan</option>
                                                        <option value="2">Faktur</option>
                                                        <option value="3" selected="true">sudah faktur</option>
                                                    <?php } else { ?>
                                                        <option value="1">Surat Jalan</option>
                                                        <option value="2">Faktur</option>
                                                    <?php } ?>
                                                </SELECT>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class=" form-control input-medium" placeholder="" name="noterima" id="noterima" value="<?= isset($table_data) ? $table_data->noterima : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Four Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">Kode/Nama Barang</th>
                                                <th style="text-align: center">No Batch</th>
                                                <th style="text-align: center">Tanggal exp</th>
                                                <th style="text-align: center">qty</th>
                                                <th style="text-align: center">sat</th>
                                                <th style="text-align: center"></th>
                                            </thead>

                                            <tbody>
                                                <?php
                                                if (isset($table_data_detail)) {
                                                    $no = 0;
                                                    foreach ($table_data_detail as $value) {
                                                        $db = db_connect();
                                                        $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang'")->getRow();
                                                        $no++;
                                                ?>

                                                        <tr id="table_row">
                                                            <td width="40%">
                                                                <select name="kode[]" id="kode<?= $no ?>" class="barang_filter input-xlarge form-select" onchange="selectCurrent(this.value, 1)">
                                                                    <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                </select>
                                                            </td>
                                                            <td width="20%"><input name="no_batch[]" id="no_batch<?= $no ?>" type="text" value="<?= $value->no_batch; ?>" class="form-control input-medium rightJustified"></td>
                                                            <td width="15%"><input name="tgl_exp[]" id="tgl_exp<?= $no ?>" type="text" value="<?= $value->tanggal_exp; ?>" placeholder='yyyy-mm-dd' class="form-control rightJustified input-small">

                                                            </td>
                                                            <td width="10%">
                                                                <input name="qty[]" onchange="check_qty(this.value, <?= $no ?>)" value="<?= $value->qtyterima; ?>" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small">
                                                                <input name="qty_po[]" value="<?= $value->qtyorder; ?>" id="qty_po<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                <input name="disc[]" value="<?= $value->disc; ?>" id="disc<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td width="10%">
                                                                <input name="sat[]" id="sat<?= $no ?>" type="text" class="form-control input-small" value="<?= $value->satuan; ?>">
                                                                <input name="hargabeli[]" id="harga<?= $no ?>" type="hidden" value="<?= $value->hargabeli; ?>" class="form-control ">
                                                            </td>
                                                            <td width="5%">
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr id="table_row">
                                                        <td>
                                                            <select name="kode[]" id="kode1" class="barang_filter input-xlarge form-select" data-placeholder="pilih Barang" onchange="selectCurrent(this.value, 1)">
                                                            </select>
                                                        </td>
                                                        <td><input name="no_batch[]" id="no_batch" type="text" class="form-control rightJustified input-medium"></td>
                                                        <td><input name="tgl_exp[]" id="tgl_exp" type="text" placeholder='yyyy-mm-dd' class="form-control rightJustified input-small">

                                                        </td>
                                                        <td>
                                                            <input name="qty[]" onchange="check_qty(this.value, 1)" value="0" id="qty1" type="text" class="form-control rightJustified input-small">
                                                            <input name="qty_po[]" value="0" id="qty_po1" type="hidden" class="form-control rightJustified">
                                                            <input name="disc[]" value="0" id="disc1" type="hidden" class="form-control rightJustified">
                                                        </td>
                                                        <td>
                                                            <input name="sat[]" id="sat1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)">
                                                            <input name="hargabeli[]" id="harga1" type="hidden" class="form-control ">
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="wells">
                                                <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button>
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
                                                    <input id="tanggalkirim" name="tanggalkirim" class="form-control date-picker input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tgllpb)) : date('Y-m-d') ?>">
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
                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>">
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
                                                <textarea class="form-control" name="alamat" id="alamat"><?= isset($table_data) ? $table_data->alamat : '' ?></textarea>
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
                                <?php
                                if (isset($table_data)) {
                                    if ($table_data->no_terima != 3) {
                                ?>
                                        <button type="submit" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <button type="submit" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                <?php } ?>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fas fa-edit"></i> Data Baru</button>
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
    var cabang = "<?= $cabang ?>";

    //Element add data pesanan pembelian
    var idrow = 2;

    function tambah() {
        var gudang = $('[name=gudang]').val();
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var td6 = x.insertCell(5);

        var akun = "<select name='kode[]' id=kode" + idrow + " onchange='selectCurrent(this.value," + idrow + ")' class='barang_filter form-control' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='no_batch[]'    id=no_batch" + idrow + " onchange='totalline(" + idrow + ")'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrow + " onchange='totalline(" + idrow + ")'  type='text' placeholder='yyyy-mm-dd' class='form-control rightJustified'  >";
        td4.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrow + ")'  id=qty" + idrow + " onchange='totalline(" + idrow + ")' value='0'  type='text' class='form-control rightJustified'  > <input name='qty_po[]'  id=qty_po" + idrow + " value='0'  type='hidden' class='form-control rightJustified'  > <input name='disc[]'  id=disc" + idrow + " type='hidden' class='form-control' >";
        td5.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' > <input name='hargabeli[]'    id=harga" + idrow + " type='hidden' class='form-control' >";
        td6.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
        initailizeSelect2_barang_filter(cabang, gudang);
    }

    //Delete Table Element Data Biaya lain - lain   
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    //show data PO Pembelian
    function showpo() {
        var xhttp;
        var str = $('[name="supp"]').val();

        if (str == "") {
            document.getElementById("kodepo").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodepo").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('pembelian/transaksi/penerimaanbarang/getlistpo/') ?>" + str, true);
        xhttp.send();
    }

    // Posting dari po
    function post_brg() {
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var gudang = $('[name=gudang]').val();
        var kode = $('[name=kodepo]').val();
        if ($('[name=supp]').val() == "" || gudang == null) {
            swal('PESANAN PEMBELIAN', 'Pilih Supplier dan gudang Terlebih Dahulu ...', '');
            $('#save_btn').attr('disabled', true);
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/penerimaanbarang/postpo/') ?>" + kode + "/" + gudang,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }
                        x = i + 1;
                        var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i].namabarang);
                        $('#kode' + x).append(option).trigger('change');
                        document.getElementById("qty" + x).value = data[i].qtyorder - data[i].qtykirim;
                        document.getElementById("qty_po" + x).value = data[i].qtyorder - data[i].qtykirim;
                        document.getElementById("disc" + x).value = data[i].disc;
                        document.getElementById("sat" + x).value = data[i].satuan;
                        document.getElementById("harga" + x).value = number_format(data[i].hargabeli, 2);


                    }
                }
            });

            $.ajax({
                url: "<?= base_url('pembelian/transaksi/penerimaanbarang/postpoheader/') ?>" + kode,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('[name="keterangan"]').val(data.ket);
                    $('[name="alamat"]').val(data.alamat1);
                    $('#sppn').val(data.sppn);
                    if (data.sppn == 'Y') {
                        $('#nomorbukti').val(nomorbukti_ppn);
                    } else {
                        $('#nomorbukti').val(nomorbukti);
                    }
                }
            });
        }
    }

    function selectCurrent(str, vid) {
        var gudang = $('[name=gudang]').val();
        var kodepo = $('[name="kodepo"]').val();
        var supp = $('[name=supp]').val();
        $.ajax({
            url: "<?= base_url('pembelian/transaksi/penerimaanbarang/showbarangpo/') ?>" + str + "/" + kodepo + "/" + gudang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    swal('PENERIMAAN PEMBELIAN', 'barang tidak sesuai dengan pesanan pembelian', '');
                    $('#save_btn').attr('disabled', true);
                } else {
                    if (data.qtypo > 0) {
                        $('#qty_po' + vid).val(data.qtypo);
                    } else {
                        swal('stock', 'qtypo tidak ditemukan', '');
                        /*$('#save_btn'+vid).attr('disabled',true);*/
                    }
                }
                if (data.kode_spl1 == supp && data.bruto_spl1 == data.hargabeli && data.disc_spl1 == data.disc) {

                    $('#sat' + vid).val(data.satuan);
                    $('#disc' + vid).val(data.disc_spl1);
                    document.getElementById("harga" + vid).value = number_format(data.bruto_spl1, 2);

                } else if (data.kode_spl2 == supp && data.bruto_spl2 == data.hargabeli && data.disc_spl2 == data.disc) {
                    $('#sat' + vid).val(data.satuan);
                    $('#disc' + vid).val(data.disc_spl2);
                    document.getElementById("harga" + vid).value = number_format(data.bruto_spl2, 2);

                } else if (data.kode_spl3 == supp && data.bruto_spl3 == data.hargabeli && data.disc_spl3 == data.disc) {
                    $('#sat' + vid).val(data.satuan);
                    $('#disc' + vid).val(data.disc_spl3);
                    document.getElementById("harga" + vid).value = number_format(data.bruto_spl3, 2);

                } else if (data.kode_spl4 == supp && data.bruto_spl4 == data.hargabeli && data.disc_spl4 == data.disc) {
                    $('#sat' + vid).val(data.satuan);
                    $('#disc' + vid).val(data.disc_spl4);
                    document.getElementById("harga" + vid).value = number_format(data.bruto_spl4, 2);

                } else {
                    $('#sat' + vid).val(data.satuan);
                    $('#disc' + vid).val(data.disc);
                    document.getElementById("harga" + vid).value = number_format(data.hargabeli, 2);
                }

            }
        });
    }

    function check_qty(str, id) {
        var str_a = $('[name="kodepo"]').val();
        var str_b = $('#kode' + id).val();
        var str_c = parseInt($('#qty_po' + id).val());
        var vstr = parseInt(str);
        var vid = id;
        if (vstr > str_c) {
            swal('PEMBELIAN', 'qty tidak boleh lebih dari qty order...', '');
            $('#save_btn').attr('disabled', true);
        } else {
            $('#save_btn').attr('disabled', false);
        }
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENERIMAAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENERIMAAN PEMBELIAN', 'Gunakan Tab untuk Next dan Mouse klik pada simpan ...', '');
        }
    });

    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var tanggal = $('[name="tanggal"]').val();
        var nobukti = $('[name="nomorbukti"]').val();
        var qty_a = document.getElementsByName('qty[]');
        var qty_b = document.getElementsByName('qty_po[]');
        var kode = $('[name="kode[]"]').val();
        var tot_qty = 0
        for (var i = 0; i < qty_a.length; i++) {
            if (parseInt(qty_a[i].value))
                tot_qty += parseInt(qty_a[i].value);
        }

        var tot_qtypo = 0
        for (var i = 0; i < qty_b.length; i++) {
            if (parseInt(qty_b[i].value))
                tot_qtypo += parseInt(qty_b[i].value);
        }
        if (tot_qty > tot_qtypo) {
            swal('PENERIMAAN PEMBELIAN', ' total qty tidak boleh lebih dari total qty orderan ...', '');
        } else if (nobukti == "") {
            swal('PENERIMAAN PEMBELIAN', 'Nomor Bukti tidak ditemukan ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/penerimaanbarangadd') ?>",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    if (data.status == 1) {
                        swal({
                            title: "PENERIMAAN PEMBELIAN",
                            html: "<p> No Bukti   : <b>" + data.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('pembelian/transaksi/penerimaanbarang') ?>";
                        });
                    } else {
                        swal('PENERIMAAN PEMBELIAN', 'Data gagal disimpan ... ' + data.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PEMBELIAN', 'Data gagal disimpan ...', '');


                }
            });
        }

    });

    $(document).ready(function() {
        initailizeSelect2_barang_filter(cabang, gudang);
        initailizeSelect2_gudangFilter(cabang);
    });
</script>