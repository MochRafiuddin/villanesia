<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MConciergeService;

class CAConciergeService extends Controller
{
    public function get_concierge_service(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        $tipe = MConciergeService::where('deleted',1)
                ->where('id_bahasa',$id_bahasa)                
                ->get();

        if (count($tipe)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $tipe,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
            ], 400);
        }
    }
}
