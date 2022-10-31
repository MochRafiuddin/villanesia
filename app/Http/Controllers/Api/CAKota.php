<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MKota;
use DB;

class CAKota extends Controller
{
    public function get_city(Request $request)
    {        
        // $id_bahasa = $request->id_bahasa;

        $tipe = MKota::where('deleted',1)                              
                ->get();

        if (count($tipe)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'data' => $tipe,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
                'code' => 0,
            ], 400);
        }
    }

    public function get_best_destination(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;
        $page = ($request->page-1)*6;

        $tipe = MKota::selectRaw('m_kota.*, COUNT(m_properti.id_properti) as total_kota')
                ->join('m_properti', 'm_kota.id_kota','=','m_properti.id_kota')
                ->where('m_kota.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->groupBy('m_properti.id_kota')
                ->orderBy('total_kota','desc')
                ->limit(6)
                ->offset($page)
                ->get();        
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }
}
