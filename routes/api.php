<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CAPropertiTipe;
use App\Http\Controllers\Api\CAProperti;
use App\Http\Controllers\Api\CAPromosiWisata;
use App\Http\Controllers\Api\CAPromosiKendaraan;
use App\Http\Controllers\Api\CAConciergeService;
use App\Http\Controllers\Api\CAKota;
use App\Http\Controllers\Api\CAAmenities;
use App\Http\Controllers\Api\CAFasilitas;
use App\Http\Controllers\Api\CAAboutUs;
use App\Http\Controllers\Api\CAFaq;
use App\Http\Controllers\Api\CATermCondition;
use App\Http\Controllers\Api\CAPrivacyPolicy;
use App\Http\Controllers\Api\CAAuth;
use App\Http\Controllers\Api\CAFavorit;
use App\Http\Controllers\Api\CABooking;
use App\Http\Controllers\Api\CAProfile;
use App\Http\Controllers\Api\CAAds;
use App\Http\Controllers\Api\CASetting;
use App\Http\Controllers\Api\CANegara;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => 'logapi'], function () {	
    Route::get('/get-property-type', [CAPropertiTipe::class, 'get_property_type']);    
    Route::get('/get-property-by-type', [CAProperti::class, 'get_property_type']);
    Route::get('/get-promotion-tour', [CAPromosiWisata::class, 'get_promotion_tour']);
    Route::get('/get-promotion-transportation', [CAPromosiKendaraan::class, 'get_promotion_transportation']);
    Route::get('/get-concierge-service', [CAConciergeService::class, 'get_concierge_service']);
    Route::get('/get-city', [CAKota::class, 'get_city']);
    Route::get('/get-amenities', [CAAmenities::class, 'get_amenities']);
    Route::get('/get-facilities', [CAFasilitas::class, 'get_facilities']);
    Route::get('/get-about-us', [CAAboutUs::class, 'get_about_us']);
    Route::get('/get-faq', [CAFaq::class, 'get_faq']);
    Route::get('/get-term-and-condition', [CATermCondition::class, 'get_term_and_condition']);
    Route::get('/get-privacy-policy', [CAPrivacyPolicy::class, 'get_privacy_policy']);
    Route::get('/get-property-detail', [CAProperti::class, 'get_property_detail']);
    Route::get('/get-property', [CAProperti::class, 'get_property']);
    Route::get('/auth-signup', [CAAuth::class, 'register']);
    Route::get('/auth-signin', [CAAuth::class, 'login']);
    Route::get('/get-property-booking', [CAProperti::class, 'get_property_detail_harga']);
    Route::get('/get-property-detail-amenities-fasilitas', [CAProperti::class, 'get_property_detail_amenities_fasilitas']);
    Route::get('/get-property-detail-review', [CAProperti::class, 'get_property_detail_review']);
    Route::get('auth/google/callback', [CAAuth::class, 'handleProviderCallback']);
    Route::get('/get-ads', [CAAds::class, 'get_ads']);
    Route::get('/get-setting', [CASetting::class, 'get_setting']);
    Route::get('/get-best-destinations', [CAKota::class, 'get_best_destination']);
    Route::get('/get-properti-by-city', [CAProperti::class, 'get_properti_by_city']);
    Route::get('/get-city-search', [CAKota::class, 'get_city_search']);
    Route::get('/get-property-by-facilities', [CAProperti::class, 'get_property_by_facilities']);
    Route::get('/get-country', [CANegara::class, 'get_country']);
    Route::post('/post-forget-password', [CAAuth::class, 'post_forget_password']);
});

Route::group(['prefix' => 'v','middleware' => 'myauth'], function () {
    Route::get('/auth-signout', [CAAuth::class, 'logout']);
    Route::get('/get-favorite', [CAFavorit::class, 'get_favorite']);
    Route::post('/post-favorite', [CAFavorit::class, 'post_favorite']);
    Route::post('/post-unfavorite', [CAFavorit::class, 'post_unfavorite']);
    Route::get('/get-booking', [CABooking::class, 'get_booking']);
    Route::post('/post-property-booking', [CAProperti::class, 'post_property_booking']);
    Route::get('/get-booking-detail', [CABooking::class, 'get_booking_detail']);
    Route::post('/post-review', [CABooking::class, 'post_review']);
    Route::post('/post-profile-img', [CAProfile::class, 'post_profile_img']);
    Route::put('/put-profile', [CAProfile::class, 'put_profile']);
    Route::put('/put-profile-pi', [CAProfile::class, 'put_profile_pi']);
    Route::post('/post-booking-cancel', [CABooking::class, 'post_booking_cancel']);
    Route::get('/get-profile', [CAProfile::class, 'get_profile']);
    Route::get('/get-personal-information', [CAProfile::class, 'get_personal_information']);
    Route::put('/put-email', [CAProfile::class, 'put_email']);
    Route::put('/put-phone', [CAProfile::class, 'put_phone']);
    Route::put('/put-another-phone', [CAProfile::class, 'put_another_phone']);
    Route::delete('/delete-another-phone', [CAProfile::class, 'delete_another_phone']);
    Route::post('/post-property-coupon', [CAProperti::class, 'post_property_coupon']);
    Route::post('/post-property-payment', [CAProperti::class, 'post_property_payment']);
    Route::put('/put-forget-password', [CAAuth::class, 'put_forget_password']);
});
