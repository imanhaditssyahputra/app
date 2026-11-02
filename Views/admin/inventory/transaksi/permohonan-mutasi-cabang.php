<?= main_header('Inventory', 'Permintaan Mutasi Barang Antar Region', 'Inventory', 'Daftar Permintaan Mutasi Barang Antar Region') ?>
<?= main_periode('Daftar Permintaan Mutasi Barang Antar Region') ?>
<?= main_button('Daftar Permintaan Mutasi Region', 0, 1, 'inventory/transaksi/permohonanmutasicabang/entri') ?>

<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="example" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">No. Mohon</th>
                            <th style="text-align: center">Tanggal</th>
                            <th style="text-align: center">Region Minta</th>
                            <th style="text-align: center">Region Tujuan</th>
                            <th style="text-align: center">Keterangan</th>
                            <th style="text-align: center">Status</th>
                            <th style="text-align: center">Aksi</th>
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
        table = $('#example').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('inventory/transaksi/permohonanmutasicabangtablefilter/') ?>" + tgl1 + "/" + tgl2,
                "type": "GET"
            },
            "aLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "Semua"] // change per page values here
            ],
        });
    });

    function filterdata() {
        var tgl1 = document.getElementById("tanggal1").value;
        var tgl2 = document.getElementById("tanggal2").value;
        table.ajax.url("<?= base_url('inventory/transaksi/permohonanmutasicabangtablefilter/') ?>" + tgl1 + "/" + tgl2).load();
    }

    function data_delete(id) {
        if (confirm('Are you sure ?')) {
            $.ajax({
                type: "post",
                url: "<?= base_url('inventory/transaksi/permohonanmutasicabangdelete/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    swal('PERMOHNAN MUTASI REGION', 'Data Berhasil Di Delete', '');
                    table_reload('example');
                }
            });
        }
    }
    
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