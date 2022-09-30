<?php

namespace App\Http\Controllers;

use App\Models\MFaq;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CFaq extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('faq.index')
            ->with('bahasa',$bahasa)
            ->with('title','Faq');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('faq/create-save');
        return view('faq.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Faq')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('faq/create-save');
        return view('faq.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Faq')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MFaq::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('faq/show-save/'.$id);
        return view('faq.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Faq')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MFaq::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('faq.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Faq')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id_tipe'=> 'required', 
            'judul' => 'required', 
            'isi'   => 'required', 
        ]);
        // dd($validator->errors());
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
                
        if ($request->id) {
            $tipe = new MFaq();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->id_tipe = $request->id_tipe;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MFaq();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->id_tipe = $request->id_tipe;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MFaq::where('id',$tipe->id)->update(['id_ref_bahasa' => $tipe->id]);
        }

        return redirect()->route('faq-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'judul' => 'required', 
            'isi'   => 'required', 
            'id_tipe'=> 'required' 
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        MFaq::where('id',$request->id)->update(['id_tipe'=>$request->id_tipe, 'judul'=>$request->judul, 'isi'=>$request->isi,]);                    

        return redirect()->route('faq-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MFaq::updateDeleted($id);
        $data = MFaq::find($id);        
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MFaq::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MFaq::where('id',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('faq-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MFaq::withDeleted();
        // dd($model);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('faq/detail/'.$row->id).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('faq/show/'.$row->id).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';
                $btn .= '<a href="'.url('faq/delete/'.$row->id).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('bahasa', function ($row) {                                
                $bahasa = MBahasa::where('id_bahasa',$row->id_bahasa)->first();
                $btn = '<i class="flag-icon '.$bahasa->logo.'"></i> '.$bahasa->nama_bahasa;                
                return $btn;
            })
            ->editColumn('tipe', function ($row) {                                
                if ($row->id_tipe==1) {
                    $btn = "Renter";
                }else{
                    $btn = "Hoster";
                }
                return $btn;
            })
            ->rawColumns(['action','bahasa'])
            ->addIndexColumn()
            ->toJson();
    }

    public function bahasa(Request $request)
    {   
        $bahasa = MBahasa::where('id_bahasa',$request->kode)->first();
        $model = MFaq::withDeleted()->where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id', 'DESC')->first();        
        if ($model) {
            $data = $model->id;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
