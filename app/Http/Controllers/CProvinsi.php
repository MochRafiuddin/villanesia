<?php

namespace App\Http\Controllers;

use App\Models\MProvinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CProvinsi extends Controller
{
    public function index()
    {        
        return view('provinsi.index')            
            ->with('title','Provinsi');
    }
    public function create()
    {                   
        $url = url('provinsi/create-save');
        return view('provinsi.form')
            ->with('data',null)            
            ->with('title','Provinsi')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MProvinsi::find($id);        
        $url = url('provinsi/show-save/'.$id);
        return view('provinsi.form')
            ->with('data',$data)
            ->with('title','Provinsi')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MProvinsi::find($id);
        return view('provinsi.detail')
            ->with('data',$data)
            ->with('title','Provinsi')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_provinsi' => 'required',             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = new MProvinsi();
        $tipe->nama_provinsi = $request->nama_provinsi;            
        $tipe->save();        

        return redirect()->route('provinsi-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_provinsi' => 'required',             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        MProvinsi::where('id_provinsi',$request->id)->update(['nama_provinsi'=>$request->nama_provinsi]);                    

        return redirect()->route('provinsi-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MProvinsi::updateDeleted($id);
        $data = MProvinsi::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MProvinsi::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MProvinsi::where('id_provinsi',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('provinsi-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MProvinsi::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('provinsi/detail/'.$row->id_provinsi).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('provinsi/show/'.$row->id_provinsi).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('provinsi/delete/'.$row->id_provinsi).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
