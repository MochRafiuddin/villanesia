<?php

namespace App\Http\Controllers;

use App\Models\MJenisTempat;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CJenisTempat extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('jenis_tempat.index')
            ->with('bahasa',$bahasa)
            ->with('title','Jenis Tempat');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('jenis-tempat/create-save');
        return view('jenis_tempat.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Jenis Tempat')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('jenis-tempat/create-save');
        return view('jenis_tempat.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Jenis Tempat')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MJenisTempat::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('jenis-tempat/show-save/'.$id);
        return view('jenis_tempat.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Jenis Tempat')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MJenisTempat::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('jenis_tempat.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_jenis_tempat' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }        
        if ($request->id) {
            $tipe = new MJenisTempat();
            $tipe->nama_jenis_tempat = $request->nama_jenis_tempat;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MJenisTempat();
            $tipe->nama_jenis_tempat = $request->nama_jenis_tempat;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MJenisTempat::where('id_jenis_tempat',$tipe->id_jenis_tempat)->update(['id_ref_bahasa' => $tipe->id_jenis_tempat]);
        }

        return redirect()->route('jenis-tempat-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_jenis_tempat' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
                
        MJenisTempat::where('id_jenis_tempat',$request->id)->update(['nama_jenis_tempat'=>$request->nama_jenis_tempat]);                    

        return redirect()->route('jenis-tempat-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MJenisTempat::updateDeleted($id);
        return redirect()->route('jenis-tempat-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MJenisTempat::withDeleted()->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_jenis_tempat.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust"></span></a>';
                $btn .= '<a href="'.url('jenis-tempat/detail/'.$row->id_jenis_tempat).'" class="text-warning mr-2"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('jenis-tempat/show/'.$row->id_jenis_tempat).'" class="text-danger mr-2"><span class="mdi mdi-pen"></span></a>';                
                $btn .= '<a href="'.url('jenis-tempat/delete/'.$row->id_jenis_tempat).'" class="text-primary delete"><span class="mdi mdi-delete"></span></a>';
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
        $model = MJenisTempat::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_jenis_tempat', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_jenis_tempat;
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
