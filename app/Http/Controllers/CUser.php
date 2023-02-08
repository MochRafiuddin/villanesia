<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use DataTables;

class CUser extends Controller
{
    public function index()
    {
         return view('user.index')->with('title','User');
    }
    public function create()
    {                   
        $url = url('user/create-save');
        return view('user.form')
            ->with('data',null)            
            ->with('title','User')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = User::find($id);        
        $url = url('user/show-save/'.$id);
        return view('user.form')
            ->with('data',$data)
            ->with('title','User')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function profile()
    {        
        $cus = MCustomer::find(Auth::user()->id_ref);
        if ($cus) {
            $data = $cus;
        }else {
            $data = null;
        }
        $url = url('user/profile-save/'.Auth::user()->id_ref);
        return view('user.profile')
            ->with('data',$data)
            ->with('title','Profile')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = User::find($id);        
        $url = url('user/show-save/'.$id);
        return view('user.detail')
            ->with('data',$data)
            ->with('title','User')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required',
            'email' => 'required',
            'no_telfon' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }

        $cek_user_username = User::where('deleted',1)
                ->where('username',$request->username)                
                ->get();
        $cek_user_email = User::where('deleted',1)
                ->where('email',$request->email)
                ->get();
        if (count($cek_user_username)>0 && count($cek_user_email)>0) {
           return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['username' => 'username and email is already used, please enter new username and email','email' => 'username and email is already used, please enter new username and email']); 
        }elseif (count($cek_user_username)>0) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['username' => 'username is already used, please enter new username']);
        }elseif (count($cek_user_email)>0) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['email' => 'email is already used, please enter new email']);
        }

        $cus = new MCustomer();
        $cus->nama_depan = substr($request->email,0,6);;
        $cus->save();
        
        $tipe = new User();
        $tipe->username = $request->username;
        $tipe->password = Hash::make($request->password);
        $tipe->email = $request->email;
        $tipe->no_telfon = $request->no_telfon;
        $tipe->tipe_user = 1;
        $tipe->id_ref = $cus->id;
        $tipe->save();        

        return redirect()->route('user-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',            
            'email' => 'required',
            'no_telfon' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }                
        
        $tipe = User::find($request->id);
        $tipe->username = $request->username;        
        $tipe->email = $request->email;
        $tipe->no_telfon = $request->no_telfon;
        if ($request->password != null) {
            $tipe->password = Hash::make($request->password);
        }
        $tipe->update();        

        return redirect()->route('user-index')->with('msg','Sukses Mengubah Data');
    }
    public function profile_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_depan' => 'required',            
            'nama_belakang' => 'required',
            'alamat' => 'required',
            'nama_kota' => 'required',
            'nama_provinsi' => 'required',
            'kode_pos' => 'required',
            'tentang' => 'required',
            'jenis_kelamin' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        
        if ($request->file('nama_foto')) {
            $gambar = round(microtime(true) * 1000).'.'.$request->file('nama_foto')->extension();
            $request->file('nama_foto')->move(public_path('upload/profile_img'), $gambar);
        }else{
            $gambar = null;
        }

        // dd(json_encode($request->notelpon));
        $tipe = MCustomer::find($request->id);
        $tipe->nama_depan = $request->nama_depan;
        $tipe->nama_belakang = $request->nama_belakang;
        $tipe->alamat = $request->alamat;
        $tipe->nama_kota = $request->nama_kota;
        $tipe->nama_provinsi = $request->nama_provinsi;
        $tipe->kode_pos = $request->kode_pos;
        $tipe->tentang = $request->tentang;
        $tipe->jenis_kelamin = $request->jenis_kelamin;
        $tipe->no_telfon_lain = json_encode($request->notelpon);
        $tipe->nama_foto = $gambar;
        $tipe->update();        

        return redirect()->route('profile-index')->with('msg','Sukses Mengubah Data');
    }
    public function data()
    {
        $model = User::withDeleted()->where('tipe_user',1);
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';       
                $btn .= '<a href="javascript:void(0)" data-toggle="modal"  data-id="'.$row->id_user.'" data-original-title="Password" class="text-success editPass mr-2"><span class="mdi mdi-lock-reset" data-toggle="tooltip" data-placement="Top" title="Reset Password"></span></a>';
                $btn .= '<a href="'.url('user/detail/'.$row->id_user).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('user/show/'.$row->id_user).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('user/delete/'.$row->id_user).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }
    public function delete($id)
    {
        $m = User::find($id);
        $m->deleted = 0;
        $m->save();
        return redirect()->route('user-index')->with('msg','Sukses Menambahkan Data');
    }
    public function reset_password(Request $request)
    {   
        // echo $id;
        $user = User::find($request->id_user);
        $user->password = Hash::make($request->password);
        $user->update();
        return response()->json(['success'=>'Sukses Update Data']);
    }
    public function ubah_password(Request $request)
    {           
        $password_lama = $request->password_lama;
        $password_baru = $request->password_baru;

        $data = User::where('id_user',Auth::user()->id_user)->first();
        $cek = Hash::check($password_lama, $data->password);

        if ($cek == false) {
            return response()->json(['status'=>0, 'msg'=>'Password Lama Salah !']);
        }

        $user = User::find(Auth::user()->id_user);
        $user->password = Hash::make($password_baru);
        $user->update();

        return response()->json(['status'=>1, 'msg'=>'Sukses Ubah Password']);
    }
}
