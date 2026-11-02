<?php

$session = \Config\Services::session();
    if ($session->get('username') === '') {
        $session->setFlashdata('sukses', 'Ooops... Anda belum login');
    }
?>
<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title><?= $title ?? '' ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= BASEURL ?>public/assets/images/favicon.png">

        <!-- Bootstrap Css -->
        <link href="<?= BASEURL ?>public/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="<?= BASEURL ?>public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="<?= BASEURL ?>public/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- sweet alert css-->
        <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.css" rel="stylesheet" type="text/css"> 

    </head>

    <body onload="noBack();" onpageshow="if (event.persisted) noBack();" onunload="">
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Sign in to continue to AJF.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="<?= BASEURL ?>public/assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div class="auth-logo">
                                    <a href="" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="<?= BASEURL ?>assets/images/logo-light.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="<?= BASEURL ?>public/assets/images/logo.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" action="<?= base_url('authentication') ?>" method="post">
        
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" onchange="getcabang(this.value)" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Cabang</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <select name="cabang" class="form-control" id="cabang" placeholder="Cabang" aria-label="cabang" disabled required>
                                                    <option value="all"></option>
                                                </select>
                                            </div>
                                        </div>
                
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password" aria-label="Password" aria-describedby="password-addon" required>
                                                <button class="btn btn-light" onclick="showPassword()" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                        </div>
                                    </form>
                                </div>
            
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

        <!-- JAVASCRIPT -->
        <script src="<?= BASEURL ?>public/assets/libs/jquery/jquery.min.js"></script>
        <script src="<?= BASEURL ?>public/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?= BASEURL ?>public/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="<?= BASEURL ?>public/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="<?= BASEURL ?>public/assets/libs/node-waves/waves.min.js"></script>
        <!-- sweets alert JS-->
        <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
        <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.js"></script>
        
        <!-- App js -->
        <script src="<?= BASEURL ?>public/assets/js/app.js"></script>
    </body>
</html>
<?php if ( $session->getFlashdata('ntf1')) {?>
    <script>
        swal({
            title: "Oops...",
            html: "<?php echo $session->getFlashdata('ntf1'); ?>",
            type: "error",
            confirmButtonText: "OK"
        })
    </script>
<?php } ?>
<script>
    window.history.forward();
    function noBack() { window.history.forward(); }
    
    var isCtrl = false;
    document.onkeyup=function(e)
    {
        if(e.which == 17)
        isCtrl=false;
    }
    document.onkeydown=function(e)
    {
        if(e.which == 17)
        isCtrl=true;
        if((e.which == 85 && isCtrl == true) || (e.which == 67 && isCtrl == true))
        {
            return false;
        }
    }


    var isNS = (navigator.appName == "Netscape") ? 1 : 0;
    if(navigator.appName == "Netscape") document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
    function mischandler(){
        return false;
    }
    function mousehandler(e){
        var myevent = (isNS) ? e : event;
        var eventbutton = (isNS) ? myevent.which : myevent.button;
        if((eventbutton==2)||(eventbutton==3)) return false;
    }
    document.oncontextmenu = mischandler;
    document.onmousedown = mousehandler;
    document.onmouseup = mousehandler;
    
    history.pushState(null, document.title, location.href);
    window.addEventListener('popstate', function (event)
    {
      history.pushState(null, document.title, location.href);
    });
    function getcabang(id) { 
        if(id!=""){
            $.ajax({
                type: "post",
                url: "<?= base_url('autgetcbg/') ?>" + id,
                dataType: "json",
                success: function(res) {
                    if(res.namacabang=='***'){
                        document.getElementById('password').disabled=true;  
                        document.getElementById('username').focus();
                        swal('','Nama User Salah / Tidak Ditemukan...','');
                    } else {
                        var cbg = $("<option selected></option>").val(res.kode).text(res.namacabang);
                        $('#cabang').append(cbg).trigger('change');
                    
                    }
                    
                }
            });    
        }	
    } 

    function showPassword() {
        var x = document.getElementById("password");
        if (x.type == "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>