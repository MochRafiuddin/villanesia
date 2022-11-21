<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MTermConditionDetail;

class CATermCondition extends Controller
{
    public function get_term_and_condition(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        // $tipe = MTermConditionDetail::where('id_bahasa',$id_bahasa)
        //         ->where('deleted',1)
        //         ->get();

        $tipe = MTermConditionDetail::selectRaw('page_term_condition_detail.*, (select IFNULL((select id_tipe from page_term_condition_detail as a where a.id_bahasa = '.$id_bahasa.' and a.id < page_term_condition_detail.id ORDER BY a.id DESC LIMIT 1),0)) as id_tipe_sebelumnya')
                ->where('page_term_condition_detail.id_bahasa',$id_bahasa)
                ->where('page_term_condition_detail.deleted',1)
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
