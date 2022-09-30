<?php

namespace App\Http\Controllers;

use App\Models\MTipeProperti;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CTipeProperti extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('tipe_properti.index')
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('tipe-properti/create-save');
        return view('tipe_properti.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('tipe-properti/create-save');
        return view('tipe_properti.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MTipeProperti::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('tipe-properti/show-save/'.$id);
        return view('tipe_properti.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MTipeProperti::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('tipe_properti.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_tipe_properti' => 'required', 
            'gambar'             => 'mimes:jpeg,jpg,png,gif|required|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        $request->file('gambar')->move(public_path('upload/tipe_properti'), $gambar);           
        if ($request->id) {
            $tipe = new MTipeProperti();
            $tipe->nama_tipe_properti = $request->nama_tipe_properti;
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MTipeProperti();
            $tipe->nama_tipe_properti = $request->nama_tipe_properti;
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MTipeProperti::where('id_tipe_properti',$tipe->id_tipe_properti)->update(['id_ref_bahasa' => $tipe->id_tipe_properti]);
        }

        return redirect()->route('tipe-properti-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_tipe_properti' => 'required', 
            'gambar'             => 'mimes:jpeg,jpg,png,gif|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/tipe_properti'), $gambar);
            MTipeProperti::where('id_tipe_properti',$request->id)->update(['nama_tipe_properti'=>$request->nama_tipe_properti, 'gambar'=>$gambar]);            
        }else{
            MTipeProperti::where('id_tipe_properti',$request->id)->update(['nama_tipe_properti'=>$request->nama_tipe_properti]);            
        }

        return redirect()->route('tipe-properti-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        $data = MTipeProperti::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MTipeProperti::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MTipeProperti::where('id_tipe_properti',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('tipe-properti-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MTipeProperti::withDeleted()->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_tipe_properti.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('tipe-properti/detail/'.$row->id_tipe_properti).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('tipe-properti/show/'.$row->id_tipe_properti).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('tipe-properti/delete/'.$row->id_tipe_properti).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('bahasa', function ($row) {                                
                $bahasa = MBahasa::where('id_bahasa',$row->id_bahasa)->first();
                $btn = '<i class="flag-icon '.$bahasa->logo.'"></i> '.$bahasa->nama_bahasa;                
                return $btn;
            })
            ->rawColumns(['action','bahasa'])
            ->addIndexColumn()
            ->toJson();
    }

    public function bahasa(Request $request)
    {   
        $bahasa = MBahasa::where('id_bahasa',$request->kode)->first();
        $model = MTipeProperti::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_tipe_properti', 'DESC')->first();        
        if ($model) {
            $data = $model->id_tipe_properti;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
