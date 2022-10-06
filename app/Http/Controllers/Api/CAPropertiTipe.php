<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MTipeProperti;

class CAPropertiTipe extends Controller
{
    public function get_property_type(Request $request)
    {
        $id_bahasa = $request->id_bahasa;

        $tipe = MTipeProperti::withDeleted()->where('id_bahasa',$id_bahasa)->get();

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
