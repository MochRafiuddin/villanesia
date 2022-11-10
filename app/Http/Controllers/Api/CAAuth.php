<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Auth;
use Socialite;
use Validator;
use App\Models\User;
use App\Models\MCustomer;
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

        $cus = new MCustomer();
        $cus->nama_depan = $request->username;
        $cus->save();

        // $data['username'] = $request->username;
        // $data['email'] = $request->email;
        // $data['token'] = Str::random(30);

        $user = User::create([
            'id_ref' => $cus->id,
            'tipe_user' => 2,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),            
        ]);        

        $key = Str::random(30);
        $token = new MApiKey();
        $token->id_user = $user->id_user;
        $token->token = $key;
        $token->save();

        $get_user = User::selectRaw('m_customer.*, m_users.id_user, m_users.username, m_users.password, m_users.id_ref, m_users.email, m_users.no_telfon, m_users.g_id, m_users.g_photo, m_users.tipe_user, api_key.token')
                ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
                ->leftJoin('api_key','api_key.id_user','=','m_users.id_user')
                ->where('m_users.id_user',$user->id_user)
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $get_user,
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

    public function handleProviderCallback(Request $request)
    {
        
        $provider = $request->provider;
        $validated = $this->validateProvider($provider);
        
        $providerUser = Socialite::driver($provider)->userFromToken($request->access_provider_token);
        // dd($providerUser->getAvatar());
        $cek_email = User::where("email",$providerUser->getEmail())->first();
        if ($cek_email) {
            if ($cek_email->g_id == null) {
                User::where("email",$providerUser->getEmail())->update(['g_id'=>$providerUser->getId(), 'g_photo'=>$providerUser->getAvatar()]);
                $gid = $providerUser->getId();
            }else {
                $gid = $cek_email->g_id;
            }

            if ($gid !== $providerUser->getId()) {
                return response()->json([
                    'message' => 'Email address for this accounts already registered, please use another account',
                    'code' => 0,
                ]);
            }
            $id_user = $cek_email->id_user;
        }else {
            $cus = new MCustomer();
            $cus->nama_depan = substr($providerUser->getEmail(),0,6);
            $cus->save();

            $token = new User();
            $token->g_id = $providerUser->getId();
            $token->g_photo = $providerUser->getAvatar();
            $token->email = $providerUser->getEmail();
            $token->username = substr($providerUser->getEmail(),0,6);
            $token->password = Hash::make("password");
            $token->id_ref = $cus->id;
            $token->tipe_user = 2;
            $token->save();

            $id_user = $token->id_user;
        }


        $cek_token = MApiKey::where('id_user',$id_user)->first();
        if ($cek_token) {
            MApiKey::where('id_user',$id_user)->delete();    
        }
        $key = Str::random(30);
        $token = new MApiKey();
        $token->id_user = $id_user;
        $token->token = $key;
        $token->save();

        $get_user = User::selectRaw('m_customer.*, m_users.id_user, m_users.username, m_users.password, m_users.id_ref, m_users.email, m_users.no_telfon, m_users.g_id, m_users.g_photo, m_users.tipe_user')
                ->join('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.id_user',$id_user)
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->get();
                                
        // $request->session()->regenerate();            
        return response()->json([
            'message' => 'Login Success',
            'key' => $key,
            'code' => 1,
            'user_data' => $get_user,
        ]);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return response()->json(["message" => 'You can only login via google account'], 400);
        }
    }

    public function login(Request $request)
    {        
        
        if(is_numeric($request->username)){
            $arr = ['no_telfon' => $request->username, 'password' => $request->password, 'deleted' => 1];
        }
        else {
            $arr = ['username' => $request->username, 'password' => $request->password, 'deleted' => 1];
        }

        if(!Auth::attempt($arr))        
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
                ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
                ->where('m_users.id_user',auth::user()->id_user)
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->get();
        // $request->session()->regenerate();            
        return response()->json([
            'message' => 'Login Success',
            'key' => $key,
            'code' => 1,
            'user_data' => $get_user,
        ]);
    }
}

