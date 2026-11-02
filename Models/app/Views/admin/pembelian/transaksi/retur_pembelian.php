<?= main_header('Pembelian', 'Retur',) ?>
<?= main_periode('Daftar Retur Pembelian', 1) ?>
<?= main_button($tambah, 0, 1, 'pembelian/transaksi/returpembelian/entri') ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor#</th>
                            <th>Tanggal</th>
                            <th>Pemasok</th>
                            <th>Keterangan</th>
                            <th>Total</th>
                            <th style="width: 8%;"></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="fw-bold">Total</td>
                            <td>0</td>
                            <td colspan="3"></td>
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
        table = $('#example').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('pembelian/transaksi/returpembeliantable/') ?>"+tgl1+"/"+tgl2,
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
                            iTotal += aaData[i][5]*1;
                        }


                        var iTot = 0;
                        for ( var i=iStart ; i<iEnd ; i++ )
                        {
                            var x = aaData[aiDisplay[i]][5];
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
        table.ajax.url("<?= base_url('pembelian/transaksi/returpembeliantable/') ?>" +tgl1+"/"+tgl2).load();
    }
</script>