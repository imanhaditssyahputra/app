<?= main_header('PENJUALAN', 'Export Data Cortex',) ?>
<?= main_periode('Laporan Data Cortex', 1) ?>
<?= main_button('', 0, 1,) ?>


<div class="tabbable tabbable-custom tabbable-full-width">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="faktur-tab" data-bs-toggle="tab" data-bs-target="#faktur" type="button" role="tab" aria-controls="faktur" aria-selected="true">Faktur</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="fakturdetail-tab" data-bs-toggle="tab" data-bs-target="#fakturdetail" type="button" role="tab" aria-controls="fakturdetail" aria-selected="false" onclick="DataDetail()">Faktur Detail</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="faktur" role="tabpanel" aria-labelledby="faktur-tab">
            <div class="row">
                <div class="col-12">
                    <div style="overflow-x: auto;">
                        <div class="datatable-example">
                            <table id="fakturtable" class="display table table-bordered nowrap w-100" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center">Baris</th>
                                        <th style="text-align: center">Tanggal Faktur</th>
                                        <th style="text-align: center">Jenis Faktur</th>
                                        <th style="text-align: center">Kode Transaksi</th>
                                        <th style="text-align: center">Keterangan Tambahan</th>
                                        <th style="text-align: center">Dokumen Pendukung</th>
                                        <th style="text-align: center">Referensi</th>
                                        <th style="text-align: center">Cap Fasilitas</th>
                                        <th style="text-align: center">ID TKU Penjual</th>
                                        <th style="text-align: center">NPWP/NIK Pembeli</th>
                                        <th style="text-align: center">Jenis ID Pembeli</th>
                                        <th style="text-align: center">Negara Pembeli</th>
                                        <th style="text-align: center">Nomor Dokumen Pembeli</th>
                                        <th style="text-align: center">Nama Pembeli</th>
                                        <th style="text-align: center">Alamat Pembeli</th>
                                        <th style="text-align: center">Email Pembeli</th>
                                        <th style="text-align: center">ID TKU Pembeli</th>
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
        <div class="tab-pane fade" id="fakturdetail" role="tabpanel" aria-labelledby="fakturdetail-tab">
        <div class="row">
                <div class="col-12">
                    <div style="overflow-x: auto;">
                        <div class="datatable-example">
                            <table id="fakturDetailtable" class="display table table-bordered nowrap w-100" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center">Baris</th>
                                        <th style="text-align: center">Barang/Jasa</th>
                                        <th style="text-align: center">Kode Barang Jasa</th>
                                        <th style="text-align: center">Nama Barang/Jasa</th>
                                        <th style="text-align: center">Nama Satuan Ukur</th>
                                        <th style="text-align: center">Harga Satuan</th>
                                        <th style="text-align: center">Jumlah Barang Jasa</th>
                                        <th style="text-align: center">Total Diskon</th>
                                        <th style="text-align: center">DPP</th>
                                        <th style="text-align: center">DPP Nilai Lain</th>
                                        <th style="text-align: center">Tarif PPN</th>
                                        <th style="text-align: center">PPN</th>
                                        <th style="text-align: center">Tarif PPnBM</th>
                                        <th style="text-align: center">PPnBM</th>
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
    </div>
</div>
<script>
    var tgl1 = "1";
    var tgl2 = "1";
    $(document).ready(function() {
        
        table = $('#fakturtable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/exportdata/coretaxtable/') ?>" + tgl1 + "/" + tgl2 + "/1",
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
            ]
        });
    });

    function DataDetail(){
        tableDetail = $('#fakturDetailtable').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('penjualan/exportdata/coretaxtable/') ?>" + tgl1 + "/" + tgl2 + "/2",
                "type": "GET",
                "dataSrc": function (json) {
                // Proses data di sini
                console.log(json.differentDataCortex); // Lihat data yang diterima
                return json.data; // Kembalikan data yang ingin ditampilkan di DataTable
            }
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
            ]
        });
    }

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('penjualan/exportdata/subpenjualantable/') ?>" + tgl1 + "/" + tgl2).load();
    }
</script>