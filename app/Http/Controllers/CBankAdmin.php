<?php

namespace App\Http\Controllers;

use App\Models\MBankAdmin;
use App\Models\MBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CBankAdmin extends Controller
{
    public function index()
    {        
        return view('bank_admin.index')            
            ->with('title','Bank Admin');
    }
    public function create()
    {
        $bank = MBank::withDeleted()->get();        
        $url = url('bank-admin/create-save');
        return view('bank_admin.form')
            ->with('data',null)
            ->with('bank',$bank)
            ->with('title','Bank Admin')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {
        $bank = MBank::withDeleted()->get();
        $data = MBankAdmin::find($id);        
        $url = url('bank-admin/show-save/'.$id);
        return view('bank_admin.form')
            ->with('data',$data)
            ->with('bank',$bank)
            ->with('title','Bank Admin')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {   
        $bank = MBank::withDeleted()->get();
        $data = MBankAdmin::find($id);
        return view('bank_admin.detail')
            ->with('data',$data)
            ->with('bank',$bank)
            ->with('title','Bank Admin')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id_bank' => 'required',
            'branch' => 'required',
            'acc_name' => 'required',
            'acc_number' => 'required'            
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = new MBankAdmin();
        $tipe->id_bank = $request->id_bank;            
        $tipe->branch = $request->branch;
        $tipe->acc_name = $request->acc_name;
        $tipe->acc_number = $request->acc_number;
        $tipe->save();        

        return redirect()->route('bank-admin-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id_bank' => 'required',
            'branch' => 'required',
            'acc_name' => 'required',
            'acc_number' => 'required'          
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        MBankAdmin::where('id_bank_admin',$request->id)->update(['id_bank'=>$request->id_bank, 'branch'=>$request->branch, 'acc_name'=>$request->acc_name, 'acc_number'=>$request->acc_number]);

        return redirect()->route('bank-admin-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MBankAdmin::updateDeleted($id);
        return redirect()->route('bank-admin-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MBankAdmin::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                                
                $btn .= '<a href="'.url('bank-admin/detail/'.$row->id_bank_admin).'" class="text-warning mr-2" data-toggle="tooltip" data-placement="Top" title="Detail Data"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('bank-admin/show/'.$row->id_bank_admin).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('bank-admin/delete/'.$row->id_bank_admin).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('bank', function ($row) {
                $bank = MBank::where('id_bank',$row->id_bank)->first();
                return $bank->nama_bank;
            })
            ->rawColumns(['action','bank'])
            ->addIndexColumn()
            ->toJson();
    }
}
