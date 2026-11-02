<div class="container-fluid">
    <?= main_header('Utilitas', 'Data Master Nomor Bukti', 'Utilitas', 'Nomor Bukti') ?>
    <?= main_periode('Daftar Kode Nomor Bukti', 0) ?>




    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active fs-6 fw-bold" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">No urut</button>
            <button class="nav-link fs-6 fw-bold" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">No Faktur Pajak</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="my-3">
                <button class=" btn btn-default refresh-btn fw-bold" onclick="reload_table()"><i class="fa-solid fa-refresh fw-bold"></i> Refresh</button>
            </div>

            <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center">Kode</th>
                        <th style="text-align: center">Nomor Urut</th>
                        <th style="text-align: center">Keterangan</th>
                        <th style="text-align: center;width:16%;">&nbsp</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="my-3">
                <button class="btn btn-success refresh-btn fw-bold" onclick="reload_table()"><i class="fa-solid fa-refresh fw-bold"></i> Refresh</button>
            </div>

            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: center">Kode</th>
                        <th style="text-align: center">Nomor Date</th>
                        <th style="text-align: center">Nomor Urut</th>
                        <th style="text-align: center">Keterangan</th>

                        <th style="text-align: center;width:16%;">&nbsp</th>

                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    var save_method; //for save method string
    var table1;
    var table2;

    $(document).ready(function() {
        table1 =$('#table').DataTable({
            "processing": true, //Feature control the processing indicator.
            ajax: '<?= base_url('utilitas/nomorbuktitable') ?>'
        });
        table2 = $('#datatable').DataTable({
            "processing": true, //Feature control the processing indicator.
            ajax: '<?= base_url('utilitas/nomorbuktitablepajak') ?>'
        });
    });

    function reload_table()
    {
        table1.ajax.reload(null,false); //reload datatable ajax 
        table2.ajax.reload(null,false); //reload datatable ajax 
    }

    function data_edit(id)
    {
        save_method = 'update_nobukti';
        $('#form')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('utilitas/nomorbukti/datanobukti/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="kode"]').val(data.kdtr);
                $('[name="nomor"]').val(data.cdno);
                $('[name="ket"]').val(data.ket)
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Data'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal('UTILITAS NOMOR BUKTI', 'Error Saat Get Data', '');
            }
        });
    }

    function data_editfp(id)
    {
        save_method = 'update_nobuktifp';
        $('#form_pajak')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string

        //Ajax Load data from ajax
        $.ajax({
            url : "<?= base_url('utilitas/nomorbuktifp/datanobuktifp/') ?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="kode"]').val(data.kdtr);
                $('[name="no_date"]').val(data.no_date);
                $('[name="nomor"]').val(data.cdno);
                $('[name="ket"]').val(data.ket);
                $('#modal_form_fp').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit Data Faktur Pajak'); // Set title to Bootstrap modal title
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                swal('UTILITAS NOMOR BUKTI', 'Error Saat Get Data', '');
            }
        });
    }

    function save()
    {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 

        if (save_method == 'update_nobukti'){
            data = $('#form').serialize();
        }else{
            data = $('#form_pajak').serialize();
        }
        // ajax adding data to database
        $.ajax({
            url : "<?= base_url('utilitas/nomorbukti/save') ?>",
            type: "POST",
            data: data,
            dataType: "JSON",
            success: function(data)
            {

                if(data) //if success close modal and reload ajax table
                {
                    $('#modal_form').modal('hide');
                    reload_table();
                    $('#modal_form_fp').modal('hide');
                    reload_table();

                }
                else
                {
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('Simpan'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 


            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure you want to delete data ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/nomorbukti/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('UTILITAS NOMOR BUKTI', 'Data berhasil di hapus', '');
                    reload_table();
                }
            });
        }
    }
</script>

<!-- Bootstrap modal Nomor Bukti -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Data Nomor Bukti</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-close"></i></span></button>
            </div>
            <div class="modal-body form">
                <form id="form" class="form-horizontal">
                    <input type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode</label>
                                <div class="col-md-9">
                                    <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="5" type="text" require readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nomor Urut</label>
                                <div class="col-md-9">
                                    <input name="nomor" placeholder="nomor Urut" class="form-control" maxlength="100" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Keterangan</label>
                                <div class="col-md-9">
                                    <input name="ket" placeholder="Keterangan" class="form-control" maxlength="255" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Bootstrap modal Nomor Bukti -->
<div class="modal fade" id="modal_form_fp" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Data Nomor urut paja</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-close"></i></span></button>
            </div>
            <div class="modal-body form">
                <form id="form_pajak" class="form-horizontal">
                    <input type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode</label>
                                <div class="col-md-9">
                                    <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="5" type="text" require readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">No Date</label>
                                <div class="col-md-9">
                                    <input name="no_date" placeholder="No Date" class="form-control" maxlength="100" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nomor Urut</label>
                                <div class="col-md-9">
                                    <input name="nomor" placeholder="nomor Urut" class="form-control" maxlength="100" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Keterangan</label>
                                <div class="col-md-9">
                                    <input name="ket" placeholder="Keterangan" class="form-control" maxlength="255" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->