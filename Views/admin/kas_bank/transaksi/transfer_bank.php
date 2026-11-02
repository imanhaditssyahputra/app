<div class="container-fluid">
    <?= main_header('Keuangan', 'Daftar Transfer Kas/Bank', 'Keuangan', 'Data Transfer Kas/Bank') ?>
    <?= main_periode('Daftar Transfer Kas/Bank') ?>

    <?= main_button($tambah, 1, 1, 'kasbank/transaksi/transferbank/entri') ?>

    <div style="overflow-x: auto;">
        <div class="datatable-example">
            <table class="table table-striped table-hover table-bordered" id="table">
                <thead>
                    <tr>
                        <th style="text-align: center">Nomor</th>
                        <th style="text-align: center">Tanggal</th>
                        <th style="text-align: center">Bank (Keluar)</th>
                        <th style="text-align: center">Bank (Masuk)</th>
                        <th style="text-align: center">Keterangan</th>
                        <th style="text-align: center">Total</th>
                        <th>&nbsp</th>
                        <th>&nbsp</th>
                        <th>&nbsp</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>

                    <td colspan="5" style="text-align:right">Total:</td>
                    <td style="text-align:center"></td>
                    <td colspan="3"></td>


                </tfoot>

            </table>
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
                "url": "<?= base_url('kasbank/transaksi/transferbanktable/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"
            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][5] * 1;
                }


                var iTot = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][5];
                    var y = Number(x.replace(/[^0-9\.]+/g, ""));
                    iTot += y;
                }

                var nCells = nRow.getElementsByTagName('td');
                nCells[1].innerHTML = number_format(iTot);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('kasbank/transaksi/transferbanktable/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('kasbank/transaksi/transferbank/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('KAS & BANK', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>