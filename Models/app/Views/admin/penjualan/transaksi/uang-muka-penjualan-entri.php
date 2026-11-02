<?= main_header('Penjualan', 'Uang Muka Penjualan', 'Daftar Uang Muka Penjualan', 'Entri Data',  'penjualan/transaksi/uangmukapenjualan') ?>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>*Data Baru
        </div>
        <!--div class="tools">
						 <span class="label label-sm label-danger">										
						  REGISTER : 
						</span>

					</div-->

    </div>

    <div class="portlet-body form">
        <form id="frmpembelian" class="form-horizontal" method="post">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Pelanggan</label>
                                <div class="col-md-6">
                                    <div class="input-group position-relative">
                                        <select id="cust" name="cust" class="form-select input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <option>---Pilih Customer</option>
                                            <option value="CS.000025">RSU KASIH BUNDA</option>
                                            <option value="CS.000006">RS HERMINA ARCAMANIK</option>
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
                                <label class="col-md-3 control-label pt-2">No. Faktur #</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" name="nomorbukti" id="nomorbukti" value="SM.2023.04.000001" readonly>

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
                                    <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="2023-04-18" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">No. SO</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div id="listpo"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">Jumlah SO</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="jumlahso" id="jumlahso" value="0" readonly>
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
                                        <input type="text" class="form-control input-medium" placeholder="" name="uangmuka" id="uangmuka" value="0">
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
                                    <input type="text" class="form-control input-medium" placeholder="" name="keterangan" id="keterangan" value="">
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
                                        <select id="kasbank" name="kasbank" class="form-select input-medium select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
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
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="row">
                                <label class="col-md-3 control-label pt-2">No. Kartu</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control input-medium" placeholder="" name="nokartu" id="nokartu" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                    <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-plus"></i> Data Baru</button>
                </div>
            </div>
        </form>
    </div>
</div>