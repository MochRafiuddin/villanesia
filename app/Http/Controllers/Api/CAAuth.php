<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Auth;
use Validator;
use App\Models\User;
use App\Models\MApiKey;
use Illuminate\Support\Str;

class CAAuth extends Controller
{
    public function register(Request $request)
    {
        $cek_user = User::where('deleted',1)
                ->where('username',$request->username)
                ->where('email',$request->email)
                ->get();
        if (count($cek_user)>0) {
            return response()->json([
                'success' => false,
                'message' => 'username atau email is already used, please enter new username atau email',
                'code' => 0,
            ], 400);
        }

        $data['username'] = $request->username;
        $data['email'] = $request->email;
        // $data['token'] = Str::random(30);

        $user = User::create([
            'id_ref' => 0,
            'tipe_user' => 2,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),            
        ]);        

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
        ], 200);
    }    

    public function logout(Request $request)
    {        
        MApiKey::where('token',$request->header('auth-key'))->delete();        
        Auth::logout();
        return response()->json([
            'message' => 'Logout Success',            
            'code' => 1,
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password, 'deleted' => 1]))
        {
            return response()->json([
                'success' => false,
                'message' => "Oops, we couldn't find your account",
                'code' => 0,
            ], 400);
        }

        $cek_token = MApiKey::where('id_user',auth::user()->id_user)->first();
        if ($cek_token) {
            MApiKey::where('id_user',auth::user()->id_user)->delete();    
        }
        $key = Str::random(30);
        $token = new MApiKey();
        $token->id_user = auth::user()->id_user;
        $token->token = $key;
        $token->save();

        $get_user = User::selectRaw('m_customer.*, m_users.id_user, m_users.username, m_users.password, m_users.id_ref, m_users.email, m_users.no_telfon, m_users.g_id, m_users.g_photo, m_users.tipe_user')
                ->join('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->get();
        $request->session()->regenerate();            
        return response()->json([
            'message' => 'Login Success',
            'key' => $key,
            'code' => 1,
            'user_data' => $get_user,
        ]);
    }
}

