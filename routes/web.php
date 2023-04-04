<?php
use App\Http\Controllers\CLogin;
use App\Http\Controllers\CDashboard;
use App\Http\Controllers\CTipeProperti;
use App\Http\Controllers\CTipeBooking;
use App\Http\Controllers\CJenisTempat;
use App\Http\Controllers\CAkhirPekan;
use App\Http\Controllers\CNegara;
use App\Http\Controllers\CProvinsi;
use App\Http\Controllers\CKota;
use App\Http\Controllers\CFasilitas;
use App\Http\Controllers\CAmenities;
use App\Http\Controllers\CPromosiKendaraan;
use App\Http\Controllers\CPromosiWisata;
use App\Http\Controllers\CConciergeService;
use App\Http\Controllers\CBank;
use App\Http\Controllers\CBankAdmin;
use App\Http\Controllers\CAds;
use App\Http\Controllers\CKupon;
use App\Http\Controllers\CAboutUs;
use App\Http\Controllers\CFaq;
use App\Http\Controllers\CTermConditionDetail;
use App\Http\Controllers\CPrivacyPolicyDetail;
use App\Http\Controllers\CProperti;
use App\Http\Controllers\CBooking;
use App\Http\Controllers\CSetting;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\CMCPayment;
use App\Http\Controllers\CChat;
use App\Http\Controllers\CUser;
use App\Http\Controllers\CBanner;
use App\Http\Controllers\CRole;
use App\Http\Controllers\CCron;
use App\Http\Controllers\CSplashSlide;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CLogin::class,'index'])->middleware("guest");
Route::post('/auth', [CLogin::class,'authenticate']);
Route::get('/logout', [CLogin::class,'logout']);
Route::post('callback-order', [CMCPayment::class, 'index']);
Route::get('set-lunas/{id}', [CMCPayment::class, 'set_lunas']);
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('redirect-invoice-sukses', [CMCPayment::class, 'redirect_invoice_sukses']);
Route::get('redirect-invoice-gagal', [CMCPayment::class, 'redirect_invoice_gagal']);
Route::get('update-expired-time', [CCron::class, 'update_expired_time']);

Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', [CDashboard::class,'index'])->name('dashboard');
    Route::get('/dashboard/data-booking', [CDashboard::class,'data_booking']);
    //iipe properti
    Route::get('/tipe-properti', [CTipeProperti::class,'index'])->name('tipe-properti-index')->middleware('check.role:Master - Tipe Properti');
    Route::get('/tipe-properti/data', [CTipeProperti::class,'data']);
    Route::get('/tipe-properti/create', [CTipeProperti::class,'create']);
    Route::get('/tipe-properti/create-bahasa/{id}/{kode}', [CTipeProperti::class,'create_bahasa']);
    Route::post('/tipe-properti/create-save', [CTipeProperti::class,'create_save']);
    Route::get('/tipe-properti/show/{id}', [CTipeProperti::class,'show']);
    Route::get('/tipe-properti/detail/{id}', [CTipeProperti::class,'detail']);
    Route::post('/tipe-properti/show-save/{id}', [CTipeProperti::class,'show_save']);
    Route::get('/tipe-properti/delete/{id}', [CTipeProperti::class,'delete']);
    Route::get('/tipe-properti/bahasa', [CTipeProperti::class,'bahasa']);

    //tipe booking
    Route::get('/tipe-booking', [CTipeBooking::class,'index'])->name('tipe-booking-index');
    Route::get('/tipe-booking/data', [CTipeBooking::class,'data']);
    Route::get('/tipe-booking/create', [CTipeBooking::class,'create']);
    Route::get('/tipe-booking/create-bahasa/{id}/{kode}', [CTipeBooking::class,'create_bahasa']);
    Route::post('/tipe-booking/create-save', [CTipeBooking::class,'create_save']);
    Route::get('/tipe-booking/show/{id}', [CTipeBooking::class,'show']);
    Route::get('/tipe-booking/detail/{id}', [CTipeBooking::class,'detail']);
    Route::post('/tipe-booking/show-save/{id}', [CTipeBooking::class,'show_save']);
    Route::get('/tipe-booking/delete/{id}', [CTipeBooking::class,'delete']);
    Route::get('/tipe-booking/bahasa', [CTipeBooking::class,'bahasa']);

    //jenis tempat
    Route::get('/jenis-tempat', [CJenisTempat::class,'index'])->name('jenis-tempat-index');
    Route::get('/jenis-tempat/data', [CJenisTempat::class,'data']);
    Route::get('/jenis-tempat/create', [CJenisTempat::class,'create']);
    Route::get('/jenis-tempat/create-bahasa/{id}/{kode}', [CJenisTempat::class,'create_bahasa']);
    Route::post('/jenis-tempat/create-save', [CJenisTempat::class,'create_save']);
    Route::get('/jenis-tempat/show/{id}', [CJenisTempat::class,'show']);
    Route::get('/jenis-tempat/detail/{id}', [CJenisTempat::class,'detail']);
    Route::post('/jenis-tempat/show-save/{id}', [CJenisTempat::class,'show_save']);
    Route::get('/jenis-tempat/delete/{id}', [CJenisTempat::class,'delete']);
    Route::get('/jenis-tempat/bahasa', [CJenisTempat::class,'bahasa']);

    //akhir pekan
    Route::get('/akhir-pekan', [CAkhirPekan::class,'index'])->name('akhir-pekan-index');
    Route::get('/akhir-pekan/data', [CAkhirPekan::class,'data']);
    Route::get('/akhir-pekan/create', [CAkhirPekan::class,'create']);
    Route::get('/akhir-pekan/create-bahasa/{id}/{kode}', [CAkhirPekan::class,'create_bahasa']);
    Route::post('/akhir-pekan/create-save', [CAkhirPekan::class,'create_save']);
    Route::get('/akhir-pekan/show/{id}', [CAkhirPekan::class,'show']);
    Route::get('/akhir-pekan/detail/{id}', [CAkhirPekan::class,'detail']);
    Route::post('/akhir-pekan/show-save/{id}', [CAkhirPekan::class,'show_save']);
    Route::get('/akhir-pekan/delete/{id}', [CAkhirPekan::class,'delete']);
    Route::get('/akhir-pekan/bahasa', [CAkhirPekan::class,'bahasa']);

    //negara
    Route::get('/negara', [CNegara::class,'index'])->name('negara-index')->middleware('check.role:Master - Negara');
    Route::get('/negara/data', [CNegara::class,'data']);
    Route::get('/negara/create', [CNegara::class,'create']);
    Route::post('/negara/create-save', [CNegara::class,'create_save']);
    Route::get('/negara/show/{id}', [CNegara::class,'show']);
    Route::get('/negara/detail/{id}', [CNegara::class,'detail']);
    Route::post('/negara/show-save/{id}', [CNegara::class,'show_save']);
    Route::get('/negara/delete/{id}', [CNegara::class,'delete']);

    //provinsi
    Route::get('/provinsi', [CProvinsi::class,'index'])->name('provinsi-index')->middleware('check.role:Master - Provinsi');
    Route::get('/provinsi/data', [CProvinsi::class,'data']);
    Route::get('/provinsi/create', [CProvinsi::class,'create']);
    Route::post('/provinsi/create-save', [CProvinsi::class,'create_save']);
    Route::get('/provinsi/show/{id}', [CProvinsi::class,'show']);
    Route::get('/provinsi/detail/{id}', [CProvinsi::class,'detail']);
    Route::post('/provinsi/show-save/{id}', [CProvinsi::class,'show_save']);
    Route::get('/provinsi/delete/{id}', [CProvinsi::class,'delete']);

    //kota
    Route::get('/kota', [CKota::class,'index'])->name('kota-index')->middleware('check.role:Master - Kota');
    Route::get('/kota/data', [CKota::class,'data']);
    Route::get('/kota/create', [CKota::class,'create']);
    Route::post('/kota/create-save', [CKota::class,'create_save']);
    Route::get('/kota/show/{id}', [CKota::class,'show']);
    Route::get('/kota/detail/{id}', [CKota::class,'detail']);
    Route::post('/kota/show-save/{id}', [CKota::class,'show_save']);
    Route::get('/kota/delete/{id}', [CKota::class,'delete']);

    //fasilitas
    Route::get('/fasilitas', [CFasilitas::class,'index'])->name('fasilitas-index')->middleware('check.role:Master - Fasilitas');
    Route::get('/fasilitas/data', [CFasilitas::class,'data']);
    Route::get('/fasilitas/create', [CFasilitas::class,'create']);
    Route::post('/fasilitas/create-save', [CFasilitas::class,'create_save']);
    Route::get('/fasilitas/show/{id}', [CFasilitas::class,'show']);
    Route::get('/fasilitas/detail/{id}', [CFasilitas::class,'detail']);
    Route::post('/fasilitas/show-save/{id}', [CFasilitas::class,'show_save']);
    Route::get('/fasilitas/delete/{id}', [CFasilitas::class,'delete']);
    Route::get('/fasilitas/create-bahasa/{id}/{kode}', [CFasilitas::class,'create_bahasa']);
    Route::get('/fasilitas/bahasa', [CFasilitas::class,'bahasa']);

    //amenities
    Route::get('/amenities', [CAmenities::class,'index'])->name('amenities-index')->middleware('check.role:Master - Amenities');
    Route::get('/amenities/data', [CAmenities::class,'data']);
    Route::get('/amenities/create', [CAmenities::class,'create']);
    Route::post('/amenities/create-save', [CAmenities::class,'create_save']);
    Route::get('/amenities/show/{id}', [CAmenities::class,'show']);
    Route::get('/amenities/detail/{id}', [CAmenities::class,'detail']);
    Route::post('/amenities/show-save/{id}', [CAmenities::class,'show_save']);
    Route::get('/amenities/delete/{id}', [CAmenities::class,'delete']);
    Route::get('/amenities/create-bahasa/{id}/{kode}', [CAmenities::class,'create_bahasa']);
    Route::get('/amenities/bahasa', [CAmenities::class,'bahasa']);

    //promosi-kendaraan
    Route::get('/promosi-kendaraan', [CPromosiKendaraan::class,'index'])->name('promosi-kendaraan-index')->middleware('check.role:Master - Promosi Kendaraan');
    Route::get('/promosi-kendaraan/data', [CPromosiKendaraan::class,'data']);
    Route::get('/promosi-kendaraan/create', [CPromosiKendaraan::class,'create']);
    Route::post('/promosi-kendaraan/create-save', [CPromosiKendaraan::class,'create_save']);
    Route::get('/promosi-kendaraan/show/{id}', [CPromosiKendaraan::class,'show']);
    Route::get('/promosi-kendaraan/detail/{id}', [CPromosiKendaraan::class,'detail']);
    Route::post('/promosi-kendaraan/show-save/{id}', [CPromosiKendaraan::class,'show_save']);
    Route::get('/promosi-kendaraan/delete/{id}', [CPromosiKendaraan::class,'delete']);
    Route::get('/promosi-kendaraan/create-bahasa/{id}/{kode}', [CPromosiKendaraan::class,'create_bahasa']);
    Route::get('/promosi-kendaraan/bahasa', [CPromosiKendaraan::class,'bahasa']);

    //promosi-wisata
    Route::get('/promosi-wisata', [CPromosiWisata::class,'index'])->name('promosi-wisata-index')->middleware('check.role:Master - Promosi Wisata');
    Route::get('/promosi-wisata/data', [CPromosiWisata::class,'data']);
    Route::get('/promosi-wisata/create', [CPromosiWisata::class,'create']);
    Route::post('/promosi-wisata/create-save', [CPromosiWisata::class,'create_save']);
    Route::get('/promosi-wisata/show/{id}', [CPromosiWisata::class,'show']);
    Route::get('/promosi-wisata/detail/{id}', [CPromosiWisata::class,'detail']);
    Route::post('/promosi-wisata/show-save/{id}', [CPromosiWisata::class,'show_save']);
    Route::get('/promosi-wisata/delete/{id}', [CPromosiWisata::class,'delete']);
    Route::get('/promosi-wisata/create-bahasa/{id}/{kode}', [CPromosiWisata::class,'create_bahasa']);
    Route::get('/promosi-wisata/bahasa', [CPromosiWisata::class,'bahasa']);

    //concierge-service
    Route::get('/concierge-service', [CConciergeService::class,'index'])->name('concierge-service-index')->middleware('check.role:Master - Concierge Service');
    Route::get('/concierge-service/data', [CConciergeService::class,'data']);
    Route::get('/concierge-service/create', [CConciergeService::class,'create']);
    Route::post('/concierge-service/create-save', [CConciergeService::class,'create_save']);
    Route::get('/concierge-service/show/{id}', [CConciergeService::class,'show']);
    Route::get('/concierge-service/detail/{id}', [CConciergeService::class,'detail']);
    Route::post('/concierge-service/show-save/{id}', [CConciergeService::class,'show_save']);
    Route::get('/concierge-service/delete/{id}', [CConciergeService::class,'delete']);
    Route::get('/concierge-service/create-bahasa/{id}/{kode}', [CConciergeService::class,'create_bahasa']);
    Route::get('/concierge-service/bahasa', [CConciergeService::class,'bahasa']);

    //bank
    Route::get('/master-bank', [CBank::class,'index'])->name('master-bank-index')->middleware('check.role:Master - Bank');
    Route::get('/bank/data', [CBank::class,'data']);
    Route::get('/bank/create', [CBank::class,'create']);
    Route::post('/bank/create-save', [CBank::class,'create_save']);
    Route::get('/bank/show/{id}', [CBank::class,'show']);
    Route::get('/bank/detail/{id}', [CBank::class,'detail']);
    Route::post('/bank/show-save/{id}', [CBank::class,'show_save']);
    Route::get('/bank/delete/{id}', [CBank::class,'delete']);

    //bank-admin
    Route::get('/bank-admin', [CBankAdmin::class,'index'])->name('bank-admin-index')->middleware('check.role:Master - Bank Admin');
    Route::get('/bank-admin/data', [CBankAdmin::class,'data']);
    Route::get('/bank-admin/create', [CBankAdmin::class,'create']);
    Route::post('/bank-admin/create-save', [CBankAdmin::class,'create_save']);
    Route::get('/bank-admin/show/{id}', [CBankAdmin::class,'show']);
    Route::get('/bank-admin/detail/{id}', [CBankAdmin::class,'detail']);
    Route::post('/bank-admin/show-save/{id}', [CBankAdmin::class,'show_save']);
    Route::get('/bank-admin/delete/{id}', [CBankAdmin::class,'delete']);

    //ads
    Route::get('/ads', [CAds::class,'index'])->name('ads-index')->middleware('check.role:Master - Ads');
    Route::get('/ads/data', [CAds::class,'data']);
    Route::get('/ads/create', [CAds::class,'create']);
    Route::post('/ads/create-save', [CAds::class,'create_save']);
    Route::get('/ads/show/{id}', [CAds::class,'show']);
    Route::get('/ads/detail/{id}', [CAds::class,'detail']);
    Route::post('/ads/show-save/{id}', [CAds::class,'show_save']);
    Route::get('/ads/delete/{id}', [CAds::class,'delete']);
    Route::get('/ads/setting', [CAds::class,'setting']);
    Route::post('/ads/setting-save', [CAds::class,'setting_save']);
    Route::get('/ads/list-properti', [CAds::class,'list_properti']);

    //kupon
    Route::get('/kupon', [CKupon::class,'index'])->name('kupon-index')->middleware('check.role:Master - Kupon');
    Route::get('/kupon/data', [CKupon::class,'data']);
    Route::get('/kupon/create', [CKupon::class,'create']);
    Route::post('/kupon/create-save', [CKupon::class,'create_save']);
    Route::get('/kupon/show/{id}', [CKupon::class,'show']);
    Route::get('/kupon/detail/{id}', [CKupon::class,'detail']);
    Route::post('/kupon/show-save/{id}', [CKupon::class,'show_save']);
    Route::get('/kupon/delete/{id}', [CKupon::class,'delete']);

    //about-us
    Route::get('/about-us', [CAboutUs::class,'index'])->name('about-us-index')->middleware('check.role:Master - About Us');
    Route::get('/about-us/data', [CAboutUs::class,'data']);
    Route::get('/about-us/create', [CAboutUs::class,'create']);
    Route::post('/about-us/create-save', [CAboutUs::class,'create_save']);
    Route::get('/about-us/show/{id}', [CAboutUs::class,'show']);
    Route::get('/about-us/detail/{id}', [CAboutUs::class,'detail']);
    Route::post('/about-us/show-save/{id}', [CAboutUs::class,'show_save']);
    Route::get('/about-us/delete/{id}', [CAboutUs::class,'delete']);
    Route::get('/about-us/create-bahasa/{id}/{kode}', [CAboutUs::class,'create_bahasa']);
    Route::get('/about-us/bahasa', [CAboutUs::class,'bahasa']);

    //faq
    Route::get('/faq', [CFaq::class,'index'])->name('faq-index')->middleware('check.role:Master - Faq');
    Route::get('/faq/data', [CFaq::class,'data']);
    Route::get('/faq/create', [CFaq::class,'create']);
    Route::post('/faq/create-save', [CFaq::class,'create_save']);
    Route::get('/faq/show/{id}', [CFaq::class,'show']);
    Route::get('/faq/detail/{id}', [CFaq::class,'detail']);
    Route::post('/faq/show-save/{id}', [CFaq::class,'show_save']);
    Route::get('/faq/delete/{id}', [CFaq::class,'delete']);
    Route::get('/faq/create-bahasa/{id}/{kode}', [CFaq::class,'create_bahasa']);
    Route::get('/faq/bahasa', [CFaq::class,'bahasa']);

    //term-condition
    Route::get('/term-condition', [CTermConditionDetail::class,'index'])->name('term-condition-index')->middleware('check.role:Master - Term Condition');
    Route::get('/term-condition/data', [CTermConditionDetail::class,'data']);
    Route::get('/term-condition/create', [CTermConditionDetail::class,'create']);
    Route::post('/term-condition/create-save', [CTermConditionDetail::class,'create_save']);
    Route::get('/term-condition/show/{id}', [CTermConditionDetail::class,'show']);
    Route::get('/term-condition/detail/{id}', [CTermConditionDetail::class,'detail']);
    Route::post('/term-condition/show-save/{id}', [CTermConditionDetail::class,'show_save']);
    Route::get('/term-condition/delete/{id}', [CTermConditionDetail::class,'delete']);
    Route::get('/term-condition/create-bahasa/{id}/{kode}', [CTermConditionDetail::class,'create_bahasa']);
    Route::get('/term-condition/bahasa', [CTermConditionDetail::class,'bahasa']);

    //privacy-policy
    Route::get('/privacy-policy', [CPrivacyPolicyDetail::class,'index'])->name('privacy-policy-index')->middleware('check.role:Master - Privacy Policy');
    Route::get('/privacy-policy/data', [CPrivacyPolicyDetail::class,'data']);
    Route::get('/privacy-policy/create', [CPrivacyPolicyDetail::class,'create']);
    Route::post('/privacy-policy/create-save', [CPrivacyPolicyDetail::class,'create_save']);
    Route::get('/privacy-policy/show/{id}', [CPrivacyPolicyDetail::class,'show']);
    Route::get('/privacy-policy/detail/{id}', [CPrivacyPolicyDetail::class,'detail']);
    Route::post('/privacy-policy/show-save/{id}', [CPrivacyPolicyDetail::class,'show_save']);
    Route::get('/privacy-policy/delete/{id}', [CPrivacyPolicyDetail::class,'delete']);
    Route::get('/privacy-policy/create-bahasa/{id}/{kode}', [CPrivacyPolicyDetail::class,'create_bahasa']);
    Route::get('/privacy-policy/bahasa', [CPrivacyPolicyDetail::class,'bahasa']);

    //properti
    Route::get('/list-properti', [CProperti::class,'index'])->name('properti-index')->middleware('check.role:Properti - List Properti');
    Route::get('/properti/data', [CProperti::class,'data']);
    Route::get('/properti-add', [CProperti::class,'add'])->middleware('check.role:Properti - Tambah Properti');
    Route::get('/properti-add/{id}', [CProperti::class,'addByTipe']);
    Route::post('/properti/create-save', [CProperti::class,'create_save']);
    Route::post('/properti/periode-save', [CProperti::class,'periode_save']);
    Route::post('/properti/show-save/{id}', [CProperti::class,'show_save']);
    Route::get('/properti/show/{id}', [CProperti::class,'show']);
    Route::get('/properti/detail/{id}', [CProperti::class,'detail']);
    Route::get('/properti/periode-delete', [CProperti::class,'periode_delete']);
    Route::get('/properti/create-bahasa/{id}/{kode}', [CProperti::class,'create_bahasa']);
    Route::get('/properti/bahasa', [CProperti::class,'bahasa']);
    Route::get('/properti/delete/{id}', [CProperti::class,'delete']);
    Route::post('/properti/upload', [CProperti::class,'upload']);
    Route::get('/properti/delete-img/{id}', [CProperti::class,'delete_img']);
    Route::get('/properti/kota-provinsi/', [CProperti::class,'kotaByProvinsi']);

    //booking
    Route::get('/list-booking', [CBooking::class,'index'])->name('booking-index')->middleware('check.role:Booking - List Booking');
    Route::get('/booking/data', [CBooking::class,'data']);
    Route::get('/booking/detail/{id}', [CBooking::class,'detail']);
    Route::post('/booking/confirm/{id}', [CBooking::class,'confirm']);
    Route::post('/booking/decline/{id}', [CBooking::class,'decline']);
    Route::post('/booking/extra/{id}', [CBooking::class,'extra']);
    Route::post('/booking/discount/{id}', [CBooking::class,'discount']);
    Route::post('/booking/cek-tanggal/', [CBooking::class,'cek_tanggal']);

    //setting
    Route::get('/setting', [CSetting::class,'index'])->name('setting-index')->middleware('check.role:Setting - Data Setting');
    Route::get('/setting/data', [CSetting::class,'data']);
    Route::get('/setting/show/{id}', [CSetting::class,'show']);
    Route::post('/setting/show-save/{id}', [CSetting::class,'show_save']);

    //user
    Route::get('/user', [CUser::class,'index'])->name('user-index')->middleware('check.role:Setting - User');
    Route::get('/user/data', [CUser::class,'data']);
    Route::get('/user/create', [CUser::class,'create']);
    Route::post('/user/create-save', [CUser::class,'create_save']);
    Route::get('/user/show/{id}', [CUser::class,'show']);
    Route::get('/user/detail/{id}', [CUser::class,'detail']);
    Route::post('/user/show-save/{id}', [CUser::class,'show_save']);
    Route::get('/user/delete/{id}', [CUser::class,'delete']);
    Route::post('/user/reset-pass', [CUser::class,'reset_password']);
    
    //profile
    Route::get('/user/profile', [CUser::class,'profile'])->name('profile-index');
    Route::post('/user/profile-save/{id}', [CUser::class,'profile_save']);
    Route::post('/user/ubah-pass', [CUser::class,'ubah_password']);

    //CHAT
    Route::get('/chat', [CChat::class,'index'])->name('chat-index')->middleware('check.role:Messages');
    Route::get('/chat/get-chat/{id}', [CChat::class,'chat_detail']);
    Route::post('/chat/tambah-chat-detail', [CChat::class,'tambah_chat_detail']);

    // Route::get('/trip-detail', [CMCPayment::class,'pdf_email']);

    //banner
    Route::get('/banner', [CBanner::class,'index'])->name('master-banner-index')->middleware('check.role:Master - Banner');
    Route::get('/banner/data', [CBanner::class,'data']);
    Route::get('/banner/create', [CBanner::class,'create']);
    Route::post('/banner/create-save', [CBanner::class,'create_save']);
    Route::get('/banner/show/{id}', [CBanner::class,'show']);
    Route::get('/banner/detail/{id}', [CBanner::class,'detail']);
    Route::post('/banner/show-save/{id}', [CBanner::class,'show_save']);
    Route::get('/banner/delete/{id}', [CBanner::class,'delete']);

    //Role
    Route::get('/role', [CRole::class,'index'])->name('role-index')->middleware('check.role:Setting - role');
    Route::get('/role/data', [CRole::class,'data']);
    Route::get('/role/create', [CRole::class,'create']);
    Route::post('/role/create-save', [CRole::class,'create_save']);
    Route::get('/role/show/{id}', [CRole::class,'show']);
    Route::get('/role/detail/{id}', [CRole::class,'detail']);
    Route::post('/role/show-save/{id}', [CRole::class,'show_save']);
    Route::get('/role/delete/{id}', [CRole::class,'delete']);
    Route::get('/role/get_menu', [CRole::class,'get_menu']);
    Route::post('/role/save_menu', [CRole::class,'save_menu']);

    //splash_slide
    Route::get('/splash-slide', [CSplashSlide::class,'index'])->name('splash-slide-index');
    Route::get('/splash-slide/data', [CSplashSlide::class,'data']);
    Route::get('/splash-slide/create', [CSplashSlide::class,'create']);
    Route::post('/splash-slide/create-save', [CSplashSlide::class,'create_save']);
    Route::get('/splash-slide/show/{id}', [CSplashSlide::class,'show']);
    Route::get('/splash-slide/detail/{id}', [CSplashSlide::class,'detail']);
    Route::post('/splash-slide/show-save/{id}', [CSplashSlide::class,'show_save']);
    Route::get('/splash-slide/delete/{id}', [CSplashSlide::class,'delete']);
});