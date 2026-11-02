<?php
error_reporting(0);
$db = db_connect();
if (!empty($id)) {
    $query = "SELECT * from tr_jurnal where novoucher = '$id'";

    $table_data = $db->query($query)->getRow();
    $table_data_Result = $db->query($query)->getResult();
}
isset($table_data) ? main_header('Buku Besar', 'Jurnal', 'Daftar Jurnal', 'Edit Jurnal', 'bukubesar/transaksi/daftarjurnal') : main_header('Buku Besar', 'Jurnal', 'Daftar Jurnal', 'Entri Junral', 'bukubesar/transaksi/daftarjurnal')
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
            <i class="fa fa-reorder"></i><?php if (isset($table_data)) {
                                                echo 'Data Edit';
                                            } else {
                                                echo '*Data Baru';
                                            }  ?>
        </div>
        <div class="tools">
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="frmjurnal" action="#" class="form-horizontal" method="post">
            <input type="hidden" name="id" id="id" value="<?= isset($table_data) ? $table_data->novoucher : 0 ?>">
            <div class="form-body">
                <!-- First Row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Nomor Bukti</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-medium" name="nomorbukti" readonly id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->novoucher : $nomor ?>" value="<?= isset($table_data) ? $table_data->novoucher : $nomor ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Tanggal</label>
                                <div class="col-md-7">
                                    <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tanggal)) : date('Y-m-d') ?>" />
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
                                <label class="control-label pt-2 col-md-3">Cabang</label>
                                <div class="col-md-9">
                                    <select id="unit" name="unit" class="form-control input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                        <?php
                                        foreach ($unit as $row) { ?>
                                            <option value="<?php echo $row->kode; ?>" <?php if (!empty($id)) {
                                                                                            if ($row->kode == $table_data->kodecbg) {
                                                                                                echo "selected=true";
                                                                                            }
                                                                                        } ?>><?php echo $row->nama; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Keterangan</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->ket : '' ?>">
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
                                <label class="control-label pt-2 col-md-3">Jenis Jurnal</label>
                                <div class="col-md-9">
                                    <select id="jenis" name="jenis" class="form-control input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                        <?php
                                        foreach ($JenisJurnal as $row) { ?>
                                            <option value="<?php echo $row->jurnal_kode; ?>" <?php if (!empty($id)) {
                                                                                                    if ($row->jurnal_kode == $table_data->jenis) {
                                                                                                        echo "selected=true";
                                                                                                    }
                                                                                                } ?>><?php echo $row->jurnal_nama; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">No. Ref</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" placeholder="" name="noref" id="noref" value="<?= isset($table_data) ? $table_data->noref : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Four Row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3"></label>
                                <div class="col-md-7">
                                    <div class="form-check form-check-success">
                                        <input class="form-check-input" type="checkbox" name="postingju" id="formCheckcolor2" <?= isset($table_data) && $table_data->posted == 'Y' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="formCheckcolor2">
                                            Posting Jurnal
                                        </label>
                                        <input type="hidden" class="form-control" placeholder="" name="txtposting" id="txtposting" value="<?= isset($table_data) && $table_data->posted == 'Y' ? 'Y' : 'T' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Five Row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                <theads>
                                    <tr>
                                        <th style="text-align: center">Kode Akun</th>
                                        <th style="text-align: center">Uraian</th>
                                        <th style="text-align: center">Debet</th>
                                        <th style="text-align: center">Kredit</th>
                                        <th style="text-align: center"></th>
                                    </tr>
                                </theads>
                                <tbody>
                                    <?php
                                    if (isset($table_data)) {
                                        $no = 0;
                                        foreach ($table_data_Result as $value) {
                                            $db = db_connect();
                                            $akun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->kodeakun'")->getRow();
                                            $no++;
                                    ?>
                                            <tr>
                                                <td>
                                                    <select name="akun[]" id="akun<?= $no ?>" class="selectakun form-control input-medium">
                                                        <option value="<?= $value->kodeakun; ?>"><?= $akun->namaakun; ?></option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input name="ket[]" id="ket<?= $no ?>" type="text" class="form-control input-xlarge" value="<?= $value->keterangan ?>">
                                                </td>
                                                <td><input name="debet[]" id="debet<?= $no ?>" type="text" class="form-control input-medium rightJustified" value="<?= number_format($value->debet) ?>" onchange="total()"></td>
                                                <td><input name="kredit[]" id="kredit<?= $no ?>" type="text" class="form-control input-medium rightJustified" value="<?= number_format($value->kredit) ?>" onchange="total()"></td>
                                                <td> <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
                                            </tr>
                                            <script>
                                                $(document).ready(function() {
                                                    total()
                                                });
                                            </script>
                                        <?php }
                                    } else {
                                        ?>
                                        <tr>
                                            <td>
                                                <select name="akun[]" id="akun1" class="selectakun form-control input-medium">
                                                    <option value="">--- Pilih Akun ---</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input name="ket[]" id="ket1" type="text" class="form-control input-xlarge" onkeypress="return tabE(this,event)">
                                            </td>
                                            <td><input name="debet[]" id="debet1" type="text" class="form-control input-medium rightJustified" value="0" onchange="total()"></td>
                                            <td><input name="kredit[]" id="kredit1" type="text" class="form-control input-medium rightJustified" value="0" onchange="total()"></td>
                                            <td> <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- six Row -->
                <div class="row">
                    <div class="col-md-3">
                        <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button>
                    </div>
                    <div class="col-md-4">
                        <font color="red"><b><input type="text" class="form-control" id="_selisih" placeholder="Selisih" disabled></b></font>
                    </div>
                    <div class="col-md-2">
                        <font color="red"><b><input type="text" class="form-control rightJustified" placeholder="Jumlah Debet" id="_jumdebet" disabled></b></font>
                    </div>
                    <div class="col-md-2">
                        <font color="red"><b><input type="text" class="form-control rightJustified" placeholder="Jumlah Kredit" id="_jumkredit" disabled></b></font>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <div class="form-actions">
                    <button id="btnsimpan" type="button" onclick="save()" class="btn blue" <?= isset($table_data) &&  $table_data->posted == 'T' || !isset($table_data) ? '' : 'disabled' ?>><i class="fa fa-save"></i> Simpan</button>
                    <!--button id="btncetak" type="button" class="btn yellow" onclick="window.open(_urlcetak())"><i class="fa fa-print"></i> Cetak</button-->
                    <!--button id="btncetak" type="button" class="btn yellow" onclick="window.open(''))"><i class="fa fa-print"></i> Cetak</button-->
                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                </div>
                <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
        </form>
    </div>
</div>

<script>
    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;
        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);

        var akun = "<select name='akun[]' class='selectakun form-control ' id=akun" + idrowx + " ><option value=''>--- Pilih Akun ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ket[]'  id=ket" + idrowx + "   type='text' class='form-control'>";
        td3.innerHTML = "<input name='debet[]' id=debet" + idrowx + "  type='text' class='form-control rightJustified'  value='0' onchange='total()'>";
        td4.innerHTML = "<input name='kredit[]' id=kredit" + idrowx + "  type='text' class='form-control rightJustified'  value='0' onchange='total()'>";
        td5.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash-can'></i></button>";
        initailizeSelect2_akun();
    }

    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
        total();
    }

    function total() {
        try {
            var StatusPosted = $('#txtposting').val();
            var lvluser = "<?= $leveluser ?>";
            var table = document.getElementById('datatable');
            var rowCount = table.rows.length;

            tdebet = 0;
            tkredit = 0;

            for (var i = 1; i < rowCount; i++) {
                var row = table.rows[i];

                debet = row.cells[2].children[0].value;
                var debet1 = Number(debet.replace(/[^0-9\.]+/g, ""));

                kredit = row.cells[3].children[0].value;
                var kredit1 = Number(kredit.replace(/[^0-9\.]+/g, ""));

                tdebet = tdebet + eval(debet1);
                tkredit = tkredit + eval(kredit1);


            }


            document.getElementById("_jumdebet").value = number_format(tdebet);
            document.getElementById("_jumkredit").value = number_format(tkredit);

            if (tdebet > 0 || tkredit > 0) {
                if (tdebet > tkredit) {
                    selisih = tkredit - tdebet;
                    ket = "(Kredit)";
                    document.getElementById("_selisih").value = "Selisih " + number_format(selisih) + " " + ket;
                    document.getElementById("btnsimpan").disabled = true;
                    //document.getElementById("btncetak").disabled=true;

                } else
                if (tdebet < tkredit) {
                    selisih = tdebet - tkredit;
                    ket = "(Debet)";
                    document.getElementById("_selisih").value = "Selisih " + number_format(selisih) + " " + ket;
                    document.getElementById("btnsimpan").disabled = true;
                    //document.getElementById("btncetak").disabled=true;
                } else {
                    selisih = 0;
                    ket = "";
                    document.getElementById("_selisih").value = "";
                    document.getElementById("btnsimpan").disabled = false;
                    //document.getElementById("btncetak").disabled=false;
                }
                if (StatusPosted == "Y") {
                    if (lvluser == 1) {
                        document.getElementById("btnsimpan").disabled = false;
                    } else {
                        document.getElementById("btnsimpan").disabled = true;
                    }

                } else {
                    document.getElementById("btnsimpan").disabled = false;
                }
            } else {
                document.getElementById("_selisih").value = "";
                document.getElementById("btnsimpan").disabled = true;
                //document.getElementById("btncetak").disabled=true;
            }
        } catch (e) {
            alert(e);
        }
    }

    $("#btnsimpan").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('JURNAL UMUM', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('JURNAL UMUM', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (noform == "") {
            swal('JURNAL UMUM', 'Data Belum Lengkap/Belum ada transaksi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('bukubesar/transaksi/jurnalsave') ?>",
                data: $('#frmjurnal').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    swal({
                        title: "JURNAL UMUM",
                        html: "<p> No. Bukti   : <b>" + noform + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        location.href = "<?php echo base_url() ?>bukubesar/transaksi/daftarjurnal";
                    });

                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    $("#error").show().fadeOut(5000);
                }
            });
        }


    }
</script>