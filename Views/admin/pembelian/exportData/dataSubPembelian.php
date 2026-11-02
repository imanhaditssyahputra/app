<?= main_header('PEMBELIAN', 'Export Data',) ?>
<?= main_periode('Laporan Sub PEMBELIAN', 1) ?>
<?= main_button('', 0, 1,) ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="datatable-subpembelian" class="table table-bordered nowrap w-100">
                    <thead>
                        <tr>
                            <!--th style="text-align: center">Register</th-->
                            <th style="text-align: center">No Nota</th>
                            <th style="text-align: center">tgl Input</th>
                            <th style="text-align: center">tgl Faktur</th>
                            <th style="text-align: center">NO PO</th>
                            <th style="text-align: center">Kode Ereport Barang</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">qty</th>
                            <th style="text-align: center">Batch</th>
                            <th style="text-align: center">ED</th>
                            <th style="text-align: center">Harga Beli</th>
                            <th style="text-align: center">Disc %</th>
                            <th style="text-align: center">Sub jumlah</th>
                            <th style="text-align: center">Disc Rp</th>
                            <th style="text-align: center">Harga HPP</th>
                            <th style="text-align: center">PPN</th>
                            <th style="text-align: center">Jumlah</th>
                            <th style="text-align: center">tgl expired</th>
                            <th style="text-align: center">No Pajak</th>
                            <th style="text-align: center">Merk</th>
                            <th style="text-align: center">Kategori</th>
                            <th style="text-align: center">Kode Ereport Supplier</th>
                            <th style="text-align: center">Supplier</th>
                            <th style="text-align: center">Faktur supplier</th>
                            <th style="text-align: center">Status</th>

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
        table = $('#datatable-subpembelian').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('pembelian/exportdata/datasubpembeliantable/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"
            },
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
            ]
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('pembelian/exportdata/datasubpembeliantable/') ?>" + tgl1 + "/" + tgl2).load();
    }
</script>