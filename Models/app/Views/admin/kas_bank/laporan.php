<div class="container-fluid">
    <?= main_header('Keuangan ', 'Laporan', 'Kas & Bank', 'Laporan') ?>
    <div class="note note-success">
        <p>
            Laporan - laporan untuk transaksi Kas &amp; Bank
            <br>
        </p>
    </div>
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i>Parameter Laporan
            </div>

        </div>

        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form id="frmkeuangan" class="form-horizontal" method="post">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Nomor Bukti</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Nomor Otomatis" readonly name="nomorbukti" id="nomorbukti" value="" onkeypress="return tabE(this,event)">
                                        </div>
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
                                    <label class="col-md-6 control-label">Tanggal</label>
                                    <div class="col-md-6">
                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="2023-05-04" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">s/d</label>
                                    <div class="col-md-6">
                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="2023-05-04" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Kas & Bank</label>
                                    <div class="col-md-9">
                                        <select id="Pembayaran" name="Pembayaran" class="form-select input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <option value="110101">Kas Kecil</option>
                                            <option value="110102">Bank</option>
                                            <option value="110103">Bank BJB</option>
                                            <option value="110201">Deposito Bank</option>
                                            <option value="110202">BRI</option>
                                            <option value="110104">New Account</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Cabang</label>
                                    <div class="col-md-9">
                                        <select id="Pembayaran" name="Pembayaran" class="form-select input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <option value="110101">Kas Kecil</option>
                                            <option value="110102">Bank</option>
                                            <option value="110103">Bank BJB</option>
                                            <option value="110201">Deposito Bank</option>
                                            <option value="110202">BRI</option>
                                            <option value="110104">New Account</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="save()" class="btn blue"> Tampilkan</button>

                    </div>
                    <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
            </form>
        </div>
    </div>
</div>