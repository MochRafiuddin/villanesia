<?php

namespace App\Http\Controllers;

use App\Models\MKupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CKupon extends Controller
{
    public function index()
    {        
        return view('kupon.index')            
            ->with('title','Kupon');
    }
    public function create()
    {        
        $url = url('kupon/create-save');
        return view('kupon.form')
            ->with('data',null)
            ->with('title','Kupon')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {
        $data = MKupon::find($id);        
        $url = url('kupon/show-save/'.$id);
        return view('kupon.form')
            ->with('data',$data)
            ->with('title','Kupon')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {   
        $data = MKupon::find($id);
        return view('kupon.detail')
            ->with('data',$data)
            ->with('title','Kupon')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'kode_kupon' => 'required',
            'nama_kupon' => 'required',
            'deskripsi' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kuota_kupon' => 'required',
            'kuota_terpakai' => 'required',
            'satuan' => 'required',
            'nominal' => 'required',
            'maks_diskon' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = new MKupon();
        $tipe->kode_kupon = $request->kode_kupon;
        $tipe->nama_kupon = $request->nama_kupon;
        $tipe->deskripsi = $request->deskripsi;
        $tipe->tanggal_mulai = date('Y-m-d',strtotime($request->tanggal_mulai));
        $tipe->tanggal_selesai = date('Y-m-d',strtotime($request->tanggal_selesai));
        $tipe->kuota_kupon = $request->kuota_kupon;
        $tipe->kuota_terpakai = $request->kuota_terpakai;
        $tipe->satuan = $request->satuan;
        $tipe->nominal = $request->nominal;
        $tipe->maks_diskon = $request->maks_diskon;
        $tipe->save();        

        return redirect()->route('kupon-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'kode_kupon' => 'required',
            'nama_kupon' => 'required',
            'deskripsi' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kuota_kupon' => 'required',
            'kuota_terpakai' => 'required',
            'satuan' => 'required',
            'nominal' => 'required',
            'maks_diskon' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        $data = [
            'kode_kupon' => $request->kode_kupon,
            'nama_kupon' => $request->nama_kupon,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => date('Y-m-d',strtotime($request->tanggal_mulai)),
            'tanggal_selesai' => date('Y-m-d',strtotime($request->tanggal_selesai)),
            'kuota_kupon' => $request->kuota_kupon,
            'kuota_terpakai' => $request->kuota_terpakai,
            'satuan' => $request->satuan,
            'nominal' => $request->nominal,
            'maks_diskon' => $request->maks_diskon,
        ];
        MKupon::where('id_kupon',$request->id)->update($data);

        return redirect()->route('kupon-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MKupon::updateDeleted($id);
        return redirect()->route('kupon-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MKupon::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                     
                $btn .= '<a href="'.url('kupon/detail/'.$row->id_kupon).'" class="text-warning mr-2"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('kupon/show/'.$row->id_kupon).'" class="text-danger mr-2"><span class="mdi mdi-pen"></span></a>';                
                $btn .= '<a href="'.url('kupon/delete/'.$row->id_kupon).'" class="text-primary delete"><span class="mdi mdi-delete"></span></a>';
                return $btn;
            })
            ->editColumn('tanggal_mulai', function ($row) {
                $hsl3=date('d-m-Y',strtotime($row->tanggal_mulai));
                return $hsl3;
            })
            ->editColumn('tanggal_selesai', function ($row) {
                $hsl3=date('d-m-Y',strtotime($row->tanggal_selesai));
                return $hsl3;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
