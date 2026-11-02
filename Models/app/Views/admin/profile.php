<style>
    .navLink.active {
        border-top: 3px solid #d12610 !important;
        margin-top: 0;
        position: relative;
    }

    .nav_icon {
        width: 37px;
        height: 37px;
        display: inline-block;
        color: #b9cbd5;
        font-size: 15px;
        padding: 12px 10px 10px 8px;
        margin: 0 8px 0 0;
        text-align: center;
        background: #e0eaf0 !important;
    }

    .profile_nav {
        font-size: 13px;
        color: #000;
        display: block;
        /* background: #f0f6fa; */
        /* border-left: solid 2px #c4d5df; */
    }

    /* .profile_nav */

    .profile_nav button {
        margin-bottom: 2px;
        background: #f0f6fa !important;
        border-left: solid 2px #c4d5df !important;
    }

    .profile_nav button.active {
        background: #169ef4 !important;
        border-left: solid 2px #0c91e5 !important;
    }

    .profile_nav button.active span i {
        background: #0c91e5 !important;
    }
</style>
<div class="row">
    <div class="col-12">
        <!-- ========== -->
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link  navLink active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true" style="font-size: 15px; font-weight: 400; color: #424242;">Beranda</button>
                <button class="nav-link navLink " id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false" style="font-size: 15px; font-weight: 400; color: #424242;">Biodata</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="list-unstyled profile-nav">
                            <li>
                                <img src="https://ajf.sistemkesehatan.id/assets/puser/foto.jpg" class="img-responsive" alt="">
                                <!-- a href="#" class="profile-edit">
													 edit
												</a -->
                            </li>

                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-8 profile-info">
                                <h1 style="    color: #383839; font-size: 24px; font-weight: 400;margin: 0 0 10px 0;">Anugerah Jaya Fordic</h1>
                                <p>
                                </p>
                                <p>
                                    <a href="">
                                    </a>
                                </p>
                                <ul class="list-inline d-flex">
                                    <li>
                                        <i class="fa fa-envelope me-3" style="color: #b5c1c9; font-size: 15px;"></i>
                                    </li>
                                    <li>
                                        <i class="fa fa-phone" style="color: #b5c1c9; font-size: 15px;"></i>
                                    </li>

                                </ul>
                            </div>
                            <!--end col-md-8-->

                        </div>
                        <!--end row-->

                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <!-- ================ -->
                <div class="d-flex align-items-start">
                    <div class="row" style="width: 100%;">
                        <div class="col-3">
                            <div class="profile_nav nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true" style="padding: 0px 0px; width: 100%; margin-bottom: 2px;">
                                    <span class="float-start">
                                        <i class="fa fa-cog nav_icon"></i> Biodata
                                    </span>
                                </button>
                                <button class="profile_nav nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false" style="padding: 0px 0px; width: 100%; margin-bottom: 2px;">
                                    <span class="float-start">
                                        <i class="fa fa-lock nav_icon"></i> Mengganti Foto
                                    </span>
                                </button>
                                <button class="profile_nav nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false" style="padding: 0px 0px; width: 100%;">
                                    <span class="float-start">
                                        <i class="fa fa-lock nav_icon"></i> Mengganti Password
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <div class="tab-content">
                                        <div id="tab_1-1" class="tab-pane active">
                                            <form role="form" action="#">
                                                <div class="form-group mt-2">
                                                    <label class="form-label float-start">Nama Lengkap</label>
                                                    <input type="text" placeholder="" class="form-control" value="Anugerah Jaya Fordic" id="_nama" name="_nama">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="form-label float-start">Nomor Handphone</label>
                                                    <input type="text" placeholder="" class="form-control" value="" id="_nohp" name="_nohp">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="form-label float-start">Email</label>
                                                    <input type="text" placeholder="" class="form-control" value="" id="_email" name="_email">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="form-label float-start">Website</label>
                                                    <input type="text" placeholder="" class="form-control" value="" id="_website" name="_website">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="form-label float-start">Deskripsi</label>
                                                    <textarea class="form-control" rows="3" placeholder="" id="_desc" name="_desc"></textarea>
                                                </div>
                                                <h4><span id="error" style="display:none; color:#F00">Terjadi Kesalahan... </span> <span id="success" style="display:none; color:#0C0">Data sudah disimpan...</span></h4>
                                                <div class="margiv-top-10">
                                                    <a href="#" class="btn green" id="btnsimpanbio" onclick="save_bio()">
                                                        Simpan
                                                    </a>
                                                    <a href="#" class="btn default">
                                                        Batal
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <div class="tab-content">
                                        <div id="tab_2-2" class="tab-pane active">
                                            <p>
                                                Ubahlah foto di bagian ini.
                                            </p>
                                            <div>
                                                <div class="">
                                                    <img src="https://ajf.sistemkesehatan.id/assets/puser/foto.jpg" alt="">
                                                </div>
                                                <div class="mt-1">
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
                                                    <div class="clearfix margin-top-10">
                                                        <span class="label label-danger">
                                                            CATATAN!
                                                        </span>
                                                        <span>
                                                            Ukuran file foto yang akan diupload tidak lebih dari 500Kb.
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <input type="submit" class="btn green" value="Kirim">
                                                <a href="#" class="btn default">
                                                    Batal
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                    <div class="tab-content">
                                        <div id="tab_3-3" class="tab-pane active">
                                            <form action="#">
                                                <div class="form-group mt-2">
                                                    <label class="control-label form-label float-start">Password Sekarang</label>
                                                    <input type="password" class="form-control" id="_password" name="_password">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="control-label form-label float-start">Password Baru</label>
                                                    <input type="password" class="form-control" id="_passwordbaru" name="_passwordbaru">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label class="control-label form-label float-start">Ulangi Password Baru</label>
                                                    <input type="password" onkeyup="checkPass();" class="form-control" id="_confirm_password" name="_confirm_password">
                                                </div>


                                                <div class="err" id="results"></div>
                                                <div class="margin-top-10">


                                                    <a href="#" class="btn green" id="btngantipsw" onclick="save_psw()">
                                                        Ganti Password
                                                    </a>
                                                    <a href="#" class="btn default">
                                                        Batal
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ================ -->
            </div>
        </div>
        <!-- ========== -->
    </div>
</div>