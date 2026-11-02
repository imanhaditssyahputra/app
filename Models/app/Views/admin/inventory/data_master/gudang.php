<?= main_header('Inventory', 'Data Gudang', 'Inventory', 'Gudang') ?>
<?= main_periode('Daftar Gudang', 0) ?>
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
                            <th style="text-align: center">Cabang</th>
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
            <div class="modal-body">
                <form id="submit_form" class="form-horizontal">
                    <input type="hidden" value="0" name="id" id="id" />
                    <div class="form-body">
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Kode </label>
                                <div class="col-md-9">
                                    <input name="kode" placeholder="Kode" class="form-control input-small" maxlength="6" type="text" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Nama</label>
                                <div class="col-md-9">
                                    <input name="nama" placeholder="Nama kategori" class="form-control" maxlength="30" type="text">
                                    <span id="nameErr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <label class="control-label pt-2 col-md-3">Cabang</label>
                                <div class="col-md-9">
                                    <select class="form-control input-medium select_cabang" id="cabang" name="cabang">
                                        <option>---Pilih Cabang</option>
                                        <?php
                                        $db = db_connect();
                                        $cabang = $db->table('ms_unit')->get()->getResult();
                                        foreach ($cabang as $key => $value) {
                                            echo '<option value="' . $value->kode . '">' . $value->nama . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="cabangErr"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('inventory/datamaster/gudang_data_get') ?>'
        });
    });

    $('.add-btn').click(function(e) {
        e.preventDefault();
        $('#submit_form').trigger('reset');
        $('input[name=id]').val(0);
    });

    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=kode]').val();
        if (id == "") {
            $.ajax({
                url: "<?= base_url('inventory/datamaster/gudang_data') ?>",
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal('MASTER GUDANG', 'Data Berhasil Di Tambah', '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    } else {
                        $('.namaErr').html(res.namaErr);
                        $('.cabangErr').html(res.cabangErr);
                    }
                }
            });
        }else{
            $.ajax({
                url: "<?= base_url('inventory/datamaster/gudangdataupdate/') ?>"+ id,
                type: "post",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal('MASTER GUDANG', 'Data Berhasil Di Update', '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    } else {
                        $('.namaErr').html(res.namaErr);
                        $('.cabangErr').html(res.cabangErr);
                    }
                }
            });
        }
    });

    function data_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('inventory/datamaster/gudangdataedit/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=nama]').val(res.nama);
                $('input[name=kode]').val(res.kode);
                $('select[name=cabang]').val(res.cabang);
            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/gudangdatadelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER GUDANG', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>