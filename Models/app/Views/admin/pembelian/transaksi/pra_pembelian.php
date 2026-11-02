<?php 

    main_header('Pembelian', 'Pesanan Pra Pembelian');
    main_periode('Daftar Pesanan Pra Pembelian', 1);
    main_button($tambah, 0, 1, 'pembelian/transaksi/prapembelian/entri');
?>
<!-- <h1>Check Ping</h1>
<p>Ping Status: <span id="pingStatus">-</span></p> -->

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor #</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th></th>
                            <th></th>
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
        table = $('#table').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('pembelian/transaksi/prapembeliantable/') ?>"+tgl1+"/"+tgl2,
                        "type": "GET"
                    },
                    "aLengthMenu": [
                        [10, 15, 20, -1],
                        [10, 15, 20, "Semua"] // change per page values here
                    ],
                });
        // Panggil fungsi untuk memeriksa ping secara berkala dengan interval 5 detik
        // setInterval(checkPing, 5000);
        // checkPing(); // Pertama kali panggil untuk inisialisasi
    });

    function filterdata()
    {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('pembelian/transaksi/prapembeliantable/') ?>" +tgl1+"/"+tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/transaksi/prapembeliandelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PRA PESANAN PEMBELIAN', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>