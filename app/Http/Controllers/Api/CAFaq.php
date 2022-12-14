<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MFaq;

class CAFaq extends Controller
{
    public function get_faq(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        // $tipe = MFaq::where('id_bahasa',$id_bahasa)
        //         ->where('deleted',1)
        //         ->get();

        $tipe = MFaq::selectRaw('page_faq.*, (select IFNULL((select id_tipe from page_faq as a where a.id_bahasa = '.$id_bahasa.' and a.id < page_faq.id ORDER BY a.id DESC LIMIT 1),0)) as id_tipe_sebelumnya')
                ->where('page_faq.id_bahasa',$id_bahasa)
                ->where('page_faq.deleted',1)
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
