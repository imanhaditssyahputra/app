<?php 
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('inv_mutasi_cabang')->where('id_mutasi_cabang', $id)->get()->getRow();
    $table_data_detail = $db->table('inv_mutasi_cabang_detail')->where('id_mutasi_cabang', $id)->get()->getResult();
    if($table_data->statusid ==3){
        main_header('Inventory', 'Mutasi Barang Antar Cabang', 'Daftar Mutasi', 'View Mutasi Antar Cabang');
    }else{
        main_header('Inventory', 'Mutasi Barang Antar Cabang', 'Daftar Mutasi', 'Edit Mutasi Antar Cabang');
    }
}else{
    main_header('Inventory', 'Mutasi Barang Antar Cabang', 'Daftar Mutasi', 'Entri Mutasi Antar Cabang');
}
?> 



<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i><?php 
            if(isset($table_data) && $table_data->statusid == 3){ echo 'Data View';}
            elseif (isset($table_data) && $table_data->statusid != 3) {echo 'Data Edit'; }
            else{echo '*Data Baru';}  ?>
        </div>


    </div>

    <div class="portlet-body form">
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->id_mutasi_cabang : 0 ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fa fa-file"></i>
                                Mutasi Barang Cabang
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
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tanggal)) : date('Y-m-d') ?>" />
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
                                                    <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" readonly value="<?= isset($table_data) ? $table_data->nomutasi_cabang : $nomor ?>">
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
                                            <label class="col-md-3 control-label pt-2">Cabang Permintaan</label>
                                            <div class="col-md-6">
                                                <div class="input-group position-relative">
                                                    <select id="cabang_minta" name="cabang_minta" class="form-control input-medium select2" data-placeholder="Pilih...">
                                                        <option>---Pilih Cabang</option>
                                                        <?php
                                                            $cabangasal = $db->table("ms_unit")->getWhere(['kode !=' => $cabang])->getResult();
                                                            foreach ($cabangasal as $key => $value) { ?>
                                                                <option value="<?= $value->kode ?>" <?php if(isset($table_data) && $value->kode==$table_data->cabang_tujuan){ echo "selected=true";}?>><?= $value->nama ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="input-group-btn position-absolute start-100 top-0">
                                                        <a class="btn green" onclick="showmmc()"><i class="fa fa-refresh"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Cabang Pengirim</label>
                                            <div class="col-md-4">
                                                <?php  if(isset($table_data)){
                                                    $cabangasal = $db->table("ms_unit")->getWhere(['kode' => $table_data->cabang_asal])->getRow();
                                                } ?>
                                                <input type="text" id="cabang_kirim" name="cabang_kirim" class="form-control input-medium" value="<?= isset($table_data) ? $cabangasal->nama : '' ?>" readonly>
                                                <input type="hidden" id="cabang_pengirim" name="cabang_pengirim" class="form-control input-medium" value="<?= isset($table_data) ? $table_data->cabang_asal : '' ?>" readonly>
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
                                                        <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>">
                                                        <input type="hidden" class="form-control input-large" placeholder="" name="gudangTujuan" id="gudangTujuan" value="<?= isset($table_data) ? $table_data->gudang_asal : '' ?>">
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
                                                            <?php if(isset($table_data)){?>
                                                                <input type="text" class="form-control input-medium" placeholder="" name="nomohon" id="nomohon" value="<?= isset($table_data) ? $table_data->nomohon : '' ?>" readonly>
                                                            <?php }else{ ?>
                                                            <select name="nomohon" id="nomohon" class="form-select input-medium select2">
                                                                <option value="">---Tanpa Permintaan---</option>
                                                            </select>
                                                            <span class="input-group-btn position-absolute start-100 top-0">
                                                                <a class="btn blue" onclick="getmmcandcheckqty()"><i class="fa fa-download"></i></a>
                                                            </span>
                                                            <?php } ?>
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
                                                <?php
                                                    if (isset($table_data_detail)) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->table("inv_barang")->getWhere(['kodeitem'=> $value->kodeitem, 'kdcbg' => $table_data->cabang_asal, 'kdgudang'=> $table_data->gudang_asal])->getRow();
                                                            $batch = $db->table("inv_nobatch")->getWhere(['kodeitem'=> $value->kodeitem, 'no_batch' => $value->nobatch, 'gudang'=> $table_data->gudang_asal, 'cabang'=> $table_data->cabang_asal])->getRow();
                                                            $mohon = $db->table("inv_mohond_mutasi_cabang")->getWhere(['kodeitem'=> $value->kodeitem, 'nomohon' => $table_data->nomohon, 'gudang'=> $table_data->gudang_tujuan, 'cabang'=> $table_data->cabang_tujuan])->getRow();
                                                            $no++;
                                                ?>
                                                <tr>
                                                    <td width="30%">
                                                        <select name="kode[]" id="kode<?=$no?>" class="barang_filter_cabang form-select" onchange="showbarangname(this.value, 1)">
                                                        <option value="<?= $value->kodeitem;?>"><?= $barang->namabarang;?></option>
                                                        </select>
                                                    </td>

                                                    <td width="15%">
                                                        <input name="no_batch[]" readonly="" value="<?= $value->nobatch;?>" id="no_batch<?=$no?>" type="text" class="form-control rightJustified">
                                                    </td>
                                                    <td width="2%"><a class="btn default" id="lupharga<?=$no?>" onclick="showbatch(this.id)"><i class="fa fa-search"></i></a>
                                                    </td>
                                                    <td width="15%"><input name="tgl_exp[]" value="<?= $value->tglexp;?>" id="tgl_exp<?=$no?>" type="text" readonly class="form-control rightJustified">
                                                    </td>

                                                    <td width="10%">
                                                        <input name="qty[]" value="<?= $value->qty;?>" onchange="check_qty(this.value, <?=$no?>)" id="qty<?=$no?>" type="text" class="form-control rightJustified">
                                                        <input name="qtybatch[]" value="<?= $batch->qty_saldo;?>" id="qtybatch<?=$no?>" type="hidden" class="form-control rightJustified">
                                                        <input name="qtymohon[]" value="<?= $mohon->qtymohon - $mohon->qtymutasi;?>" id="qtymohon<?=$no?>" type="hidden" class="form-control rightJustified">
                                                    </td>
                                                    <td width="10%">
                                                        <input name="stock[]" value="<?= $barang->stok ?>" id="stock<?=$no?>" type="text" class="form-control rightJustified" readonly>
                                                        <input name="qtylebih[]" value="<?= $mohon->qtymohon - $mohon->qtymutasi;?>" id="qtylebih<?=$no?>" type="hidden" class="form-control rightJustified">
                                                    </td>
                                                    <td width="10%"><input name="sat[]" value="<?= $value->satuan;?>" id="sat<?=$no?>" type="text" class="form-control" >
                                                    </td>
                                                    <td width="3%">
                                                        <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                    </td>
                                                </tr>
                                                <?php } }else{?>
                                                    <tr>
                                                    <td width="30%">
                                                        <select name="kode[]" id="kode1" class="barang_filter_cabang form-select" onchange="showbarangname(this.value, 1)">
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
                                                        <input name="stock[]" value="" id="stock1" type="text" class="form-control rightJustified" readonly>
                                                        <input name="qtylebih[]" value="1" id="qtylebih1" type="hidden" class="form-control rightJustified">
                                                    </td>
                                                    <td width="10%"><input name="sat[]" id="sat1" type="text" class="form-control " onkeypress="return tabE(this,event)">
                                                    </td>
                                                    <td width="3%">
                                                        <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                    </td>
                                                </tr>
                                                <?php } ?>
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
                                <?php if(isset($table_data) && $table_data->statusid !=2){ ?>
                                    <button type="button" onclick="save()" class="btn blue" id="save_btn1"><i class="fa fa-save"></i> Simpan</button>
                                    <a class="btn default print_laporan" onclick="javascript:window.open(_urlcetak('<?=$table_data->nomutasi_cabang?>'),'_blank');"><i class="fa fa-print"></i> Cetak</a>
                                <?php } else if(!isset($table_data)){ ?>
                                    <button type="button" onclick="save()" class="btn blue" id="save_btn1"><i class="fa fa-save"></i> Simpan</button>  
                                    <a class="btn default print_laporan" onclick="javascript:window.open(_urlcetak_shipping(),'_blank');" ><i class="fa fa-print"></i> Cetak Shipping</a>
                                <?php } ?>
                                
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

        var akun = "<select name='kode[]' id=kode" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control' ><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='no_batch[]'  readonly  value='' id=no_batch" + idrowx +
            " type='text' class='form-control rightJustified'  >";
        td3.innerHTML = "<a class='btn default' id=lupharga" + idrowx +
            "  onclick='showbatch(this.id)'><i class='fa fa-search'></i></a>";
        td4.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrowx +
            " value=''  type='text' readonly class='form-control rightJustified'  >";
        td5.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrowx + ")'  id=qty" + idrowx +
            " value='0'  type='text' class='form-control rightJustified'>  <input name='qtybatch[]'    id=qtybatch" +
            idrowx +
            "  value=''  type='hidden' class='form-control rightJustified'  >  <input name='qtymohon[]'    id=qtymohon" +
            idrowx + "  value=''  type='hidden' class='form-control rightJustified'  > ";
        td6.innerHTML = "<input name='stock[]'  readonly  id=stock" + idrowx + " value='0'  type='text' class='form-control rightJustified'  > <input name='qtylebih[]'    id=qtylebih" +
            idrowx + "  value=''  type='hidden' class='form-control rightJustified'  >";
        td7.innerHTML = "<input name='sat[]'    id=sat" + idrowx + " type='text' class='form-control' >";
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
        var cabangKirim = $('#cabang_pengirim').val();
        initailizeSelect2_barang_filterCabang(cabangKirim)
    }

    //show data Mohon Mutasi Cabang
    function showmmc() {
        var xhttp;
        var str = $('[name="cabang_minta"]').val();

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
        xhttp.open("GET", "<?= base_url('inventory/transaksi/mutasicabang/showmmc/') ?>" + str, true);
        xhttp.send();
    }

    //Show data barang
    function showbarangname(str, id) {
        var xhttp;
        var vid = id;
        var nomohon = $('[name=nomohon]').val();
        $.ajax({
            url: "<?= base_url('inventory/transaksi/mutasicabang/showbarangmutasicabang/') ?>" + str + "/" + nomohon,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    swal('MUTASI CABANG', 'Barang Tidak Sesuai dengan permohonan...', '');
                    $('#save_btn1').attr('disabled', true);
                }else{
                    $('#save_btn1').attr('disabled', false);
                }

                    $('#qtymohon' + vid).val(data.qtymohon);
                    $('#sat' + vid).val(data.satuan);
                    $('#stock' + vid).val(data.stok);
                    // $('#qtylebih' + vid).val(data.sisa);
                    var table = document.getElementById('datatable');
                    var rowCount = table.rows.length;
                    for (var i = 1; i < rowCount; i++) {
                        var row = table.rows[i];

                        kode = row.cells[0].children[0].value;
                        qtylebih = row.cells[5].children[1].value;
                        qtymohon = row.cells[4].children[2].value;
                        if (kode == data.kodeitem) {
                            $('#qtylebih' + vid).val(qtylebih);
                            // $('#qtymohon' + vid).val(qtymohon);
                        }
                    }
                
            }
        });
    }

    //Show Data Barang, header and check gqty
    function getmmcandcheckqty() {
        var str = $('[name=nomohon]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/mutasicabang/getdataandcheckqty/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {

                    $('#cabang_kirim').val(data.datahead.namacabangtujuan);
                        $('#cabang_pengirim').val(data.datahead.cabang_tujuan);
                        $('#keterangan').val(data.datahead.keterangan);
                        $('#gudangTujuan').val(data.datahead.gudang_tujuan);
                        
                        if(data.datahead.gudang_tujuan == "" &&  data.datahead.namacabangtujuan == "")
                        {
                            swal('MUTASI GUDANG', 'Data Belum ke get semua', '');
                        }
                    for (i = 0; i <= data.dataitem.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;
                        var option = $("<option selected></option>").val(data.dataitem[i].kodeitem).text(data.dataitem[i]
                            .namabarang);
                        $('#kode' + x).append(option).trigger('change');

                        document.getElementById("qty" + x).value = data.dataitem[i].sisa;
                        document.getElementById("qtymohon" + x).value = data.dataitem[i].sisa;
                        document.getElementById("qtylebih" + x).value = data.dataitem[i].sisa;
                        document.getElementById("sat" + x).value = data.dataitem[i].satuan;
                        document.getElementById("stock" + x).value = data.dataitem[i].stok;
                        if (data.dataitem[i].sisa > data.dataitem[i].stok) {
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
        }
        getbarang()
    }

    //Show Batch perbarang
    function showbatch(id) {
        var vid = id.substring(8);
        document.getElementById("nopilihbatch").value = vid;
        var gudangKirim = document.getElementById("gudangTujuan").value;
        var item = document.getElementById("kode" + vid).value;
        var xhttp;
        if (item == "" && gudangKirim == "") {
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
        xhttp.open("GET", "<?= base_url('getbatchid/') ?>" + item + "/" + gudangKirim, true);
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
        var str_e = parseInt($('#qtymohon' + id).val());
        var vstr = parseInt(str);
        var vid = id;
        if (vstr > str_c) {
            swal('MUTASI CABANG', 'qty tidak boleh lebih dari qty Nobatch...', '');
            $('#save_btn1').attr('disabled', true);
        } else {
            $('#save_btn1').attr('disabled', false);
        }
        
        var hitungsisa = vstr - str_d;
        

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        qty = row.cells[5].children[1].value - row.cells[4].children[0].value;
        var qtysisa = row.cells[5].children[1].value = qty;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var tot_qty = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];
            kode = row.cells[0].children[0].value;
            qty = row.cells[4].children[0].value;
            qty_jual = row.cells[4].children[1].value;
            if (kode == str_b) {
                $('#qtylebih' + i).val(qtysisa);
                tot_qty += parseInt(qty);
            }
        }
        if (tot_qty > str_e) {
            swal('MUTASI CABANG', 'qty tidak boleh lebih dari qty permohonan...', '');
            $('#save_btn1').attr('disabled', true);
        }else {
            $('#save_btn1').attr('disabled', false);
        }
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('MUTASI CABANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn1").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('MUTASI CABANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Save Data Mutasi CABANG
    function save() {
        var cabangminta = $('#cabang_minta').val();
        var cabangtujuan = $('#cabang_pengirim').val();
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
        if (cabangminta == null || cabangtujuan == null) {
            swal('MUTASI CABANG', 'Cabang minta atau Cabang pengirim belum diisi ...', '');
        }else if($('#save_btn1').prop('disabled') == true){

        }else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/mutasicabangsave') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "MUTASI CABANG",
                        html: "<p> No. MUTASI   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        window.open(_urlcetak(data));
                        location.href = "<?= BASEURL . 'inventory/transaksi/mutasicabang' ?>";
                    });

                },
                error: function(data) {
                    swal('MUTASI GUDANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function _urlcetak_shipping()
    {
        var baseurl = "<?php echo base_url()?>";
        var param= $('[name="nomohon"]').val();				
        return baseurl+'inventory/transaksi/mutasicabangcetaktracer/'+param;
    }

    function _urlcetak(data)
    {
        var baseurl = "<?php echo base_url()?>";	
        return baseurl+'inventory/transaksi/mutasicabangcetak/'+data;
    }
</script>