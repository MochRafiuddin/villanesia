<?php

namespace App\Http\Controllers;

use App\Models\MSplashSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CSplashSlide extends Controller
{
    public function index()
    {        
        return view('splash_slide.index')            
            ->with('title','Splash Slide');
    }
    public function create()
    {                   
        $url = url('splash-slide/create-save');
        return view('splash_slide.form')
            ->with('data',null)            
            ->with('title','Splash Slide')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MSplashSlide::find($id);        
        $url = url('splash-slide/show-save/'.$id);
        return view('splash_slide.form')
            ->with('data',$data)
            ->with('title','Splash Slide')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MSplashSlide::find($id);
        return view('splash_slide.detail')
            ->with('data',$data)
            ->with('title','Splash Slide')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_ss' => 'required',
            'tipe' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }               
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
        $request->file('gambar')->move(public_path('upload/splash_slide'), $gambar);        
        
        $tip = new MSplashSlide();
        $tip->nama_ss = $request->nama_ss;
        $tip->tipe = $request->tipe;
        $tip->detail_text = $request->detail_text;
        $tip->gambar = $gambar;
        $tip->save();        

        return redirect()->route('splash-slide-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_ss' => 'required',
            'tipe' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }

        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            // dd($request->id);
            $request->file('gambar')->move(public_path('upload/splash_slide'), $gambar);
            MSplashSlide::where('id_ss',$request->id)->update(['nama_ss'=>$request->nama_ss, 'gambar'=>$gambar,'tipe'=>$request->tipe, 'detail_text'=>$request->detail_text]);
        }else{
            MSplashSlide::where('id_ss',$request->id)->update(['nama_ss'=>$request->nama_ss, 'tipe'=>$request->tipe, 'detail_text'=>$request->detail_text]);
        }        

        return redirect()->route('splash-slide-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MSplashSlide::updateDeleted($id);
        return redirect()->route('splash-slide-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MSplashSlide::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('splash-slide/detail/'.$row->id_ss).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('splash-slide/show/'.$row->id_ss).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('splash-slide/delete/'.$row->id_ss).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->editColumn('tipe', function ($row) {
                if ($row->tipe == 1) {
                    $btn ="Splash Screen";
                }else {
                    $btn ="Image Slide";
                }        
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
