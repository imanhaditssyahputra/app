<?= main_header('Penjualan', 'Export Data',) ?>
<?= main_periode('Export Faktur Pajak', 1) ?>
<?= main_button('', 0, 1,) ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="faktur_pajak" class="display table table-bordered nowrap w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No</th>
                            <th style="text-align: center">Faktur Pajak</th>
                            <th style="text-align: center">No Nota</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">NO NPWP</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Nama Yayasan</th>
                            <th style="text-align: center">Alamat NPWP</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">Qty</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Batch</th>
                            <th style="text-align: center">ED</th>
                            <th style="text-align: center">Disc %</th>
                            <th style="text-align: center">Harga Jual</th>
                            <th style="text-align: center">Disc Rp</th>
                            <th style="text-align: center">DPP</th>
                            <th style="text-align: center">ppn</th>
                            <th style="text-align: center">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <td style="text-align:right">Total:</td>
                            <td></td>
                            <th></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#faktur_pajak').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/exportdata/fakturpajaktable/') ?>" + tgl1 + "/" + tgl2,
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
            ],
            columnDefs: [{
                targets: [5, 6, 7, 8],
                className: 'dt-body-left',
            }],
            footerCallback: function(row, data, start, end, display) {
                let api = this.api();

                // Remove the formatting to get integer data for summation
                let intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                // Total over all pages
                qty = api
                    .column(9)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                hjual = api
                    .column(12)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                discrp = api
                    .column(13)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                dpp = api
                    .column(14)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                ppn = api
                    .column(15)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                total = api
                    .column(16)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(9).footer().innerHTML = number_format(qty);
                api.column(12).footer().innerHTML = number_format(hjual);
                api.column(13).footer().innerHTML = number_format(discrp);
                api.column(14).footer().innerHTML = number_format(dpp);
                api.column(15).footer().innerHTML = number_format(ppn);
                api.column(16).footer().innerHTML = number_format(total);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/exportdata/fakturpajaktable/') ?>" + tgl1 + "/" + tgl2).load();
    }
</script>