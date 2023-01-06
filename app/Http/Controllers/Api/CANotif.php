<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MApiKey;
use App\Models\MNotif;

class CANotif extends Controller
{
    public function get_notif(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $notif = MNotif::where('id_user',$user->id_user)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $notif,
        ], 200);        
    }

    public function update_read_notif(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $id_notif = $request->id_notif;

        $notif = MNotif::where('id_notif',$id_notif)->where('id_user',$user->id_user)->update(['readed'=>1]);
        
        return response()->json([
            'success' => true,
            'message' => 'Update Success',
            'code' => 1,
        ], 200);        
    }
}
