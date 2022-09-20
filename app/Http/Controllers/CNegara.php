<?php

namespace App\Http\Controllers;

use App\Models\MNegara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CNegara extends Controller
{
    public function index()
    {        
        return view('negara.index')            
            ->with('title','Negara');
    }
    public function create()
    {                   
        $url = url('negara/create-save');
        return view('negara.form')
            ->with('data',null)            
            ->with('title','Negara')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MNegara::find($id);        
        $url = url('negara/show-save/'.$id);
        return view('negara.form')
            ->with('data',$data)
            ->with('title','Negara')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MNegara::find($id);
        return view('negara.detail')
            ->with('data',$data)
            ->with('title','Negara')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_negara' => 'required', 
            'gambar'      => 'mimes:jpeg,jpg,png,gif|required|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        $request->file('gambar')->move(public_path('upload/negara'), $gambar);           
        
            $tipe = new MNegara();
            $tipe->nama_negara = $request->nama_negara;
            $tipe->gambar = $gambar;
            $tipe->save();        

        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_negara' => 'required', 
            'gambar'      => 'mimes:jpeg,jpg,png,gif|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/negara'), $gambar);
            MNegara::where('id_negara',$request->id)->update(['nama_negara'=>$request->nama_negara, 'gambar'=>$gambar]);            
        }else{
            MNegara::where('id_negara',$request->id)->update(['nama_negara'=>$request->nama_negara]);            
        }

        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MNegara::updateDeleted($id);
        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MNegara::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('negara/detail/'.$row->id_negara).'" class="text-warning mr-2"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('negara/show/'.$row->id_negara).'" class="text-danger mr-2"><span class="mdi mdi-pen"></span></a>';                
                $btn .= '<a href="'.url('negara/delete/'.$row->id_negara).'" class="text-primary delete"><span class="mdi mdi-delete"></span></a>';
                return $btn;
            })
            ->rawColumns(['action','gambar'])
            ->addIndexColumn()
            ->toJson();
    }
}
