<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use DataTables;
use Illuminate\Support\Facades\Validator;

class CSetting extends Controller
{
    public function index()
    {        
        return view('setting.index')
        ->with('title','Setting');
    }
    public function show($id)
    {        
        $data = Setting::find($id);
        $url = url('setting/show-save/'.$id);
        return view('setting.form')
            ->with('data',$data)            
            ->with('title','Properti')
            ->with('titlePage','Edit')            
            ->with('url',$url);
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'kode' => 'required', 
            'nama' => 'required',            
            'nilai' => 'required',            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        Setting::where('id',$request->id)->update(['nilai'=>$request->nilai]);                    

        return redirect()->route('setting-index')->with('msg','Sukses Menambahkan Data');
    }
    public function data()
    {        
        $model = Setting::select('*');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '<a href="'.url('setting/show/'.$row->id).'" class="text-danger" data-toggle="tooltip" data-placement="Top" title="Edit Data"><span class="mdi mdi-pen"></span></a>';
                return $btn;
            })            
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }    
}
