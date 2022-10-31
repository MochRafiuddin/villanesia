<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MSetting;

class CASetting extends Controller
{
    public function get_setting(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;        
        
        $data = MSetting::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
            'data' => $data,
        ], 200);        
    }
}
