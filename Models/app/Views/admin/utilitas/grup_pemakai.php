<div class="containe-fluid">
    <?= main_header('Utilitas', 'Grup Pemakai') ?>
    <?= main_periode('Daftar Grup Pemakai Aplikasi', 0) ?>
    <?= main_button('Data Baru', 1, 1, '', 'tambahDataModal') ?>


    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center">Kode</th>
                <th style="text-align: center">Nama</th>
                <th style="text-align: center;width:25%;">&nbsp</th>

            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>


<!-- Bootstrap modal -->
<div class="modal fade" id="tambahDataModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Data Grup Pemakai</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-close"></i></span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode</label>
                                <div class="col-md-9">
                                    <input name="kode" placeholder="Auto" class="form-control input-small" maxlength="6" type="text" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nama Grup</label>
                                <div class="col-md-9">
                                    <input name="nama" placeholder="Nama Grup Pemakai" class="form-control" maxlength="30" type="text">
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


<div class="modal fade" id="menuapp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title2">Grup Pemakai</h4>

            </div>
            <div class="modal-body" id="menuappbody">

            </div>
            <div class="modal-footer">
                <button type="button" id="btntutup" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('utilitas/gruppemakaitable') ?>'
        });
    });

    function save(){
        var id = $('input[name=id]').val();
        if($('input[name=nama]').val() == ""){
            swal('UTILITAS GROUP PEMAKAI', 'Data Belum Lengkap...', '');
        }else{
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/gruppemakai/save') ?>",
                data: $('#form').serialize(),
                dataType: "json",
                success: function(res) {
                        swal({
                            title: "UTILITAS GROUP PEMAKAI",
                            html: "<p>Data "+res+" Berhasil di tambah </p>",
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            location.reload();
                        });
                        table_reload('table');
                        $('#tambahDataModal').modal('hide');
                },
                error: function(res) {
                    swal('UTILITAS GROUP PEMAKAI', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function data_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "get",
            url: "<?= base_url('utilitas/gruppemakai/viewmodulgroup/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.nomor);
                $('input[name=kode]').val(res.nomor);
                $('input[name=nama]').val(res.nmgrup);
            }
        });
    }

    function edit_menu(id)
    {
	   $('.modal-title2').text('MASTER');
	   $("#menuappbody").html('<iframe src="<?php echo base_url('utilitas/gruppemakai/menu/');?>'+id+'" frameborder="no" width="100%" height="900"></iframe>');
	   $('#menuapp').modal('show');
    }

    function data_delete(id){
        if (confirm('Are you sure you want to delete data ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/gruppemakai/deldata/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER CABANG', 'Data berhasil di hapus', '');
                    table_reload('table');
                }
            });
        }
    }
</script>