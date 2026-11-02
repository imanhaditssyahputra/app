<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('tr_transfer')->where('nomor', $id)->get()->getRow();
}
isset($table_data) ? main_header('Keuangan ', 'Transfer Kas/Bank', 'Daftar Transfer Kas/Bank  ', 'Edit Transfer Kas/Bank', 'kasbank/transaksi/transferbank') :  main_header('Keuangan ', 'Transfer Kas/Bank', 'Daftar Transfer Kas/Bank  ', 'Entri Transfer Kas/Bank', 'kasbank/transaksi/transferbank')
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
            <i class="fa fa-reorder"></i><?= !empty($id) ? 'Form Edit Transfer Kas/Bank' : 'Form Entri Transfer Kas/Bank' ?>
        </div>
        <div class="tools">
        </div>
    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="frmtransferkasbank" class="form-horizontal form-body" method="post">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->nomor : 0 ?>">
            <!-- First Row -->
            <div class="row ">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="row">
                            <label class="control-label pt-2 col-md-3">Nomor Bukti</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-medium" readonly name="nomorbukti" id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->nomor : 'Nomor Auto' ?>" value="<?= isset($table_data) ? $table_data->nomor : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="row">
                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                            <div class="col-md-5">
                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tanggal)) : date('Y-m-d') ?>" required />
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
                            <label class="control-label pt-2 col-md-3">Dari Kas/Bank</label>
                            <div class="col-md-7">
                                <select id="sumber" name="sumber" class="form-select input-medium select2" data-placeholder="Pilih..." required>
                                    <?php

                                    foreach ($bank as $row) { ?>
                                        <option value="<?php echo $row->kodeakun; ?>" <?php if (!empty($id)) {
                                                                                            if ($row->kodeakun == $table_data->bank_sumber) {
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
                            <label class="control-label pt-2 col-md-3">Ke Kas/Bank</label>
                            <div class="col-md-7">
                                <select id="tujuan" name="tujuan" class="form-select input-medium select2" data-placeholder="Pilih..." required>
                                    <?php

                                    foreach ($bankTujuan as $value) { ?>
                                        <option value="<?php echo $value->kodeakun; ?>" <?php if (!empty($id)) {
                                                                                            if ($value->kodeakun == $table_data->bank_tujuan) {
                                                                                                echo "selected=true";
                                                                                            }
                                                                                        } ?>><?php echo $value->namaakun; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="PembayaranErr" style='width:89%;text-align:center;'></span>
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
                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                            <div class="col-md-5">
                                <textarea name="keterangan" class="form-control" value="" id="keterangan" cols="20" rows="2" required><?= isset($table_data) ? $table_data->uraian : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="row">
                            <label class="control-label pt-2 col-md-3">Nilai Transfer</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control input-large" placeholder="" name="jumlah" id="jumlah" value="<?= isset($table_data) ? $table_data->jumlah : '' ?>" required>
                            </div>
                            <span id="informationErr" style='width:89%;text-align:center;'></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                <div class="btn-group">
                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                </div>
            </div>
            <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
        </form>
    </div>
</div>

<script>
    $('#frmtransferkasbank').submit(function(e) {
        e.preventDefault();
        var tanggal = $('#tanggal').val();
        $.ajax({
            type: "post",
            url: "<?= base_url('kasbank/transaksi/transferbank/save') ?>",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: function() {
                $('#process').css('display', 'block');
            },
            success: function(data) {
                $('#process').css('display', 'none');
                if (data.status == 1) {
                    swal({
                        title: "TRANSFER KAS & BANK",
                        html: "<p> No. Bukti   : <b>" + data.message + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('kasbank/transaksi/transferbank') ?>";
                    });
                } else {
                    swal('TRANSFER KAS & BANK', 'Data gagal disimpan ... ' + data.message, '');
                }

            },
            error: function(data) {
                $('#process').css('display', 'none');
                swal('TRANSFER KAS & BANK', 'Data Gagal Disimpan', '');
            }
        });
    });
</script>