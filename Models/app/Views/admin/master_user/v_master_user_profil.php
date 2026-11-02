<?php
$model_Global =  model('Global_model','M_GLOBAL');
?>
<div class="containe-fluid">
    <div class="row profile">
        <div class="col-md-12">
            <div class="tabbable tabbable-custom tabbable-full-width">
                <ul class="nav nav-pills mb-3 pb-1 border-bottom border-2 border-primary" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#tab_1_1" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Beranda</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#tab_1_3" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Ubah Biodata</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1_1">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="list-unstyled profile-nav">
                                    <li>
                                        <img src="<?= BASEURL ?>assets/puser/<?= $avatar;?>" alt="" class="img-thumbnail rounded-circle">
                                    </li>

                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8 profile-info">
                                        <h1><?=$nama;?></h1>
                                        <p><?=$desc;?> </p>
                                        <p></p>
                                        <p>
                                            <a href="<?=$website;?>">
                                                <?=$website;?>
                                            </a>
                                        </p>
                                        <ul class="list-inline">
                                            <li>
                                                <i class="fa fa-envelope"></i> <?=$email;?>
                                            </li>
                                            <li>
                                                <i class="fa fa-phone"></i> <?=$hp;?>
                                            </li>
                                        </ul>

                                        <h4>Periode Data Aktif : 5-2023</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_1_3">
                        <style>
                            .control-label {
                                padding-left: 0px;
                                text-align: left;
                                width: 100%;
                            }
                        </style>
                        <div class="row profile-account">
                            <div class="col-md-3">
                                <div class="nav nav-tabs flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#tab_1" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                        <i class="fa fa-angle-double-right float-start"></i> Biodata</button>
                                    <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#tab_3" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                        <i class="fa fa-angle-double-right float-start"></i> Ubah Foto Profile
                                    </button>
                                    <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#tab_4" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                                        <i class="fa fa-angle-double-right float-start"></i> Ubah Password
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content">
                                    <div id="tab_1" class="tab-pane active">
                                        <form id="formbio" role="form" action="#">
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class="control-label">Nama Lengkap </label>
                                                    <input type="text" placeholder="" class="form-control" value="<?php echo $nama;?>" id="_nama" name="_nama" / required>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class="control-label">Nomor Handphone</label>
													<input type="text" placeholder="" class="form-control" value="<?php echo $hp;?>" id="_nohp" name="_nohp"/ required>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class="control-label">Email</label>
													<input type="text" placeholder="" class="form-control" value="<?php echo $email;?>" id="_email" name="_email"/ required>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class="control-label">Website</label>
													<input type="text" placeholder="" class="form-control" value="<?php echo $website;?>" id="_website" name="_website"/>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class="control-label">Deskripsi</label>
													<textarea class="form-control" rows="3" placeholder="" id="_desc" name="_desc" ><?php echo $desc;?></textarea>
                                                </div>
                                            </div>
                                            <div class="margiv-top-10">
                                                <a href="#" class="btn green" id="btnsimpanbio" onclick="save_bio()">
                                                    Simpan
                                                </a>
                                                <a href="#" class="btn default">
                                                    Batal
                                                </a>
                                            </div>
                                            <h4><span id="error1" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success1" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                                        </form>
                                    </div>

                                    <div id="tab_3" class="tab-pane">
                                        <p>Ubahlah foto di bagian ini.</p>										
                                        <form name="frmupload" id="frmupload" role="form" action="<?= base_url('master_user/saveimgprofileuser') ?>" method="post" enctype="multipart/form-data">
                                            <div>
                                            <input type="hidden" name="filefoto_old" id="filefoto_old" value="<?=$avatar?>">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                        <img src="<?= BASEURL ?>assets/puser/<?= $avatar;?>" alt=""/>
                                                    </div>
                                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                    </div>
                                                    <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new">
                                                                    Pilih Foto
                                                            </span>
                                                            <span class="fileinput-exists">
                                                                    Ganti
                                                            </span>
                                                            <input type="file" name="filefoto" id="filefoto">
                                                            
                                                        </span>
                                                        <a href="#" class="btn default fileinput-exists" data-dismiss="fileinput">
                                                                Hapus
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="clearfix margin-top-10">
                                                    <span class="label label-danger">
                                                            CATATAN!
                                                    </span>
                                                    <span>
                                                            Ukuran file foto yang akan diupload tidak lebih dari 500Kb.
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="progressNumber" style="display:none;"></div>
                                            <progress id="prog" value="0" max="100.0" style="display:none;"></progress>
                                            <div class="margin-top-10">
                                                <input type="submit" class="btn green" id="btnsimpanbio" value="Kirim" />
                                                <a href="#" class="btn default">
                                                        Batal
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="tab_4" class="tab-pane">
                                        <form id="frmpw" role="form" action="#">
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class=" control-label">Password Sekarang </label>
                                                    <input type="password" class="form-control" id="_password" name="_password"/>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class=" control-label">Password Baru </label>
                                                    <input type="password" class="form-control" id="_passwordbaru" name="_passwordbaru"/>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <label class=" control-label">Ulangi Password Baru </label>
                                                    <input type="password" onchange="checkPass();" class="form-control" id="_confirm_password" name="_confirm_password"/>
                                                </div>
                                            </div>
                                            <div class="margiv-top-10">
                                                <a href="#" class="btn green" id="btngantipsw" onclick="save_psw()">
                                                    Ganti Password
                                                </a>
                                                <a href="#" class="btn default">
                                                    Batal
                                                </a>
                                            </div>
                                            <h4><span id="error4" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success4" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function save_bio(){				
    $.ajax({
        url: "<?= base_url('master_user/savebioprofileuser') ?>",
	    data: $('#formbio').serialize(),
        type:'POST',
        success:function(data){
            swal('BIODATA USER', data,'');
        },
        error:function(data){
            swal('BIODATA USER', 'Data Gagal Di Update','');
        }
    });        
}

function checkPass(){
    var pass1 = $('#_passwordbaru').val();
    var pass2 = $('#_confirm_password').val();

    var goodColor = "#66cc66";
    var badColor = "#ff6666";

    if(pass1 !="" && pass2 !=""){
        if(pass1 == pass2){          

        }else{
            swal('PASSWORD USER', 'Password Tidak sama','');
        }
    }else{
        swal('PASSWORD USER', 'Password baru atau password lama harus diisi','');
    }
}

function save_psw(){
    $("#loader").hide();      	
    var passnum = $("#_password").val();
    var passnumbaru = $("#_passwordbaru").val();

    if(passnum.length > 1 && passnumbaru.length > 2) {           
        $.ajax({
            type: "POST",
            url: "<?= base_url('master_user/savepwprofileuser') ?>",
	        data: $('#frmpw').serialize(),
            success: function(html) {
                if(html == 'ok')
                {
                    swal({
                        title: "PASSWORD USER",
                        html: "<p>Password Sudah Berhasil Diubah...</p>",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('master_user/userprofile') ?>";
                    });
                }
                else
                {
                    swal({
                        title: "PASSWORD USER",
                        html: "<p>Password Lama Salah...</p>",
                        confirmButtonText: "OK"
                    }).then((value) => {
                        window.location.href = "<?= base_url('master_user/userprofile') ?>";
                    });
                }
            }
        });


    }

};
</script>