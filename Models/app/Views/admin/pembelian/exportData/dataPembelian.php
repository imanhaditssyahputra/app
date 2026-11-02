<?= main_header('PEMBELIAN', 'Export Data',) ?>
<?= main_periode('Laporan export PEMBELIAN ', 1) ?>
<?= main_button('', 0, 1,) ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="datatable-pembelian" class="table table-bordered nowrap w-100">
                    <thead>
                        <tr>
                            <!--th style="text-align: center">Register</th-->
                            <th style="text-align: center">No Nota</th>
                            <th style="text-align: center">Tgl Input</th>
                            <th style="text-align: center">Tgl Faktur</th>
                            <th style="text-align: center">Kode supplier</th>
                            <th style="text-align: center">Nama Supplier</th>
                            <th style="text-align: center">Alamat</th>
                            <th style="text-align: center">Kota</th>
                            <th style="text-align: center">Telp</th>
                            <th style="text-align: center">Hp</th>
                            <th style="text-align: center">NPWP</th>
                            <th style="text-align: center">Total</th>
                            <th style="text-align: center">Termin</th>
                            <th style="text-align: center">Tgl jatuh Tempo</th>
                            <th style="text-align: center">Bayar</th>
                            <th style="text-align: center">Bruto</th>
                            <th style="text-align: center">Disc</th>
                            <th style="text-align: center">DPP</th>
                            <th style="text-align: center">PPN</th>
                            <th style="text-align: center">Sisa bayar</th>
                            <th style="text-align: center">Jenis Bayar</th>
                            <th style="text-align: center">Nobayar</th>
                            <th style="text-align: center">Limit Kredit</th>
                            <th style="text-align: center">Saldo Kredit</th>
                            <th style="text-align: center">User</th>
                            <th style="text-align: center">lunas</th>
                            <th style="text-align: center">Tagihan</th>
                            <th style="text-align: center">Terbilang</th>
                            <th style="text-align: center">Nofaktur Supplier</th>
                            <th style="text-align: center">tgl Faktur Supplier</th>
                            <th style="text-align: center">NO PO</th>
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
        table = $('#datatable-pembelian').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('pembelian/exportdata/datapembeliantable/') ?>"+tgl1+"/"+tgl2,
                "type": "GET"
            },
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', footer: true },
                { extend: 'excelHtml5', footer: true },
                { extend: 'csvHtml5', footer: true },
                { extend: 'pdfHtml5', footer: true }
            ]
        });
    });

    function filterdata()
    {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('pembelian/exportdata/datapembeliantable/') ?>" +tgl1+"/"+tgl2).load();
    }
</script>