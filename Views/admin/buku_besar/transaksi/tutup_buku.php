<?php
$model_Global =  model('Global_model', 'M_GLOBAL');
main_header('Akuntansi ', 'Tutup Buku')
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
<div class="note note-success">
    <p>
        Modul ini berfungsi untuk Proses Tutup Buku Periode Berjalan.<br>
        Pastikan Transaksi sudah selesai sebelum proses ini.
        <br>
    </p>
</div>

<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>Parameter Proses
        </div>

    </div>
    <div class="portlet-body form">
        <!-- BEGIN FORM-->
        <form id="frmtutupbuku" class="form-horizontal form-bordered1" method="post">
            <div class="form-body">
                <div class="row mb-3">
                    <label class="control-label col-md-3"> Bulan / Tahun Tutup:</label>
                    <div class="col-md-3">
                        <select id="blntutup" name="blntutup" class="form-select input-sm select2me input-medium" data-placeholder="Pilih..." disabled>
                            <option value="NONE">&nbsp</option>
                            <?php
                            for ($i = 1; $i <= 12; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if ($bulan == $i) {
                                                                        echo "selected=true";
                                                                    } ?>><?php echo $model_Global->_namabulan($i); ?></option>
                            <?php
                            }

                            ?>

                        </select>

                    </div>
                    <div class="col-md-2">
                        <input maxlength="4" type="text" size="3" name="thntutup" id="thntutup" class="form-control" value="<?php echo $tahun; ?>" disabled />
                    </div>

                </div>

                <div class="row mb-3">
                    <label class="control-label col-md-3"> Bulan / Tahun Dibuka :</label>
                    <div class="col-md-3">
                        <select id="blnbuka" name="blnbuka" class="form-select input-sm select2me input-medium" data-placeholder="Pilih..." disabled>
                            <option value="NONE">&nbsp</option>
                            <?php
                            for ($i = 1; $i <= 12; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if ($bulan_buka == $i) {
                                                                        echo "selected=true";
                                                                    } ?>><?php echo $model_Global->_namabulan($i); ?></option>
                            <?php

                            }

                            ?>

                        </select>

                    </div>
                    <div class="col-md-2">
                        <input maxlength="4" type="text" size="5" name="thnbuka" id="thnbuka" class="form-control" value="<?php echo $tahun_buka; ?>" disabled />
                    </div>
                </div>
            </div>
            <div class="row form-actions fluid">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <button type="button" class="btn blue" onclick="save()" id="btnproses">Tutup Buku</button>
                    <br />
                    <h4>
                        <div class="err" id="resultss"></div>
                    </h4>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function save() {

        var thntutup = $("#thntutup").val();
        var thnbuka = $("#thnbuka").val();
        var blntutup = $("#blntutup").val();
        var blnbuka = $("#blnbuka").val();

        $("#resultss").html('');


        if (thntutup.length > 0 && thnbuka.length > 0 && blnbuka.length > 0 && blntutup.length > 0) {
            var qry = 'thntutup=' + thntutup + '&thnbuka=' + thnbuka + '&blntutup=' + blntutup + '&blnbuka=' + blnbuka;
            document.getElementById('btnproses').disabled = true;

            $.ajax({
                type: "POST",
                url: "<?= base_url('bukubesar/transaksi/tutupbuku/save') ?>",
                data: qry,
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(html) {

                    document.getElementById('btnproses').disabled = false;
                    if (html.trim() == "ok") {
                        $('#process').css('display', 'none');
                        $("#resultss").html('<span style="color:blue;">Proses Selesai...</span>');
                    } else if (html.trim() == "not ok") {
                        $('#process').css('display', 'none');
                        $("#resultss").html('<span style="color:red;">Data sudah pernah diproses...</span>');
                    } else {
                        $('#process').css('display', 'none');
                        $("#resultss").html('<span style="color:red;">Tidak ada data yang diproses...</span>');
                    }
                }
            });
        }
        e.preventDefault();


    };
</script>