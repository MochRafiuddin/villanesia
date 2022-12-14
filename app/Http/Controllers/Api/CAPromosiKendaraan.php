<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MPromosiKendaraan;

class CAPromosiKendaraan extends Controller
{
    public function get_promotion_transportation(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        $tipe = MPromosiKendaraan::where('deleted',1)
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
                'code' => 0,
            ], 400);
        }
    }
}
