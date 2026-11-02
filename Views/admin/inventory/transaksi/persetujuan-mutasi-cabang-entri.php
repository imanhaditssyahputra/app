<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('inv_acc_mutasi_cabang')->where('idacc_mutasi_cabang', $id)->get()->getRow();
    $table_data_detail = $db->table('inv_accd_mutasi_cabang')->where('noacc_mutasi', $table_data->noacc_mutasi)->get()->getResult();

    main_header('Inventory', 'Approval Mutasi Masuk Barang Antar Region', 'Daftar Approval Mutasi Masuk', 'View Approval Mutasi Masuk Antar Region', 'inventory/transaksi/persetujuanmutasicabang');
} else {
    main_header('Inventory', 'Approval Mutasi Masuk Barang Antar Region', 'Daftar Approval Mutasi Masuk', 'Entri Approval Mutasi Masuk Antar Region', 'inventory/transaksi/persetujuanmutasicabang');
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

<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i><?php
                                                    if (isset($table_data) && $table_data->statusid == 3) {
                                                        echo 'Data View';
                                                    } elseif (isset($table_data) && $table_data->statusid != 3) {
                                                        echo 'Data Edit';
                                                    } else {
                                                        echo '*Data Baru';
                                                    }  ?>
                </div>
            </div>
            <div class="portlet-body form">
                <form id="frmpenjualan" class="form-horizontal" method="post">
                    <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->idacc_mutasi_cabang : 0 ?>">
                    <div class="form-body">
                        <div class="tabbable tabbable-custom tabbable-full-width">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab1" data-toggle="tab">
                                        <i class="fa fa-file"></i>
                                        Approval Mutasi Masuk Barang Region
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <!-- First Row -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row"> <label class="col-md-3 control-label pt-2">Tanggal</label>
                                                    <div class="col-md-4">
                                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tanggal)) : date('Y-m-d') ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">No. Bukti</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" readonly value="<?= isset($table_data) ? $table_data->noacc_mutasi : $nomor ?>">
                                                            <input type="text" placeholder="Otomatis" class="form-control input-medium" value="<?= isset($table_data) ? $table_data->noacc_mutasi : '' ?>" readonly>
                                                        </div>
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
                                                    <label class="col-md-3 control-label pt-2">Penerima Region</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group position-reletive">
                                                            <select id="cabang_terima" name="cabang_terima" class="form-control input-medium select2" data-placeholder="Pilih...">
                                                                <option>---Pilih Region</option>
                                                                <?php
                                                                $cabangterima = $db->table("ms_unit")->getWhere(['kode ' => $cabang])->getResult();
                                                                foreach ($cabangterima as $key => $value) { ?>
                                                                    <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->cabang_terima) {
                                                                                                            echo "selected=true";
                                                                                                        } ?>><?= $value->nama ?></option>
                                                                <?php } ?>
                                                            </select>
                                                            <span class="input-group-btn start-100 top-0">
                                                                <a class="btn green" onclick="showmc()"><i class="fa fa-refresh"></i></a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">Region Pengirim</label>
                                                    <div class="col-md-4">
                                                        <?php isset($table_data) ?  $cabangkirim = $db->table("ms_unit")->getWhere(['kode ' => $table_data->cabang_kirim])->getRow() : ''; ?>
                                                        <input type="text" id="ncabang_kirim" name="ncabang_kirim" class="form-control input-medium" value="<?= isset($table_data) ? $cabangkirim->nama : '' ?>" readonly>
                                                        <input type="hidden" id="cabang_kirim" name="cabang_kirim" class="form-control input-medium" value="<?= isset($table_data) ? $table_data->cabang_kirim : '' ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Third Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">Keterangan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No. Mutasi</label>
                                                            <div class="col-md-6">
                                                                <div class="input-group position-reletive">
                                                                    <?php
                                                                    if (isset($table_data)) {
                                                                    ?>
                                                                        <input type="text" id="nomutasi" name="nomutasi" class="form-control input-medium" value="<?= isset($table_data) ? $table_data->nomutasi_cabang : '' ?>" readonly>
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <select name="nomutasi" id="nomutasi" class="form-control input-medium select2">
                                                                            <option value="">---Tanpa Mutasi---</option>
                                                                        </select>
                                                                        <span class="input-group-btn start-100 top-0">
                                                                            <a class="btn blue" onclick="getmc()"><i class="fa fa-download"></i></a>
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
                                    <!-- Fourth Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No Permintaan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="nomohon" id="nomohon" value="<?= isset($table_data) ? $table_data->nomohon : '' ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Fiveth Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                                    <thead>
                                                        <tr>
                                                            <th style="text-align: center">Kode/Nama Barang</th>
                                                            <th style="text-align: center">No Batch</th>
                                                            <th style="text-align: center">Tanggal EXP</th>
                                                            <th style="text-align: center">qty kirim</th>
                                                            <th style="text-align: center">sat</th>
                                                            <th></th>
                                                        </tr>
                                                        <thead>

                                                        <tbody>
                                                            <?php
                                                            if (isset($table_data_detail)) {
                                                                $no = 0;
                                                                foreach ($table_data_detail as $value) {
                                                                    $db = db_connect();
                                                                    $barang = $db->table("inv_barang")->getWhere(['kodeitem' => $value->kodeitem, 'kdcbg' => $table_data->cabang_kirim, 'kdgudang' => $table_data->gudang_kirim])->getRow();
                                                                    $no++;
                                                            ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select name="kode[]" id="kode<?= $no ?>" class="form-control input-xlarge" readonly>
                                                                                <option value="<?= $value->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input name="no_batch[]" readonly="" value="<?= $value->nobatch; ?>" id="no_batch<?= $no ?>" type="text" class="form-control rightJustified input-small">
                                                                        </td>
                                                                        <td><input name="tgl_exp[]" value="<?= $value->tglexp; ?>" id="tgl_exp<?= $no ?>" type="text" readonly class="form-control rightJustified input-small">
                                                                        </td>

                                                                        <td>
                                                                            <input name="qty[]" value="<?= $value->qtyacc; ?>" onchange="check_qty(this.value, <?= $no ?>)" id="qty<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly>
                                                                        </td>
                                                                        <td><input name="sat[]" value="<?= $value->satuan; ?>" id="sat<?= $no ?>" type="text" class="form-control input-small" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash" aria-hidden="true"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                <?php  }
                                                            } else { ?>
                                                                <tr>
                                                                    <td>
                                                                        <select name="kode[]" id="kode1" class="form-control input-xlarge" readonly>
                                                                            <option value="">--- Pilih Barang ---</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input name="no_batch[]" readonly="" value="" id="no_batch1" type="text" class="form-control rightJustified input-small">
                                                                    </td>
                                                                    <td><input name="tgl_exp[]" value="" id="tgl_exp1" type="text" readonly class="form-control rightJustified input-small">
                                                                    </td>

                                                                    <td>
                                                                        <input name="qty[]" value="" onchange="check_qty(this.value, 1)" id="qty1" type="text" class="form-control rightJustified input-small" readonly>
                                                                    </td>
                                                                    <td><input name="sat[]" id="sat1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash" aria-hidden="true"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                </table>

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
                                        <?php
                                        if (!isset($table_data)) {
                                        ?>
                                            <button type="button" onclick="save()" class="btn blue" id="save_btn1"><i class="fa fa-save"></i> Simpan</button>
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
    </div>
</div>



<script>
    var cabang = "<?= $cabang ?>";

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

    //show data Mutasi Cabang
    function showmc() {
        var xhttp;
        var str = $('[name="cabang_terima"]').val();

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
        xhttp.open("GET", "<?= base_url('inventory/transaksi/persetujuanmutasicabang/showmc/') ?>" + str, true);
        xhttp.send();
    }

    // GET data Header dan item barang
    function getmc() {
        var str = $('[name=nomutasi]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/persetujuanmutasicabang/GetDataHeaderMC/') ?>" + str,
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

						document.getElementById("no_batch" + x).value = data[i].nobatch;
						document.getElementById("tgl_exp" + x).value = data[i].tglexp;
						document.getElementById("qty" + x).value = data[i].qty - data[i].qtysetuju;
						document.getElementById("sat" + x).value = data[i].satuan;
                    }
                }
            });
            $.ajax({
                url: "<?= base_url('inventory/transaksi/persetujuanmutasicabang/getheadermohon/') ?>" + str,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {

                        x = i + 1;
                        $('#nomohon').val(data[i].nomohon);
                        $('#ncabang_kirim').val(data[i].nama);
                        $('#cabang_kirim').val(data[i].cabang_asal);
                        $('#keterangan').val(data[i].keterangan);
                        if (data[i].cetak != 1) {
                            swal('APPROVAL MUTASI MASUK REGION', 'Mutasi Cabang Belum di Cetak  ...', '');
                            document.getElementById("save_btn1").disabled = true;
                        }
                    }
                }
            });
        }
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('APPROVAL MUTASI MASUK REGION', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn1").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('APPROVAL MUTASI MASUK REGION', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    //Save data persetujuan mutasi gudang
    function save() {
        var cabangasal = $('[name="cabang_kirim"]').val();
        var cabangtujuan = $('[name="cabang_terima"]').val();
        var nominta = $('[name="nomohon"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (cabangasal == "" || cabangtujuan == "" || nominta == "") {
            swal('APPROVAL MUTASI MASUK REGION', 'REGION / nominta belum diisi atau kosong ...', '');
        } else {
            $.ajax({
                url: '<?= base_url('inventory/transaksi/persetujuanmutasicabangsave') ?>',
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "APPROVAL MUTASI MASUK REGION",
                        html: "<p> No. Mutasi   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        window.open(_urlcetak(data));
                        location.href = "<?= BASEURL . 'inventory/transaksi/persetujuanmutasicabang' ?>";
                    });

                },
                error: function(data) {
                    swal('APPROVAL MUTASI MASUK REGION', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function _urlcetak(data) {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'inventory/transaksi/accmutasicabangcetak/' + data;
    }
</script>