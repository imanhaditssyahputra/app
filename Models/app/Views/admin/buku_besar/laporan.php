<div class="container-fluid">
    <?= main_header('Buku Besar ', 'Laporan') ?>

    <div class="note note-success">
        <p>
            Berisi Laporan Akuntansi ( Laporan Keuangan )
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
            <form id="frmlaporan" class="form-horizontal form-bordered1">
                 <input id="tableId" type="text" value="0" name="id">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Nama Laporan</label>
                                    <div class="col-md-9">
                                        <select id="idlap" name="idlap" class="select2me bs-select form-select" data-show-subtext="true" data-placeholder="Pilih...">
                                            <optgroup label="Laporan Keuangan">
                                                <option data-subtext="101" value="">Select</option>
                                                <option data-subtext="101" value="101">Daftar Jurnal</option>
                                                <option data-subtext="102" value="102">Buku Besar</option>
                                                <option data-subtext="103" value="103">Neraca Saldo</option>
                                                <option data-subtext="104" value="104">Neraca Akhir</option>
                                                <option data-subtext="105" value="105">Laba Rugi</option>
                                            </optgroup>
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
                                    <label class="col-md-3 control-label">Mulai</label>
                                    <div class="col-md-3">
                                        <input id="tanggal1" name="tanggal1" class="form-control input-medium" type="date" value="2023-05-04" />
                                    </div>
                                    <label class="col-md-3 control-label">s/d</label>
                                    <div class="col-md-3">
                                        <input id="tanggal2" name="tanggal2" class="form-control input-medium" type="date" value="2023-05-04" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <div class="row">
                                    <label class="col-md-3 control-label">Kode Akun Perkiraan</label>
                                    <div class="col-md-9">
                                        <select name="perk" id="perk" class="select2_el form-select">
                                            <option value="">--- Pilih Akun ---</option>
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
                                        <select id="cabang" name="cabang" class="select2me bs-select form-select" data-show-subtext="true">
                                            <optgroup label="Daftar Cabang">
                                                <option data-subtext="" value="">-- Semua --</option>
                                                <option data-subtext="" value="01">PUSAT</option>
                                                <option data-subtext="" value="02">CAB. KRWNG</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-actions fluid">
                        <div class="col-md-3"></div>
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green print_laporan">Tampilkan</button>
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

<script>
    $(document).ready(function(){
        $("#frmlaporan").submit(function(){
          alert('hello');
            //  $id = $('#tableId').val();
            // $.ajax({
            //     type: "post",
            //     url: "</?= base_url('bukubesar/laporan-add')?>",
            //     dataType: "json",
            //     success: function (res) {
            //         console.log(res);
                    
            //     }
            // });
        })
    })
</script>