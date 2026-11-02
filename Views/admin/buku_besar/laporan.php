<?= main_header('Buku Besar ', 'Laporan', 'Buku Besar', 'Laporan') ?>
<div class="row">
    <div class="col-md-12">

        <div class="note note-success">
            <p class="mb-0">
                Laporan - laporan untuk Buku Besar
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
                                            <optgroup label="Keuangan">
                                                <option data-subtext="101" value="101">Daftar Jurnal</option>
                                                <option data-subtext="102" value="102">Buku Besar</option>
                                                <option data-subtext="103" value="103">Neraca Saldo</option>
                                                <option data-subtext="104" value="104">Neraca Akhir</option>
                                                <option data-subtext="105" value="105">Laba Rugi</option>
                                            </optgroup>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
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
                            <div class="col-12 mt-2">
                                <div class="mb-2 row">
                                    <div class="col-md-3">
                                        <label for="suplier" class="form-label">Kode Akun Perkiraan</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select id="perk" name="perk" class="select2 bs-select form-control" data-show-subtext="true">
                                            <optgroup label="Kode Akun Perkiraan">
                                                <option data-subtext="" value="">-- Semua --</option>
                                                <?php
                                                foreach ($bank as $row) { ?>
                                                    <option data-subtext="" value="<?php echo $row->kodeakun; ?>"><?php echo $row->namaakun; ?></option>
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
                                    <a class="btn btn-success text-light" onclick="javascript:window.open(_urlcetak(),'_blank');">Tampilkan</a>
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
    function _urlcetak() {
        var baseurl = "<?php echo base_url() ?>";
        var idlap = $('[name="idlap"]').val();
        var tgl1 = $('[name="tanggal1"]').val();
        var tgl2 = $('[name="tanggal2"]').val();
        var akun = $('[name="perk"]').val();
        var cbg = $('[name="cabang"]').val();
        var param = idlap + '~' + tgl1 + '~' + tgl2 + '~' + akun + '~' + cbg;

        return baseurl + 'bukubesar/laporan/cetaklaporan/' + param;
    }
</script>