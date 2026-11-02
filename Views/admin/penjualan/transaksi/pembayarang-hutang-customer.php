<?= main_header('Penjualan', 'Penerimaan', 'Penjualan', 'Data Penerimaan') ?>
<?= main_periode('Daftar Penerimaan Penjualan') ?>
<?= main_button($tambah, 0, 1, 'penjualan/transaksi/pembayaranhutangcustomer/entri') ?>


<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No</th>
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal Bayar</th>
                            <th style="text-align: center">No Faktur</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Bank</th>
                            <th style="text-align: center">Status</th>
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

<!-- Modal Faktur -->
<div class="modal fade" id="lupfaktur" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbayar">
                <div class="modal-header">
                    <h4 class="modal-title">Daftar Faktur Bayar</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="tablefaktur">
                            <thead>
                                <th style="width:5%;">No</th>
                                <th style="width:20%;">No Bayar</th>
                                <th style="width:20%;">Tanggal Bayar</th>
                                <th style="width:15%;">Faktur</th>
                            </thead>
                            <tbody id="daftarfaktur"></tbody>
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
    $(document).ready(function() {
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#example').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/transaksi/pembayaranhutangcustomertable/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"

            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][7] * 1;
                }


                var iTot = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][7];
                    var y = Number(x.replace(/[^0-9\.]+/g, ""));
                    iTot += y;
                }

                var nCells = nRow.getElementsByTagName('td');
                nCells[1].innerHTML = number_format(iTot);
            }
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/transaksi/pembayaranhutangcustomertable/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('penjualan/transaksi/pembayaranhutangcustomer/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PEMBAYARAN CUSTOMER', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }

    function ShowFaktur(data) {
        var xhttp;
        if (data == "") {
            document.getElementById("daftarfaktur").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupfaktur').modal('show');
                document.getElementById("daftarfaktur").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/pembayaranhutangcustomertablefaktur/') ?>" + data, true);
        xhttp.send();
    }
</script>