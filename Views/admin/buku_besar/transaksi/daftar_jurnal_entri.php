<div class="container-fluid">
    <?= main_header('Buku Besar', 'Daftar Jurnal') ?>

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-reorder"></i>Data Jurnal
            </div>
            <div class="tools">
                <!--a href="javascript:;" class="reload">
											</a-->

            </div>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
            <form id="frmjurnal" action="#" class="form-horizontal" method="post">
                <div class="form-body">
                    <!--h4 class="form-section">Deskripsi</h4-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Nomor Bukti</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="" name="nomorbukti" id="nomorbukti" value="JU.2023.05.000001" readonly>
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
                                        <input id="tanggal" name="tanggal" class="form-control input-medium" type="date" value="2023-05-04" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Cabang</label>
                                    <div class="col-md-6">
                                        <select id="cabang" name="cabang" class="form-control input-mediumx select2me" data-placeholder="Pilih..." onkeypress="return tabE(this,event)" required>
                                            <option value="01">PUSAT</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Keterangan</label>
                                    <div class="col-md-9">
                                        <!--textarea class="form-control" rows="2" id="keterangan" name="keterangan" onkeypress="return tabE(this,event)"></textarea-->
                                        <input type="text" class="form-control" placeholder="" name="keterangan" id="keterangan" onkeypress="return tabE(this,event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Jenis Jurnal</label>
                                    <div class="col-md-6">
                                        <select id="jenis" name="jenis" class="bs-select form-select" data-show-subtext="true" data-placeholder="Pilih..." onkeypress="return tabE(this,event)">
                                            <option data-subtext="JURNAL UMUM" value="JU">JU</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">No. Ref</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="" name="noref" value="" id="noref" onkeypress="return tabE(this,event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-borderedx table-condensed">
                                    <theads>
                                        <tr>
                                            <th width="20%" style="text-align: center">Kode Akun</th>
                                            <th width="40%" style="text-align: center">Uraian</th>
                                            <th width="20%" style="text-align: center">Debet</th>
                                            <th width="20%" style="text-align: center">Kredit</th>
                                        </tr>
                                    </theads>
                                </table>
                                <div class="bodycontainer- scrollable-">
                                    <table id="datatable" class="table table-hoverx table-stripedx table-borderedx table-condensed table-scrollable">
                                        <tbody>
                                            <tr>
                                                <td width="20%">
                                                    <select name="akun[]" id="akun0" class="select2_el form-select">
                                                        <option value="">--- Pilih Akun ---</option>
                                                    </select>
                                                </td>
                                                <td width="40%">
                                                    <input name="ket[]" id="ket1" type="text" class="form-select " size="100%" onkeypress="return tabE(this,event)">
                                                </td>
                                                <td width="20%"><input name="debet[]" id="debet1" ; type="text" class="form-control rightJustified" size="40%" value="0" onChange="total();formatCurrency(this)" onkeypress="return tabE(this,event);formatCurrency(this)"></td>
                                                <td width="20%"><input name="kredit[]" id="kredit1" type="text" class="form-control rightJustified" size="40%" value="0" onChange="total();formatCurrency(this)" onkeypress="return tabE(this,event);formatCurrency(this)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <table class="table table-condensed">
                                    <tfoot>
                                        <tr>
                                            <td width="20%"><button type="button" onclick="addRow('datatable', 'daftar_entri_tr')" class="btn green"><i class="fa fa-plus"></i> </button>
                                                <button type="button" onclick="deleteLastRow('datatable')" class="btn red"><i class="fa fa-trash"></i></button>
                                            </td>
                                            <td width="40%" align="center">
                                                <font color="red"><b><input type="text" class="form-control" id="_selisih" disabled></b></font>
                                            </td>

                                            <td width="20%" align="right">
                                                <font color="red"><b><input type="text" class="form-control rightJustified" id="_jumdebet" disabled></b></font>
                                            </td>
                                            <td width="20%" align="right">
                                                <font color="red"><b><input type="text" class="form-control rightJustified" id="_jumkredit" disabled></b></font>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="form-actions">
                        <button id="btnsimpan" type="button" onclick="save()" class="btn blue"><i class="fa fa-save"></i> Simpan</button>
                        <!--button id="btncetak" type="button" class="btn yellow" onclick="window.open(_urlcetak())"><i class="fa fa-print"></i> Cetak</button-->
                        <!--button id="btncetak" type="button" class="btn yellow" onclick="window.open(''))"><i class="fa fa-print"></i> Cetak</button-->
                        <button type="button" class="btn green" onclick="this.form.reset();location.reload();"><i class="fa fa-edit"></i> Data Baru</button>
                    </div>
                    <h2><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h2>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    //Current Element Delete & Data Add & Last Emement Delete Common Function
    let appendData = {
        'daftar_entri_tr': '<td width="20%"><select name="akun[]" id="akun0" class="select2_el form-select select2-hidden-accessible" tabindex="-1" aria-hidden="true"><option value="">--- Pilih Akun ---</option></select></td> <td width = "40%" > <input name = "ket[]"id = "ket1" type = "text" class = "form-control " size = "100%" onkeypress = "return tabE(this,event)" > </td><td width="20%"><input name="debet[]" id="debet1" ;="" type="text" class="form-control rightJustified" size="40%" value="0" onchange="total();formatCurrency(this)" onkeypress="return tabE(this,event);formatCurrency(this)"></td> <td width = "20%" > <input name = "kredit[]" id = "kredit1"  type = "text" class = "form-control rightJustified" size = "40%" value = "0" onchange = "total();formatCurrency(this)" onkeypress = "return tabE(this,event);formatCurrency(this)" > </td>',
    }

    //Add Table Element
    function addRow(tableId, appendDataKey) {
        let tableBody = document.querySelector(`#${tableId} tbody`);
        let tr = document.createElement('tr');
        tr.innerHTML = appendData[appendDataKey];
        tableBody.appendChild(tr);
    }


    //Delete Table Element Last Row
    function deleteLastRow(tableId) {
        let tableBody = document.querySelector(`#${tableId} tbody`);
        if (tableBody.lastChild.id == '') {
            tableBody.removeChild(tableBody.lastChild);
        }
    }
</script>