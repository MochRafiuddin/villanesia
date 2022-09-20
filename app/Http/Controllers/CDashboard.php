<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CDashboard extends Controller
{
    public function index()
    {
         return view('dashboard.index')->with('title','Dashboard');
    }
}
