<?= main_header('Inventory', 'Data Merk', 'Inventory', 'Merk') ?>
<?= main_periode('Daftar Merk', 0) ?>
<?= main_button($tambah, 1, 0, '', 'tambahDataModal',0) ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center;width:5%">Kode</th>
                            <th style="text-align: center">Nama</th>
                            <th style="text-align: center;width:16%;">&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap modal -->
<div class="modal fade" id="tambahDataModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Data</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-close"></i></button>
            </div>
            <form id="submit_form" class="form-horizontal">
                <div class="modal-body form">
                    <input type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Kode </label>
                                <div class="col-md-9">
                                    <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="6" type="text" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Nama</label>
                                <div class="col-md-9">
                                    <input name="nama" placeholder="Nama kategori" class="form-control" maxlength="30" type="text">
                                    <span class="help-block"></span>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('inventory/datamaster/merktable') ?>'
        });
    });

    $('.add-btn').click(function(e) {
        e.preventDefault();
        $('#submit_form').trigger('reset');
    });

    // Add & Update Data 
    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=kode]').val();
        if (id == "") {
            $.ajax({
                url: "<?= base_url('inventory/datamaster/merkadd') ?>",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal('MASTER MERK', 'Data Berhasil Tambah', '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    } else {
                        $('.namaErr').html(res.namaErr);
                    }
                }
            });
        }else{
            $.ajax({
                url: "<?= base_url('inventory/datamaster/merkupdate/') ?>"+ id,
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal('MASTER MERK', 'Data Berhasil Update', '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    } else {
                        $('.namaErr').html(res.namaErr);
                    }
                }
            });
        }
    });

    function data_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('inventory/datamaster/merkedit/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.id);
                $('input[name=nama]').val(res.nama);
                $('input[name=kode]').val(res.kode);
            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/merkdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER MERK', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>