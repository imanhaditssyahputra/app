<?= main_header('Inventory', 'Approval Mutasi Barang Antar Cabang') ?>
<?= main_periode('Daftal Approval Mutasi Barang Antar Cabang', 1) ?>
<?= main_button('Daftar Approval Mutasi Cabang', 0, 1, 'inventory/transaksi/persetujuanmutasicabang/entri') ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No. Approval</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Pengirim Cabang</th>
                            <th style="text-align: center">Peneriman Cabang</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th style="text-align: center">Aksi</th>
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
    $(document).ready(function() {
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#example').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('inventory/transaksi/persetujuanmutasicabangtable/') ?>"+tgl1+"/"+tgl2,
                        "type": "GET"
                    },
                    "aLengthMenu": [
                        [5, 15, 20, -1],
                        [5, 15, 20, "Semua"] // change per page values here
                    ],
                });
    });
    function filterdata()
    {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('inventory/transaksi/persetujuanmutasicabangtable/') ?>" +tgl1+"/"+tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/transaksi/delpersetujuanmutasicabang/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PERSETUJUAN MUTASI CABANG', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }
</script>