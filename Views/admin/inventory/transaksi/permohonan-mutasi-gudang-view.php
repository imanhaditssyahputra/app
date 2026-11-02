<?= main_header('Inventory', 'Permintaan Mutasi Barang Antar Gudang', 'Daftar Permintaan Mutasi', 'Edit Permintaan Mutasi Antar Gudang') ?>


<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>*Data Edit
        </div>
    </div>

    <div class="portlet-body form">
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fa fa-file"></i>
                                Permintaan Mutasi Barang
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Tanggal</label>
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
                                                    <input type="text" placeholder="Otomatis" name="nomorbukti" class="form-control" value="<?php echo $header->nomohon;?>" readonly>
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
                                            <label class="col-md-3 control-label pt-2">Dari Gudang Asal</label>
                                            <div class="col-md-4">
                                                <select id="gudang_asal" name="gudang_asal" class="form-select input-medium select_gudang_filter" data-placeholder="Pilih..." >
                                                    <?php
                                                    foreach($GudangAsal as $row) { ?>
                                                        <option value="<?php echo $row->kode;?>"
                                                        <?php if($row->kode==$header->gudang_asal){ echo "selected=true";}?>>
                                                        <?php echo $row->nama;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Ke Gudang Tujuan</label>
                                            <div class="col-md-4">
                                                <select id="gudang_tujuan" name="gudang_tujuan" class="form-select input-medium select_gudang_filter" onchange="getbarang()" data-placeholder="Pilih...">
                                                    <?php
                                                    foreach($GudangTujuan as $row) { ?>
                                                        <option value="<?php echo $row->kode;?>"
                                                        <?php if($row->kode==$header->gudang_tujuan){ echo "selected=true";}?>>
                                                        <?php echo $row->nama;?></option>
                                                    <?php } ?>
                                                </select>
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
                                                        <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?php echo $header->keterangan;?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="background-color: #e6f7ff; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.2) 0px 2px 8px 2px; height: auto; overflow-x: scroll; padding: 5px; width: auto;">
                                <div class="col-md-12">
                                    <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th width="30%" style="text-align: center">Kode/Nama Barang</th>
                                                <th width="10%" style="text-align: center">qty</th>
                                                <th width="10%" style="text-align: center">Stock Asal</th>
                                                <th width="10%" style="text-align: center">satuan</th>
                                            </tr>
                                            <thead>
                                            <tbody>
                                                <?php
                                                    $no=1;
                                                    foreach($detail as $row){
                                                ?>
                                                <tr>
                                                    <td width="30%">
                                                        <select name="kode[]" id="kode1" class="barang_filter form-select" onchange="showbarangname(this.value, <?php echo $no;?>)" required/>
                                                        <option value="<?= $row->kodeitem;?>"><?= $row->namabarang;?></option>
                                                        </select>
                                                    </td>

                                                    <td width="10%">
                                                        <input name="qty[]" value="<?= $row->qtymohon - $row->qtyterima;?>" onchange="check_qty(this.value, <?php echo $no;?>)" id="qty<?php echo $no;?>" type="text" class="form-control rightJustified" required>
                                                        <input name="qtymohon[]" value="<?= $row->qtymohon;?>" id="qtymohon<?php echo $no;?>" type="hidden" class="form-control rightJustified" required>
                                                        <input name="qtyterima[]" value="<?= $row->qtyterima;?>" id="qtyterima<?php echo $no;?>" type="hidden" class="form-control rightJustified" required>
                                                    </td>
                                                    <?php
                                                        $db = db_connect();
                                                        $qstock = $db->query("SELECT * FROM inv_barang where kodeitem ='$row->kodeitem' and kdgudang='$header->gudang_asal' and kdcbg='$cabang'")->getRow();
                                                        if($qstock == null  || $qstock->stok <= 0){
                                                            $stokasal = "0";
                                                        }else{
                                                            $stokasal = $qstock->stok;
                                                        }
                                                    ?>
                                                    <td width="10%">
                                                        <input name="stock[]" oninput="totalline(1)" value="<?= $stokasal;?>" id="stock<?php echo $no;?>" type="text" class="form-control rightJustified" readonly>
                                                        <input name="qtyhitung[]" value="<?= $row->qtymohon;?>" id="qtyhitung<?php echo $no;?>" type="hidden" class="form-control rightJustified" required>
                                                    </td>
                                                    <td width="10%"><input name="sat[]" id="sat<?php echo $no;?>" type="text" value="<?= $row->satuan;?>" class="form-control"></td>
                                                </tr>
                                                <?php $no++; } ?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- tab1-->
                    </div>
                    <!--tab-->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well">
                                <button type="button" onclick="save()" class="btn blue" id="save_btn1"><i class="fa fa-save"></i> Simpan</button>
                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>



<script>

    //hitung qty
    function check_qty(str, id) {
        var str_a = $('#kode' + id).val();
        var str_b = parseInt($('#qtymohon' + id).val());
        var str_c = parseInt($('#qtyterima' + id).val());
        var vstr = parseInt(str);
        var vid = id;
        var sisa = str_b - str_c ;
        if(vstr == 0){
            var hitungsisa = str_c;
        }else{
            var hitungsisa = str_c + vstr ;
        }

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        var qtysisa = row.cells[2].children[1].value = hitungsisa;
        

    }

    //Save Data Permohonan
    function save() {
        var gudangTujuan = $('#gudang_tujuan').val();
        var gudangasal = $('#gudang_asal').val();
        var tanggal = $('[name="tanggal"]').val();
        if (gudangTujuan == null || gudangasal == null) {
            swal('MUTASI GUDANG', 'Gudang tujuan atau gudang asal belum diisi belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/transaksi/permohonanmutasigudang/saveview/1') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                success: function(data) {
                    document.getElementById("save_btn1").disabled = true;
                    swal({
                        title: "PERMINTAAN MUTASI GUDANG",
                        html: "<p> No. PERMINTAAN MUTASI   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        location.href = "<?= BASEURL . 'inventory/transaksi/permohonanmutasigudang' ?>";
                    });

                },
                error: function(data) {
                    swal('PERMOHONAN MUTASI', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    

    // function _urlcetak(data) {
    //     var baseurl = "<?php echo base_url()?>";
    //     var param = $('[name="nomorbukti"]').val();
    //     return baseurl + 'inventory_mohon_mutasi_cabang/cetak/' + data;
    // }
</script>