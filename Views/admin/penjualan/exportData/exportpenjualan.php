<?= main_header('PENJUALAN', 'Export Data Penjualan',) ?>
<?= main_periode('Export PENJUALAN  ', 1) ?>
<?= main_button('', 0, 1,) ?>



<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="export_penjualan" class="display table table-bordered nowrap w-100" style="width:100%">
                    <thead>
                        <tr>
                            <th>No Nota</th>
                            <th>Tgl Penjualan</th>
                            <th>Kode Customer</th>
                            <th>Nama Customer</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Telp</th>
                            <th>Hp</th>
                            <th>Nama Yayasan/PT</th>
                            <th>total</th>
                            <th>Termin</th>
                            <th>Tgl Jatuh Tempo</th>
                            <th>No konsiyansi</th>
                            <th>Bayar</th>
                            <th>Bruto</th>
                            <th>Disc Rp</th>
                            <th>DPP</th>
                            <th>PPN</th>
                            <th>Sisa Bayar</th>
                            <th>Marketing</th>
                            <th>Kode Bayar</th>
                            <th>Jenis Bayar</th>
                            <th>No Bayar</th>
                            <th>Ongkos Kirim</th>
                            <th>Limit Kredit</th>
                            <th>Saldo Kredit</th>
                            <th>User</th>
                            <th>Lunas</th>
                            <th>Tagihan</th>
                            <th>Terbilang</th>
                            <th>No Pajak</th>
                            <th>Total CN</th>
                            <th>No PO</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        var tgl1 = "1";
        var tgl2 = "1";
        table = $('#export_penjualan').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/exportdata/exportpenjualantable/') ?>" + tgl1 + "/" + tgl2,
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
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/exportdata/exportpenjualantable/') ?>" + tgl1 + "/" + tgl2).load();
    }
</script>