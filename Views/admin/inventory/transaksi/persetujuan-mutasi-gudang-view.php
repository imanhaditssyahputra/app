<?= main_header('Inventory', 'Approval Mutasi Barang Antar Gudang') ?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-reorder"></i>*Data View
                </div>


            </div>

            <div class="portlet-body form">
                <form id="frmpenjualan" class="form-horizontal" method="post">
                    <div class="form-body">
                        <div class="tabbable tabbable-custom tabbable-full-width">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#tab1" data-toggle="tab">
                                        <i class="fa fa-file"></i>
                                        Approval Mutasi Barang Cabang
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row"> <label class="col-md-3 control-label pt-2">Tanggal</label>
                                                    <div class="col-md-4">
                                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?php echo date('Y-m-d');?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">No. Bukti</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" readonly value="">
                                                            <input type="text" placeholder="Otomatis" class="form-control input-medium" value="<?php echo $header->noaccmutasi;?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">Gudang terima</label>
                                                    <div class="col-md-6">
                                                        <div class="input-group position-reletive">
                                                            <select id="gudang_terima" name="gudang_terima" class="form-control input-medium select_gudang_filter" readonly>
                                                            <?php
                                                            foreach($GudangTerima as $row) { ?>
                                                                <option value="<?php echo $row->kode;?>"
                                                                <?php if($row->kode==$header->gudangterima){ echo "selected=true";}?>>
                                                                <?php echo $row->nama;?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="row">
                                                    <label class="col-md-3 control-label pt-2">Gudang kirim</label>
                                                    <div class="col-md-4">
                                                        <input type="text" id="ngudang_kirim" name="ngudang_kirim" class="form-control input-medium" value="<?=$GudangKirim->nama?>" readonly>
                                                        <input type="hidden" id="gudang_kirim" name="gudang_kirim" class="form-control input-medium" value="<?=$header->gudangkirim?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">Keterangan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?=$header->keterangan?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No. Mutasi</label>
                                                            <div class="col-md-6">
                                                                <div class="input-group position-reletive">
                                                                    <input type="text" class="form-control input-large" placeholder="" name="nomutasi" id="nomutasi" value="<?=$header->nomormutasi?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <div class="row">
                                                            <label class="col-md-3 control-label pt-2">No Permintaan</label>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control input-large" placeholder="" name="nomohon" id="nomohon" value="<?=$header->nomohon?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="background-color: #e6f7ff; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 8px 2px; height: auto; overflow-x: scroll; padding: 5px; width: auto;">
                                        <div class="col-md-12">

                                            <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable" style="width: 130%">
                                                <thead>
                                                    <tr>
                                                        <th width="30%" style="text-align: center">Kode/Nama Barang</th>
                                                        <th width="15%" style="text-align: center">No Batch</th>
                                                        <th width="15%" style="text-align: center">Tanggal EXP</th>
                                                        <th width="10%" style="text-align: center">qty kirim</th>
                                                        <th width="10%" style="text-align: center">sat</th>
                                                        <th width="3%"></th>
                                                    </tr>
                                                    <thead>
                                                    <tbody>
                                                        <?php
                                                            $no=1;
                                                            foreach($detail as $row){
                                                        ?>
                                                        <tr>
                                                            <td width="30%">
                                                                <select name="kode[]" id="kode<?=$no?>" class="form-control" readonly>
                                                                    <option value="<?= $row->kodeitem;?>"><?= $row->namabarang;?></option>
                                                                </select>
                                                            </td>
                                                            <td width="15%">
                                                                <input name="no_batch[]" readonly="" value="<?= $row->nobatch;?>" id="no_batch<?=$no?>" type="text" class="form-control rightJustified">
                                                            </td>
                                                            <td width="15%"><input name="tgl_exp[]" value="<?= $row->tglexp;?>" id="tgl_exp<?=$no?>" type="text" readonly class="form-control rightJustified">
                                                            </td>

                                                            <td width="10%">
                                                                <input name="qty[]" value="<?= $row->qtyacc;?>" id="qty<?=$no?>" type="text" class="form-control rightJustified" readonly>
                                                            </td>
                                                            <td width="10%"><input name="sat[]" id="sat<?=$no?>" type="text" value="<?= $row->satuan;?>" class="form-control " readonly>
                                                            </td>
                                                            <td width="3%">
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php $no++; } ?>
                                                    </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="col-xs-9">
                                                </div>
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
                                        <button type="button" onclick="cetak()" class="btn blue" id="save_btn1"><i class="fa fa-print"></i> Cetak</button>
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
    var cabang = "<?= $cabang?>";

    //Delete Table Element Current Row
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }
    
    //Save data persetujuan mutasi gudang
    function save() {
        var gudangasal = $('[name="gudang_kirim"]').val();
        var gudangtujuan = $('[name="gudang_terima"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (gudangasal == "" || gudangtujuan == "") {
            swal('APPROVAL MUTASI GUDANG', 'Gudang belum diisi ...', '');
        } else {
            $.ajax({
                url: '<?= base_url('inventory/transaksi/persetujuanmutasigudang/save/1') ?>',
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "APPROVAL MUTASI GUDANG",
                        html: "<p> No. Mutasi   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        location.href = "<?= BASEURL . 'inventory/transaksi/persetujuanmutasigudang' ?>";
                    });

                },
                error: function(data) {
                    swal('APPROVAL MUTASI GUDANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }
</script>