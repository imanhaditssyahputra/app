<?= main_header('Pembelian', 'Pesanan Pra Pembelian', 'Daftar Pesanan Pra Pembelian', 'Entri Pesanan Pra Pembelian', 'pembelian/transaksi/prapembelian') ?>


<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_prapofile')->where('id', $id)->get()->getRow();
    $table_data_detail = $db->table('ap_prapodetail')->where('kodeprapo', $table_data->kodeprapo)->get()->getResult();
}
?>
<!-- <h1>Check Ping</h1>
<p>Ping Status: <span id="pingStatus">-</span></p>
<input type="hidden" id="colorping"> -->
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
    <div class="col-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    *New Data
                </div>

            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form id="submit_form" class="form-horizontal form-bordered1">
                    <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->id : 0 ?>">
                    <div class="form-body">
                        <div class="tabbable tabbable-custom tabbable-full-width">
                            <ul class="nav nav-tabs">
                                <li class="active" style="border-left: 1px solid #ddd; border-right: 1px solid #ddd; padding: 8px 0px;">
                                    <a href="#tab1" data-toggle="tab">
                                        Pesanan Pra Pembelian
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <!-- First Row -->
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">Tanggal PRAPO</label>
                                                    <div class="col-md-5">
                                                        <input id="tanggal" name="tanggal" class="form-control" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglprapo)) : date('Y-m-d') ?>">
                                                        <span class="tanggalErr"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">Nomor #</label>
                                                    <div class="col-md-7">
                                                        <input id="nomor" name="nomor" class="form-control" type="text" placeholder="Nomor Auto" value="<?= isset($table_data) ? $table_data->kodeprapo : '' ?>" readonly>
                                                        <input type="hidden" class="form-control input-medium" name="nomorbukti" readonly id="nomorbukti" value="<?= isset($table_data) ? $table_data->kodeprapo : $nomor ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- second Row -->
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="control-label pt-2 col-md-3">Keterangan</label>
                                                    <div class="col-md-5">
                                                        <input id="keterangan" name="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>" class="form-control" type="text">
                                                        <span class="keteranganErr"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
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
                                    <!-- third Row -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div style="overflow-x: auto;">
                                                <div class="datatable-example">
                                                    <table id="table" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align: center">Nama Barang</th>
                                                                <th style="text-align: center">Kuantitas</th>
                                                                <th style="text-align: center">stock</th>
                                                                <th style="text-align: center">Satuan</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if (isset($table_data_detail) && $table_data->statusid == 1) {
                                                                $count = 0;
                                                                foreach ($table_data_detail as $value) {
                                                                    $db = db_connect();
                                                                    $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang' group by kodeitem")->getRow();
                                                                    $count++;
                                                            ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select name="kode[]" id="kode<?= $count ?>" class="barang_filter form-select input-xlarge">
                                                                                <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input name="qty[]" id="qty<?= $count ?>" type="text" class="form-control input-medium rightJustified" value="<?= $value->qtyprapo ?>">
                                                                            <input name="stok_min[]" value="<?= $barang->stok_min; ?>" id="stok_min<?= $count ?>" type="hidden" class="form-control rightJustified">
                                                                            <input name="qtykirim[]" value="<?= $value->qtykirim; ?>" id="qtykirim<?= $count ?>" type="hidden" class="form-control rightJustified" required>
                                                                        </td>
                                                                        <td>
                                                                            <input name="stock[]" value="<?= $barang->stok; ?>" id="stock<?= $count ?>" type="text" readonly="" class="form-control input-medium rightJustified">
                                                                            <input name="qtyhitung[]" value="<?= $value->qtyprapo; ?>" id="qtyhitung<?= $count ?>" type="hidden" class="form-control rightJustified" required>
                                                                        </td>
                                                                        <td>
                                                                            <input name="sat[]" id="sat<?= $count ?>" type="text" class="form-control input-small " value="<?= $value->satuan ?>">
                                                                            <input name="harga[]" value="<?= $value->hargabeli ?>" id="harga<?= $count ?>" type="hidden" class="form-control rightJustified">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" onclick="deleteCurrentRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                            } else if (isset($table_data_detail) && $table_data->statusid == 3) {
                                                                $count = 0;
                                                                foreach ($table_data_detail as $value) {
                                                                    $db = db_connect();
                                                                    $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$value->kodeitem' and kdcbg='$cabang' group by kodeitem")->getRow();
                                                                    $count++;
                                                                ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select name="kode[]" id="kode<?= $count ?>" class="barang_filter form-select input-xlarge">
                                                                                <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input name="qty[]" id="qty<?= $count ?>" onchange="check_qty(this.value, <?= $count ?>)" type="text" class="form-control rightJustified input-medium" value="<?= $value->qtyprapo - $value->qtykirim ?>" readonly>
                                                                            <input name="stok_min[]" value="<?= $barang->stok_min; ?>" id="stok_min<?= $count ?>" type="hidden" class="form-control rightJustified">
                                                                            <input name="qtykirim[]" value="<?= $value->qtykirim; ?>" id="qtykirim<?= $count ?>" type="hidden" class="form-control rightJustified" required>
                                                                        </td>
                                                                        <td>
                                                                            <input name="stock[]" value="<?= $barang->stok; ?>" id="stock<?= $count ?>" type="text" readonly="" class="form-control rightJustified input-medium">
                                                                            <input name="qtyhitung[]" value="<?= $value->qtyprapo; ?>" id="qtyhitung<?= $count ?>" type="hidden" class="form-control rightJustified" required>
                                                                        </td>
                                                                        <td>
                                                                            <input name="sat[]" id="sat<?= $count ?>" type="text" class="form-control input-small" value="<?= $value->satuan ?>">
                                                                            <input name="harga[]" value="<?= $value->hargabeli ?>" id="harga<?= $count ?>" type="hidden" class="form-control rightJustified">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" onclick="deleteCurrentRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                                        </td>
                                                                    </tr>

                                                                <?php }
                                                            } else { ?>
                                                                <tr>
                                                                    <td>
                                                                        <select onchange="selectCurrent(this.value,1)" name="kode[]" id="kode1" class="barang_filter form-select input-xlarge ">
                                                                            <option unit="" value="" stock="" price="">--- Pilih Barang ---</option>

                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input name="qty[]" value="0" id="qty1" type="text" onchange="check_qty(this.value, 1)" class="form-control input-medium rightJustified">
                                                                        <input name="stok_min[]" value="1" id="stok_min1" type="hidden" class="form-control rightJustified">
                                                                        <input name="qtykirim[]" value="0" id="qtykirim1" type="hidden" class="form-control rightJustified" required>
                                                                    </td>
                                                                    <td>
                                                                        <input name="stock[]" value="" id="stock1" type="text" readonly="" class="form-control input-medium rightJustified">
                                                                        <input name="qtyhitung[]" value="0" id="qtyhitung1" type="hidden" class="form-control rightJustified" required>
                                                                    </td>
                                                                    <td>
                                                                        <input name="sat[]" id="sat1" type="text" class="form-control input-small">
                                                                        <input name="harga[]" value="0" id="harga1" type="hidden" class="form-control input-small rightJustified">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                                    </td>
                                                                </tr>
                                                            <?php }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <button type="button" onclick="addRow('example', 'pre_pembelian_entri')" class="btn green"><i class="fa fa-plus"></i> </button> -->

                                    <button type="button" onclick="add_row()" class="btn green"><i class="fa fa-plus"></i> </button>
                                    <div class="mt-4">
                                        <button type="submit" id="save_btn" class="btn blue me-1"><i class="fa fa-save"></i> Simpan</button>
                                        <button type="button" class="btn green"><i class="fa-regular fa-pen-to-square"></i> Data Baru</button>
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
                                                                        <th>Stock Min Beli</th>
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
    var gudang = <?= $gudang ?>;
    var id = "<?= $id ?>";
    let selectedData = [];
    $(document).ready(function() {
        $('#bufferStockTable').DataTable({
            ajax: '<?= base_url('pembelian/transaksi/prapembelianbufferstocktable') ?>',
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
        });
        // $('#table').DataTable({
        //     "lengthMenu": [],
        //     "searching": false
        // });
        initailizeSelect2_barang_filter(cabang, gudang);
                
       
    });

    //Add table Element Current Row
    var idrow = 2;
    function add_row() {
        var table = document.getElementById("table");
        var idrowx = table.rows.length;

        var x = document.getElementById('table').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);

        if(id !==""){
            var akun = "<select name='kode[]' id=kode" + idrowx + " onchange='selectCurrent(this.value," + idrowx +
                ")' class='barang_filter form-control' required ><option value=''>--- Pilih Barang ---</option></select>";
            td1.innerHTML = akun;
            td2.innerHTML = "<input name='qty[]' id=qty" + idrowx + 
                " onchange='check_qty(this.value," + idrowx +
                ")'  value='0'  type='text' class='form-control rightJustified'  required> <input name='stok_min[]'    id=stok_min" +
                idrowx + " value='0'  type='hidden' class='form-control rightJustified'  > <input name='qtykirim[]'  id=qtykirim" + idrow + " value='0'  type='hidden' class='form-control'>";
            td3.innerHTML = "<input name='stock[]'    id=stock" + idrowx + " value=''  type='text' class='form-control rightJustified' readonly>  <input name='qtyhitung[]'  id=qtyhitung" + idrow + " value='0'  type='hidden' class='form-control'>";
            td4.innerHTML = "<input name='sat[]'    id=sat" + idrowx + " type='text' class='form-control input-small' required>  <input name='harga[]'  id=harga" + idrowx + " oninput='totalline(" + idrowx +
                ") value='0'  type='hidden' class='form-control rightJustified'>";
            td5.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        }else{
            var akun = "<select name='kode[]' id=kode" + idrow + " onchange='selectCurrent(this.value," + idrow +
                ")' class='barang_filter form-control' required ><option value=''>--- Pilih Barang ---</option></select>";
            td1.innerHTML = akun;
            td2.innerHTML = "<input name='qty[]' id=qty" + idrow +
                " onchange='check_qty(this.value," + idrow +
                ")'   value='0'  type='text' class='form-control rightJustified'  required> <input name='stok_min[]'    id=stok_min" +
                idrow + " value='0'  type='hidden' class='form-control rightJustified'  > <input name='qtykirim[]'  id=qtykirim" + idrow + " value='0'  type='hidden' class='form-control'>";
            td3.innerHTML = "<input name='stock[]'    id=stock" + idrow + " value=''  type='text' class='form-control rightJustified' readonly>  <input name='qtyhitung[]'  id=qtyhitung" + idrow + " value='0'  type='hidden' class='form-control'>";
            td4.innerHTML = "<input name='sat[]'    id=sat" + idrow + " type='text' class='form-control input-small' required>  <input name='harga[]'  id=harga" + idrow + " oninput='totalline(" + idrow +
                ") value='0'  type='hidden' class='form-control rightJustified'>";
            td5.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        }
        initailizeSelect2_barang_filter(cabang, gudang);
        idrow++;
    }
    
    var tampungData = [];

    function tampungdata() {
        const checkboxes = document.querySelectorAll('#checkboxes');
        const kodeitem = event.target.value;
        const isChecked = event.target.checked;
        var table = document.getElementById('table');
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
                add_row()
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
                    document.getElementById("table").deleteRow(i);
                }
            }
        }
        
    }

    // Fungsi untuk menambahkan data ke datatable
    function tambahData(kodeitem, namabarang, satuan, stok, stok_min, qtybeli,  index) {
        // Ambil datatable
        var table = $('#table').DataTable();
        var kodeitem = kodeitem;
        var namabarang= namabarang;
        var satuan = satuan;
        var stok =   stok;
        var stok_min =  stok_min;
        var qtybeli =  qtybeli;
        var table = document.getElementById('table');
        var rowCount = table.rows.length;
        x = rowCount - 1;
        for (var i = 1; i <= rowCount; i++) {
            var row = table.rows[i];
            kodedata = row.cells[0].children[0].value;
            if (kodedata =="") {
                var barang = $("<option selected></option>").val(kodeitem).text(namabarang);
                $('#kode' + i).append(barang).trigger('change');

                document.getElementById("qty" + i).value = qtybeli;
                document.getElementById("stok_min" + i).value = stok_min;
                document.getElementById("stock" + i).value = stok;
                document.getElementById("sat" + i).value = satuan;
                document.getElementById("harga" + i).value = 0;
            }

        }
    }
    
    //Delete Table Element Current Row
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("table").deleteRow(i);
    }

    function selectCurrent(str, id) {
        $.ajax({
            url: "<?= base_url('GetDataBarangFilter/') ?>" + str + "/" + cabang + "/" + gudang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('#stock' + id).val(data.stok);
                $('#stok_min' + id).val(data.stok_min);
                $('#sat' + id).val(data.satuan);
                document.getElementById("harga" + id).value = number_format(data.hargabeli);
            }
        });
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PRA PESANAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PRA PESANAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=id]').val();
        var tanggal = $('[name="tanggal"]').val();
        // var colorping = $('#colorping').val();
        var arr = [];
        // if(colorping == 0 || colorping == 1){
        $.ajax({
            url: "<?= base_url('pembelian/transaksi/prapembelianadd') ?>",
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
                        title: "PRA PESANAN PEMBELIAN",
                        html: "<p> Nomor Bukti   : <b>" + data.message + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        //window.open(_urlcetak(data));
                        window.location.href = "<?= base_url('pembelian/transaksi/prapembelian') ?>";
                    });
                } else {
                    swal('PRA PESANAN PEMBELIAN', 'Data gagal disimpan ... ' + data.message, '');
                }

            },
            error: function(data) {
                $('#process').css('display', 'none');
                swal('PRA PESANAN PEMBELIAN', 'Data gagal disimpan ... ' + data, '');
            }
        });
        // }else{
        //     swal('PRA PESANAN PEMBELIAN', 'Koneksi Jaringan tidak stabil', '');
        // }
    });

    /*****DATA PENDING*******/
    //hitung qty
    function check_qty(str, id) {
        var str_a = $('#kode' + id).val();
        var str_c = parseInt($('#qtykirim' + id).val());
        var vstr = parseInt(str);
        if (vstr == 0) {
            var hitungsisa = str_c;
        } else {
            var hitungsisa = str_c + vstr;
        }

        var table = document.getElementById('table');
        var row = table.rows[id];
        var qtysisa = row.cells[2].children[1].value = hitungsisa;
        $.ajax({
            url: "<?= base_url('pembelian/transaksi/checkstockbeli/') ?>" + str_a + "/" + vstr,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.status == 1) {
                    swal('PRA PEMBELIAN', data.message, '');
                    document.getElementById("save_btn").disabled = true;
                } else {
                    document.getElementById("save_btn").disabled = false;
                }
            }
        });
    }

    //url Cetak Buffer Stock
    function _urlcetakBufferStock() {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'pembelian/transaksi/prapembelianCetakBufferStock';
    }
    

</script>