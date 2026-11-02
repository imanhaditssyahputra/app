<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_bayar')->where('idbayar', $id)->get()->getRow();
    $table_dataD = $db->table('ap_bayardetil')->where('kodebyr', $table_data->kodebyr);
    $table_data_detail = $table_dataD->get()->getResult();
    $table_dataDRow = $table_dataD->get()->getRow();
}
isset($table_data) ? main_header('Pembelian', 'Pembayaran', ' Daftar Pembayaran Pembelian ', 'View Pembayaran', 'pembelian/transaksi/pembayaranpembelian') : main_header('Pembelian', 'Pembayaran', ' Daftar Pembayaran Pembelian ', 'Entri Pembayaran', 'pembelian/transaksi/pembayaranpembelian')
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
            <i class="fa fa-reorder"></i><?php if (isset($table_data)) {
                                                echo 'Data View';
                                            } else {
                                                echo '*Data Baru';
                                            }  ?>
        </div>
    </div>

    <div class="portlet-body form">
        <form id="frmpembelian" class="form-horizontal" method="post">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->kodebyr : 0 ?>">
            <div class="form-body">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-bs-toggle="tab">
                                Pembayaran
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active fade show" id="tab1">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Pembayaran Ke</label>
                                            <div class="col-md-9">
                                                <select id="supp" name="supp" class="form-control input-medium select2" data-placeholder="Pilih...">
                                                    <option value="" class="">---- pilih supplier----</option>
                                                    <?php
                                                    if (isset($table_data)) {
                                                        $suplier = $db->table('ap_supplier')->getWhere(['kode' => $table_data->kodesup])->getResult();
                                                        foreach ($suplier as $key => $value) { ?>
                                                            <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                                    echo "selected=true";
                                                                                                } ?>><?= $value->nama ?></option>
                                                        <?php   }
                                                    } else {
                                                        $suplier = $db->query("SELECT * FROM ap_supplier a 
                                                                INNER JOIN ap_pufile b ON a.kode = b.kodesup WHERE b.statusid=1 GROUP BY b.kodesup")->getResult();
                                                        foreach ($suplier as $key => $value) {
                                                        ?>
                                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
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
                                                <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="<?= isset($table_data) ? $table_data->kodebyr : $nomor ?>" readonly>
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
                                            <label class="control-label pt-2 col-md-3">Kas/Bank</label>
                                            <div class="col-md-9">
                                                <select id="kasbank" name="kasbank" class="form-control input-medium select2" data-placeholder="Pilih..." required>
                                                    <option value="" class="">---- pilih Akun----</option>
                                                    <?php
                                                    $akun = $db->table('ms_akun')->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult();
                                                    foreach ($akun as $key => $value) {
                                                        if ($value->id != 6 && $value->id != 7 && $value->id != 97) {
                                                    ?>
                                                            <option value="<?= $value->kodeakun ?>" <?php if (isset($table_data) && $value->kodeakun == $table_data->kodebank) {
                                                                                                        echo "selected=true";
                                                                                                    } ?>><?= $value->namaakun ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Bayar</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglbayar)) : date('Y-m-d') ?>" />
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
                                            <label class="control-label pt-2 col-md-3">Metode Bayar</label>
                                            <div class="col-md-3">
                                                <select id="metodebayar" name="metodebayar" class="form-control input-small select2" data-placeholder="Pilih..." required>
                                                    <?php if (isset($table_data)) { ?>
                                                        <option value="<?= $table_data->metodebayar ?>">
                                                            <?php if ($table_data->metodebayar == 'T') {
                                                                echo 'Tunai';
                                                            } elseif ($table_data->metodebayar == 'C') {
                                                                echo 'Cek/Giro';
                                                            } else {
                                                                echo 'Transfer Bank';
                                                            } ?>
                                                        </option>
                                                    <?php } else { ?>
                                                        <option value="">Pilih</option>
                                                        <option value="T">Tunai</option>
                                                        <option value="C">Cek/Giro</option>
                                                        <option value="F">Transfer Bank</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="hidden" class="form-control input-small" name="nomorcek" id="nomorcek" value="<?= isset($table_data) ? $table_data->nomorcek : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal Cek</label>
                                            <div class="col-md-9">
                                                <input id="tanggalcek" name="tanggalcek" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglbayar)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Four Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Nilai Pembayaran</label>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control input-medium rightJustified" name="jumlahbayar" id="jumlahbayar" value="<?= isset($table_data) ? $table_data->jumlahbayar : '0' ?>" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn green" onclick="total()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Keterangan</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control input-large" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Five Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center">No. Faktur</th>
                                                    <th style="text-align: center">Tgl. Faktur</th>
                                                    <th style="text-align: center">Total Faktur</th>
                                                    <th style="text-align: center">Terhutang</th>
                                                    <th style="text-align: center">Uang Muka</th>
                                                    <th style="text-align: center">Bayar</th>
                                                    <th style="text-align: center">Diskon</th>
                                                    <th style="text-align: center">Pembayaran</th>
                                                </tr>
                                                <thead>
                                                <tbody>
                                                    <?php
                                                    if (isset($table_data_detail)) {
                                                        $no = 0;
                                                        foreach ($table_data_detail as $value) {
                                                            $db = db_connect();
                                                            $no++;
                                                    ?>
                                                            <tr>
                                                                <td><input name="faktur[]" id="faktur<?= $no ?>" type="text" class="form-control input-medium" value="<?= $value->nomorfaktur; ?>" readonly></td>
                                                                <td><input name="tglfaktur[]" id="tglfaktur<?= $no ?>" type="text" class="form-control input-small" value="<?= date('d-m-Y', strtotime($value->tglfaktur)) ?>"></td>
                                                                <td><input name="totfaktur[]" id="totfaktur<?= $no ?>" type="text" class="form-control rightJustified input-small" value="<?= number_format($value->totalfaktur) ?>" readonly></td>
                                                                <td><input name="hutang[]" id="hutang<?= $no ?>" type="text" class="form-control rightJustified input-small" value="<?= number_format($value->terhutang) ?>" readonly></td>
                                                                <td><input name="uangmuka[]" id="uangmuka<?= $no ?>" type="text" class="form-control rightJustified input-small" value="<?= number_format($value->uangmuka) ?>" readonly></td>
                                                                <td><input name="bayar[]" onchange="totalline(<?= $no ?>)" value="<?= number_format($value->bayar) ?>" id="bayar<?= $no ?>" type="text" class="form-control rightJustified input-small"></td>
                                                                <td><input name="disc[]" onchange="totalline(<?= $no ?>)" value="<?= number_format($value->diskon) ?>" id="disc<?= $no ?>" type="text" class="form-control rightJustified input-small"></td>
                                                                <td><input name="jumlah[]" id="jumlah<?= $no ?>" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            </tr>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    totalline(<?= $no ?>)
                                                                });
                                                            </script>
                                                        <?php }
                                                    } else {
                                                        ?>
                                                        <tr>
                                                            <td><input name="faktur[]" id="faktur1" type="text" class="form-control input-medium" readonly></td>
                                                            <td><input name="tglfaktur[]" id="tglfaktur1" type="text" class="form-control input-small" readonly></td>
                                                            <td><input name="totfaktur[]" id="totfaktur1" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            <td><input name="hutang[]" id="hutang1" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            <td><input name="uangmuka[]" id="uangmuka1" type="text" class="form-control rightJustified input-small" readonly></td>
                                                            <td><input name="bayar[]" onchange="totalline(1)" value="0" id="bayar1" type="text" class="form-control rightJustified input-small"></td>
                                                            <td><input name="disc[]" onchange="totalline(1)" value="0" id="disc1" type="text" class="form-control rightJustified input-small"></td>
                                                            <td><input name="jumlah[]" id="jumlah1" type="text" class="form-control rightJustified input-small" onchange="total()" readonly></td>
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
                            <?php if (isset($table_data)) { ?>
                                <a class="btn yellow print_laporan" onclick="javascript:window.open(_urlcetak(),'_blank');"><i class="fa fa-print"></i> Cetak</a>
                            <?php } ?>
                            <div class="btn-group">
                                <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                            </div>
                            <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                        </div>
                    </div>

                    <div class="col-md-4 invoice-block">
                        <div class="well">
                            <table>
                                <tr>
                                    <td width="40%"><strong>SUB TOTAL</strong></td>
                                    <td width="1%"><strong>:</strong></td>
                                    <td width="59" align="right"><strong><span id="_vsubtotal"></span></strong></td>
                                </tr>
                                <tr>
                                    <td width="40%"><strong>DISKON</strong></td>
                                    <td width="1%"><strong>:</strong></td>
                                    <td width="59" align="right"><strong><span id="_vdiskon"></span></strong></td>
                                </tr>
                                <tr>
                                    <td width="40%"><strong>TOTAL</strong></td>
                                    <td width="1%"><strong>:</strong></td>
                                    <td width="59" align="right"><strong><span id="_vtotal"></span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        initailizeSelect2_supp();
    });

    var idrow = 2;

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
        var td8 = x.insertCell(7);

        td1.innerHTML = "<input name='faktur[]'      id=faktur" + idrow + " type='text' class='form-control'  readonly>";
        td2.innerHTML = "<input name='tglfaktur[]'   id=tglfaktur" + idrow + " type='text' class='form-control'  readonly>";
        td3.innerHTML = "<input name='totfaktur[]'   id=totfaktur" + idrow + " onchange='totalline(" + idrow + ")' value='1'  type='text' class='form-control rightJustified'  readonly>";
        td4.innerHTML = "<input name='hutang[]'      id=hutang" + idrow + " type='text' class='form-control rightJustified' readonly >";
        td5.innerHTML = "<input name='uangmuka[]'    id=uangmuka" + idrow + " type='text' class='form-control rightJustified' readonly >";
        td6.innerHTML = "<input name='bayar[]'       id=bayar" + idrow + " onchange='totalline(" + idrow + ")'  type='text' class='form-control rightJustified' onclick='formatCurrency(this)'>";
        td7.innerHTML = "<input name='disc[]'        id=disc" + idrow + " onchange='totalline(" + idrow + ")' type='text' class='form-control rightJustified' onclick='formatCurrency(this)'>";
        td8.innerHTML = "<input name='jumlah[]'      id=jumlah" + idrow + " type='text' class='form-control rightJustified' size='40%' readonly>";

        idrow++;
    }

    //Delete Table Element Data Biaya lain - lain   
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("datatable").deleteRow(i);
    }

    function getfaktur() {
        var xhttp;
        var str = $('[name=supp]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/pembayaranpembelian/postfaktur/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }
                        x = i + 1;
                        document.getElementById("faktur" + x).value = data[i].kodepu;
                        document.getElementById("tglfaktur" + x).value = data[i].tglpu;
                        document.getElementById("totfaktur" + x).value = number_format(data[i].total);
                        document.getElementById("hutang" + x).value = number_format(data[i].hutang);
                        document.getElementById("uangmuka" + x).value = number_format(data[i].uangmuka);
                        document.getElementById("bayar" + x).value = 0;
                        document.getElementById("disc" + x).value = 0;
                        totalline(x);
                    }

                }
            });
        }
    }


    function total() {

        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            jumlah = row.cells[5].children[0].value;
            diskon = row.cells[6].children[0].value;
            var jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
            var diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));

            tjumlah = tjumlah + eval(jumlah1);
            tdiskon = tdiskon + eval(diskon1);
        }

        document.getElementById("_vsubtotal").innerHTML = number_format(tjumlah);
        document.getElementById("_vdiskon").innerHTML = number_format(tdiskon);
        document.getElementById("_vtotal").innerHTML = number_format(tjumlah - tdiskon);
        document.getElementById("jumlahbayar").value = number_format(tjumlah - tdiskon);
    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        uangmuka = row.cells[4].children[0].value;
        jumlah = row.cells[5].children[0].value;
        diskon = row.cells[6].children[0].value;

        hutang = row.cells[3].children[0].value;

        var jumlah1 = Number(jumlah.replace(/[^0-9\.]+/g, ""));
        var diskon1 = Number(diskon.replace(/[^0-9\.]+/g, ""));
        var hutang1 = Number(hutang.replace(/[^0-9\.]+/g, ""));
        var uangmuka1 = Number(uangmuka.replace(/[^0-9\.]+/g, ""));

        if ((jumlah1 + uangmuka1) > hutang1 + uangmuka1) {
            swal('PEMBELIAN', 'Jumlah pembayaran melebihi Hutang ...', '');
            row.cells[5].children[0].value = 0;
        }


        tot = eval(jumlah1) - eval(diskon1);
        row.cells[7].children[0].value = number_format(tot);
        total();

    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PEMBAYARAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PEMBAYARAN PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var kas = $('[name="kasbank"]').val();
        var metode = $('[name="metodebayar"]').val();
        var jumlahbayar = $('[name="jumlahbayar"]').val();
        if (jumlahbayar == "0" || jumlahbayar == "0.00") {
            swal('PEMBELIAN', 'Jumlah pembayaran belum diisi ...', '');
        } else if (kas == "" || metode == "") {
            swal('PEMBAYARAN PEMBELIAN', 'Kas/Bank atau meode pembayaran belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/pembayaranpembeliansave') ?>",
                data: $('#frmpembelian').serialize(),
                type: 'post',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    var parse = JSON.parse(data);
                    if (parse.status == 1) {
                        swal({
                            title: "PEMBAYARAN PEMBELIAN",
                            html: "<p> No. Bukti   : <b>" + parse.message + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('pembelian/transaksi/pembayaranpembelian') ?>";
                        });
                    } else {
                        swal('PEMBAYARAN PEMBELIAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }


                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PEMBAYARAN PEMBELIAN', 'Data gagal disimpan ...', '');


                }
            });
        }
    }

    function _urlcetak() {
        var baseurl = "<?php echo base_url() ?>";
        var param = $('[name="nomorbukti"]').val();
        return baseurl + 'pembelian/transaksi/cetakbayarnpo/' + param;
    }
</script>