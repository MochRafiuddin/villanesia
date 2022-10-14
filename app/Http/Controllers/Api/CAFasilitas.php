<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MFasilitas;

class CAFasilitas extends Controller
{
    public function get_facilities(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $all = $request->all;

        $tipe = MFasilitas::where('deleted',1)
                ->where('id_bahasa',$id_bahasa);
        if ($all == 0) {
            $tipe = $tipe->limit(6)->offset(0);
        }
        $data = $tipe->get();
        if (count($data)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'data' => $data,
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
