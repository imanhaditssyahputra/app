<?= main_header('Inventory', 'Data Barang', 'Inventory', 'Barang') ?>
<?= main_periode('Daftar Barang', 0) ?>
<?= main_button($tambah, 1, 0, 'inventory/datamaster/barang/entri') ?>
<div class="row">
    <div class="col-md-9">
        <button class="btn btn-primary fw-bold me-2 pt-2" onclick="validasiall()"><i class="fa-solid fa-refresh fw-bold"></i> Validasi ALL</button>
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

<!-- DataTable Barang-->
<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">

                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            
                            <th style="text-align: center;">Gambar</th>
                            <th style="text-align: center;width:5%">Kode</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Stock</th>
                            <th style="text-align: center">Stock Min</th>
                            <th style="text-align: center">Stock Max</th>
                            <th style="text-align: center">NIE</th>
                            <th style="text-align: center">Tgl Exp NIE</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Batch</th>
                            <th style="text-align: center">Validasi</th>
                            <th style="text-align: center;width:10%;">&nbsp</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

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
                                <th style="width:10%;">Region</th>
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

<!-- Image preview modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
    <div class="modal-content">
      <div class="modal-body p-0">
        <button type="button" class="close p-2" data-bs-dismiss="modal" aria-label="Close" style="position:absolute; right:10px; z-index:2;">
          &times;
        </button>
        <img id="preview-image-src" src="" alt="Preview" style="width:100%; height:auto; display:block; object-fit:contain; background:#000;">
      </div>
    </div>
  </div>
</div>

<script>
    var cabang = "<?= $cbg ?>";
    var table;
    var base_url = '<?= base_url('inventory/datamaster/barangtable'); ?>';


    $(document).ready(function() {
        table = $('#table').DataTable({
            "searching": false,
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
            "processing": true,
            "serverSide": true,
            "order": [],
            "destroy": true,
            "select": true,

            "aLengthMenu": [
                [15, 20, -1],
                [15, 20, "Semua"] // change per page values here
            ],
            "ajax": {
                "url": base_url,
                "type": "POST",
                "async": true,
                "data": {
                    "gudang": gudang
                },
            },

        });
    }

    function data_edit(id) {
        var base_urlHome = "<?php echo base_url() ?>";
        return base_urlHome + 'inventory/datamaster/barang/entri/' + id;

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
                    html: data,
                    confirmButtonText: "OK"
                }).then(function(value) {
                    window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                });
            },
            error: function(data) {
                $('#process').css('display', 'none');
                swal({
                    title: "MASTER BARANG",
                    html: data,
                    confirmButtonText: "OK"
                }).then(function(value) {
                    window.location.href = "<?= base_url('inventory/datamaster/barang') ?>";
                });

            }
        });
    }
</script>

<!-- Script to open preview -->
<script>
  // pastikan jQuery sudah ada di halaman (project ini pakai jQuery)
  $(document).on('click', '.preview-image', function(e){
    e.preventDefault();
    var src = $(this).data('src') || $(this).find('img').attr('src');
    $('#preview-image-src').attr('src', src);
    // bootstrap 4/5 modal show
    $('#imagePreviewModal').modal('show');
  });

  // optional: clear src saat modal hide agar tidak tetap dimemory
  $('#imagePreviewModal').on('hidden.bs.modal', function () {
    $('#preview-image-src').attr('src','');
  });
</script>