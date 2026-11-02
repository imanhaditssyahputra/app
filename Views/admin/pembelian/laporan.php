<?= main_header('Pembelian', 'Laporan', '', 'Laporan') ?>
<div class="row">
    <div class="col-md-12">

        <div class="note note-success">
            <p class="mb-0">
                Laporan - laporan untuk transaksi pembelian
            </p>
        </div>

        <br>

        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>Parameter Laporan
                </div>

            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form id="frmlaporan" class="form-horizontal form-bordered1">
                    <div class="form-body">
                        <input type="hidden" value="" id="id">
                        <div class="row ms-0">
                            <div class="col-12 mt-2">
                                <div class="mb-2 row">
                                    <div class="col-md-3">
                                        <label for="nama_laporan" class="form-label">Nama Laporan</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="idlap" name="idlap" class="select2 bs-select form-control" data-show-subtext="true" data-placeholder="Pilih...">
                                            <optgroup label="PEMBELIAN GLOBAL">
                                                <option value="">--- PILIH LAPORAN ---</option>
                                                <option data-subtext="101" value="101">Laporan Pembelian ( GLOBAL )</option>
                                                <option data-subtext="102" value="102">Laporan Retur Pembelian ( DETAIL )</option>
                                                <option data-subtext="103" value="103">Laporan Pembelian ( DETAIL )</option>
                                            </optgroup>
                                            <optgroup label="Hutang">
                                                <option data-subtext="201" value="201">Faktur Belum Lunas</option>
                                                <option data-subtext="202" value="202">Laporan Hutang</option>
                                                <option data-subtext="203" value="203">Umur Hutang</option>
                                                <option data-subtext="204" value="204">Laporan hutang supplier yang sudah bayar/lunas</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="mb-2 row">
                                    <div class="col-md-3">
                                        <label for="suplier" class="form-label">Suplier</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="suplier" name="suplier" class="select2 bs-select form-control" data-show-subtext="true">
                                            <optgroup label="Daftar Supplier">
                                                <option data-subtext="" value="">-- Semua --</option>
                                                <?php foreach ($supp as $data) { ?>
                                                    <option data-subtext="" value="<?= $data->kode ?>"><?= $data->nama ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2 date-inputs" style="display: none;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="mulai" class="form-label">Mulai</label>
                                    </div>
                                    <div class="col-md-5">
                                        <input id="tanggal1" name="tanggal1" class="form-control  input-medium" type="date" value="<?= date('Y-m-d') ?>" />
                                    </div>
                                    <div class="col-md-1">
                                        <label for="sd" class="form-label">S/D</label>
                                    </div>
                                    <div class="col-md-1">
                                        <input id="tanggal2" name="tanggal2" class="form-control input-medium" type="date" value="<?= date('Y-m-d') ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2 year-inputs" style="display: none;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="mulai" class="form-label">bulan</label>
                                    </div>
                                    <div class="col-md-5">
                                        <select id="periodebln" name="periodebln" class=" input-sm SelectInModalAkun input-medium " data-placeholder="Pilih...">
                                            <option value="NONE">&nbsp</option>
                                            <?php
                                            $model_Global =  model('Global_model', 'M_GLOBAL');
                                            for ($i = 1; $i <= 12; $i++) {
                                                if ($model_Global->_LoadProfile()->periode_bulan == $i) { ?>
                                                    <option value="<?php echo $i; ?>" selected='true'><?php echo $model_Global->_periodebulan(); ?></option>
                                                <?php
                                                } else {
                                                ?>
                                                    <option value="<?php echo $i; ?>"><?php echo $model_Global->_namabulan($i); ?></option>
                                            <?php
                                                }
                                            }

                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label for="sd" class="form-label">tahun</label>
                                    </div>
                                    <div class="col-md-1">
                                        <input id="periodethn" name="periodethn" class="form-control input-medium" type="text" value="<?= $model_Global->_LoadProfile()->periode_tahun ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="mb-2 row">
                                    <div class="col-md-3">
                                        <label for="barang" class="form-label">Barang</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select name="barang" id="barang" class="select2 bs-select form-control input-largex">
                                            <optgroup label="Daftar Barang">
                                                <option data-subtext="" value="">-- Semua --</option>
                                                <?php foreach ($barang as $data) { ?>
                                                    <option data-subtext="" value="<?= $data->kodeitem ?>"><?= $data->namabarang ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="mb-2 row">
                                    <div class="col-md-3">
                                        <label for="cabang" class="form-label">Cabang</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="cabang" name="cabang" class="select2 bs-select form-control" data-show-subtext="true" onchange="getcabang()">
                                            <optgroup label="Daftar Cabang">
                                                <option data-subtext="" value="">-- Semua --</option>
                                                <?php foreach ($cbg as $data) { ?>
                                                    <option data-subtext="" value="<?= $data->kode ?>"><?= $data->nama ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8 my-3">
                                <div class="text-center">
                                    <a class="btn green text-light" id="tampilkan" onclick="javascript:window.open(_urlcetak(),'_blank');">Tampilkan</a>
                                    <a class="btn btn-success text-light btn-xls" id="tampilkan" onclick="javascript:window.open(_urlexportxls());">Export EXCEL</a>
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
    $('#idlap').change(function() {
        if ($(this).val() == 101 || $(this).val() == 102) {
            document.getElementById('barang').disabled = true;
            document.getElementById('cabang').disabled = true;
        } else if ($(this).val() == 103) {
            document.getElementById('cabang').disabled = true;
            document.getElementById('barang').disabled = false;
        }

        if ($(this).val() == 102 || $(this).val() == 103) {
            swal('', 'Supplier harus dipilih terlebih dahulu ...', '');
            $('#suplier').focus();
        }
        if ($(this).val() == 102 && $('#suplier').val() == '' || $(this).val() == 103 && $('#suplier').val() == ''|| $(this).val() == 204 && $('#suplier').val() == '') {
            swal('', 'Supplier tidak boleh kosong...', '');
            document.getElementById("tampilkan").disabled = true;
        }

        if ($(this).val() == 101 || $(this).val() == 102 || $(this).val() == 103 || $(this).val() == 201 || $(this).val() == 203 || $(this).val() == 204) {
            $('.date-inputs').show();
        } else {
            $('.date-inputs').hide();
        }

        if ($(this).val() == 202) {
            $('.year-inputs').show();
        } else {
            $('.year-inputs').hide();
        }

        if ($(this).val() == 204) {
            document.getElementById('barang').disabled = true;
        }
    });

    function _urlcetak() {

        var baseurl = "<?php echo base_url() ?>";
        var idlap = $('[name="idlap"]').val();
        var tgl1 = $('[name="tanggal1"]').val();
        var tgl2 = $('[name="tanggal2"]').val();
        var bln = $('[name="periodebln"]').val();
        var thn = $('[name="periodethn"]').val();
        var supp = $('[name="suplier"]').val();
        var brg = $('[name="barang"]').val();
        var cbg = $('[name="cabang"]').val();
        var param = idlap + '~' + tgl1 + '~' + tgl2  + '~' + bln + '~' + thn + '~' + supp + '~' + brg + '~' + cbg;
        return baseurl + 'pembelian/laporan/cetaklaporan/' + param;
    }

    function _urlexportxls() {
        var baseurl = "<?php echo base_url() ?>";
        var idlap = $('[name="idlap"]').val();
        var tgl1 = $('[name="tanggal1"]').val();
        var tgl2 = $('[name="tanggal2"]').val();
        var bln = $('[name="periodebln"]').val();
        var thn = $('[name="periodethn"]').val();
        var supp = $('[name="suplier"]').val();
        var brg = $('[name="barang"]').val();
        var cbg = $('[name="cabang"]').val();
        var param = idlap + '~' + tgl1 + '~' + tgl2  + '~' + bln + '~' + thn + '~' + supp + '~' + brg + '~' + cbg;
        return baseurl + 'pembelian/laporan/CetakLaporanExcel/' + param;
    }
</script>