 <!-- ========== Left Sidebar Start ========== -->
 <div class="vertical-menu">
     <div data-simplebar class="h-100">
         <!--- Sidemenu -->
         <div id="sidebar-menu">
             <!-- Left Menu Start -->
             <ul class="metismenu list-unstyled" id="side-menu">
                 <!-- <div class="px-3 mb-5"><input type="text" class="form-control" style="border: none; border-bottom: 1px solid #ccd; background-color: transparent;"></div> -->
                 <li>
                     <a href="<?= BASEURL ?>" class="waves-effect">
                         <i class="bx bx-home-circle"></i>
                         <span key="t-dashboards">Dashboards</span>
                     </a>
                 </li>

                 <?php if ($model_Global->cek_menu($level, 100) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="bx bx-money"></i>
                             <span key="t-multi-level">Kas/Bank</span>
                         </a>
                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 120) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Transaksi</a>
                                     <ul class="sub-menu" aria-expanded="true">

                                         <?php if ($model_Global->cek_menu($level, 121) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'kasbank/transaksi/penerimaan' ?>" key="t-level-2-1"><i class="fa-solid fa-plus-circle" style="font-size: 13px;"></i> Penerimaan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 122) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'kasbank/transaksi/pembayaran' ?>" key="t-level-2-1"><i class="fa-solid fa-minus-circle" style="font-size: 13px;"></i> Pengeluaran</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 123) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'kasbank/transaksi/transferbank' ?>" key="t-level-2-2"><i class="fa-solid fa-right-left" style="font-size: 13px;"></i> Transfer Bank</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 124) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'kasbank/transaksi/bukukas' ?>" key="t-level-2-2"><i class="fa-solid  fa-book" style="font-size: 13px;"></i> Buku Kas</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 130) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'kasbank/laporan' ?>" key="t-level-1-1">Laporan</a></li>
                             <?php } ?>
                         </ul>

                     </li>
                 <?php }
                    if ($model_Global->cek_menu($level, 200) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="bx bx-book"></i>
                             <span key="t-multi-level">Buku/Besar</span>
                         </a>

                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 210) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Data Master</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 211) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'bukubesar/datamaster/kodeakun' ?>" key="t-level-2-1">Kode Akun</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 212) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'bukubesar/datamaster/saldoawalakun' ?>" key="t-level-2-1">Saldo Awal Akun</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 220) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Transaksi</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 221) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'bukubesar/transaksi/entrijurnal' ?>" key="t-level-2-1"><i class="fa-solid fa-edit" style="font-size: 13px;"></i> Entri Jurnal</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 222) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'bukubesar/transaksi/daftarjurnal' ?>" key="t-level-2-1"><i class="fa-solid fa-list-ul" style="font-size: 13px;"></i> Daftar Jurnal</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 224) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'bukubesar/transaksi/tutupbuku' ?>" key="t-level-2-1">Tutup Buku</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 230) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'bukubesar/laporan' ?>" key="t-level-1-1">Laporan</a></li>
                             <?php } ?>
                         </ul>
                     </li>
                 <?php }
                    if ($model_Global->cek_menu($level, 300) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="bx bxs-truck"></i>
                             <span key="t-multi-level">Pembelian</span>
                         </a>
                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 311) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'pembelian/suplier' ?>" key="t-level-1-1">Supplier</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 320) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Transaksi</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 321) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/prapembelian' ?>" key="t-level-2-1">Pra Pembelian</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 322) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/pesananpembelian' ?>" key="t-level-2-2">Pesanan Pembelian</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 323) > 0) { ?>
                                             <li>
                                                 <a href="javascript: void(0);" class="has-arrow waves-effect" key="t-level-2-3">Penerimaan Barang</a>
                                                 <ul class="sub-menu" aria-expanded="true">
                                                     <?php if ($model_Global->cek_menu($level, 3231) > 0) { ?>
                                                         <a href="<?= BASEURL . 'pembelian/transaksi/buktipenerimaanbarang' ?>" key="t-level-2-3-1">Bukti Penerimaan Barang</a>
                                                     <?php }
                                                        if ($model_Global->cek_menu($level, 3232) > 0) { ?>
                                                         <a href="<?= BASEURL . 'pembelian/transaksi/penerimaanbarang' ?>" key="t-level-2-3-2">Receiving</a>
                                                     <?php } ?>
                                                 </ul>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 324) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/fakturpembelian' ?>" key="t-level-2-4">Faktur Pembelian</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 325) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/uangmukapembelian' ?>" key="t-level-2-4">Uang Muka Pembelian</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 326) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/pembayaranpembelian' ?>" key="t-level-2-6">Pembayaran Pembelian</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 327) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/transaksi/returpembelian' ?>" key="t-level-2-6">Retur Pembelian</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 330) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'pembelian/laporan' ?>" key="t-level-1-1">Laporan</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 340) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Export Data</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 341) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/exportdata/datasubpembelian' ?>" key="t-level-2-1">Data Sub PEMBELIAN</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 342) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'pembelian/exportdata/datapembelian' ?>" key="t-level-2-1">Data PEMBELIAN</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php } ?>
                         </ul>
                     </li>
                 <?php }
                    if ($model_Global->cek_menu($level, 400) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="bx bx-cart"></i>
                             <span key="t-multi-level">Penjualan</span>
                         </a>
                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 412) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'penjualan/datacustomer' ?>" key="t-level-1-1">Data Customer</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 411) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'penjualan/daftarmarketing' ?>" key="t-level-1-1">Daftar Marketing</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 420) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Transaksi</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 421) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/pesananmasuk' ?>" key="t-level-2-1">Pesanan Masuk</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 422) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/pesanandisiapkan' ?>" key="t-level-2-1">Pesanan Disiapkan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 423) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/fakturpenjualan' ?>" key="t-level-2-2">Faktur Penjualan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 424) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/uangmukapenjualan' ?>" key="t-level-2-2">Uang Muka Penjualan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 427) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/kontrabon' ?>" key="t-level-2-2">Kontra Bon</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 425) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/pembayaranhutangcustomer' ?>" key="t-level-2-2"> Pembayaran Hutang Customer</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 426) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/transaksi/returpenjualan' ?>" key="t-level-2-2">Retur Penjualan</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 430) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'penjualan/laporan' ?>" key=" t-level-1-1">Laporan</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 440) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Export Data</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 441) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/exportdata/fakturpajak' ?>" key="t-level-2-1">Faktur Pajak</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 443) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/exportdata/datasubpenjualan' ?>" key="t-level-2-1">Data Sub Penjualan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 442) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/exportdata/exportpenjualan' ?>" key="t-level-2-1">Export Penjualan</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 444) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/exportdata/datasubpenjualanpack' ?>" key="t-level-2-1">Export Sub Penjualan Pack</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 445) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'penjualan/exportdata/datacortex' ?>" key="t-level-2-1">Export Data Cortex</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php } ?>
                         </ul>
                     </li>
                 <?php }
                    if ($model_Global->cek_menu($level, 500) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="fa-solid fa-barcode"></i>
                             <span key="t-multi-level">Inventory</span>
                         </a>
                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 510) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Data Master</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 511) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/kategori' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Kategori</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 514) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/rak' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Rak</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 515) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/gudang' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Gudang</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 516) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/satuan' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Satuan</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 517) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/merk' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Merk</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 519) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/barang' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Barang</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 518) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/datamaster/hargabarangpercustomer' ?>" key="t-level-2-1"><i class="bx bx-edit"></i>Harga Barang per Customer</a>
                                             </li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 520) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Transaksi</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 530) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/stokopname' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Stok Opname</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 526) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/permohonanmutasigudang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Permohonan Mutasi Gudang</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 521) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/mutasigudang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Mutasi Gudang</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 527) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/persetujuanmutasigudang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Persetujuan Mutasi Gudang</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 522) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/permohonanmutasicabang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Permohonan Mutasi Region</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 523) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/mutasicabang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Permohonan Mutasi Disiapkan</a>
                                             </li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 524) > 0) { ?>
                                             <li>
                                                 <a href="<?= BASEURL . 'inventory/transaksi/persetujuanmutasicabang' ?>" key="t-level-2-1"><i class="bx bx-list-ul"></i>Persetujuan Penerimaan Mutasi</a>
                                             </li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php }
                                if ($model_Global->cek_menu($level, 540) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'inventory/laporan' ?>" key="t-level-1-1">Laporan</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 550) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'inventory/importdatabarang' ?>" key="t-level-1-1">Import Data Barang</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 560) > 0) { ?>
                                 <li>
                                     <a href="javascript: void(0);" class="has-arrow" key="t-level-1-2">Export Data</a>
                                     <ul class="sub-menu" aria-expanded="true">
                                         <?php if ($model_Global->cek_menu($level, 561) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'inventory/exportdata/datastock' ?>" key="t-level-2-1"></i>Data Stock</a></li>
                                         <?php }
                                            if ($model_Global->cek_menu($level, 562) > 0) { ?>
                                             <li><a href="<?= BASEURL . 'inventory/exportdata/datahargapercust' ?>" key="t-level-2-2"></i>Data Harga Percustomer</a></li>
                                         <?php } ?>
                                     </ul>
                                 </li>
                             <?php } ?>
                         </ul>
                     </li>
                 <?php }
                    if ($model_Global->cek_menu($level, 900) > 0) { ?>
                     <li>
                         <a href="javascript: void(0);" class="has-arrow waves-effect">
                             <i class="bx bx-cog"></i>
                             <span key="t-multi-level">Utilitas</span>
                         </a>
                         <ul class="sub-menu" aria-expanded="true">
                             <?php if ($model_Global->cek_menu($level, 901) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'utilitas/pengguna' ?>" key="t-level-1-1">Pengguna Aplikasi</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 902) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'utilitas/gruppemakai' ?>" key="t-level-1-1">Grup Pemakai</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 903) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'utilitas/kodecabang' ?>" key="t-level-1-1">Kode Region</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 904) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'utilitas/profileedit' ?>" key="t-level-1-1">Profile</a></li>
                             <?php }
                                if ($model_Global->cek_menu($level, 905) > 0) { ?>
                                 <li><a href="<?= BASEURL . 'utilitas/nomorbukti' ?>" key="t-level-1-1">Nomor Bukti</a></li>
                             <?php } ?>
                         </ul>
                     </li>
                 <?php } ?>
             </ul>
         </div>
         <!-- Sidebar -->
     </div>
 </div>
 <!-- Left Sidebar End -->