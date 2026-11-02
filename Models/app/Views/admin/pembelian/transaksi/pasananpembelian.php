<?php 
    main_header('Pembelian ', 'Pesanan Pembelian');
    main_periode('Daftar Pesanan Pembelian', 1);
    main_button($tambah, 1, 1, 'pembelian/transaksi/pesananpembelian/entri'); ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor #</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th style="width: 10%;">&nbsp</th>
                            <th style="width: 10%;">&nbsp</th>
                            <th style="width: 10%;">&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="fw-bold">Total</td>
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
        table = $('#table').DataTable({
                    "processing": true, //Feature control the processing indicator.
                    "ajax": {
                        "url": "<?= base_url('pembelian/transaksi/pesananpembeliantable/') ?>"+tgl1+"/"+tgl2,
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
        table.ajax.url("<?= base_url('pembelian/transaksi/pesananpembeliantable/') ?>" +tgl1+"/"+tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('pembelian/transaksi/pesananpembeliandelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PESANAN PEMBELIAN', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }
</script>