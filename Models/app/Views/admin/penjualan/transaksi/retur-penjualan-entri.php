<?php
$db = db_connect();
if ($data != []) {
    main_header('Penjualan', 'Penerimaan', 'Daftar Retur Penjualan', ' Edit Retur', 'penjualan/transaksi/returpenjualan');
    $DataOrder = 'Data Edit';
    $CSRow = $db->table("ar_customer")->getWhere(['kode' => $data->kodecust])->getRow();
} else {
    main_header('Penjualan', 'Penerimaan', 'Daftar Retur Penjualan', ' Entri Retur', 'penjualan/transaksi/returpenjualan');
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
        <form id="frmPenjualan" class="form-horizontal" method="post">
            <input type="hidden" value="<?= ($data != []) ? $data->idretur : 0 ?>" name="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">
                                Retur
                            </a>
                        </li>
                        <li class="">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="home" aria-selected="true">
                                Info
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Pelanggan</label>
                                            <div class="col-md-9">
                                                <select id="cust" name="cust" class="form-select input-medium select2" data-placeholder="Pilih...">
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
                                                    <a class="btn green" onclick="showsi()"><i class="fa fa-refresh"></i></a>
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
                                                <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= ($data != []) ? $data->koderetur : $nomor ?>" readonly>
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
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tglretur)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Faktur</label>
                                            <div class="col-md-9">
                                                <select name="kodesi" id="kodesi" class="form-control input-medium select2">
                                                    <option value="">---Tanpa SI---</option>
                                                    <?php if ($data != []) { ?>
                                                        <option value="<?= $data->kodesi ?>" selected><?= $data->kodesi ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn blue" onclick="getsi()"><i class="fa fa-download"></i></a>
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
                                            <label class="control-label pt-2 col-md-3">Gudang *</label>
                                            <div class="col-md-9">
                                                <select id="gudang" name="gudang" class="form-select input-medium select2" data-placeholder="Pilih...">
                                                    <option value="">--PLILIH --</option>
                                                    <?php foreach ($gudang as $row) { ?>
                                                        <option value="<?= $row->kode ?>" <?= $data != [] && $data->gudang == $row->kode ? 'selected' : '' ?>><?= $row->nama ?></option>
                                                    <?php } ?>
                                                </select>
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
                                                                <select name="kode[]" id="kode1" class=" form-select input-large">
                                                                    <option value="<?= $key->kodeitem; ?>"><?= ($barang != '') ? $barang->namabarang : ''; ?></option>
                                                                </select>
                                                            </td>
                                                            <td> <input name="qty[]" value="<?= $key->qtyretur; ?>" id="qty<?= $no; ?>" type="text" class="form-control input-xsmall rightJustified"></td>
                                                            <td> <input name="sat[]" id="sat<?= $no; ?>" type="text" class="form-control input-xsmall" value="<?= $key->satuan; ?>" readonly></td>
                                                            <td> <input name="no_batch[]" id="no_batch<?= $no; ?>" type="text" value="<?= $key->no_batch; ?>" class="form-control input-small"></td>
                                                            <td> <input name="tgl_exp[]" id="tgl_exp<?= $no; ?>" type="text" value="<?= $key->tanggal_exp; ?>" class="form-control input-small"></td>
                                                            <td> <input name="harga[]" value="<?= $key->hargajual; ?>" id="harga<?= $no; ?>" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            <td> <a class="btn default" id="lupharga<?= $no; ?>" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                            <td> <input name="disc[]" value="<?= $key->disc; ?>" id="disc<?= $no; ?>" type="text" class="form-control input-xsmall rightJustified " readonly></td>
                                                            <td> <input name="jumlah[]" id="jumlah<?= $no; ?>" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            <td> <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
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
                                                            <select name="kode[]" id="kode1" class=" form-select input-large">
                                                                <option value="">--- Pilih Barang ---</option>
                                                            </select>
                                                        </td>
                                                        <td> <input name="qty[]" onchange="totalline(1);total()" value="1" id="qty1" type="text" class="form-control input-xsmall rightJustified"></td>
                                                        <td> <input name="sat[]" id="sat1" type="text" class="form-control input-xsmall" onkeypress="return tabE(this,event)" readonly></td>
                                                        <td> <input name="no_batch[]" id="no_batch1" type="text" class="form-control input-small"></td>
                                                        <td> <input name="tgl_exp[]" id="tgl_exp1" type="text" class="form-control input-small"></td>
                                                        <td> <input name="harga[]" onchange="totalline(1)" value="0" id="harga1" type="text" class="form-control input-small rightJustified" readonly></td>
                                                        <td> <a class="btn default" id="lupharga1" data-toggle="modal" href="#lupharga" onclick="getidharga(this.id)"><i class="fa fa-search"></i></a></td>
                                                        <td> <input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control input-xsmall rightJustified " readonly></td>
                                                        <td> <input name="jumlah[]" id="jumlah1" ; type="text" class="form-control rightJustified input-small" onchange="total()" readonly></td>
                                                        <td> <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
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
                        <!-- tab1-->

                        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="profile-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">PPN</label>
                                            <div class="col-md-7">
                                                <input type="hidden" id="sppn" name="sppn" class="form-control" value="<?= ($data != []) ? $data->sppn : '' ?>" readonly>
                                                <input type="text" id="sppnmiror" name="sppnmiror" class="form-control input-medium" value="<?= ($data != []) ? (($data->sppn == 'Y') ? 'Ya' : 'Tidak') : ''  ?> " readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                                            <div class="col-md-9">
                                                <textarea row="3" class="form-control input-xlarge" placeholder="" name="keterangan" id="keterangan" maxlength="100"><?= ($data != []) ? $data->keterangan : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--tab2-->
                </div>
                <!--tab-->
                <div class="row">
                    <div class="col-md-8">
                        <div class="wells">
                            <button id="btnsimpan" type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>


                            <div class="btn-group">
                                <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fas fa-edit"></i> Data Baru</button>
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



<script>
    var cabang = "<?= $cabang; ?>";
    var rowtable = 2;

    function tambah() {
        var table = document.getElementById("datatable");
        var idrow = table.rows.length;

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
        td2.innerHTML = "<input name='qty[]'    id=qty" + idrow + " value='1'  type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='sat[]' readonly   id=sat" + idrow + " type='text' class='form-control' >";
        td4.innerHTML = "<input name='no_batch[]'  id=no_batch" + idrow + "  value=''  type='text' class='form-control rightJustified'>";
        td5.innerHTML = "<input name='tgl_exp[]'  id=tgl_exp" + idrow + " value=''  type='text' class='form-control rightJustified'>";
        td6.innerHTML = "<input name='harga[]' readonly id=harga" + idrow + " value='0'  type='text' class='form-control rightJustified'>";
        td7.innerHTML = "<a class='btn default' id=lupharga" + idrow +
            " data-toggle='modal' href='#lupharga' onclick='getidharga(this.id)'><i class='fa fa-search'></i></a>";
        td8.innerHTML = "<input name='disc[]'  readonly id=disc" + idrow + " value='0'  type='text' class='form-control rightJustified'  >";
        td9.innerHTML = "<input name='jumlah[]' readonly id=jumlah" + idrow +
            " type='text' class='form-control rightJustified' size='40%'>";
        td10.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        rowtable++;
    }

    //Delete Table Element Data Biaya lain - lain   
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        total();
    }

    function showsi() {
        var xhttp;
        var str = $('[name="cust"]').val();

        if (str == "") {
            document.getElementById("kodesi").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodesi").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/returpenjualan/showsi/') ?>" + str, true);
        xhttp.send();
    }

    function getsi() {
        var xhttp;
        var str = $('[name=kodesi]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/returpenjualan/getdatasi/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {

                    $('#sppn').val(data.DataHeader.sppn);
                    var optionPPN = data.DataHeader.sppn == 'Y' ? 'Ya' : 'Tidak';
                    $('#sppnmiror').val(optionPPN);

                    for (i = 0; i <= data.Dataitem.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;

                        var option = $("<option selected></option>").val(data.Dataitem[i].kodeitem).text(data.Dataitem[i]
                            .namabarang);
                        $('#kode' + x).append(option).trigger('change');

                        document.getElementById("qty" + x).value = data.Dataitem[i].qtysi;
                        document.getElementById("sat" + x).value = data.Dataitem[i].satuan;
                        document.getElementById("no_batch" + x).value = data.Dataitem[i].no_batch;
                        document.getElementById("tgl_exp" + x).value = data.Dataitem[i].tanggal_exp;
                        document.getElementById("harga" + x).value = data.Dataitem[i].hargajual;
                        document.getElementById("disc" + x).value = data.Dataitem[i].disc;
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

        var tot = tjumlah + tdiskon + tppn;

        document.getElementById("_vsubtotal").innerHTML = number_format(tjumlah);
        document.getElementById("_vdiskon").innerHTML = number_format(tdiskon);
        document.getElementById("_dpp").innerHTML = number_format(tjumlah - tdiskon);
        document.getElementById("_vppn").innerHTML = number_format(tppn);
        document.getElementById("_vtotal").innerHTML = number_format(tjumlah - tdiskon + tppn);

        document.getElementById("_vvdiskon").value = number_format(tdiskon);
        document.getElementById("_vvdpp").value = number_format(tjumlah - tdiskon);
        document.getElementById("_vvppn").value = number_format(tppn);
        document.getElementById("_vvtotal").value = number_format(tjumlah - tdiskon + tppn);

        if (tot > 0) {
            document.getElementById("btnsimpan").disabled = false;

        } else {
            document.getElementById("btnsimpan").disabled = true;

        }
    }

    function totalline(id) {
        var table = document.getElementById('datatable');
        var row = table.rows[id];
        jumlah = row.cells[1].children[0].value * row.cells[5].children[0].value;
        diskon = (row.cells[7].children[0].value / 100) * jumlah;
        tot = jumlah - diskon;
        row.cells[8].children[0].value = number_format(tot);
        total();
    }

    $("#btnsimpan").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENJUALAN RETUR', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENJUALAN RETUR', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var gudang = $('#gudang').val();
        if (noform == "" || gudang == "") {
            swal('RETUR PENJUALAN', 'No Retur atau gudang belum di isi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/returpenjualan/save') ?>",
                data: $('#frmPenjualan').serialize(),
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
                            title: "RETUR PENJUALAN",
                            html: "<p> No. Retur   : <b>" + parse.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('penjualan/transaksi/returpenjualan') ?>";
                        });
                    } else {
                        swal('RETUR PENJUALAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }

                },
                error: function(data) {
                    swal('RETUR PENJUALAN', 'Data gagal disimpan ...', '');


                }
            });
        }
    }
</script>