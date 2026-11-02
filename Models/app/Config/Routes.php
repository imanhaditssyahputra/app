<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->post('/authentication', 'Auth::login_check');
$routes->post('/autcbg', 'Auth::selectcabang');
$routes->post('/autgetcbg/(:any)', 'Auth::getcabang/$1');
$routes->get('/logout', 'Auth::log_out');
$routes->get('/', 'Auth::index');
$cek = session()->get('is_logged_in');
if (session()->get('is_logged_in') == true) {
    $routes->get('/dashboard', 'Home::dashboard');
    $routes->get('/profile', 'Home::profile');
    $routes->post('/searchbarangfilter/(:any)/(:any)', 'Home::search_barang_filter/$1/$2');
    $routes->post('/searchbarangfiltercabang/(:any)', 'Home::search_barang_filtercabang/$1');
    $routes->post('/searchgudang', 'Home::search_gudang');
    $routes->post('/searchgudangfilter/(:any)', 'Home::search_gudang_filter/$1');
    $routes->post('/searchsupp', 'Home::search_supplier');
    $routes->post('/searchcust/(:any)', 'Home::search_cust_filter/$1');
    $routes->post('/searchakun', 'Home::search_akun');
    $routes->post('/searchakunrl', 'Home::search_akun_RL');
    $routes->get('/showbarangdata/(:any)/(:any)', 'Home::showbarangdata/$1/$2');
    $routes->get('/GetDataBarangFilter/(:any)/(:any)/(:any)', 'Home::DataBarang/$1/$2/$3');
    $routes->get('/GetDataBarangFilterCabang/(:any)/(:any)', 'Home::DataBarangCabang/$1/$2');
    $routes->get('/getbatchid/(:any)/(:any)', 'Home::getbatchData/$1/$2');
    $routes->get('/getbatchSo/(:any)/(:any)', 'Home::getbatchDataSo/$1/$2');
    $routes->get('/gethargacs/(:any)/(:any)', 'Home::gethargaData/$1/$2');
    $routes->post('/commonedit', 'CommonFun::common_edit');
    $routes->post('/commondelete', 'CommonFun::common_delete');
    $routes->get('/pingdata', 'Home::pingdata');

    // KAS AND BANK SECTION Route
    $routes->group('kasbank', static function ($routes) {
        //Second Link
        $routes->group('transaksi', static function ($routes) {
            // MODULE PENERIMAAN KAS & BANK
            $routes->get('penerimaan', 'Kasbank::penerimaan');
            $routes->get('penerimaantable/(:any)/(:any)', 'Kasbank::penerimaantable/$1/$2');
            $routes->get('penerimaan/entri(:any)?', 'Kasbank::penerimaan_entri$1');
            $routes->post('penerimaan/save', 'Kasbank::penerimaansave');
            $routes->post('penerimaan/delete/(:any)', 'Kasbank::PenerimaanDel/$1');
            $routes->get('penerimaan/cetak/(:any)', 'Kasbank::PenerimaanCetak/$1');
            // MODULE PEMBAYARAN KAS & BANK
            $routes->get('pembayaran', 'Kasbank::pembayaran');
            $routes->get('pembayaran/entri(:any)?', 'Kasbank::pembayaran_entri$1');
            $routes->get('pembayarantable/(:any)/(:any)', 'Kasbank::pembayarantable/$1/$2');
            $routes->post('pembayaran/save', 'Kasbank::pembayaransave');
            $routes->post('pembayaran/delete/(:any)', 'Kasbank::PembayaranDel/$1');
            $routes->get('pembayaran/cetak/(:any)', 'Kasbank::PembayaranCetak/$1');
        });
        //Third Link
        $routes->get('laporan', 'Kasbank::laporan');
        //Fourth Link
        $routes->group('exportdata', static function ($routes) {
            $routes->get('datasubpembelian', 'Pembelian::data_sub_pembelian');
            $routes->get('datapembelian', 'Pembelian::data_pembelian');
        });
    });


    $routes->group('bukubesar', static function ($routes) {
        //Second Link
        $routes->group('datamaster', static function ($routes) {
            // MODULE KODE AKUN BUKU BESAR
            $routes->get('kodeakun', 'Bukubesar::kode_akun');
            $routes->post('kodeakun-add', 'Bukubesar::kodeakun_add');
            $routes->get('kodeakuntable', 'Bukubesar::kodeakun_table');
            $routes->post('akundelete/(:any)', 'Bukubesar::kodedelete/$1');
            // MODULE SALDO AWAL BUKU BESAR
            $routes->get('saldoawalakun', 'Bukubesar::saldo_awal_akun');
            $routes->post('saldoawalakun-add', 'Bukubesar::saldo_awal_add');
            $routes->get('saldo-table', 'Bukubesar::saldo_table');
            $routes->post('saldoawal-edit/(:any)', 'Bukubesar::SaldoAwalEdit/$1');
            $routes->post('saldoawal-delete/(:any)', 'Bukubesar::SaldoAwalDelete/$1');
        });
        $routes->group('transaksi', static function ($routes) {
            $routes->get('entrijurnal(:any)?', 'Bukubesar::entri_jurnal$1');
            $routes->get('daftarjurnal', 'Bukubesar::daftar_jurnal');
            $routes->get('daftarjurnal-table/(:any)/(:any)', 'Bukubesar::DaftarJurnalTable/$1/$2');
            $routes->get('tutupbuku', 'Bukubesar::tutup_buku');
        });
        //Third Link
        $routes->get('laporan', 'Bukubesar::laporan');
        $routes->post('laporan-add', 'Bukubesar::laporan_add');
        //Fourth Link
        $routes->group('exportdata', static function ($routes) {
            $routes->get('datasubpembelian', 'Pembelian::data_sub_pembelian');
            $routes->get('datapembelian', 'Pembelian::data_pembelian');
        });
    });

    // Pembelian Section Route
    $routes->group('pembelian', static function ($routes) {
        //First Link
        $routes->get('suplier', 'Pembelian::suplier');
        $routes->post('suplieradd', 'Pembelian::suplier_add');
        $routes->get('supliertable', 'Pembelian::suplier_table');
        $routes->post('suplierdelete/(:any)', 'Pembelian::suplier_delete/$1');
        $routes->post('suplieredit/(:any)', 'Pembelian::suplier_edit/$1');
        $routes->post('suplierupdate/(:any)', 'Pembelian::suplier_update/$1');
        $routes->get('getinfosupplier/(:any)', 'Pembelian::GetinfoSupplier/$1');
        //Second Link
        $routes->group('transaksi', static function ($routes) {
            // MODULE PRA PESANAN PEMBELIAN
            $routes->get('prapembelian', 'Pembelian::pra_pembelian');
            $routes->get('prapembeliantable/(:any)/(:any)', 'Pembelian::pra_pembelian_table/$1/$2');
            $routes->get('prapembelianbufferstocktable', 'Pembelian::pra_pembelian_table_bufferStock');
            $routes->get('prapembelian/entri(:any)?', 'Pembelian::pra_pembelian_entri$1');
            $routes->post('prapembeliandelete/(:any)', 'Pembelian::pra_pembelian_delete/$1');
            $routes->post('prapembelianadd', 'Pembelian::pra_pembelian_add');
            // MODULE PESANAN PEMBELIAN
            $routes->get('pesananpembelian', 'Pembelian::pesanan_pembelian');
            $routes->get('pesananpembelian/entri(:any)?', 'Pembelian::pesanan_pembelian_entri$1');
            $routes->get('pesananpembeliangetprapo/(:any)/(:any)', 'Pembelian::getbarangprapo/$1/$2');
            $routes->get('pocekdatabarangsupp/(:any)/(:any)', 'Pembelian::pocekdatabarangsupp/$1/$2');
            $routes->post('pochangesupphargapercust/(:any)/(:any)', 'Pembelian::changesupphargapercust/$1/$2');
            $routes->post('pesananpembelianadd', 'Pembelian::pesanan_pembelian_add');
            $routes->get('pesananpembeliantable/(:any)/(:any)', 'Pembelian::pesanan_pembelian_table/$1/$2');
            $routes->post('pesananpembeliandelete/(:any)', 'Pembelian::po_pembelian_delete/$1');
            $routes->get('pesananpembeliancetak/(:any)', 'Pembelian::cetakpo/$1');
            $routes->get('cetakpenerimaanpo/(:any)', 'Pembelian::cetakPenerimaan/$1');
            // MODULE BUKTI PENERIMAAN BARANG
            $routes->get('buktipenerimaanbarang', 'Pembelian::buktipenerimaan_barang');
            $routes->get('buktipenerimaanbarangtable/(:any)/(:any)', 'Pembelian::bukti_penerimaan_baranag_table/$1/$2');
            $routes->get('buktipenerimaanbarang/entri(:any)?', 'Pembelian::bukti_penerimaan_barang_entri$1');
            $routes->get('cetakpenerimaanpo/(:any)', 'Pembelian::cetakPenerimaan/$1');
            $routes->get('penerimaanbarang/getlistpo/(:any)', 'Pembelian::listpo/$1');
            $routes->get('penerimaanbarang/postpo/(:any)/(:any)', 'Pembelian::postdatapo/$1/$2');
            $routes->get('penerimaanbarang/postpoheader/(:any)', 'Pembelian::postdatapoheader/$1');
            $routes->get('penerimaanbarang/showbarangpo/(:any)/(:any)/(:any)', 'Pembelian::data_barang_list_po/$1/$2/$3');
            $routes->post('penerimaanbarangadd', 'Pembelian::terima_pembelian_add');
            $routes->post('penerimaanbarangdelete/(:any)', 'Pembelian::terima_pembelian_delete/$1');
            // MODULE PENERIMAAN BARANG / RECEIVING
            $routes->get('penerimaanbarang', 'Pembelian::penerimaan_barang');
            $routes->get('penerimaanbarangtable/(:any)/(:any)', 'Pembelian::penerimaan_pembelian_table/$1/$2');
            $routes->get('penerimaanbarang/entri(:any)?', 'Pembelian::penerimaan_barang_entri$1');
            $routes->get('penerimaanbarang/getlistpo/(:any)', 'Pembelian::listpo/$1');
            $routes->get('penerimaanbarang/postpo/(:any)/(:any)', 'Pembelian::postdatapo/$1/$2');
            $routes->get('penerimaanbarang/postpoheader/(:any)', 'Pembelian::postdatapoheader/$1');
            $routes->get('penerimaanbarang/showbarangpo/(:any)/(:any)/(:any)', 'Pembelian::data_barang_list_po/$1/$2/$3');
            $routes->post('penerimaanbarangadd', 'Pembelian::terima_pembelian_add');
            $routes->post('penerimaanbarangdelete/(:any)', 'Pembelian::terima_pembelian_delete/$1');
            // MODULE FAKTUR PEMBELIAN
            $routes->get('fakturpembelian', 'Pembelian::faktur_pembelian');
            $routes->get('fakturpembeliantable/(:any)/(:any)', 'Pembelian::faktur_pembelian_table/$1/$2');
            $routes->get('fakturpembelian/entri(:any)?', 'Pembelian::faktur_pembelian_entri$1');
            $routes->get('fakturpembelian/getlistpb/(:any)', 'Pembelian::listpb/$1');
            $routes->get('fakturpembelian/postpb/(:any)', 'Pembelian::postdatapb/$1');
            $routes->post('fakturpembeliansave', 'Pembelian::faktur_pembelian_save');
            $routes->get('fakturpembeliancetak/(:any)', 'Pembelian::cetak_faktur/$1');
            // MODULE UANG MUKA PEMBELIAN
            $routes->get('uangmukapembelian', 'Pembelian::uang_muka_pembelian');
            $routes->get('uangmukapembeliantable/(:any)/(:any)', 'Pembelian::um_pembelian_table/$1/$2');
            $routes->get('uangmukapembelian/entri(:any)?', 'Pembelian::uang_muka_pembelian_entri$1');
            $routes->get('uangmukapembelian/getlistpo/(:any)', 'Pembelian::listpo/$1');
            $routes->get('uangmukapembelian/postpofile/(:any)', 'Pembelian::postpofile/$1');
            $routes->post('uangmukapembeliansave', 'Pembelian::uangmuka_pembelian_save');
            // MODULE PEMBAYARAN PEMBELIAN
            $routes->get('pembayaranpembelian', 'Pembelian::pembayaran_pembelian');
            $routes->get('pembayaranpembeliantable/(:any)/(:any)', 'Pembelian::pembayaran_pembelian_table/$1/$2');
            $routes->get('pembayaranpembelian/entri(:any)?', 'Pembelian::pembayaran_pembelian_entri$1');
            $routes->get('pembayaranpembelian/postfaktur/(:any)', 'Pembelian::getfaktur/$1');
            $routes->post('pembayaranpembeliansave', 'Pembelian::bayar_pembelian_save');
            $routes->get('cetakbayarnpo/(:any)', 'Pembelian::cetakbayar/$1');
            // MODULE RETUR PEMBELIAN
            $routes->get('returpembelian', 'Pembelian::retur_pembelian');
            $routes->get('returpembeliantable/(:any)/(:any)', 'Pembelian::retur_pembelian_table/$1/$2');
            $routes->get('returpembelian/entri(:any)?', 'Pembelian::retur_pembelian_entri$1');
            $routes->get('returpembelian/getlistpu/(:any)', 'Pembelian::listpu/$1');
            $routes->get('returpembelian/postpu/(:any)', 'Pembelian::postdatapu/$1');
            $routes->post('returpembeliansave', 'Pembelian::retur_pembelian_save');
            $routes->get('returpembeliancetak/(:any)', 'Pembelian::cetakretur/$1');
        });
        //Third Link
        $routes->get('laporan', 'Pembelian::laporan');
        $routes->get('laporan/cetaklaporan/(:segment)', 'Pembelian::CetakLaporan/$1');
        //Fourth Link
        $routes->group('exportdata', static function ($routes) {
            $routes->get('datasubpembelian', 'Pembelian::data_sub_pembelian');
            $routes->get('datasubpembeliantable/(:any)/(:any)', 'Pembelian::datasubpembelian_table/$1/$2');
            $routes->get('datapembelian', 'Pembelian::data_pembelian');
            $routes->get('datapembeliantable/(:any)/(:any)', 'Pembelian::datapembelian_table/$1/$2');
        });
    });

    // Penjualan Section Route
    $routes->group('penjualan', static function ($routes) {
        //First Link
        $routes->get('datacustomer', 'Penjualan::data_customer');
        $routes->post('datacustomeradd', 'Penjualan::data_customer_add');
        $routes->get('datacustomertable', 'Penjualan::data_customer_table');
        $routes->post('datacustomeredit/(:any)', 'Penjualan::data_customer_edit/$1');
        $routes->post('datacustomerupdate/(:any)', 'Penjualan::data_customer_update/$1');
        $routes->post('datacustomerdelete/(:any)', 'Penjualan::data_customer_delete/$1');
        //Second Link
        $routes->get('daftarmarketing', 'Penjualan::daftar_marketing');
        $routes->post('marketingadd/(:any)', 'Penjualan::marketing_add/$1');
        $routes->get('daftarmarketingtable', 'Penjualan::daftar_marketing_table');
        $routes->post('marketingedit/(:any)', 'Penjualan::marketing_edit/$1');
        $routes->post('daftarmarketingdelete/(:any)', 'Penjualan::daftar_marketing_delete/$1');
        //Third Link
        $routes->group('transaksi', static function ($routes) {
            $routes->get('pesananmasuk', 'Penjualan::pesanan_masuk');
            $routes->get('pesanantable/(:any)/(:any)', 'Penjualan::pesanan_table/$1/$2');
            $routes->get('pesananmasuk/entri(:any)?', 'Penjualan::pesanan_masuk_entri$1');
            $routes->get('pesananmasuk/DataCustomer/(:any)', 'Penjualan::DataCustomer/$1');
            $routes->get('pesananmasuk/ShowBarang/(:any)/(:any)/(:any)', 'Penjualan::ShowBarangPO/$1/$2/$3');
            $routes->get('pesananmasuk/InfoData/(:any)/(:any)/(:any)', 'Penjualan::infoibrg/$1/$2/$3');
            $routes->get('pesananmasuk/syncdata/(:any)/(:any)', 'Penjualan::syncdata/$1/$2');
            $routes->post('pesananmasuksave', 'Penjualan::pesananmasuksave');
            $routes->post('pesananmasuk/delete/(:any)', 'Penjualan::pesananmasukDelete/$1');
            $routes->get('pesananmasukcetak/(:any)', 'Penjualan::cetak_pesananmasuk/$1');
            $routes->get('pesananmasukcetakshipping/(:any)', 'Penjualan::cetak_shipping/$1');

            $routes->get('pesanandisiapkan', 'Penjualan::pesanan_disiapkan');
            $routes->get('pesanandisiapkantable/(:any)/(:any)', 'Penjualan::pesanandisiapkan_table/$1/$2');
            $routes->get('pesanandisiapkan/entri(:any)?', 'Penjualan::pesanan_disiapkan_entri$1');
            $routes->get('pesanandisiapkan/showso/(:any)', 'Penjualan::ShowSo/$1');
            $routes->get('pesanandisiapkan/dataso/(:any)', 'Penjualan::DataSo/$1');
            $routes->get('pesanandisiapkan/showbarang/(:any)/(:any)/(:any)', 'Penjualan::showbarang/$1/$2/$3');
            $routes->post('pesanandisiapkan/save', 'Penjualan::pesanandisiapkansave');
            $routes->post('pesanandisiapkan/delete/(:any)', 'Penjualan::pesanandisiapkandelete/$1');

            $routes->get('fakturpenjualan', 'Penjualan::faktur_penjualan');
            $routes->get('fakturpenjualantable/(:any)/(:any)', 'Penjualan::fakturpenjualan_table/$1/$2');
            $routes->get('fakturpenjualan/entri(:any)?', 'Penjualan::faktur_penjualan_entri$1');
            $routes->get('fakturpenjualan/showsd/(:any)', 'Penjualan::ShowSd/$1');
            $routes->get('fakturpenjualan/getcs/(:any)', 'Penjualan::GetCs/$1');
            $routes->get('fakturpenjualan/getsd/(:any)/(:any)', 'Penjualan::getsd/$1/$2');
            $routes->get('fakturpenjualan/getsd3/(:any)', 'Penjualan::getsd3/$1');
            $routes->get('fakturpenjualan/getsd4/(:any)/(:any)', 'Penjualan::getsd4/$1/$2');
            $routes->post('fakturpenjualan/save', 'Penjualan::fakturpenjualansave');
            $routes->get('fakturpenjualancetak/(:any)', 'Penjualan::cetak_faktur/$1');
            $routes->get('fakturpenjualancetaksj/(:any)', 'Penjualan::cetak_suratjalan/$1');

            $routes->get('uangmukapenjualan', 'Penjualan::uang_muka_penjualan');
            $routes->get('uangmukapenjualan/entri', 'Penjualan::uang_muka_penjualan_entri');

            $routes->get('pembayaranhutangcustomer', 'Penjualan::pembayaran_hutang_customer');
            $routes->get('pembayaranhutangcustomertable/(:any)/(:any)', 'Penjualan::bayar_table/$1/$2');
            $routes->get('pembayaranhutangcustomer/entri(:any)?', 'Penjualan::pembayaran_hutang_customer_entri$1');
            $routes->get('pembayaranhutangcustomer/getfaktur/(:any)', 'Penjualan::bayar_getfaktur/$1');
            $routes->post('pembayaranhutangcustomer/save', 'Penjualan::bayar_save');
            $routes->get('cetakbayar/(:any)', 'Penjualan::cetak_bayar/$1');
            $routes->get('pembayaranhutangcustomertablefaktur/(:any)', 'Penjualan::getsearchbayarfaktur/$1');

            $routes->get('returpenjualan', 'Penjualan::retur_penjualan');
            $routes->get('returpenjualantable/(:any)/(:any)', 'Penjualan::retur_table/$1/$2');
            $routes->get('returpenjualan/entri(:any)?', 'Penjualan::retur_penjualan_entri$1');
            $routes->get('returpenjualan/showsi/(:any)', 'Penjualan::retur_showsi/$1');
            $routes->get('returpenjualan/getdatasi/(:any)', 'Penjualan::retur_getdatasi/$1');
            $routes->post('returpenjualan/save', 'Penjualan::retur_save');
            $routes->get('returpenjualancetak/(:any)', 'Penjualan::cetak_retur/$1');
        });
        //Fourth Link
        $routes->get('laporan', 'Penjualan::laporan');
        $routes->get('laporan/cetaklaporan/(:segment)', 'Penjualan::CetakLaporan/$1');

        //Five Link
        $routes->group('exportdata', static function ($routes) {
            $routes->get('fakturpajak', 'Penjualan::faktur_pajak');
            $routes->get('fakturpajaktable/(:any)/(:any)', 'Penjualan::fakturpajak_table/$1/$2');
            $routes->get('datasubpenjualan', 'Penjualan::data_sub_penjualan');
            $routes->get('subpenjualantable/(:any)/(:any)', 'Penjualan::subpenjualan_table/$1/$2');
            $routes->get('exportpenjualan', 'Penjualan::export_penjualan');
            $routes->get('exportpenjualantable/(:any)/(:any)', 'Penjualan::exportpenjualan_table/$1/$2');
        });
    });

    // Inventory Section Route
    $routes->group('inventory', static function ($routes) {
        //First Link
        $routes->group('datamaster', static function ($routes) {
            // Master Data Kategori
            $routes->get('kategori', 'Inventory::kategori');
            $routes->post('kategoriadd', 'Inventory::kategori_add');
            $routes->post('kategoriupdate/(:num)', 'Inventory::kategori_update/$1');
            $routes->post('kategoriedit/(:any)', 'Inventory::kategori_edit/$1');
            $routes->post('kategoridelete/(:any)', 'Inventory::kategori_delete/$1');
            $routes->get('kategoritable', 'Inventory::kategori_table');

            // Master Data Rak
            $routes->get('rak', 'Inventory::rak');
            $routes->post('rakadd', 'Inventory::rak_add');
            $routes->post('rakupdate/(:num)', 'Inventory::rak_update/$1');
            $routes->post('rakedit/(:any)', 'Inventory::rak_edit/$1');
            $routes->post('rakdelete/(:any)', 'Inventory::rak_delete/$1');
            $routes->get('raktable', 'Inventory::rak_table');

            //Master Data Gudang
            $routes->get('gudang', 'Inventory::gudang');
            $routes->post('gudang_data', 'Inventory::gudang_data_submit');
            $routes->get('gudang_data_get', 'Inventory::gudang_data_get');
            $routes->post('gudangdataedit/(:num)', 'Inventory::gudang_data_edit/$1');
            $routes->post('gudangdataupdate/(:any)', 'Inventory::gudang_data_update/$1');
            $routes->post('gudangdatadelete/(:num)', 'Inventory::gudang_data_delete/$1');

            // Master Data Satuan
            $routes->get('satuan', 'Inventory::satuan');
            $routes->get('satuantable', 'Inventory::satuan_table');
            $routes->post('satuanadd', 'Inventory::satuan_add');
            $routes->post('satuanedit/(:any)', 'Inventory::satuan_edit/$1');
            $routes->post('satuanupdate/(:num)', 'Inventory::satuan_update/$1');
            $routes->post('satuandelete/(:any)', 'Inventory::satuan_delete/$1');

            //Master Data Merk
            $routes->get('merk', 'Inventory::merk');
            $routes->post('merkadd', 'Inventory::merk_add');
            $routes->post('merkedit/(:any)', 'Inventory::merk_edit/$1');
            $routes->post('merkupdate/(:num)', 'Inventory::merk_update/$1');
            $routes->post('merkdelete/(:any)', 'Inventory::merk_delete/$1');
            $routes->post('merkadd/(:any)', 'Inventory::merk_add/$1');
            $routes->get('merktable', 'Inventory::merk_table');

            //Master Data Barang
            $routes->get('barang', 'Inventory::barang');
            $routes->post('barangtable/(:any)/(:any)', 'Inventory::barang_table/$1/$2');
            $routes->post('barangadd', 'Inventory::barang_add');
            $routes->post('barangedit/(:any)', 'Inventory::barang_edit/$1');
            $routes->post('barangupdate/(:any)', 'Inventory::barang_update/$1');
            $routes->post('barangdelete/(:any)', 'Inventory::barang_delete/$1');
            $routes->get('barangtablebatch/(:any)/(:any)/(:any)', 'Inventory::barang_table_batch/$1/$2/$3');
            $routes->post('validasiperbarang/(:any)', 'Inventory::validasi_perbarang/$1');
            $routes->post('validasiallbarang/(:any)', 'Inventory::validasiallbarang/$1');

            //Master Data Harga Barang Percustomer
            $routes->get('hargabarangpercustomer', 'Inventory::harga_barang_per_customer');
            $routes->get('hargabarang/entri(:any)?', 'Inventory::hargabarang_entri$1');
            $routes->get('getcabang/(:any)', 'Inventory::getcabang/$1');
            $routes->get('getnamecust/(:any)/(:any)/(:any)', 'Inventory::getnamecust/$1/$2/$3');
            $routes->get('getkodespl1/(:any)/(:any)/(:any)', 'Inventory::getkodespl1/$1/$2/$3');
            $routes->post('hargabarangadd', 'Inventory::harga_barang_add');
            $routes->get('hargabarangtable/(:any)', 'Inventory::harga_barang_table/$1');
            $routes->post('hargabarangedit/(:any)', 'Inventory::harga_barang_edit/$1');
            $routes->get('hargabarang/editpercust/(:any)/(:any)/(:any)/(:any)', 'Inventory::vieweditpercust/$1/$2/$3/$4');
            $routes->post('hargabarang/saveeditpercust', 'Inventory::saveeditpercust');
            $routes->post('hargabarangdelete/(:any)', 'Inventory::harga_barang_delete/$1');
        });
        //Second Link
        $routes->group('transaksi', static function ($routes) {
            $routes->get('stokopname', 'Inventory::stok_opname');
            $routes->get('stokopnametable/(:any)', 'Inventory::stok_opname_table/$1');
            $routes->post('praso', 'Inventory::praso');
            $routes->get('stokopname/entri(:any)?', 'Inventory::stok_opname_entri$1');
            $routes->get('stokopname/showbarang/(:any)/(:any)', 'Inventory::SO_ShowBarang/$1/$2');
            $routes->post('stokopname/save', 'Inventory::SO_Save');
            $routes->post('sodelete/(:any)', 'Inventory::delso/$1');
            $routes->post('pascaso', 'Inventory::pascaso');
            $routes->get('cetaklembarso/(:any)/(:any)', 'Inventory::cetakformulir/$1/$2');
            $routes->get('cetaklembarhasilso/(:any)/(:any)', 'Inventory::hasilso/$1/$2');
            $routes->get('cetaklembarhasilselisihso/(:any)/(:any)', 'Inventory::hasilsosel/$1/$2');
            // Bagian Permohonan Mutasi Gudang
            $routes->get('permohonanmutasigudang', 'Inventory::permohonan_mutasi_gudang');
            $routes->get('permohonanmutasigudangtablefilter/(:any)/(:any)', 'Inventory::permohonan_mutasi_gudang_table_filter/$1/$2');
            $routes->get('permohonanmutasigudang/entri', 'Inventory::permohonan_mutasi_gudang_entri');
            $routes->get('permohonanmutasigudangedit/edit/(:any)/(:any)', 'Inventory::permohonan_mutasi_gudang_edit/$1/$2');
            $routes->get('permohonanmutasigudang/view/(:any)/(:any)', 'Inventory::permohonan_mutasi_gudang_view/$1/$2');
            $routes->post('permohonanmutasigudang/save/(:any)', 'Inventory::permohonan_mutasi_gudang_save/$1');
            $routes->post('permohonanmutasigudang/saveview/(:any)', 'Inventory::permohonan_mutasi_gudang_save_view/$1');
            $routes->post('DeleteDataPermohonanMutasiGudang/(:any)', 'Inventory::Delete_Data_Permohonan_Mutasi_Gudang/$1');
            // Bagian Mutasi Gudang
            $routes->get('mutasigudang', 'Inventory::mutasi_gudang');
            $routes->get('mutasigudangtable/(:any)/(:any)', 'Inventory::mutasi_gudang_table/$1/$2');
            $routes->get('mutasigudang/entri', 'Inventory::mutasi_gudang_entri');
            $routes->get('mutasigudang/edit/(:any)/(:any)', 'Inventory::mutasigudangedit/$1/$2');
            $routes->get('mutasigudang/showbarangmutasigudang/(:any)/(:any)', 'Inventory::mutasi_gudang_GetDataBarang/$1/$2');
            $routes->get('mutasigudang/showmmg/(:any)', 'Inventory::mutasi_gudang_getlistmmg/$1');
            $routes->get('mutasigudang/getheadermutasi/(:any)', 'Inventory::getmmgheader/$1');
            $routes->get('mutasigudang/getdataandcheckqty/(:any)', 'Inventory::mutasi_gudang_GetDataAndCheckQty/$1');
            $routes->post('mutasigudang/save/(:any)', 'Inventory::mutasi_gudang_save/$1');
            $routes->post('delmutasigudang/(:any)', 'Inventory::del_mutasi_gudang/$1');
            // Bagian Persetujuan Mutasi Gudang
            $routes->get('persetujuanmutasigudang', 'Inventory::persetujuan_mutasi_gudang');
            $routes->get('persetujuanmutasigudangtable/(:any)/(:any)', 'Inventory::acc_mutasi_gudang_table/$1/$2');
            $routes->get('persetujuanmutasigudang/entri', 'Inventory::persetujuan_mutasi_gudang_entri');
            $routes->get('persetujuanmutasigudang/showmg/(:any)', 'Inventory::persetujuan_mutasi_gudang_getlistmg/$1');
            $routes->get('persetujuanmutasigudang/getdataandcheckqty/(:any)', 'Inventory::acc_gudang_GetDataAndCheckQty/$1');
            $routes->get('persetujuanmutasigudang/getheaderacc/(:any)', 'Inventory::getmgheader/$1');
            $routes->post('persetujuanmutasigudang/save/(:any)', 'Inventory::acc_mutasi_gudang_save/$1');
            $routes->get('persetujuanmutasigudang/view/(:any)/(:any)', 'Inventory::acc_mutasigudang_view/$1/$2');
            $routes->post('delpersetujuanmutasigudang/(:any)', 'Inventory::del_acc_mutasi_gudang/$1');
            // Bagian Permohonan Mutasi CABANG
            $routes->get('permohonanmutasicabang', 'Inventory::permohonan_mutasi_cabang');
            $routes->get('permohonanmutasicabangtablefilter/(:any)/(:any)', 'Inventory::permohonan_mutasi_cabang_table_filter/$1/$2');
            $routes->get('permohonanmutasicabang/entri(:any)?', 'Inventory::permohonanmutasicabang_entri$1');
            $routes->post('permohonanmutasicabangsave', 'Inventory::permohonanMutasiCabang_save');
            $routes->post('permohonanmutasicabangdelete/(:any)', 'Inventory::permohonanMutasiCabang_delete/$1');
            $routes->get('permohonanmutasicabangcetak/(:any)', 'Inventory::cetak_mohoncabang/$1');
            // Bagian Mutasi CABANG
            $routes->get('mutasicabang', 'Inventory::mutasi_cabang');
            $routes->get('mutasicabangtable/(:any)/(:any)', 'Inventory::mutasi_cabang_table/$1/$2');
            $routes->get('mutasicabang/entri(:any)?', 'Inventory::mutasicabangentri$1');
            $routes->get('mutasicabang/showmmc/(:any)', 'Inventory::mutasi_cabang_getlistmmc/$1');
            $routes->get('mutasicabang/getdataandcheckqty/(:any)', 'Inventory::mutasi_cabang_GetDataAndCheckQty/$1');
            $routes->get('mutasicabang/showbarangmutasicabang/(:any)/(:any)', 'Inventory::mutasi_cabang_GetDataBarang/$1/$2');
            $routes->post('mutasicabangsave', 'Inventory::MutasiCabang_save');
            $routes->post('delmutasicabang/(:any)', 'Inventory::del_mutasicabang/$1');
            $routes->get('mutasicabangcetaktracer/(:any)', 'Inventory::cetaktracer_mutasicabang/$1');
            $routes->get('mutasicabangcetak/(:any)', 'Inventory::cetak_mutasicabang/$1');
            // Bagian PERSETUJUAN Mutasi CABANG
            $routes->get('persetujuanmutasicabang', 'Inventory::persetujuan_mutasi_cabang');
            $routes->get('persetujuanmutasicabangtable/(:any)/(:any)', 'Inventory::acc_mutasi_cabang_table_filter/$1/$2');
            $routes->get('persetujuanmutasicabang/entri(:any)?', 'Inventory::persetujuanmutasicabangentri$1');
            $routes->get('persetujuanmutasicabang/showmc/(:any)', 'Inventory::AccShowMc/$1');
            $routes->get('persetujuanmutasicabang/GetDataHeaderMC/(:any)', 'Inventory::GetDataHeadMC/$1');
            $routes->get('persetujuanmutasicabang/getheadermohon/(:any)', 'Inventory::GetDataHeadMMC/$1');
            $routes->post('persetujuanmutasicabangsave', 'Inventory::ACCMutasiCabang_save');
            $routes->post('permohonanmutasicabangdelete/(:any)', 'Inventory::permohonanMutasiCabang_delete/$1');
            $routes->get('accmutasicabangcetak/(:any)', 'Inventory::cetak_acccabang/$1');
        });
        //Third Link
        $routes->get('laporan', 'Inventory::laporan');
        $routes->get('laporan/cetaklaporan/(:segment)', 'Inventory::CetakLaporan/$1');
        //Fourth Link
        $routes->get('importdatabarang', 'Inventory::import_data_barang');
        //Five Link
        $routes->group('exportdata', static function ($routes) {
            $routes->get('datastock', 'Inventory::data_stock');
            $routes->get('export_stock_table/(:any)', 'Inventory::exportstock_table/$1');
        });
    });

    // Utilitas Section Route
    $routes->group('utilitas', static function ($routes) {
        $routes->get('pengguna', 'Utilitas::pengguna');
        $routes->get('penggunatable', 'Utilitas::pengguna_table');
        $routes->get('penggunaview/(:any)', 'Utilitas::pengguna_view/$1');
        $routes->post('penggunadelete/(:any)', 'Utilitas::pengguna_delete/$1');
        $routes->post('penggunasave', 'Utilitas::pengguna_save');

        $routes->get('gruppemakai', 'Utilitas::grup_pemakai');
        $routes->get('gruppemakaitable', 'Utilitas::grup_pemakai_table');
        $routes->post('gruppemakai/save', 'Utilitas::save_gruppemakai');
        $routes->get('gruppemakai/viewmodulgroup/(:any)', 'Utilitas::viewmodulgroup/$1');
        $routes->get('gruppemakai/menu/(:any)', 'Utilitas::menumodulgroup/$1');
        $routes->post('gruppemakai/savefrmmenu', 'Utilitas::tambah_itemmenu');
        $routes->post('gruppemakai/savefrmgrup', 'Utilitas::save_frmmenu');
        $routes->post('gruppemakai/deldatagrup/(:any)', 'Utilitas::deldatagrup/$1');
        $routes->post('gruppemakai/deldata/(:any)', 'Utilitas::deldata/$1');

        $routes->get('kodecabang', 'Utilitas::kode_cabang');
        $routes->get('kodecabangtable', 'Utilitas::kode_cabang_table');
        $routes->post('kodecabangsave', 'Utilitas::kode_cabang_save');
        $routes->get('kodecabangview/(:any)', 'Utilitas::kode_cabang_view/$1');
        $routes->post('kodecabangdelete/(:any)', 'Utilitas::kode_cabang_delete/$1');

        $routes->get('profileedit', 'Utilitas::profile_edit');
        $routes->post('profile/identitas/(:any)', 'Utilitas::update_profile/$1');
        $routes->post('profile/peridedata/(:any)', 'Utilitas::update_profile/$1');
        $routes->post('profile/akuncoa/(:any)', 'Utilitas::update_profile/$1');

        $routes->get('nomorbukti', 'Utilitas::nomor_bukti');
        $routes->get('nomorbuktitable', 'Utilitas::nomor_bukti_table');
        $routes->get('nomorbukti/datanobukti/(:any)', 'Utilitas::data_nobukti/$1');
        $routes->get('nomorbuktitablepajak', 'Utilitas::nomor_bukti_Fpajak_table');
        $routes->get('nomorbuktifp/datanobuktifp/(:any)', 'Utilitas::data_nobuktifp/$1');
        $routes->post('nomorbukti/save', 'Utilitas::savenobukti');
        $routes->post('nomorbukti/delete/(:any)', 'Utilitas::del_datanobukti/$1');
    });

    // Master User Section Route
    $routes->group('master_user', static function ($routes) {
        $routes->get('userprofile', 'Master_user_profile::profileview');
        $routes->post('savebioprofileuser', 'Master_user_profile::savebio');
        $routes->post('saveimgprofileuser', 'Master_user_profile::uploadImg');
        $routes->post('savepwprofileuser', 'Master_user_profile::Savepw');
    });


    //Data Submit
    $routes->post('submit/(:num)', 'Pembelian::submit_data/$1');
} else {
}


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
