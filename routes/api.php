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
Route::group(['prefix' => 'v','middleware' => 'myauth'], function () {	
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
});