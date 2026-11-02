<?= main_header('Pembelian', 'Pembayaran', 'Pembelian', 'Data Pembayaran') ?>
<?= main_periode('Daftar Pembayaran Pembelian', 1) ?>
<?= main_button($tambah, 0, 1, 'pembelian/transaksi/pembayaranpembelian/entri') ?>



<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No</th>
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">No. Cek</th>
                            <th style="text-align: center">Tanggal Cek</th>
                            <th style="text-align: center">Supplier</th>
                            <th style="text-align: center">Bank</th>
                            <th style="text-align: center">Nilai Pembayaran</th>
                            <th>&nbsp</th>
                            <th>&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>

                        <td colspan="7" style="text-align:right">Total:</td>
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
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#example').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('pembelian/transaksi/pembayaranpembeliantable/') ?>"+tgl1+"/"+tgl2,
                        "type": "GET"
                    },
                    "aLengthMenu": [
                        [10, 15, 20, -1],
                        [10, 15, 20, "Semua"] // change per page values here
                    ],
                    "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {

                        var iTotal = 0;
                        for ( var i=0 ; i<aaData.length ; i++ )
                        {
                            iTotal += aaData[i][7]*1;
                        }


                        var iTot = 0;
                        for ( var i=iStart ; i<iEnd ; i++ )
                        {
                            var x = aaData[aiDisplay[i]][7];
                            var y = Number(x.replace(/[^0-9\.]+/g,""));
                            iTot += y;
                        }

                        var nCells = nRow.getElementsByTagName('td');
                        nCells[1].innerHTML = number_format(iTot);
                    }
                });
    });
    function filterdata()
    {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('pembelian/transaksi/pembayaranpembeliantable/') ?>" +tgl1+"/"+tgl2).load();
    }
</script>