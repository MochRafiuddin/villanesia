<?php

namespace App\Http\Controllers;

use App\Models\MRole;
use App\Models\MMenu;
use App\Models\MapRoleMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CRole extends Controller
{
    public function index()
    {   
        $menu = MMenu::all();     
        return view('role.index')            
            ->with('menu',$menu)
            ->with('title','Role');
    }
    public function create()
    {                   
        $url = url('role/create-save');
        return view('role.form')
            ->with('data',null)            
            ->with('title','Role')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {        
        $data = MRole::find($id);        
        $url = url('role/show-save/'.$id);
        return view('role.form')
            ->with('data',$data)
            ->with('title','Role')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MRole::find($id);
        return view('role.detail')
            ->with('data',$data)
            ->with('title','Role')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_role' => 'required',             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = new MRole();
        $tipe->nama_role = $request->nama_role;            
        $tipe->save();        

        return redirect()->route('role-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_role' => 'required',             
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        MRole::where('id_role',$request->id)->update(['nama_role'=>$request->nama_role]);                    

        return redirect()->route('role-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MRole::updateDeleted($id);        
        return redirect()->route('role-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MRole::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_role.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-account-key" data-toggle="tooltip" data-placement="Top" title="Set Menu"></span></a>';
                $btn .= '<a href="'.url('role/detail/'.$row->id_role).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('role/show/'.$row->id_role).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('role/delete/'.$row->id_role).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
    public function save_menu(Request $request)
    {    
        MapRoleMenu::where('id_role',$request->id_role)->delete();
        for ($i=0; $i < count($request->menu) ; $i++) { 
            $menu = new MapRoleMenu();
            $menu->id_role = $request->id_role;
            $menu->id_menu = $request->menu[$i];
            $menu->save();
        }
        return response()->json(['status' => true, 'msg' => 'Your dates available']);
    }
    public function get_menu(Request $request)
    {            
        $menu = MapRoleMenu::where('id_role',$request->id_role)->get();
        return response()->json(['status' => true, 'data' => $menu]);
    }
}
