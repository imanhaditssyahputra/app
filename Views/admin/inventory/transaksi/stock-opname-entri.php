<?php
$db = db_connect();
if ($data != []) {
    main_header('Inventory', 'Stock Opname', 'Daftar Stok Opname', 'View Stok Opname', 'inventory/transaksi/stokopname');
    $DataOrder = 'Data View';
} else {
    main_header('Inventory', 'Stock Opname', 'Daftar Stok Opname', 'Entri Stok Opname', 'inventory/transaksi/stokopname');
    $DataOrder = 'Data Baru';
}
?>


<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>*<?= $DataOrder ?>
        </div>


    </div>

    <div class="portlet-body form">
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <input type="hidden" value="<?= ($data != []) ? $data->kode_so : 0 ?>" name="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fa fa-file"></i>
                                Stok Opname
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tgl)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Petugas</label>
                                            <div class="col-md-7">
                                                <input type="text" name="pic" class="form-control input-medium" value="<?= ($data != []) ? $data->kodeuser : '' ?>">
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
                                                <?php if ($data != []) { ?>
                                                    <input type="text" class="form-control input-medium" name="gudang" id="gudang" value="<?= ($data != []) ? $data->nama_gudang : '' ?>" readonly>
                                                <?php } else { ?>
                                                    <select id="gudang" name="gudang" class="form-control input-medium select_gudang_filter" data-placeholder="Pilih..." onchange="getBarang(this.value)">
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Nama Barang</label>
                                            <div class="col-md-9">
                                                <?php if ($data != []) { ?>
                                                    <input type="text" class="form-control input-large" name="barang" id="barang" value="<?= ($data != []) ? $data->namabarang : '' ?>" readonly>
                                                <?php } else { ?>
                                                    <select id="barang" name="barang" class="form-control input-large barang_filter" data-placeholder="Pilih Barang..." onchange="showbarangname(this.value)">
                                                    </select>
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
                                            <label class="control-label pt-2 col-md-3">No. SO <font color="red">*</font></label>
                                            <div class="col-md-6">
                                                <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= ($data != []) ? $data->kode_so : $nomor ?>" readonly>
                                                <input type="text" class="form-control input-medium" placeholder="Nomor Auto" value="<?= ($data != []) ? $data->kode_so : '' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Satuan</label>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="sat" id="sat" class="form-control input-medium" value="<?= ($data != []) ? $data->sat : '' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- fourth Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">No Batch</th>
                                                <th style="text-align: center"></th>
                                                <th style="text-align: center">tanggal exp</th>
                                                <th style="text-align: center">Kuantitas</th>
                                                <th></th>
                                            </thead>

                                            <tbody align="center">
                                                <?php if ($DetailItem != []) {
                                                    $no = 0;
                                                    foreach ($DetailItem as $key) {
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td><input name="no_batch[]" value="<?= $key->no_batch; ?>" id="no_batch<?= $no ?>" type="text" class="form-control input-xlarge rightJustified"></td>
                                                            <td><a class="btn default" id="lupharga<?= $no ?>" data-toggle="modal" href="#lupbatch" onclick="getbatch(this.id)"><i class="fa fa-search"></i></a></td>
                                                            <td><input name="tgl_exp[]" value="<?= $key->tanggal_exp; ?>" id="tgl_exp<?= $no ?>" type="text" placeholder='YYYY-MM-DD' class="form-control input-medium rightJustified"></td>
                                                            <td><input name="qty[]" oninput="totalline()" value="<?= $key->qty_stok; ?>" id="qty<?= $no ?>" type="text" class="form-control input-large rightJustified"></td>
                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } else { ?>
                                                    <tr>
                                                        <td><input name="no_batch[]" value="" id="no_batch1" type="text" class="form-control input-xlarge rightJustified"></td>
                                                        <td>
                                                            <a class="btn default" id="lupharga1" data-toggle="modal" onclick="getbatch(this.id)"><i class="fa fa-search"></i></a>
                                                        </td>
                                                        <td><input name="tgl_exp[]" value="" id="tgl_exp1" type="date()" placeholder='YYYY-MM-DD' class="form-control input-medium rightJustified"></td>
                                                        <td><input name="qty[]" oninput="totalline()" value="0" id="qty1" type="text" class="form-control input-large rightJustified"></td>
                                                        <td>
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
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
                                        <div class="col-md-3">
                                            <div class="panel-footer clearfix"> <span class="pull-right">
                                                    <input type="text" name="total" readonly id="total_qty" class="form-control" placeholder="Total kuantitas"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--tab-->
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="well">
                                        <button type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
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
<!-- Modal Nobatch -->
<div class="modal fade" id="lupbatch" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbatch">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory Nobatch/ED</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarnobatch">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" onclick="reloadBatch()" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
                </div>
        </div>
    </div>
</div>

<script>
    var cabang = '<?= $cabang ?>';
    var idrow = 2;

    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var akun = "<input name='no_batch[]'    id=no_batch" + idrowx + " value=''  type='text' class='form-control rightJustified'  >";
        td1.innerHTML = akun;
        td2.innerHTML = "<a class='btn default' id=lupharga" + idrowx + " data-toggle='modal' onclick='getbatch(this.id)'><i class='fa fa-search'></i></a>";
        td3.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrowx + " value='' placeholder='YYYY-MM-DD'  type='date()' class='form-control rightJustified'  >";
        td4.innerHTML = "<input name='qty[]'    id=qty" + idrowx + " oninput='totalline()' value=''  type='text' class='form-control rightJustified'  >";
        td5.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
    }

    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    function getBarang(data) {
        initailizeSelect2_barang_filter(cabang, data);
    }

    function showbarangname(str) {
        var gudang = $('#gudang').val();
        $.ajax({
            url: "<?= base_url('inventory/transaksi/stokopname/showbarang/') ?>" + str + '/' + gudang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#sat').val(data.satuan);
            }
        });
    }

    //Show Batch perbarang
    function getbatch(id) {
        var vid = id.substring(8);
        document.getElementById("nopilihbatch").value = vid;
        var gudang = document.getElementById("gudang").value;
        var item = document.getElementById("barang").value;
        var xhttp;
        if (item == "" && gudang == "") {
            document.getElementById("daftarnobatch").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupbatch').modal('show');

                document.getElementById("daftarnobatch").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('getbatchSo/') ?>" + item + "/" + gudang, true);
        xhttp.send();
    }

    //Entry batch
    function post_batch(v1, v2, v3, v4) {

        id = document.getElementById("nopilihbatch").value;
        var kode = $('#barang').val();

        if (v1 == kode) {
            document.getElementById("no_batch" + id).value = v2;
            document.getElementById("tgl_exp" + id).value = v3;
            $('#lupbatch').modal('hide');
        } else {
            swal('STOCK OPNAME', 'barang tidak Sesuai  ...', '');
        }
        totalline(id);
    }

    function totalline() {
        var arr = document.getElementsByName('qty[]');
        var tot = 0
        for (var i = 0; i < arr.length; i++) {
            if (parseInt(arr[i].value))
                tot += parseInt(arr[i].value);
        }
        $('#total_qty').val(tot);
    }

    function save() {
        var noform = $('[name="pic"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var barang = $('[name="barang"]').val();
        if (noform == "" && barang == "") {
            swal('STOK OPNAME', 'Petugas atau Barang belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/stokopname/save') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    swal({
                        title: "STOK OPNAME",
                        html: "<p> No. SO   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('inventory/transaksi/stokopname') ?>";
                    });

                },
                error: function(data) {
                    swal('STOK OPNAME', 'Data gagal disimpan ...', '');
                }
            });
        }
    }


    $(document).ready(function() {
        totalline();
		initailizeSelect2_gudangFilter(cabang)
    })
</script>