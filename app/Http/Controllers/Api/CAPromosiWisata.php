<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MPromosiWisata;

class CAPromosiWisata extends Controller
{
    public function get_promotion_tour(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        $tipe = MPromosiWisata::where('deleted',1)
                ->where('id_bahasa',$id_bahasa)                
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
                'code' => 1,
            ], 400);
        }
    }
}
