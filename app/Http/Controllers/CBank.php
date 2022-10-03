<?php

namespace App\Http\Controllers;

use App\Models\MBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CBank extends Controller
{
    public function index()
    {        
        return view('bank.index')            
            ->with('title','Bank');
    }
    public function create()
    {                   
        $url = url('bank/create-save');
        return view('bank.form')
            ->with('data',null)            
            ->with('title','Bank')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MBank::find($id);        
        $url = url('bank/show-save/'.$id);
        return view('bank.form')
            ->with('data',$data)
            ->with('title','Bank')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MBank::find($id);
        return view('bank.detail')
            ->with('data',$data)
            ->with('title','Bank')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_bank' => 'required',
            'no_telfon' => 'required'             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = new MBank();
        $tipe->nama_bank = $request->nama_bank;            
        $tipe->no_telfon = $request->no_telfon;            
        $tipe->save();        

        return redirect()->route('master-bank-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_bank' => 'required',
            'no_telfon' => 'required'             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        MBank::where('id_bank',$request->id)->update(['nama_bank'=>$request->nama_bank,'no_telfon'=>$request->no_telfon]);                    

        return redirect()->route('master-bank-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MBank::updateDeleted($id);
        return redirect()->route('master-bank-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MBank::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('bank/detail/'.$row->id_bank).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('bank/show/'.$row->id_bank).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('bank/delete/'.$row->id_bank).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
}
