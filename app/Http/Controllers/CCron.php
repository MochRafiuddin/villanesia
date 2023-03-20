<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CCron extends Controller
{
    function update_expired_time()
    {
        $book = DB::table('t_booking')->where('id_status_booking',2)->where('respone_payment_page','!=','')->get();
        // dd($book);
        foreach ($book as $key) {
            $expired_time = json_decode($key->respone_payment_page)->data->expired_time;
            $now = date('Y-m-d H:i:s');
            
            $str_et = strtotime($expired_time);
            $str_n = strtotime($now);
            
            if ($str_n >= $str_et) {                
                // dd($expired_time." ---");
		        $tBooking = DB::table('t_booking')->where('id_booking',$key->id_booking)
                ->update(array('id_status_booking' => 4, 'alasan_cancel' => "sorry inquiry #".$key->kode_booking." canceled due to expired payment"));;
            }            
        }
        return response()->json(['status'=>true,'msg'=>'Success Update Expired Time']);
    }
}
