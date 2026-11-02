<?php
$db = db_connect();
if ($data != []) {
    main_header('Penjualan', 'Kontra Bon', 'Daftar Kontra Bon', 'View Kontra Bon', 'penjualan/transaksi/kontrabon');
    $DataOrder = 'Data View';
    $CSRow = $db->table("ar_customer")->getWhere(['kode' => $data->kodecust])->getRow();
} else {
    main_header('Penjualan', 'Kontra Bon', 'Daftar Kontra Bon', 'Entri Kontra Bon',  'penjualan/transaksi/kontrabon');
    $DataOrder = 'Data Baru';
}
?>

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

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>*<?= $DataOrder ?>
        </div>

    </div>

    <div class="portlet-body form">
        <form id="frmpenjualan" class="form-horizontal" method="post">
            <input type="hidden" value="<?= ($data != []) ? $data->id : 0 ?>" name="id" />
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Kontra Bon</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Customer</label>
                                            <div class="col-md-9">
                                                <select id="cust" name="cust" class="form-select input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                                    <option>---Pilih Customer</option>
                                                    <?php if ($data != []) {
                                                        foreach ($arCust as $key) { ?>
                                                            <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kodecust) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                        <?php }
                                                    } else {
                                                        foreach ($res as $key) {  ?>
                                                            <option value="<?= $key->kode ?>" <?= ($data != []) ? (($key->kode == $data->kodecust) ? "selected" : '') : '' ?>><?= $key->nama ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn blue" onclick="getfaktur()"><i class="fa fa-download"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No. Bukti #</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= ($data != []) ? $data->kodebon : $nomor ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Second Row -->
                            <!-- <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Kas/Bank</label>
                                            <div class="col-md-9">
                                                <select id="kasbank" name="kasbank" class="form-select input-medium select2" data-placeholder="Pilih..." onkeypress="return tabE(this,event)" required>
                                                    <option value="" class="">---- pilih Akun----</option>
                                                    <?php
                                                    $akun = $db->table('ms_akun')->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult();
                                                    foreach ($akun as $key => $value) { ?>
                                                        <option value="<?= $value->kodeakun ?>" <?php if ($data != [] && $value->kodeakun == $data->kodebank) {
                                                                                                    echo "selected=true";
                                                                                                } ?>><?= $value->namaakun ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">No Rek</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control input-medium" name="norek" id="norek" value="<?= ($data != []) ? $data->norek : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Nama Rek</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control input-medium" name="namarek" id="namarek" value="<?= ($data != []) ? $data->namarek : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <!-- Third Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="col-md-3 control-label pt-2">Nilai Pembayaran</label>
                                            <div class="col-md-6">
                                                <div class="input-group position-relative">
                                                    <input type="text" class="form-control input-medium rightJustified" name="jumlahbayar" id="jumlahbayar" value="<?= ($data != []) ? number_format($data->nominal) : '0' ?>" readonly>
                                                    <span class="input-group-btn start-100 top-0">
                                                        <a class="btn green" onclick="total()"><i class="fa fa-refresh"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl kwitansi</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tanggal)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- fifth Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center">No. Faktur</th>
                                                    <th style="text-align: center">Tgl. Faktur</th>
                                                    <th style="text-align: center">Total Faktur</th>
                                                    <th style="text-align: center">Terhutang</th>
                                                    <th style="text-align: center">No PO</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($DetailItem != []) {
                                                    $no = 0;
                                                    foreach ($DetailItem as $value) {
                                                        $no++;
                                                ?>
                                                        <tr>
                                                            <td><input name="checkbox[]" type="checkbox" id="checkboxes<?=$no?>" value="<?= $value->nomorfaktur; ?>" onchange="getvalue(this,<?=$no?>)" checked></td>
                                                            <td><input name="faktur[]" id="faktur<?=$no?>" type="text" class="form-control input-medium" value="<?= $value->nomorfaktur; ?>" readonly></td>
                                                            <td><input name="tglfaktur[]" id="tglfaktur<?=$no?>" type="text" class="form-control " value="<?= date('d-m-Y', strtotime($value->tglfaktur)) ?>" readonly></td>
                                                            <td><input name="totfaktur[]" id="totfaktur<?=$no?>" type="text" class="form-control " value="<?= number_format($value->totalfaktur) ?>" readonly></td>
                                                            <td><input name="hutang[]" id="hutang<?=$no?>" type="text" class="form-control " value="<?= number_format($value->terhutang) ?>" readonly></td>
                                                            <td><input name="nopo[]" value="<?= $value->nopo ?>" id="nopo<?=$no?>" type="text" class="form-control input-small"></td>
                                                        </tr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                // totalline(<?= $no ?>)
                                                            });
                                                        </script>
                                                    <?php }
                                                } else { $no = 0;?>
                                                    <tr>
                                                        <td><input name="checkbox[]" type="checkbox" id="checkboxes1" value="0" onchange="getvalue(this,1)"></td>
                                                        <td><input name="faktur[]" id="faktur1" type="text" class="form-control input-medium" onkeypress="return tabE(this,event)" readonly></td>
                                                        <td><input name="tglfaktur[]" id="tglfaktur1" type="text" class="form-control input-small" onkeypress="return tabE(this,event)" readonly></td>
                                                        <td><input name="totfaktur[]" id="totfaktur1" type="text" class="form-control input-medium rightJustified" readonly></td>
                                                        <td><input name="hutang[]" id="hutang1" type="text" class="form-control rightJustified input-medium" readonly></td>
                                                        <td><input name="nopo[]"  id="nopo1" type="text" class="form-control rightJustified input-medium"></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- tab1-->
                </div>
                <!--tab-->
                <div class="row">
                    <div class="col-md-8">
                        <div class="wells">
                            <button type="button" onclick="save()" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                            <?php if ($data != []) { ?>
                                <a class="btn yellow print_laporan" onclick="javascript:window.open(_urlcetak('<?= $data->kodebon ?>'),'_blank');"><i class="fa fa-print"></i> Cetak</a>
                            <?php } ?>
                            <div class="btn-group">
                                <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                            </div>
                            <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var idrow = 2;
    var id = $('[name=id]').val();

    function tambah() {
        var table = document.getElementById("datatable");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var td6 = x.insertCell(5);
        var td7 = x.insertCell(6);
        if(id !=0){
            td1.innerHTML = "<input name='checkbox[]'    id=checkboxes"+idrowx+" type='checkbox' value='0' onclick='getvalue(this,"+idrowx+")' >";
            td2.innerHTML = "<input name='faktur[]'      id=faktur" + idrowx + " type='text' class='form-control'  readonly>";
            td3.innerHTML = "<input name='tglfaktur[]'   id=tglfaktur" + idrowx + " type='text' class='form-control'  readonly>";
            td4.innerHTML = "<input name='totfaktur[]'   id=totfaktur" + idrowx + "  value='1'  type='text' class='form-control rightJustified'  readonly>";
            td5.innerHTML = "<input name='hutang[]'      id=hutang" + idrowx + " type='text' class='form-control rightJustified' readonly >";
            td6.innerHTML = "<input name='nopo[]'       id=nopo" + idrowx + "  type='text' class='form-control rightJustified' onclick='formatCurrency(this)'>";
        }else{
            td1.innerHTML = "<input name='checkbox[]'    id=checkboxes"+idrow+" type='checkbox' value='0' onclick='getvalue(this,"+idrow+")' >";
            td2.innerHTML = "<input name='faktur[]'      id=faktur" + idrow + " type='text' class='form-control'  readonly>";
            td3.innerHTML = "<input name='tglfaktur[]'   id=tglfaktur" + idrow + " type='text' class='form-control'  readonly>";
            td4.innerHTML = "<input name='totfaktur[]'   id=totfaktur" + idrow + "  value='1'  type='text' class='form-control rightJustified'  readonly>";
            td5.innerHTML = "<input name='hutang[]'      id=hutang" + idrow + " type='text' class='form-control rightJustified' readonly >";
            td6.innerHTML = "<input name='nopo[]'       id=nopo" + idrow + "  type='text' class='form-control rightJustified' onclick='formatCurrency(this)'>";
        }
        
        idrow++;
    }

    function getfaktur() {
        var xhttp;
        var str = $('[name=cust]').val();
        var kode = $('[name=nomorbukti]').val();
        no = <?=$no?>;
        if (str != "") {
                url = "<?= base_url('penjualan/transaksi/kontrabon/getfaktur/') ?>" + str;
            $.ajax({
                url: url,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {
                            if(id !=0){
                                
                                tambah();
                                x = no + 1;
                                document.getElementById("faktur" + x).value = data[i].kodesi;
                                document.getElementById("tglfaktur" + x).value = data[i].tglsi;
                                document.getElementById("totfaktur" + x).value = number_format(data[i].total);
                                document.getElementById("hutang" + x).value = number_format(data[i].hutang);
                                document.getElementById("nopo" + x).value = data[i].nopo;
                                no++;
                            }else{
                                if (i > 0) {
                                    tambah();
                                }
                                x = i + 1;
                                document.getElementById("faktur" + x).value = data[i].kodesi;
                                document.getElementById("tglfaktur" + x).value = data[i].tglsi;
                                document.getElementById("totfaktur" + x).value = number_format(data[i].total);
                                document.getElementById("hutang" + x).value = number_format(data[i].hutang);
                                document.getElementById("nopo" + x).value = data[i].nopo;
                            }
                    }

                }
            });
        }
    }

    function getvalue(check,x){
        var faktur = $('#faktur'+ x).val();
        if (check.checked) {
            document.getElementById("checkboxes" + x).value = faktur;
        } else {
            document.getElementById("checkboxes" + x).value = 0;
        }
        total();
    }

    function total() {
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        tjumlah = 0;
        // tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];
            check = row.cells[0].children[0].value;
            jumlah = row.cells[4].children[0].value;
            var jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
            if(check != "0"){
                tjumlah = tjumlah + eval(jumlah1);
            }
        }
        document.getElementById("jumlahbayar").value = number_format(tjumlah);
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('KONTRA BON', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('KONTRA BON', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {

        var noform = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var jumlahbayar = $('[name="jumlahbayar"]').val();
        var metodebyr = $('[name="metodebayar"]').val();
        if (jumlahbayar == "0" || jumlahbayar == "0.00" || metodebyr == "") {
            swal('KONTRA BON', 'Jumlah pembayaran / metode bayar belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/kontrabon/save') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    var parse = JSON.parse(data);
                    $('#process').css('display', 'none');
                    if (parse.status == 1) {
                        swal({
                            title: "KONTRA BON",
                            html: "<p> No. Bukti   : <b>" + parse.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.open(_urlcetak(parse.message));
                            window.location.href = "<?= base_url('penjualan/transaksi/kontrabon') ?>";
                        });
                    } else {
                        swal('KONTRA BON', 'Data gagal disimpan ... ' + parse.message, '');
                    }

                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('KONTRA BON', 'Data gagal disimpan...', '');
                }
            });
        }
    }

    function _urlcetak(data) {
        var baseurl = "<?php echo base_url() ?>";
        return baseurl + 'penjualan/transaksi/cetakkontrabon/' + data;
    }
</script>