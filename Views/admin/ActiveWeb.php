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
                                        <h5 class="text-primary">Lock screen system website !</h5>
                                        <p>Fungsi ini untuk mengunci website agar tidak bisa diakses oleh orang lain</p>

                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= BASEURL ?>public/assets/images/profile-img.png" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="p-2">
                                <form class="form-horizontal" action="" method="post">

                                    <div class="">
                                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                            <input class="form-check-input" type="checkbox" id="SwitchCheckSizelg" onclick="active(this.value)" <?= ($DataIdentity->active_website == 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="SwitchCheckSizelg">Lock Screen System</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="mt-5 text-center">
                        <p>always smile when doing something</p>
                        <p>© <script>
                                document.write(new Date().getFullYear())
                            </script> Created By Iman Haditssyahputra</p>
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
    <!-- <script src="<?= BASEURL ?>public/assets/js/app.js"></script> -->
</body>

</html>
<script>
    function active(str) {
        $.ajax({
            type: "post",
            url: "<?= base_url('activeCheck/') ?>" + str,
            dataType: "json",
            success: function(res) {
                swal('', 'Website berhasil dilock...', '');
            }
        });
    }
</script>