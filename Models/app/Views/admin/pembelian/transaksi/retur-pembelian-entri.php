<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_retur')->where('idretur', $id)->get()->getRow();
    $table_dataD = $db->table('ap_returdetil')->where('koderetur', $table_data->koderetur);
    $table_data_detail = $table_dataD->get()->getResult();
    $table_dataDRow = $table_dataD->get()->getRow();
}
isset($table_data) ? main_header('Pembalian', 'Retur', 'Daftar Retur Pembelian', 'View Retur', 'pembelian/transaksi/returpembelian') : main_header('Pembalian', 'Retur', 'Daftar Retur Pembelian', 'Entri Retur', 'pembelian/transaksi/returpembelian')
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

<div class="row">
    <div class="col-md-12">
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
                <form id="frmpembelian" class="form-horizontal" method="post">
                    <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->kodepu : 0 ?>">
                    <div class="form-body">
                        <div class="tabbable tabbable-custom tabbable-full-width">
                            <nav>
                                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Retur</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Info</button>

                                </div>
                            </nav>
                            <div class="tab-content p-3 border bg-light" id="nav-tabContent">
                                <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <!-- First Row -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">Supplier *</label>
                                                    <div class="col-md-9">
                                                        <select id="supp" name="supp" class="form-control input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                                            <option>----PILIH-----</option>
                                                            <?php
                                                            $suplier = $db->table('ap_supplier')->get()->getResult();
                                                            foreach ($suplier as $key => $value) { ?>
                                                                <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                                        echo "selected=true";
                                                                                                    } ?>><?= $value->nama ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <span class="input-group-btn start-100 top-0">
                                                            <a class="btn green" onclick="showpu()"><i class="fa fa-refresh"></i></a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">No. Retur #</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->koderetur : $nomor ?>" value="<?= isset($table_data) ? $table_data->koderetur : $nomor ?>" readonly>
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
                                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglretur)) : date('Y-m-d') ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">No. Faktur</label>
                                                    <div class="col-md-9">
                                                        <?php if (isset($table_data)) { ?>
                                                            <input type="text" name="kodepu" id="kodepu" value="<?= isset($table_data) ? $table_data->kodepu : '' ?>" class="form-control input-medium" readonly>
                                                        <?php } else { ?>
                                                            <select name="kodepu" id="kodepu" class="form-control input-medium select2">
                                                                <option value="">---Tanpa Faktur---</option>
                                                            </select>
                                                            <span class="input-group-btn start-100 top-0">
                                                                <a class="btn green" onclick="getpu()"><i class="fa fa-refresh"></i></a>
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Third Row -->
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
                                                            <th style="text-align: center">Tanggal EXP</th>
                                                            <th style="text-align: center">Harga</th>
                                                            <th style="text-align: center"></th>
                                                            <th style="text-align: center">Diskon</th>
                                                            <th style="text-align: center">Total Harga</th>
                                                            <th style="text-align: center"></th>

                                                        </tr>
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
                                                                <tr>
                                                                    <td>
                                                                        <select name="kode[]" id="kode<?= $no ?>" class="barang_filter form-control input-large">
                                                                            <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                        </select>
                                                                    </td>

                                                                    <td><input name="qty[]" value="<?= $value->qtyretur; ?>" id="qty<?= $no ?>" type="text" class="form-control input-xsmall rightJustified"></td>
                                                                    <td><input name="sat[]" id="sat<?= $no ?>" type="text" class="form-control input-xsmall" value="<?= $value->satuan; ?>" readonly></td>
                                                                    <td><input name="no_batch[]" id="no_batch<?= $no ?>" type="text" class="form-control input-small" value="<?= $value->no_batch; ?>" readonly></td>
                                                                    <td><input name="tgl_exp[]" id="tgl_exp<?= $no ?>" type="text" class="form-control input-small" value="<?= $value->tanggal_exp; ?>" readonly></td>
                                                                    <td><input name="harga[]" value="<?= $value->hargabeli; ?>" id="harga<?= $no ?>" type="text" class="form-control input-small rightJustified" readonly></td>
                                                                    <td><a class="btn default" id="lupharga<?= $no ?>" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                                    <td><input name="disc[]" value="<?= $value->disc; ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified input-xsmall" readonly></td>
                                                                    <td><input name="jumlah[]" id="jumlah<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly></td>
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
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode1" class="barang_filter form-control input-large">
                                                                        <option value="">--- Pilih Barang ---</option>
                                                                    </select>
                                                                </td>

                                                                <td><input name="qty[]" onchange="totalline(1);total()" value="0" id="qty1" type="text" class="form-control input-xsmall rightJustified"></td>
                                                                <td><input name="sat[]" id="sat1" type="text" class="form-control input-xsmall" onkeypress="return tabE(this,event)" readonly></td>
                                                                <td><input name="no_batch[]" id="no_batch1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)" readonly></td>
                                                                <td><input name="tgl_exp[]" id="tgl_exp1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)" readonly></td>
                                                                <td><input name="harga[]" onchange="totalline(1)" value="0" id="harga1" type="text" class="form-control rightJustified input-small" readonly></td>
                                                                <td><a class="btn default" id="lupharga1" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                                <td><input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control rightJustified input-xsmall" readonly></td>
                                                                <td><input name="jumlah[]" id="jumlah1" ; type="text" class="form-control rightJustified input-small" onchange="total()" readonly></td>
                                                                <td>
                                                                    <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button>
                                                                </td>
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
                                                    <label class="control-label pt-2 col-md-3">PPN</label>
                                                    <div class="col-md-7">
                                                        <?php if (isset($table_data)) { ?>
                                                            <input type="text" name="sppn" id="sppn" value="<?= $table_data->sppn == 'Y' ? 'Ya' : 'Tidak' ?>" class="form-control input-medium" readonly>
                                                        <?php } else { ?>
                                                            <select name="sppn" id="sppn" class="form-select select2 input-medium" onchange="total()">
                                                                <option value="">Pilih</option>
                                                                <option value="Y">Ya</option>
                                                                <option value="T">Tidak</option>
                                                            </select>
                                                        <?php } ?>
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
                                                    <label class="control-label pt-2 col-md-3">Keterangan</label>
                                                    <div class="col-md-9">
                                                        <textarea row="3" class="form-control input-xlarge" placeholder="" name="keterangan" id="keterangan" maxlength="100"><?= isset($table_data) ? $table_data->keterangan : '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!--tab-->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="wells">
                                    <?php if (!isset($table_data)) { ?>
                                        <button type="button" onclick="save()" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                                    <?php } ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-pencil-square-o"></i> Data Baru</button>
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
                                            <td width="40%"><strong>DISKON</strong></td>
                                            <input type="hidden" name="vdiskon" id="_vvdiskon">
                                            <td width="1%"><strong>:</strong></td>
                                            <td width="59" align="right"><strong><span id="_vdiskon"></span></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="40%"><strong>DPP</strong></td>
                                            <input type="hidden" name="vdpp" id="_vvdpp">
                                            <td width="1%"><strong>:</strong></td>
                                            <td width="59" align="right"><strong><span id="_dpp"></span></strong></td>
                                        </tr>
                                        <tr>
                                            <td width="40%"><strong>PPN</strong></td>
                                            <input type="hidden" name="vppn" id="_vvppn">
                                            <td width="1%"><strong>:</strong></td>
                                            <td width="59" align="right"><strong><span id="_vppn"></span></strong></td>
                                        </tr>

                                        <tr>
                                            <td width="40%"><strong>TOTAL</strong></td>
                                            <input type="hidden" name="vtotal" id="_vvtotal">
                                            <td width="1%"><strong>:</strong></td>
                                            <td width="59" align="right"><strong><span id="_vtotal"></span></strong></td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var cabang = "<?= $cabang ?>";

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
        var td8 = x.insertCell(7);
        var td9 = x.insertCell(8);
        var td10 = x.insertCell(9);
        var akun = "<select name='kode[]' id=kode" + idrow + " class=' form-control' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]'    id=qty" + idrow + " onchange='totalline(" + idrow +
            ")' value='1'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' readonly>";
        td4.innerHTML = "<input name='no_batch[]'    id=no_batch" + idrow + " type='text' class='form-control'readonly>";
        td5.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrow + " type='text' class='form-control' readonly>";
        td6.innerHTML = "<input name='harga[]'  id=harga" + idrow + " onchange='totalline(" + idrow +
            ") value='0'  type='text' class='form-control rightJustified' readonly>";
        td7.innerHTML = "<a class='btn default' id=lupharga" + idrow +
            " data-toggle='modal' href='#lupharga' onclick='getidharga(this.id)'><i class='fa fa-search'></i></a>";
        td8.innerHTML = "<input name='disc[]'   id=disc" + idrow + " onchange='totalline(" + idrow +
            ")' value='0'  type='text' class='form-control rightJustified' readonly >";
        td9.innerHTML = "<input name='jumlah[]' id=jumlah" + idrow +
            " type='text' class='form-control rightJustified' size='40%' readonly>";
        td10.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
    }

    //Delete Table Element Data Biaya lain - lain   
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        total();
    }

    //show data PB Pembelian
    function showpu() {
        var xhttp;
        var str = $('[name="supp"]').val();

        if (str == "") {
            document.getElementById("kodepu").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodepu").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('pembelian/transaksi/returpembelian/getlistpu/') ?>" + str, true);
        xhttp.send();
    }

    function getpu() {
        var xhttp;
        var str = $('[name=kodepu]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/returpembelian/postpu/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {

                    for (i = 0; i <= data.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;

                        var option = $("<option selected></option>").val(data[i].kodeitem).text(data[i]
                            .namabarang);
                        $('#kode' + x).append(option).trigger('change');

                        document.getElementById("qty" + x).value = data[i].qtypu;
                        document.getElementById("sat" + x).value = data[i].satuan;
                        document.getElementById("no_batch" + x).value = data[i].no_batch;
                        document.getElementById("tgl_exp" + x).value = data[i].tanggal_exp;
                        document.getElementById("harga" + x).value = number_format(data[i].hargabeli);
                        document.getElementById("disc" + x).value = data[i].disc;
                        //header
                        var optionGudang = $("<option selected></option>").val(data[i].gudang).text(data[i].nama);
                        $('#gudang').append(optionGudang).trigger('change');
                        $('[name="sppn"]').val(data[i].sppn).trigger('change');
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
            harga = row.cells[5].children[0].value;
            diskon = row.cells[7].children[0].value;
            var jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
            var harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));
            var diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));

            tjumlah = tjumlah + eval(jumlah1 * harga1);

            diskon = eval((diskon1 / 100) * jumlah1 * harga1);

            tdiskon = tdiskon + diskon;


        }

        var cppn = document.getElementById("sppn").value;
        if (cppn == "Y" || cppn == "Ya") {
            tppn = (tjumlah - tdiskon) * 0.11;
        } else {
            tppn = 0;
        }


        document.getElementById("_vsubtotal").innerHTML = number_format(tjumlah);
        document.getElementById("_vdiskon").innerHTML = number_format(tdiskon);
        document.getElementById("_dpp").innerHTML = number_format(tjumlah - tdiskon);
        document.getElementById("_vppn").innerHTML = number_format(tppn);
        document.getElementById("_vtotal").innerHTML = number_format(tjumlah - tdiskon + tppn);

        document.getElementById("_vvdiskon").value = number_format(tdiskon);
        document.getElementById("_vvdpp").value = number_format(tjumlah - tdiskon);
        document.getElementById("_vvppn").value = number_format(tppn);
        document.getElementById("_vvtotal").value = number_format(tjumlah - tdiskon + tppn);
    }

    function totalline(id) {
        var table = document.getElementById('datatable');
        var row = table.rows[id];
        jumlah = row.cells[1].children[0].value * Number(row.cells[5].children[0].value.replace(/[^0-9\.]+/g, ""));
        diskon = (row.cells[7].children[0].value / 100) * jumlah;
        tot = jumlah - diskon;
        row.cells[8].children[0].value = number_format(tot);
        total();
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('RETUR PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('RETUR PEMBELIAN', 'Gunakan Tab untuk Next dan Mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var total = $('#_vtotal').text();
        if (noform == "" || total == "" /*|| total == "0.00"*/ ) {
            swal('RETUR PEMBELIAN', 'Data Belum Lengkap/Belum ada transaksi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/returpembeliansave') ?>",
                data: $('#frmpembelian').serialize(),
                type: 'post',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    var parse = JSON.parse(data);
                    if (parse.status == 1) {
                        swal({
                            title: "RETUR PEMBELIAN",
                            html: "<p> No. Retur   : <b>" + parse.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            // location.href = "<?php echo base_url() ?>pembelian_retur";
                            window.location.href = "<?= base_url('pembelian/transaksi/returpembelian') ?>";
                        });
                    } else {
                        swal('RETUR PEMBELIAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('RETUR PEMBELIAN', 'Data gagal disimpan ...', '');
                }
            });
        }
    }
</script>