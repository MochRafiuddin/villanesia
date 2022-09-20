<?php

namespace App\Http\Controllers;

use App\Models\MPrivacyPolicyDetail;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CPrivacyPolicyDetail extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('privacy_policy.index')
            ->with('bahasa',$bahasa)
            ->with('title','Privacy Policy');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('privacy-policy/create-save');
        return view('privacy_policy.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Privacy Policy')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('privacy-policy/create-save');
        return view('privacy_policy.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Privacy Policy')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MPrivacyPolicyDetail::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('privacy-policy/show-save/'.$id);
        return view('privacy_policy.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Privacy Policy')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MPrivacyPolicyDetail::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('privacy_policy.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Privacy Policy')
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
            $tipe = new MPrivacyPolicyDetail();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->id_tipe = $request->id_tipe;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MPrivacyPolicyDetail();
            $tipe->judul = $request->judul;
            $tipe->isi = $request->isi;
            $tipe->id_tipe = $request->id_tipe;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MPrivacyPolicyDetail::where('id',$tipe->id)->update(['id_ref_bahasa' => $tipe->id]);
        }

        return redirect()->route('privacy-policy-index')->with('msg','Sukses Menambahkan Data');
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
        
        MPrivacyPolicyDetail::where('id',$request->id)->update(['id_tipe'=>$request->id_tipe, 'judul'=>$request->judul, 'isi'=>$request->isi,]);                    

        return redirect()->route('privacy-policy-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MPrivacyPolicyDetail::updateDeleted($id);
        return redirect()->route('privacy-policy-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MPrivacyPolicyDetail::withDeleted()->orderBy('id_ref_bahasa');
        // dd($model);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust"></span></a>';
                $btn .= '<a href="'.url('privacy-policy/detail/'.$row->id).'" class="text-warning mr-2"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('privacy-policy/show/'.$row->id).'" class="text-danger mr-2"><span class="mdi mdi-pen"></span></a>';
                if ($row->id_tipe!=1) {                    
                    $btn .= '<a href="'.url('privacy-policy/delete/'.$row->id).'" class="text-primary delete"><span class="mdi mdi-delete"></span></a>';
                }
                return $btn;
            })
            ->addColumn('bahasa', function ($row) {                                
                $bahasa = MBahasa::where('id_bahasa',$row->id_bahasa)->first();
                $btn = '<i class="flag-icon '.$bahasa->logo.'"></i> '.$bahasa->nama_bahasa;                
                return $btn;
            })
            ->editColumn('tipe', function ($row) {                                
                if ($row->id_tipe==1) {
                    $btn = "Privacy Police";
                }else{
                    $btn = "Seperti FAQ";
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
        $model = MPrivacyPolicyDetail::withDeleted()->where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id', 'DESC')->first();        
        if ($model) {
            $data = $model->id;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
