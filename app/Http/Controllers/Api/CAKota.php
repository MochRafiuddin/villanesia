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
        $limit = 6;
        $page = ($request->page-1)*$limit;

        $tipe = MKota::selectRaw('m_kota.*, COUNT(m_properti.id_properti) as total_kota')
                ->join('m_properti', 'm_kota.id_kota','=','m_properti.id_kota')
                ->where('m_kota.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->groupBy('m_properti.id_kota')
                ->orderBy('total_kota','desc')
                ->limit(6)
                ->offset($page)
                ->get();        

        $get_total_all_data = MKota::selectRaw('COUNT(m_properti.id_properti) as total_kota')
                ->join('m_properti', 'm_kota.id_kota','=','m_properti.id_kota')
                ->where('m_kota.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->groupBy('m_properti.id_kota')
                ->orderBy('total_kota','desc')
                ->get()->count();

        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
            'total_page' => $total_page
        ], 200);        
    }

    public function get_city_search(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;

        $tipe = MKota::selectRaw('m_kota.*')
                ->join('m_properti', 'm_kota.id_kota','=','m_properti.id_kota')
                ->where('m_kota.deleted',1)
                ->where('m_properti.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->groupBy('m_kota.id_kota')                
                ->get();        
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }
}
