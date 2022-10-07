<?php

namespace App\Http\Controllers;

use App\Models\MAmenities;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CAmenities extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('amenities.index')
            ->with('bahasa',$bahasa)
            ->with('title','Amenities');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('amenities/create-save');
        return view('amenities.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Amenities')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('amenities/create-save');
        return view('amenities.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Amenities')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MAmenities::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('amenities/show-save/'.$id);
        return view('amenities.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Amenities')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MAmenities::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('amenities.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Amenities')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_amenities' => 'required', 
            'icon'         => 'mimes:jpeg,jpg,png,gif|max:10000',            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->file('icon')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('icon')->extension();
            $request->file('icon')->move(public_path('upload/amenities'), $gambar);
        }else{
            $gambar ="";
        }
        if ($request->id) {
            $tipe = new MAmenities();
            $tipe->nama_amenities = $request->nama_amenities;
            $tipe->icon = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MAmenities();
            $tipe->nama_amenities = $request->nama_amenities;
            $tipe->icon = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MAmenities::where('id_amenities',$tipe->id_amenities)->update(['id_ref_bahasa' => $tipe->id_amenities]);
        }

        return redirect()->route('amenities-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_amenities' => 'required', 
            'icon'         => 'mimes:jpeg,jpg,png,gif|max:10000',            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->icon) {            
            $icon = round(microtime(true) * 1000).'.'.$request->file('icon')->extension();
            $request->file('icon')->move(public_path('upload/amenities'), $icon);
            MAmenities::where('id_amenities',$request->id)->update(['nama_amenities'=>$request->nama_amenities, 'icon'=>$icon]);            
        }else{
            MAmenities::where('id_amenities',$request->id)->update(['nama_amenities'=>$request->nama_amenities]);            
        }

        return redirect()->route('amenities-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MAmenities::updateDeleted($id);
        $data = MAmenities::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MAmenities::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MAmenities::where('id_amenities',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('amenities-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MAmenities::withDeleted()->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_amenities.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('amenities/detail/'.$row->id_amenities).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('amenities/show/'.$row->id_amenities).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('amenities/delete/'.$row->id_amenities).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MAmenities::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_amenities', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_amenities;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
