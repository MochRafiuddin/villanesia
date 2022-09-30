<?php

namespace App\Http\Controllers;

use App\Models\MAboutUs;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CAboutUs extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('about_us.index')
            ->with('bahasa',$bahasa)
            ->with('title','About Us');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('about-us/create-save');
        return view('about_us.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','About Us')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('about-us/create-save');
        return view('about_us.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','About Us')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MAboutUs::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('about-us/show-save/'.$id);
        return view('about_us.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','About Us')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MAboutUs::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('about_us.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','About Us')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'judul' => 'required', 
            'isi'   => 'required', 
            'gambar'=> 'mimes:jpeg,jpg,png,gif|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        $request->file('gambar')->move(public_path('upload/about_us'), $gambar);           
        if ($request->id) {
            $tipe = new MAboutUs();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MAboutUs();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MAboutUs::where('id',$tipe->id)->update(['id_ref_bahasa' => $tipe->id]);
        }

        return redirect()->route('about-us-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'judul' => 'required', 
            'isi'   => 'required', 
            'gambar'=> 'mimes:jpeg,jpg,png,gif|max:10000'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/about_us'), $gambar);
            MAboutUs::where('id',$request->id)->update(['judul'=>$request->judul, 'isi'=>$request->isi, 'gambar'=>$gambar]);            
        }else{
            MAboutUs::where('id',$request->id)->update(['judul'=>$request->judul, 'isi'=>$request->isi,]);            
        }

        return redirect()->route('about-us-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MAboutUs::updateDeleted($id);
        return redirect()->route('about-us-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MAboutUs::select('*');
        // dd($model);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('about-us/detail/'.$row->id).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('about-us/show/'.$row->id).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
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
        $model = MAboutUs::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id', 'DESC')->first();        
        if ($model) {
            $data = $model->id;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
