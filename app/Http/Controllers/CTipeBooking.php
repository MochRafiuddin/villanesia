<?php

namespace App\Http\Controllers;

use App\Models\MTipeBooking;
use App\Models\MBahasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CTipeBooking extends Controller
{
    public function index()
    {
        $bahasa = MBahasa::all();
        return view('tipe_booking.index')
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Booking');
    }
    public function create()
    {        
        $bahasa = MBahasa::where('is_default',1)->first();        
        $url = url('tipe-booking/create-save');
        return view('tipe_booking.form')
            ->with('data',null)
            ->with('id',null)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Booking')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $url = url('tipe-booking/create-save');
        return view('tipe_booking.form')
            ->with('data',null)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe booking')
            ->with('titlePage','Tambah')
            ->with('url',$url);
    }
    public function show($id)
    {        
        $data = MTipeBooking::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();
        $url = url('tipe-booking/show-save/'.$id);
        return view('tipe_booking.form')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Edit')
            ->with('url',$url);
    }
    public function detail($id)
    {        
        $data = MTipeBooking::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        return view('tipe_booking.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('bahasa',$bahasa)
            ->with('title','Tipe Properti')
            ->with('titlePage','Detail');
    }
    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_tipe_booking' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }        
        if ($request->id) {
            $tipe = new MTipeBooking();
            $tipe->nama_tipe_booking = $request->nama_tipe_booking;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->id_ref_bahasa = $request->id;
            $tipe->save();
        }else{
            $tipe = new MTipeBooking();
            $tipe->nama_tipe_booking = $request->nama_tipe_booking;            
            $tipe->id_bahasa = $request->id_bahasa;
            $tipe->save();
    
            MTipeBooking::where('id_tipe_booking',$tipe->id_tipe_booking)->update(['id_ref_bahasa' => $tipe->id_tipe_booking]);
        }

        return redirect()->route('tipe-booking-index')->with('msg','Sukses Menambahkan Data');
    }
    public function show_save(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_tipe_booking' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                        ->withInput($request->all())
                        ->withErrors($validator->errors());
        }
                
        MTipeBooking::where('id_tipe_booking',$request->id)->update(['nama_tipe_booking'=>$request->nama_tipe_booking]);                    

        return redirect()->route('tipe-booking-index')->with('msg','Sukses Menambahkan Data');
    }
    public function delete($id)
    {        
        $data = MTipeBooking::find($id);
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MTipeBooking::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);            
        }else{
            MTipeBooking::where('id_tipe_booking',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('tipe-booking-index')->with('msg','Sukses Menambahkan Data');

    }
    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MTipeBooking::withDeleted()->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';                
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_tipe_booking.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost"><span class="mdi mdi-adjust" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"></span></a>';
                $btn .= '<a href="'.url('tipe-booking/detail/'.$row->id_tipe_booking).'" class="text-warning mr-2"><span class="mdi mdi-information-outline" data-toggle="tooltip" data-placement="Top" title="Detail Data"></span></a>';                
                $btn .= '<a href="'.url('tipe-booking/show/'.$row->id_tipe_booking).'" class="text-danger mr-2"><span class="mdi mdi-pen" data-toggle="tooltip" data-placement="Top" title="Edit Data"></span></a>';                
                $btn .= '<a href="'.url('tipe-booking/delete/'.$row->id_tipe_booking).'" class="text-primary delete"><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
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
        $model = MTipeBooking::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->orderBy('id_tipe_booking', 'DESC')->first();
        // dd($model);
        if ($model) {
            $data = $model->id_tipe_booking;
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }
}
