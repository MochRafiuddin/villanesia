<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Mail;
use App\Mail\EmailPassword;
use Auth;
use Socialite;
use Validator;
use App\Models\User;
use App\Models\MCustomer;
use App\Models\MApiKey;
use Illuminate\Support\Str;
use App\Traits\Helper;

class CAAuth extends Controller
{
    use Helper;

    public function register(Request $request)
    {
        $cek_user_username = User::where('deleted',1)
                ->where('username',$request->username)                
                ->get();
        $cek_user_email = User::where('deleted',1)
                ->where('email',$request->email)
                ->get();
        if (count($cek_user_username)>0 && count($cek_user_email)>0) {
            return response()->json([
                'success' => false,
                'message' => 'username and email is already used, please enter new username and email',
                'code' => 0,
            ], 400);
        }elseif (count($cek_user_username)>0) {
            return response()->json([
                'success' => false,
                'message' => 'username is already used, please enter new username',
                'code' => 0,
            ], 400);
        }elseif (count($cek_user_email)>0) {
            return response()->json([
                'success' => false,
                'message' => 'email is already used, please enter new email',
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

        $this->kirim_email($request->email,$request->username,null,null,null,null,null,'email.emailDaftar','Thank you for signing up with Villanesia',null,null);

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

    public function login_google(Request $request)
    {
        
        $email = $request->email;
        $g_id = $request->g_id;
        $g_photo = $request->g_photo;
        // dd($providerUser->getAvatar());
        $cek_email = User::where("email",$email)->first();
        if ($cek_email) {
            if ($cek_email->g_id == null) {
                User::where("email",$email)->update(['g_id'=>$g_id, 'g_photo'=>$g_photo]);
                $gid = $g_id;
            }else {
                $gid = $cek_email->g_id;
            }

            if ($gid !== $g_id) {
                return response()->json([
                    'message' => 'Email address for this accounts already registered, please use another account',
                    'code' => 0,
                ]);
            }
            $id_user = $cek_email->id_user;
        }else {
            $cus = new MCustomer();
            $cus->nama_depan = substr($email,0,6);
            $cus->save();

            $cek_username = User::where('username', 'like', '%' .substr($email,0,6). '%')->orderBy('id_user','desc')->first();
            if ($cek_username == null) {
                $user_name = substr($email,0,6);
            }else {
                $names = str_split($cek_username->username,6);
                if (count($names) > 1) {
                    $angka = $names[1]+1;
                    $user_name = $names[0].''.$angka;
                }else {
                    $user_name = $names[0].'1';
                }
            }

            $token = new User();
            $token->g_id = $g_id;
            $token->g_photo = $g_photo;
            $token->email = $email;
            $token->username = $user_name;
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

    public function login_apple(Request $request)
    {
        
        $email = $request->email;
        $apple_id = $request->apple_id;        
        // dd($providerUser->getAvatar());
        $cek_email = User::where("email",$email)->first();
        if ($cek_email) {
            if ($cek_email->apple_id == null) {
                User::where("email",$email)->update(['apple_id'=>$apple_id]);
                $apple_id = $apple_id;
            }else {
                $apple_id = $cek_email->apple_id;
            }

            if ($apple_id !== $apple_id) {
                return response()->json([
                    'message' => 'Email address for this accounts already registered, please use another account',
                    'code' => 0,
                ]);
            }
            $id_user = $cek_email->id_user;
        }else {
            $cus = new MCustomer();
            $cus->nama_depan = substr($email,0,6);
            $cus->save();

            $cek_username = User::where('username', 'like', '%' .substr($email,0,6). '%')->orderBy('id_user','desc')->first();
            if ($cek_username == null) {
                $user_name = substr($email,0,6);
            }else {
                $names = str_split($cek_username->username,6);
                if (count($names) > 1) {
                    $angka = $names[1]+1;
                    $user_name = $names[0].''.$angka;
                }else {
                    $user_name = $names[0].'1';
                }
            }

            $token = new User();
            $token->apple_id = $apple_id;
            $token->email = $email;
            $token->username = $user_name;
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

    public function post_forget_password(Request $request)
    {
        $id_bahasa = $request->id_bahasa;
        $email = $request->email;

        $cek_email = User::join('m_customer','m_customer.id','m_users.id_ref')
            ->select('m_users.*','m_customer.nama_depan','m_customer.nama_belakang')
            ->where('email',$request->email)->first();
        if ($cek_email == null) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, your email was not found',
                'code' => 0,
            ], 400);
        }

        $password = Str::random(6);
        // Mail::to($request->email)->send(new EmailPassword($cek_email->username,$password,"Forgot Password - Villanesia"));
        $this->kirim_email($email,$cek_email->nama_depan,$cek_email->nama_belakang,$cek_email->username,$password,null,null,'email.mailView','Forgot Password - Villanesia',null,null);
        User::where('id_user',$cek_email->id_user)->update(['password' => Hash::make($password)]);
        return response()->json([
            'success' => true,
            'message' => "Account found, please check your email for new password. If you can't find the email in your inbox, also check your spam.",
            'code' => 1,
        ], 200);
    }

    public function put_forget_password(Request $request)
    {
        $id_bahasa = $request->id_bahasa;
        $password_lama = $request->password_lama;
        $password_baru = $request->password_baru;

        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $data = User::where('id_user',$user->id_user)->first();
        $cek = Hash::check($password_lama, $data->password);

        if ($cek == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your old password is wrong, please enter the correct old password',
                'code' => 0,
            ], 400);
        }

        $cek_email = User::where('id_user',$user->id_user)->update(['password' => Hash::make($password_baru)]);
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
            'code' => 1,
        ], 200);
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

