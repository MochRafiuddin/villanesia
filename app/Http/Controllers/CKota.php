<?php

namespace App\Http\Controllers;

use App\Models\MKota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CKota extends Controller
{
    public function index()
    {        
        return view('kota.index')            
            ->with('title','Kota');
    }
    public function create()
    {                   
        $url = url('kota/create-save');
        return view('kota.form')
            ->with('data',null)            
            ->with('title','Kota')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MKota::find($id);        
        $url = url('kota/show-save/'.$id);
        return view('kota.form')
            ->with('data',$data)
            ->with('title','Kota')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MKota::find($id);
        return view('kota.detail')
            ->with('data',$data)
            ->with('title','Kota')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_kota' => 'required', 
            'gambar'    => 'mimes:jpeg,jpg,png,gif|required|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        $request->file('gambar')->move(public_path('upload/kota'), $gambar);           
        
            $tipe = new MKota();
            $tipe->nama_kota = $request->nama_kota;
            $tipe->gambar = $gambar;
            $tipe->save();        

        return redirect()->route('kota-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_kota' => 'required', 
            'gambar'    => 'mimes:jpeg,jpg,png,gif|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/kota'), $gambar);
            MKota::where('id_kota',$request->id)->update(['nama_kota'=>$request->nama_kota, 'gambar'=>$gambar]);            
        }else{
            MKota::where('id_kota',$request->id)->update(['nama_kota'=>$request->nama_kota]);            
        }

        return redirect()->route('kota-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MKota::updateDeleted($id);
        return redirect()->route('kota-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MKota::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('kota/detail/'.$row->id_kota).'" class="text-warning mr-2"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('kota/show/'.$row->id_kota).'" class="text-danger mr-2"><span class="mdi mdi-pen"></span></a>';                
                $btn .= '<a href="'.url('kota/delete/'.$row->id_kota).'" class="text-primary delete"><span class="mdi mdi-delete"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
