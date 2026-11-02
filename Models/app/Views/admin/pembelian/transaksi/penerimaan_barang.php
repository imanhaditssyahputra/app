<?php
    main_header('Pembelian', 'Penerimaan Barang', '', '');
    main_periode('Daftar Penerimaan Barang', 1);
    main_button($tambah, 0, 1, 'pembelian/transaksi/penerimaanbarang/entri');
?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor #</th>
                            <th>No. Terima</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Keterangan</th>
                            <th>Status</th>
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
                        "url": "<?= base_url('pembelian/transaksi/penerimaanbarangtable/') ?>"+tgl1+"/"+tgl2,
                        "type": "GET"
                    },
                    "aLengthMenu": [
                        [10, 15, 20, -1],
                        [10, 15, 20, "Semua"] // change per page values here
                    ],
                });
    });
    function filterdata()
    {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('pembelian/transaksi/penerimaanbarangtable/') ?>" +tgl1+"/"+tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/transaksi/penerimaanbarangdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PRA PESANAN PEMBELIAN', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }
</script>