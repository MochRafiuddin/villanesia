<?php

namespace App\Http\Controllers;

use App\Models\MAds;
use App\Models\MAdsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Image;

class CAds extends Controller
{
    public function index()
    {        
        return view('ads.index')            
            ->with('title','Ads');
    }
    public function create()
    {        
        $url = url('ads/create-save');
        return view('ads.form')
            ->with('data',null)
            ->with('title','Ads')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }    
    public function show($id)
    {
        $data = MAds::find($id);        
        $url = url('ads/show-save/'.$id);
        return view('ads.form')
            ->with('data',$data)
            ->with('title','Ads')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {   
        $data = MAds::find($id);
        return view('ads.detail')
            ->with('data',$data)
            ->with('title','Ads')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_ads' => 'required',            
            'tipe_konten_ads' => 'required',
            'redirect_url_ads' => 'required',
            'status' => 'required'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }

        if ($request->file('konten_ads')) {
            // $gambar = round(microtime(true) * 1000).'.'.$request->file('konten_ads')->extension();
            // $request->file('konten_ads')->move(public_path('upload/ads'), $gambar);

            $image     = $request->file('konten_ads');
            $gambar    = round(microtime(true) * 1000).'.'.$request->file('konten_ads')->extension();            

            $image_resize = Image::make($image->getRealPath());
            if ($request->posisi==1) {                
                $image_resize->resize(200, 400);
            }else {
                $image_resize->resize(300, 600);
            }
            $image_resize->save(public_path('upload/ads/' .$gambar));
        }else{
            $gambar ="";
        }
        
        $tipe = new MAds();
        $tipe->nama_ads = $request->nama_ads;
        $tipe->konten_ads = $gambar;
        $tipe->tipe_konten_ads = $request->tipe_konten_ads;
        $tipe->redirect_url_ads = $request->redirect_url_ads;
        $tipe->status = $request->status;
        $tipe->posisi = $request->posisi;
        $tipe->save();        

        return redirect()->route('ads-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_ads' => 'required',
            'konten_ads' => 'required',
            'tipe_konten_ads' => 'required',
            'redirect_url_ads' => 'required',
            'status' => 'required'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
        if ($request->file('konten_ads')) {
            // $gambar = round(microtime(true) * 1000).'.'.$request->file('konten_ads')->extension();
            // $request->file('konten_ads')->move(public_path('upload/ads'), $gambar);

            $image     = $request->file('konten_ads');
            $gambar    = round(microtime(true) * 1000).'.'.$request->file('konten_ads')->extension();            

            $image_resize = Image::make($image->getRealPath());
            if ($request->posisi==1) {                
                $image_resize->resize(200, 400);
            }else {
                $image_resize->resize(300, 600);
            }
            $image_resize->save(public_path('upload/ads/' .$gambar));
        }else{
            $gambar ="";
        }
        $data = [
            'nama_ads' => $request->nama_ads,
            'konten_ads' => $gambar,
            'tipe_konten_ads' => $request->tipe_konten_ads,
            'redirect_url_ads' => $request->redirect_url_ads,
            'status' => $request->status,
            'posisi' => $request->posisi,
        ];
        MAds::where('id_ads',$request->id)->update($data);

        return redirect()->route('ads-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {
        MAds::updateDeleted($id);
        return redirect()->route('ads-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $model = MAds::withDeleted();
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                     
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_ads.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-settings" data-toggle="tooltip" data-placement="Top" title="Setting Ads"></span></a>';
                $btn .= '<a href="'.url('ads/detail/'.$row->id_ads).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('ads/show/'.$row->id_ads).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('ads/delete/'.$row->id_ads).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->editColumn('tipe_konten_ads', function ($row) {
                if ($row->tipe_konten_ads == 1) {
                    $html = "Image / GIF";
                }else {
                    $html = "Video";
                }
                return $html;
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 0) {
                    $html = "Pending";
                }else if ($row->status == 1){
                    $html = "Show (running)";
                }else if ($row->status == 2){
                    $html = "Complete";
                }else{
                    $html = "Banned";
                }
                return $html;
            })            
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->toJson();
    }

    public function setting(Request $request)
    {           
        $model = MAdsDetail::where('id_ads',$request->id)->first();
        if ($model) {
            $data['tgl_mulai'] = date('d-m-Y',strtotime($model->tanggal_mulai));
            $data['tgl_selesai'] = date('d-m-Y',strtotime($model->tanggal_selesai));
            $data['jam_mulai'] = $model->jam_mulai;
            $data['jam_selesai'] = $model->jam_selesai;
        }else{
            $data['tgl_mulai'] = '';
            $data['tgl_selesai'] = '';
            $data['jam_mulai'] = '';
            $data['jam_selesai'] = '';
        }
        return response()->json($data);
    }

    public function setting_save(Request $request)
    {          
        MAdsDetail::where('id_ads',$request->id)->delete();
        
        $tipe = new MAdsDetail();
        $tipe->id_ads = $request->id;
        $tipe->tanggal_mulai = date('Y-m-d',strtotime($request->tgl_mulai));
        $tipe->tanggal_selesai = date('Y-m-d',strtotime($request->tgl_selesai));
        $tipe->jam_mulai = $request->jam_mulai;
        $tipe->jam_selesai = $request->jam_selesai;
        $tipe->save();        

        return response()->json(['success'=>'User saved successfully.']);
        
    }
}
