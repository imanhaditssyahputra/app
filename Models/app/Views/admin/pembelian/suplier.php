<style>
    .rightJustified {
        text-align: right;
    }

    .total {
        font-size: 14px;
        font-weight: bold;
        color: blue;
    }

    .bodycontainer {
        max-height: 150px;
        width: 100%;
        margin: 0;
        overflow-y: auto;
    }

    .table-scrollable {
        margin: 0;
        padding: 0;
    }

    .glyphicon.fast-right-spinner {
        -webkit-animation: glyphicon-spin-r 1s infinite linear;
        animation: glyphicon-spin-r 1s infinite linear;
    }

    @-webkit-keyframes glyphicon-spin-r {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }
    }

    @keyframes glyphicon-spin-r {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }
    }
</style>
<?php
    main_header('Pembelian', 'Data Supplier');
    main_button($tambah, 1, 0, '', 'tambahDataModal', 0);
?>


<div class="row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    Daftar Supplier
                </div>

            </div>
            <div class="portlet-body">
                <!-- <div class="table-toolbar">
                    <div class="btn-group">

                    </div>
                    <button class="btn btn-success add-btn" onclick="add_data()"><i class="fa-solid fa-plus"></i> Supplier Baru</button>
                    <button class="btn btn-default refresh-btn" onclick="reload_table()"><i class="fa-solid fa-refresh"></i> Refresh</button>
                </div> -->
                <div style="overflow-x: auto;">
                    <div class="datatable-example">
                        <table id="table" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center;width:5%">Kode</th>
                                    <th style="text-align: center;width:20%">Nama</th>
                                    <th style="text-align: center;width:30%">Alamat</th>
                                    <th style="text-align: center;width:10%">Telpon</th>
                                    <th style="text-align: center;width:10%">Kontak</th>
                                    <th style="text-align: center;width:16%">&nbsp</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" style="height: auto;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="tambahDataModalLabel">Tambah Data</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x fs-3"></i>
                </button>
            </div>
            <form id="submit_form" class="form-horizontal">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id" />
                    <div class="form-body overflow-hidden container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Kode</label>
                                        <div class="col-md-6">
                                            <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="10" type="text" value="" readonly>
                                        </div>
                                        <span id="kodeErr"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Telpon</label>
                                        <div class="col-md-3">
                                            <input name="telp" placeholder="No. Telpon" class="form-control " maxlength="12" type="text">
                                            <span id="telpErr" class=""></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-3">NPWP</label>
                                        <div class="col-md-3">
                                            <input name="npwp" placeholder="No. NPWP" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Nama</label>
                                        <div class="col-md-9">
                                            <input name="nama" placeholder="Nama Supplier" class="form-control" type="text">
                                        </div>
                                        <span class="text-danger" id="namaErr"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Fax</label>
                                        <div class="col-md-9">
                                            <input name="fax" placeholder="Nomor Fax" class="form-control" maxlength="16" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Alamat 1</label>
                                        <div class="col-md-9">
                                            <input name="alamat1" placeholder="Alamat Supplier" class="form-control" type="text">
                                        </div>
                                        <span id="alamatErr"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Handphone</label>
                                        <div class="col-md-9">
                                            <input name="hp" placeholder="Nomor HP" class="form-control" maxlength="16" type="text">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Alamat 2</label>
                                        <div class="col-md-9">
                                            <input name="alamat2" placeholder="Alamat Supplier" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Contactname</label>
                                        <div class="col-md-9">
                                            <input name="contactname" placeholder="Nama Kontak Person" class="form-control" maxlength="40" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Kota</label>
                                        <div class="col-md-9">
                                            <input name="kota" placeholder="Nama Kota" class="form-control" type="text">
                                        </div>
                                        <span id="kotaErr"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Email</label>
                                        <div class="col-md-9">
                                            <input name="email" placeholder="Alamat email" class="form-control" maxlength="40" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Kode Pos</label>
                                        <div class="col-md-3">
                                            <input name="kodepos" placeholder="Kode Pos" class="form-control" type="text">
                                        </div>
                                        <label class="control-label pt-2 col-md-3">PKP</label>
                                        <div class="col-md-3">
                                            <select name="pkp" class="form-select">
                                                <option value="Y">Ya</option>
                                                <option value="T" selected="true">Tidak</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">T.O.P</label>
                                        <div class="col-md-9">
                                            <input name="top" placeholder="" class="form-control" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('pembelian/supliertable') ?>'
        });
    });


    $('.add-btn').click(function(e) {
        e.preventDefault();
        <?php $kode = $nourut;?>
        $('#tambahDataModal form').trigger('reset');
        $('input[name=kode]').val('<?= $kode ?>');
        $('input[name=id]').val(0);
    });


    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=id]').val();
        if (id == 0) {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/suplieradd') ?>",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.error) {
                        if (res.kodeErr) {
                            $('#kodeErr').html(res.kodeErr);
                        } else {
                            $('#kodeErr').html('');
                        }
                        if (res.namaErr) {
                            $('#namaErr').html(res.namaErr);
                        } else {
                            $('#namaErr').html('');
                        }
                        if (res.telpErr) {
                            $('#telpErr').html(res.telpErr);
                        } else {
                            $('#telpErr').html('');
                        }
                        if (res.kotaErr) {
                            $('#kotaErr').html(res.kotaErr);
                        } else {
                            $('#kotaErr').html('');
                        }
                        if (res.alamatErr) {
                            $('#alamatErr').html(res.alamatErr);
                        } else {
                            $('#alamatErr').html('');
                        }
                    } else {
                        swal({
                            title: "MASTER SUPPLIER",
                            html: "<p>Data Berhasil di tambah </p>",
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            location.reload();
                        });
                        table_reload('table');
                        $('#tambahDataModal').modal('hide');
                    }
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/suplierupdate/') ?>" + id,
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.error) {
                        if (res.kodeErr) {
                            $('#kodeErr').html(res.kodeErr);
                        } else {
                            $('#kodeErr').html('');
                        }
                        if (res.namaErr) {
                            $('#namaErr').html(res.namaErr);
                        } else {
                            $('#namaErr').html('');
                        }
                        if (res.telpErr) {
                            $('#telpErr').html(res.telpErr);
                        } else {
                            $('#telpErr').html('');
                        }
                        if (res.kotaErr) {
                            $('#kotaErr').html(res.kotaErr);
                        } else {
                            $('#kotaErr').html('');
                        }
                        if (res.alamatErr) {
                            $('#alamatErr').html(res.alamatErr);
                        } else {
                            $('#alamatErr').html('');
                        }
                    } else {
                        swal('SUPPLIER PEMBELIAN', 'Data berhasil di edit', '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    }
                }
            });
        }
    });

    function suplier_delete(id, name) {
        if (confirm('Are you sure you want to delete data ' + name + ' ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/suplierdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER SUPPLIER', 'Data berhasil di hapus', '');
                    table_reload('table');
                }
            });
        }
    }


    function suplier_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('pembelian/suplieredit/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.id);
                $('input[name=kode]').val(res.kode);
                $('input[name=telp]').val(res.telp);
                $('input[name=npwp]').val(res.npwp);
                $('input[name=nama]').val(res.nama);
                $('input[name=alamat1]').val(res.alamat1);
                $('input[name=alamat2]').val(res.alamat2);
                $('input[name=fax]').val(res.fax);
                $('input[name=hp]').val(res.hp);
                $('input[name=contactname]').val(res.contactname);
                $('input[name=kota]').val(res.kota);
                $('input[name=email]').val(res.email);
                $('input[name=kodepos]').val(res.kodepos);
                $('select[name=pkp]').val(res.pkp);
                $('input[name=top]').val(res.top);
            }
        });
    }
</script>