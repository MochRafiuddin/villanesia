<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CChat extends Controller
{
    public function index()
    {        
        return view('chat.index')            
            ->with('title','Chat');
    }
}
