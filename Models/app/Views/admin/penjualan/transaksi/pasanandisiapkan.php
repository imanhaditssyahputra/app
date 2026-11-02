<?= main_header('Penjualan', 'Pengiriman Pesanan', 'Penjualan', 'Data Pengiriman Pesanan') ?>
<?= main_periode('Daftar Pengiriman Pesanan', 0) ?>
<?= main_button($tambah, 1, 0, 'penjualan/transaksi/pesanandisiapkan/entri', '', '') ?>

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
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No</th>
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th>&nbsp</th>
                            <th>&nbsp</th>
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
                "url": "<?= base_url('penjualan/transaksi/pesanandisiapkantable/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"

            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ]
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/transaksi/pesanandisiapkantable/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/delete/') ?>" + id,
                dataType: "json",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(res) {
                    $('#process').css('display', 'none');
                    swal('PESANAN DISIAPKAN', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }
</script>