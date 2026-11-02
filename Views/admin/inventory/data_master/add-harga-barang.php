<?php
$db = db_connect();
if ($data != []) {
    $DataInvBarang = $db->table("inv_barang")->getWhere(['kodeitem' => $data->kodeitem, 'kdcbg' => $data->cabang])->getRow();
    $DataInvBarangKode = str_replace(' ', '', $DataInvBarang->kodeitem);
    $datakodeitem = str_replace(' ', '', $data->kodeitem);
    main_header('Inventory', 'Data Harga Barang per Customer', 'Inventory', 'Edit Harga Barang', 'inventory/datamaster/hargabarangpercustomer' );
    $DataOrder = 'Data Edit';
}else{
    main_header('Inventory', 'Data Harga Barang per Customer', 'Inventory', 'Entri Harga Barang', 'inventory/datamaster/hargabarangpercustomer' );
    $DataOrder = 'Data Baru';
}
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i>* <?= $DataOrder;?>
        </div>
    </div>
    <div class="portlet-body form ">
        <form id="submit_form" class="form-horizontal" method="post">
            <input type="hidden" value="<?= ($data != []) ? $data->kodeitem : 0 ?>" name="id" />
            <div class="form-body ">
                <div class="tabbable tabbable-custom tabbable-full-width">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab">
                                <i class="fa fa-file"></i>
                                Harga Barang
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <!--BARIS KE 1-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tanggal</label>
                                            <div class="col-md-9">
                                                <input id="tanggal" name="tanggal" class="form-control input-medium"
                                                type="date" value="<?= ($data != []) ? date('Y-m-d', strtotime($data->tanggal)) : date('Y-m-d') ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Gudang*</label>
                                            <div class="col-md-9">
                                                <select id="gudang" name="gudang" class="form-control input-medium select2" data-placeholder="Pilih Gudang"  onchange="getstokgudang()" required>
                                                    <option value="">pilih</option>
                                                    <?php foreach($gudang as $row) { ?>
                                                        <option value="<?php echo $row->kode;?>" <?= ($data != []) ? (($row->kode == $data->gudang) ? 'selected' : '') : '' ?>>
                                                        <?php echo $row->nama;?></option>
                                                    <?php } ?>
                                                </select>
                                                <input id="cabang" name="cabang" class="form-control input-medium" value="<?= ($data != []) ? $data->cabang : '' ?>" type="hidden"> 
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!--BARIS KE 2-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="row">
                                            <div style="overflow-x: auto;">
                                                <div class="datatable-example">
                                                    <table id="datatable2"
                                                        class="table table-hoverx table-responsive table-stripedx table-borderedx table-condensed table-scrollable">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align: center">Nama Supplier</th>
                                                                <th style="text-align: center">H. Beli Bruto</th>
                                                                <th style="text-align: center">disc %</th>
                                                                <th style="text-align: center">H. Beli Netto</th>
                                                                <th style="text-align: center"></th>

                                                            </tr>
                                                            <thead>
                                                            <?php if ($data != []) { ?>
                                                                <tbody id="tbody-data">
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp1" name="supp1"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)" onchange="showdatasup(this.value, 1)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>" <?= ($DataInvBarang->kode_spl1 !='') ? (($DataInvBarang->kode_spl1 ==$row->kode) ? "selected" : '') : '' ?>>
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto1" id="bruto1" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup1','netto1')"
                                                                                class="form-control input-small rightJustified" value=' <?= ($DataInvBarang->kode_spl1 !='') ? $DataInvBarang->bruto_spl1 : '0' ?>'></td>
                                                                        <td width="10%"><input name="disc_sup1" id="disc_sup1"
                                                                                type="text"
                                                                                oninput="totalline1('bruto1', $(this).attr('id'), 'netto1')"
                                                                                class="form-control input-xsmall rightJustified" value='<?= ($DataInvBarang->kode_spl1 !='') ? $DataInvBarang->disc_spl1 : '0' ?>'></td>
                                                                        <td><input name="netto1" oninput="totalline1()"
                                                                                id="netto1" value='<?= ($DataInvBarang->kode_spl1 !='') ? $DataInvBarang->netto_spl1 : '0' ?>' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio" <?= ($data != []) ? (($DataInvBarang->kode_spl1 == $data->kode_spl) ? 'checked' : '') : '' ?>
                                                                                    class="form-check-input  radio1">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp2" name="supp2"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>" <?= ($DataInvBarang->kode_spl2 !='') ? (($DataInvBarang->kode_spl2 ==$row->kode) ? "selected" : '') : '' ?>>
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto2" id="bruto2" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup2','netto2')"
                                                                                class="form-control input-small rightJustified" value='<?= ($DataInvBarang->kode_spl2 !='') ? $DataInvBarang->bruto_spl2 : '0' ?>'></td>
                                                                        <td width="10%"><input name="disc_sup2" id="disc_sup2"
                                                                                type="text"
                                                                                oninput="totalline1('bruto2', $(this).attr('id'), 'netto2')"
                                                                                class="form-control input-xsmall rightJustified" value='<?= ($DataInvBarang->kode_spl2 !='') ? $DataInvBarang->disc_spl2 : '0' ?>'></td>
                                                                        <td><input name="netto2"
                                                                                oninput="totalline1('bruto2','disc_sup2',$(this).attr('id'))"
                                                                                id="netto2" value='<?= ($DataInvBarang->kode_spl2 !='') ? $DataInvBarang->netto_spl2 : '0' ?>' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio" <?= ($data != []) ? (($DataInvBarang->kode_spl2 == $data->kode_spl) ? 'checked' : '') : '' ?>
                                                                                    class="form-check-input  radio2">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp3" name="supp3"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>" <?= ($DataInvBarang->kode_spl3 !='') ? (($DataInvBarang->kode_spl3 ==$row->kode) ? "selected" : '') : '' ?>>
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto3" id="bruto3" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup3','netto3')"
                                                                                class="form-control input-small rightJustified" value='<?= ($DataInvBarang->kode_spl3 !='') ? $DataInvBarang->bruto_spl3 : '0' ?>'></td>
                                                                        <td width="10%"><input name="disc_sup3" id="disc_sup3"
                                                                                type="text"
                                                                                oninput="totalline1('bruto3',$(this).attr('id'),'netto3')"
                                                                                class="form-control input-xsmall rightJustified" value='<?= ($DataInvBarang->kode_spl3 !='') ? $DataInvBarang->disc_spl3 : '0' ?>'></td>
                                                                        <td><input name="netto3"
                                                                                oninput="totalline1('bruto3','disc_sup3',$(this).attr('id'))"
                                                                                id="netto3" value='<?= ($DataInvBarang->kode_spl3 !='') ? $DataInvBarang->netto_spl3 : '0' ?>' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio" <?= ($data != []) ? (($DataInvBarang->kode_spl3 == $data->kode_spl) ? 'checked' : '') : '' ?>
                                                                                    class="form-check-input  radio3">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp4" name="supp4"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>" <?= ($DataInvBarang->kode_spl4 !='') ? (($DataInvBarang->kode_spl4 ==$row->kode) ? "selected" : '') : '' ?>>
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto4" id="bruto4" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup4','netto4')"
                                                                                class="form-control input-small rightJustified" value='<?= ($DataInvBarang->kode_spl4 !='') ? $DataInvBarang->bruto_spl4 : '0' ?>'></td>
                                                                        <td width="10%"><input name="disc_sup4" id="disc_sup4"
                                                                                type="text"
                                                                                oninput="totalline1('bruto4',$(this).attr('id'),'netto4')"
                                                                                class="form-control input-xsmall rightJustified" value='<?= ($DataInvBarang->kode_spl4 !='') ? $DataInvBarang->disc_spl4 : '0' ?>'></td>
                                                                        <td><input name="netto4"
                                                                                oninput="totalline1('bruto4','disc_sup4',$(this).attr('id'))"
                                                                                id="netto4" value='<?= ($DataInvBarang->kode_spl4 !='') ? $DataInvBarang->netto_spl4 : '0' ?>' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio" <?= ($data != []) ? (($DataInvBarang->kode_spl4 == $data->kode_spl) ? 'checked' : '') : '' ?>
                                                                                    class="form-check-input  radio4">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center"><label class="control-label" >Harga
                                                                                Acuan</label></td>
                                                                        <td>
                                                                            <input name="kd_spl" id="kode_spl" readonly
                                                                                type="hidden" value="<?= ($data != []) ? $data->kode_spl : '' ?>" class="form-control rightJustified">
                                                                            <input name="bruto_spl" id="bruto_spl" readonly value="<?= ($data != []) ? $data->bruto_spl : '' ?>"
                                                                                type="text" class="form-control rightJustified" required>
                                                                        </td>
                                                                        
                                                                        <td>
                                                                            <input name="disc_spl" id="disc_spl" readonly value="<?= ($data != []) ? $data->disc_spl : '' ?>"
                                                                                type="text" class="form-control rightJustified">
                                                                        </td>
                                                                        <td><input name="jumlah_netto" onchange="total()" id="jumlah_netto" readonly
                                                                                value='<?= ($data != []) ? $data->netto_spl : '' ?>' type="text"
                                                                                class="form-control rightJustified" required></td>
                                                                    </tr>
                                                                </tbody>
                                                            <?php }else{ ?>
                                                                <tbody id="tbody-data">
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp1" name="supp1"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)" onchange="showdatasup(this.value, 1)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>">
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto1" id="bruto1" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup1','netto1')"
                                                                                class="form-control input-small rightJustified" value='0'></td>
                                                                        <td width="10%"><input name="disc_sup1" id="disc_sup1"
                                                                                type="text"
                                                                                oninput="totalline1('bruto1', $(this).attr('id'), 'netto1')"
                                                                                class="form-control input-xsmall rightJustified" value='0'></td>
                                                                        <td><input name="netto1" oninput="totalline1()"
                                                                                id="netto1" value='0' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio"
                                                                                    class="form-check-input  radio1">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp2" name="supp2"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>">
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto2" id="bruto2" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup2','netto2')"
                                                                                class="form-control input-small rightJustified" value='0'></td>
                                                                        <td width="10%"><input name="disc_sup2" id="disc_sup2"
                                                                                type="text"
                                                                                oninput="totalline1('bruto2', $(this).attr('id'), 'netto2')"
                                                                                class="form-control input-xsmall rightJustified" value='0'></td>
                                                                        <td><input name="netto2"
                                                                                oninput="totalline1('bruto2','disc_sup2',$(this).attr('id'))"
                                                                                id="netto2" value='0' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio"
                                                                                    class="form-check-input  radio2">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp3" name="supp3"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>">
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto3" id="bruto3" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup3','netto3')"
                                                                                class="form-control input-small rightJustified" value='0'></td>
                                                                        <td width="10%"><input name="disc_sup3" id="disc_sup3"
                                                                                type="text"
                                                                                oninput="totalline1('bruto3',$(this).attr('id'),'netto3')"
                                                                                class="form-control input-xsmall rightJustified" value='0'></td>
                                                                        <td><input name="netto3"
                                                                                oninput="totalline1('bruto3','disc_sup3',$(this).attr('id'))"
                                                                                id="netto3" value='0' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio"
                                                                                    class="form-check-input  radio3">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <select id="supp4" name="supp4"
                                                                                class="form-control input-medium select2me"
                                                                                data-placeholder="Pilih..."
                                                                                onkeypress="return tabE(this,event)">
                                                                                <option value="">--- Pilih Barang ---</option>
                                                                                <?php
                                                                            foreach($supp as $row) { ?>
                                                                                <option value="<?php echo $row->kode;?>">
                                                                                    <?php echo $row->nama;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input name="bruto4" id="bruto4" type="text"
                                                                                oninput="totalline1($(this).attr('id'),'disc_sup4','netto4')"
                                                                                class="form-control input-small rightJustified" value='0'></td>
                                                                        <td width="10%"><input name="disc_sup4" id="disc_sup4"
                                                                                type="text"
                                                                                oninput="totalline1('bruto4',$(this).attr('id'),'netto4')"
                                                                                class="form-control input-xsmall rightJustified" value='0'></td>
                                                                        <td><input name="netto4"
                                                                                oninput="totalline1('bruto4','disc_sup4',$(this).attr('id'))"
                                                                                id="netto4" value='0' type="text"
                                                                                class="form-control input-small rightJustified" readonly></td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input name="radio[]" id="radio" type="radio"
                                                                                    class="form-check-input  radio4">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="center"><label class="control-label" >Harga
                                                                                Acuan</label></td>
                                                                        <td>
                                                                            <input name="kd_spl" id="kode_spl" readonly
                                                                                type="hidden" class="form-control rightJustified">
                                                                            <input name="bruto_spl" id="bruto_spl" readonly
                                                                                type="text" class="form-control rightJustified" required>
                                                                        </td>
                                                                        
                                                                        <td>
                                                                            <input name="disc_spl" id="disc_spl" readonly
                                                                                type="text" class="form-control rightJustified">
                                                                        </td>
                                                                        <td><input name="jumlah_netto" onchange="total()" id="jumlah_netto" readonly
                                                                                value='0' type="text"
                                                                                class="form-control rightJustified" required></td>
                                                                    </tr>
                                                                </tbody> 
                                                            <?php } ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Barang</label>
                                            <div class="col-md-9">
                                                <select name="barang" id="barang"
                                                    class="barang_filter  form-control input-large" data-placeholder="Pilih Barang"
                                                    onchange="infobarang()" required>
                                                    <option value="">--- Pilih Barang ---</option>
                                                    <?php if($data != []) { ?>
                                                        <option value="<?php echo $DataInvBarangKode;?>" <?= ($data != []) ? (($DataInvBarangKode == $datakodeitem) ? "selected" : '') : '' ?>>
                                                        <?php echo $data->nama_barang;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Satuan</label>
                                            <div class="col-md-4">
                                                <input type="text" name="satuan" id="satuan"
                                                        class="form-control rightJustified" value="<?= ($data != []) ? $DataInvBarang->satuan :'' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Beli Terakhir</label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="hargasendiri" value="0" id="hargamodal"
                                                        class="form-control rightJustified">
                                                <input type="text" name="tglbeliakhir" id="tgl_beli_akhir"
                                                        class="form-control rightJustified" value="<?= ($data != []) ? date('Y-m-d', strtotime($DataInvBarang->tglbeliakhir)) :'' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="row">
                                            <label class="control-label pt-2 col-md-3">Tgl Jual Terakhir</label>
                                            <div class="col-md-9">
                                                <input type="text" name="tgljualakhir" id="tgl_jual_akhir"
                                                    class="form-control rightJustified" value="<?= ($data != []) ? date('Y-m-d',strtotime($DataInvBarang->tgljualakhir)) : '' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--BARIS KE 3-->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <div class="row">
                                            <div style="overflow-x: auto;">
                                                <div class="datatable-example">
                                                    <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                                        <thead>
                                                            <tr>
                                                                <th  style="text-align: center">Nama Customer</th>
                                                                <th  style="text-align: center">Bruto</th>
                                                                <th  style="text-align: center">disc%</th>
                                                                <th  style="text-align: center">Netto</th>
                                                                <th  style="text-align: center">Bagian 1</th>
                                                                <th  style="text-align: center">Bagian 2</th>
                                                                <th  style="text-align: center">Bagian 3</th>
                                                                <th  style="text-align: center">Hitung Profit</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php if ($DetailItem != []) {
                                                                $no = 0;
                                                                foreach ($DetailItem as $key) {
                                                                    $no++;
                                                                    $rpcust = $key->bruto_cust * ($key->diskon_pr / 100);
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $no;?>"
                                                                        class="selectCustfilter form-select input-medium" required onchange="getnamecust(this.value, <?= $no;?>)">
                                                                        <option value="<?= $key->kode_customer;?>"><?= $key->nama_customer;?></option>
                                                                    </select>
                                                                    <input id="namecust<?= $no;?>" name="namecust[]" type="hidden" value="<?= $key->nama_customer;?>"
                                                                        class="form-control rightJustified" required>
                                                                </td>
                                                                <td><input id="bruto<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="bruto[]" type="text" value="<?=number_format($key->bruto_cust,2,'.',','); ?>"
                                                                        class="form-control input-small rightJustified" required></td>
                                                                <td>
                                                                    <input id="disc<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="disc[]" type="text" value="<?=$key->diskon_pr; ?>"
                                                                        class="form-control input-xsmall rightJustified" required>
                                                                    <input id="rpcust<?= $no;?>" name="rpcust[]" type="hidden" value="<?= $rpcust;?>"
                                                                        class="form-control rightJustified" required>
                                                                </td>
                                                                <td><input id="netto<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="netto[]" type="text" value="<?=number_format($key->netto_cust,2,'.',','); ?>"
                                                                        class="form-control input-small rightJustified" required></td>
                                                                <td>
                                                                    <input id="bagian1<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="bagian1[]" type="text" value="<?= $key->bagian1;?>"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 1">
                                                                    <input id="fee1<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee1[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified" value="<?= $key->fee1_pr;?>">
                                                                    <input id="fee1rp<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee1rp[]" type="text" placeholder="Rp bagian 1"
                                                                        class="form-control input-small rightJustified" readonly value="<?= number_format($key->fee1_rp,2,'.',',');?>">
                                                                </td>
                                                                <td>
                                                                    <input id="bagian2<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="bagian2[]" type="text" value="<?= $key->bagian2;?>"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 2">
                                                                    <input id="fee2<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee2[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified" value="<?= $key->fee2_pr;?>">
                                                                    <input id="fee2rp<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee2rp[]" type="text" placeholder="Rp Bagian 2"
                                                                        class="form-control input-small rightJustified" readonly value="<?= number_format($key->fee2_rp,2,'.',',');?>">
                                                                </td>
                                                                <td>
                                                                    <input id="bagian3<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="bagian3[]" type="text" value="<?= $key->bagian3;?>"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 3">
                                                                    <input id="fee3<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee3[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified" value="<?= $key->fee3_pr;?>">
                                                                    <input id="fee3rp<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="fee3rp[]" type="text" placeholder="Rp Bagian 3"
                                                                        class="form-control input-small rightJustified" readonly value="<?= number_format($key->fee3_rp,2,'.',',');?>">
                                                                </td>
                                                                <td>
                                                                    <input id="profitrp<?= $no;?>" name="profitrp[]" type="text"
                                                                        placeholder="Profit Rp" readonly value="<?= number_format($key->profit_rp,2,'.',',');?>"
                                                                        class="form-control input-small rightJustified"
                                                                        oninput="totalline(<?= $no;?>)">
                                                                    <input id="netmarginrp<?= $no;?>"
                                                                        oninput="totalline(<?= $no;?>)" name="netmarginrp[]"
                                                                        type="text" placeholder="Net Margin Rp"
                                                                        class="form-control input-small rightJustified" readonly value="<?= number_format($key->netmargin_rp,2,'.',',');?>">
                                                                    <input id="Profit<?= $no;?>" oninput="totalline(<?= $no;?>)"
                                                                        name="Profit[]" placeholder="Profit %" type="text"
                                                                        class="form-control input-small rightJustified" readonly value="<?= number_format($key->profit_pr,2,'.',',');?>">
                                                                </td>
                                                            </tr>
                                                            <?php } 
                                                                }else{
                                                                $idx = 1;?>
                                                            <tr>
                                                                <td>
                                                                    <select name="kode[]" id="kode<?= $idx;?>"
                                                                        class="selectCustfilter form-control input-medium" onchange="getnamecust(this.value, <?= $idx;?>)">
                                                                        <option value="">--- Pilih Customer ---</option>
                                                                    </select>
                                                                    <input id="namecust<?= $idx;?>" name="namecust[]" type="hidden" value=""
                                                                        class="form-control rightJustified" required>
                                                                </td>
                                                                <td><input id="bruto<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="bruto[]" type="text" value="0"
                                                                        class="form-control input-small rightJustified" required></td>
                                                                <td>
                                                                    <input id="disc<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="disc[]" type="text" value="0"
                                                                        class="form-control input-xsmall rightJustified" required>
                                                                    <input id="rpcust<?= $idx;?>" name="rpcust[]" type="hidden" value="0"
                                                                        class="form-control rightJustified" required>
                                                                </td>
                                                                <td><input id="netto<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="netto[]" type="text" value="0"
                                                                        class="form-control input-small rightJustified" required></td>
                                                                <td>
                                                                    <input id="bagian1<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="bagian1[]" type="text"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 1">
                                                                    <input id="fee1<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee1[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified">
                                                                    <input id="fee1rp<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee1rp[]" type="text" placeholder="Rp bagian 1"
                                                                        class="form-control input-small rightJustified" readonly>
                                                                </td>
                                                                <td>
                                                                    <input id="bagian2<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="bagian2[]" type="text"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 2">
                                                                    <input id="fee2<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee2[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified">
                                                                    <input id="fee2rp<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee2rp[]" type="text" placeholder="Rp Bagian 2"
                                                                        class="form-control input-small rightJustified" readonly>
                                                                </td>
                                                                <td>
                                                                    <input id="bagian3<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="bagian3[]" type="text"
                                                                        class="form-control input-small rightJustified" placeholder="Bagian 3">
                                                                    <input id="fee3<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee3[]" type="text" placeholder="CN % "
                                                                        class="form-control input-small rightJustified">
                                                                    <input id="fee3rp<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="fee3rp[]" type="text" placeholder="Rp Bagian 3"
                                                                        class="form-control input-small rightJustified" readonly>
                                                                </td>
                                                                <td>
                                                                    <input id="profitrp<?= $idx;?>" name="profitrp[]" type="text"
                                                                        placeholder="Profit Rp" readonly
                                                                        class="form-control input-small rightJustified"
                                                                        oninput="totalline(<?= $idx;?>)">
                                                                    <input id="netmarginrp<?= $idx;?>"
                                                                        oninput="totalline(<?= $idx;?>)" name="netmarginrp[]"
                                                                        type="text" placeholder="Net Margin Rp"
                                                                        class="form-control input-small rightJustified" readonly>
                                                                    <input id="Profit<?= $idx;?>" oninput="totalline(<?= $idx;?>)"
                                                                        name="Profit[]" placeholder="Profit %" type="text"
                                                                        class="form-control input-small rightJustified" readonly>
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
                            <!--BARIS KE 4-->
                            <div class="row ms-0">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <?php if($data == []){?>
                                            <button type="button" onclick="tambah()" id='tambahitem' class="btn green"><i
                                                    class="fa fa-plus"></i> </button>
                                            <button type="button" onclick="hapus()" class="btn red"><i
                                                    class="fa fa-trash"></i></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!--BARIS KE 5-->
                            <div class="row ms-0">
                                <div class="well">
                                    <div class="mb-2 row">
                                        <div class="col-md-4">
                                            <button type="submit" id="btnSave" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                                            <a href="<?=base_url('inventory/datamaster/hargabarangpercustomer') ?>" class="btn btn-danger">Batal</a>
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
    <?php if($data != []){ ?> initailizeSelect2_barang_filter(<?=$data->cabang?>, <?=$data->gudang?>); <?php } ?>
    });
    function getstokgudang(){
        var gudang = $('#gudang').val();    
            $.ajax({
                url : "<?= base_url('inventory/datamaster/getcabang/') ?>" + gudang,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    initailizeSelect2_barang_filter(data.cabang, gudang);
                    initailizeSelect2_cust(data.cabang);
                    $('#cabang').val(data.cabang);
                    total();

                },
                error: function(data) {
                    swal('HARGA BARANG', 'Gagal narik data barang ...', '');
                }
            });
    }

    var idrow = 2;
    function tambah() {
        var gudang = $('#gudang').val();    
            $.ajax({
                url : "<?= base_url('inventory/datamaster/getcabang/') ?>" + gudang,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    initailizeSelect2_barang_filter(data.cabang);
                    initailizeSelect2_cust(data.cabang);

                },
                error: function(data) {
                    swal('HARGA BARANG', 'Gagal narik data barang ...', '');    
                }
            });
        
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

        var akun = "<select name='kode[]' id=kode" + idrowx +" class='selectCustfilter form-control' onchange='getnamecust(this.value," + idrowx +
            ")' required ><option value=''>--- Pilih Customer ---</option></select> <input name='namecust[]'    id=namecust" + idrowx + " type='hidden' class='form-control rightJustified' value='0' required ></td></tr>";
        td1.innerHTML = akun;
        td2.innerHTML = "<input name='bruto[]'    id=bruto" + idrowx + " oninput='totalline(" + idrowx +
            ")'  type='text' class='form-control rightJustified' value='0' required >";
        td3.innerHTML = "<input name='disc[]'    id=disc" + idrowx +
            " type='text' class='form-control rightJustified' value='0' oninput='totalline(" + idrowx + ")' required > <input name='rpcust[]'    id=rpcust" + idrowx + "  type='hidden' class='form-control rightJustified' value='0' required >";
        td4.innerHTML = "<input name='netto[]'    id=netto" + idrowx +
            " type='text' class='form-control rightJustified' value='0' oninput='totalline(" + idrowx + ")' required>";

        td5.innerHTML = "<input name='bagian1[]' placeholder='bagian 1' id=bagian1" + idrowx + " oninput='totalline(" +
        idrowx + ")'  type='text' class='form-control rightJustified'>      <input name='fee1[]'  id=fee1" + idrowx +
            " oninput='totalline(" + idrowx +
            ")'  type='text' class='form-control rightJustified' placeholder='CN %'>      <input name='fee1rp[]'  id=fee1rp" +
            idrowx + " oninput='totalline(" + idrowx +
            ")'  type='text' class='form-control rightJustified' placeholder='Rp Bagian 1' readonly>";

        td6.innerHTML = "<input name='bagian2[]' placeholder='bagian 2' id=bagian2" + idrowx + " oninput='totalline(" +
        idrowx +
            ")'  type='text' class='form-control rightJustified' >     <input name='fee2[]' placeholder='CN %'  id=fee2" +
            idrowx + " oninput='totalline(" + idrowx +
            ")'  type='text' class='form-control rightJustified'  >     <input name='fee2rp[]' id=fee2rp" + idrowx +
            " type='text' class='form-control rightJustified'  oninput='totalline(" + idrowx +
            ")' placeholder='Rp Bagian 2' readonly>";

        td7.innerHTML = "<input name='bagian3[]' placeholder='bagian 3' id=bagian3" + idrowx + "oninput='totalline(" +
        idrowx +
            ")'  type='text' class='form-control rightJustified'>      <input name='fee3[]' placeholder='CN %'   id=fee3" +
            idrowx + " type='text' class='form-control rightJustified' oninput='totalline(" + idrowx +
            ")'>        <input name='fee3rp[]'  id=fee3rp" + idrowx + " oninput='totalline(" + idrowx +
            ")'  type='text' class='form-control rightJustified' placeholder='Rp Bagian 3' readonly>";

        td8.innerHTML = "<input name='profitrp[]' placeholder='Profit Rp' id=profitrp" + idrowx +
            " type='text' class='form-control rightJustified' oninput='totalline(" + idrowx +
            ")' readonly>      <input name='netmarginrp[]' id=netmarginrp" + idrowx +
            " type='text' class='form-control rightJustified' oninput='totalline(" + idrowx +
            ")' placeholder='Net Margin Rp' readonly>     <input name='Profit[]' placeholder='Profit %'  id=Profit" +
            idrowx + " oninput='totalline(" + idrowx + ")'  type='text' class='form-control rightJustified' readonly>";
        idrow++;
    }

    function hapus() {
        if (idrow > 2) {
            var x = document.getElementById('datatable').deleteRow(idrow - 1);
            idrow--;
            total();
        }
        $('#btnSave').attr('disabled', false);
        $('#tambahitem').attr('disabled', false);
    }

    function getnamecust(str, id) {   
    var xhttp;     
    var vid = id;
    $('#kode'+vid).val();
    var barang = $('[name="barang"]').val();
    var cabang = $('[name="cabang"]').val();
    $.ajax({
            url : "<?= base_url('inventory/datamaster/getnamecust/') ?>" +str+'/'+barang+'/'+cabang,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {           
                //$('#namabarang'+vid).val(data.namabarang);
                $('#namecust'+vid).val(data.datacs.nama);
                if(data.datahbpc != null){
                    swal('HARGA BARANG', 'Customer '+data.datahbpc.nama_customer+' dan barang '+data.datahbpc.nama_barang+ ' sudah ada', '');
                    $('#btnSave').attr('disabled', true);
                    $('#tambahitem').attr('disabled', true);
                }else{
                    $('#btnSave').attr('disabled', false);
                    $('#tambahitem').attr('disabled', false);
                }
            }
        }); 
    
    }


    function showdatasup(str, id) {   
    var xhttp;     
    var vid = id;
    $('#bruto'+vid).val(0);
    $('#disc_sup'+vid).val(0);
    $('#Netto'+vid).val(0);
    var kd_barang = $('#barang').val();
    $.ajax({
            url : "<?php echo base_url();?>inventory_harga/showdatasup/?kode="+str+"&kd_barang="+kd_barang,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {           
                //$('#namabarang'+vid).val(data.namabarang);
                $('#bruto'+vid).val(data.hargabeli);
                $('#disc_sup'+vid).val(data.disc);
                $('#netto'+vid).val(data.netto_spl);
                totalline(vid);
            }
        }); 
    
    }

    $("input[type=text]").on("keypress", function(event) {
        // Cek jika tombol yang ditekan adalah tombol "Enter" (kode 13)
        if (event.which === 13) {
            // Mencegah pengiriman form
            event.preventDefault();
            swal('PENJUALAN DISIAPKAN', 'Gunakan Tab untuk Next dan mouse klik pada simpan ...', '');
        }
    });

    $('#submit_form').submit(function(e) {
        e.preventDefault();
        var barang = $('[name="barang"]').val();
        var kodeSpl = $('[name="kd_spl"]').val();
        var tanggal = $('[name="tanggal"]').val();
        var kode = $('[name="kode[]"]').val();
        if (barang == "" || kodeSpl =="") {
            swal('HARGA BARANG', 'NAMA BARANG atau Acuan belum diisi ...', '');
        } else {
            $.ajax({
                url: "<?= base_url('inventory/datamaster/hargabarangadd') ?>",
                data: $('#submit_form').serialize(),
                type: 'post',
                success: function(data) {
                    swal({
                        title: "HARGA BARANG",
                        html: "<p> Kode Barang   : <b>" + barang + "</b> </p>" +
                            "Tanggal :  " + tanggal,
                        type: "info",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        location.href = "<?= BASEURL . 'inventory/datamaster/hargabarangpercustomer' ?>";
                    });
                },
                error: function(data) {
                    swal('HARGA BARANG', 'Data gagal disimpan ...', '');
                }
            });
        }
        

    });

    function totalline1(bruto, disc_sup, netto_sup) {
        var bruto_sup1 = document.getElementById(bruto).value;
        var disc_sup1 = document.getElementById(disc_sup).value;

        var result_harga = bruto_sup1 - (disc_sup1 / 100) * bruto_sup1;

        document.getElementById(netto_sup).value = number_format(result_harga);
    }

    function totalline(id) {

        var table = document.getElementById('datatable');
        var row = table.rows[id];
        var hargamodal = $('#hargamodal').val();
        var vhargamodal = Number(hargamodal.replace(/[^0-9\.]+/g, ""));

        var jumlah_netto = $('#jumlah_netto').val();
        var vjumlah_netto = Number(jumlah_netto.replace(/[^0-9\.]+/g, ""));

        var bruto_cust = row.cells[1].children[0].value;
        var diskon_cust = row.cells[2].children[0].value;

        var vbrt_cust = Number(bruto_cust.replace(/[^0-9\.]+/g, ""));
        var vdisc_cust = Number(diskon_cust.replace(/[^0-9\.]+/g, ""));

        var netto_cust = vbrt_cust - (vdisc_cust / 100) * vbrt_cust;
        row.cells[3].children[0].value = number_format(netto_cust);

        var discCustPersen = vbrt_cust * (vdisc_cust / 100);
        row.cells[2].children[1].value = number_format(discCustPersen);

        var fee1 = row.cells[4].children[1].value;
        var fee1rp = ((fee1 / 100) * netto_cust);
        row.cells[4].children[2].value = number_format(fee1rp);

        var fee2 = row.cells[5].children[1].value;
        var fee2rp = ((fee2 / 100) * netto_cust);
        row.cells[5].children[2].value = number_format(fee2rp);

        var fee3 = row.cells[6].children[1].value;
        var fee3rp = ((fee3 / 100) * vbrt_cust);
        row.cells[6].children[2].value = number_format(fee3rp);

        var profitrp = netto_cust - fee1rp - fee2rp - fee3rp - vhargamodal - vjumlah_netto;
        row.cells[7].children[0].value = number_format(profitrp);
        var netmargin = netto_cust - fee1rp - fee2rp - fee3rp;
        row.cells[7].children[1].value = number_format(netmargin);

        var profit_pr = (profitrp / netmargin)*100;
        row.cells[7].children[2].value = number_format(profit_pr);

    }

    function infobarang() {
        var xhttp;
        var vid = $('#barang').val();
        var gudang = $('#gudang').val();    
        var cabang = $('#cabang').val();    

        $.ajax({
            url: "<?= base_url('inventory/datamaster/getkodespl1/') ?>" + vid+"/"+ cabang+"/"+ gudang,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                if(data.spl1){
                    $('#satuan').val(data.spl1.satuan);
                    $('#tgl_beli_akhir').val(data.spl1.tglbeliakhir);
                    $('#tgl_jual_akhir').val(data.spl1.tgljualakhir);
                    var option = $("<option selected></option>").val(data.spl1.kode_spl1).text(data.spl1.nama);
                    $('#supp1').append(option).trigger('change');
                    $('#bruto1').val(data.spl1.bruto_spl1);
                    $('#disc_sup1').val(data.spl1.disc_spl1);
                    $('#netto1').val(data.spl1.netto_spl1);
                    if(data.spl1.kode_spl == data.spl1.kode_spl1){
                        $('.radio1').attr('checked', 'checked');
                    }
                    $('#kode_spl').val(data.spl1.kode_spl);
                    $('#bruto_spl').val(data.spl1.bruto_spl);
                    $('#disc_spl').val(data.spl1.disc_spl);
                    $('#jumlah_netto').val(data.spl1.netto_spl);
                }

                if(data.spl2){
                    var option2 = $("<option selected></option>").val(data.spl2.kode_spl2).text(data.spl2.nama);
                    $('#supp2').append(option2).trigger('change');
                    $('#bruto2').val(data.spl2.bruto_spl2);
                    $('#disc_sup2').val(data.spl2.disc_spl2);
                    $('#netto2').val(data.spl2.netto_spl2);   
                    if(data.spl2.kode_spl == data.spl2.kode_spl2){
                        $('.radio2').attr('checked', 'checked');
                    }
                }

                if(data.spl3){
                    var option3 = $("<option selected></option>").val(data.spl3.kode_spl3).text(data.spl3.nama);
                    $('#supp3').append(option3).trigger('change');
                    $('#bruto3').val(data.spl3.bruto_spl3);
                    $('#disc_sup3').val(data.spl3.disc_spl3);
                    $('#netto3').val(data.spl3.netto_spl3);
                    if(data.spl3.kode_spl == data.spl3.kode_spl3){
                        $('.radio3').attr('checked', 'checked');
                    }
                }

                if(data.spl4){
                    var option4 = $("<option selected></option>").val(data.spl4.kode_spl4).text(data.spl4.nama);
                    $('#supp4').append(option4).trigger('change');
                    $('#bruto4').val(data.spl4.bruto_spl4);
                    $('#disc_sup4').val(data.spl4.disc_spl4);
                    $('#netto4').val(data.spl4.netto_spl4);
                    if(data.spl4.kode_spl == data.spl4.kode_spl4){
                        $('.radio4').attr('checked', 'checked');
                    }
                }
                
            }
        });
    }

    $("#datatable2").on('click', '#radio', function() {
        var currentRow = $(this).closest("tr");
        var test1 = $("#jumlah_netto").val(currentRow.find("td:eq(3) input[type='text']").val());
        var test = $("#kode_spl").val(currentRow.find("td:eq(0) option:selected").val());
        var test = $("#bruto_spl").val(currentRow.find("input[type='text']").val());
        var test = $("#disc_spl").val(currentRow.find("td:eq(2) input[type='text']").val());
        total();

    });

    function total(){
        var table = document.getElementById('datatable2');
        var rowCount = table.rows.length;

        tjumlah = 0;
        tdiskon = 0;

        for (var i = 1; i < rowCount; i++) {
            var row = table.rows[i];

            harga = row.cells[3].children[0].value;
            var harga1 = Number(harga.replace(/[^0-9\.]+/g, ""));

            tjumlah =  harga1;

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
            var diskon_cust = row.cells[2].children[0].value;

            var vbrt_cust = Number(bruto_cust.replace(/[^0-9\.]+/g, ""));
            var vdisc_cust = Number(diskon_cust.replace(/[^0-9\.]+/g, ""));

            var netto_cust = vbrt_cust - (vdisc_cust  / 100) * vbrt_cust;
            row.cells[3].children[0].value = number_format(netto_cust);
            

            var fee1 = row.cells[4].children[1].value;
            var fee1rp = ((fee1 / 100) * netto_cust);
            row.cells[4].children[2].value = number_format(fee1rp);

            var fee2 = row.cells[5].children[1].value;
            var fee2rp = ((fee2 / 100) * netto_cust);
            row.cells[5].children[2].value = number_format(fee2rp);

            var fee3 = row.cells[6].children[1].value;
            var fee3rp = ((fee3 / 100) * vbrt_cust);
            row.cells[6].children[2].value = number_format(fee3rp);

            var profitrp = netto_cust - fee1rp - fee2rp - fee3rp - vhargamodal - vjumlah_netto;
            row.cells[7].children[0].value = number_format(profitrp);
            var netmargin = netto_cust - fee1rp - fee2rp - fee3rp;
            row.cells[7].children[1].value = number_format(netmargin);

            var profit_pr = (profitrp / netmargin)*100;
            row.cells[7].children[2].value = number_format(profit_pr);
        }
    }

</script>