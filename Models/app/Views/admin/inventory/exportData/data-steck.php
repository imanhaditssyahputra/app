<?= main_header('Inventory', 'Export Data') ?>
<?= main_periode('Laporan Export Stock', 1) ?>
<?= main_button('', 0, 0, '', '', 1) ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="export_stock" class="display table table-bordered nowrap w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">Kode Stock</th>
                            <th style="text-align: center">Nama Stock</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Stock awal</th>
                            <th style="text-align: center">Stock Akhir</th>
                            <th style="text-align: center">Stock Order</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Nilai Stock</th>
                            <th style="text-align: center">Stock Minimum</th>
                            <th style="text-align: center">Stock Maximum</th>
                            <th style="text-align: center">Merk</th>
                            <th style="text-align: center">Kategori</th>

                            <th style="text-align: center">PPN</th>
                            <th style="text-align: center">Gudang</th>
                            <th style="text-align: center">Tgl Beli</th>
                            <th style="text-align: center">Tgl Exp</th>
                            <th style="text-align: center">Tgl Jual</th>
                            <th style="text-align: center">Cabang</th>
                            <th style="text-align: center">Qty Terjual</th>
                            <th style="text-align: center">Laba Terjual</th>

                            <th style="text-align: center">Supplier 1</th>
                            <th style="text-align: center">Supplier 2</th>
                            <th style="text-align: center">Supplier 3</th>
                            <th style="text-align: center">Supplier 4</th>

                            <th style="text-align: center">bruto 1</th>
                            <th style="text-align: center">bruto 2</th>
                            <th style="text-align: center">bruto 3</th>
                            <th style="text-align: center">bruto 4</th>

                            <th style="text-align: center">disc 1</th>
                            <th style="text-align: center">disc 2</th>
                            <th style="text-align: center">disc 3</th>
                            <th style="text-align: center">disc 4</th>

                            <th style="text-align: center">Netto 1</th>
                            <th style="text-align: center">Netto 2</th>
                            <th style="text-align: center">Netto 3</th>
                            <th style="text-align: center">Netto 4</th>
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
        table = $('#export_stock').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverside": true, 
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', footer: true },
                { extend: 'excelHtml5', footer: true },
                { extend: 'csvHtml5', footer: true },
                { extend: 'pdfHtml5', footer: true }
            ],
        });
    });

    function GetFilterBarangGudang()
    {
        if ($('#gudang').val() != undefined) {
            var gudang = $('#gudang').val();
        }
        table.ajax.url("<?= base_url('inventory/exportdata/export_stock_table/') ?>" +gudang).load();
    }
</script>