<?= main_header('Penjualan', 'Pesanan Penjualan', 'Penjualan', 'Data Pesanan Penjualan') ?>
<?= main_periode('Daftar Pesanan Penjualan') ?>
<?= main_button($tambah, 0, 1, 'penjualan/transaksi/pesananmasuk/entri') ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No</th>
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th style="text-align: center">Total</th>
                            <th style="width: 10%">aksi</th>
                            <th style="width: 10%">&nbsp</th>
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
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#table').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('penjualan/transaksi/pesanantable/') ?>"+tgl1+"/"+tgl2,
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
                            iTotal += aaData[i][6]*1;
                        }


                        var iTot = 0;
                        for ( var i=iStart ; i<iEnd ; i++ )
                        {

                            var x = aaData[aiDisplay[i]][6];
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
        table.ajax.url("<?= base_url('penjualan/transaksi/pesanantable/') ?>" +tgl1+"/"+tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('penjualan/transaksi/pesananmasuk/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PESANAN PENJUALAN', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>