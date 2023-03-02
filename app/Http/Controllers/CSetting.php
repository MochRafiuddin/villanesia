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
        if ($request->id == 7) {
            $validator = Validator::make($request->all(),[
                'merchant_id' => 'required', 
                'secret_unbound_id' => 'required',            
                'hash_key' => 'required',
                'url' => 'required',
            ]);
        }else {
            $validator = Validator::make($request->all(),[
                'kode' => 'required', 
                'nama' => 'required',            
                'nilai' => 'required',            
            ]);
        }
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        if ($request->id == 7) {
            $nilai = '{"merchant_id":"'.$request->merchant_id.'","secret_unbound_id":"'.$request->secret_unbound_id.'","hash_key":"'.$request->hash_key.'","url":"'.$request->url.'"}';
            Setting::where('id',$request->id)->update(['nilai'=>$nilai]);                    
        }else {
            Setting::where('id',$request->id)->update(['nilai'=>$request->nilai]);                                
        }

        return redirect()->route('setting-index')->with('msg','Sukses Menambahkan Data');
    }
    public function data()
    {        
        $model = Setting::whereNotIn('id', [5,6]);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '<a href="'.url('setting/show/'.$row->id).'" class="text-danger" data-toggle="tooltip" data-placement="Top" title="Edit Data"><span class="mdi mdi-pen"></span></a>';
                return $btn;
            })
            ->editColumn('nilai', function ($row) {
                if ($row->id == 4) {
                    if ($row->nilai == 0) {
                        $btn = 'Tidak Tampil';
                    }else{
                        $btn = 'Tampil';
                    }
                }else {
                    $btn = $row->nilai;
                }

                return $btn;
            })
            ->addColumn('nilai', function ($row) {                                
                $date = '<div style="width:600px"><p>'.$row->nilai.'</p></div>';
                return $date;
            })            
            ->rawColumns(['action','nilai'])
            ->addIndexColumn()
            ->toJson();
    }    
}
