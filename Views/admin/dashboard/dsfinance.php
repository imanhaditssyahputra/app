<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="mb-4 me-3">

                        <img src="<?= BASEURL ?>assets/puser/<?= $avatar; ?>" alt="" class="avatar-sm rounded">
                    </div>

                    <div>
                        <h5><?= $nama_lengkap; ?></h5>
                        <p class="text-muted mb-1"><?= $email; ?></p>
                        <p class="text-muted mb-0"><?= $mobilephone; ?></p>

                    </div>
                </div>
            </div>

            <div class="card-body border-top">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <p class="fw-medium mb-2">Saldo Awal Kas :</p>
                            <h4>Rp <?= number_format($SaldoAwalKas); ?></h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mt-4 mt-sm-0">
                            <p class="fw-medium mb-2">Saldo Akhir Kas :</p>
                            <h4>Rp <?= number_format($SaldoAkhirKas); ?></h4>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div>
                <div class="row">
                    <div class="col-lg-9 col-sm-8">
                        <div class="p-4">
                            <h5 class="text-primary">Welcome Back !</h5>
                            <p><?= $nama_lengkap; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-4 align-self-center">
                        <div>
                            <img src="assets/images/crypto/features-img/img-1.png" alt="" class="img-fluid d-block">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
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
            </div>
            <div class="col-sm-4">
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
            </div>
            <div class="col-sm-4">
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
            </div>
        </div>

    </div>
</div>
<!-- end row -->