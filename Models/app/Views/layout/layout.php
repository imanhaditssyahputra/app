<!doctype html>
<html lang="en">

<?php

use CodeIgniter\View;

$session = session();
$model_Global =  model('Global_model', 'M_GLOBAL');
$periodecek = $model_Global->_periodebulan() . '-' . $model_Global->_periodetahun();
$tgln = $model_Global->tgln();
$data  = [
    'model_Global' => $model_Global,
    'username' => $session->get('username'),
    'level' => $session->get('level'),
    'cabang' => $session->get('cabang'),
    'periode' => $periodecek,
    'convdate' => $tgln,
];
?>

<head>

    <meta charset="utf-8" />
    <title><?= $title ?? '' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= BASEURL ?>public/assets/images/favicon.png">

    <!-- Bootstrap Css -->
    <link href="<?= BASEURL ?>public/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= BASEURL ?>public/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/ajf-css.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASEURL ?>public/assets/css/style-metronic.css" rel="stylesheet" type="text/css" />
    <!-- Data Table Css  -->
    <link href="<?= BASEURL ?>public/assets/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <!-- <link rel="stylesheet" href="cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> -->
    <!-- App Css-->
    <link href="<?= BASEURL ?>public/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <script src="<?= BASEURL ?>public/assets/datatable/jquery-3.5.1.js"></script>
    <!-- sweet alert css-->
    <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.css" rel="stylesheet" type="text/css">

    <!-- select2 CSS -->
    <link href="<?= BASEURL ?>public/assets/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- tui charts Css -->
    <link href="assets/libs/tui-chart/tui-chart.min.css" rel="stylesheet" type="text/css" />
    <?php
    if (!empty($css)) {
        foreach ($css as $key => $value) {
            echo '<link href="' . BASEURL . $value . '" rel="stylesheet" type="text/css">';
        }
    }
    ?>
</head>

<body data-sidebar="dark">
    <!-- Loader -->
    <!-- <div id="preloader">
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
    </div> -->
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php echo view('layout/header', $data) ?>
        <?php echo view('layout/sidebar', $data) ?>

        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <?php echo view($body) ?>

                    <!-- footer section  -->
                    <?php echo view('layout/footer', $data) ?>
                </div>
            </div>
            <!-- End Page-content -->

        </div>
    </div>

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <!-- <script src="<?= BASEURL ?>public/assets/js/jquery-3.5.1.js"></script> -->

    <script src="<?= BASEURL ?>public/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/libs/node-waves/waves.min.js"></script>
    <!-- <script src="cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->

    <!-- sweets alert JS-->
    <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/plugins/sweet-alert2/sweetalert2.js"></script>

    <!--Select2 Js -->
    <script src="<?= BASEURL ?>public/assets/libs/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="<?= BASEURL ?>public/assets/js/pages/ecommerce-select2.init.js" type="text/javascript"></script>

    <?php
    if (!empty($js)) {
        foreach ($js as $key => $value) {
            echo '<script src="' . BASEURL . $value . '"></script>';
        }
    } else {
    ?>
        <script src="<?= BASEURL ?>public/assets/datatable/jquery.dataTables.min.js"></script>
    <?php } ?>

    <!-- App js -->
    <script>
        ! function(s) {
            "use strict";
            var e, t = localStorage.getItem("language"),
                n = "en";

            function a(e) {
                document.getElementById("header-lang-img") && ("en" == e ? document.getElementById("header-lang-img").src = "<?= BASEURL ?>public/assets/images/flags/us.jpg" : "sp" == e ? document.getElementById("header-lang-img").src = "<?= BASEURL ?>public/assets/images/flags/spain.jpg" : "gr" == e ? document.getElementById("header-lang-img").src = "<?= BASEURL ?>public/assets/images/flags/germany.jpg" : "it" == e ? document.getElementById("header-lang-img").src = "<?= BASEURL ?>public/assets/images/flags/italy.jpg" : "ru" == e && (document.getElementById("header-lang-img").src = "<?= BASEURL ?>public/assets/images/flags/russia.jpg"), localStorage.setItem("language", e), null == (t = localStorage.getItem("language")) && a(n), s.getJSON("<?= BASEURL ?>lang/" + t + ".json", function(e) {
                    s("html").attr("lang", t), s.each(e, function(e, t) {
                        "head" === e && s(document).attr("title", t.title), s("[key='" + e + "']").text(t)
                    })
                }))
            }

            function c() {
                for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, s = e.length; t < s; t++) "nav-item dropdown active" === e[t].parentElement.getAttribute("class") && (e[t].parentElement.classList.remove("active"), null !== e[t].nextElementSibling && e[t].nextElementSibling.classList.remove("show"))
            }

            function r(e) {
                1 == s("#light-mode-switch").prop("checked") && "light-mode-switch" === e ? (s("html").removeAttr("dir"), s("#dark-mode-switch").prop("checked", !1), s("#rtl-mode-switch").prop("checked", !1), s("#dark-rtl-mode-switch").prop("checked", !1), s("#bootstrap-style").attr("href", "<?= BASEURL ?>css/bootstrap.min.css"), s("#app-style").attr("href", "<?= BASEURL ?>css/app.min.css"), sessionStorage.setItem("is_visited", "light-mode-switch")) : 1 == s("#dark-mode-switch").prop("checked") && "dark-mode-switch" === e ? (s("html").removeAttr("dir"), s("#light-mode-switch").prop("checked", !1), s("#rtl-mode-switch").prop("checked", !1), s("#dark-rtl-mode-switch").prop("checked", !1), s("#bootstrap-style").attr("href", "<?= BASEURL ?>css/bootstrap-dark.min.css"), s("#app-style").attr("href", "<?= BASEURL ?>css/app-dark.min.css"), sessionStorage.setItem("is_visited", "dark-mode-switch")) : 1 == s("#rtl-mode-switch").prop("checked") && "rtl-mode-switch" === e ? (s("#light-mode-switch").prop("checked", !1), s("#dark-mode-switch").prop("checked", !1), s("#dark-rtl-mode-switch").prop("checked", !1), s("#bootstrap-style").attr("href", "<?= BASEURL ?>css/bootstrap-rtl.min.css"), s("#app-style").attr("href", "<?= BASEURL ?>css/app-rtl.min.css"), s("html").attr("dir", "rtl"), sessionStorage.setItem("is_visited", "rtl-mode-switch")) : 1 == s("#dark-rtl-mode-switch").prop("checked") && "dark-rtl-mode-switch" === e && (s("#light-mode-switch").prop("checked", !1), s("#rtl-mode-switch").prop("checked", !1), s("#dark-mode-switch").prop("checked", !1), s("#bootstrap-style").attr("href", "<?= BASEURL ?>css/bootstrap-dark-rtl.min.css"), s("#app-style").attr("href", "<?= BASEURL ?>css/app-dark-rtl.min.css"), s("html").attr("dir", "rtl"), sessionStorage.setItem("is_visited", "dark-rtl-mode-switch"))
            }

            function l() {
                document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || (console.log("pressed"), s("body").removeClass("fullscreen-enable"))
            }
            s("#side-menu").metisMenu(), s("#vertical-menu-btn").on("click", function(e) {
                    e.preventDefault(), s("body").toggleClass("sidebar-enable"), 992 <= s(window).width() ? s("body").toggleClass("vertical-collpsed") : s("body").removeClass("vertical-collpsed")
                }), s("#sidebar-menu a").each(function() {
                    var e = window.location.href.split(/[?#]/)[0];
                    this.href == e && (s(this).addClass("active"), s(this).parent().addClass("mm-active"), s(this).parent().parent().addClass("mm-show"), s(this).parent().parent().prev().addClass("mm-active"), s(this).parent().parent().parent().addClass("mm-active"), s(this).parent().parent().parent().parent().addClass("mm-show"), s(this).parent().parent().parent().parent().parent().addClass("mm-active"))
                }), s(document).ready(function() {
                    var e;
                    0 < s("#sidebar-menu").length && 0 < s("#sidebar-menu .mm-active .active").length && (300 < (e = s("#sidebar-menu .mm-active .active").offset().top) && (e -= 300, s(".vertical-menu .simplebar-content-wrapper").animate({
                        scrollTop: e
                    }, "slow")))
                }), s(".navbar-nav a").each(function() {
                    var e = window.location.href.split(/[?#]/)[0];
                    this.href == e && (s(this).addClass("active"), s(this).parent().addClass("active"), s(this).parent().parent().addClass("active"), s(this).parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().parent().addClass("active"), s(this).parent().parent().parent().parent().parent().parent().addClass("active"))
                }), s('[data-bs-toggle="fullscreen"]').on("click", function(e) {
                    e.preventDefault(), s("body").toggleClass("fullscreen-enable"), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
                }), document.addEventListener("fullscreenchange", l), document.addEventListener("webkitfullscreenchange", l), document.addEventListener("mozfullscreenchange", l), s(".right-bar-toggle").on("click", function(e) {
                    s("body").toggleClass("right-bar-enabled")
                }), s(document).on("click", "body", function(e) {
                    0 < s(e.target).closest(".right-bar-toggle, .right-bar").length || s("body").removeClass("right-bar-enabled")
                }),
                function() {
                    if (document.getElementById("topnav-menu-content")) {
                        for (var e = document.getElementById("topnav-menu-content").getElementsByTagName("a"), t = 0, s = e.length; t < s; t++) e[t].onclick = function(e) {
                            "#" === e.target.getAttribute("href") && (e.target.parentElement.classList.toggle("active"), e.target.nextElementSibling.classList.toggle("show"))
                        };
                        window.addEventListener("resize", c)
                    }
                }(), [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(e) {
                    return new bootstrap.Tooltip(e)
                }), [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function(e) {
                    return new bootstrap.Popover(e)
                }), [].slice.call(document.querySelectorAll(".offcanvas")).map(function(e) {
                    return new bootstrap.Offcanvas(e)
                }), window.sessionStorage && ((e = sessionStorage.getItem("is_visited")) ? (s(".right-bar input:checkbox").prop("checked", !1), s("#" + e).prop("checked", !0), r(e)) : sessionStorage.setItem("is_visited", "light-mode-switch")), s("#light-mode-switch, #dark-mode-switch, #rtl-mode-switch, #dark-rtl-mode-switch").on("change", function(e) {
                    r(e.target.id)
                }), s("#password-addon").on("click", function() {
                    0 < s(this).siblings("input").length && ("password" == s(this).siblings("input").attr("type") ? s(this).siblings("input").attr("type", "input") : s(this).siblings("input").attr("type", "password"))
                }), null != t && t !== n && a(t), s(".language").on("click", function(e) {
                    a(s(this).attr("data-lang"))
                }), s(window).on("load", function() {
                    s("#status").fadeOut(), s("#preloader").delay(350).fadeOut("slow")
                }), Waves.init(), s("#checkAll").on("change", function() {
                    s(".table-check .form-check-input").prop("checked", s(this).prop("checked"))
                }), s(".table-check .form-check-input").change(function() {
                    s(".table-check .form-check-input:checked").length == s(".table-check .form-check-input").length ? s("#checkAll").prop("checked", !0) : s("#checkAll").prop("checked", !1)
                })
        }(jQuery);
    </script>

    <!-- =============================Basic Design Start============================== -->
    <!-- Main Header Area Start -->
    <?php
    function main_header($pagetitle, $pagename, $second_link_name = '', $third_link_name = '', $second_url = '', $third_url = '')
    {
    ?>
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title">
                    <?= $pagetitle ?> <small><?= $pagename ?></small>
                </h3>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="<?= BASEURL ?>">Awal</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?= $second_url != '' ? BASEURL . $second_url : '#' ?>"><?= $second_link_name != '' ? $second_link_name : $pagetitle ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?= $third_url != '' ? BASEURL . $third_url : '' ?>"><?= $third_link_name != '' ? '' : 'Data' ?> <?= $third_link_name != '' ? $third_link_name : $pagename ?></a>
                    </li>
                </ul>
            </div>
        </div>
    <?php
    }
    ?>
    <!-- Main Header Area End -->

    <!-- Main Periode Area Start -->
    <?php
    function main_periode($periodecaption, $is_show_periodebadge = 1, $tgl1 = '', $tgl2 = '')
    {
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet" style="margin-bottom: 15px;">
                    <div class="portlet-title">
                        <div class="caption">
                            <?= $periodecaption ?>
                            <?php
                            if ($is_show_periodebadge == 1) {
                                $model_Global =  model('Global_model', 'M_GLOBAL');
                                $nbulan = $model_Global->_periodebulan();
                                $periode = 'Periode ' . $nbulan . '-' . $model_Global->_periodetahun();
                            ?>

                                <span class="badge bg-warning text-light" style="border-radius: 0px !important;">
                                    <?= $periode ?> </span>
                            <?php
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <!-- Main Periode Area End -->

    <!-- Main Add Button Modal Area Start -->
    <?php
    function main_button($btnname, $is_show_refreshbtn = 1, $is_show_datebtn = 1, $url = '', $id = '', $isShowFilterGudang = 1)
    {
    ?>

        <div class="row">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-body">
                        <div class="table-toolbar d-flex justify-content-between">
                            <div class="d-flex">
                                <?php
                                if ($btnname != '') { ?>

                                    <?php if (!empty($id)) { ?>
                                        <button type="button" data-bs-target="#<?= $id ?>" id="main_button" data-bs-toggle='modal' class="btn fw-bold me-2 add-btn">
                                            <i class="fa fa-plus"></i>
                                            <?= $btnname ?>
                                        </button>
                                    <?php } else if (!empty($url)) {
                                    ?>
                                        <a href="<?= base_url() . $url ?>" class="btn add-btn fw-bold me-2" id="main_button">
                                            <i class="fa fa-plus"></i>
                                            <?= $btnname ?>
                                        </a>
                                    <?php
                                    } else { ?>
                                        <button type="button" class="btn add-btn fw-bold me-2" id="main_button">
                                            <i class="fa fa-plus"></i>
                                            <?= $btnname ?>
                                        </button>
                                    <?php } ?>
                                <?php }
                                if ($is_show_refreshbtn == 1) {
                                ?>
                                    <button class="btn btn-default refresh-btn fw-bold me-2"><i class="fa-solid fa-refresh fw-bold"></i> Refresh</button>
                                <?php } ?>
                            </div>
                            <div class="btn-group" role="group">
                                <?php
                                if ($is_show_datebtn == 1) {
                                ?>
                                    <button id="btnGroupVerticalDrop1" type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Data <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">
                                            Periode Data
                                        </button>
                                    </div>
                                <?php
                                } else
                                if ($isShowFilterGudang == 1) {
                                ?>
                                    <select class="form-select select2" id="gudang" name="gudang" data-show-subtext="true" data-placeholder="Pilih Gudang" onchange="GetFilterBarangGudang()">
                                        <option value=""></option>
                                        <option value="0">ALL</option>
                                        <?php
                                        $db = db_connect();
                                        $ShowGudang = $db->table('inv_gudang')->get()->getResult();
                                        foreach ($ShowGudang as $value) {
                                        ?>
                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                        <?php } ?>

                                    </select>
                                <?php } else if ($isShowFilterGudang == 2) { ?>
                                    <select class="form-select select2" id="cabang" name="cabang" data-show-subtext="true" data-placeholder="Pilih Cabang" onchange="GetFilterBarangCabang()">
                                        <option value=""></option>
                                        <option value="0">ALL</option>
                                        <?php
                                        $db = db_connect();
                                        $ShowCabang = $db->table('ms_unit')->get()->getResult();
                                        foreach ($ShowCabang as $value) {
                                        ?>
                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                        <?php } ?>

                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-small">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Periode Data</h4>
                    </div>
                    <div class="modal-body">
                        <form action="#" class="form-horizontal">
                            <div class="row mb-4">
                                <label for="horizontal-mulai-input" class="col-sm-3 col-form-label">Mulai</label>
                                <div class="col-sm-9">
                                    <input id="tanggal1" name="tanggal1" class="form-control input-medium" type="date" value="hh/bb/tttt">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="horizontal-sd-input" class="col-sm-3 col-form-label">s/d</label>
                                <div class="col-sm-9">
                                    <input id="tanggal2" name="tanggal2" class="form-control input-medium" type="date" value="hh/bb/tttt">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <p align="center">
                            <button type="button" id="btnfilter" class="btn green" onclick="filterdata()" data-bs-dismiss="modal">Buka Data</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <!-- Main Add Button Modal Area End -->
    <!-- =============================Basic Design End============================== -->

    <script>
        $(document).ready(function() {
            arrbulan = ['JANUARI', 'PEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOPEMBER', 'DESEMBER'];
            date = new Date();
            bulan = date.getMonth();
            tahun = date.getFullYear();
            str_bln = '<?= $periodecek ?>';
            str_date = arrbulan[bulan] + "-" + tahun;
            if (str_bln != str_date) {
                swal({
                    title: "Oops...",
                    html: "<p>Bulan berbeda dengan bulan ini, Silahkan cek periode bulan dan nomor bukti untuk mengurutkan nomor</p>",
                    type: "info"
                })
                document.getElementById("main_button").disabled = true;
            }
        });
        //Delete Table Element Current Row
        function deleteCurrentRow(e) {
            if (e.closest('tr').id == '') {
                e.closest('tr').remove();
            }
        }
    </script>

    <script>
        let isTrue = true;

        let errorMessage = {
            'barang_valid': ['kategori', 'namebarang', 'hargajual', 'gudang', 'rak', 'ppn', 'merk', 'satuan', 'stok', 'stokmin', 'stokmax', 'cabang'],
            'gudang_valid': ['nameErr'],
        }

        // Data Insert Update Function
        function submitData(fromId, getInputId, url, validationObject = '', modalId = '', reload = '') {
            if (isTrue) {
                $(`#${fromId}`).submit(function(e) {
                    e.preventDefault();
                    let getId = $(`#${getInputId}`).val();
                    var formData = new FormData(this);
                    $.ajax({
                        type: "POST",
                        url: `<?= BASEURL ?>${url}/` + getId,
                        data: formData,
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            isTrue = false;

                            if (data.error) {
                                let validation = errorMessage[`${validationObject}`];
                                if (validation.length > 0) {
                                    validation.forEach(function(value) {
                                        if (data[value] != undefined) {
                                            $(`#${value}`).html(data[value]);
                                        } else {
                                            $(`#${value}`).html('');
                                        }
                                    })
                                }
                            } else {
                                if (modalId != '') {
                                    $(`#${modalId}`).modal('hide');
                                }

                                if (reload != '') {
                                    if (reload.startsWith('http')) {
                                        window.location.href = `${reload}`;
                                    } else {
                                        table_reload(reload)
                                    }
                                }
                            }
                        }
                    });
                })
            }
        }

        //Data Delete Funcion
        function daleteData(url, idNumber, tableName) {
            if (confirm('Are you sure?')) {
                $.ajax({
                    type: "post",
                    url: `<?= BASEURL ?>${url}/` + idNumber,
                    dataType: "json",
                    success: function(res) {
                        table_reload(tableName);
                    }
                });
            }
        }
    </script>

    <script>
        function table_reload(table) {
            $('#' + table).DataTable().ajax.reload();
        }

        $('.refresh-btn').click(function(e) {
            e.preventDefault();
            table_reload('table');
        });
    </script>

    <script>
        function SelectInModal() {
            $(".SelectInModal").select2({
                dropdownParent: $('#tambahDataModal'),
            })
        }
        //Search Supplier
        initailizeSelect2_supp();

        function initailizeSelect2_supp() {
            $(".select2me").select2({
                ajax: {
                    url: "<?= base_url('searchsupp') ?>",
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Search akun
        initailizeSelect2_akun();

        function initailizeSelect2_akun() {
            $(".selectakun").select2({
                ajax: {
                    url: "<?= base_url('searchakun') ?>",
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Search Gudang
        initailizeSelect2_gudang();

        function initailizeSelect2_gudang() {
            $(".select_gudang").select2({
                ajax: {
                    url: "<?= base_url('searchgudang') ?>",
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Search Barang Filter Cabang and Gudang
        function initailizeSelect2_barang_filter(cbg, gudang) {
            $(".barang_filter").select2({
                ajax: {
                    url: "<?= base_url('searchbarangfilter/') ?>" + cbg + "/" + gudang,
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Search Barang Filter Cabang 
        function initailizeSelect2_barang_filterCabang(cbg) {
            $(".barang_filter_cabang").select2({
                ajax: {
                    url: "<?= base_url('searchbarangfiltercabang/') ?>" + cbg,
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        // Search Gudang Filter
        initailizeSelect2_gudangFilter(cabang);

        function initailizeSelect2_gudangFilter(cabang) {
            $(".select_gudang_filter").select2({
                ajax: {
                    url: "<?= base_url('searchgudangfilter/') ?>" + cabang,
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Search Customer
        function initailizeSelect2_cust(cbg) {
            $(".selectCustfilter").select2({
                ajax: {
                    url: "<?= base_url('searchcust/') ?>" + cbg,
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        function initailizeSelect2_akunRL() {
            $(".select_akun_rl").select2({
                ajax: {
                    url: "<?= base_url('searchakunrl') ?>",
                    type: "post",
                    dataType: 'json',
                    minimumInputLength: 3,
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },

                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }

        //Number Format
        function number_format(number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            var n = !isFinite(+number) ? 0 : +number
            var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
            var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
            var s = ''

            var toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec)
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
            }

            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }

        function checkPing() {
            var pingStatusElement = document.getElementById('pingStatus');
            pingStatusElement.innerText = 'Mengukur ping...';

            $.ajax({
                url: '<?= base_url('pingdata') ?>', // Panggil metode getPing di controller
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 0) {
                        pingStatusElement.innerText = response.pingResult;

                        if (response.pingTime <= 50) {
                            pingStatusElement.style.color = 'green'; // Jika ping bagus (<= 50 ms), warna ijo
                            $('#colorping').val('0');
                        } else if (response.pingTime <= 100) {
                            pingStatusElement.style.color = 'yellow'; // Jika ping kurang (<= 100 ms), warna kuning
                            $('#colorping').val('1');
                        } else {
                            pingStatusElement.style.color = 'blue'; // Jika ping tidak stabil (> 100 ms), warna biru
                            $('#colorping').val('2');
                        }
                    } else {
                        pingStatusElement.innerText = 'Gagal mengukur ping';
                        $('#colorping').val('3');
                    }
                },
                error: function() {
                    pingStatusElement.innerText = 'Gagal mengukur ping';
                }
            });
        }
    </script>

</body>


</html>


<!-- 
    ftp.bdisp.net
    root@internet-bhai.bdisp.net
    ^fNJ_o#wS.S{ 
-->