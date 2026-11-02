<div class="container-fluid">
    <?php
    $db = db_connect();
    if (!empty($id)) {
        $table_data = $db->table('tr_penerimaan')->where('terima_register', $id)->get()->getRow();
        $table_dataD = $db->table('tr_penerimaand')->where('terimad_nomor', $table_data->terima_nomor);
        $table_data_detail = $table_dataD->get()->getResult();
        $table_dataDRow = $table_dataD->get()->getRow();
    }
    isset($table_data) ? main_header('Keuangan ', 'View Penerimaan Kas/Bank', 'Daftar Penerimaan Kas/Bank', 'View Penerimaan Kas/Bank', 'kasbank/transaksi/penerimaan') : main_header('Keuangan ', 'Entri Penerimaan Kas/Bank', 'Daftar Penerimaan Kas/Bank', 'Entri Penerimaan Kas/Bank', 'kasbank/transaksi/penerimaan')
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
                <i class="fa fa-reorder"></i><?= !empty($id) ? 'Form View Penerimaan' : 'Form Entri Penerimaan' ?>
            </div>
            <div class="tools">


            </div>


        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form id="frmkeuangan" class="form-horizontal" method="post">
                <input type="hidden" name="id" id="id" value="<?= isset($table_data) ? $table_data->terima_register : 0 ?>">
                <div class="form-body">
                    <!-- First Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Nomor Bukti</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input-medium" name="nomorbukti" readonly id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->terima_nomor : 'Nomor Auto' ?>" value="<?= isset($table_data) ? $table_data->terima_nomor : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Tanggal</label>
                                    <div class="col-md-7">
                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->terima_tanggal)) : date('Y-m-d') ?>" />
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
                                    <label class="control-label pt-2 col-md-3">Kas/Bank</label>
                                    <div class="col-md-9">
                                        <select id="kasbank" name="kasbank" class="form-select input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <?php
                                            foreach ($bank as $row) { ?>
                                                <option value="<?php echo $row->kodeakun; ?>" <?php if (!empty($id)) {
                                                                                                    if ($row->kodeakun == $table_data->terima_kasbank) {
                                                                                                        echo "selected=true";
                                                                                                    }
                                                                                                } ?>><?php echo $row->namaakun; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Cabang</label>
                                    <div class="col-md-9">
                                        <select id="unit" name="unit" class="form-control input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <?php
                                            foreach ($unit as $row) { ?>
                                                <option value="<?php echo $row->kode; ?>" <?php if (!empty($id)) {
                                                                                                if ($row->kode == $table_data->terima_kodecbg) {
                                                                                                    echo "selected=true";
                                                                                                }
                                                                                            } ?>><?php echo $row->nama; ?></option>
                                            <?php } ?>
                                        </select>
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
                                    <label class="control-label pt-2 col-md-3">Diterima dari</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input-medium" placeholder="" name="penerima" id="penerima" value="<?= isset($table_data) ? $table_data->terima_penerima : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Keterangan</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->terima_uraian : '' ?>" onkeypress="return tabE(this,event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Four Row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatable2" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center">Kode Akun</th>
                                            <th style="text-align: center">Uraian / Keterangan</th>
                                            <th style="text-align: center">Jumlah</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($table_data)) {
                                            $no = 0;
                                            foreach ($table_data_detail as $value) {
                                                $akun = $db->query("SELECT * FROM ms_akun where kodeakun='$value->terimad_akun'")->getRow();
                                                $no++;
                                        ?>
                                                <tr>
                                                    <td>
                                                        <select name="akun[]" id="akun<?= $no ?>" class="selectakun form-select input-large">
                                                            <option value="<?= $value->terimad_akun; ?>"><?= $akun->namaakun; ?></option>
                                                        </select>
                                                    </td>
                                                    <td><input name="ket[]" id="ket<?= $no ?>" type="text" class="form-control input-xlarge" value="<?= $value->terimad_uraian; ?>"> </td>
                                                    <td><input name="jumlah[]" id="jumlah<?= $no ?>" type="text" value="<?= number_format($value->terimad_jumlah); ?>" class="form-control input-medium rightJustified" onchange="total()"> </td>
                                                    <td><button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
                                                </tr>
                                                <script>
                                                    $(document).ready(function() {
                                                        total()
                                                    });
                                                </script>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td>
                                                    <select name="akun[]" id="akun1" class="selectakun form-select input-large">
                                                        <option value="">--- Pilih Akun ---</option>
                                                    </select>
                                                </td>
                                                <td> <input name="ket[]" id="ket1" type="text" class="form-control input-xlarge"> </td>
                                                <td> <input name="jumlah[]" id="jumlah1" type="text" class="form-control rightJustified input-medium" value="0" onchange="total()"> </td>
                                                <td> <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa-solid fa-trash-can"></i></button> </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Five Row -->
                    <div class="row">
                        <div class="col-md-7">
                            <div class="wells">
                                <button type="button" onclick="add_row()" class="btn green"><i class="fa fa-plus"></i> </button>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-3 invoice-block">
                            <div class="well">
                                <table>
                                    <tr>
                                        <td width="40%"><strong>TOTAL</strong></td>
                                        <td width="1%"><strong>:</strong></td>
                                        <td width="59" align="right"><strong><span id="_jumlah"></span></strong>&nbsp;&nbsp;</td>
                                        <input type="hidden" id="_h_total" name="_h_total">
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="save()" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                        <div class="btn-group">
                            <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                        </div>
                    </div>
                    <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
            </form>
        </div>
    </div>
</div>
<script>
    var idrow = 2;

    function add_row() {
        var table = document.getElementById("datatable2");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable2').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);

        var akun = "<select name='akun[]' id=akun" + idrowx + " class='selectakun form-select input-large'> <option value=''>--- Pilih Akun ---</option></select>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='ket[]' id=ket" + idrowx + "  type='text' class='form-control input-xlarge'>";
        td3.innerHTML = "<input name='jumlah[]'    id=jumlah" + idrowx + " value='0'  type='text' class='form-control input-medium rightJustified' onchange='total()'>";
        td4.innerHTML = "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";
        initailizeSelect2_akun();
        idrow++;
    }

    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable2").deleteRow(i);
        total();
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('KAS & BANK', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('KAS & BANK', 'Gunakan Tab untuk Next dan Mouse klik pada simpan ...', '');
        }
    });

    function total() {
        var arr = document.getElementsByName('jumlah[]');
        var tot = 0
        for (var i = 0; i < arr.length; i++) {
            jumlah1 = Number(arr[i].value.replace(/[^0-9\.]+/g, ""));
            tot += jumlah1;
        }
        document.getElementById("_jumlah").innerHTML = number_format(tot);
        $('#_h_total').val(tot);
    }

    function save() {
        var tanggal = $('[name="tanggal"]').val();
        $.ajax({
            url: "<?= base_url('kasbank/transaksi/penerimaan/save') ?>",
            data: $('#frmkeuangan').serialize(),
            type: 'POST',
            beforeSend: function() {
                $('#process').css('display', 'block');
            },
            success: function(data) {
                $('#process').css('display', 'none');
                var parse = JSON.parse(data);
                if (parse.status == 1) {
                    swal({
                        title: "PENERIMAAN KAS & BANK",
                        html: "<p> No. Bukti   : <b>" + parse.message + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('kasbank/transaksi/penerimaan') ?>";
                    });
                } else {
                    swal('PENERIMAAN KAS & BANK', 'Data gagal disimpan ... ' + parse.message, '');
                }

            },
            error: function(data) {
                $('#process').css('display', 'none');
                swal('PENERIMAAN KAS & BANK', 'Data Gagal Disimpan', '');
            }
        });
    }
</script>