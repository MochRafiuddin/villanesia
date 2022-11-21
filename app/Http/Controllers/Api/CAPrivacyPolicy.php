<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MPrivacyPolicyDetail;

class CAPrivacyPolicy extends Controller
{
    public function get_privacy_policy(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;

        // $tipe = MPrivacyPolicyDetail::where('id_bahasa',$id_bahasa)
        //         ->where('deleted',1)
        //         ->get();

        $tipe = MPrivacyPolicyDetail::selectRaw('page_privacy_policy_detail.*, (select IFNULL((select id_tipe from page_privacy_policy_detail as a where a.id_bahasa = '.$id_bahasa.' and a.id < page_privacy_policy_detail.id ORDER BY a.id DESC LIMIT 1),0)) as id_tipe_sebelumnya')
                ->where('page_privacy_policy_detail.id_bahasa',$id_bahasa)
                ->where('page_privacy_policy_detail.deleted',1)
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
