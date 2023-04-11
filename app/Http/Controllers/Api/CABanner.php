<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MBanner;
use App\Models\User;
use App\Models\MApiKey;
use App\Models\MSetting;
use DateTime;

class CABanner extends Controller
{
    public function get_banner(Request $request)
    {        
        $tampil_banner_user = 1;
        if ($request->header('auth-key') != null) {
            $api = MApiKey::where('token',$request->header('auth-key'))->first();
            $user = User::where('id_user',$api->id_user)->first();
            if ($user->waktu_banner == null) {
                $tampil_banner_user = 1;
            }else {                
                $date2 = \Carbon\Carbon::parse(date('Y-m-d H:i:s'));
                $date1 = \Carbon\Carbon::parse($user->waktu_banner);

                $diffInHours = $date1->diffInHours($date2);
                // dd($diffInHours);
                $set = MSetting::where('kode','waktu_banner')->first();
                if ($set->nilai < $diffInHours) {
                    $tampil_banner_user = 1;                    
                }else {
                    $tampil_banner_user = 0;
                }
            }

        }
        $id_bahasa = $request->id_bahasa;

        $tipe = MBanner::where('status',1)
                ->where('deleted',1)
                ->orderBy('updated_date','desc')                
                ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'tampil_banner_user' => $tampil_banner_user,
            'data' => $tipe,
        ], 200);        
    }

    public function update_waktu_banner(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        
        User::where('id_user',$user->id_user)->update(['waktu_banner'=>date('Y-m-d H:i:s')]);
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
        ], 200);        
    }
}
