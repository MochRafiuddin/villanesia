<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MBanner;

class CABanner extends Controller
{
    public function get_banner(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        $tipe = MBanner::where('status',1)
                ->where('deleted',1)
                ->orderBy('updated_date','desc')                
                ->limit(1)
                ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $tipe,
        ], 200);        
    }
}
