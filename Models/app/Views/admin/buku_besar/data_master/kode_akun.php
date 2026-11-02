<div class="container-fluid">
    <?= main_header('Buku Besar', 'Akun Perkiraan') ?>
    <?= main_periode('Daftar Akun Perkiraan', 0) ?>
    <?= main_button('Data Baru', 1, 1, '', 'tambahDataModal') ?>

    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="text-align: center">Kode Perkiraan</th>
                <th style="text-align: center">Nama</th>
                <th style="text-align: center">Tipe Akun</th>
                <th style="text-align: center">Transaksi</th>
                <th style="text-align: center;width:16%;">&nbsp;</th>

            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="tambahDataModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content m-auto" style="width:72%;">
            <div class="modal-header">
                <h3 class="modal-title">Data Akun</h3>

                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa-solid fa-close"></i></span></button>
            </div>
            <form id="submit_form" class="form-horizontal">
                <div class="modal-body form">
                    <input id="tableId" type="hidden" value="0" name="id" />
                    <div class="form-body">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode Akun</label>
                                <div class="col-md-9 text-center">
                                    <input id="kode_akun" name="kode_akun" placeholder="Kode Akun" class="form-control" type="text">
                                    <span class="kodeErr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Nama Akun</label>
                                <div class="col-md-9 text-center">
                                    <input id="nama_akun" name="nama_akun" placeholder="Nama Akun" class="form-control" type="text">
                                    <span class="namaAkun"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Tipe Akun</label>
                                <div class="col-md-9 text-center">
                                    <select id="jenis" name="tipe_akun" class="form-control input-mediumx " data-placeholder="Pilih...">

                                        <option value="BANK">Kas & Bank</option>
                                        <option value="AREC">Piutang Usaha</option>
                                        <option value="INTR">Persediaan</option>
                                        <option value="OCAS">Aset Lancar Lainnya</option>
                                        <option value="FASS">Aset Tetap</option>
                                    </select>
                                    <span class="jenisErr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Akun Induk</label>
                                <div class="col-md-9 text-center">
                                    <select id="akuninduk" name="akun_induk" class="form-control input-mediumx " data-placeholder="Pilih...">
                                        <option value="1101">1101 - Kas & Bank</option>
                                        <option value="110101">110101 - Kas Kecil</option>
                                        <option value="110102">110102 - Bank</option>
                                        <option value="110103">110103 - Bank BJB</option>
                                    </select>
                                    <span class="indukErr"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode Penerimaan</label>
                                <div class="col-md-9 text-center">
                                    <input id="kode_panerimaan" name="kode_panerimaan" placeholder="Kode Bukti Penerimaan" class="form-control" type="text" maxlength="10">
                                    <span class="penerimaan"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Kode Pengeluaran</label>
                                <div class="col-md-9 text-center">
                                    <input id="kode_pengeluaran" name="kode_pengeluaran" placeholder="Kode Bukti Penerimaan" class="form-control" type="text" maxlength="10">
                                    <span class="pengeluaran"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="control-label col-md-3">Akun Transaksi</label>
                                <div class="col-md-9 text-center">
                                    <select id="akuntx" name="akun_transaksi" class="form-control input-mediumx " data-placeholder="Pilih...">
                                        <option value="Y">Ya</option>
                                        <option value="T">Tidak</option>
                                    </select>
                                    <span class="transiksi"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "ajax": {
                "url": "<?= base_url('bukubesar/datamaster/kodeakuntable') ?>",
            }
        });
        $('.add-btn').click(function() {
            $("#submit_form")[0].reset();
        })

        $("#submit_form").submit(function(e) {
            e.preventDefault();
            $id = $('#tableId').val();
            $.ajax({
                type: "post",
                url: "<?= base_url('bukubesar/datamaster/kodeakun-add') ?>",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.error) {
                        if (res.oneErr) {
                            $(".kodeErr").html(res.oneErr)
                        } else {
                            $(".kodeErr").html('')
                        }
                        if (res.twoErr) {
                            $(".namaAkun").html(res.twoErr)
                        } else {
                            $(".namaAkun").html('')
                        }
                        if (res.threeErr) {
                            $(".jenisErr").html(res.threeErr)
                        } else {
                            $(".jenisErr").html('')
                        }
                        if (res.fourErr) {
                            $(".indukErr").html(res.fourErr)
                        } else {
                            $(".indukErr").html('')
                        }
                        if (res.fiveErr) {
                            $(".penerimaan").html(res.fiveErr)
                        } else {
                            $(".penerimaan").html('')
                        }
                        if (res.sixErr) {
                            $(".pengeluaran").html(res.sixErr)
                        } else {
                            $(".pengeluaran").html('')
                        }
                        if (res.sevenErr) {
                            $(".transiksi").html(res.sevenErr)
                        } else {
                            $(".transiksi").html('')
                        }
                    } else {
                        table_reload('table');
                        $('#tambahDataModal').modal('hide');
                    }
                }
            })
        })
    })

    function kode_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('commonedit') ?>",
            dataType: "json",
            data: {
                id: id,
                table: 'tbl_kode_akun',
            },
            success: function(data) {
                $('#tableId').val(data.id);
                $('#kode_akun').val(data.kode_akun);
                $('#nama_akun').val(data.nama_akun);
                $('#jenis').val(data.tipe_akun);
                $('#akuninduk').val(data.akun_induk);
                $('#kode_panerimaan').val(data.kode_panerimaan);
                $('#kode_pengeluaran').val(data.kode_pengeluaran);
                $('#akuntx').val(data.akun_transaksi);
            }
        });
    }

    function kode_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('bukubesar/datamaster/akundelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    table_reload('table');
                }
            });
        }
    }
</script>