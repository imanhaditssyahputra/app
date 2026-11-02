<?php
error_reporting(0);
$model_Global =  model('Global_model', 'M_GLOBAL');
$db = db_connect();
foreach ($reportcbgpst as $result) {
    $bulan[] = $result->bulan;
    if ($result->kodecbg == '01') {
        $valuecbgpusat[] = (float)$result->jumlah;
    } elseif ($result->kodecbg == '02') {
        $valuecbgkrwg[] = (float)$result->jumlah;
    }
}

////// Purchase Chart
foreach ($reportChartPurchase as $result) {
    $bulan[] = $result->bulan;
    $valuePurchaseChart[] = (float)$result->jumlah;
}

$differentData = [];
foreach ($reportAllMarketing as $result) {
    $bulanAllMarketing[] = $result->bulan;
    $DataSales = $db->table('ar_sales')->getWhere(['id' => $result->id_sales])->getRow();
    $differentData[] = [
        'namesales' => $DataSales->nama_sales, 
        'valuesales' =>(float)$result->jumlah,
        'bulansales' => $result->bulan
    ];
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
$View = [
    'avatar' => $DataUser->avatar,
    'nama_lengkap' => $session->get('nama_lengkap'),
    'email' => $DataUser->email,
    'mobilephone' => $DataUser->mobilephone,
    'cabang' => $cabang,
    'asset' => $asset,
    'piutang' => $piutang,
    'hutang' => $hutang,
    'lr' => $lr,
    'periode' => $periode,
    'HitungFKPembelian' => $HitungFKPembelian,
    'HitungFKPenjualan' => $HitungFKPenjualan,
    'jumlahCurrentjual' => $jumlahCurrentjual,
    'jumlahPrevjual' => $jumlahPrevjual,
    'HitungFKPenjualanPercen' => $HitungFKPenjualanPercen,
    'nmgrup' => $DataGroup->nmgrup,
    'HitungSofile' => $HitungSofile,
    'CountFakturUser' => $CountFakturUser,
    'HitungFaktur' => $HitungFaktur,
    'HitungRetur' => $HitungRetur,
    'level' => $level,
    'CountOrder' => $CountOrder,
    'CountKirim' => $CountKirim,
    'CountRetur' => $CountRetur,
    'SaldoAwalKas' => $SaldoAwalKas,
    'SaldoAkhirKas' => $SaldoAkhirKas,
    'valuecbgpusat' => $valuecbgpusat,
    'valuecbgkrwg' => $valuecbgkrwg,
    'DataSalesPerCS' => $DataSalesPerCS,
];

if ($level == 1 || $level == 2 || $level == 8) {
    echo view('admin/dashboard/dsadmin', $View);
} else if ($level == 3) {
    echo view('admin/dashboard/dssales', $View);
} else if ($level == 4 || $level == 10) {
    echo view('admin/dashboard/dsgudang', $View);
} else if ($level == 5) {
    echo view('admin/dashboard/dsfinance', $View);
} else if ($level == 6) {
    echo view('admin/dashboard/dsfaktur', $View);
}
?>

<!-- apexcharts -->
<script src="<?= BASEURL ?>public/assets/libs/apexcharts/apexcharts.min.js"></script>
<!-- <script src="<?= BASEURL ?>public/assets/js/highcharts.js"></script> -->
<!-- <script src="<?= BASEURL ?>public/assets/js/exporting.js"></script> -->

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<!-- tui charts plugins -->
<script src="<?= BASEURL ?>public/assets/libs/tui-chart/tui-chart-all.min.js"></script>

<!-- tui charts map -->
<script src="<?= BASEURL ?>public/assets/libs/tui-chart/maps/usa.js"></script>

<!-- html2canvas Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- SheetJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script type="text/javascript">
    $(function() {
        $('#purchase-chart').highcharts({
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
                categories: <?php echo json_encode($GetBulan); ?>
            },
            exporting: {
                enabled: true // Aktifkan fitur ekspor
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
                data: <?php echo json_encode($valuePurchaseChart); ?>,
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
                categories: <?php echo json_encode($GetBulan); ?>
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
    // Tambahkan event listener untuk tombol ekspor
    document.getElementById('export-chart').addEventListener('click', function() {
        html2canvas(document.querySelector("#column-charts")).then(canvas => {
            // Buat link untuk mengunduh gambar
            let link = document.createElement('a');
            link.download = 'chart-export.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });

    document.getElementById('export-excel').addEventListener('click', function() {
        // Data dari chart (contoh: data series dan kategori)
        const categories = <?php echo json_encode($bulan); ?>;
        const seriesData = <?php echo json_encode($valueRpPerMarketing); ?>;

        // Buat array untuk data Excel
        const excelData = [
            ["Bulan", "Penjualan"], // Header
            ...categories.map((category, index) => [category, seriesData[index]]) // Data
        ];

        // Buat worksheet dari data
        const worksheet = XLSX.utils.aoa_to_sheet(excelData);

        // Buat workbook dan tambahkan worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Penjualan");

        // Ekspor ke file Excel
        XLSX.writeFile(workbook, 'chart-export.xlsx');
    });

    // Handle resize chart saat ukuran window berubah
    window.addEventListener('resize', function() {
        columnChartWidth = document.getElementById("column-charts").offsetWidth;
        columnChart.resize({
            width: columnChartWidth,
            height: 350
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $('#user-sales-chart').highcharts({
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
                text: "Penjualan <?= $session->get('nama_lengkap'); ?>",
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
                categories: <?php echo json_encode($GetBulan); ?>
            },
            exporting: {
                enabled: false
            },
            yAxis: {
                title: {
                    text: 'Jumlah'
                },
            },
            exporting: {
                enabled: true // Aktifkan fitur ekspor
            },
            tooltip: {
                formatter: function() {
                    return 'The value for <b>' + this.x + '</b> is <b>' + Highcharts.numberFormat(this.y, 0) + '</b>, in ' + this.series.name;
                }
            },

            series: [{
                    name: 'Bulan',
                    data: <?php echo json_encode($valueRpPerMarketing); ?>,
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
    var DataAllsales = <?php echo json_encode($differentData); ?>;
    var bulanCategories = <?php echo json_encode($GetBulan); ?>; // ['Jan', 'Peb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nop', 'Des']
    var groupedData = {};

    // 1. Inisialisasi semua sales dengan array bernilai 0 untuk setiap bulan
    for (var i = 0; i < DataAllsales.length; i++) {
        var salesName = DataAllsales[i].namesales;
        if (!groupedData[salesName]) {
            groupedData[salesName] = new Array(bulanCategories.length).fill(0); // Array berisi 0 untuk setiap bulan
        }
    }

    // 2. Isi nilai sales sesuai bulan dan nama sales
    for (var i = 0; i < DataAllsales.length; i++) {
        var salesName = DataAllsales[i].namesales;
        var bulan = DataAllsales[i].bulansales; // Ambil bulan sebagai string
        var bulanIndex = bulanCategories.indexOf(bulan); // Cari index bulan dalam kategori

        // Jika bulan ditemukan, masukkan nilai sales
        if (bulanIndex !== -1) {
            groupedData[salesName][bulanIndex] = DataAllsales[i].valuesales; // Set nilai sales pada index bulan yang sesuai
        }
    }

    // 3. Format data untuk Highcharts
    var seriesData = [];
    for (var name in groupedData) {
        seriesData.push({
            name: name,
            data: groupedData[name],
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
        });
    }

    // 4. Konfigurasi Highcharts
    Highcharts.chart('sales-chart-all-user', {
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
        xAxis: {
            categories: bulanCategories, // Kategori bulan
            crosshair: true,
            accessibility: {
                description: 'Month'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah'
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: seriesData // Data yang telah dikelompokkan
    });
</script>
