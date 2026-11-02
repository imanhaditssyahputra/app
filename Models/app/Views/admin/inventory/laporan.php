<?= main_header('Inventory', 'Laporan') ?>

<div class="row">
    <div class="col-md-12">

        <div class="note note-success">
            <p>
                Laporan - laporan untuk transaksi Inventory
                <br>
            </p>
        </div>

        <br>
        <!-- form-group -->
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>Parameter Laporan
                </div>

            </div>
            <div class="portlet-body form">
                <!-- BEGIN FORM-->
                <form id="frmlaporan" class="form-horizontal form-bordered1" method="post">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Nama Laporan</label>
                                        <div class="col-md-9">
                                            <select id="idlap" name="idlap" class="select2 bs-select form-control" data-show-subtext="true" data-placeholder="Pilih...">
                                                <optgroup label="Inventory">
                                                    <option data-subtext="101" value="101">Daftar Barang</option>
                                                    <option data-subtext="102" value="102">Kartu Stok Persediaan</option>
                                                    <option data-subtext="103" value="103">Umur Persediaan</option>
                                                    <option data-subtext="104" value="104">Laporan Mutasi Cabang</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Mulai</label>
                                        <div class="col-md-3 ">
                                            <input id="tanggal1" name="tanggal1" class="form-control  input-medium" type="date" value="<?=date('Y-m-d')?>" />
                                        </div>
                                        <label class="col-md-3  control-label mt-2">s/d</label>
                                        <div class="col-md-3 ">
                                            <input id="tanggal2" name="tanggal2" class="form-control input-medium" type="date" value="<?=date('Y-m-d')?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Cabang</label>
                                        <div class="col-md-9">
                                            <select id="cabang" name="cabang" class="select2 bs-select form-control" data-show-subtext="true" onchange="getcabang()">
                                                <optgroup label="Daftar Cabang">
                                                    <option data-subtext="" value="">-- Semua --</option>
                                                    <?php foreach($cbg as $data){?>
                                                        <option data-subtext="" value="<?=$data->kode?>"><?=$data->nama?></option>
                                                    <?php }?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Kelompok Barang</label>
                                        <div class="col-md-9">
                                            <select id="kaateg" name="kateg" class="select2 bs-select form-control" data-show-subtext="true">
                                                <optgroup label="Daftar Kelompok Barang">
                                                    <option data-subtext="" value="">-- Semua --</option>
                                                    <?php foreach($kateg as $data){?>
                                                        <option data-subtext="" value="<?=$data->kode?>"><?=$data->nama?></option>
                                                    <?php }?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Barang</label>
                                        <div class="col-md-9">
                                            <select name="barang" id="barang" class="select2 bs-select form-control input-largex">
                                                <optgroup label="Daftar Barang">
                                                    <option data-subtext="" value="">-- Semua --</option>
                                                    <?php foreach($barang as $data){?>
                                                        <option data-subtext="" value="<?=$data->kodeitem?>"><?=$data->namabarang?></option>
                                                    <?php }?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3  control-label mt-2">Gudang</label>
                                        <div class="col-md-9">
                                            <select id="gudang" name="gudang" type="hidden" class="form-control bs-select select_gudang_filter">
                                                <optgroup label="Daftar Gudang">
                                                    <option data-subtext="" value="">--- Semua ---</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions fluid">
                            <div class="col-md-offset-3 col-md-7 text-center">
                                <a class="btn green print_laporan" onclick="javascript:window.open(_urlcetak(),'_blank');">Tampilkan</a>

                                <br />
                                <h4>
                                    <div class="err" id="resultss"></div>
                                </h4>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>

    $('#idlap').change(function() {
        if ($(this).val() == 102 && $('#barang').val() == '') {
            swal('', 'Cabang dan Nama Barang harus dipilih terlebih dahulu ...', '');
            $('#barang').focus();
        }else if($(this).val() == 102 && $('#cabang').val() == ''){
            swal('', 'Cabang harus dipilih ...', '');
            $('#cabang').focus();
        }
    });

    function _urlcetak() {
        if ($('#idlap').val() == 102 && $('#barang').val() == '') {
            swal('', 'Barang harus dipilih terlebih dahulu ...', '');
            $('#barang').focus();
        }else if($('#idlap').val() == 102 && $('#cabang').val() == ''){
            swal('', 'Cabang harus dipilih ...', '');
            $('#cabang').focus();
            document.getElementById("btnsimpan").disabled=true;
        }else{
            var baseurl = "<?php echo base_url()?>";
            var idlap = $('[name="idlap"]').val();
            var tgl1 = $('[name="tanggal1"]').val();
            var tgl2 = $('[name="tanggal2"]').val();
            var brg = $('[name="barang"]').val();
            var cbg = $('[name="cabang"]').val();
            var kat = $('[name="kateg"]').val();
            var gud = $('[name="gudang"]').val();
            var param = idlap + '~' + tgl1 + '~' + tgl2 + '~' + kat + '~' + brg + '~' + cbg + '~' + gud;
            return baseurl + 'inventory/laporan/cetaklaporan/' + param;
        }

    }

    function getcabang(){ 
        var cabang = $('#cabang').val(); 
        initailizeSelect2_gudangFilter(cabang);
        initailizeSelect2_barang_filterCabang(cabang);

    }
</script>