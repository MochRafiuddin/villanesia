<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MSplashSlide;
use DB;

class CASplashSlide extends Controller
{    

    public function get_splash_slide(Request $request)
    {                
        // $id_bahasa = $request->id_bahasa;

        $tipe = MSplashSlide::where('deleted',1)
                ->orderBy('tipe')                
                ->get();                
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }
}
