<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MProperti;

class CAProperti extends Controller
{
    public function get_property_type(Request $request)
    {
        $id_tipe = $request->id_tipe;
        $id_bahasa = $request->id_bahasa;

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa)
                ->where('id_tipe_properti',$id_tipe)
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
