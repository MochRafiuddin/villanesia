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
        $bahasa_asli = $request->bahasa_asli;
        $bahasa_lain = $request->bahasa_lain;

        $tlplain = [];
        if ($no_telfon != null) {
            $no_telfon2 = (string)$no_telfon;
            if($no_telfon2[0] != 0){
                $no_telfon = '0'.$no_telfon2;
            }
        }

        if ($no_telfon_lain != null) {
            /*$tlplain = [];
            $notlp = explode(",",$no_telfon_lain);
            for ($i=0; $i < count($notlp) ; $i++) { 
                $tlplain[] = $notlp[$i];
            }*/

            $notlp = explode(",",$no_telfon_lain);
            for ($i=0; $i < count($notlp) ; $i++) { 
                if ($notlp[$i][0] != 0) {
                    $tlplain[] = '0'.$notlp[$i];
                }else {
                    $tlplain[] = $notlp[$i];
                }
            }

        }

        if ($muser->id_ref == 0) {
            $custom = new MCustomer();
            $custom->nama_depan = $nama_depan;
            $custom->nama_belakang = $nama_belakang;
            $custom->jenis_kelamin = $jenis_kelamin;            
            $custom->no_telfon_lain = $tlplain;
            $custom->bahasa_asli = $bahasa_asli;
            $custom->bahasa_lain = $bahasa_lain;
            $custom->save();

            $muser = User::where('id_user',$user->id_user)->update(['id_ref' => $custom->id]);
            if ($email != null) {
                $muser = User::where('id_user',$user->id_user)->update(['email' => $email]);
            }
            if ($no_telfon != null) {
                $muser = User::where('id_user',$user->id_user)->update(['no_telfon' => $no_telfon]);
            }
        }else {

            if ($no_telfon != null && $email != null) {

                $cek_user_email = User::where('email',$email)->where('id_user','<>',$user->id_user)->get()->count();
                $cek_user_telfon = User::where('no_telfon',$no_telfon)->where('id_user','<>',$user->id_user)->get()->count();
                if ($cek_user_email > 0 && $cek_user_telfon > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'phone number and email is already used, please enter new phone number and email',
                        'code' => 0,            
                    ], 400);
                }elseif ($cek_user_email > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'email is already used, please enter new email',
                        'code' => 0,            
                    ], 400);
                }elseif ($cek_user_telfon > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'phone number is already used, please enter new phone number',
                        'code' => 0,            
                    ], 400);
                }
                $muser = MCustomer::where('id',$muser->id_ref)->update(['nama_depan' => $nama_depan, 'nama_belakang' => $nama_belakang, 'jenis_kelamin' => $jenis_kelamin, 'no_telfon_lain' => $tlplain, 'bahasa_asli' => $bahasa_asli, 'bahasa_lain' => $bahasa_lain]);

                // $muser = User::where('id_user',$user->id_user)->update(['email' => $email, 'no_telfon' => $no_telfon]);
                if ($email != null) {
                    $muser = User::where('id_user',$user->id_user)->update(['email' => $email]);
                }
                if ($no_telfon != null) {
                    $muser = User::where('id_user',$user->id_user)->update(['no_telfon' => $no_telfon]);
                }

            }else{

                if($no_telfon != null){
                    $cek_user_telfon = User::where('no_telfon',$no_telfon)->where('id_user','<>',$user->id_user)->get()->count();

                    if ($cek_user_telfon > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'phone number is already used, please enter new phone number',
                            'code' => 0,            
                        ], 400);
                    }

                    $muser = MCustomer::where('id',$muser->id_ref)->update(['nama_depan' => $nama_depan, 'nama_belakang' => $nama_belakang, 'jenis_kelamin' => $jenis_kelamin, 'no_telfon_lain' => $tlplain, 'bahasa_asli' => $bahasa_asli, 'bahasa_lain' => $bahasa_lain]);
                    if ($no_telfon != null) {
                        $muser = User::where('id_user',$user->id_user)->update(['no_telfon' => $no_telfon]);
                    }

                }

                if($email != null){
                    $cek_user_email = User::where('email',$email)->where('id_user','<>',$user->id_user)->get()->count();

                    if ($cek_user_email > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'email is already used, please enter new email',
                            'code' => 0,            
                        ], 400);
                    }

                    $muser = MCustomer::where('id',$muser->id_ref)->update(['nama_depan' => $nama_depan, 'nama_belakang' => $nama_belakang, 'jenis_kelamin' => $jenis_kelamin, 'no_telfon_lain' => $tlplain, 'bahasa_asli' => $bahasa_asli, 'bahasa_lain' => $bahasa_lain]);
                    if ($email != null) {
                        $muser = User::where('id_user',$user->id_user)->update(['email' => $email]);
                    }

                }

            }

        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,            
        ], 200);
    }

    public function get_profile(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $data = User::from('m_users as a')
            ->selectRaw('a.id_user, a.email, a.no_telfon, b.tentang, b.id_negara, c.nama_negara, b.nama_foto, b.nama_depan, b.jenis_kelamin')
            ->leftJoin('m_customer as b','a.id_ref','=','b.id')
            ->leftJoin('m_negara as c','b.id_negara','=','c.id_negara')
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('c.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->first();
        
        if ($data->email != null && $data->nama_foto != null && $data->id_negara != null && $data->tentang != null && $data->nama_depan != null && $data->jenis_kelamin != null && $data->no_telfon != null ) {
            $complite = "100%";
        }elseif ($data->email != null && $data->nama_foto != null && $data->id_negara != null && $data->tentang != null && $data->nama_depan != null && $data->jenis_kelamin != null) {
            $complite = "75%";
        }elseif ($data->email != null && $data->nama_foto != null && $data->id_negara != null && $data->tentang != null) {
            $complite = "50%";
        }else {
            $complite = "25%";
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
            'profile_complete' => $complite,
        ], 200);
    }

    public function get_personal_information(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $data = User::from('m_users as a')
            ->selectRaw('a.id_user, a.email, a.no_telfon, b.*')
            ->leftJoin('m_customer as b','a.id_ref','=','b.id')            
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
        ], 200);
    }

    public function put_email(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $email = $request->email;
        $cek_user = User::where('deleted',1)                
                ->where('email',$email)
                ->where('id_user','<>',$user->id_user)
                ->get();
        if (count($cek_user)>0) {
            return response()->json([
                'success' => false,
                'message' => 'email is already used, please enter new email',
                'code' => 0,
            ], 400);
        }

        $muser = User::where('id_user',$user->id_user)->update(['email' => $email]);
        
        return response()->json([
            'success' => true,
            'message' => 'email update',
            'code' => 1,            
        ], 200);
    }

    public function put_phone(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $no_telfon = (string)$request->no_telfon;
        if($no_telfon[0] != 0){
            $no_telfon = '0'.$no_telfon;
        }
        
        $cek_user = User::where('deleted',1)                
                ->where('no_telfon',$no_telfon)
                ->where('id_user','<>',$user->id_user)
                ->get();
        if (count($cek_user)>0) {
            return response()->json([
                'success' => false,
                'message' => 'phone number is already used, please enter new phone number',
                'code' => 0,
            ], 400);
        }

        $muser = User::where('id_user',$user->id_user)->update(['no_telfon' => $no_telfon]);
        
        return response()->json([
            'success' => true,
            'message' => 'phone number update',
            'code' => 1,            
        ], 200);
    }

    public function put_another_phone(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $data = User::from('m_users as a')
            ->selectRaw('a.id_user,b.*')
            ->leftJoin('m_customer as b','a.id_ref','=','b.id')            
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->first();
        $var = json_decode($data->no_telfon_lain, TRUE);        

        $no_telfon_lain = $request->no_telfon_lain;        

        if ($no_telfon_lain != null) {
            $tlplain = [];
            $notlp = explode(",",$no_telfon_lain);
            for ($i=0; $i < count($notlp) ; $i++) { 
                if ($notlp[$i][0] != 0) {
                    $tlplain[] = '0'.$notlp[$i];
                }else {
                    $tlplain[] = $notlp[$i];
                }
            }
        }
        $output = array_merge($var, $tlplain);
        $muser = MCustomer::where('id',$data->id)->update(['no_telfon_lain' => $output]);
        
        return response()->json([
            'success' => true,
            'message' => 'another phone number update',
            'code' => 1,            
        ], 200);
    }

    public function delete_another_phone(Request $request)
    {        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $data = User::from('m_users as a')
            ->selectRaw('a.id_user,b.*')
            ->leftJoin('m_customer as b','a.id_ref','=','b.id')            
            ->where('a.deleted',1)
            ->where('b.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->first();
        $var = json_decode($data->no_telfon_lain, TRUE);        

        $no_telfon_lain = $request->no_telfon_lain;        

        if ($no_telfon_lain != null) {
            $tlplain = [];
            $notlp = explode(",",$no_telfon_lain);
            for ($i=0; $i < count($notlp) ; $i++) { 
                if ($notlp[$i][0] != 0) {
                    $tlplain[] = "0".$notlp[$i];
                }else {
                    $tlplain[] = $notlp[$i];
                }
            }
        }
        $output = array_values(array_diff($var, $tlplain));
        $muser = MCustomer::where('id',$data->id)->update(['no_telfon_lain' => $output]);
        
        return response()->json([
            'success' => true,
            'message' => 'another phone number delete',
            'code' => 1,            
        ], 200);
    }
}
