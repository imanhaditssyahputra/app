<?php
$db = db_connect();
if ($data != []) {
    main_header('Penjualan', 'Pengiriman Pesanan', ' Daftar Pengiriman Pesanan ', 'Edit Pengiriman Pesanan', 'penjualan/transaksi/pesanandisiapkan');
    $DataOrder = 'Data Edit';
    $CSRow = $db->table("ar_customer")->getWhere(['kode' => $data->kodecust])->getRow();
} else {
    main_header('Penjualan', 'Pengiriman Pesanan', ' Daftar Pengiriman Pesanan ', 'Entri Pengiriman Pesanan', 'penjualan/transaksi/pesanandisiapkan');
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
                            <button class="nav-link active fw-bold" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><i class="fa-solid fa-file"></i> Pengiriman</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><i class="fa-solid fa-info-circle"></i> Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="paketan-tab" data-bs-toggle="tab" data-bs-target="#paketan" type="button" role="tab" aria-controls="paketan" aria-selected="false"><i class="fa-solid fa-info-circle"></i> Barang Paket</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Kirim Ke</label>
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
                                                    <a class="btn green" onclick="showpo()"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Nomor #</label>
                                            <div class="col-md-7">
                                                <input type="hidden" class="form-control input-medium" name="nomorbukti" id="nomorbukti" readonly value="<?= ($data != []) ? $data->kodekirim : '' ?>">
                                                <input type="text" class="form-control input-medium" readonly placeholder="Nomor Auto" value="<?= ($data != []) ? $data->kodekirim : '' ?>">
                                                <input type="hidden" class="form-control input-medium" readonly placeholder="Nomor Auto" name="NoPBBeli" id="NoPBBeli" value="<?= ($data != []) ? $data->nolpb : $NoPBBeli ?>">
                                                <input type="hidden" class="form-control input-medium" readonly placeholder="Nomor Auto" name="NoPUBeli" id="NoPUBeli" value="<?= ($data != []) ? $data->kodepu : $NoPUBeli ?>">
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
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-6">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tglkirim)) : date('Y-m-d') ?>" />
                                                <input type="hidden" name="sppn" id="sppn" class="form-control input-medium" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">No. Pesanan</label>
                                            <div class="col-md-7">
                                                <select name="kodeso" id="kodeso" class="form-select input-medium select2">
                                                    <option value="">---Tanpa SO---</option>
                                                    <?php if ($data != []) { ?>
                                                        <option value="<?= $data->kodeso ?>" selected><?= $data->kodeso ?></option>
                                                    <?php } ?>
                                                </select>
                                                <span class="input-group-btn start-100 top-0">
                                                    <a class="btn blue" onclick="getso()"><i class="fa fa-download"></i></a>
                                                </span>
                                                <input id="no-po" name="no-po" class="form-control input-medium" type="hidden" value="<?= ($data != []) ? $DataSo->no_po : '' ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Third Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center">Kode/Nama Barang</th>
                                                    <th style="text-align: center">No Batch</th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center">Tanggal EXP</th>
                                                    <th style="text-align: center">qty</th>
                                                    <th style="text-align: center">Stock</th>
                                                    <th style="text-align: center">sat</th>
                                                    <th style="text-align: center">Detail Paket</th>
                                                    <th style="text-align: center"></th>
                                                </tr>
                                                <thead>

                                                <tbody>
                                                    <?php if ($DetailItem != []) {
                                                        $no = 0;
                                                        foreach ($DetailItem as $key) {
                                                            $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem' and kdcbg='$cabang'")->getRow();
                                                            $batch = $db->query("SELECT * FROM inv_nobatch where kodeitem='$key->kodeitem' and cabang='$cabang' and no_batch='$key->no_batch'")->getRow();
                                                            $Arsodetail = $db->query("SELECT * FROM ar_sodetail where kodeso='$data->kodeso' and kodeitem='$key->kodeitem' and cabang='$cabang'")->getRow();
                                                            $ArKirimPaket = $db->table('ar_kirimpaket')->getWhere(['kodekirim' => $data->kodekirim, 'kodeitem_barang' => $key->kodeitem])->getRow();
                                                            $no++;
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $no ?>" class="barang_filter_cabang form-select input-xlarge" onchange="showbarangname(this.value, <?= $no ?>)">
                                                                        <option value="<?= $key->kodeitem; ?>"><?= $barang->namabarang; ?></option>
                                                                    </select>
                                                                    <input name="idkode[]" value="<?= $Arsodetail->id; ?>" id="idkode<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                </td>
                                                                <td>
                                                                    <input name="no_batch[]" readonly="" value="<?= $key->no_batch; ?>" id="no_batch<?= $no ?>" type="text" class="form-control input-small rightJustified">
                                                                </td>
                                                                <td>
                                                                    <a class="btn default" id="lupharga<?= $no ?>" data-toggle="modal" onclick="getbatch(this.id)"><i class="fa fa-search"></i></a>
                                                                </td>
                                                                <td>
                                                                    <input name="tgl_exp[]" value="<?= $key->tgl_exp; ?>" id="tgl_exp<?= $no ?>" type="text" readonly class="form-control input-small rightJustified">
                                                                </td>
                                                                <td>
                                                                    <input name="qty[]" onchange="check_qty(this.value, <?= $no ?>)" oninput="totalline(<?= $no ?>)" value="<?= $key->qtykirim; ?>" id="qty<?= $no ?>" type="text" class="form-control input-xsmall rightJustified">
                                                                    <input name="qty_jual[]" value="<?= $batch->qty_saldo; ?>" id="qty_jual<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                    <input name="qtypesan[]" value="<?= $Arsodetail->qtyorder; ?>" id="qtypesan<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                </td>
                                                                <td>
                                                                    <input name="stock[]" oninput="totalline(<?= $no ?>)" value="<?= $barang->stok; ?>" id="stock<?= $no ?>" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                                    <input name="qtysisapesan[]" value="<?= $key->qtykirim; ?>" id="qtysisapesan<?= $no ?>" type="hidden" class="form-control rightJustified">
                                                                </td>
                                                                <td>
                                                                    <input name="sat[]" id="sat<?= $no ?>" type="text" class="form-control input-xsmall" value="<?= $key->satuan; ?>">
                                                                </td>
                                                                <td>
                                                                    <?php if ($ArKirimPaket) { ?>
                                                                        <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" disabled><i class="fa fa-search"></i></a>
                                                                        <input name="ifViewDetailPaket[]" id="ifViewDetailPaket<?= $no ?>" type="hidden" class="form-control input-xsmall" value="1">
                                                                    <?php } else { ?>
                                                                        <a class="btn default" id="ViewDetailPaket<?= $no ?>" data-toggle="modal" onclick="getPaket(this.id  )"><i class="fa fa-search"></i></a>
                                                                        <input name="ifViewDetailPaket[]" id="ifViewDetailPaket<?= $no ?>" type="hidden" class="form-control input-xsmall" value="1">
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                                </td>

                                                            </tr>
                                                        <?php  }
                                                    } else { ?>
                                                        <tr>
                                                            <td>
                                                                <select name="kode[]" id="kode1" class="barang_filter_cabang form-select input-xlarge" onchange="showbarangname(this.value, 1)">
                                                                    <option> ---- Pilih Barang---- </option>
                                                                </select>
                                                                <input name="idkode[]" value="" id="idkode1" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="no_batch[]" readonly="" value="" id="no_batch1" type="text" class="form-control rightJustified input-small">
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="lupharga1" data-toggle="modal" onclick="getbatch(this.id)"><i class="fa fa-search"></i></a>
                                                            </td>
                                                            <td>
                                                                <input name="tgl_exp[]" value="" id="tgl_exp1" type="text" readonly class="form-control rightJustified input-small">
                                                            </td>
                                                            <td>
                                                                <input name="qty[]" onchange="check_qty(this.value, 1)" oninput="totalline(1)" value="0" id="qty1" type="text" class="form-control input-xsmall rightJustified">
                                                                <input name="qty_jual[]" value="" id="qty_jual1" type="hidden" class="form-control rightJustified">
                                                                <input name="qtypesan[]" value="" id="qtypesan1" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="stock[]" oninput="totalline(1)" value="" id="stock1" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                                <input name="qtysisapesan[]" value="" id="qtysisapesan1" type="hidden" class="form-control rightJustified">
                                                            </td>
                                                            <td>
                                                                <input name="sat[]" id="sat1" type="text" class="form-control input-xsmall" onkeypress="return tabE(this,event)">
                                                            </td>
                                                            <td>
                                                                <a class="btn default" id="ViewDetailPaket1" data-toggle="modal" onclick="getPaket(this.id)"><i class="fa fa-search"></i></a>
                                                                <input name="ifViewDetailPaket[]" id="ifViewDetailPaket1" type="hidden" class="form-control input-xsmall" value="0">
                                                            </td>
                                                            <td>
                                                                <button type="button" onclick="deleteRow(this)" class="btn red"><i class="fa fa-trash"></i></button>
                                                            </td>

                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-9">
                                        <div class="wells">
                                            <button type="button" onclick="tambah()" class="btn green"><i class="fa fa-plus"></i> </button>
                                            <!-- <button type="button" onclick="hapus()" class="btn red"><i class="fa fa-trash-o"></i></button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Gudang</label>
                                            <div class="col-md-9">
                                                <select id="gudang" name="gudang" class="form-select input-medium select2" data-placeholder="Pilih...">
                                                    <option value="">--PLILIH --</option>
                                                    <?php foreach ($gudang as $row) { ?>
                                                        <option value="<?= $row->kode ?>" <?= $data != [] && $data->gudang == $row->kode ? 'selected' : '' ?>><?= $row->nama ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-4">Keterangan</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control input-large" placeholder="" name="keterangan" id="keterangan" value="<?= ($data != []) ? $data->keterangan : '' ?>">
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
                                            <label class="control-label pt-2 col-md-3">Alamat</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" name="alamat" id="alamat"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="paketan" role="tabpanel" aria-labelledby="paketan-tab">
                            <!-- First Row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="datatable2" class="table table-hover table-striped table-bordered table-condensed table-scrollable">
                                            <thead>
                                                <th style="text-align: center">Barang Paket</th>
                                                <th style="text-align: center">Nama Barang Detail</th>
                                                <th style="text-align: center">No Batch</th>
                                                <th style="text-align: center">Tanggal exp</th>
                                                <th style="text-align: center">qty batch</th>
                                                <th style="text-align: center">qty disiapkan</th>
                                                <th style="text-align: center">satuan</th>
                                            </thead>
                                            <tbody align="center">
                                                <?php
                                                $no = 0;
                                                foreach ($KirimPaket as $key) {

                                                    $barang = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem_paket' and kdcbg='$cabang'")->getRow();
                                                    $barangpaket = $db->query("SELECT * FROM inv_barang where kodeitem='$key->kodeitem_barang' and kdcbg='$cabang'")->getRow();
                                                    $RowBatch = $db->table('inv_nobatch')->getWhere(['no_batch' => $key->nobatch_paket, 'kodeitem' => $key->kodeitem_paket, 'cabang' => $cabang])->getRow();
                                                    $no++;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <input name="namabarangpaket[]" id="namabarangpaket<?= $no ?>" type="text" class="form-control input-large" value="<?= $barangpaket->namabarang ?>" readonly>
                                                            <input name="kodebarangpaket[]" id="kodebarangpaket<?= $no ?>" type="hidden" class="form-control input-large" value="<?= $key->kodeitem_barang ?>">
                                                        </td>
                                                        <td>
                                                            <input name="namabarangdetail[]" id="namabarangdetail<?= $no ?>" type="text" class="form-control input-large" value="<?= $barang->namabarang ?>" readonly>
                                                            <input name="kodebarangdetail[]" id="kodebarangdetail<?= $no ?>" type="hidden" class="form-control input-large" value="<?= $key->kodeitem_paket ?>">
                                                        </td>
                                                        <td>
                                                            <input name="no_batchpaket[]" readonly="" value="<?= $key->nobatch_paket ?>" id="no_batchpaket<?= $no ?>" type="text" class="form-control rightJustified input-small">
                                                        </td>
                                                        <td>
                                                            <input name="edpaket[]" readonly="" value="<?= $key->tglexp_paket ?>" id="edpaket<?= $no ?>" type="text" class="form-control rightJustified input-small">
                                                        </td>
                                                        <td>
                                                            <input name="qtybatchpaket[]" readonly="" value="<?= $RowBatch->qty_saldo ?>" id="qtybatchpaket<?= $no ?>" type="text" class="form-control rightJustified input-xsmall">
                                                        </td>
                                                        <td>
                                                            <input name="qtypaket[]" value="<?= $key->qtykirim_paket ?>" id="qtypaket<?= $no ?>" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                            <input name="idkodeitembarang[]" value="<?= $key->id_kodeitembarang ?>" id="idkodeitembarang<?= $no ?>" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                        </td>
                                                        <td>
                                                            <input name="satpaket[]" value="<?= $key->satuan ?>" id="satpaket<?= $no ?>" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                            <input name="outstanding[]" value="<?= $key->satuan ?>" id="outstanding<?= $no ?>" type="text" class="form-control input-xsmall rightJustified" readonly>
                                                        </td>
                                                    </tr>

                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-9">
                                    <div class="wells">
                                        <button type="button" onclick="tambah2()" class="btn green"><i class="fa fa-plus"></i> </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well">
                                <button type="button" onclick="save()" id="save_btn1" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
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
<!-- Modal Nobatch -->
<div class="modal fade" id="lupbatch" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbatch">
                <div class="modal-header">
                    <h4 class="modal-title">Inventory Nobatch/ED</h4>
                    <h5><strong><span id="namabarangharga"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarnobatch">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btntutup" class="btn red" onclick="reloadBatch()" data-bs-dismiss="modal" aria-label="Close">Tutup</button>
                </div>
        </div>
    </div>
</div>

<!-- Modal View Detail Barang Paket -->
<div class="modal fade" id="DetailPaket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbarangPaket">
                <div class="modal-header">
                    <h4 class="modal-title">Barang Paket</h4>
                    <h5><strong><span id="namabarangpaket"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarbarangpaket">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
        </div>
    </div>
</div>

<!-- Modal View Detail Barang Paket -->
<!-- <div class="modal fade" id="ShowPaketBatch" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <span id="nopilihbarangbatchpaket">
                <div class="modal-header">
                    <h4 class="modal-title">Nobatch Barang Paket</h4>
                    <h5><strong><span id="namabarangbatchpaket"></span></strong></h5>
                </div>
                <div class="modal-body">
                    <div id="daftarbarangpaketBatch">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
        </div>
    </div>
</div> -->
<script>
    var cbg = '<?= $cabang ?>';
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
        var td9 = x.insertCell(8);

        var akun = "<select name='kode[]' id=kode" + idrowx + " onchange='showbarangname(this.value," + idrowx +
            ")' class='barang_filter_cabang form-control input-xlarge' ><option value=''>--- Pilih Barang ---</option></select> <input name='idkode[]'    id=idkode" +
            idrowx + "  value=''  type='hidden' class='form-control rightJustified'  >";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='no_batch[]'  readonly  value='' id=no_batch" + idrowx +
            " type='text' class='form-control rightJustified input-small '>";
        td3.innerHTML = "<a class='btn default' id=lupharga" + idrowx +
            " data-toggle='modal' onclick='getbatch(this.id)'><i class='fa fa-search'></i></a>";
        td4.innerHTML = "<input name='tgl_exp[]'    id=tgl_exp" + idrowx +
            " value=''  type='text' readonly class='form-control rightJustified input-small'>";
        td5.innerHTML = "<input name='qty[]'  onchange='check_qty(this.value," + idrowx + ")'  id=qty" + idrowx +
            " oninput='totalline(" + idrowx +
            ")' value='0'  type='text' class='form-control rightJustified input-xsmall'  > <input name='qty_jual[]'    id=qty_jual" +
            idrowx +
            "  value='1'  type='hidden' class='form-control rightJustified'  > <input name='qtypesan[]'    id=qtypesan" +
            idrowx + "  value='1'  type='hidden' class='form-control rightJustified'  >";
        td6.innerHTML = "<input name='stock[]'  readonly  id=stock" + idrowx + " oninput='totalline(" + idrowx +
            ")' value='0'  type='text' class='form-control rightJustified input-xsmall'  > <input name='qtysisapesan[]'    id=qtysisapesan" +
            idrowx + "  value='1'  type='hidden' class='form-control rightJustified'  > ";
        td7.innerHTML = "<input name='sat[]'    id=sat" + idrowx + " type='text' class='form-control input-xsmall' >";
        td8.innerHTML = "<a class='btn default' id=ViewDetailPaket" + idrowx +
            " data-toggle='modal' onclick='getPaket(this.id)'><i class='fa fa-search'></i></a> <input name='ifViewDetailPaket[]'    id=ifViewDetailPaket" +
            idrowx + "  value='0'  type='hidden' class='form-control rightJustified'  >";
        td9.innerHTML =
            "<button type='button' onclick='deleteRow(this)' class='btn red'><i class='fa fa-trash'></i></button>";


        idrow++;
        initailizeSelect2_barang_filterCabang(cbg);
    }

    function tambah2() {
        var table = document.getElementById("datatable2");
        var idrowx = table.rows.length;

        var x = document.getElementById('datatable2').insertRow(idrowx);
        var td1 = x.insertCell(0);
        var td2 = x.insertCell(1);
        var td3 = x.insertCell(2);
        var td4 = x.insertCell(3);
        var td5 = x.insertCell(4);
        var td6 = x.insertCell(5);
        var td7 = x.insertCell(6);

        var akun = "<input name='namabarangpaket[]'  readonly  value='' id=namabarangpaket" + idrowx +
            " type='text' class='form-control rightJustified input-large '> <input name='kodebarangpaket[]'  readonly  value='' id=kodebarangpaket" + idrowx +
            " type='hidden' class='form-control rightJustified input-large '>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='namabarangdetail[]'  readonly  value='' id=namabarangdetail" + idrowx +
            " type='text' class='form-control rightJustified input-large '> <input name='kodebarangdetail[]'  readonly  value='' id=kodebarangdetail" + idrowx +
            " type='hidden' class='form-control rightJustified input-large '>";
        td3.innerHTML = "<input name='no_batchpaket[]'  readonly  value='' id=no_batchpaket" + idrowx +
            " type='text' class='form-control rightJustified input-small '>";
        td4.innerHTML = "<input name='edpaket[]'  id=edpaket" + idrowx +
            " value=''  type='text' class='form-control rightJustified input-small'  readonly>";
        td5.innerHTML = "<input name='qtybatchpaket[]'    id=qtybatchpaket" + idrowx + " type='text' class='form-control input-xsmall' readonly>";
        td6.innerHTML = "<input name='qtypaket[]'  id=qtypaket" + idrowx +
            " value='0'  type='text' class='form-control rightJustified input-xsmall'  readonly> <input name='idkodeitembarang[]'    id=idkodeitembarang" + idrowx + " type='hidden' class='form-control input-xsmall' readonly >";
        td7.innerHTML = "<input name='satpaket[]'    id=satpaket" + idrowx + " type='text' class='form-control input-xsmall' readonly > <input name='outstanding[]'    id=outstanding" + idrowx + " type='text' class='form-control input-xsmall' readonly >";
        idrow++;
    }

    function deleteRow(button) {
        var row = button.parentNode.parentNode;
        var itemCode = row.cells[0].children[0].value; // get the item code from the first cell of the row
        var table = document.getElementById("datatable2");
        var rowCount = table.rows.length;
        for (var i = rowCount - 1; i >= 1; i--) { // loop backwards through the rows in datatable2
            var row2 = table.rows[i];
            var itemCode2 = row2.cells[0].children[1].value; // get the item code from the second cell of the row in datatable2
            if (itemCode == itemCode2) {
                table.deleteRow(i);
            }
        }
        row.parentNode.removeChild(row);
        $('#save_btn1').attr('disabled', false);
    }

    function showbarangname(str, id) {
        var xhttp;
        var vid = id;
        var customer = $('#cust').val();
        var kodeso = $('[name="kodeso"]').val();
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/showbarang/') ?>" + str + '/' + customer + '/' + kodeso,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.DataOne == null) {
                    swal('Pesanan Disiapkan', 'stock atau barang tidak sesuai dengan pesanan', '');
                    $('#save_btn' + vid).attr('disabled', true);
                } else {
                    if (data.DataOne.stok > 0) {
                        $('#sat' + vid).val(data.DataOne.satuan);
                        $('#stock' + vid).val(data.DataOne.stok);
                        $('#idkode' + vid).val(data.DataThree.id);
                    } else {
                        swal('stock', 'stock tidak mencukupi...', '');
                        $('#sat' + vid).val(data.DataOne.satuan);
                        $('#stock' + vid).val(data.DataOne.stok);
                        $('#idkode' + vid).val(data.DataThree.id);
                        /*$('#save_btn'+vid).attr('disabled',true);*/
                    }
                    var table = document.getElementById('datatable');
                    var rowCount = table.rows.length;
                    for (var i = 1; i < rowCount; i++) {
                        var row = table.rows[i];
                        strkode = data.DataOne.kodeitem.trim();
                        kode = row.cells[0].children[0].value;
                        qtypesan = row.cells[4].children[2].value;
                        qtysisa = row.cells[5].children[1].value;
                        if (kode == strkode) {
                            $('#qtysisapesan' + vid).val(qtysisa);
                            $('#qtypesan' + vid).val(qtypesan);
                        }
                    }
                    document.getElementById("ifViewDetailPaket" + id).value = data.DataTwo;
                }

            }
        });
    }

    function showpo() {
        var xhttp;
        var str = $('[name="cust"]').val();

        if (str == "") {
            document.getElementById("kodeso").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("kodeso").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('penjualan/transaksi/pesanandisiapkan/showso/') ?>" + str, true);
        xhttp.send();
    }

    function Savelpb() {
        var xhttp;
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var str = $('[name=kodeso]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/savelpb') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    var parse = JSON.parse(data);
                    $('#process').css('display', 'none');
                    if (parse.status == 1) {
                        swal('PENJUALAN DISIAPKAN', 'Data Berhasil di Validasi ... ' + parse.message, '');
                    } else {
                        swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ', '');
                }
            });
        }
    }

    function getso() {
        var xhttp;
        var nomorbukti = '<?= $nomor ?>';
        var nomorbukti_ppn = '<?= $nomor_ppn ?>';
        var str = $('[name=kodeso]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/dataso/') ?>" + str,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    for (i = 0; i <= data.DataOne.length - 1; i++) {
                        if (i > 0) {
                            tambah();
                        }

                        x = i + 1;

                        var option = $("<option selected></option>").val(data.DataOne[i].kodeitem).text(data.DataOne[i]
                            .namabarang);
                        $('#kode' + x).append(option).trigger('change');

                        document.getElementById("idkode" + x).value = data.DataOne[i].id;
                        document.getElementById("qty" + x).value = data.DataOne[i].sisa;
                        document.getElementById("qtypesan" + x).value = data.DataOne[i].sisa;
                        document.getElementById("qtysisapesan" + x).value = data.DataOne[i].sisa;
                        document.getElementById("sat" + x).value = data.DataOne[i].satuan;
                        document.getElementById("stock" + x).value = data.DataOne[i].stok;
                        document.getElementById("no-po").value = data.DataOne[i].no_po;
                        document.getElementById("sppn").value = data.DataOne[i].sppn;
                        document.getElementById("keterangan").value = data.DataOne[i].ket;
                        //document.getElementById("gudang").value=data[i].kode;
                        $('[name="gudang"]').val(data.DataOne[i].kode);
                        if (data.DataOne[i].sppn == 'Y') {
                            $('#nomorbukti').val(nomorbukti_ppn);
                        } else {
                            $('#nomorbukti').val(nomorbukti);
                        }
                        var sisastok = parseInt(data.DataOne[i].sisa);
                        var stokbrg = parseInt(data.DataOne[i].stok);
                        if (sisastok > stokbrg) {
                            swal({
                                title: "STOCK BARANG",
                                html: "<p>Cek Stock Terlebih Dahulu</p>",
                                type: "info",
                                confirmButtonText: "OK"
                            });
                        }
                    }

                    for (i = 0; i <= data.DataTwo.length - 1; i++) {
                        x = i + 1;
                        var data1 = data.DataTwo[i].idData;
                        document.getElementById("ifViewDetailPaket" + x).value = data.DataTwo[i].idData;
                    }
                }
            });
        }
    }

    //Show Batch perbarang
    function getbatch(id) {
        var vid = id.substring(8);
        document.getElementById("nopilihbatch").value = vid;
        var gudang = document.getElementById("gudang").value;
        var item = document.getElementById("kode" + vid).value;
        var xhttp;
        if (item == "" && gudang == "") {
            document.getElementById("daftarnobatch").innerHTML = "";
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $('#lupbatch').modal('show');

                document.getElementById("daftarnobatch").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('getbatchid/') ?>" + item + "/" + gudang, true);
        xhttp.send();
    }

    //Entry batch
    function post_batch(v1, v2, v3, v4) {

        id = document.getElementById("nopilihbatch").value;
        var kode = $('#kode' + id).val();
        var qty = parseInt($('#qty' + id).val());
        var qtybatch = parseInt(v4);

        if (v1 == kode) {
            document.getElementById("no_batch" + id).value = v2;
            document.getElementById("tgl_exp" + id).value = v3;
            document.getElementById("qty_jual" + id).value = v4;
            $('#lupbatch').modal('hide');
        } else {
            swal('PENJUALAN DISIAPKAN', 'barang tidak Sesuai  ...', '');
        }
        if (qty > qtybatch) {
            swal('No BATCH', 'qty Tidak Sesuai / lebih dari qty No BATCH  ...', '');
            document.getElementById("save_btn1").disabled = true;
        } else {
            document.getElementById("save_btn1").disabled = false;
        }
    }

    function getPaket(id) {
        var vid = id.substring(15);
        var item = document.getElementById("kode" + vid).value;
        var qty = document.getElementById("qty" + vid).value;
        var cek = $('#ViewDetailPaket' + vid).prop('disabled') == true;
        if (cek == false) {
            var param = item + '/' + qty + '/' + vid;
            if (item == "") {
                swal('PESANAN PENJUALAN', 'barang tidak boleh kosong ...', '');
            } else {

                if (item == "") {
                    document.getElementById("daftarbarangpaket").innerHTML = "";
                    return;
                }
                xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        $('#DetailPaket').modal('show');
                        document.getElementById("daftarbarangpaket").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "<?= base_url('penjualan/transaksi/pesanandisiapkan/ShowPaketDisiapkan/') ?>" + param, true);
                xhttp.send();
            }
        }

    }

    function CheckQtyBatchPaket(str, id) {
        var kodeitem = $('#kodebarang_detail' + id).val();
        var qtyhitungpaket = parseInt($('#hitung_paket' + id).val());
        var table = document.getElementById("datatable2");
        var x = table.rows.length;
        var CountPaket = $('[name="countpaket"]').val();
        var isError = false;
        for (i = 0; i <= x - 1; i++) {
            a = i + 1;
            var batchpaket = $('#no_batchpaket' + a).val();
            var idkodeitem = $('#kodebarangdetail' + a).val();
            var outstanding = parseInt($('#outstanding' + a).val());
            if (batchpaket == str && idkodeitem == kodeitem) {
                var hitung = outstanding - qtyhitungpaket;
                if (hitung < 0) {
                    alert('stock batch dibarang detail tidak mencukupi');
                    isError = true;
                    break;
                }else{
                    isError = false;
                    break;
                }
            }
            if (isError) break;
        }
         // tombol dikontrol sekali saja
        $('#save_btn1').prop('disabled', isError);
        $('#terapkanpaket').prop('disabled', isError);
        $.ajax({
            url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/SelectBatchPaket/') ?>" + str + '/' + kodeitem,
            type: "GET",
            dataType: "JSON",

            success: function(data) {
                datacek = data;
                document.getElementById("stockbatchpaket" + id).value = data.qty_saldo;
                document.getElementById("edpaket" + id).value = data.tanggal_exp;
                if (data.qty_saldo < qtyhitungpaket) {
                    alert('stock kurang dari orderan');
                    $('#save_btn1').attr('disabled', true);
                    $('#terapkanpaket').attr('disabled', true);
                } else {
                    $('#save_btn1').attr('disabled', false);
                    $('#terapkanpaket').attr('disabled', false);
                }

            }
        });
        
        
    }

    function TerapkanBatchPaket(vid) {
        var table = document.getElementById("datatable2");
        var x = table.rows.length;
        var CountPaket = parseInt($('[name="countpaket"]').val());
        var isError = false;
        for (let i = 0; i < CountPaket; i++) {
            let id = i + 1;
            let namabarangPaketDetail = $('#namabarangPaketDetail' + id).val();
            let hitung_paket = parseInt($('#hitung_paket' + id).val()) || 0;
            let Stock_batch = parseInt($('#stockbatchpaket' + id).val()) || 0;

            if (Stock_batch < hitung_paket) {
                alert('Barang ' + namabarangPaketDetail + ' melebihi stock batch');
                isError = true;
                break;
            }
            let batchrinci = $('#batchpaket' + id).val();
            let kodedetailrinci = $('#kodebarang_detail' + id).val();

            for (let j = 0; j < x; j++) {
                let a = j + 1;
                let batchtable2 = $('#no_batchpaket' + a).val();
                let idkodeitem = $('#kodebarangdetail' + a).val();
                let outstanding = parseInt($('#outstanding' + a).val()) || 0;
                if (batchtable2 == batchrinci && idkodeitem == kodedetailrinci) {
                    let hitung = outstanding - hitung_paket;
                    // if (hitung < 0) {
                    //     alert('Stock batch di barang detail tidak mencukupi');
                    //     isError = true;
                    //     break;
                    // }
                }
            }

            if (isError) break;
        }

        // tombol dikontrol sekali saja
        $('#save_btn1').prop('disabled', isError);
        $('#terapkanpaket').prop('disabled', isError);

        if ($('#save_btn1').prop('disabled') == false) {
            for (i = 0; i <= CountPaket - 1; i++) {
                no = i + 1;
                var kodebarang_paket = $('#kodebarang_paket' + no).val();
                var namabarang_paket = $('#namabarang_paket' + no).val();
                var namabarangPaketDetail = $('#namabarangPaketDetail' + no).val();
                var kodebarang_detail = $('#kodebarang_detail' + no).val();
                var hitung_paket = $('#hitung_paket' + no).val();
                var satuan_paket = $('#satuan_paket' + no).val();
                var batchpaket = $('#batchpaket' + no).val();
                var Stock_batch = $('#stockbatchpaket' + no).val();
                var EdBatch = $('#edpaket' + no).val();
                if (kodebarang_paket != undefined) {
                    tambah2();

                    document.getElementById("namabarangpaket" + x).value = namabarang_paket;
                    document.getElementById("kodebarangpaket" + x).value = kodebarang_paket;
                    document.getElementById("namabarangdetail" + x).value = namabarangPaketDetail;
                    document.getElementById("kodebarangdetail" + x).value = kodebarang_detail;
                    document.getElementById("no_batchpaket" + x).value = batchpaket;
                    document.getElementById("edpaket" + x).value = EdBatch;
                    document.getElementById("qtybatchpaket" + x).value = Stock_batch;
                    document.getElementById("qtypaket" + x).value = hitung_paket;
                    document.getElementById("idkodeitembarang" + x).value = vid;
                    document.getElementById("satpaket" + x).value = satuan_paket;
                    document.getElementById("outstanding" + x).value = Stock_batch - hitung_paket;

                    x++;
                }

                $('#DetailPaket').modal('hide');
            }
            document.getElementById("ifViewDetailPaket" + vid).value = 1;
            $('#ViewDetailPaket' + vid).attr('disabled', true);
            $('#ViewDetailPaket' + vid).prop('disabled', true);
            savelpbPerBarang(vid);
        }

    }

    function savelpbPerBarang(id) {
        var xhttp;
        var kodedata = $('#kode' + id).val();
        var idkode = $('#idkode' + id).val();
        var qty = $('#qty' + id).val();
        var NoBuktiPB = $('[name=NoPBBeli]').val();
        var NoBuktiPU = $('[name=NoPUBeli]').val();
        var str = $('[name=kodeso]').val();
        if (str != "") {
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/savelpbPerBarang/') ?>" + kodedata + '/' + NoBuktiPB + '/' + NoBuktiPU + '/' + str + '/' + idkode + '/' + qty,
                type: "GET",
                dataType: "JSON",
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    $('#process').css('display', 'none');
                    if (data.status == 1) {
                        swal('PENJUALAN DISIAPKAN', 'Data Berhasil di Validasi ... ' + data.message, '');
                        document.getElementById("stock" + id).value = data.stok;
                        // $('#lupharga' + id).attr('disabled', false);
                        // $('#lupharga' + id).prop('disabled', false);
                    } else {
                        swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ' + data.message, '');
                    }
                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    $('#process').css('display', 'none');
                    swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ', '');
                }
            });
        }
    }

    function getBatchPaket(id, kode) {
        var vid = id;
        var item = kode;
        var gudang = document.getElementById("gudang").value;
        var param = item + '/' + gudang;
        if (item == "") {
            swal('PESANAN PENJUALAN', 'barang tidak boleh kosong ...', '');
        } else {

            if (item == "") {
                document.getElementById("daftarbarangpaketBatch").innerHTML = "";
                return;
            }
            xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    $('#ShowPaketBatch').modal('show');
                    document.getElementById("daftarbarangpaketBatch").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?= base_url('getbatchid/') ?>" + item + "/" + gudang, true);
            xhttp.send();
        }

    }

    function check_qty(str, id) {
        var str_a = $('[name="kodeso"]').val();
        var str_b = $('#kode' + id).val();
        var str_c = parseInt($('#qty_jual' + id).val());
        var str_d = parseInt($('#qtysisapesan' + id).val());
        var str_e = parseInt($('#qtypesan' + id).val());
        var vstr = parseInt(str);
        var vid = id;
        if (vstr > str_c) {
            swal('Penjualan', 'qty tidak boleh lebih dari qty Nobatch...', '');
            $('#save_btn1').attr('disabled', true);
        } else {
            $('#save_btn1').attr('disabled', false);
        }

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        qty = row.cells[5].children[1].value - row.cells[4].children[0].value;
        var qtysisa = row.cells[5].children[1].value = qty;
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var tot_qty = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];
            kode = row.cells[0].children[0].value;
            qty = row.cells[4].children[0].value;
            qty_jual = row.cells[4].children[1].value;
            if (kode == str_b) {
                $('#qtysisapesan' + i).val(qtysisa);
                tot_qty += parseInt(qty);
            }
        }
        if (tot_qty > str_e) {
            swal('Pesanan Disiapkan', 'qty tidak boleh lebih dari qty pesanan masuk...', '');
            $('#save_btn1').attr('disabled', true);
        }
        HitungqtyPaket(str_b, vstr, id);
    }

    function HitungqtyPaket(kode, qty, id) {
        if (kode != "") {
            var table2 = document.getElementById('datatable2');
            var rowCount = table2.rows.length;

            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/CheckBarangPaket/') ?>" + kode,
                type: "GET",
                dataType: "JSON",

                success: function(data) {
                    for (i = 0; i <= data.length - 1; i++) {
                        if (data[i].kodeitem_barang != "") {
                            for (x = 1; x < rowCount; x++) {
                                var row = table2.rows[x];

                                KodePaket = row.cells[0].children[1].value;
                                QtyBatch = row.cells[4].children[0].value;
                                idkodeitembarang = row.cells[5].children[1].value;
                                if (data[i].kodeitem_barang == KodePaket) {
                                    if (idkodeitembarang == id) {
                                        var hitung_paket = parseInt(data[i].qty_paket) * qty;
                                        document.getElementById("qtypaket" + x).value = hitung_paket;
                                        if (QtyBatch < hitung_paket) {
                                            swal('PENJUALAN DISIAPKAN', 'qty paket tidak boleh lebih dari qty batch', '');
                                            $('#save_btn1').attr('disabled', true);
                                        } else {
                                            $('#save_btn1').attr('disabled', false);
                                        }
                                    }

                                }
                            }
                        }
                    }

                }
            });
        }
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENJUALAN DISIAPKAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $("#save_btn1").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENJUALAN DISIAPKAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    function save() {
        var noform = $('[name="nomorbukti"]').val();
        var cust = $('[name="cust"]').val();
        var tanggal = $('#tanggal').val();
        var gudang = $('#gudang').val();
        var table = document.getElementById('datatable');
        var rowCount = table.rows.length;
        var itemFound = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            kode = row.cells[0].children[0].value;
            qty = row.cells[4].children[0].value;
            qty_jual = row.cells[4].children[1].value;
            qty_jual = row.cells[4].children[1].value;

            var a = Number(qty.replace(/[^0-9\.]+/g, ""));

            var b = Number(qty_jual.replace(/[^0-9\.]+/g, ""));
            // check_qty(a, i);
            if (a > b) {
                swal('PENJUALAN DISIAPKAN', 'qty di baris ke ' + i + ' tidak boleh lebih dari qty no batch ...', '');
                $('#save_btn1').attr('disabled', true);
            }
            var ViewDetailPaket = $('#ifViewDetailPaket' + i).val();
            if (ViewDetailPaket == 1) {
                itemFound = itemFound;
            } else {
                itemFound = itemFound + 1;
            }
        }
        //if (itemFound > 0) {
        //  swal('PENJUALAN DISIAPKAN', 'Detail Paket belum diterapkan', '');
        //$('#save_btn1').attr('disabled', true);
       	//}

        if (noform == "") {
            swal('PENJUALAN DISIAPKAN', 'Nomor surat jalan / no. pengiriman belum diisi ...', '');
        } else if ($('#save_btn1').prop('disabled') == true) {

        } else if (gudang == "") {
            swal('PENJUALAN DISIAPKAN', 'Gudang belum diisi ...', '');
        } else {
            document.getElementById("save_btn1").disabled = true;
            $.ajax({
                url: "<?= base_url('penjualan/transaksi/pesanandisiapkan/save') ?>",
                data: $('#frmpenjualan').serialize(),
                type: 'POST',
                beforeSend: function() {
                    $('#process').css('display', 'block');
                },
                success: function(data) {
                    var parse = JSON.parse(data);
                    $('#process').css('display', 'none');
                    if (parse.status == 1) {
                        document.getElementById("save_btn1").disabled = true;
                        swal({
                            title: "PENJUALAN DISIAPKAN",
                            html: "<p> Kode Customer   : <b>" + cust + "</b> </p>" +
                                "Tanggal :  " + tanggal,
                            type: "info",
                            confirmButtonText: "OK"
                        }).then((value) => {
                            window.location.href = "<?= base_url('penjualan/transaksi/pesanandisiapkan') ?>";
                        });
                    } else {
                        swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ' + parse.message, '');
                    }


                },
                error: function(data) {
                    $('#process').css('display', 'none');
                    swal('PENJUALAN DISIAPKAN', 'Data gagal disimpan ... ', '');
                }
            });
        }
    }
</script>