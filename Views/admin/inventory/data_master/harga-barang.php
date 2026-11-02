<?= main_header('Inventory', 'Data Harga Barang per Customer', 'Inventory', 'Harga Barang') ?>
<?= main_periode('Daftar Harga Barang per Customer', 0) ?>
<?= main_button($tambah, 1, 0, 'inventory/datamaster/hargabarang/entri', '', 2) ?>
<div class="row">
    <div class="col-md-9">
    </div>
    <!-- <div class="col-md-3">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search ..." aria-label="search Barang" id="search">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                </div>
            </div>
        </div>
    </div> -->
</div>
<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center;width:5%">Kodeitem</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Nama Customer</th>
                            <th style="text-align: center">Bruto</th>
                            <th style="text-align: center">Disc</th>
                            <th style="text-align: center">Harga Jual Net</th>
                            <th style="text-align: center">Partisi 1</th>
                            <th style="text-align: center">Partisi 2</th>
                            <th style="text-align: center">Partisi 3</th>
                            <th style="text-align: center">Margin</th>
                            <th style="text-align: center">Edit Harga Pecust</th>
                            <th style="text-align: center;width:10%;">&nbsp</th>
                            <th style="text-align: center;width:10%;">&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var table;
    var base_url = '<?= base_url('inventory/datamaster/hargabarangtable'); ?>';
    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverside": true,
            "searching": false,
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
        });
        // Inisialisasi fitur pencarian responsif
        $('#search').on('keyup', function() {
            GetFilterBarangCabang();
        });
    });

    function data_edit(id) {
        $('#tambahDataModal').modal('show');
        $.ajax({
            type: "post",
            url: "<?= base_url('inventory/datamaster/hargabarangedit/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.id);
                $('input[name=nama]').val(res.nama);
                $('input[name=kode]').val(res.id);
            }
        });
    }

    function data_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/datamaster/hargabarangdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    table_reload('table');
                }
            });
        }
    }

    function GetFilterBarangCabang() {
        if ($('#cabang').val() != undefined) {
            var cabang = $('#cabang').val();
        }
        if ($('#search').val() != '') {
            var cari = $('#search').val();
        }
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "destroy": true,
            "select": true,

            "aLengthMenu": [
                [10, 20, -1],
                [10, 20, "Semua"] // change per page values here
            ],
            "ajax": {
                "url": base_url,
                "type": "POST",
                "async": true,
                "data": {
                    "cabang": cabang
                },
            },

        });
    }
</script>