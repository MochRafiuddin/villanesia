<?php

namespace App\Http\Controllers;

use App\Models\MAkhirPekan;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CAkhirPekan extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('akhir_pekan.index')
            ->with('bahasa',$bahasa)
            ->with('title','Akhir Pekan');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('akhir-pekan/create-save');
        return view('akhir_pekan.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Akhir Pekan')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('akhir-pekan/create-save');
        return view('akhir_pekan.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Akhir Pekan')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MAkhirPekan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('akhir-pekan/show-save/'.$id);
        return view('akhir_pekan.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Akhir Pekan')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MAkhirPekan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('akhir_pekan.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Akhir Pekan')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'detail_akhir_pekan' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }        
        if ($request->id) {
            $tipe = new MAkhirPekan();
            $tipe->detail_akhir_pekan = $request->detail_akhir_pekan;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MAkhirPekan();
            $tipe->detail_akhir_pekan = $request->detail_akhir_pekan;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MAkhirPekan::where('id_akhir_pekan',$tipe->id_akhir_pekan)->update(['id_ref_bahasa' => $tipe->id_akhir_pekan]);
        }

        return redirect()->route('akhir-pekan-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'detail_akhir_pekan' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
                
        MAkhirPekan::where('id_akhir_pekan',$request->id)->update(['detail_akhir_pekan'=>$request->detail_akhir_pekan]);                    

        return redirect()->route('akhir-pekan-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MAkhirPekan::updateDeleted($id);
        $data = MAkhirPekan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MAkhirPekan::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MAkhirPekan::where('id_akhir_pekan',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('akhir-pekan-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MAkhirPekan::withDeleted()->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_akhir_pekan.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('akhir-pekan/detail/'.$row->id_akhir_pekan).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('akhir-pekan/show/'.$row->id_akhir_pekan).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('akhir-pekan/delete/'.$row->id_akhir_pekan).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MAkhirPekan::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_akhir_pekan', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_akhir_pekan;
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
