<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MNegara;

class CANegara extends Controller
{
    public function get_country(Request $request)
    {   
        $id_bahasa = $request->id_bahasa;

        $data = MNegara::where('deleted',1)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
        ], 200);
    }
}
