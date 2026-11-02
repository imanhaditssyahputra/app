<?= main_header('Inventory', 'Mutasi Barang Antar Gudang', 'Daftar Mutasi', 'Entri Mutasi Antar Gudang') ?>




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
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fa fa-file"></i>
                                Mutasi Barang Gudang
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Tanggal</label>
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
                                            <label class="col-md-3 control-label pt-2">Gudang Permintaan</label>
                                            <div class="col-md-6">
                                                <div class="input-group position-relative">
                                                    <select id="gudang_minta" name="gudang_minta" class="form-control input-medium select_gudang_filter" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                                        <option>---Pilih Cabang</option>
                                                    </select>
                                                    <span class="input-group-btn position-absolute start-100 top-0">
                                                        <a class="btn green" onclick="showmmg()"><i class="fa fa-refresh"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Gudang Pengirim</label>
                                            <div class="col-md-4">
                                                <input type="text" id="gudang_tujuan" name="gudang_tujuan" class="form-control input-medium" value="" readonly>
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
                                                    <label class="col-md-3 control-label pt-2">No. Permintaan</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group position-relative">
                                                            <select name="nomohon" id="nomohon" class="form-select input-medium select2">
                                                                <option value="">---Tanpa Permintaan---</option>
                                                            </select>
                                                            <span class="input-group-btn position-absolute start-100 top-0">
                                                                <a class="btn blue" onclick="getmmgandcheckqty()"><i class="fa fa-download"></i></a>
                                                            </span>
                                                        </div>
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
                                                <th width="2%" style="text-align: center"></th>
                                                <th width="15%" style="text-align: center">Tanggal EXP</th>
                                                <th width="10%" style="text-align: center">qty</th>
                                                <th width="10%" style="text-align: center">Stock</th>
                                                <th width="10%" style="text-align: center">sat</th>
                                                <th width="3%"></th>
                                            </tr>
                                            <thead>
                                            <tbody>
                                                <tr>
                                                    <td width="30%">
                                                        <select name="kode[]" id="kode1" class="barang_filter form-select" onchange="showbarangname(this.value, 1)">
                                                            <option value="">--- Pilih Barang ---</option>
                                                        </select>
                                                    </td>

                                                    <td width="15%">
                                                        <input name="no_batch[]" readonly="" value="" id="no_batch1" type="text" class="form-control rightJustified">
                                                    </td>
                                                    <td width="2%"><a class="btn default" id="lupharga1" onclick="showbatch(this.id)"><i class="fa fa-search"></i></a>
                                                    </td>
                                                    <td width="15%"><input name="tgl_exp[]" value="" id="tgl_exp1" type="text" readonly class="form-control rightJustified">
                                                    </td>

                                                    <td width="10%">
                                                        <input name="qty[]" value="0" onchange="check_qty(this.value, 1)" id="qty1" type="text" class="form-control rightJustified">
                                                        <input name="qtybatch[]" value="1" id="qtybatch1" type="hidden" class="form-control rightJustified">
                                                        <input name="qtymohon[]" value="1" id="qtymohon1" type="hidden" class="form-control rightJustified">
                                                    </td>
                                                    <td width="10%">
                                                        <input name="stock[]" oninput="totalline(1)" value="" id="stock1" type="text" class="form-control rightJustified" readonly>
                                                        <input name="qtylebih[]" value="1" id="qtylebih1" type="hidden" class="form-control rightJustified">
                                                    </td>
                                                    <td width="10%"><input name="sat[]" id="sat1" type="text" class="form-control " onkeypress="return tabE(this,event)">
                                                    </td>
                                                    <td width="3%">
                                                        <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-xs-9">
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
                                <a class="btn default print_laporan" onclick="javascript:window.open(_urlcetak_shipping(),'_blank');"><i class="fa fa-print"></i> Cetak Shipping</a>
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
        var td7 = x.insertCell(6);
        var td8 = x.insertCell(7);

        var akun = "<select name='kode[]' id=kode" + idrow + " onchange='showbarangname(this.value," + idrow +
            ")' class='barang_filter form-control' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='no_batch[]'  readonly  value='' id=no_batch" + idrow +
            " type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<a class='btn default' id=lupharga" + idrow +
            "  onclick='showbatch(this.id)'><i class='fa fa-search'></i></a>";
        td4.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrow +
            " value=''  type='text' readonly class='form-control rightJustified'  >";
        td5.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrow + ")'  id=qty" + idrow +
            " onchange='totalline(" + idrow +
            ")' value='0'  type='text' class='form-control rightJustified'>  <input name='qtybatch[]'    id=qtybatch" +
            idrow +
            "  value=''  type='hidden' class='form-control rightJustified'  >  <input name='qtymohon[]'    id=qtymohon" +
            idrow + "  value=''  type='hidden' class='form-control rightJustified'  > ";
        td6.innerHTML = "<input name='stock[]'  readonly  id=stock" + idrow + " oninput='totalline(" + idrow +
            ")' value='0'  type='text' class='form-control rightJustified'  > <input name='qtylebih[]'    id=qtylebih" +
            idrow + "  value=''  type='hidden' class='form-control rightJustified'  >";
        td7.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control' >";
        td8.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        idrow++;
        getbarang();
    }

    //Delete Table Element Current Row
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    //get barang filter
    function getbarang() {
        var gudangkirim = $('#gudang_kirim').val();
        initailizeSelect2_barang_filter(cabang, gudangkirim);
    }

    //show data Mohon Mutasi Gudang
    function showmmg() {
        var xhttp;
        var str = $('[name="gudang_minta"]').val();

        if (str == "") {
            document.getElementById("nomohon").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("nomohon").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('inventory/transaksi/mutasigudang/showmmg/') ?>" + str, true);
        xhttp.send();
    }

    //Show data barang
    function showbarangname(str, id) {
        var xhttp;
        var vid = id;
        var gudang = $('#gudang_kirim').val();
        $.ajax({
            url: "<?= base_url('inventory/transaksi/mutasigudang/showbarangmutasigudang/') ?>" + str + "/" + gudang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    swal('MUTASI GUDANG', 'Barang Tidak Sesuai dengan permohonan...', '');
                    $('#save_btn1').attr('disabled', true);
                }

                var table = document.getElementById('datatable');
                var rowCount = table.rows.length;
                for (var i = 1; i < rowCount; i++) {
                    var row = table.rows[i];

                    kode = row.cells[0].children[0].value;
                    qty = row.cells[4].children[0].value;
                    qty_jual = row.cells[4].children[1].value;
                    qtysisa = row.cells[5].children[1].value;

                    var a = Number(qty.replace(/[^0-9\.]+/g, ""));

                    var b = Number(qty_jual.replace(/[^0-9\.]+/g, ""));
                    if (kode == data.kodeitem) {
                        $('#qtylebih' + vid).val(qtysisa);
                    }

                    $('#qtymohon' + vid).val(data[i].qtymohon);
                    $('#sat' + vid).val(data[i].satuan);
                    $('#stock' + vid).val(data[i].stok);
                
                }
                
            }
        });
    }

    //Show Data Barang, header and check gqty
    function getmmgandcheckqty() {
        var str = $('[name=nomohon]').val();
        if (str == "") {
            hapus();
            $('[id=kode1]').val('');
            $('[id=qty1]').val('');
            $('[id=sat1]').val('');
            $('[id=stock1]').val('');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/mutasigudang/getdataandcheckqty/') ?>" + str,
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

                        document.getElementById("qty" + x).value = data[i].sisa;
                        document.getElementById("qtymohon" + x).value = data[i].sisa;
                        document.getElementById("qtylebih" + x).value = data[i].sisa;
                        document.getElementById("sat" + x).value = data[i].satuan;
                        document.getElementById("stock" + x).value = data[i].stok;
                        if (data[i].sisa > data[i].stok) {
                            swal({
                                title: "STOCK BARANG",
                                html: "<p>Cek Stock Terlebih Dahulu</p>",
                                type: "info",
                                confirmButtonText: "OK"
                            });
                        }
                    }
                }
            });
            $.ajax({
                url: "<?= base_url('inventory/transaksi/mutasigudang/getheadermutasi/') ?>" + str,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {

                        x = i + 1;
                        $('#gudang_tujuan').val(data[i].namagudangtujuan);
                        $('#gudang_kirim').val(data[i].gudang_tujuan);
                        $('#keterangan').val(data[i].keterangan);
                        
                        if(data[i].gudang_tujuan == "" &&  data[i].namagudangtujuan == "")
                        {
                            swal('MUTASI GUDANG', 'Data Belum ke get semua', '');
                        }
                    }
                    
                }
            });
        }
        getbarang()
    }

    //Show Batch perbarang
    function showbatch(id) {
        var vid = id.substring(8);
        document.getElementById("nopilihbatch").value = vid;
        var gudang = document.getElementById("gudang_kirim").value;
        var item = document.getElementById("kode" + vid).value;
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
        xhttp.open("GET", "<?= base_url('getbatchid/') ?>" + item + "/" + gudang, true);
        xhttp.send();
        

    }

    //Entry batch
    function post_batch(v1, v2, v3, v4) {

        id = document.getElementById("nopilihbatch").value;
        var kode = $('#kode' + id).val();
        var qty = parseInt($('#qty' + id).val());
        var qtybatch = parseInt(v4);

        if (v1 == kode) {
            document.getElementById("no_batch" + id).value = v2;
            document.getElementById("tgl_exp" + id).value = v3;
            document.getElementById("qtybatch" + id).value = qtybatch;
            // totalline(id);
            $('#lupbatch').modal('hide');
        } else {
            swal('MUTASI CABANG', 'barang tidak Sesuai  ...', '');
        }
        if (qty > qtybatch) {
            swal('No BATCH', 'qty Tidak Sesuai / lebih dari qty No BATCH  ...', '');
            document.getElementById("save_btn1").disabled = true;
        }

    }

    //Check Qty Data
    function check_qty(str, id) {
        var str_b = $('#kode' + id).val();
        var str_c = parseInt($('#qtybatch' + id).val());
        var str_d = parseInt($('#qtylebih' + id).val());
        var vstr = parseInt(str);
        var vid = id;
        if (vstr > str_c) {
            swal('MUTASI GUDANG', 'qty tidak boleh lebih dari qty Nobatch...', '');
            $('#save_btn1').attr('disabled', true);
        } else {
            $('#save_btn1').attr('disabled', false);
        }
        
        var hitungsisa = vstr - str_d;
        if (vstr > str_d) {
            swal('MUTASI GUDANG', 'qty tidak boleh lebih dari qty permohonan...', '');
            $('#save_btn1').attr('disabled', true);
        }

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        qty = row.cells[5].children[1].value - row.cells[4].children[0].value;
        var qtysisa = row.cells[5].children[1].value = qty;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            qty = row.cells[4].children[0].value;
            qty_jual = row.cells[4].children[1].value;
            if (kode == str_b) {
                $('#qtylebih' + i).val(qtysisa);
            }

        }
        

    }

    //Save Data Mutasi Gudang
    function save() {
        var gudangminta = $('#gudang_minta').val();
        var gudangtujuan = $('#gudang_tujuan').val();
        var tanggal = $('[name="tanggal"]').val();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            qty = row.cells[4].children[0].value;
            qty_batch = row.cells[4].children[1].value;
            stock = row.cells[5].children[0].value;

            var a = Number(qty.replace(/[^0-9\.]+/g, ""));
            var b = Number(qty_batch.replace(/[^0-9\.]+/g, ""));
            var c = Number(stock.replace(/[^0-9\.]+/g, ""));
            if (a > b) {
                swal('MUTASI CABANG', 'qty di baris ke ' + i + ' tidak boleh lebih dari qty no batch ...', '');
                $('#save_btn1').attr('disabled', true);
            }else if(a > c){
                swal('MUTASI CABANG', 'qty di baris ke ' + i + ' tidak boleh lebih dari STOCK ...', '');
                $('#save_btn1').attr('disabled', true);
            }

        }
        if (gudangminta == null || gudangtujuan == null) {
            swal('MUTASI GUDANG', 'Gudang minta atau gudang pengirim belum diisi ...', '');
        }else if($('#save_btn1').prop('disabled') == true){

         }else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/mutasigudang/save/1') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "MUTASI GUDANG",
                        html: "<p> No. MUTASI   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        location.href = "<?= BASEURL . 'inventory/transaksi/mutasigudang' ?>";
                    });

                },
                error: function(data) {
                    swal('MUTASI GUDANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }
</script>