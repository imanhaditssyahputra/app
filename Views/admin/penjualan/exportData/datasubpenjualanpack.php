<?= main_header('PENJUALAN', 'Export Data Sub Penjualan Pack',) ?>
<?= main_periode('Laporan Sub Penjualan Pack', 1) ?>
<?= main_button('', 0, 1,) ?>



<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="sub_penjualan" class="display table table-bordered nowrap w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No Nota</th>
                            <th style="text-align: center">tgl Penjualan</th>
                            <th style="text-align: center">Kode Ereport Barang</th>
                            <th style="text-align: center">Nama Barang</th>
                            <th style="text-align: center">qty</th>
                            <th style="text-align: center">satuan</th>
                            <th style="text-align: center">Batch</th>
                            <th style="text-align: center">ED</th>
                            <th style="text-align: center">Harga jual</th>
                            <th style="text-align: center">Disc</th>
                            <th style="text-align: center">Sub jumlah</th>
                            <th style="text-align: center">Disc Rp</th>
                            <th style="text-align: center">Jumlah</th>
                            <th style="text-align: center">CN 1</th>
                            <th style="text-align: center">Bagian 1</th>
                            <th style="text-align: center">Total CN 1</th>
                            <th style="text-align: center">CN 2</th>
                            <th style="text-align: center">Bagian 2</th>
                            <th style="text-align: center">Total CN 2</th>
                            <th style="text-align: center">CN 3</th>
                            <th style="text-align: center">Bagian 3</th>
                            <th style="text-align: center">Total CN 3</th>
                            <th style="text-align: center">No Pajak</th>
                            <th style="text-align: center">Kode Ereport Customer</th>
                            <th style="text-align: center">Nama Customer</th>
                            <th style="text-align: center">Nama Yayasan/PT</th>
                            <th style="text-align: center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:right">Total:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
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
        table = $('#sub_penjualan').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/exportdata/subpenjualanpacktable/') ?>" + tgl1 + "/" + tgl2,
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
                    .column(4)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                hjual = api
                    .column(7)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                subjmlh = api
                    .column(8)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                discrp = api
                    .column(9)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                jmlh = api
                    .column(10)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                ttlcn1 = api
                    .column(13)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                ttlcn2 = api
                    .column(16)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                ttlcn3 = api
                    .column(19)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(4).footer().innerHTML = number_format(qty);
                // api.column(7).footer().innerHTML = number_format(hjual);
                api.column(8).footer().innerHTML = number_format(subjmlh);
                api.column(9).footer().innerHTML = number_format(discrp);
                api.column(10).footer().innerHTML = number_format(jmlh);
                api.column(13).footer().innerHTML = number_format(ttlcn1);
                api.column(16).footer().innerHTML = number_format(ttlcn2);
                api.column(19).footer().innerHTML = number_format(ttlcn3);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/exportdata/subpenjualanpacktable/') ?>" + tgl1 + "/" + tgl2).load();
    }
</script>