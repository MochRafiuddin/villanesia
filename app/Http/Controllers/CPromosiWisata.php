<?php

namespace App\Http\Controllers;

use App\Models\MPromosiWisata;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CPromosiWisata extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('promosi_wisata.index')
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Wisata');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('promosi-wisata/create-save');
        return view('promosi_wisata.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Wisata')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('promosi-wisata/create-save');
        return view('promosi_wisata.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Wisata')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MPromosiWisata::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('promosi-wisata/show-save/'.$id);
        return view('promosi_wisata.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Wisata')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MPromosiWisata::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('promosi_wisata.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Promosi Wisata')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_pro_wisata' => 'required', 
            'gambar'          => 'mimes:jpeg,jpg,png,gif|max:10000',
            'detail'          => 'required',
            'show'            => 'required',
        ]);
        // 'text_to_wa'      => 'required',
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->file('gambar')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/promosi_wisata'), $gambar);           
        }else {
            $gambar = "";
        }
        
        if ($request->id) {
            $tipe = new MPromosiWisata();
            $tipe->nama_pro_wisata = $request->nama_pro_wisata;                    
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->detail = $request->detail;
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
        }else{
            $tipe = new MPromosiWisata();
            $tipe->nama_pro_wisata = $request->nama_pro_wisata;                     
            $tipe->gambar = $gambar;
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->detail = $request->detail;
            $tipe->show = $request->show;
            $tipe->text_to_wa = $request->text_to_wa;
            $tipe->save();
    
            MPromosiWisata::where('id_pro_wisata',$tipe->id_pro_wisata)->update(['id_ref_bahasa' => $tipe->id_pro_wisata]);
        }

        return redirect()->route('promosi-wisata-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_pro_wisata' => 'required', 
            'gambar'          => 'mimes:jpeg,jpg,png,gif|max:10000',
            'detail'          => 'required',
            'show'            => 'required',
        ]);
        // 'text_to_wa'      => 'required',
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->gambar) {            
            $gambar = round(microtime(true) * 1000).'.'.$request->file('gambar')->extension();
            $request->file('gambar')->move(public_path('upload/promosi_wisata'), $gambar);
            MPromosiWisata::where('id_pro_wisata',$request->id)->update(['nama_pro_wisata'=>$request->nama_pro_wisata, 'gambar'=>$gambar, 'detail'=>$request->detail, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);
        }else{
            MPromosiWisata::where('id_pro_wisata',$request->id)->update(['nama_pro_wisata'=>$request->nama_pro_wisata, 'detail'=>$request->detail, 'show'=>$request->show, 'text_to_wa'=>$request->text_to_wa]);            
        }

        return redirect()->route('promosi-wisata-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        // MPromosiWisata::updateDeleted($id);
        $data = MPromosiWisata::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MPromosiWisata::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MPromosiWisata::where('id_pro_wisata',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('promosi-wisata-index')->with('msg','Sukses Menambahkan Data');
    }
    public function data()
    {
        $model = MPromosiWisata::withDeleted()->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_pro_wisata.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('promosi-wisata/detail/'.$row->id_pro_wisata).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('promosi-wisata/show/'.$row->id_pro_wisata).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('promosi-wisata/delete/'.$row->id_pro_wisata).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MPromosiWisata::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_pro_wisata', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_pro_wisata;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
