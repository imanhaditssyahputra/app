<div class="container-fluid">
    <?= main_header('Keuangan ', 'Transfer Kas/Bank', 'Daftar Transfer Kas/Bank  ', 'Entri Transfer Kas/Bank') ?>


    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i>Form Entri
            </div>
            <div class="tools">
            </div>
        </div>
        <?php
        $db = db_connect();
        if (!empty($id)) {
            $data = $db->table('transfer_bank')->where('id', $id)->get()->getRow();
        }

        ?>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form id="submit_form">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-body">
                    <h3>Deskripsi</h3>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Nomor Bukti</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Nomor Otomatis" readonly name="nomorbukti" id="nomorbukti" value="KT.2023.05.000001" onkeypress="return tabE(this,event)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Tanggal</label>
                                    <div class="col-md-6">
                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= isset($data) ? date('Y-m-d', strtotime($data->date)) : date('Y-m-d') ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <?php
                                    $from_kas = ['Kas Kecil', 'Bank', 'Bank BJB', 'Deposito Bank', 'BRI', 'New Account'];
                                    ?>
                                    <label class="col-md-3 control-label">Dari Kas/Bank</label>
                                    <div class="col-md-9">
                                        <select id="Pembayaran1" name="from_bank" class="form-select input-medium select2me" data-placeholder="Pilih...">
                                            <option value="">Select</option>
                                            <option value="Kas Kecil">Kas Kecil</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Bank BJB">Bank BJB</option>
                                            <option value="">Deposito Bank</option>
                                            <option value="Deposito Bank">BRI</option>
                                            <option value="New Account">New Account</option>
                                        </select>
                                    </div>
                                    <span id="PembayaranErr" style='width:89%;text-align:center;'></span>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Keterangan</label>
                                    <div class="col-md-6">
                                        <textarea name="information" class="form-control" value="" id="information" cols="20" rows="2"><?= isset($data) ? $data->information : '' ?></textarea>
                                    </div>
                                    <span id="informationErr" style='width:89%;text-align:center;'></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">KE KAS/BANK</label>
                                    <div class="col-md-9">
                                        <select id="Pembayaran" name="kas_bank" class="form-select input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <option value="">Select</option>
                                            <option value="Kas Kecil">Kas Kecil</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Bank BJB">Bank BJB</option>
                                            <option value="">Deposito Bank</option>
                                            <option value="Deposito Bank">BRI</option>
                                            <option value="New Account">New Account</option>
                                            </ /?php $db=db_connect(); $barang=$db->table('tbl_kode_akun')->get()->getResult();
                                            foreach ($barang as $value) { ?>
                                            </ /option value="<?= $value->nama_akun ?>" <?= ($data->from_bank == $value->nama_akun ? "selected" : "") ?>><?= $value->nama_akun  ?></option>


                                            </ /?php } ?>
                                        </select>
                                    </div>
                                    <span id="kasBankErr" style='width:89%;text-align:center;'></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Nilai Transfer</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control input-large" placeholder="" name="transfer_value" id="nilai_transfer" value="<?= isset($data) ? $data->transfer_value : '' ?>" onkeypress="return tabE(this,event)">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>



                    <div class="form-actions">
                        <button type="submit" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                        <div class="btn-group">
                            <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                        </div>
                    </div>
                    <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
            </form>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $("#submit_form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: "<?= base_url('kasbank/transaksi/transfer-bank-add') ?>",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.error) {
                        if (res.nomorbuktierr) {
                            $('#PembayaranErr').html(res.nomorbuktierr);
                        } else {
                            $('#PembayaranErr').html('');
                        }
                        if (res.informationerr) {
                            $('#informationErr').html(res.informationerr);
                        } else {
                            $('#informationErr').html('');
                        }
                        if (res.kas_bankerr) {
                            $('#kasBankErr').html(res.kas_bankerr);
                        } else {
                            $('#kasBankErr').html('');
                        }

                    } else {
                        table_reload('table');
                        // $('#tambahDataModal').modal('hide');
                        window.location.href = '<?= base_url('kasbank/transaksi/transferbank') ?>';
                    }
                }
            });
        });
    });
</script>