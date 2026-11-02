<?= main_header('Inventory', 'Export Data Harga Percust') ?>
<?= main_periode('Laporan Export Data Harga Percustomer', 1) ?>
<?= main_button('', 0, 0, '', '', 2) ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="export_harga_percust" class="display table table-bordered nowrap w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">Kode Item</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Nama Customer</th>
                            <th style="text-align: center">Bruto</th>
                            <th style="text-align: center">Disc</th>
                            <th style="text-align: center">Harga Jual Net</th>
                            <th style="text-align: center">Partisipasi 1</th>
                            <th style="text-align: center">Partisipasi 2</th>
                            <th style="text-align: center">Partisipasi 3</th>
                            <th style="text-align: center">Margin</th>
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
    var base_url = '<?= base_url('inventory/exportdata/export_harga_percust_table'); ?>';
    $(document).ready(function() {
        table = $('#export_harga_percust').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverside": true,
            "searching": false,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true
                },
                {
                    extend: 'csvHtml5',
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    footer: true
                }
            ],
        });
    });

    function GetFilterBarangCabang() {
        if ($('#cabang').val() != undefined) {
            var cabang = $('#cabang').val();
        }
        table = $('#export_harga_percust').DataTable({
            "processing": true,
            "serverSide": true,
            "destroy": true,
            "select": true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true
                },
                {
                    extend: 'csvHtml5',
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    footer: true
                }
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