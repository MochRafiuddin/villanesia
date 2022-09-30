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
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        // $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        // $request->file('gambar')->move(public_path('upload/negara'), $gambar);           
        // $tipe->gambar = $gambar;
        
            $tipe = new MNegara();
            $tipe->nama_negara = $request->nama_negara;
            $tipe->save();        

        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_negara' => 'required',             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        // if ($request->gambar) {            
        //     $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        //     $request->file('gambar')->move(public_path('upload/negara'), $gambar);
        //     MNegara::where('id_negara',$request->id)->update(['nama_negara'=>$request->nama_negara, 'gambar'=>$gambar]);            
        // }
        MNegara::where('id_negara',$request->id)->update(['nama_negara'=>$request->nama_negara]);            

        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MNegara::updateDeleted($id);
        $data = MNegara::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MNegara::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MNegara::where('id_negara',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('negara-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MNegara::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('negara/detail/'.$row->id_negara).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('negara/show/'.$row->id_negara).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('negara/delete/'.$row->id_negara).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->rawColumns(['action','gambar'])
            ->addIndexColumn()
            ->toJson();
    }
}
