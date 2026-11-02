<div class="container-fluid">
    <?= main_header('Keuangan', 'Daftar Transfer Kas/Bank', 'Keuangan', 'Data Transfer Kas/Bank') ?>
    <?= main_periode('Daftar Transfer Kas/Bank') ?>

    <?= main_button('Data Baru', 0, 1, 'kasbank/transaksi/transfer_bank/entri') ?>

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
                <td style="text-align:center">86,446</td>
                <td colspan="3"></td>


            </tfoot>

        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#table').DataTable({
            ajax: '<?= base_url('kasbank/transaksi/transfer-table') ?>',
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }]
        });
    });




    function bank_delete(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('kasbank/transaksi/bank-delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    table_reload('table');
                }
            });
        }
    }
</script>