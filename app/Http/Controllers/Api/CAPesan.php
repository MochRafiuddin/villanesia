<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HPesan;
use App\Models\MApiKey;

class CAPesan extends Controller
{
    public function get_chat(Request $request)
    {                
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        // $muser = User::where('id_user',$user->id_user)->first();
        $id_user = $request->id_user;

        $tipe = HPesan::from('h_pesan as a')
                ->selectRaw('a.*, b.nama_depan as nama_depan_pengirim, b.nama_belakang as nama_belakang_pengirim, c.nama_depan as nama_depan_penerima, c.nama_belakang as nama_belakang_penerima')
                ->leftJoin('m_users as d','d.id_user', '=','a.id_user_pengirim')
                ->leftJoin('m_customer as b','b.id', '=','d.id_ref')
                ->leftJoin('m_users as e','e.id_user', '=','a.id_user_penerima')
                ->leftJoin('m_customer as c','c.id', '=','e.id_ref')
                ->where('a.id_user_pengirim',$id_user)
                ->OrWhere('a.id_user_penerima',$id_user)
                ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }
}
