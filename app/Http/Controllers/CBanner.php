<?php

namespace App\Http\Controllers;

use App\Models\MBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CBanner extends Controller
{
    public function index()
    {        
        return view('banner.index')            
            ->with('title','Banner');
    }
    public function create()
    {                   
        $url = url('banner/create-save');
        return view('banner.form')
            ->with('data',null)            
            ->with('title','Banner')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MBanner::find($id);        
        $url = url('banner/show-save/'.$id);
        return view('banner.form')
            ->with('data',$data)
            ->with('title','Banner')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MBanner::find($id);
        return view('banner.detail')
            ->with('data',$data)
            ->with('title','Banner')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_banner' => 'required', 
            'status' => 'required',
            'image_banner'    => 'mimes:jpeg,jpg,png,gif|required|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        $gambar = round(microtime(true) * 1000).'.'.$request->file('image_banner')->extension();
        $request->file('image_banner')->move(public_path('upload/banner'), $gambar);
        
        $tipe = new MBanner();
        $tipe->nama_banner = $request->nama_banner;            
        $tipe->status = $request->status;
        $tipe->image_banner = $gambar;
        $tipe->save();        

        return redirect()->route('master-banner-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_banner' => 'required', 
            'status' => 'required',
            'image_banner'    => 'mimes:jpeg,jpg,png,gif|max:10000' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->file('image_banner')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('image_banner')->extension();
            $request->file('image_banner')->move(public_path('upload/banner'), $gambar);
            MBanner::where('id',$request->id)->update(['nama_banner'=>$request->nama_banner,'status'=>$request->status,'image_banner'=>$gambar]);
        }else {
            MBanner::where('id',$request->id)->update(['nama_banner'=>$request->nama_banner,'status'=>$request->status]);
        }
        

        return redirect()->route('master-banner-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MBanner::updateDeleted($id);
        return redirect()->route('master-banner-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MBanner::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('banner/detail/'.$row->id).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('banner/show/'.$row->id).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('banner/delete/'.$row->id).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('gambar', function ($row) {
                $btn = '<img src="'.asset('upload/banner/'.$row->image_banner).'" class="rounded-0" style="width:250px;height:100px"/>';
                return $btn;
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    $btn = 'Tampil';
                }else {
                    $btn = 'Tidak Tampil';
                }
                return $btn;
            })
            ->rawColumns(['action','gambar','status'])
            ->addIndexColumn()
            ->toJson();
    }
}
