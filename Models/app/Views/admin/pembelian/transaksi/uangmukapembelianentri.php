<?php echo main_header('Pembelian ', 'Uang Muka Pembelian', ' Daftar Uang Muka Pembelian', 'Entri Data', 'pembelian/transaksi/uangmukapembelian'); ?>
<?php
$db = db_connect();
if (!empty($id)) {
    $table_data = $db->table('ap_uangmuka')->where('idum', $id)->get()->getRow();
    $table_dataD = $db->table('ap_pofile')->where('kodepo', $table_data->kodepo);
    $table_data_detail = $table_dataD->get()->getResult();
    $table_dataDRow = $table_dataD->get()->getRow();
}
isset($table_data) ? main_header('Pembelian ', 'Uang Muka Pembelian', ' Daftar Uang Muka Pembelian', 'Edit Data') : main_header('Pembelian ', 'Uang Muka Pembelian', ' Daftar Uang Muka Pembelian', 'Entri Data')
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
            <i class="fa fa-reorder"></i>*Data Baru
        </div>

    </div>

    <div class="portlet-body form">
        <form id="frmpembelian" class="form-horizontal" method="post">
            <input type="hidden" name="id" value="<?= isset($table_data) ? $table_data->idum : 0 ?>">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Supplier</label>
                                <div class="col-md-6">
                                    <div class="input-group position-relative">
                                        <select id="supp" name="supp" class="form-select input-medium select2me" data-placeholder="Pilih..." required>
                                            <option value="" class="">---- pilih supplier----</option>
                                            <?php
                                            $suplier = $db->table('ap_supplier')->get()->getResult();
                                            foreach ($suplier as $key => $value) { ?>
                                                <option value="<?= $value->kode ?>" <?php if (isset($table_data) && $value->kode == $table_data->kodesup) {
                                                                                        echo "selected=true";
                                                                                    } ?>><?= $value->nama ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="input-group-btn position-absolute start-100 top-0">
                                            <a class="btn green" onclick="showpo()"><i class="fa fa-refresh"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Nomor #</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" placeholder="<?= isset($table_data) ? $table_data->kodeum : 'Nomor Auto' ?>" value="<?= isset($table_data) ? $table_data->kodeum : $nomor ?>" readonly>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Tanggal</label>
                                <div class="col-md-4">
                                    <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($table_data) ? date('Y-m-d', strtotime($table_data->tglum)) : date('Y-m-d') ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">No. Faktur</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="nomorfaktur" id="nomorfaktur" value="<?= isset($table_data) ? $table_data->nomorfaktur : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">No. PO</label>
                                <div class="col-md-6">
                                    <div class="input-group position-relative">
                                        <?php if (isset($table_data)) { ?>
                                            `<input type="text" name="kodepo" id="kodepo" value="<?= isset($table_data) ? $table_data->kodepo : '' ?>" class="form-control input-medium" readonly>
                                            <?php } else { ?>`
                                            <select id="kodepo" name="kodepo" class="form-control input-medium select2">
                                                <option value="">---Tanpa PO---</option>
                                            </select>
                                            <span class="input-group-btn position-absolute start-100 top-0">
                                                <a class="btn green" onclick="getpoheader();"><i class="fa fa-refresh"></i></a>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Jumlah PO</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="jumlahpo" id="jumlahpo" value="<?= isset($table_data) ? $table_data->jumlahpo : '0' ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h4><b><i>Pembayaran</i></b></h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Uang Muka</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-medium" placeholder="" name="uangmuka" id="uangmuka" value="<?= isset($table_data) ? $table_data->jumlahum : '0' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Keterangan</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="keterangan" id="keterangan" value="<?= isset($table_data) ? $table_data->keterangan : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Kas/Bank</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select id="kasbank" name="kasbank" class="form-control input-medium selectakun" data-placeholder="Pilih...">
                                            <option value="" class="">---- pilih Akun----</option>
                                            <?php
                                            $akun = $db->table('ms_akun')->getWhere(['akuninduk !=' => '', 'kelompok' => 'BANK'])->getResult();
                                            foreach ($akun as $key => $value) { ?>
                                                <option value="<?= $value->kodeakun ?>" <?php if (isset($table_data) && $value->kodeakun == $table_data->kodebank) {
                                                                                            echo "selected=true";
                                                                                        } ?>><?= $value->namaakun ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Rek. Tujuan</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="norek" id="norek" value="<?= isset($table_data) ? $table_data->norektujuan : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="save()" id="save_btn" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-plus"></i> Data Baru</button>
                </div>
        </form>
    </div>
</div>
<script>
    //show data PO Pembelian
    function showpo() {
        var xhttp;
        var str = $('[name="supp"]').val();

        if (str == "") {
            document.getElementById("kodepo").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodepo").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('pembelian/transaksi/uangmukapembelian/getlistpo/') ?>" + str, true);
        xhttp.send();
    }

    function getpoheader() {
        var xhttp;
        var str = $('[name=kodepo]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/uangmukapembelian/postpofile/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    total = parseInt(data.dpp) + parseInt(data.ppn) - parseInt(data.diskon) + parseInt(data.biayalain);
                    $('[name="jumlahpo"]').val(total);
                }
            });
        }
    }

    $("#save_btn").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('UANG MUKA PEMBELIAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('UANG MUKA PEMBELIAN', 'Gunakan Tab untuk Next dan Mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var nofaktur = $('[name="nomorfaktur"]').val();
        var tanggal = $('[name="tanggal"]').val();
        if (nofaktur == "") {
            swal('PEMBELIAN', 'Nomor Faktur belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('pembelian/transaksi/uangmukapembeliansave') ?>",
                data: $('#frmpembelian').serialize(),
                type: 'post',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    swal({
                        title: "PEMBELIAN UANG MUKA",
                        html: "<p> No. BUKTI   : <b>" + data + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('pembelian/transaksi/uangmukapembelian') ?>";
                    });

                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PEMBELIAN UANG MUKA', 'Data gagal disimpan ...', '');

                }
            });
        }
    }
</script>