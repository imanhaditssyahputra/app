<div class="container-fluid">
    <?= main_header('Keuangan', 'Daftar Pengeluaran Kas/Bank', 'Keuangan', 'Data Pengeluaran Kas/Bank') ?>
    <?= main_periode('Daftar Pengeluaran Kas/Bank') ?>
    <?= main_button($tambah, 0, 4, 'kasbank/transaksi/pembayaran/entri') ?>
    <div class="row">
        <div class="col-12">
            <div style="overflow-x: auto;">
                <div class="datatable-example">
                    <table class="table table-striped table-hover table-bordered" id="table">
                        <thead>
                            <tr>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">No. Bukti</th>
                                <th style="text-align: center">Tgl. Buat</th>
                                <th style="text-align: center">Tgl. Bayar</th>
                                <th style="text-align: center">Keterangan</th>
                                <th style="text-align: center">Detail</th>
                                <th style="text-align: center">Jumlah</th>
                                <th style="text-align: center">Status</th>
                                <th>&nbsp</th>
                                <th>&nbsp</th>
                                <th>&nbsp</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <td colspan="6" style="text-align:right">Total:</td>
                            <td style="text-align:center"></td>
                            <td colspan="4"></td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Data -->
<div class="modal fade" id="lupDataKeluar" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbayar">
                <div class="modal-header">
                    <h4 class="modal-title">Daftar Detail Pengeluaran</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="tablefaktur">
                            <thead>
                                <th style="width:5%;">No</th>
                                <th style="width:15%;">Kode Akun</th>
                                <th style="width:20%;">Nama Akun</th>
                                <th style="width:20%;">Uraian / Detail</th>
                                <th style="width:15%;">Jumlah</th>
                            </thead>
                            <tbody id="daftardetail"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" onclick="reloadFaktur()" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
                </div>
        </div>
    </div>
</div>

<script>
    var table;
    var base_url = '<?= base_url('kasbank/transaksi/pembayarantable'); ?>';
    $(document).ready(function() {
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#table').DataTable({
            "processing": true, //Feature control the processing indicator.
            // "ajax": {
            //     "url": "<?= base_url('kasbank/transaksi/pembayarantable/') ?>" + tgl1 + "/" + tgl2,
            //     "type": "GET"
            // },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][5] * 1;
                }


                var iTot = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][5];
                    var y = Number(x.replace(/[^0-9\.]+/g, ""));
                    iTot += y;
                }

                var nCells = nRow.getElementsByTagName('td');
                nCells[1].innerHTML = number_format(iTot);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tgl1").value;
        var tgl2 = document.getElementById("tgl2").value;
        var akun = document.getElementById("akunkode").value;
        table = $('#table').DataTable({
            "processing": true,
            "order": [],
            "destroy": true,
            "select": true,

            "aLengthMenu": [
                [15, 20, -1],
                [15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][6] * 1;
                }


                var iTot = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][6];
                    var y = Number(x.replace(/[^0-9\.]+/g, ""));
                    iTot += y;
                }

                var nCells = nRow.getElementsByTagName('td');
                nCells[1].innerHTML = number_format(iTot);
            },
            "ajax": {
                "url": base_url,
                "type": "POST",
                "async": true,
                "data": {
                    "tgl1": tgl1,
                    "tgl2": tgl2,
                    "akun": akun,

                },
            },

        });
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('kasbank/transaksi/pembayaran/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('KAS & BANK', 'Data Berhasil Di Delete', '');
                    table_reload('table');
                }
            });
        }
    }

    function ShowDetail(data) {
        var xhttp;
        if (data == "") {
            document.getElementById("daftardetail").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupDataKeluar').modal('show');
                document.getElementById("daftardetail").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('kasbank/transaksi/pembayarantableDetailData/') ?>" + data, true);
        xhttp.send();
    }
</script>