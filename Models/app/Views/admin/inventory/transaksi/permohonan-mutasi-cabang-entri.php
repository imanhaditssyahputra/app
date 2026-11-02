<?php 
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('inv_mohon_mutasi_cabang')->where('idmohon', $id)->get()->getRow();
    $table_data_detail = $db->table('inv_mohond_mutasi_cabang')->where('nomohon', $table_data->nomohon)->get()->getResult();
    if($table_data->statusid ==3){
        main_header('Inventory', 'Permintaan Mutasi Barang Antar Cabang', 'Daftar Permintaan Mutasi', 'View Permintaan Mutasi Antar Cabang');
    }else{
        main_header('Inventory', 'Permintaan Mutasi Barang Antar Cabang', 'Daftar Permintaan Mutasi', 'Edit Permintaan Mutasi Antar Cabang');
    }
}else{
    main_header('Inventory', 'Permintaan Mutasi Barang Antar Cabang', 'Daftar Permintaan Mutasi', 'Entri Permintaan Mutasi Antar Cabang');
}
?> 

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i><?php 
            if(isset($table_data) && $table_data->statusid == 3){ echo 'Data View';}
            elseif (isset($table_data) && $table_data->statusid == 1) {echo 'Data Edit'; }
            else{echo '*Data Baru';}  ?>
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
                                                    <input type="text" placeholder="Otomatis" name="nomorbukti" class="form-control" value="<?= isset($table_data) ? $table_data->nomohon : $nomor ?>" readonly>
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
                                            <label class="col-md-3 control-label pt-2">Dari Cabang Asal</label>
                                            <div class="col-md-4">
                                                <select id="cabang_asal" name="cabang_asal" class="form-select input-medium select2" data-placeholder="Pilih..." >
                                                    <option value="0">--Pilih Cabang--</option>
                                                    <?php
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode' => $cabang])->getResult();
                                                        foreach ($cabangasal as $key => $value) { ?>
                                                            <option value="<?= $value->kode ?>" <?php if(isset($table_data) && $value->kode==$table_data->cabang_asal){ echo "selected=true";}?>><?= $value->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Ke Cabang Tujuan</label>
                                            <div class="col-md-4">
                                                <select id="cabang_tujuan" name="cabang_tujuan" class="form-select input-medium select2" onchange="getbarang()" data-placeholder="Pilih...">
                                                    <option value="0">--Pilih Cabang--</option>
                                                    <?php
                                                        $cabangasal = $db->table("ms_unit")->getWhere(['kode !=' => $cabang])->getResult();
                                                        foreach ($cabangasal as $key => $value) { ?>
                                                            <option value="<?= $value->kode ?>" <?php if(isset($table_data) && $value->kode==$table_data->cabang_tujuan){ echo "selected=true";}?>><?= $value->nama ?></option>
                                                    <?php } ?>
                                                </select>
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
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="background-color: #e6f7ff; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 8px 2px; height: auto; overflow-x: scroll; padding: 5px; width: auto;">
                                <div class="col-md-12">
                                    <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th width="30%" style="text-align: center">Kode/Nama Barang</th>
                                                <th width="10%" style="text-align: center">qty</th>
                                                <th width="10%" style="text-align: center">Stock Asal</th>
                                                <th width="10%" style="text-align: center">satuan</th>
                                                <th width="5%"></th>
                                            </tr>
                                            <thead>
                                            <tbody>
                                                <?php
                                                    if (isset($table_data_detail) && $table_data->statusid < 3 ) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->table("inv_barang")->getWhere(['kodeitem'=> $value->kodeitem, 'kdcbg' => $table_data->cabang_tujuan, 'kdgudang'=> $table_data->gudang_tujuan])->getRow();
                                                            $barangMohon = $db->table("inv_barang")->getWhere(['kodeitem'=> $value->kodeitem, 'kdcbg' => $table_data->cabang_asal, 'kdgudang'=> $table_data->gudang_asal])->getRow();
                                                            $no++;
                                                ?>
                                                    <tr>
                                                        <td width="30%">
                                                            <select name="kode[]" id="kode<?=$no?>" class="barang_filter_cabang form-select" onchange="showbarangname(this.value, <?=$no?>)" required/>
                                                                <option value="<?= $value->kodeitem;?>"><?= $barang->namabarang;?></option>
                                                            </select>
                                                        </td>

                                                        <td width="10%">
                                                            <input name="qty[]" value="<?= $value->qtymohon - $value->qtymutasi;?>" id="qty<?=$no?>" type="text" class="form-control rightJustified" required>
                                                        </td>
                                                        <td width="10%"><input name="stock[]" value="<?= $barangMohon != null ? $barangMohon->stok : '0' ?>" id="stock<?=$no?>" type="text" class="form-control rightJustified" readonly></td>
                                                        <td width="10%"><input name="sat[]" id="sat<?=$no?>" type="text" value="<?= $value->satuan;?>" class="form-control"></td>
                                                        <td width="5%">
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php } 
                                                }elseif(isset($table_data_detail) && $table_data->statusid == 3){
                                                    $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $barang = $db->table("inv_barang")->getWhere(['kodeitem'=> $value->kodeitem, 'kdcbg' => $table_data->cabang_tujuan, 'kdgudang'=> $table_data->gudang_tujuan])->getRow();
                                                            $barangMohon = $db->table("inv_barang")->getWhere(['kodeitem'=> $value->kodeitem, 'kdcbg' => $table_data->cabang_asal, 'kdgudang'=> $table_data->gudang_asal])->getRow();
                                                            $no++;
                                                ?>
                                                    <tr>
                                                        <td width="30%">
                                                            <select name="kode[]" id="kode<?=$no?>" class=" form-select" onchange="showbarangname(this.value, <?=$no?>)" required/>
                                                                <option value="<?= $value->kodeitem;?>"><?= $barang->namabarang;?></option>
                                                            </select>
                                                        </td>

                                                        <td width="10%">
                                                            <input name="qty[]" value="<?= $value->qtymohon - $value->qtymutasi;?>" id="qty<?=$no?>" type="text" class="form-control rightJustified" required>
                                                        </td>
                                                        <td width="10%"><input name="stock[]" value="<?= $barangMohon != null ? $barangMohon->stok : '0' ?>" id="stock<?=$no?>" type="text" class="form-control rightJustified" readonly></td>
                                                        <td width="10%"><input name="sat[]" id="sat<?=$no?>" type="text" value="<?= $value->satuan;?>" class="form-control"></td>
                                                        <td width="5%">
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php }
                                                }else{?>
                                                    <tr>
                                                        <td width="30%">
                                                            <select name="kode[]" id="kode1" class="barang_filter_cabang form-select" onchange="showbarangname(this.value, 1)" required/>
                                                                <option value="">--- Pilih Barang ---</option>
                                                            </select>
                                                        </td>

                                                        <td width="10%">
                                                            <input name="qty[]" value="1" id="qty1" type="text" class="form-control rightJustified" required>
                                                        </td>
                                                        <td width="10%"><input name="stock[]" value="" id="stock1" type="text" class="form-control rightJustified" readonly></td>
                                                        <td width="10%"><input name="sat[]" id="sat1" type="text" class="form-control"></td>
                                                        <td width="5%">
                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php }?>
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



<script>
    var cabang = "<?= $cabang?>";
    var idrow = 2 ;
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

        var akun ="<select name='kode[]' id=kode" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control' required><option value=''>--- Pilih Barang ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrowx + ")'  id=qty" + idrowx +
            " value='1'  type='text' class='form-control rightJustified ' required>";
        td3.innerHTML = "<input name='stock[]'  readonly  id=stock" + idrowx + " value=''  type='text' class='form-control rightJustified'  >";
        td4.innerHTML = "<input name='sat[]'    id=sat" + idrowx + " type='text' class='form-control' >";
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
            url: "<?= base_url('GetDataBarangFilterCabang/') ?>"+ str + "/" + cabangtujuan,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if(data.satuan ==""){
                    $('#sat' + id).val(data.satuan);
                }else{
                    $('#sat' + id).val(data.satuan);
                }
                
            }
        });
        $.ajax({
            url: "<?= base_url('GetDataBarangFilterCabang/') ?>"+ str + "/" + cabangasal,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data != null) {
                    if(data.stok ==""){
                        $('#stock' + id).val(data.stok);
                    }else{
                        $('#stock' + id).val(data.stok);
                    }
                } else {
                    $('#stock' + id).val('0');
                }
            }
        });
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PERMOHONAN MUTASI CABANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn1").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PERMOHONAN MUTASI CABANG', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
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
            if(sts != 3){
                if (kode =="" || qty =="0" || satuan =="") {
                    swal('PERMINTAAN MUTASI CABANG', 'Data di baris ke ' + i + ' Tidak Boleh kosong', '');
                    $('#save_btn1').attr('disabled', true);
                }else{
                    $('#save_btn1').attr('disabled', false);
                }
            }
            

        }
        if (cabangTujuan == null || cabangasal == null) {
            swal('PERMINTAAN MUTASI CABANG', 'CABANG tujuan atau CABANG asal belum diisi ...', '');
        }else if($('#save_btn1').prop('disabled') == true){ 
        } else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/permohonanmutasicabangsave') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "PERMINTAAN MUTASI CABANG",
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
                    swal('PERMINTAAN MUTASI CABANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function _urlcetak(data)
    {
        var baseurl = "<?php echo base_url()?>";	
        return baseurl+'inventory/transaksi/permohonanmutasicabangcetak/'+data;
    }

    $(document).ready(function() {
        getbarang()
    });
</script>