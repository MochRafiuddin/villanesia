<?php

namespace App\Http\Controllers;

use App\Models\MProperti;
use App\Models\MPropertiHargaPeriode;
use App\Models\MPropertiExtra;
use App\Models\MPropertiKamarTidur;
use App\Models\MPropertiGalery;
use App\Models\MBahasa;
use App\Models\MTipeBooking;
use App\Models\MTipeProperti;
use App\Models\MJenisTempat;
use App\Models\MAkhirPekan;
use App\Models\MAmenities;
use App\Models\MapAmenities;
use App\Models\MFasilitas;
use App\Models\MapFasilitas;
use App\Models\MKota;
use App\Models\MProvinsi;
use App\Models\MNegara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\File;
use DataTables;

class CProperti extends Controller
{
    public function index()
    {        
        $bahasa = MBahasa::all();
        return view('Properti.index')    
            ->with('bahasa',$bahasa)
            ->with('title','Properti');
    }
    public function add()
    {   
        $bahasa = MBahasa::where('is_default',1)->first();        
        $tipe = MTipeBooking::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        return view('properti.add')
            ->with('title','Properti')
            ->with('tipe', $tipe)
            ->with('titlePage','Tambah');
    }

    public function addByTipe($id_tipe_booking)
    {   
        $bahasa = MBahasa::where('is_default',1)->first();        
        $tipe = MTipeProperti::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $pekan = MAkhirPekan::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $jenis = MJenisTempat::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $tipeId = MTipeBooking::where('id_tipe_booking',$id_tipe_booking)->first();
        $amenities = MAmenities::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $fasilitas = MFasilitas::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $kota = MKota::withDeleted()->get();
        $provinsi = MProvinsi::withDeleted()->get();
        $negara = MNegara::withDeleted()->get();
        $url = url('properti/create-save');
        return view('properti.add_pro')
            ->with('title','Properti')            
            ->with('tipe', $tipe)
            ->with('pekan', $pekan)
            ->with('jenis', $jenis)
            ->with('id',$id_tipe_booking)
            ->with('bahasa',$bahasa)
            ->with('url',$url)
            ->with('tipeId',$tipeId)
            ->with('amenities',$amenities)
            ->with('fasilitas',$fasilitas)
            ->with('kota',$kota)
            ->with('provinsi',$provinsi)
            ->with('negara',$negara)
            ->with('titlePage','Tambah');
    }

    public function create_save(Request $request)
    {   
        // dd($request);
        if ($request->id_properti) {
            $tipe = MProperti::find($request->id_properti);
            $this->insert_bahasa($tipe,$request);
            $tipe->update();        
        }else {
            $tipe = new MProperti();        
            $this->insert_bahasa($tipe,$request);
            $tipe->save();        
            MProperti::where('id_properti',$tipe->id_properti)->update(['id_ref_bahasa' => $tipe->id_properti]);
        }

        if ($request->nama_service) {
            if ($request->id_properti != null) {
                MPropertiExtra::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->nama_service); $i++) { 
                $extra = new MPropertiExtra;            
                $extra->id_properti = $tipe->id_properti;
                $extra->nama_service = $request['nama_service'][$i];
                $extra->harga = $request['harga_layanan'][$i];
                $extra->tipe = $request['tipe_layanan'][$i];
                $extra->save();
            }
        }

        if ($request->tanggal_mulai_periode) {
            if ($request->id_properti != null) {
                MPropertiHargaPeriode::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->tanggal_mulai_periode); $i++) {                                 
                $periode = new MPropertiHargaPeriode();
                $periode->id_properti = $tipe->id_properti;
                $periode->start_date = date('Y-m-d',strtotime($request['tanggal_mulai_periode'][$i]));
                $periode->end_date = date('Y-m-d',strtotime($request['tanggal_selesai_periode'][$i]));
                $periode->harga = $request['harga_periode'][$i];
                $periode->harga_tamu_tambahan = $request['harga_tamu_periode'][$i];
                $periode->harga_weekend = $request['akhir_pekan_periode'][$i];
                $periode->save();
            }
        }

        if ($request->featured_image) {
            if ($request->id_properti != null) {
                MPropertiGalery::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->featured_image); $i++) {                                 
                $amenities = new MPropertiGalery();
                $amenities->id_properti = $tipe->id_properti;                
                $amenities->featured_image = $request['featured_image'][$i];
                $amenities->nama_file = $request['nama_file'][$i];
                $amenities->id_tipe = $request['id_tipe'][$i];
                $amenities->save();

                if ($request['featured_image'][$i] == 1) {
                    $ti = MProperti::find($tipe->id_properti);
                    $ti->nama_file = $request['nama_file'][$i];
                    $ti->update();   
                }
            }
        }

        if ($request->amenities) {
            if ($request->id_properti != null) {
                MapAmenities::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->amenities); $i++) {                                 
                $amenities = new MapAmenities();
                $amenities->id_properti = $tipe->id_properti;                
                $amenities->id_amenities = $request['amenities'][$i];
                $amenities->save();
            }
        }

        if ($request->fasilitas) {
            if ($request->id_properti != null) {
                MapFasilitas::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->fasilitas); $i++) {                                 
                $fasilitas = new MapFasilitas();
                $fasilitas->id_properti = $tipe->id_properti;                
                $fasilitas->id_fasilitas = $request['fasilitas'][$i];
                $fasilitas->save();
            }
        }

        if ($request->nama_kamar_tidurs) {
            if ($request->id_properti != null) {
                MPropertiKamarTidur::where('id_properti',$request->id_properti)->delete();                
            }
            for ($i=0; $i < count($request->nama_kamar_tidurs); $i++) {                                 
                $kamar_tidurs = new MPropertiKamarTidur();
                $kamar_tidurs->id_properti = $tipe->id_properti;                
                $kamar_tidurs->nama_kamar_tidur = $request['nama_kamar_tidurs'][$i];
                $kamar_tidurs->jumlah_tamu = $request['jumlah_tamu_tidurs'][$i];
                $kamar_tidurs->jumlah_tempat_tidur = $request['jumlah_tempat_tidurs'][$i];
                $kamar_tidurs->jenis_tempat_tidur = $request['jenis_tempat_tidurs'][$i];
                $kamar_tidurs->save();
            }
        }

        return response()->json(['status'=>true,'error'=>'berhasil','msg'=>'Sukses Mengubah Data','id'=>$tipe->id_properti]);
        
        // return redirect()->route('properti-index')->with('msg','Sukses Menambahkan Data');
    }

    public function show($id)
    {
        $data = MProperti::find($id);        
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        $harga_periode = MPropertiHargaPeriode::where('id_properti',$data->id_ref_bahasa)->get();        
        $extra = MPropertiExtra::where('id_properti',$data->id_ref_bahasa)->get();
        $tidur = MPropertiKamarTidur::where('id_properti',$data->id_ref_bahasa)->get();
        $galery = MPropertiGalery::where('id_properti',$data->id_ref_bahasa)->get();
        $tipe = MTipeProperti::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $pekan = MAkhirPekan::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $jenis = MJenisTempat::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $amenities = MAmenities::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_a = MapAmenities::where('id_properti',$data->id_ref_bahasa)->get();
        $fasilitas = MFasilitas::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_f = MapFasilitas::where('id_properti',$data->id_ref_bahasa)->get();
        $tipeId = MTipeBooking::where('id_ref_bahasa',$data->id_tipe_booking)->where('id_bahasa',$data->id_bahasa)->first();
        $kota = MKota::withDeleted()->where('id_provinsi',$data->id_provinsi)->get();
        $provinsi = MProvinsi::withDeleted()->get();
        $negara = MNegara::withDeleted()->get();
        $url = url('properti/show-save/'.$id);
        return view('properti.edit')
            ->with('data',$data)
            ->with('id',$id)
            ->with('title','Properti')
            ->with('titlePage','Edit')
            ->with('tipe', $tipe)
            ->with('pekan', $pekan)
            ->with('jenis', $jenis)            
            ->with('bahasa',$bahasa)
            ->with('tipeId',$tipeId)
            ->with('amenities',$amenities)
            ->with('fasilitas',$fasilitas)
            ->with('harga_periode',$harga_periode)
            ->with('extra',$extra)
            ->with('map_a',$map_a)
            ->with('map_f',$map_f)
            ->with('tidur',$tidur)
            ->with('galery',$galery)
            ->with('kota',$kota)
            ->with('provinsi',$provinsi)
            ->with('negara',$negara)
            ->with('url',$url);
    }
    public function create_bahasa($id,$kode)
    {        
        $bahasa = MBahasa::where('id_bahasa',$kode)->first();        
        $harga_periode = MPropertiHargaPeriode::where('id_properti',$id)->get();        
        $extra = MPropertiExtra::where('id_properti',$id)->get();
        $tidur = MPropertiKamarTidur::where('id_properti',$id)->get();
        $galery = MPropertiGalery::where('id_properti',$id)->get();
        $tipe = MTipeProperti::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $pekan = MAkhirPekan::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $jenis = MJenisTempat::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $amenities = MAmenities::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_a = MapAmenities::where('id_properti',$id)->get();
        $fasilitas = MFasilitas::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_f = MapFasilitas::where('id_properti',$id)->get();
        $data = MProperti::find($id);        
        $tipeId = MTipeBooking::where('id_ref_bahasa',$data->id_tipe_booking)->where('id_bahasa',$data->id_bahasa)->first();
        $kota = MKota::withDeleted()->get();
        $provinsi = MProvinsi::withDeleted()->get();
        $negara = MNegara::withDeleted()->get();
        $url = url('properti/show-save/'.$id);
        return view('properti.edit')
            ->with('data',$data)
            ->with('id',null)
            ->with('title','Properti')
            ->with('titlePage','Edit')
            ->with('tipe', $tipe)
            ->with('pekan', $pekan)
            ->with('jenis', $jenis)            
            ->with('bahasa',$bahasa)
            ->with('tipeId',$tipeId)
            ->with('amenities',$amenities)
            ->with('fasilitas',$fasilitas)
            ->with('harga_periode',$harga_periode)
            ->with('extra',$extra)
            ->with('map_a',$map_a)
            ->with('map_f',$map_f)
            ->with('tidur',$tidur)
            ->with('galery',$galery)
            ->with('kota',$kota)
            ->with('provinsi',$provinsi)
            ->with('negara',$negara)
            ->with('url',$url);
    }
    public function show_save(Request $request)
    {
        $cek_pro = MProperti::where('id_ref_bahasa',$request->id_ref_bahasa)->where('id_bahasa',$request->id_bahasa)->where('deleted','!=',0)->first();
        // dd($cek_pro);
        if ($request->id_bahasa == 2) {
            if ($cek_pro == null) {
                $tipe = new MProperti();        
                $this->insert_bahasa($tipe,$request);
                $tipe->save();
                
                // Session::flash('msg','Data Berhasil Diperbarui'); 
                // return redirect()->to('/properti/show/'.$tipe->id_properti); 
            }
        }
        if ($request->id_properti !=null) {
            $id_pro = $request->id_properti;
        }else{
            $id_pro = $tipe->id_properti;
        }
        $data_s = [
            'deskripsi' => $request->deskripsi,
            'perlu_diketahui' => $request->perlu_diketahui,
            'setelah_label_harga' => $request->setelah_label_harga,
            'aturan_tambahan' => $request->aturan_tambahan,
            'kebijakan_pembatalan' => $request->kebijakan_pembatalan,
        ];
        MProperti::where('id_properti',$id_pro)->update($data_s);

        $data = [            
            'id_tipe_booking' => $request->id_tipe_booking,            
            'id_jenis_tempat' => $request->id_jenis_tempat,
            'judul' => $request->judul,            
            'id_tipe_properti' => $request->id_tipe_properti,
            'jumlah_kamar_tidur' => $request->jumlah_kamar_tidur,
            'jumlah_tamu' => $request->jumlah_tamu,
            'jumlah_tempat_tidur' => $request->jumlah_tempat_tidur,
            'jumlah_kamar_mandi' => $request->jumlah_kamar_mandi,
            'ukuran' => $request->ukuran,
            'satuan_ukuran' => $request->satuan_ukuran,
            'nama_properti' => $request->nama_properti,
            'sarapan' => $request->sarapan,
            'harga_tampil' => $request->harga_tampil,
            'harga_weekend' => $request->harga_weekend,
            'penerapan_harga_weekend' => $request->penerapan_harga_weekend,
            'harga_weekly' => $request->harga_weekly,
            'harga_monthly' => $request->harga_monthly,
            'tamu_tambahan' => $request->tamu_tambahan,
            'harga_tamu_tambahan' => $request->harga_tamu_tambahan,
            'jumlah_tamu_tambahan' => $request->jumlah_tamu_tambahan,
            'biaya_kebersihan' => $request->biaya_kebersihan,
            'biaya_kebersihan_tipe' => $request->biaya_kebersihan_tipe,
            'uang_jaminan' => $request->uang_jaminan,
            'pajak' => $request->pajak,
            'alamat' => $request->alamat,
            'apt_suite' => $request->apt_suite,
            'id_kota' => $request->id_kota,
            'id_provinsi' => $request->id_provinsi,
            'kode_pos' => $request->kode_pos,
            'area' => $request->area,
            'id_negara' => $request->id_negara,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'min_durasi_inap' => $request->min_durasi_inap,
            'max_durasi_inap' => $request->max_durasi_inap,
            'jam_checkin' => $request->jam_checkin,
            'jam_checkout' => $request->jam_checkout,
            'jam_operasional_mulai' => $request->jam_operasional_mulai,
            'jam_operasional_selesai' => $request->jam_operasional_selesai,
            'merokok' => $request->merokok,
            'binatang' => $request->binatang,
            'acara' => $request->acara,
            'anak' => $request->anak,
            'deleted' => 1,
        ];
        MProperti::where('id_ref_bahasa',$request->id_ref_bahasa)->where('deleted','!=',0)->update($data);

        MPropertiExtra::where('id_properti',$request->id_ref_bahasa)->delete();
        if ($request->nama_service) {
            for ($i=0; $i < count($request->nama_service); $i++) { 
                $extra = new MPropertiExtra;            
                $extra->id_properti = $request->id_ref_bahasa;
                $extra->nama_service = $request['nama_service'][$i];
                $extra->harga = $request['harga_layanan'][$i];
                $extra->tipe = $request['tipe_layanan'][$i];
                $extra->save();
            }
        }

        MPropertiGalery::where('id_properti',$request->id_ref_bahasa)->delete();
        if ($request->featured_image) {
            for ($i=0; $i < count($request->featured_image); $i++) {                                 
                $amenities = new MPropertiGalery();
                $amenities->id_properti = $request->id_ref_bahasa;                
                $amenities->featured_image = $request['featured_image'][$i];
                $amenities->nama_file = $request['nama_file'][$i];
                $amenities->id_tipe = $request['id_tipe'][$i];
                $amenities->save();

                if ($request['featured_image'][$i] == 1) {
                    MProperti::where('id_ref_bahasa',$request->id_ref_bahasa)->update(['nama_file'=>$request['nama_file'][$i]]);                    
                }
            }
        }

        MapAmenities::where('id_properti',$request->id_ref_bahasa)->delete();
        if ($request->amenities) {
            for ($i=0; $i < count($request->amenities); $i++) {                                 
                $amenities = new MapAmenities();
                $amenities->id_properti = $request->id_ref_bahasa;                
                $amenities->id_amenities = $request['amenities'][$i];
                $amenities->save();
            }
        }        
        
        MapFasilitas::where('id_properti',$request->id_ref_bahasa)->delete();
        if ($request->fasilitas) {
            for ($i=0; $i < count($request->fasilitas); $i++) {                                 
                $fasilitas = new MapFasilitas();
                $fasilitas->id_properti = $request->id_ref_bahasa;                
                $fasilitas->id_fasilitas = $request['fasilitas'][$i];
                $fasilitas->save();
            }
        }
        
        MPropertiKamarTidur::where('id_properti',$request->id_ref_bahasa)->delete();
        if ($request->nama_kamar_tidurs) {
            for ($i=0; $i < count($request->nama_kamar_tidurs); $i++) {                                 
                $kamar_tidurs = new MPropertiKamarTidur();
                $kamar_tidurs->id_properti = $request->id_ref_bahasa;                
                $kamar_tidurs->nama_kamar_tidur = $request['nama_kamar_tidurs'][$i];
                $kamar_tidurs->jumlah_tamu = $request['jumlah_tamu_tidurs'][$i];
                $kamar_tidurs->jumlah_tempat_tidur = $request['jumlah_tempat_tidurs'][$i];
                $kamar_tidurs->jenis_tempat_tidur = $request['jenis_tempat_tidurs'][$i];
                $kamar_tidurs->save();
            }
        }
        
        Session::flash('msg','Data Berhasil Diperbarui'); 

        return redirect()->to('/properti/show/'.$id_pro);
    }

    public function delete($id)
    {
        $data = MProperti::find($id);        
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        if ($bahasa->is_default==1) {
            MProperti::where('id_ref_bahasa',$data->id_ref_bahasa)->update(['deleted'=>0]);
            // MPropertiKamarTidur::where('id_properti',$id)->delete();
            // MapFasilitas::where('id_properti',$id)->delete();
            // MapAmenities::where('id_properti',$id)->delete();
            // MPropertiExtra::where('id_properti',$id)->delete();
        }else{
            MProperti::where('id_properti',$id)->update(['deleted'=>0]);
        }
        return redirect()->route('properti-index')->with('msg','Sukses Menambahkan Data');

    }

    public function detail($id)
    {   
        $data = MProperti::find($id);        
        $bahasa = MBahasa::where('id_bahasa',$data->id_bahasa)->first();        
        $harga_periode = MPropertiHargaPeriode::where('id_properti',$data->id_ref_bahasa)->get();        
        $extra = MPropertiExtra::where('id_properti',$data->id_ref_bahasa)->get();
        $tidur = MPropertiKamarTidur::where('id_properti',$data->id_ref_bahasa)->get();
        $galery = MPropertiGalery::where('id_properti',$data->id_ref_bahasa)->get();
        $tipe = MTipeProperti::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $pekan = MAkhirPekan::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $jenis = MJenisTempat::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $amenities = MAmenities::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_a = MapAmenities::where('id_properti',$data->id_ref_bahasa)->get();
        $fasilitas = MFasilitas::withDeleted()->where('id_bahasa',$bahasa->id_bahasa)->get();
        $map_f = MapFasilitas::where('id_properti',$data->id_ref_bahasa)->get();
        $tipeId = MTipeBooking::where('id_ref_bahasa',$data->id_tipe_booking)->where('id_bahasa',$data->id_bahasa)->first();
        $kota = MKota::withDeleted()->get();
        $provinsi = MProvinsi::withDeleted()->get();
        $negara = MNegara::withDeleted()->get();
        $url = url('properti/show-save/'.$id);
        return view('properti.detail')
            ->with('data',$data)
            ->with('id',$id)
            ->with('title','Properti')
            ->with('titlePage','Detail')
            ->with('tipe', $tipe)
            ->with('pekan', $pekan)
            ->with('jenis', $jenis)            
            ->with('bahasa',$bahasa)
            ->with('tipeId',$tipeId)
            ->with('amenities',$amenities)
            ->with('fasilitas',$fasilitas)
            ->with('harga_periode',$harga_periode)
            ->with('extra',$extra)
            ->with('map_a',$map_a)
            ->with('map_f',$map_f)
            ->with('tidur',$tidur)
            ->with('galery',$galery)
            ->with('kota',$kota)
            ->with('provinsi',$provinsi)
            ->with('negara',$negara)
            ->with('url',$url);
    }

    public function data()
    {
        $bahasa = (!empty($_GET["bahasa"])) ? ($_GET["bahasa"]) : (0);
        $model = MProperti::where('deleted','!=',0)->where('id_bahasa',$bahasa)->orderBy('id_ref_bahasa');
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                $btn = '';      
                $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-id="'.$row->id_properti.'" data-id_ref="'.$row->id_ref_bahasa.'" data-original-title="Edit" class="mr-2 text-success editPost" data-toggle="tooltip" data-placement="Top" title="Ganti Bahasa"><span class="mdi mdi-adjust"></span></a>';
                $btn .= '<a href="'.url('properti/show/'.$row->id_properti).'" class="text-danger mr-2" data-toggle="tooltip" data-placement="Top" title="Edit Data"><span class="mdi mdi-pen"></span></a>';                
                $btn .= '<a href="'.url('properti/detail/'.$row->id_properti).'" class="text-warning mr-2" data-toggle="tooltip" data-placement="Top" title="Detail Data"><span class="mdi mdi-information-outline"></span></a>';                
                $btn .= '<a href="'.url('properti/delete/'.$row->id_properti).'" class="text-primary delete" ><span class="mdi mdi-delete" data-toggle="tooltip" data-placement="Top" title="Hapus Data"></span></a>';
                return $btn;
            })
            ->addColumn('bahasa', function ($row) {                                
                $bahasa = MBahasa::where('id_bahasa',$row->id_bahasa)->first();
                $btn = '<div style="white-space:nowrap;"> <i class="flag-icon '.$bahasa->logo.'"></i> '.$bahasa->nama_bahasa.'</div>';                
                return $btn;
            })  
            ->rawColumns(['action','bahasa'])
            ->addIndexColumn()
            ->toJson();
    }

    public function bahasa(Request $request)
    {   
        $bahasa = MBahasa::where('id_bahasa',$request->kode)->first();
        $model = MProperti::where('id_ref_bahasa',$request->id_ref)->where('id_bahasa',$bahasa->id_bahasa)->where('deleted','!=',0)->first();
        // dd($model);
        if ($model) {
            $data = $model->id_properti;            
        }else{
            $data = 'tambah';            
        }
        return response()->json($data);
    }

    public function periode_save(Request $request)
    {                          
        $tipe = new MPropertiHargaPeriode();
        $tipe->id_properti = $request->id;
        $tipe->start_date = date('Y-m-d',strtotime($request->tanggal_mulai_periode));
        $tipe->end_date = date('Y-m-d',strtotime($request->tanggal_selesai_periode));
        $tipe->harga = $request->harga_periode;
        $tipe->harga_tamu_tambahan = $request->harga_tamu_periode;
        $tipe->harga_weekend = $request->akhir_pekan_periode;
        $tipe->save();        

        $data['id_periode'] = $tipe->id_properti_periode_cus;
        $data['start_date'] = date('Y-m-d',strtotime($request->tanggal_mulai_periode));
        $data['end_date'] = date('Y-m-d',strtotime($request->tanggal_selesai_periode));
        $data['harga'] = $request->harga_periode;
        $data['harga_tamu_tambahan'] = $request->harga_tamu_periode;
        $data['harga_weekend'] = $request->akhir_pekan_periode;
        return response()->json($data);       
    }

    public function periode_delete(Request $request)
    {
        MPropertiHargaPeriode::where('id_properti_periode_cus',$request->id)->delete();
        return response()->json(['success'=>'User saved successfully.']);
    }

    public function insert_bahasa($tipe,$request){            
        $tipe->id_tipe_booking = $request->id_tipe_booking;
        $tipe->id_ref_bahasa = $request->id_ref_bahasa;
        $tipe->id_bahasa = $request->id_bahasa;
        $tipe->id_jenis_tempat = $request->id_jenis_tempat;
        $tipe->judul = $request->judul;
        $tipe->deskripsi = $request->deskripsi;
        $tipe->perlu_diketahui = $request->perlu_diketahui;
        $tipe->id_tipe_properti = $request->id_tipe_properti;
        $tipe->jumlah_kamar_tidur = $request->jumlah_kamar_tidur;
        $tipe->jumlah_tamu = $request->jumlah_tamu;
        $tipe->jumlah_tempat_tidur = $request->jumlah_tempat_tidur;
        $tipe->jumlah_kamar_mandi = $request->jumlah_kamar_mandi;
        $tipe->ukuran = $request->ukuran;
        $tipe->satuan_ukuran = $request->satuan_ukuran;
        $tipe->nama_properti = $request->nama_properti;
        $tipe->sarapan = $request->sarapan;
        $tipe->harga_tampil = $request->harga_tampil;
        $tipe->setelah_label_harga = $request->setelah_label_harga;
        $tipe->harga_weekend = $request->harga_weekend;
        $tipe->penerapan_harga_weekend = $request->penerapan_harga_weekend;
        $tipe->harga_weekly = $request->harga_weekly;
        $tipe->harga_monthly = $request->harga_monthly;
        $tipe->tamu_tambahan = $request->tamu_tambahan;
        $tipe->harga_tamu_tambahan = $request->harga_tamu_tambahan;
        $tipe->jumlah_tamu_tambahan = $request->jumlah_tamu_tambahan;
        $tipe->biaya_kebersihan = $request->biaya_kebersihan;
        $tipe->biaya_kebersihan_tipe = $request->biaya_kebersihan_tipe;
        $tipe->uang_jaminan = $request->uang_jaminan;
        $tipe->pajak = $request->pajak;
        $tipe->alamat = $request->alamat;
        $tipe->apt_suite = $request->apt_suite;
        $tipe->id_kota = $request->id_kota;
        $tipe->id_provinsi = $request->id_provinsi;
        $tipe->kode_pos = $request->kode_pos;
        $tipe->area = $request->area;
        $tipe->id_negara = $request->id_negara;
        $tipe->latitude = $request->latitude;
        $tipe->longitude = $request->longitude;
        $tipe->kebijakan_pembatalan = $request->kebijakan_pembatalan;
        $tipe->min_durasi_inap = $request->min_durasi_inap;
        $tipe->max_durasi_inap = $request->max_durasi_inap;
        $tipe->jam_checkin = $request->jam_checkin;
        $tipe->jam_checkout = $request->jam_checkout;
        $tipe->jam_operasional_mulai = $request->jam_operasional_mulai;
        $tipe->jam_operasional_selesai = $request->jam_oprasional_selesai;
        $tipe->merokok = $request->merokok;
        $tipe->binatang = $request->binatang;
        $tipe->acara = $request->acara;
        $tipe->anak = $request->anak;
        $tipe->aturan_tambahan = $request->aturan_tambahan;
        $tipe->deleted = $request->deleted;        
    }
    public function upload(Request $request)
    {   
        if($request->TotalFiles > 0)
        {
               
           for ($x = 0; $x < $request->TotalFiles; $x++) 
           {
               if ($request->hasFile('files'.$x)) 
                {
                    $file = $request->file('files'.$x);
                    $name = round(microtime(true) * 1000).'.'.$file->extension();                    
                    $file->move(public_path('upload/properti'), $name);
                    $insert[] = $name;
                }
           }            
        //    dd($insert);
            return response()->json(['success'=>'Multiple FIle has been uploaded using ajax into db and storage directory','gambar'=>$insert]);
        }
        else
        {
           return response()->json(["message" => "Please try again."]);
        }
                
    }
    public function delete_img($img)
    {   
        // dd($img);
        $destinationPath = 'upload/properti';        
        File::delete(public_path($destinationPath.'/'.$img));
        // unlink($destinationPath.'/'.$img);
        return response()->json(['success'=>'success']);        
    }
    public function kotaByProvinsi()
    {
        $provinsi = (!empty($_GET["provinsi"])) ? ($_GET["provinsi"]) : ('');
        // dd($departement);
        $kota = MKota::withDeleted()->where('id_provinsi',$provinsi)->get();        
        $data ="<option value='' selected disabled>Pilih</option>";
        foreach ($kota as $key) {
            $data .="<option value='".$key->id_kota."' >".$key->nama_kota."</option>";
        }
        return response()->json(['status'=>true,'data'=>$data]);
    }
    
}
