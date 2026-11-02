<div class="containe-fluid">
    <?= main_header('Utilitas', 'Data Master Cabang', 'Utilitas', 'Cabang') ?>
    <?= main_periode('Daftar Cabang', 0) ?>
    <?= main_button('Data Baru', 1, 0, '', 'tambahDataModal',0) ?>


    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center">Kode</th>
                <th style="text-align: center">Nama Cabang</th>
                <th style="text-align: center">Alamat</th>
                <th style="text-align: center">Pimpinan</th>
                <th style="text-align: center">Telpon</th>
                <th style="text-align: center;width:16%;">&nbsp</th>

            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="tambahDataModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Data Cabang</h3>
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
                                    <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="5" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nama Cabang</label>
                                <div class="col-md-9">
                                    <input name="nama" placeholder="Nama Cabang" class="form-control" maxlength="100" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Alamat</label>
                                <div class="col-md-9">
                                    <input name="alamat" placeholder="Alamat Cabang" class="form-control" maxlength="255" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Pimpinan</label>
                                <div class="col-md-9">
                                    <input name="pimpinan" placeholder="Nama Pimpinan" class="form-control" maxlength="100" type="text" require>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Telpon</label>
                                <div class="col-md-9">
                                    <input name="telpon" placeholder="Nomor Telepon" class="form-control" maxlength="50" type="text" require>
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


<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('utilitas/kodecabangtable') ?>'
        });
    });

    function save(){
        var id = $('input[name=id]').val();
        if($('input[name=kode]').val() == "" || $('input[name=nama]').val() == "" || $('input[name=alamat]').val() == "" || $('input[name=pimpinan]').val() == ""){
            swal('MASTER CABANG', 'Data Belum Lengkap...', '');
        }else{
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/kodecabangsave') ?>",
                data: $('#form').serialize(),
                dataType: "json",
                success: function(res) {
                        swal({
                            title: "MASTER CABANG",
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
                    swal('MASTER CABANG', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function data_delete(id) {
        if (confirm('Are you sure you want to delete data ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/kodecabangdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER CABANG', 'Data berhasil di hapus', '');
                    table_reload('table');
                }
            });
        }
    }


    function data_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "get",
            url: "<?= base_url('utilitas/kodecabangview/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.kode);
                $('input[name=kode]').val(res.kode);
                $('input[name=nama]').val(res.nama);
                $('input[name=alamat]').val(res.alamat);
                $('input[name=pimpinan]').val(res.pimpinan);
                $('input[name=telpon]').val(res.telpon);
            }
        });
    }
</script>