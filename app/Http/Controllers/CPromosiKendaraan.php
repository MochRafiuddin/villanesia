<?php

namespace App\Http\Controllers;

use App\Models\MPromosiKendaraan;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CPromosiKendaraan extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('promosi_kendaraan.index')
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Kendaraan');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('promosi-kendaraan/create-save');
        return view('promosi_kendaraan.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Kendaraan')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('promosi-kendaraan/create-save');
        return view('promosi_kendaraan.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Kendaraan')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MPromosiKendaraan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('promosi-kendaraan/show-save/'.$id);
        return view('promosi_kendaraan.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Kendaraan')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MPromosiKendaraan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('promosi_kendaraan.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Kendaraan')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_pro_kendaraan' => 'required', 
            'gambar'             => 'mimes:jpeg,jpg,png,gif|max:10000',
            'detail_harga'       => 'required',
            'show'               => 'required',
            'text_to_wa'         => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        if ($request->file('gambar')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/promosi_kendaraan'), $gambar);           
        }else{
            $gambar ="";
        }
        if ($request->id) {
            $tipe = new MPromosiKendaraan();
            $tipe->nama_pro_kendaraan = $request->nama_pro_kendaraan;                    
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->detail_harga = $request->detail_harga;
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
        }else{
            $tipe = new MPromosiKendaraan();
            $tipe->nama_pro_kendaraan = $request->nama_pro_kendaraan;                     
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->detail_harga = $request->detail_harga;
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
    
            MPromosiKendaraan::where('id_pro_kendaraan',$tipe->id_pro_kendaraan)->update(['id_ref_bahasa' => $tipe->id_pro_kendaraan]);
        }

        return redirect()->route('promosi-kendaraan-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_pro_kendaraan' => 'required', 
            'gambar'             => 'mimes:jpeg,jpg,png,gif|max:10000',
            'detail_harga'       => 'required',
            'show'               => 'required',
            'text_to_wa'         => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/promosi_kendaraan'), $gambar);
            MPromosiKendaraan::where('id_pro_kendaraan',$request->id)->update(['nama_pro_kendaraan'=>$request->nama_pro_kendaraan, 'gambar'=>$gambar, 'detail_harga'=>$request->detail_harga, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);
        }else{
            MPromosiKendaraan::where('id_pro_kendaraan',$request->id)->update(['nama_pro_kendaraan'=>$request->nama_pro_kendaraan, 'detail_harga'=>$request->detail_harga, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);            
        }

        return redirect()->route('promosi-kendaraan-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MPromosiKendaraan::updateDeleted($id);
        $data = MPromosiKendaraan::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MPromosiKendaraan::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MPromosiKendaraan::where('id_pro_kendaraan',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('promosi-kendaraan-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MPromosiKendaraan::withDeleted()->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_pro_kendaraan.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('promosi-kendaraan/detail/'.$row->id_pro_kendaraan).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('promosi-kendaraan/show/'.$row->id_pro_kendaraan).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('promosi-kendaraan/delete/'.$row->id_pro_kendaraan).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MPromosiKendaraan::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_pro_kendaraan', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_pro_kendaraan;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
