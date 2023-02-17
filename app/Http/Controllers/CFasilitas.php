<?php

namespace App\Http\Controllers;

use App\Models\MFasilitas;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CFasilitas extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('fasilitas.index')
            ->with('bahasa',$bahasa)
            ->with('title','Fasilitas');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('fasilitas/create-save');
        return view('fasilitas.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Fasilitas')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('fasilitas/create-save');
        return view('fasilitas.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Fasilitas')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MFasilitas::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('fasilitas/show-save/'.$id);
        return view('fasilitas.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Fasilitas')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MFasilitas::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('fasilitas.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Fasilitas')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_fasilitas' => 'required', 
            'tampil_depan' => 'required', 
            'icon'         => 'mimes:jpeg,jpg,png,gif|max:10000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        if ($request->file('icon')) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('icon')->extension();
            $request->file('icon')->move(public_path('upload/fasilitas'), $gambar);           
        }else{
            $gambar ="";
        }
        if ($request->id) {
            $tipe = new MFasilitas();
            $tipe->nama_fasilitas = $request->nama_fasilitas;
            $tipe->icon = $gambar;                      
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->tampil_depan = $request->tampil_depan;
            $tipe->save();
        }else{
            $tipe = new MFasilitas();
            $tipe->nama_fasilitas = $request->nama_fasilitas;
            $tipe->icon = $gambar;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->tampil_depan = $request->tampil_depan;
            $tipe->save();
    
            MFasilitas::where('id_fasilitas',$tipe->id_fasilitas)->update(['id_ref_bahasa' => $tipe->id_fasilitas]);
        }

        return redirect()->route('fasilitas-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_fasilitas' => 'required', 
            'tampil_depan' => 'required', 
            'icon'         => 'mimes:jpeg,jpg,png,gif|max:10000',

        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->icon) {            
            $icon = round(microtime(true) * 1000).'.'.$request->file('icon')->extension();
            $request->file('icon')->move(public_path('upload/fasilitas'), $icon);
            MFasilitas::where('id_fasilitas',$request->id)->update(['nama_fasilitas'=>$request->nama_fasilitas, 'icon'=>$icon,'tampil_depan'=>$request->tampil_depan]);
        }else{
            MFasilitas::where('id_fasilitas',$request->id)->update(['nama_fasilitas'=>$request->nama_fasilitas, 'tampil_depan'=>$request->tampil_depan]);
        }

        return redirect()->route('fasilitas-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MFasilitas::updateDeleted($id);
        $data = MFasilitas::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MFasilitas::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MFasilitas::where('id_fasilitas',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('fasilitas-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MFasilitas::withDeleted()->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa')->orderBy('tampil_depan','desc');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_fasilitas.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('fasilitas/detail/'.$row->id_fasilitas).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('fasilitas/show/'.$row->id_fasilitas).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('fasilitas/delete/'.$row->id_fasilitas).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('bahasa', function ($row) {                                
                $bahasa = MBahasa::where('id_bahasa',$row->id_bahasa)->first();
                $btn = '<i class="flag-icon '.$bahasa->logo.'"></i> '.$bahasa->nama_bahasa;                
                return $btn;
            })
            ->editColumn('tampil_depan', function ($row) {                                
                if ($row->tampil_depan==1) {
                    $btn ="Ya";
                }else {
                    $btn ="Tidak";
                }
                return $btn;
            })            
            ->rawColumns(['action','bahasa','icon'])
            ->addIndexColumn()
            ->toJson();
    }

    public function bahasa(Request $request)
    {   
        $bahasa = MBahasa::where('id_bahasa',$request->kode)->first();
        $model = MFasilitas::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_fasilitas', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_fasilitas;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
