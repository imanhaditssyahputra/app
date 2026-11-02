<?= main_header('Inventory', 'Approval Mutasi Barang Antar Gudang') ?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>*Data Baru
                </div>


            </div>

            <div class="portlet-body form">
                <form id="frmpenjualan" class="form-horizontal" method="post">
                    <div class="form-body">
                        <div class="tabbable tabbable-custom tabbable-full-width">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab1" data-toggle="tab">
                                        <i class="fa fa-file"></i>
                                        Approval Mutasi Barang Cabang
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row"> <label class="col-md-3 control-label pt-2">Tanggal</label>
                                                    <div class="col-md-4">
                                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?php echo date('Y-m-d');?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">No. Bukti</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" readonly value="">
                                                            <input type="text" placeholder="Otomatis" class="form-control input-medium" value="" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">Gudang terima</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group position-reletive">
                                                            <select id="gudang_terima" name="gudang_terima" class="form-control input-medium select_gudang_filter" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                                                <option>---Pilih Cabang</option>
                                                            </select>
                                                            <span class="input-group-btn position-absolute start-100 h-100 top-0">
                                                                <a class="btn green" onclick="showmg()"><i class="fa fa-refresh"></i></a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">Gudang kirim</label>
                                                    <div class="col-md-4">
                                                        <input type="text" id="ngudang_kirim" name="ngudang_kirim" class="form-control input-medium" value="" readonly>
                                                        <input type="hidden" id="gudang_kirim" name="gudang_kirim" class="form-control input-medium" value="" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">Keterangan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No. Mutasi</label>
                                                            <div class="col-md-6">
                                                                <div class="input-group position-reletive">
                                                                    <select name="nomutasi" id="nomutasi" class="form-control input-medium select2">
                                                                        <option value="">---Tanpa Mutasi---</option>
                                                                    </select>
                                                                    <span class="input-group-btn position-absolute start-100 h-100 top-0">
                                                                        <a class="btn blue" onclick="getmc()"><i class="fa fa-download"></i></a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No Permintaan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="nomohon" id="nomohon" value="" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="background-color: #e6f7ff; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 8px 2px; height: auto; overflow-x: scroll; padding: 5px; width: auto;">
                                        <div class="col-md-12">

                                            <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable" style="width: 130%">
                                                <thead>
                                                    <tr>
                                                        <th width="30%" style="text-align: center">Kode/Nama Barang</th>
                                                        <th width="15%" style="text-align: center">No Batch</th>
                                                        <th width="15%" style="text-align: center">Tanggal EXP</th>
                                                        <th width="10%" style="text-align: center">qty kirim</th>
                                                        <th width="10%" style="text-align: center">sat</th>
                                                        <th width="3%"></th>
                                                    </tr>
                                                    <thead>
                                                    <tbody>
                                                        <tr>
                                                            <td width="30%">
                                                                <select name="kode[]" id="kode1" class="form-control" readonly>
                                                                    <option value="">--- Pilih Barang ---</option>
                                                                </select>
                                                            </td>
                                                            <td width="15%">
                                                                <input name="no_batch[]" readonly="" value="" id="no_batch1" type="text" class="form-control rightJustified">
                                                            </td>
                                                            <td width="15%"><input name="tgl_exp[]" value="" id="tgl_exp1" type="text" readonly class="form-control rightJustified">
                                                            </td>

                                                            <td width="10%">
                                                                <input name="qty[]" value="" onchange="check_qty(this.value, 1)" id="qty1" type="text" class="form-control rightJustified" readonly>
                                                            </td>
                                                            <td width="10%"><input name="sat[]" id="sat1" type="text" class="form-control " onkeypress="return tabE(this,event)" readonly>
                                                            </td>
                                                            <td width="3%">
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-xs-9">
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
    </div>
</div>



<script>
    var cabang = "<?= $cabang?>";

    //Add Table Element
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
        var td6 = x.insertCell(5);

        var akun = "<select name='kode[]' id=kode" + idrow + " class=' form-control' readonly><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='no_batch[]'  readonly  value='' id=no_batch" + idrow +
            " type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrow +
            " value=''  type='text' readonly class='form-control rightJustified'  >";
        td4.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrow + ")'  id=qty" + idrow +
            " onchange='totalline(" + idrow + ")' value=''  type='text' class='form-control rightJustified' readonly>  ";
        td5.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' readonly>";
        td6.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
    }

    //Delete Table Element Current Row
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    //show data Mutasi Gudang
    function showmg() {
        var xhttp;
        var str = $('[name="gudang_terima"]').val();

        if (str == "") {
            document.getElementById("nomutasi").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("nomutasi").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('inventory/transaksi/persetujuanmutasigudang/showmg/') ?>" + str, true);
        xhttp.send();
    }

    // GET data Header dan item barang
    function getmc() {
        var str = $('[name=nomutasi]').val();
        if (str == "") {
            hapus();
            $('[id=kode1]').val('');
            $('[id=qty1]').val('');
            $('[id=sat1]').val('');
            $('[id=stock1]').val('');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/persetujuanmutasigudang/getdataandcheckqty/') ?>" + str,
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

                        document.getElementById("no_batch" + x).value = data[i].no_batch;
                        document.getElementById("tgl_exp" + x).value = data[i].tanggal_exp;
                        document.getElementById("qty" + x).value = data[i].qty;
                        document.getElementById("sat" + x).value = data[i].satuan;
                    }
                }
            });
            $.ajax({
                url: "<?= base_url('inventory/transaksi/persetujuanmutasigudang/getheaderacc/') ?>" + str,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {

                        x = i + 1;
                        $('#nomohon').val(data[i].nomohon);
                        $('#ngudang_kirim').val(data[i].nama);
                        $('#gudang_kirim').val(data[i].gudang_asal);
                        $('#keterangan').val(data[i].keterangan);
                    }
                }
            });
        }
    }
    
    //Save data persetujuan mutasi gudang
    function save() {
        var gudangasal = $('[name="gudang_kirim"]').val();
        var gudangtujuan = $('[name="gudang_terima"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (gudangasal == "" || gudangtujuan == "") {
            swal('APPROVAL MUTASI GUDANG', 'Gudang belum diisi ...', '');
        } else {
            $.ajax({
                url: '<?= base_url('inventory/transaksi/persetujuanmutasigudang/save/1') ?>',
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "APPROVAL MUTASI GUDANG",
                        html: "<p> No. Mutasi   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        location.href = "<?= BASEURL . 'inventory/transaksi/persetujuanmutasigudang' ?>";
                    });

                },
                error: function(data) {
                    swal('APPROVAL MUTASI GUDANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }
</script>