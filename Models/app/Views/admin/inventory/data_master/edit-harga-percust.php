<?php
$db = db_connect();
main_header('Inventory', 'Data Harga Barang per Customer', 'Inventory', 'Edit Harga Percustomer', 'inventory/datamaster/hargabarangpercustomer');
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>*Data Edit
        </div>


    </div>

    <div class="portlet-body form ">
        <form id="frmharga" class="form-horizontal" method="post">
            <div class="form-body ">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab">
                                <i class="fa fa-file"></i>
                                Harga Per Customer
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Tanggal</label>
                                            <div class="col-md-6">
                                                <div class="input-group position-relative">
                                                    <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?php echo date('Y-m-d'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Gudang</label>
                                            <div class="col-md-4">
                                                <input type="text" name="namagudang" id="namagudang" class="form-control input-large" readonly="" value="<?= $RowGudang->nama; ?>">
                                                <input type="hidden" name="gudang" id="gudang" class="form-control input-large" readonly="" value="<?= $gudang; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Customer</label>
                                            <div class="col-md-7">
                                                <div class="input-group position-relative">
                                                    <input type="text" name="namacust" id="namacust" class="form-control input-large" readonly="" value="<?= $RowCustomer->nama; ?>">
                                                    <input type="hidden" name="kodecust" id="kodecust" class="form-control input-large" readonly="" value="<?= $cust; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-md-3 control-label pt-2"></label>
                                    <span class="btn blue" data-bs-toggle="modal" data-bs-target="#AllCN"> Input All CN <i class="fa fa-edit"></i></span>
                                </div>
                            </div>
                            <div class="row">
                                <h3>Daftar Barang Percustomer</h3>
                                <div class="table-responsive">
                                    <div class="col-md-12">

                                        <?php
                                        $query = "SELECT * from inv_barang_harga 
                                        where kode_customer='$cust' and cabang='$RowCabang' and gudang='$gudang' order by nama_barang";
                                        $customer = $db->query($query)->getResult();
                                        $JumData = $db->query($query)->getNumRows();
                                        $no = 1;
                                        foreach ($customer as $rows) {
                                            error_reporting(0);
                                            $supplier = $db->query("SELECT ap_supplier.*, inv_barang_harga.*, inv_barang.* 
                                                                FROM inv_barang_harga 
                                                                INNER JOIN ap_supplier ON inv_barang_harga.kode_spl = ap_supplier.kode 
                                                                INNER JOIN inv_barang ON inv_barang_harga.kodeitem = inv_barang.kodeitem 
                                                                WHERE inv_barang.kodeitem ='$rows->kodeitem' and inv_barang.kdgudang='$gudang' 
                                                                and inv_barang.kdcbg='$RowCabang' and inv_barang_harga.gudang='$gudang' 
                                                                and inv_barang_harga.cabang='$RowCabang'")->getRow();
                                        ?>
                                            <div class="accordion" id="accordionPanelsStayOpenExample">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="panelsStayOpen-heading<?= $no; ?>">
                                                        <button class="accordion-button collapsed bg-primary bg-gradient text-white" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $no; ?>" aria-expanded="true" aria-controls="panelsStayOpen-collapse<?= $no; ?>">
                                                            <?= $rows->nama_barang; ?>
                                                        </button>
                                                        <input type="hidden" name="kode[]" id="kode" class="form-control input-large" readonly="" value="<?= $rows->kodeitem; ?>">
                                                    </h2>
                                                    <div id="panelsStayOpen-collapse<?= $no; ?>" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading<?= $no; ?>">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <h1><?= $rows->nama_barang; ?></h1>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <div style="overflow-x: auto;">
                                                                        <div class="datatable-example">
                                                                            <table id="datatable2" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                                                                <thead>
                                                                                    <tr class="info">
                                                                                        <th style="text-align: center">Nama
                                                                                            Supplier</th>
                                                                                        <th style="text-align: center">H. Beli
                                                                                            Bruto
                                                                                        </th>
                                                                                        <th style="text-align: center">disc %
                                                                                        </th>
                                                                                        <th style="text-align: center">H. Beli
                                                                                            Netto</th>
                                                                                    </tr>
                                                                                    <thead>
                                                                                    <tbody id="tbody-data">
                                                                                        <tr align="center">
                                                                                            <td>
                                                                                                <input type="text" class="form-control input-large" readonly="" value="<?= $supplier->nama; ?>">
                                                                                                <input name="kd_spl" id="kode_spl" <?php if (!empty($supplier->kode_spl)) { ?> value="<?= $supplier->kode_spl; ?>" <?php } else { ?> value="" <?php } ?> readonly type="hidden" class="form-control rightJustified">
                                                                                            </td>
                                                                                            <td>
                                                                                                <input name="bruto_spl" id="bruto_spl" <?php if (!empty($supplier->kode_spl)) { ?> value="<?= $supplier->bruto_spl; ?>" <?php } else { ?> value="0" <?php } ?> readonly type="text" class="form-control input-small rightJustified">
                                                                                            </td>

                                                                                            <td>
                                                                                                <input name="disc_spl" id="disc_spl" <?php if (!empty($supplier->kode_spl)) { ?> value="<?= $supplier->disc_spl; ?>" <?php } else { ?> value="0" <?php } ?> readonly type="text" class="form-control input-small rightJustified">
                                                                                            </td>
                                                                                            <td>
                                                                                                <input name="jumlah_netto" onchange="total()" id="jumlah_netto<?= $no ?>" readonly <?php if (!empty($supplier->kode_spl)) { ?> value='<?= $supplier->netto_spl; ?>' <?php } else { ?> value='0' <?php } ?> type="text" class="form-control input-small rightJustified">
                                                                                                <input type="hidden" name="hargasendiri" id="hargamodal" value="<?= $supplier->hargamodal; ?>" class="form-control rightJustified">
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div style="overflow-x: auto;">
                                                                <div class="datatable-example">
                                                                    <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                                                        <thead>
                                                                            <tr class="info">
                                                                                <th style="text-align: center">Bruto
                                                                                </th>
                                                                                <th style="text-align: center">disc%</th>
                                                                                <th style="text-align: center">Netto
                                                                                </th>
                                                                                <th style="text-align: center">Bagian 1
                                                                                </th>
                                                                                <th style="text-align: center">Bagian 2
                                                                                </th>
                                                                                <th style="text-align: center">Bagian 3
                                                                                </th>
                                                                                <th style="text-align: center">Hitung
                                                                                    Profit</th>

                                                                            </tr>
                                                                        </thead>

                                                                        <tbody>

                                                                            <?php
                                                                            $customer = $db->query("SELECT * from inv_barang_harga 
                                                                                    where kode_customer='$cust' and kodeitem='$rows->kodeitem' and cabang='$RowCabang' and gudang='$gudang'")->getResult();
                                                                            $idx = 1;
                                                                            foreach ($customer as $row) {
                                                                            ?>
                                                                                <tr>
                                                                                    <td><input id="bruto<?= $no ?>" onchange="totalline(<?= $no ?>)" name="bruto[]" type="text" value="<?= number_format($row->bruto_cust, 2, '.', ','); ?>" class="form-control input-small rightJustified"></td>
                                                                                    <td><input id="disc<?= $no ?>" oninput="totalline(<?= $no ?>)" name="disc[]" type="text" value="<?= $row->diskon_pr; ?>" class="form-control input-small rightJustified"></td>
                                                                                    <td><input id="netto<?= $no ?>" onchange="totalline(<?= $no ?>)" name="netto[]" type="text" value="<?= number_format($row->netto_cust, 2, '.', ','); ?>" class="form-control input-small rightJustified"></td>
                                                                                    <td>
                                                                                        <input id="bagian1<?= $no ?>" onchange="totalline(<?= $no ?>)" name="bagian1[]" type="text" value="<?= $row->bagian1; ?>" class="form-control input-small rightJustified" placeholder="Bagian 1">
                                                                                        <input id="fee1<?= $no ?>" onchange="totalline(<?= $no ?>)" name="fee1[]" type="text" placeholder="CN % " value="<?= $row->fee1_pr; ?>" class="form-control input-small rightJustified">
                                                                                        <input id="fee1rp<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="fee1rp[]" type="text" placeholder="Rp bagian 1" value="<?= number_format($row->fee1_rp, 2, '.', ','); ?>" class="form-control input-small rightJustified" readonly>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="bagian2<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="bagian2[]" type="text" value="<?= $row->bagian2; ?>" class="form-control input-small rightJustified" placeholder="Bagian 2">
                                                                                        <input id="fee2<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="fee2[]" type="text" placeholder="CN % " value="<?= $row->fee2_pr; ?>" class="form-control input-small rightJustified">
                                                                                        <input id="fee2rp<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="fee2rp[]" type="text" placeholder="Rp Bagian 2" value="<?= number_format($row->fee2_rp, 2, '.', ','); ?>" class="form-control input-small rightJustified" readonly>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="bagian3<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="bagian3[]" type="text" value="<?= $row->bagian3; ?>" class="form-control input-small rightJustified" placeholder="Bagian 3">
                                                                                        <input id="fee3<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="fee3[]" type="text" placeholder="CN % " value="<?= $row->fee3_pr; ?>" class="form-control input-small rightJustified">
                                                                                        <input id="fee3rp<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="fee3rp[]" type="text" placeholder="Rp Bagian 3" value="<?= number_format($row->fee3_rp, 2, '.', ','); ?>" class="form-control input-small rightJustified" readonly>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input id="profitrp<?= $no; ?>" name="profitrp[]" type="text" placeholder="Profit Rp" readonly value="<?= number_format($row->profit_rp, 2, '.', ','); ?>" class="form-control input-small rightJustified" onchange="totalline(<?= $no; ?>)">
                                                                                        <input id="netmarginrp<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="netmarginrp[]" type="text" placeholder="Net Margin Rp" value="<?= number_format($row->netmargin_rp, 2, '.', ','); ?>" class="form-control input-small rightJustified" readonly>
                                                                                        <input id="Profit<?= $no; ?>" onchange="totalline(<?= $no; ?>)" name="Profit[]" placeholder="Profit %" type="text" value="<?= number_format($row->profit_pr, 2, '.', ','); ?>" class="form-control input-small rightJustified" readonly>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php
                                                                                $idx++;
                                                                            }

                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php $no++;
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- tab1-->
                    </div>
                    <!--tab-->

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well">


                                <button type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i>
                                    Simpan</button>

                                <div class="btn-group">
                                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-pencil-square-o"></i> Data Baru</button>
                                </div>
                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </form>
    </div>
</div>

<!-- Modal All Cn -->
<div class="modal fade" id="AllCN" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input All CN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmharga" class="form-horizontal" method="post">
                    <!-- First Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Bagian 1</label>
                                    <div class="col-md-5">
                                        <input id="bagianall" name="bagianall1" class="form-control input-medium" type="text" value="" placeholder="Bagian 1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-4"> % Bagian 1</label>
                                    <div class="col-md-7">
                                        <input id="PrAll" name="PrAll1" class="form-control input-medium" type="text" placeholder="CN %" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Second Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Bagian 2</label>
                                    <div class="col-md-5">
                                        <input id="bagianall" name="bagianall2" class="form-control input-medium" type="text" value="" placeholder="Bagian 2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-4"> % Bagian 2</label>
                                    <div class="col-md-7">
                                        <input id="PrAll" name="PrAll2" class="form-control input-medium" type="text" placeholder="CN %" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Third Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-3">Bagian 3</label>
                                    <div class="col-md-5">
                                        <input id="bagianall" name="bagianall3" class="form-control input-medium" type="text" value="" placeholder="Bagian 3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="row">
                                    <label class="control-label pt-2 col-md-4"> % Bagian 3</label>
                                    <div class="col-md-7">
                                        <input id="PrAll" name="PrAll3" class="form-control input-medium" type="text" placeholder="CN %" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Third Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" onclick="terapkan()" class="btn blue"><i class="fa fa-paper-plane"></i>
                                Terapkan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function save() {
        var kodecust = $('[name="kodecust"]').val();
        var namacust = $('[name="namacust"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (kodecust == "") {
            swal('HARGA PERCUSTTOMER', 'Nama Custtomer belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/datamaster/hargabarang/saveeditpercust') ?>",
                data: $('#frmharga').serialize(),
                type: 'POST',
                success: function(data) {
                    swal({
                        title: "HARGA PERCUSTTOMER",
                        html: "<p> Nama Customer   : <b>" + namacust + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        location.href = "<?= BASEURL . 'inventory/datamaster/hargabarangpercustomer' ?>";
                    });

                },
                error: function(data) {
                    swal('HARGA PERCUSTTOMER', 'Data gagal disimpan ...', '');
                }
            });
        }
    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        var hargamodal = $('#hargamodal').val();
        var vhargamodal = Number(hargamodal.replace(/[^0-9\.]+/g, ""));

        var jumlah_netto = $('#jumlah_netto' + id).val();
        var vjumlah_netto = Number(jumlah_netto.replace(/[^0-9\.]+/g, ""));

        var bruto_cust = $('#bruto' + id).val();
        var vbruto_cust = Number(bruto_cust.replace(/[^0-9\.]+/g, ""));

        var diskon_cust = $('#disc' + id).val();

        var netto_cust = vbruto_cust - (diskon_cust / 100) * vbruto_cust;

        var vnetto_cust = $('#netto' + id).val(number_format(netto_cust));

        var fee1 = $('#fee1' + id).val();
        var fee1rp = ((fee1 / 100) * netto_cust);
        $('#fee1rp' + id).val(number_format(fee1rp));

        var fee2 = $('#fee2' + id).val();
        var fee2rp = ((fee2 / 100) * netto_cust);
        $('#fee2rp' + id).val(number_format(fee2rp));

        var fee3 = $('#fee3' + id).val();
        var fee3rp = ((fee3 / 100) * vbruto_cust);
        $('#fee3rp' + id).val(number_format(fee3rp));

        var profitrp = netto_cust - fee1rp - fee2rp - fee3rp - vhargamodal - vjumlah_netto;
        $('#profitrp' + id).val(number_format(profitrp));

        var netmargin = netto_cust - fee1rp - fee2rp - fee3rp;
        $('#netmarginrp' + id).val(number_format(netmargin));

        var profit_pr = (profitrp / netmargin) * 100;
        $('#Profit' + id).val(number_format(profit_pr));

    }

    function total() {
        var table = document.getElementById('datatable2');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            harga = row.cells[3].children[0].value;
            var harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));

            tjumlah = harga1;

        }

        var table2 = document.getElementById('datatable');
        var rowCount2 = table2.rows.length;

        tbiaya = 0;

        for (var i = 1; i < rowCount2; i++) {
            var row = table2.rows[i];

            var hargamodal = $('#hargamodal').val();
            var vhargamodal = Number(hargamodal.replace(/[^0-9\.]+/g, ""));

            var vjumlah_netto = tjumlah;

            var bruto_cust = row.cells[1].children[0].value;
            var vbruto_cust = Number(bruto_cust.replace(/[^0-9\.]+/g, ""));
            var diskon_cust = row.cells[2].children[0].value;

            var netto_cust = vbruto_cust - (diskon_cust / 100) * vbruto_cust;
            row.cells[3].children[0].value = number_format(netto_cust);
            var fee1 = row.cells[4].children[1].value;
            var fee1rp = ((fee1 / 100) * netto_cust);
            row.cells[4].children[2].value = number_format(fee1rp);

            var fee2 = row.cells[5].children[1].value;
            var fee2rp = ((fee2 / 100) * netto_cust);
            row.cells[5].children[2].value = number_format(fee2rp);

            var fee3 = row.cells[6].children[1].value;
            var fee3rp = ((fee3 / 100) * vbruto_cust);
            row.cells[6].children[2].value = number_format(fee3rp);

            var profitrp = netto_cust - fee1rp - fee2rp - fee3rp - vhargamodal - vjumlah_netto;
            row.cells[7].children[0].value = number_format(profitrp);
            var netmargin = netto_cust - fee1rp - fee2rp - fee3rp;
            row.cells[7].children[1].value = number_format(netmargin);

            var profit_pr = (profitrp / netmargin) * 100;
            row.cells[7].children[2].value = number_format(profit_pr);
        }
    }

    function terapkan() {
        var CountData = <?= $JumData ?>;
        var bagian1 = $('[name="bagianall1"]').val();
        var cn1 = $('[name="PrAll1"]').val();

        var bagian2 = $('[name="bagianall2"]').val();
        var cn2 = $('[name="PrAll2"]').val();

        var bagian3 = $('[name="bagianall3"]').val();
        var cn3 = $('[name="PrAll3"]').val();

        var hasilbag1 = $('[name="bagian1[]"]').val(bagian1);
        var hasilbagcn1 = $('[name="fee1[]"]').val(cn1);

        var hasilbag2 = $('[name="bagian2[]"]').val(bagian2);
        var hasilbagcn2 = $('[name="fee2[]"]').val(cn2);

        var hasilbag3 = $('[name="bagian3[]"]').val(bagian3);
        var hasilbagcn3 = $('[name="fee3[]"]').val(cn3);
        $('#AllCN').modal('hide');

        for (var i = 1; i < CountData; i++) {
            totalline(i);
        }

    }
</script>

</body>

</html>