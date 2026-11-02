<?= main_header('Penjualan ', 'Laporan',) ?>



<div class="row">
    <div class="col-md-12">
        <div class="note note-success">
            <p>Laporan - laporan untuk transaksi Penjualan<br></p>
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
                <form id="frmlaporan" class="form-horizontal form-bordered1" method="post">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3 control-label pt-2">Nama Laporan</label>
                                        <div class="col-md-9">
                                            <select id="idlap" name="idlap" class="form-select select2">
                                                <optgroup label="Penjualan">	
                                                    <option value="">--- PILIH LAPORAN ---</option>
                                                    <option data-subtext="103" value="103">Laporan Penjualan ( GLOBAL )</option>
                                                    <option data-subtext="105" value="105">Laporan Retur Penjualan ( DETAIL )</option>
                                                    <option data-subtext="106" value="106">Laporan Penjualan ( DETAIL )</option>
                                                </optgroup>		
                                                <optgroup label="Piutang">	
                                                    <option data-subtext="201" value="201">Faktur Belum Lunas</option>
                                                    <option data-subtext="203" value="203">Umur Piutang</option>
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
                                        <label class="col-md-3 control-label pt-2">Mulai</label>
                                        <div class="col-md-3">
                                            <input id="tanggal1" name="tanggal1" class="form-control  input-medium" type="date" value="<?=date('Y-m-d')?>" />
                                        </div>
                                        <label class="col-md-3 control-label pt-2">s/d</label>
                                        <div class="col-md-3">
                                            <input id="tanggal2" name="tanggal2" class="form-control  input-medium" type="date" value="<?=date('Y-m-d')?>" />

                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <label class="col-md-3 control-label pt-2">Customer</label>
                                        <div class="col-md-9">
                                            <select id="cust" name="cust" class="select2 bs-select form-select" data-show-subtext="true" data-placeholder="Pilih...">
                                                <optgroup label="Daftar Customer">
                                                    <option data-subtext="" value="">-- Semua --</option>
                                                    <?php foreach($cust as $data){?>
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
                                        <label class="col-md-3 control-label pt-2">Barang</label>
                                        <div class="col-md-9">
                                            <select name="barang" data-placeholder="Pilih..." id="barang" class="select2 bs-select form-select input-largex">
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
                                        <label class="col-md-3 control-label pt-2">Cabang</label>
                                        <div class="col-md-9">
                                            <select id="cabang" name="cabang" class="select2 bs-select form-select" data-show-subtext="true">
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
                                        <label class="col-md-3 control-label pt-2">Marketing</label>
                                        <div class="col-md-9">
                                            <select id="marketing" name="marketing" class="select2 bs-select form-select" data-show-subtext="true">
                                                <optgroup label="Daftar Marketing">
                                                    <option data-subtext="" value="">-- Semua --</option>
                                                    <?php foreach($sales as $data){?>
                                                        <option data-subtext="" value="<?=$data->id?>"><?=$data->nama_sales?></option>
                                                    <?php }?>
                                                </optgroup>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-8 my-3">
                            <div class="text-center">
                                <a class="btn green print_laporan" onclick="javascript:window.open(_urlcetak(),'_blank');">Tampilkan</a>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
$('#idlap').change(function(){
	if($(this).val()==103 || $(this).val()==105){
		document.getElementById('barang').disabled = true;
        //document.getElementById('cabang').disabled = true;
	}else if ($(this).val()==106) {
		//document.getElementById('cabang').disabled = true;
		document.getElementById('barang').disabled = false;
	}
});

function _urlcetak()
{
	var baseurl = "<?php echo base_url()?>";
	var idlap= $('[name="idlap"]').val();				
	var tgl1 = $('[name="tanggal1"]').val();				
	var tgl2 = $('[name="tanggal2"]').val();				
	var cust = $('[name="cust"]').val();				
	var brg  = $('[name="barang"]').val();			
	var cbg  = $('[name="cabang"]').val();			
	var marketing  	= $('[name="marketing"]').val();			
	var param= idlap+'~'+tgl1+'~'+tgl2+'~'+cust+'~'+brg+'~'+cbg+'~'+marketing;	
    return baseurl+'penjualan/laporan/cetaklaporan/'+param;
}

</script>