<?= main_header('Penjualan', 'Faktur', 'Penjualan', ' Data Faktur') ?>
<?= main_periode('Daftar Faktur Penjualan', 0) ?>
<?= main_button($tambah, 0, 1, 'penjualan/transaksi/fakturpenjualan/entri', '', '') ?>



<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">NO</th>
                            <th style="text-align: center">Nomor #</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Pelanggan</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th style="text-align: center">Status Retur</th>
                            <th style="text-align: center">Jatuh Tempo</th>
                            <th style="text-align: center">Total</th>
                            <th>&nbsp</th>
                            <th>&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <td colspan="8" style="text-align:right">Total:</td>
                        <td style="text-align:right"></td>
                        <td colspan="2"></td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kurir Konfirmasi -->
<div class="modal fade" id="kurir" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <span id="nopilihbayar">
                <div class="modal-header">
                    <h4 class="modal-title">Konfirmasi kurir</h4>
                </div>
                <div class="modal-body">
                    <div class="kurir">
                        <form id="submit_form" class="form-horizontal">
                            <input type="hidden" class="form-control" id="id" name="id" readonly>
                            <div class="mb-3">
                                <label for="faktur" class="form-label">No FAKTUR</label>
                                <input type="text" class="form-control" id="faktur" name="faktur" placeholder="Masukkan No Faktur" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="kurir" class="form-label">Kurir</label>
                                <input type="text" class="form-control" id="kurir" name="kurir" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
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
                "url": "<?= base_url('penjualan/transaksi/fakturpenjualantable/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"

            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

                var iTotal = 0;
                for (var i = 0; i < aaData.length; i++) {
                    iTotal += aaData[i][8] * 1;
                }


                var iTot = 0;
                for (var i = iStart; i < iEnd; i++) {

                    var x = aaData[aiDisplay[i]][8];
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
        table.ajax.url("<?= base_url('penjualan/transaksi/fakturpenjualantable/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/delete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PESANAN DISIAPKAN', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }

    function kurir(id,faktur) {
        $('#kurir').modal('show');
		var kurirname = "<?=$username ?>";
        $.ajax({
            type: "get",
            url: "<?= base_url('penjualan/transaksi/fakturpenjualangetdata/') ?>" + id,
            dataType: "json",
            success: function(res) {
                $('input[name=id]').val(res.id);
                $('input[name=faktur]').val(res.kodesi);
                if(res.kurir == null || res.kurir == ''){
                    $('input[name=kurir]').val(kurirname);
                }else{
                    $('input[name=kurir]').val(res.kurir);
                }
                $('input[name=password]').val('');
            }
        });
    }

    // Add & Update Data 
    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var id = $('input[name=id]').val();
        $.ajax({
            type: "post",
            url: "<?= base_url('penjualan/transaksi/fakturpenjualanupdatekurir/') ?>" + id,
            data: $(this).serialize(),
            dataType: "json",
            success: function(res) {
                if (res.status ==1) {
                    swal('KONFIRMASI KURIR', res.message, '');
                    $('#kurir').modal('hide');
                    table_reload('example');
                } else {
                    $('#kurir').modal('hide');
                    swal('KONFIRMASI KURIR', res.message, '');
                    table_reload('example');
                }
            }
        });
    });

    function ShowMessageLogs(data) {
        var xhttp;
        if (data == "") {
            document.getElementById("daftarlogs").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#messagelog').modal('show');
                document.getElementById("daftarlogs").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/ShowMessageLogs/') ?>" + data, true);
        xhttp.send(); 
    }
</script>