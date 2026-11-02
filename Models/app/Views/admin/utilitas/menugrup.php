<!-- Bootstrap Css -->
<link href="<?= BASEURL ?>public/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= BASEURL ?>public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/ajf-css.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/style-metronic.css" rel="stylesheet" type="text/css" />
    <!-- Data Table Css  -->
    <link href="<?= BASEURL ?>public/assets/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- <link rel="stylesheet" href="cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> -->
    <!-- App Css-->
    <link href="<?= BASEURL ?>public/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <script src="<?= BASEURL ?>public/assets/datatable/jquery-3.5.1.js"></script>
    <!-- sweet alert css-->
    <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.css" rel="stylesheet" type="text/css"> 

    <!-- select2 CSS -->
    <link href="<?= BASEURL ?>public/assets/css/select2.min.css" rel="stylesheet" type="text/css"/>

<div class="note note-success">
    <p>
        NAMA GRUP : <?php echo $namagrup;?><br>
    </p>
</div>

<div class="row">
    <div class="col-md-7 col-sm-12">
        <form id="frmgrup" method="post" action="">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-user"></i>Daftar Menu Grup
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn blue"><i class="fa fa-check"></i> Simpan</button>
                    </div>
                </div>
                <div class="portlet-body table-responsive">
                    <table class="table table-striped table-bordered table-hover display" id="t2">
                        <thead>
                            <tr>
                                <th style="width:8px;" rowspan="2">
                                    <input type="checkbox" class="group-checkable " data-set="#t2 .checkboxes" />
                                </th>
                                <th style="text-align: center;" rowspan="2">Kode</th>
                                <th style="text-align: center;" rowspan="2">Nama Menu</th>
                                <th style="text-align: center;" rowspan="2">Tambah</th>
                                <th style="text-align: center;" colspan="2">Edit</th>
                                <th style="text-align: center;" rowspan="2">Hapus</th>
                                <th style="text-align: center;" rowspan="2">cetak</th>
                                <th style="text-align: center;" colspan="2">Tampil Harga</th>
                                <th style="text-align: center;" rowspan="2">&nbsp</th>
                            </tr>
                            <tr>
                                <th align="center">Transaksi</th>
                                <th align="center">Percust</th>
                                <th align="center">Harga</th>
                                <th align="center">Profit</th>
                            </tr>
                        </thead>
                            
                        <tbody>

                            <?php
							   $no=1; 
                               foreach($modul as $row)
                               {   ?>
                            <tr class="show1" id="row_<?php echo $row->nomor;?>">
                                <td>
                                    <input type="checkbox" class="checkboxes" value="<?php echo $row->nomor_modul;?>"
                                        name="<?php echo "notransG".$no;?>" id="<?php echo "notransG".$no;?>" />
                                </td>
                                <td><?php echo $row->nomor_modul;?></td>
                                <td><?php echo $row->nama;?></td>
                                <td align="center"><input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->uadd==1) { ?>checked<?php }?> name="<?php echo "uadd".$no;?>" />
                                </td>
                                <td align="center">
                                    <input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->uedit==1) { ?>checked<?php }?>
                                        name="<?php echo "uedit".$no;?>" />
                                </td>
                                <td align="center">
                                    <input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->ueditpercust==1) { ?>checked<?php }?>
                                        name="<?php echo "ueditpercust".$no;?>" />
                                </td>
                                <td align="center">
                                    <input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->udel==1) { ?>checked<?php }?> name="<?php echo "udel".$no;?>" />
                                </td>
                                <td align="center"><input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->ucetak==1) { ?>checked<?php }?>
                                        name="<?php echo "ucetak".$no;?>" /></td>
                                <td align="center"><input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->uharga==1) { ?>checked<?php }?>
                                        name="<?php echo "uharga".$no;?>" /></td>
                                <td align="center"><input type="checkbox" class="checkboxes" value="1"
                                        <?php if($row->uhargaprofit==1) { ?>checked<?php }?>
                                        name="<?php echo "uhargaprofit".$no;?>" /></td>
                                <td align="center">
                                    <button class="btn btn-primary" onclick="delData(<?=$row->nomor;?>)"><i class="fa-solid fa-trash text-white"></i>Hapus</button>
                                </td>
                            </tr>
                            <?php $no++;} ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <input type="hidden" name="nomorG" id="nomorG" value="<?php echo $idgrup;?>" />
            <input type="hidden" id="jumdataG" name="jumdataG" value="<?php echo $no-1; ?>" />

        </form>

    </div>
    <div class="col-md-5 col-sm-12">
        <form id="frmmenu" method="post" action="">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Daftar Menu Aplikasi
                    </div>
                    <div class="actions">

                        <button type="submit" class="btn green"><i class="fa fa-plus"></i> Tambah</button>


                    </div>

                </div>
                <div class="portlet-body table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="t3">
                        <thead>
                            <tr>
                                <th class="table-checkbox">
                                    <input type="checkbox" class="group-checkable" data-set="#t3 .checkboxes" />
                                </th>
                                <th style="text-align: center">Kode</th>
                                <th style="text-align: center">Nama Menu Aplikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                               $no = 1;
                               foreach($modulapp as $row)
                               {   ?>
                            <tr class="show1" id="row_<?php echo $row->kode;?>">
                                <td>
                                    <input type="checkbox" class="checkboxes" value="<?php echo $row->kode;?>"
                                        name="<?php echo "notrans".$no;?>" id="<?php echo "notrans".$no;?>" />
                                </td>
                                <td><?php echo $row->kode;?></td>
                                <td><?php echo $row->nama;?></td>
                            </tr>
                            <?php $no++;} ?>



                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    <input type="hidden" name="nomor" id="nomor" value="<?php echo $idgrup;?>" />
    <input type="hidden" id="jumdata" name="jumdata" value="<?php echo $no-1; ?>" />

    </form>
    <div class="col-md-12">
        <label><b>Note : </b></label></br>
        <ul>
            <li><i>level 1 untuk tidak bisa cetak dan tidak bisa liat harga</i></li>
            <li><i>level 2 untuk bisa cetak dan bisa liat harga</i></li>
        </ul>
    </div>

</div>
</div>
</div>


</div>
</div>

<!-- JAVASCRIPT -->
    <!-- <script src="<?= BASEURL ?>public/assets/js/jquery-3.5.1.js"></script> -->
    <script src="<?= BASEURL ?>public/assets/datatable/jquery.dataTables.min.js"></script>

    <script src="<?= BASEURL ?>public/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/node-waves/waves.min.js"></script>
    <!-- <script src="cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->

    <!-- sweets alert JS-->
    <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.js"></script>

    <!--Select2 Js -->
    <script src="<?= BASEURL ?>public/assets/libs/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="<?= BASEURL ?>public/assets/js/pages/ecommerce-select2.init.js" type="text/javascript"></script>
<script>

$(document).ready(function() {
    $('#t2').DataTable({});
    $('#t3').DataTable({});
});

$(document).ready(function() {
    $('#frmmenu').on('submit', function(e) {
        $.ajax({
            url: "<?php echo base_url('utilitas/gruppemakai/savefrmmenu');?>",
            data: $(this).serialize(),
            type: 'POST',
            success: function(data) {
                swal({
                    title: "UTILITAS GROUP PEMAKAI",
                    html: "<p>Data Berhasil Ditambahkan. </p>",
                    confirmButtonText: "OK"
                }).then((value) => {
                    location.reload();
                });
            },
            error: function(data) {
                $("#error").show().fadeOut(5000);
            }
        });
        e.preventDefault();
    });


});

$(document).ready(function() {
    $('#frmgrup').on('submit', function(e) {

        $.ajax({
            url: "<?php echo base_url('utilitas/gruppemakai/savefrmgrup');?>",
            data: $(this).serialize(),
            type: 'POST',
            success: function(data) {
                swal({
                    title: "UTILITAS GROUP PEMAKAI",
                    html: "<p>Data Berhasil Disimpan. </p>",
                    confirmButtonText: "OK"
                }).then((value) => {
                    location.reload();
                });
            },
            error: function(data) {
                $("#error").show().fadeOut(5000);
            }
        });
        e.preventDefault();

    });


});

function delData(data){
    $.ajax({
        url: "<?php echo base_url('utilitas/gruppemakai/deldatagrup/');?>"+data,
        data: $('#frmgrup').serialize(),
        type: 'POST',
        success: function(data) {
            swal({
                    title: "UTILITAS GROUP PEMAKAI",
                    html: "<p>Data Berhasil Hapus. </p>",
                    confirmButtonText: "OK"
                }).then((value) => {
                    location.reload();
                });
        },
        error: function(data) {
            $("#error").show().fadeOut(5000);
        }
    });
}

</script>