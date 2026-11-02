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
                                                <option  value="">--- PILIH LAPORAN ---</option>
                                                <option data-subtext="101" value="101">Laporan Pembelian ( GLOBAL )</option>	
                                                <option data-subtext="102" value="102">Laporan Retur Pembelian ( DETAIL )</option>	
                                                <option data-subtext="103" value="103">Laporan Pembelian ( DETAIL )</option>
                                            </optgroup>		
                                            <optgroup label="Hutang">	
                                                <option data-subtext="201" value="201">Faktur Belum Lunas</option>	
                                                <option data-subtext="203" value="203">Umur Hutang</option>	                                                
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
                                                    <?php foreach($supp as $data){?>
                                                        <option data-subtext="" value="<?=$data->kode?>"><?=$data->nama?></option>
                                                    <?php }?>
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
                                        <input id="tanggal1" name="tanggal1" class="form-control  input-medium" type="date" value="<?=date('Y-m-d')?>" />
                                    </div>
                                    <div class="col-md-1">
                                        <label for="sd" class="form-label">S/D</label>
                                    </div>
                                    <div class="col-md-1">
                                        <input id="tanggal2" name="tanggal2" class="form-control input-medium" type="date" value="<?=date('Y-m-d')?>" />
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
                                                <?php foreach($barang as $data){?>
                                                    <option data-subtext="" value="<?=$data->kodeitem?>"><?=$data->namabarang?></option>
                                                <?php }?>
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
                                                <?php foreach($cbg as $data){?>
                                                    <option data-subtext="" value="<?=$data->kode?>"><?=$data->nama?></option>
                                                <?php }?>
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

    $('#idlap').change(function(){
        if($(this).val()==101 || $(this).val()==102){
            document.getElementById('barang').disabled = true;
            document.getElementById('cabang').disabled = true;
        }else if ($(this).val()==103) {
            document.getElementById('cabang').disabled = true;
            document.getElementById('barang').disabled = false;
        }
    });

    function _urlcetak()
    {
        var baseurl = "<?php echo base_url()?>";
        var idlap= $('[name="idlap"]').val();				
        var tgl1 = $('[name="tanggal1"]').val();				
        var tgl2 = $('[name="tanggal2"]').val();				
        var supp = $('[name="suplier"]').val();				
        var brg  = $('[name="barang"]').val();			
        var cbg  = $('[name="cabang"]').val();			
        var param= idlap+'~'+tgl1+'~'+tgl2+'~'+supp+'~'+brg+'~'+cbg;	
        return baseurl + 'pembelian/laporan/cetaklaporan/' + param;
    }
</script>