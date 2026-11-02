<div class="containe-fluid">
    <?= main_header('Utilitas', 'Pengguna Aplikasi') ?>
    <?= main_periode('Daftar Pengguna Aplikasi', 0) ?>
    <?= main_button('Data Baru', 1, 1, '', 'tambahDataModal') ?>

    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center">User ID</th>
                <th style="text-align: center">Nama Lengkap</th>
                <th style="text-align: center">Region</th>
                <th style="text-align: center;width:16%;">&nbsp</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>


<!-- Bootstrap modal -->
<div class="modal fade" id="tambahDataModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Data Pengguna Aplikasi</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-close"></i></span></button>
            </div>
            <div class="modal-body form">
                <form id="form" class="form-horizontal">
                    <input type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">User ID</label>
                                <div class="col-md-9">
                                    <input name="uidlogin" placeholder="ID Pemakai" class="form-control input-medium" type="text">
                                    <span class="uidloginerr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Password</label>
                                <div class="col-md-9">
                                    <input name="password" placeholder="Password" type="password" class="form-control input-medium">
                                    <span class="passerr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nama Karyawan</label>
                                <div class="col-md-9">

                                    <input name="karyawan" placeholder="Nama Karyawan" class="form-control input-medium" type="text">
                                    <span class="karyawanerr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode Region</label>
                                <div class="col-md-9">
                                    <select name="unit" class="form-select">
                                        <option value="">--Pilih Kode--</option>
                                        <?php
                                        $db = db_connect();
                                        $cabang = $db->table('ms_unit')->get()->getResult();
                                        foreach ($cabang as $key) {
                                            echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="cabangerr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Grup Pengguna</label>
                                <div class="col-md-9">
                                    <select name="grup" class="form-select">
                                        <option value="">--Pilih Grup--</option>
                                        <?php
                                        $db = db_connect();
                                        $cabang = $db->table('ms_modul_grup')->get()->getResult();
                                        foreach ($cabang as $key) {
                                            echo '<option value="' . $key->nomor . '">' . $key->nmgrup . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <span class="gruperr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row align-items-center"> <!-- PERBAIKAN 1: Ditambahkan 'align-items-center' agar teks label dan kotak checkbox sejajar vertikal -->
                                <label class="control-label col-md-3">DEBUG</label>
                                <div class="col-md-9">
                                    <div class="form-check" style="padding-left: 0; min-height: auto;">
                                        <input name="debuguser" class="form-check-input" type="checkbox" id="debuguser" 
                                            style="width: 20px; height: 20px; margin: 0; float: none; display: inline-block; cursor: pointer;">
                                    </div>
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
            ajax: '<?= base_url('utilitas/penggunatable') ?>'
        });
    });

    function save() {
        $.ajax({
            url: "<?= base_url('utilitas/penggunasave') ?>",
            type: "post",
            data: $('#form').serialize(),
            dataType: "json",
            success: function(res) {
                if (!res.error) {
                    if (res.errorinsert) {
                        swal('MASTER PENGGUNA', res.message, '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    } else {
                        swal('MASTER PENGGUNA', res.message, '');
                        $('#tambahDataModal').modal('hide');
                        table_reload('table');
                    }

                } else {
                    $('.uidloginerr').html(res.uidErr);
                    $('.passerr').html(res.passErr);
                    $('.karyawanerr').html(res.karyawanErr);
                    $('.cabangerr').html(res.unitErr);
                    $('.gruperr').html(res.grupErr);
                }
            }
        });
    }

    function data_edit(id) {
        id = id;
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "get",
            url: "<?= base_url('utilitas/penggunaview/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.uidlogin);
                $('input[name=uidlogin]').val(res.uidlogin);
                $('input[name=karyawan]').val(res.username);
                $('select[name=unit]').val(res.cabang);
                $('select[name=grup]').val(res.level);
            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('utilitas/penggunadelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER PENGGUNA', 'Data Berhasil dihapus', '');
                    table_reload('table');
                }
            });
        }
    }
</script>