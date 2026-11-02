<div class="container-fluid">
    <?= main_header('Keuangan', 'Daftar Buku Kas/Bank', 'Keuangan', 'Data Buku Kas/Bank') ?>
    <?= main_periode('Daftar Buku Kas/Bank') ?>
    <?= main_button('', 0, 2, 'kasbank/transaksi/pembayaran/entri') ?>
    <div class="row">
        <div class="col-12">
            <div style="overflow-x: auto;">
                <div class="datatable-example">
                    <table class="table table-striped table-hover table-bordered" id="table">
                        <thead>
                            <tr>
                                <th style="text-align: center">No</th>
                                <th style="text-align: center">No. Bukti</th>
                                <th style="text-align: center">Tanggal</th>
                                <th style="text-align: center">Uraian</th>
                                <th style="text-align: center">Pemasukan</th>
                                <th style="text-align: center">Pengeluaran</th>
                                <th style="text-align: center">Saldo</th>
                                <th style="text-align: center">Nama Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
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
        var akun = "1";
        table = $('#table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('kasbank/transaksi/bukukastable/') ?>" + tgl1 + "/" + tgl2 + "/" + akun,
                "type": "GET"
            },
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "Semua"] // change per page values here
            ],
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
            ]
        });

    });

    function filterdata() {
        var bln = document.getElementById("periodebln").value;
        var thn = document.getElementById("periodethn").value;
        var akun = document.getElementById("kodeakun").value;
        table.ajax.url("<?= base_url('kasbank/transaksi/bukukastable/') ?>" + bln + "/" + thn + "/" + akun).load();
    }
    SelectInModalAkun();
</script>