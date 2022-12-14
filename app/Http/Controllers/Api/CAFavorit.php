<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MFavorit;
use App\Models\MProperti;
use App\Models\MApiKey;

class CAFavorit extends Controller
{
    public function get_favorite(Request $request)
    {        
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $id_tipe = $request->id_tipe;
        $nama_properti = strtolower($request->nama_properti);
        $order_by = $request->order_by;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        
        $tipe = MFavorit::selectRaw('h_favorit.id, h_favorit.id_properti, h_favorit.id_user, h_favorit.created_date, h_favorit.deleted, h_favorit.updated_date, m_properti.id_bahasa, m_properti.id_ref_bahasa, m_properti.judul, m_properti.alamat, m_properti.harga_tampil, m_properti.jumlah_kamar_tidur, m_properti.jumlah_kamar_mandi, (m_properti.jumlah_tamu+COALESCE(m_properti.jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, m_properti.sarapan, m_properti.nilai_rating, m_properti.nama_file, m_properti.id_tipe_properti')
                ->leftJoin('m_properti','h_favorit.id_properti','=','m_properti.id_ref_bahasa')                
                ->where('h_favorit.deleted',1)
                ->where('h_favorit.id_user',$user->id_user)                
                ->where('m_properti.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->orderBy('h_favorit.created_date','desc')
                ->limit($limit)
                ->offset($page);
        $get_total_all_data = MFavorit::selectRaw('h_favorit.id')
                ->leftJoin('m_properti','h_favorit.id_properti','=','m_properti.id_ref_bahasa')
                ->where('h_favorit.deleted',1)
                ->where('h_favorit.id_user',$user->id_user)                
                ->where('m_properti.deleted',1)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->orderBy('h_favorit.created_date','desc');    

        if ($id_tipe != 0) {
            if ($id_tipe != null) {
                $tipe = $tipe->where('m_properti.id_tipe_properti',$id_tipe);
                $get_total_all_data = $get_total_all_data->where('m_properti.id_tipe_properti',$id_tipe);
            }
        }
        if ($nama_properti != null) {
            $tipe = $tipe->where('m_properti.judul', 'like', '%'.$nama_properti.'%');
            $get_total_all_data = $get_total_all_data->where('m_properti.judul', 'like', '%'.$nama_properti.'%');
        }
        if ($order_by == 1) {
            $tipe = $tipe->orderBy('m_properti.harga_tampil','asc');
            $get_total_all_data = $get_total_all_data->orderBy('m_properti.harga_tampil','asc');
        }elseif ($order_by == 2) {
            $tipe = $tipe->orderBy('m_properti.harga_tampil','desc');
            $get_total_all_data = $get_total_all_data->orderBy('m_properti.harga_tampil','desc');
        }elseif ($order_by == 3) {
            $tipe = $tipe->orderBy('m_properti.nilai_rating','desc');
            $get_total_all_data = $get_total_all_data->orderBy('m_properti.nilai_rating','desc');
        }elseif ($order_by == 4) {
            $tipe = $tipe->orderByRaw('(m_properti.total_amenities + m_properti.total_fasilitas) desc');
            $get_total_all_data = $get_total_all_data->orderByRaw('(m_properti.total_amenities + m_properti.total_fasilitas) desc');
        }elseif ($order_by == 5) {
            $tipe = $tipe->orderBy('m_properti.created_date','desc');
            $get_total_all_data = $get_total_all_data->orderBy('m_properti.created_date','desc');
        }elseif ($order_by == 6) {
            $tipe = $tipe->orderBy('m_properti.created_date','asc');
            $get_total_all_data = $get_total_all_data->orderBy('m_properti.created_date','asc');
        }else {
            $tipe = $tipe->orderBy('h_favorit.created_date','desc');
            $get_total_all_data = $get_total_all_data->orderBy('h_favorit.created_date','desc');
        }
        $get_total_all_data = $get_total_all_data->count();
        $data = $tipe->get();
        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'total_data' => count($data),
                'result' => $data,
                'total_page' => $total_page
            ], 200);        
    }
    public function post_favorite(Request $request)
    {        
        $id_properti = $request->id_properti;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $tipe = MFavorit::where('id_properti',$id_properti)
                ->where('id_user',$user->id_user)
                ->first();

        if ($tipe) {
            if ($tipe->deleted == 0) {
                MFavorit::where('id_properti',$id_properti)
                ->where('id_user',$user->id_user)
                ->update(['deleted' => 1]);
                                
                $tot = MProperti::find($id_properti);
                $tot->total_favourite = $tot->total_favourite + 1;
                $tot->update();

                return response()->json([
                    'success' => true,
                    'message' => 'Added to favorite',
                    'code' => 1,
                ], 200);        
            }else{
                return response()->json([
                    'success' => true,
                    'message' => 'Cannot add to favorite',
                    'code' => 0,
                ], 400);        
            }
        }else{
            $favorite = new MFavorit();
            $favorite->id_properti = $id_properti;
            $favorite->id_user = $user->id_user;            
            $favorite->save();            

            $tot = MProperti::find($id_properti);
            $tot->total_favourite = $tot->total_favourite + 1;
            $tot->update();

            return response()->json([
                'success' => true,
                'message' => 'Added to favorite',
                'code' => 1,
            ], 200);        
        }
    }

    public function post_unfavorite(Request $request)
    {        
        $id_properti = $request->id_properti;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();        ;
        
        $fav = MFavorit::where('id_properti',$id_properti)->where('id_user',$user->id_user)->update(['deleted' => 0]);
                                
        $tot = MProperti::find($id_properti);
        $tot->total_favourite = $tot->total_favourite - 1;
        $tot->update();

        if ($fav) {            
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorite',
                'code' => 1,
            ], 200);        
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Cannot removed from favorite',
                'code' => 1,
            ], 200);
        }
            
    }
}
