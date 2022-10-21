<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MApiKey;
use App\Models\MCustomer;
use App\Models\User;
use Image;

class CAProfile extends Controller
{
    public function post_profile_img(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $my_pdf_path_for_example = 'upload/profile_img/';
        if (!file_exists(public_path($my_pdf_path_for_example))) {
            mkdir(public_path($my_pdf_path_for_example), 0777, true);
        }

        if ($request->file('image')) {            

            $image     = $request->file('image');
            $gambar    = round(microtime(true) * 1000).'.'.$request->file('image')->extension();            

            $image_resize = Image::make($image->getRealPath());
            
            $image_resize->resize(500, 500);
            
            $image_resize->save(public_path('upload/profile_img/' .$gambar));
        }else{
            return response()->json([
                'success' => false,
                'message' => 'error no files',
                'code' => 0,            
            ], 400);
        }

        $muser = User::where('id_user',$user->id_user)->first();

        if ($muser->id_ref == 0) {
            $custom = new MCustomer();
            $custom->nama_foto = $gambar;
            $custom->save();

            $muser = User::where('id_user',$user->id_user)->update(['id_ref' => $custom->id]);
        }else {
            $muser = MCustomer::where('id',$muser->id_ref)->update(['nama_foto' => $gambar]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
        ], 200);
    }

    public function put_profile(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $muser = User::where('id_user',$user->id_user)->first();

        $tentang = $request->tentang;
        $id_negara = $request->id_negara;
        $nama_provinsi = $request->nama_provinsi;
        $nama_kota = $request->nama_kota;

        if ($muser->id_ref == 0) {
            $custom = new MCustomer();
            $custom->tentang = $tentang;
            $custom->id_negara = $id_negara;
            $custom->nama_provinsi = $nama_provinsi;
            $custom->nama_kota = $nama_kota;
            $custom->save();

            $muser = User::where('id_user',$user->id_user)->update(['id_ref' => $custom->id]);
        }else {
            $muser = MCustomer::where('id',$muser->id_ref)->update(['tentang' => $tentang, 'id_negara' => $id_negara, 'nama_provinsi' => $nama_provinsi, 'nama_kota' => $nama_kota]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
        ], 200);
    }

    public function put_profile_pi(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $muser = User::where('id_user',$user->id_user)->first();

        $nama_depan = $request->nama_depan;
        $nama_belakang = $request->nama_belakang;
        $jenis_kelamin = $request->jenis_kelamin;
        $email = $request->email;
        $no_telfon = $request->no_telfon;
        $no_telfon_lain = $request->no_telfon_lain;

        if ($no_telfon_lain != null) {
            $tlplain = [];
            $notlp = explode(",",$no_telfon_lain);
            for ($i=0; $i < count($notlp) ; $i++) { 
                $tlplain[] = $notlp[$i];
            }
        }

        if ($muser->id_ref == 0) {
            $custom = new MCustomer();
            $custom->nama_depan = $nama_depan;
            $custom->nama_belakang = $nama_belakang;
            $custom->jenis_kelamin = $jenis_kelamin;            
            $custom->no_telfon_lain = $tlplain;
            $custom->save();

            $muser = User::where('id_user',$user->id_user)->update(['id_ref' => $custom->id, 'email' => $email, 'no_telfon' => $no_telfon]);
        }else {
            $cek_user = User::where('email',$email)->where('no_telfon',$no_telfon)->get()->count();
            if ($cek_user > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'phone number atau email is already used, please enter new phone number atau email',
                    'code' => 0,            
                ], 400);
            }
            $muser = MCustomer::where('id',$muser->id_ref)->update(['nama_depan' => $nama_depan, 'nama_belakang' => $nama_belakang, 'jenis_kelamin' => $jenis_kelamin, 'no_telfon_lain' => $tlplain]);

            $muser = User::where('id_user',$user->id_user)->update(['email' => $email, 'no_telfon' => $no_telfon]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
        ], 200);
    }
}
