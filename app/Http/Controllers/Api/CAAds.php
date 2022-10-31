<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MAds;

class CAAds extends Controller
{
    public function get_ads(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;        
        
        $properti = MAds::from('m_ads as a')
            ->selectRaw('a.*')
            ->leftJoin('t_ads_detail as b','a.id_ads', '=','b.id_ads')
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('a.status',1)
            ->where('a.posisi',1)
            ->whereRaw('now() BETWEEN CONCAT(b.tanggal_mulai," ",b.jam_mulai) AND CONCAT(b.tanggal_selesai," ",b.jam_selesai)')
            ->orderBy('b.id_ads','asc')
            ->limit(1)->get();

        $promotion_transportation = MAds::from('m_ads as a')
            ->selectRaw('a.*')
            ->leftJoin('t_ads_detail as b','a.id_ads', '=','b.id_ads')
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('a.status',1)
            ->where('a.posisi',2)
            ->whereRaw('now() BETWEEN CONCAT(b.tanggal_mulai," ",b.jam_mulai) AND CONCAT(b.tanggal_selesai," ",b.jam_selesai)')
            ->orderBy('b.id_ads','asc')
            ->limit(1)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
            'property' => $properti,
            'promotion_transportation' => $promotion_transportation,
        ], 200);        
    }
}
