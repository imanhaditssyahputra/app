<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('inv_mohon_mutasi_cabang')->where('idmohon', $id)->get()->getRow();
    $table_data_detail = $db->table('inv_mohond_mutasi_cabang')->where('nomohon', $table_data->nomohon)->get()->getResult();
    $disabled = ($table_data->cabang == $cabang) ? '' : 'disabled';
    $readonly = ($table_data->cabang == $cabang) ? '' : 'readonly';
    if ($table_data->statusid == 3) {
        main_header('Inventory', 'Permintaan Mutasi Barang Antar Region', 'Daftar Permintaan Mutasi', 'View Permintaan Mutasi Antar Region', 'inventory/transaksi/permohonanmutasicabang');
    } else {
        main_header('Inventory', 'Permintaan Mutasi Barang Antar Region', 'Daftar Permintaan Mutasi', 'Edit Permintaan Mutasi Antar Region', 'inventory/transaksi/permohonanmutasicabang');
    }
} else {
    $disabled = '';
    $readonly = '';
    main_header('Inventory', 'Permintaan Mutasi Barang Antar Region', 'Daftar Permintaan Mutasi', 'Entri Permintaan Mutasi Antar Region', 'inventory/transaksi/permohonanmutasicabang');
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
            <i class="fa fa-reorder"></i><?php
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
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->idmohon : 0 ?>">
            <input type="hidden" name="sts" value="<?= isset($table_data) ? $table_data->statusid : '' ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fa fa-file"></i>
                                Permintaan Mutasi Barang
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tanggal)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Bukti</label>
                                            <div class="col-md-7">
                                                <input type="text" placeholder="Otomatis" name="nomorbukti" class="form-control input-medium" value="<?= isset($table_data) ? $table_data->nomohon : $nomor ?>" readonly>
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
                                            <label class="control-label pt-2 col-md-3">Dari Region Asal</label>
                                            <div class="col-md-9">
                                                <select id="cabang_asal" name="cabang_asal" class="form-select input-medium select2" data-placeholder="Pilih..." <?=$readonly?>>
                                                    <option value="0">--Pilih Region--</option>
                                                    <?php
                                                     if (!isset($table_data)) {
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode' => $cabang])->getResult();
                                                        foreach ($cabangasal as $key => $value) { ?>
                                                        <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                    <?php }
                                                     }else{
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode' => $table_data->cabang_asal])->getRow();
                                                     ?>
                                                     <option value="<?= $cabangasal->kode ?>" <?php if (isset($table_data) && $cabangasal->kode == $table_data->cabang_asal) {
                                                                                                echo "selected=true";
                                                                                            } ?>><?= $cabangasal->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Ke Region Tujuan</label>
                                            <div class="col-md-9">
                                                <select id="cabang_tujuan" name="cabang_tujuan" class="form-select input-medium select2" onchange="getbarang()" data-placeholder="Pilih..." <?=$readonly?>>
                                                    <option value="0">--Pilih Region--</option>
                                                    <?php
                                                    if (!isset($table_data)) {
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode !=' => $cabang])->getResult();
                                                        foreach ($cabangasal as $key => $value) { ?>
                                                        <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->cabang_tujuan) {
                                                                                                echo "selected=true";
                                                                                            } ?>><?= $value->nama ?></option>
                                                    <?php }
                                                     }else{
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode' => $table_data->cabang_tujuan])->getRow();
                                                     ?>
                                                      <option value="<?= $cabangasal->kode ?>" <?php if (isset($table_data) && $cabangasal->kode == $table_data->cabang_tujuan) {
                                                                                                echo "selected=true";
                                                                                            } ?>><?= $cabangasal->nama ?></option>
                                                    <?php } ?>
                                                </select>
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
                                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>" <?=$readonly ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3"></label>
                                            <div class="col-md-6">
                                                <span class="btn blue btn-buffer"  data-bs-toggle="modal" data-bs-target=<?php if(empty($id)) echo '#bufferStock';?>> Buffer Stock <i class="fa fa-search"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fourth Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="overflow-x: auto;">
                                        <div class="datatable-example">
                                            <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                                <thead>
                                                    <th style="text-align: center">Kode/Nama Barang</th>
                                                    <th style="text-align: center">qty</th>
                                                    <th style="text-align: center">Stock Asal</th>
                                                    <th style="text-align: center">satuan</th>
                                                    <!-- <th style="text-align: center">Isi Paket</th> -->
                                                    <th></th>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (isset($table_data_detail) && $table_data->statusid < 3) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->table("inv_barang")->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $table_data->cabang_tujuan, 'kdgudang' => $table_data->gudang_tujuan])->getRow();
                                                            $barangMohon = $db->table("inv_barang")->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $table_data->cabang_asal, 'kdgudang' => $table_data->gudang_asal])->getRow();
                                                            $no++;
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $no ?>" class="barang_filter_cabang input-xlarge form-select" onchange="showbarangname(this.value, <?= $no ?>)" required <?=$readonly?> />
                                                                    <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input name="qty[]" value="<?= $value->qtymohon - $value->qtymutasi; ?>" id="qty<?= $no ?>" type="text" class="form-control input-medium rightJustified" <?=$readonly ?> required>
                                                                </td>
                                                                <td><input name="stock[]" value="<?= $barangMohon != null ? $barangMohon->stok : '0' ?>" id="stock<?= $no ?>" type="text" class="form-control input-medium rightJustified" readonly></td>
                                                                <td><input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $value->satuan; ?>" class="form-control input-small" <?=$readonly ?>></td>
                                                                <!-- <td>
                                                                    <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                                </td> -->
                                                                <td>
                                                                    <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } elseif (isset($table_data_detail) && $table_data->statusid == 3) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->table("inv_barang")->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $table_data->cabang_tujuan, 'kdgudang' => $table_data->gudang_tujuan])->getRow();
                                                            $barangMohon = $db->table("inv_barang")->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $table_data->cabang_asal, 'kdgudang' => $table_data->gudang_asal])->getRow();
                                                            $no++;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $no ?>" class="form-select input-xlarge" onchange="showbarangname(this.value, <?= $no ?>)" required <?=$readonly?> />
                                                                    <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input name="qty[]" value="<?= $value->qtymohon - $value->qtymutasi; ?>" id="qty<?= $no ?>" type="text" class="form-control input-medium rightJustified" <?=$readonly ?> required>
                                                                </td>
                                                                <td><input name="stock[]" value="<?= $barangMohon != null ? $barangMohon->stok : '0' ?>" id="stock<?= $no ?>" type="text" class="form-control input-medium rightJustified" readonly></td>
                                                                <td><input name="sat[]" id="sat<?= $no ?>" type="text" value="<?= $value->satuan; ?>" class="form-control input-small" <?=$readonly ?>></td>
                                                                <!-- <td>
                                                                    <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                                </td> -->
                                                                <td>
                                                                    <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } else { ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode1" class="barang_filter_cabang input-xlarge form-select" onchange="showbarangname(this.value, 1)" required />
                                                                <option value="">--- Pilih Barang ---</option>
                                                                </select>
                                                            </td>

                                                            <td>
                                                                <input name="qty[]" value="0" id="qty1" type="text" class="form-control input-small rightJustified" required>
                                                            </td>
                                                            <td><input name="stock[]" value="" id="stock1" type="text" class="form-control input-small rightJustified" readonly></td>
                                                            <td><input name="sat[]" id="sat1" type="text" class="form-control input-small"></td>
                                                            <!-- <td>
                                                                <a class="btn default" id="ViewDetailPaket1" data-toggle="modal" onclick="getPaket(this.id)"> <i class="fa fa-search"></i></a>
                                                            </td> -->
                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
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
                        <!-- tab1-->
                    </div>
                    <!--tab-->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well">
                                <button type="button" onclick="save()" class="btn blue" id="save_btn1"><i class="fa fa-save"></i> Simpan</button>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
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

<!-- Modal Here -->
<div class="modal fade" id="bufferStock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Daftar Barang Buffer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <!-- <form id="frmbuffer"> -->
                        <div class="portlet box blue">
                            <div class="portlet-title" style="padding: 2px 10px 2px 10px;">
                                <div class="d-flex justify-content-between align-items-center" style="font-size: 18px; font-weight: 400;">
                                    <span class="caption mb-0 d-flex align-items-center">
                                        <i class="fa fa-folder me-1"></i>Daftar Barang Buffer
                                    </span>
                                    <span>
                                        <!-- <button type="submit" class="btn blue" onclick="terapkanbuffer()"><i class="fa fa-check"></i> terapkan</button> -->
                                        <button type="submit" class="btn btn-dark" onclick="javascript:window.open(_urlcetakBufferStock(),'_blank');"><i class="fa fa-print"></i> Cetak</button>

                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->
                                <div class="form-horizontal form-bordered1">
                                    <div class="form-body">
                                        <div class="tabbable tabbable-custom tabbable-full-width">
                                            <!-- <div class="tab-content">
                                                <div class="tab-pane active" id="tab1"> -->
                                            <!-- First Row -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div style="overflow-x: auto;">
                                                        <div class="datatable-example">
                                                            <table id="bufferStockTable" class="display datatable table table-hover" style="width:100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>

                                                                        </th>
                                                                        <th>Kode item</th>
                                                                        <th>Nama Barang</th>
                                                                        <th>Satuan</th>
                                                                        <th>Tanggal Beli terakhir</th>
                                                                        <th>Stok Akhir</th>
                                                                        <th>stok min</th>
                                                                        <th>stok max</th>
                                                                        <th>Min Beli</th>
                                                                        <th>MOQ Principle</th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    var cabang = "<?= $cabang ?>";
    var idrow = 2;
    // Add table Elementt current row
    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        // var td6 = x.insertCell(5);

        var akun = "<select name='kode[]' id=kode" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control input-xlarge' required><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrowx + ")'  id=qty" + idrowx +
            " value='0'  type='text' class='form-control input-small rightJustified ' required>";
        td3.innerHTML = "<input name='stock[]'  readonly  id=stock" + idrowx + " value=''  type='text' class='form-control input-small rightJustified'  >";
        td4.innerHTML = "<input name='sat[]'    id=sat" + idrowx + " type='text' class='form-control input-small' >";
        // td5.innerHTML = "<a class='btn default' id=ViewDetailPaket" + idrowx +
        //     " data-toggle='modal' onclick='getPaket(this.id)'><i class='fa fa-search'></i></a>";
        td5.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
        getbarang()
    }

    //Delete Table Element Current Row
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    //Get Search barang filter
    function getbarang() {
        var cabangTujuan = $('#cabang_tujuan').val();
        initailizeSelect2_barang_filterCabang(cabangTujuan);
    }

    // Show data barang
    function showbarangname(str, id) {
        var cabangtujuan = $('#cabang_tujuan').val();
        var cabangasal = $('#cabang_asal').val();
        $.ajax({
            url: "<?= base_url('GetDataBarangFilterCabang/') ?>" + str + "/" + cabangtujuan,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.satuan == "") {
                    $('#sat' + id).val(data.satuan);
                } else {
                    $('#sat' + id).val(data.satuan);
                }

            }
        });
        $.ajax({
            url: "<?= base_url('GetDataBarangFilterCabang/') ?>" + str + "/" + cabangasal,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data != null) {
                    if (data.stok == "") {
                        $('#stock' + id).val(data.stok);
                    } else {
                        $('#stock' + id).val(data.stok);
                    }
                } else {
                    $('#stock' + id).val('0');
                }
            }
        });
    }

    //Show Paket perbarang
    function getPaket(id) {
        var vid = id.substring(15);
        var item = document.getElementById("kode" + vid).value;
        var qty = document.getElementById("qty" + vid).value;
        var param = item + '/' + qty;
        if (item == "") {
            swal('PERMINTAAN MUTASI REGION', 'barang tidak boleh kosong ...', '');
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

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PERMOHONAN MUTASI REGION', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn1").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PERMOHONAN MUTASI REGION', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Save Data Permohonan
    function save() {
        var cabangTujuan = $('#cabang_tujuan').val();
        var cabangasal = $('#cabang_asal').val();
        var sts = $('[name="sts"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            qty = row.cells[1].children[0].value;
            satuan = row.cells[3].children[0].value;
            if (sts != 3) {
                if (kode == "" || qty == "0" || satuan == "") {
                    swal('PERMINTAAN MUTASI REGION', 'Data di baris ke ' + i + ' Tidak Boleh kosong', '');
                    $('#save_btn1').attr('disabled', true);
                } else {
                    $('#save_btn1').attr('disabled', false);
                }
            }


        }
        if (cabangTujuan == null || cabangasal == null) {
            swal('PERMINTAAN MUTASI REGION', 'REGION tujuan atau REGION asal belum diisi ...', '');
        } else if ($('#save_btn1').prop('disabled') == true) {} else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/permohonanmutasicabangsave') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "PERMINTAAN MUTASI REGION",
                        html: "<p> No. PERMINTAAN MUTASI   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        window.open(_urlcetak(data));
                        location.href = "<?= BASEURL . 'inventory/transaksi/permohonanmutasicabang' ?>";
                    });

                },
                error: function(data) {
                    swal('PERMINTAAN MUTASI REGION', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function _urlcetak(data) {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'inventory/transaksi/permohonanmutasicabangcetak/' + data;
    }

    $(document).ready(function() {
        $('#bufferStockTable').DataTable({
            ajax: '<?= base_url('pembelian/transaksi/prapembelianbufferstocktable') ?>',
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
        });
        getbarang()
    });

    var tampungData = [];

    function tampungdata() {
        const checkboxes = document.querySelectorAll('#checkboxes');
        const kodeitem = event.target.value;
        const isChecked = event.target.checked;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        if (isChecked) {
            // tambahkan data ke array tampungData
            const data = {
            kodeitem: kodeitem,
            namabarang: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(3)`).text(),
            satuan: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(4)`).text(),
            tglbeliakhir: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(5)`).text(),
            stok: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(6)`).text(),
            stok_min: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(7)`).text(),
            selisih: $(`tr:has(input[value="${kodeitem}"]) td:nth-child(9)`).text(),
            };
            tampungData.push(data);
            if(tampungData.length > 1){
                tambah()
            }
            tambahData(data.kodeitem, data.namabarang, data.satuan, data.stok, data.stok_min, data.selisih,tampungData.length);

        } else {
            // hapus data dari array tampungData
            const index = tampungData.findIndex((item) => item.kodeitem === kodeitem);
            if (index !== -1) {
            tampungData.splice(index, 1);
            }
            // Call deleteRow function
            // const row = event.target.parentNode.parentNode;
            // deleteRow(row);
            for (var i = 1; i < rowCount; i++) {
                var row = table.rows[i];
                kodedata = row.cells[0].children[0].value;
                if (kodedata == kodeitem) {
                    document.getElementById("datatable").deleteRow(i);
                }
            }
        }
        
    }

    // Fungsi untuk menambahkan data ke datatable
    function tambahData(kodeitem, namabarang, satuan, stok, stok_min, qtybeli,  index) {
        // Ambil datatable
        var table = $('#datatable').DataTable();
        var kodeitem = kodeitem;
        var namabarang= namabarang;
        var satuan = satuan;
        var stok =   stok;
        var stok_min =  stok_min;
        var qtybeli =  qtybeli;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        x = rowCount - 1;
        for (var i = 1; i <= rowCount; i++) {
            var row = table.rows[i];
            kodedata = row.cells[0].children[0].value;
            if (kodedata =="") {
                var barang = $("<option selected></option>").val(kodeitem).text(namabarang);
                $('#kode' + i).append(barang).trigger('change');

                document.getElementById("qty" + i).value = qtybeli;
                // document.getElementById("stok_min" + i).value = stok_min;
                document.getElementById("stock" + i).value = stok;
                document.getElementById("sat" + i).value = satuan;
                // document.getElementById("harga" + i).value = 0;
            }

        }
    }

    //Function Buffer Stock
    function _urlcetakBufferStock() {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'pembelian/transaksi/prapembelianCetakBufferStock';
    }
</script>