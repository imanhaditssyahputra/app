<?= main_header('Inventory', 'Data Barang', 'Inventory', 'Barang') ?>
<?= main_periode('Daftar Barang', 0) ?>
<?= main_button($tambah, 1, 0, '', 'modal_form', 1) ?>
<div class="row">
    <div class="col-md-9">
        <button class="btn btn-primary fw-bold me-2 pt-2" onclick="validasiall()"><i class="fa-solid fa-refresh fw-bold"></i> Validasi ALL</button>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search ..." aria-label="search Barang" id="search">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group" id="process" style="display: none;">
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">

                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center;width:5%">Kode</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Stock</th>
                            <th style="text-align: center">NIE</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Batch</th>
                            <th style="text-align: center">Validasi</th>
                            <th style="text-align: center;width:16%;">&nbsp</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Data</h3>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-close"></i></button>
            </div>
            <div class="modal-body">
                <form id="submit_form" class="form-horizontal">
                    <input type="hidden" value="0" id="id">
                    <div class="form-body">
                        <!--/BARIS KE 1-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Kode Item *</label>
                                        <div class="col-md-9">
                                            <input type="text" name="kodeitem" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Nama Barang</label>
                                        <div class="col-md-9">
                                            <input type="text" name="namebarang" class="form-control">
                                            <span id="namebarang"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/BARIS KE 2-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Kategori</label>
                                        <div class="col-md-9">
                                            <select name="kategori" class="form-select">
                                                <option value="0">--Pilih--</option>
                                                <?php
                                                $db = db_connect();
                                                $kategori = $db->table('inv_kategori')->get()->getResult();
                                                foreach ($kategori as $key) {
                                                    echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="kategori"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Satuan</label>
                                        <div class="col-md-9">
                                            <select name="satuan" class="form-select">
                                                <option value="0">--Pilih--</option>
                                                <?php
                                                $db = db_connect();
                                                $satuan = $db->table('inv_satuan')->get()->getResult();
                                                foreach ($satuan as $key) {
                                                    echo '<option value="' . $key->nama . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="satuan"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/BARIS KE 3-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Harga Jual</label>
                                        <div class="col-md-3">
                                            <input type="text" name="hargajual" class="form-control" value="0">
                                            <span id="hargajual"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-3">Harga Beli</label>
                                        <div class="col-md-3">
                                            <input type="text" name="hargabeli" class="form-control" readonly value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Stock</label>
                                        <div class="col-md-3">
                                            <input type="text" name="stok" class="form-control" value="0" readonly>
                                            <span id="stok"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-2">NIE</label>
                                        <div class="col-md-4">
                                            <input type="text" name="nie" class="form-control" value="" required>
                                            <span id="nie"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/BARIS KE 4-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Gudang</label>
                                        <div class="col-md-3">
                                            <select name="gudang" class="form-select">
                                                <option value="">--Pilih--</option>
                                                <?php
                                                $db = db_connect();
                                                $gudang = $db->table('inv_gudang')->get()->getResult();
                                                foreach ($gudang as $key) {
                                                    echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="gudang"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-3">Rak</label>
                                        <div class="col-md-3">
                                            <select name="rak" class="form-select">
                                                <option value="0">--Pilih--</option>
                                                <?php
                                                $db = db_connect();
                                                $rak = $db->table('inv_rak')->get()->getResult();
                                                foreach ($rak as $key) {
                                                    echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="rak"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Cabang</label>
                                        <div class="col-md-3">
                                            <select name="cabang" class="form-select">
                                                <option value="0">--Pilih Cabang--</option>
                                                <?php
                                                $db = db_connect();
                                                $cabang = $db->table('ms_unit')->get()->getResult();
                                                foreach ($cabang as $key) {
                                                    echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="cabang"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-2">Tgl Exp NIE</label>
                                        <div class="col-md-4">
                                            <input type="date" name="tglexpnie" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/BARIS KE 5-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">PPN</label>
                                        <div class="col-md-3">
                                            <select name="ppn" class="form-select">
                                                <option value="">--Pilih--</option>
                                                <option value="Y">YA</option>
                                                <option value="T">TIDAK</option>
                                            </select>
                                            <span id="ppn"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-3">Merk</label>
                                        <div class="col-md-3">
                                            <select name="merk" class="form-select">
                                                <option value="0">--Pilih--</option>
                                                <?php
                                                $db = db_connect();
                                                $merk = $db->table('inv_merk')->get()->getResult();
                                                foreach ($merk as $key) {
                                                    echo '<option value="' . $key->kode . '">' . $key->nama . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <span id="merk"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="row">
                                        <label class="control-label pt-2 col-md-3">Stock Min</label>
                                        <div class="col-md-3">
                                            <input type="text" name="stokmin" class="form-control" value="0">
                                            <span id="stokmin"></span>
                                        </div>
                                        <label class="control-label pt-2 col-md-3">Stock Max</label>
                                        <div class="col-md-3">
                                            <input type="text" name="stokmax" class="form-control" value="0">
                                            <span id="stokmin"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end bg-white">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal Nobatch -->
<div class="modal fade" id="lupbatch" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihharga">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory Nobatch/ED</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                                <th style="width:20%;">Kode item</th>
                                <th style="width:20%;">Nama Barang</th>
                                <th style="width:15%;">Nobatch</th>
                                <th style="width:15%;">Tanggal exp</th>
                                <th style="width:10%;">stok Batch</th>
                                <th style="width:10%;">Cabang</th>
                            </thead>
                            <tbody id="daftarnobatch"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" onclick="reloadBatch()" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
                </div>
        </div>
    </div>
</div>
<script>
    var cabang = "<?= $cbg ?>";
    var table;
    var base_url = '<?= base_url('inventory/datamaster/barangtable/'); ?>';


    $(document).ready(function() {
        table = $('#table').DataTable({
            "searching": false,
        });
        // Inisialisasi fitur pencarian responsif
        $('#search').on('keyup', function() {
            GetFilterBarangGudang();
        });
    });

    function GetFilterBarangGudang() {

        if ($('#gudang').val() != undefined) {
            var gudang = $('#gudang').val();
        }
        if ($('#search').val() != '') {
            var cari = $('#search').val();
        }
        get_datatables(gudang, cari)
    }

    function get_datatables(gudang, cari) {
        table = $('#table').DataTable({
            "searching": false,
            "processing": true, //Feature control the processing indicator.
            "serverside": true,

            "order": [],
            "destroy": true,
            "select": true,

            "aLengthMenu": [
                [15, 20, -1],
                [15, 20, "Semua"] // change per page values here
            ],
            "ajax": {
                "url": base_url + gudang + "/" + cari,
                "type": "POST",
            }
        });
    }

    $('.add-btn').click(function(e) {
        e.preventDefault();
        $('#submit_form').trigger('reset');
        $('input[name=kodeitem]').val('<?= $nourut ?>');
    });

    // Data Barang Add & Update
    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('#id').val();
        if (id == 0) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/barangadd') ?>",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal({
                            title: "MASTER BARANG",
                            html: res.message,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                        });
                        $('#modal_form').modal('hide');
                    } else {
                        // $('#kategori').html(res.kategori);
                        $('#namebarang').html(res.namebarang);
                        $('#gudang').html(res.gudang);
                        $('#nie').html(res.nie);
                        // $('#rak').html(res.rak);
                        $('#ppn').html(res.ppn);
                        // $('#merk').html(res.merk);
                        $('#satuan').html(res.satuan);
                        $('#cabang').html(res.cabang);
                    }
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/barangupdate/') ?>" + id,
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (!res.error) {
                        swal({
                            title: "MASTER BARANG",
                            html: "<p>Data Berhasil di Update </p>",
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                        });
                        $('#modal_form').modal('hide');
                    } else {
                        // $('#kategori').html(res.kategori);
                        $('#namebarang').html(res.namebarang);
                        $('#gudang').html(res.gudang);
                        // $('#rak').html(res.rak);
                        $('#ppn').html(res.ppn);
                        // $('#merk').html(res.merk);
                        $('#satuan').html(res.satuan);
                        $('#cabang').html(res.cabang);
                    }
                }
            });
        }
    });

    function data_edit(id) {
        $('#modal_form').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('inventory/datamaster/barangedit/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('#id').val(res.id);
                $('select[name=kategori]').val(res.kdkategori);
                $('input[name=namebarang]').val(res.namabarang);
                $('input[name=hargajual]').val(res.hargajual);
                $('input[name=hargabeli]').val(res.hargabeli);
                $('input[name=nie]').val(res.nie);
                $('input[name=tglexpnie]').val(res.tglexpnie);
                $('select[name=gudang]').val(res.kdgudang);
                $('select[name=rak]').val(res.kdrak);
                $('select[name=ppn]').val(res.ppn);
                $('select[name=merk]').val(res.kdmerk);
                $('input[name=kodeitem]').val(res.kodeitem);
                $('select[name=satuan]').val(res.satuan);
                $('input[name=stok]').val(res.stok);
                $('input[name=stokmin]').val(res.stok_min);
                $('input[name=stokmax]').val(res.stok_max);
                $('select[name=cabang]').val(res.kdcbg);
                $('input[name=kode]').val(res.id);
            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/barangdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('MASTER BARANG', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }

    function searchBatch(kode, cabang, gudang) {
        var xhttp;
        if (kode == "" && gudang == "") {
            document.getElementById("daftarnobatch").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupbatch').modal('show');
                document.getElementById("daftarnobatch").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('inventory/datamaster/barangtablebatch/') ?>" + kode + "/" + cabang + "/" + gudang, true);
        xhttp.send();
    }

    function reloadBatch() {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    function validasi(id) {
        $.ajax({
            type: "post",
            url: "<?= base_url('inventory/datamaster/validasiperbarang/') ?>" + id,
            dataType: "json",
            success: function(res) {
                if (res.DataOne != '') {
                    swal({
                        title: "MASTER BARANG",
                        html: "Data Berhasil Di Validasi",
                        confirmButtonText: "OK"
                    }).then(function(value) {
                        window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                    });
                }

            }
        });
    }

    function validasiall() {
        $.ajax({
            url: "<?= base_url('inventory/datamaster/validasiallbarang/') ?>" + cabang,
            type: "POST",
            dataType: "JSON",
            beforeSend: function() {
                $('#process').css('display', 'block');
            },
            success: function(data) {
                $('#process').css('display', 'none');
                swal({
                    title: "MASTER BARANG",
                    html: "Data Berhasil Di Validasi",
                    confirmButtonText: "OK"
                }).then(function(value) {
                    window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                });
            },
            error: function(data) {
                swal({
                    title: "MASTER BARANG",
                    html: "Data Berhasil Di Validasi",
                    confirmButtonText: "OK"
                }).then(function(value) {
                    window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                });
            }
        });
    }
</script>