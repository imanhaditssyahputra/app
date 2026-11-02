<div class="container-fluid">
    <?= main_header('Akuntansi ', 'Tutup Buku') ?>
    <div class="note note-success">
        <p>
            Modul ini berfungsi untuk Proses Tutup Buku Periode Berjalan.<br>
            Pastikan Transaksi sudah selesai sebelum proses ini.
            <br>
        </p>
    </div>

    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i>Parameter Proses
            </div>

        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form id="frmtutupbuku" class="form-horizontal form-bordered1" method="post">
                <div class="form-body">
                    <div class="row form-group mb-3">
                        <label class="control-label col-md-3"> Bulan / Tahun Tutup:</label>
                        <div class="col-md-3">
                            <select id="blntutup" name="blntutup" class="form-select input-sm select2me input-medium" data-placeholder="Pilih..." disabled>
                                <option value="NONE">&nbsp</option>
                                <option value="1">JANUARI</option>
                                <option value="2">PEBRUARI</option>
                                <option value="3">MARET</option>
                                <option value="4">APRIL</option>
                                <option value="5" selected=true>MEI</option>
                                <option value="6">JUNI</option>
                                <option value="7">JULI</option>
                                <option value="8">AGUSTUS</option>
                                <option value="9">SEPTEMBER</option>
                                <option value="10">OKTOBER</option>
                                <option value="11">NOPEMBER</option>
                                <option value="12">DESEMBER</option>

                            </select>

                        </div>
                        <div class="col-md-2">
                            <input maxlength="4" type="text" size="3" name="thntutup" id="thntutup" class="form-control" value="2023" disabled />
                        </div>

                    </div>

                    <div class="row form-group mb-3">
                        <label class="control-label col-md-3"> Bulan / Tahun Dibuka :</label>
                        <div class="col-md-3">
                            <select id="blnbuka" name="blnbuka" class="form-select input-sm select2me input-medium" data-placeholder="Pilih..." disabled>
                                <option value="NONE">&nbsp</option>
                                <option value="1">JANUARI</option>
                                <option value="2">PEBRUARI</option>
                                <option value="3">MARET</option>
                                <option value="4">APRIL</option>
                                <option value="5">MEI</option>
                                <option value="6" selected=true>JUNI</option>
                                <option value="7">JULI</option>
                                <option value="8">AGUSTUS</option>
                                <option value="9">SEPTEMBER</option>
                                <option value="10">OKTOBER</option>
                                <option value="11">NOPEMBER</option>
                                <option value="12">DESEMBER</option>

                            </select>

                        </div>
                        <div class="col-md-2">
                            <input maxlength="4" type="text" size="5" name="thnbuka" id="thnbuka" class="form-control" value="2023" disabled />
                        </div>
                    </div>
                </div>
                <div class="row form-actions fluid">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                        <button type="button" class="btn blue" onclick="save()" id="btnproses">Tutup Buku</button>
                        <br />
                        <h4>
                            <div class="err" id="resultss"></div>
                        </h4>
                        <div>
                            <img id="proses" src="https://ajf.sistemkesehatan.id/assets/img/loading-spinner-blue.gif" class="img-responsive" style="visibility:hidden" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>