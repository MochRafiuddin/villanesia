<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CLogin extends Controller
{
    public function index()
    {
         return view('login')->with('title','Login');
    }
    public function authenticate(Request $request)
    {

        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'deleted' => 1, 'tipe_user' => 1])) {
            $request->session()->regenerate();            
            // return redirect(url('dashboard'));   
            redirect()->route('dashboard');         
        }
         
        return back()->withSuccess('Username / Password Salah');
    }
    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
