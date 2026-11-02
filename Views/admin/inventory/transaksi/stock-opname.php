<?= main_header('Inventory', 'Stock Opname'); ?>
<?= main_periode('Daftar Stock Opname', 0) ?>

<div class="form-group" id="process" style="display: none;">
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-body">
                <div class="table-toolbar d-flex justify-content-between">
                    <div class="d-flex">
                        <a href="<?= BASEURL ?>inventory/transaksi/stokopname/entri" class="btn add-btn fw-bold me-2">
                            <i class="fa fa-plus"></i>
                            Data Baru
                        </a>
                        <button class="btn btn-default refresh-btn fw-bold me-2"><i class="fa-solid fa-refresh fw-bold"></i> Refresh</button>
                        <?php if ($AksesPraSO) { ?>
                            <button class="btn btn-default refresh-btn fw-bold me-2 text-white" style="background-color: #5bc0de;" onclick="praso()" <?= ($setpraso == 1) ? 'disabled' : '' ?>><i class="fa-solid fa-folder fw-bold"></i> PRA SO</button>
                            <button class="btn btn-default refresh-btn fw-bold me-2 text-white" style="background-color: #428bca;" onclick="pascaso()" <?= ($setpraso == 1) ? '' : 'disabled' ?>><i class="fa-solid fa-paper-plane fw-bold"></i> PASCA SO</button>
                        <?php } ?>
                    </div>
                    <div>
                        <button id="btnGroupVerticalDrop1" type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Data <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" id="cetak1" onclick="getModal(this.id)">
                                Cetak Lembar Stock Opname
                            </button>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" id="cetak2" onclick="getModal(this.id)">
                                Cetak Hasil Stock Opname
                            </button>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" id="cetak3" onclick="getModal(this.id)">
                                Cetak Hasil Stock Opname Selisih
                            </button>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" id="DPSO" onclick="GetShowPeriodeSo(this.id)">
                                Periode SO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div style="overflow-x: auto;">
            <div class="datatable-example">
                <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th style="text-align: center;width:15%">Nama Gudang</th>
                            <th style="text-align: center;width:10%">Tanggal</th>
                            <th style="text-align: center;width:10%">Kode Item</th>
                            <th style="text-align: center;width:30%">Nama Barang</th>
                            <th style="text-align: center">Satuan</th>
                            <th style="text-align: center">Qty</th>
                            <th style="text-align: center;width:16%;">&nbsp</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--Modal Lembar Stock Opname-->
<div class="modal fade" id="cetak" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-small">
        <div class="modal-content">
            <span id="SO">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul">Lembar Cetakan</h4>
                </div>
                <div class="modal-body">
                    <form action="#" class="form-horizontal">

                        <div class="row mb-4">
                            <label for="horizontal-mulai-input" class="col-sm-3 col-form-label">Region</label>
                            <div class="col-sm-9">
                                <select class="form-control input-medium" id="cabang" name="cabang" data-placeholder="Pilih Region">
                                    <option value="0">ALL</option>
                                    <?php
                                    $db = db_connect();
                                    $cabang =  $db->table("ms_unit")->get()->getResult();
                                    foreach ($cabang as $row) {
                                    ?>
                                        <option value="<?= $row->kode ?>"> <?= $row->nama ?> </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-sd-input" class="col-sm-3 col-form-label">Tanggal</label>
                            <div class="col-sm-9">
                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <p align="center">
                        <button type="button" id="btnfilter" class="btn green" onclick="javascript:window.open(GetFormulir(),'_blank')" data-bs-dismiss="modal">Buka Data</button>
                    </p>
                </div>
        </div>
    </div>
</div>


<!--Modal Periode Stock Opname-->
<div class="modal fade" id="periodeso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-medium">
        <div class="modal-content">
            <span id="DataPeriodeSo">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul">Lembar Periode SO</h4>
                </div>
                <div class="modal-body">
                    <form action="#" class="form-horizontal">
                        <table id="tableperiodeso" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $db = db_connect();
                                $session = session();
                                $cabang = $session->get('cabang');
                                $RowTglSo = $db->query("SELECT tgl FROM inv_stockopname where cabang='$cabang' GROUP BY tgl")->getResult();
                                foreach ($RowTglSo as $row) {
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= date('d-m-Y', strtotime($row->tgl)); ?></td>
                                        <td><input name="radio[]" id="radio<?= $no ?>" value="<?= date('Y-m-d', strtotime($row->tgl)); ?>" onclick="ambilTanggal(this)" type="radio" class="form-check-input"></td>
                                    </tr>
                                <?php $no++;
                                } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="periodeso" id="ambilperiodeso">
                    </form>
                </div>
                <div class="modal-footer">
                    <p align="center">
                        <button type="button" id="btnfilter" class="btn green" onclick="GetPeriodeSo()" data-bs-dismiss="modal">Buka Data</button>
                    </p>
                </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var tgl1 = "1";
        table = $('#table').DataTable({
            "processing": true, //Feature control the processing indicator.
            "ajax": {
                "url": "<?= base_url('inventory/transaksi/stokopnametable/') ?>" + tgl1,
                "type": "GET"

            },
            "aLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "Semua"] // change per page values here
            ],
        });
    });

    function delete_data(id) {
        if (confirm('Yakin data barang dengan kode ' + id + ' ini akan dihapus ?')) {
            // ajax delete data to database
            $.ajax({
                url: "<?= base_url('inventory/transaksi/sodelete/') ?>" + id,
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    //if success reload ajax table
                    $('#modal_form').modal('hide');
                    reload_table();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting data');
                }
            });

        }
    }

    function getModal(id) {
        var vid = id.substring(5);
        document.getElementById("SO").value = vid;
        $('#cetak').modal('show');
    }

    function GetShowPeriodeSo(id) {
        var vid = id.substring(5);
        document.getElementById("DataPeriodeSo").value = vid;
        $('#periodeso').modal('show');
    }

    function GetFormulir() {
        var cetak = $('#SO').val();
        var cabang = $('#cabang').val();
        var tgl = $('#tanggal').val();

        var baseurl = "<?php echo base_url() ?>";
        if (cetak == 1)
            return baseurl + 'inventory/transaksi/cetaklembarso/' + cabang + '/' + tgl;
        else if (cetak == 2)
            return baseurl + 'inventory/transaksi/cetaklembarhasilso/' + cabang + '/' + tgl;
        else
            return baseurl + 'inventory/transaksi/cetaklembarhasilselisihso/' + cabang + '/' + tgl;
    }

    function praso() {
        if (confirm('klik yes jika SO ingin di mulai ')) {
            // ajax delete data to database
            $.ajax({
                url: "<?= base_url('inventory/transaksi/praso') ?>",
                type: "post",
                dataType: "JSON",
                success: function(data) {
                    swal('STOK OPNAME', 'Reset Stock Opname Berhasil', '');
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal('STOK OPNAME', 'Error deleting data', '');
                    location.reload();
                },
                statusCode: {
                    400: function() {
                        swal('STOK OPNAME', '400 status code! user error', '');
                    },
                    500: function() {
                        swal('STOK OPNAME', '500 status code! server error', '');
                    }
                }
            });

        }
    }

    function pascaso() {
        if (confirm('Pengecekan Pasca SO')) {
            // ajax delete data to database
            $.ajax({
                url: "<?= base_url('inventory/transaksi/pascaso') ?>",
                type: "post",
                dataType: "JSON",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    swal('STOK OPNAME', 'Pengecekan data berhasil', '');
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#process').css('display', 'none');
                    swal('STOK OPNAME', 'Pengecekan data berhasil', '');
                    location.reload();
                }
            });

        }
    }

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    function ambilTanggal(radio) {
        var tanggal = radio.value;
        var test1 = $("#ambilperiodeso").val(tanggal);
    }

    function GetPeriodeSo() {
        var tgl1 = document.getElementById("ambilperiodeso").value;
        table.ajax.url("<?= base_url('inventory/transaksi/stokopnametable/') ?>" + tgl1).load();
    }
</script>