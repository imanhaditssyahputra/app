<div class="container-fluid">
    <?= main_header('Buku Besar', 'Daftar Jurnal') ?>
    <?= main_periode('Daftar Jurnal', 0) ?>
    <?= main_button($tambah, 1, 1, 'bukubesar/transaksi/entrijurnal') ?>
    <div class="row">
        <div class="col-12">
            <div style="overflow-x: auto;">
                <div class="datatable-example">
                    <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th style="text-align: center; width:5%">#No</th>
                                <th style="text-align: center; width:10%">#No. Jurnal</th>
                                <th style="text-align: center; width:5%">No. Ref</th>
                                <th style="text-align: center; width:10%">Tanggal</th>
                                <th style="text-align: center; width:5%">Kode Akun</th>
                                <th style="text-align: center; width:15%">Keterangan</th>
                                <th style="text-align: center; width:10%">Debet</th>
                                <th style="text-align: center; width:10%">Kredit</th>
                                <th style="text-align: center; width:5%">Tipe</th>
                                <th style="text-align: center;width:20%;">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">Total</td>
                                <td></td>
                                <td></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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
                "url": "<?= base_url('bukubesar/transaksi/daftarjurnal-table/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"
            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                var iTotal1 = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][6] * 1;
                    iTotal1 += aaData[i][7] * 1;
                }


                var iTot = 0;
                var iTot1 = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][6];
                    var y = Number(x.replace(/[^0-9\.]+/g, ""));
                    iTot += y;

                    var x1 = aaData[aiDisplay[i]][7];
                    var y1 = Number(x1.replace(/[^0-9\.]+/g, ""));
                    iTot1 += y1;
                }

                var nCells = nRow.getElementsByTagName('td');
                nCells[1].innerHTML = number_format(iTot);
                nCells[2].innerHTML = number_format(iTot1);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('bukubesar/transaksi/daftarjurnal-table/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function JurnalEdit(id) {
        window.location.href = "<?= base_url('bukubesar/transaksi/entrijurnal/') ?>" + id;
    }


    function JurnalDelete(id) {
        if (confirm('Are you sure you want to delete data ' + id + ' ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('bukubesar/transaksi/daftarjurnal-Delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('SALDO AWAL AKUN', 'Data berhasil di hapus', '');
                    table_reload('table');
                }
            });
        }
    }
</script>