<?php

namespace App\Http\Controllers;

use App\Models\MConciergeService;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CConciergeService extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('con_service.index')
            ->with('bahasa',$bahasa)
            ->with('title','Concierge Service');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('concierge-service/create-save');
        return view('con_service.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Concierge Service')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('concierge-service/create-save');
        return view('con_service.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Concierge Service')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MConciergeService::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('concierge-service/show-save/'.$id);
        return view('con_service.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Concierge Service')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MConciergeService::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('con_service.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Concierge Service')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_con_service' => 'required', 
            'gambar'          => 'mimes:jpeg,jpg,png,gif|max:10000',
            'show'            => 'required',
            'text_to_wa'      => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->file('gambar')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/con_service'), $gambar);         
        }else {
            $gambar = "";
        }         
        if ($request->id) {
            $tipe = new MConciergeService();
            $tipe->nama_con_service = $request->nama_con_service;                    
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;            
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
        }else{
            $tipe = new MConciergeService();
            $tipe->nama_con_service = $request->nama_con_service;                     
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;            
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
    
            MConciergeService::where('id_con_service',$tipe->id_con_service)->update(['id_ref_bahasa' => $tipe->id_con_service]);
        }

        return redirect()->route('concierge-service-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_con_service' => 'required', 
            'gambar'          => 'mimes:jpeg,jpg,png,gif|max:10000',
            'show'            => 'required',
            'text_to_wa'      => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/con_service'), $gambar);
            MConciergeService::where('id_con_service',$request->id)->update(['nama_con_service'=>$request->nama_con_service, 'gambar'=>$gambar, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);
        }else{
            MConciergeService::where('id_con_service',$request->id)->update(['nama_con_service'=>$request->nama_con_service, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);            
        }

        return redirect()->route('concierge-service-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MConciergeService::updateDeleted($id);
        $data = MConciergeService::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MConciergeService::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MConciergeService::where('id_con_service',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('concierge-service-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MConciergeService::withDeleted()->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_con_service.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('concierge-service/detail/'.$row->id_con_service).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('concierge-service/show/'.$row->id_con_service).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('concierge-service/delete/'.$row->id_con_service).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MConciergeService::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_con_service', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_con_service;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
