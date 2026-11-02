<?= main_header('Penjualan', 'Uang Muka Penjualan', 'Penjualan', 'Data Uang Muka Penjualan') ?>
<?= main_periode('Data Uang Muka Penjualan') ?>
<?= main_button($tambah, 0, 1, 'penjualan/transaksi/uangmukapenjualan/entri') ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <!--th style="text-align: center">Register</th-->
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th style="text-align: center">Jatuh Tempo</th>
                            <th style="text-align: center">Total</th>
                            <th>&nbsp</th>
                            <th>&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>

                        <td colspan="6" style="text-align:right">Total:</td>
                        <td style="text-align:right"></td>
                        <td colspan="2"></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#example').DataTable({});
    });
</script>