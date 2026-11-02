<?php
error_reporting(0);
$model_Global =  model('Global_model', 'M_GLOBAL');
$db = db_connect();
foreach ($report as $result) {
    $bulan[] = $result->bulan;
    $value[] = (float)$result->jumlah;
}
foreach ($reportJumDataPenjualan as $row) {
    $bulan1[] = $row->bulan;
    $value1[] = $row->jumlah;
}

foreach ($RpFKPenjualan as $result) {
    $bulanRpFK[] = $result->bulan;
    $valueRpFK[] = (float)$result->jumlah;
}
foreach ($RpMarketingPenjualan as $result) {
    $bulanRpPerMarketing[] = $result->bulan;
    $valueRpPerMarketing[] = (float)$result->jumlah;
}
$session = session();
$level = $session->get('level');
$DataGroup =  $db->table("ms_modul_grup")->getWhere(['nomor' => $level])->getRow();
if (strlen($HitungFaktur) == 2) {
    $persenfaktur = ($HitungFaktur / 120 * 100);
} else if (strlen($HitungFaktur) == 3) {
    $persenfaktur = ($HitungFaktur / 1200 * 100);
} else if (strlen($HitungFaktur) == 4) {
    $persenfaktur = ($HitungFaktur / 12000 * 100);
}

if ($level == 1 || $level == 2 || $level == 8) {
?>
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="row info-card">
                <div class="col-md-3 col-12">
                    <div class="card mini-stats-wid">
                        <div class="card-body" style="background-color: #27a9e3;">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h2 class="mb-2 text-end text-white fw-bold"><?php echo number_format($asset); ?></h2>
                                    <h4 class="text-end text-white fw-light fw-medium">ASET SAAT INI</h4>
                                </div>
                            </div>
                        </div>
                        <a href="<?= BASEURL . 'inventory/datamaster/barang' ?>" class="p-1 text-white text-uppercase" style="background-color: #208dbe;">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="card mini-stats-wid">
                        <div class="card-body" style="background-color: #28b779;">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h2 class="mb-2 text-end text-white fw-bold"><?php echo number_format($piutang); ?></h2>
                                    <h4 class="text-end text-white fw-light fw-medium">PIUTANG USAHA (Rp)</h4>
                                </div>
                            </div>
                        </div>
                        <a href="<?= BASEURL . 'penjualan/transaksi/fakturpenjualan' ?>" class="p-1 text-white text-uppercase" style="background-color: #10a062;">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="card mini-stats-wid">
                        <div class="card-body" style="background-color: #ffb848;">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h2 class="mb-2 text-end text-white fw-bold"><?php echo number_format($hutang); ?></h2>
                                    <h4 class="text-end text-white fw-light fw-medium">HUTANG USAHA (Rp)</h4>
                                </div>
                            </div>
                        </div>
                        <a href="<?= BASEURL . 'pembelian/transaksi/fakturpembelian' ?>" class="p-1 text-white text-uppercase" style="background-color: #cb871b;">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="card mini-stats-wid">
                        <div class="card-body" style="background-color: #e7191b;">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h2 class="mb-2 text-end text-white fw-bold"><?php echo number_format($lr); ?></h2>
                                    <h4 class="text-end text-white fw-light fw-medium">LABA RUGI TAHUN INI (Rp)</h4>
                                </div>
                            </div>
                        </div>
                        <a href="<?= BASEURL . 'bukubesar/laporan' ?>" class="p-1 text-white text-uppercase" style="background-color: #bc0d0e;">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="card">
                <h4 class="card-title p-2 mb-0" style="background-color: #35aa47; color: #fff;"> <i class="fa-solid fa-chart-column"></i> Penjualan Bulanan</h4>
                <div class="card-body" style="border: 1px solid #35aa47;">
                    <div id="sales-chart"></div>
                </div>
            </div>

        <?php } else if ($level == 3 || $level == 6) { ?>
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Dashboards</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-4">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-3">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p><?= $session->get('nama_lengkap'); ?></p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="<?= BASEURL ?>assets/images/profile-img.png" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <img src="<?= BASEURL ?>assets/puser/<?= $DataUser->avatar; ?>" alt="" class="img-thumbnail rounded-circle">
                                    </div>
                                    <h5 class="font-size-15 text-truncate"><?= $session->get('nama_lengkap'); ?></h5>
                                    <p class="text-muted mb-0 text-truncate"><?= $DataGroup->nmgrup; ?></p>
                                </div>
                                <div class="col-sm-8">
                                    <div class="pt-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5 class="font-size-15"><?= number_format($HitungSofile); ?></h5>
                                                <p class="text-muted mb-0">Jumlah Order</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="font-size-15"><?= number_format($HitungFaktur); ?></h5>
                                                <p class="text-muted mb-0">Jumlah Faktur</p>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <a href="<?= BASEURL ?>master_user/userprofile" class="btn btn-primary waves-effect waves-light btn-sm">View Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Jumlah Penjualan Bulan ini</h4>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-muted"><?= $periode ?></p>
                                    <h3>Rp <?= number_format($HitungFaktur); ?></h3>

                                    <div class="mt-4">
                                        <a href="<?= BASEURL . 'penjualan/transaksi/pesananmasuk' ?>" class="btn btn-primary waves-effect waves-light btn-sm">View Order <i class="mdi mdi-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-0">Data jumlah berdasarkan bulan ini</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Jumlah Order</p>
                                            <h4 class="mb-0"><?= number_format($HitungSofile); ?></h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bx-copy-alt font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Jumlah Faktur</p>
                                            <h4 class="mb-0"><?= number_format($HitungFaktur); ?></h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center ">
                                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Jumlah Retur</p>
                                            <h4 class="mb-0"><?= number_format($HitungRetur); ?></h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="bx bx-reset font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="card">
                        <div class="card-body">
                            <?php if ($level == 3) { ?>

                                <div id="column-charts" data-colors='["--bs-primary"]' dir="ltr"></div>
                            <?php }
                            if ($level == 6) { ?>
                                <div class="d-sm-flex flex-wrap">
                                    <h4 class="card-title mb-4">Jumlah Penjualan <script>
                                            document.write(new Date().getFullYear())
                                        </script>
                                    </h4>
                                    <div class="ms-auto">
                                        <ul class="nav nav-pills">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#">Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="fullmonth-column-chart" class="apex-charts" data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($level == 6) { ?>
                <div class="row">
                    <div class="card">
                        <h4 class="card-title p-2 mb-0" style="background-color: #35aa47; color: #fff;"> <i class="fa-solid fa-chart-column"></i> Penjualan Bulanan</h4>
                        <div class="card-body" style="border: 1px solid #35aa47;">
                            <div id="sales-chart-Rp-PerUser"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- end row -->
        <?php } ?>
        </div>
    </div>
    <!-- end row -->

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="<?= BASEURL ?>public/assets/js/highcharts.js"></script>
    <script src="<?= BASEURL ?>public/assets/js/exporting.js"></script>

    <!-- tui charts plugins -->
    <script src="<?= BASEURL ?>public/assets/libs/tui-chart/tui-chart-all.min.js"></script>

    <!-- tui charts map -->
    <script src="<?= BASEURL ?>public/assets/libs/tui-chart/maps/usa.js"></script>

    <script type="text/javascript">
        $(function() {
            $('#sales-chart').highcharts({
                chart: {
                    type: 'column',
                    margin: 75,
                    options3d: {
                        enabled: false,
                        alpha: 10,
                        beta: 25,
                        depth: 70
                    }
                },

                title: {
                    text: '',
                    style: {
                        fontSize: '18px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                subtitle: {
                    text: '<?php echo $tahun; ?>',
                    style: {
                        fontSize: '15px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        depth: 25
                    }
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: <?php echo json_encode($bulan); ?>
                },
                exporting: {
                    enabled: false
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    },
                },
                tooltip: {
                    formatter: function() {
                        return 'The value for <b>' + this.x + '</b> is <b>' + Highcharts.numberFormat(this.y, 0) + '</b>, in ' + this.series.name;
                    }
                },

                series: [{
                        name: 'Bulan',
                        data: <?php echo json_encode($value); ?>,
                        shadow: true,
                        dataLabels: {
                            enabled: true,
                            color: '#045396',
                            align: 'center',
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 0);
                            }, // one decimal
                            y: 0, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },


                ],

            });
        });
    </script>

    <script>
        var options = {
                chart: {
                    height: 360,
                    type: "bar",
                    stacked: !0,
                    toolbar: {
                        show: !1
                    },
                    zoom: {
                        enabled: !0
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: !1,
                        columnWidth: "15%",
                        endingShape: "rounded"
                    }
                },
                dataLabels: {
                    enabled: !1
                },
                series: [{
                    name: "Jumlah",
                    data: <?php echo json_encode($value1); ?>,
                }],
                xaxis: {
                    categories: <?php echo json_encode($bulan1); ?>
                },
                colors: ["#556ee6", "#f1b44c", "#34c38f"],
                legend: {
                    position: "bottom"
                },
                fill: {
                    opacity: 1
                }
            },
            chart = new ApexCharts(document.querySelector("#fullmonth-column-chart"), options);
        chart.render();
        optionsbar = {
            chart: {
                height: 200,
                type: "radialBar",
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    dataLabels: {
                        name: {
                            fontSize: "13px",
                            color: void 0,
                            offsetY: 60
                        },
                        value: {
                            offsetY: 22,
                            fontSize: "16px",
                            color: void 0,
                            formatter: function(e) {
                                return e + "%"
                            }
                        }
                    }
                }
            },
            colors: ["#556ee6"],
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    shadeIntensity: .15,
                    inverseColors: !1,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 65, 91]
                }
            },
            stroke: {
                dashArray: 4
            },
            series: [<?= number_format($persenfaktur); ?>],
            labels: ["Penjualan"]
        };
        (chart = new ApexCharts(document.querySelector("#radialBar-chart"), optionsbar)).render();
    </script>

    <script type="text/javascript">
        $(function() {
            $('#sales-chart-Rp-PerUser').highcharts({
                chart: {
                    type: 'column',
                    margin: 75,
                    options3d: {
                        enabled: false,
                        alpha: 10,
                        beta: 25,
                        depth: 70
                    }
                },

                title: {
                    text: '',
                    style: {
                        fontSize: '18px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                subtitle: {
                    text: '<?php echo $tahun; ?>',
                    style: {
                        fontSize: '15px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        depth: 25
                    }
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: <?php echo json_encode($bulan); ?>
                },
                exporting: {
                    enabled: false
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    },
                },
                tooltip: {
                    formatter: function() {
                        return 'The value for <b>' + this.x + '</b> is <b>' + Highcharts.numberFormat(this.y, 0) + '</b>, in ' + this.series.name;
                    }
                },

                series: [{
                        name: 'Bulan',
                        data: <?php echo json_encode($valueRpFK); ?>,
                        shadow: true,
                        dataLabels: {
                            enabled: true,
                            color: '#045396',
                            align: 'center',
                            formatter: function() {
                                return Highcharts.numberFormat(this.y, 0);
                            }, // one decimal
                            y: 0, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    },


                ],

            });
        });
    </script>

    <script type="text/javascript">
        var columnChartWidth = $("#column-charts").width(),
            container = document.getElementById("column-charts"),
            data = {
                categories: <?php echo json_encode($bulan); ?>,
                series: [{
                    name: "Penjualan <?= $session->get('nama_lengkap'); ?>",
                    data: <?php echo json_encode($valueRpPerMarketing); ?>
                }, ],
            },
            options = {
                chart: {
                    width: columnChartWidth,
                    height: 380,
                    title: "Penjualan Bulanan",
                    format: "1,000",
                },
                yAxis: {
                    title: "Jumlah",
                    align: "left"
                },
                xAxis: {
                    title: "Bulan"
                },
                legend: {
                    align: "top"
                },
            },
            theme = {
                chart: {
                    background: {
                        color: "#fff",
                        opacity: 0
                    }
                },
                title: {
                    color: "#8791af"
                },
                xAxis: {
                    title: {
                        color: "#8791af"
                    },
                    label: {
                        color: "#8791af"
                    },
                    tickColor: "#8791af",
                },
                yAxis: {
                    title: {
                        color: "#8791af"
                    },
                    label: {
                        color: "#8791af"
                    },
                    tickColor: "#8791af",
                },
                plot: {
                    lineColor: "rgba(166, 176, 207, 0.1)"
                },
                legend: {
                    label: {
                        color: "#8791af"
                    }
                },
                series: {
                    colors: ["#556ee6"]
                },
            };
        tui.chart.registerTheme("myTheme", theme), (options.theme = "myTheme");
        var columnChart = tui.chart.columnChart(container, data, options);
        $(window).resize(function() {
            (columnChartWidth = $("#column-charts").width()),
            columnChart.resize({
                width: columnChartWidth,
                height: 350
            });
        });
    </script>