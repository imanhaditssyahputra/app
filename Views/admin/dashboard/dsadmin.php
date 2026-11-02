
<!-- tui charts Css -->
<link href="assets/libs/tui-chart/tui-chart.min.css" rel="stylesheet" type="text/css" />
<!-- start page title -->
<!-- end row -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="d-flex align-items-center">
            <img src="<?= BASEURL ?>assets/puser/<?= $avatar; ?>" alt="" class="avatar-sm rounded">
            <div class="ms-3 flex-grow-1">
                <h5 class="mb-2 card-title">Hello, <?= $nama_lengkap; ?></h5>
                <p class="text-muted mb-0">Welcome Back</p>
            </div>
            <div>
                <a href="<?= BASEURL ?>master_user/userprofile" class="btn btn-primary"><i class="bx bx-user align-middle"></i> Profile</a>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->
<?php if ($cabang == '01') { ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card mini-stats-wid" style="background-color: gainsboro;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">ASET SAAT INI</p>
                            <h4 class="mb-0"><?php echo number_format($asset); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <a href="<?= BASEURL . 'inventory/datamaster/barang' ?>" class="p-1 text-uppercase">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                </div>
            </div>
        </div><!--end col-->
        <div class="col-lg-4">
            <div class="card mini-stats-wid" style="background-color: aquamarine;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">PIUTANG USAHA (Rp)</p>
                            <h4 class="mb-0"><?php echo number_format($piutang); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <a href="<?= BASEURL . 'penjualan/transaksi/fakturpenjualan' ?>" class="p-1 text-uppercase">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                </div>
            </div>
        </div><!--end col-->
        <div class="col-lg-4">
            <div class="card mini-stats-wid" style="background-color: lightgrey;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">HUTANG USAHA (Rp)</p>
                            <h4 class="mb-0"><?php echo number_format($hutang); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <a href="<?= BASEURL . 'pembelian/transaksi/fakturpembelian' ?>" class="p-1 text-uppercase">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                </div>
            </div>
        </div><!--end col-->
        <!-- <div class="col-lg-3">
            <div class="card mini-stats-wid" style="background-color: silver;">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">LABA RUGI TAHUN INI (Rp)</p>
                            <h4 class="mb-0"><?php echo number_format($lr); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top py-3">
                    <a href="<?= BASEURL . 'bukubesar/laporan' ?>" class="p-1 text-uppercase">Lihat Rinci <i class="fa-regular fa-circle-right float-end"></i></a>
                </div>
            </div>
        </div> -->
        <!--end col-->
    </div><!--end row-->
<?php } ?>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button class="nav-link active fs-6 fw-bold" id="nav-penjualan-tab" data-bs-toggle="tab" data-bs-target="#ds-penjualan" type="button" role="tab" aria-controls="nav-penjualan" aria-selected="false">Dashboard penjualan</button>
        <button class="nav-link  fs-6 fw-bold" id="nav-pembelian-tab" data-bs-toggle="tab" data-bs-target="#ds-pembelian" type="button" role="tab" aria-controls="nav-pembelian" aria-selected="true">Dashboard Pembelian</button>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent" style="background-color: lightyellow; padding:20px; border-radius:10px; box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
    <div class="tab-pane fade show active" id="ds-penjualan" role="tabpanel" aria-labelledby="nav-penjualan-tab">
        <div class="row">
            <div class="col-lg-8">
                <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                    <div class="card-body">
                        <div class="d-sm-flex flex-wrap row">
                            <div class="col-md-4">
                                
                                <h4 class="card-title mb-4">Statistics Sales Region</h4>
                            </div>
                            <div class="col-md-8 text-end">
                                <button id="combine-data" class="btn">Gabungkan Data</button>
                                <button id="reset-data" class="btn">Reset Data</button>
                            </div>
                        </div>
                        <div id="sales-chart"></div>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-lg-4">
                <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Jumlah Penjualan Bulan ini</h4>
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="text-muted mb-4"><?= $periode ?></p>
                                <h3 class="mb-4">
                                    Rp <?= number_format($HitungFKPenjualan); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <?php if ($jumlahCurrentjual > $jumlahPrevjual) { ?>
                                        <span class="text-success me-2"> <?= number_format($HitungFKPenjualanPercen) ?> %
                                            <i class="mdi mdi-arrow-up"></i>
                                        </span>
                                    <?php } else { ?>
                                        <span class="text-danger me-2"> <?= number_format($HitungFKPenjualanPercen) ?> %
                                            <i class="mdi mdi-arrow-down"></i>
                                        </span>
                                    <?php } ?>

                                </h3>

                                <div class="mb-4">
                                    <a href="<?= BASEURL . 'penjualan/transaksi/fakturpenjualan' ?>" class="btn btn-primary waves-effect waves-light btn-sm">View Penjualan <i class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted ">Data jumlah berdasarkan bulan ini</p>
                    </div>
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->
        <div class="row">
            <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Statistics Sales User</h4>
                    </div>
                    <div id="sales-chart-all-user"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                <div class="card-body" style="max-width: 100%; overflow-x: auto;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Statistics Sales Customer</h4>
                    </div>
                    <div id="sales-charts-cs" style="min-width: 5000px; height:500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade " id="ds-pembelian" role="tabpanel" aria-labelledby="nav-pembelian-tab">
        <div class="row">
            <div class="col-lg-8">
                <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-sm-flex flex-wrap">
                                <h4 class="card-title mb-4">Statistics Purchase</h4>
                            </div>
                            <div id="purchase-chart"></div>
                        </div>
                    </div>
                </div>
            </div><!--end col-->
            <div class="col-lg-4">
                    <div class="card" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Jumlah Pembelian Bulan ini</h4>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-muted"><?= $periode ?></p>
                                    <h3>Rp <?= number_format($HitungFKPembelian); ?></h3>

                                    <div class="mt-4">
                                        <a href="<?= BASEURL . 'pembelian/transaksi/fakturpembelian' ?>" class="btn btn-primary waves-effect waves-light btn-sm">View Pembelian <i class="mdi mdi-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-0">Data jumlah berdasarkan bulan ini</p>
                        </div>
                    </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->
    </div>
</div>

<script>
$(function() {
    // Data awal
    var dataPusat = <?php echo json_encode($valuecbgpusat); ?>;
    var dataKarawang = <?php echo json_encode($valuecbgkrwg); ?>;

    // Inisialisasi chart
    var chart = Highcharts.chart('sales-chart', {
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
            categories: <?php echo json_encode($GetBulan); ?>,
        },
        exporting: {
            enabled: true
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
            name: 'Region Pusat',
            data: dataPusat,
            shadow: true,
            dataLabels: {
                enabled: true,
                color: '#045396',
                align: 'center',
                formatter: function() {
                    return Highcharts.numberFormat(this.y, 0);
                },
                y: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        {
            name: 'Region Karawang',
            data: dataKarawang,
            shadow: true,
            dataLabels: {
                enabled: true,
                color: '#045396',
                align: 'center',
                formatter: function() {
                    return Highcharts.numberFormat(this.y, 0);
                },
                y: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });

    // Fungsi untuk menggabungkan data
    function combineData() {
        // Hitung total gabungan per bulan
        var combinedData = dataPusat.map(function(value, index) {
            return value + dataKarawang[index];
        });

        // Hapus semua series yang ada
        while(chart.series.length > 0) {
            chart.series[0].remove(false);
        }

        // Tambahkan series gabungan
        chart.addSeries({
            name: 'Total Gabungan',
            data: combinedData,
            shadow: true,
            dataLabels: {
                enabled: true,
                color: '#045396',
                align: 'center',
                formatter: function() {
                    return Highcharts.numberFormat(this.y, 0);
                },
                y: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }, true); // true untuk redraw
    }

    // Fungsi untuk reset ke data awal
    function resetData() {
        // Hapus semua series
        while(chart.series.length > 0) {
            chart.series[0].remove(false);
        }

        // Tambahkan kembali series awal
        chart.addSeries({
            name: 'Region Pusat',
            data: dataPusat,
            shadow: true,
            dataLabels: {
                enabled: true,
                color: '#045396',
                align: 'center',
                formatter: function() {
                    return Highcharts.numberFormat(this.y, 0);
                },
                y: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }, false);

        chart.addSeries({
            name: 'Region Karawang',
            data: dataKarawang,
            shadow: true,
            dataLabels: {
                enabled: true,
                color: '#045396',
                align: 'center',
                formatter: function() {
                    return Highcharts.numberFormat(this.y, 0);
                },
                y: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }, true); // redraw
    }

    // Event handler tombol
    $('#combine-data').click(function() {
        combineData();
    });

    $('#reset-data').click(function() {
        resetData();
    });
});
</script>


<?php 
    $customers = [];
    $salesDataCS = [];
    foreach ($DataSalesPerCS as $row) {
        $customers[] = $row->nama;
        $salesDataCS[] = (float)$row->total;
    }
?>
<script>
$(function() {
    // Generate data dummy untuk 100 customer
    var customers = <?php echo json_encode($customers); ?>;
    var salesData = <?php echo json_encode($salesDataCS); ?>;

    Highcharts.chart('sales-charts-cs', {
        chart: {
            type: 'column',
            margin: 75
        },
        title: {
            text: 'Penjualan Per Customer Bulan <?php echo $model_Global->_periodebulan() .' '. $model_Global->_periodetahun(); ?>',
            style: {
                fontSize: '18px',
                fontFamily: 'Verdana, sans-serif'
            }
        },
        subtitle: {
            text: 'Bulan: <?php echo $model_Global->_periodebulan() .' '. $model_Global->_periodetahun(); ?>',
            style: {
                fontSize: '15px',
                fontFamily: 'Verdana, sans-serif'
            }
        },
        xAxis: {
            categories: customers,
            title: {
                text: 'Customer'
            },
            labels: {
                rotation: -45,
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Penjualan (Rp)'
            },
            labels: {
                formatter: function() {
                    return Highcharts.numberFormat(this.value, 0, ',', '.');
                }
            }
        },
        tooltip: {
            pointFormat: 'Penjualan: <b>Rp {point.y:,.0f}</b>'
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    rotation: 0,
                    color: '#000000',
                    align: 'right',
                    format: '{point.y:,.0f}', // format angka tanpa desimal
                    y: 10, // posisi label
                    style: {
                        fontSize: '9px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            }
        },
        series: [{
            name: 'Penjualan',
            data: salesData,
            color: '#7cb5ec'
        }],
        credits: {
            enabled: false
        },
        exporting: {
            enabled: true
        }
    });
});
</script>
