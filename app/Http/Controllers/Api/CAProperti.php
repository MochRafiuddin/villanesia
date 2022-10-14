<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MProperti;
use App\Models\MPropertiKamarTidur;
use App\Models\MPropertiExtra;
use App\Models\MPropertiGalery;
use App\Models\MPropertiHargaPeriode;
use App\Models\MFasilitas;
use App\Models\MAmenities;
use App\Models\MReviewRating;
use App\Models\MBooking;
use App\Models\MapAmenities;
use App\Models\MapFasilitas;
use DateInterval;
use DatePeriod;
use DateTime;
use Carbon\Carbon;
use App\Traits\Helper;

class CAProperti extends Controller
{
    use Helper;
    public function get_property_type(Request $request)
    {
        $id_tipe = $request->id_tipe;
        $id_bahasa = $request->id_bahasa;
        $page = ($request->page-1)*6;
        $order_by = $request->order_by;

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa)
                ->where('id_tipe_properti',$id_tipe)
                ->limit(6)
                ->offset($page);
        if ($order_by == 1) {
            $tipe = $tipe->orderBy('harga_tampil','asc');
        }elseif ($order_by == 2) {
            $tipe = $tipe->orderBy('harga_tampil','desc');
        }elseif ($order_by == 3) {
            $tipe = $tipe->orderBy('nilai_rating','desc');
        }elseif ($order_by == 4) {
            $tipe = $tipe->orderByRaw('(total_amenities+total_fasilitas) desc');
        }elseif ($order_by == 5) {
            $tipe = $tipe->orderBy('created_date','desc');
        }else {
            $tipe = $tipe->orderBy('created_date','asc');
        }
        $data = $tipe->get();
        if (count($data)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
                'code' => 0,
            ], 400);
        }
    }
    public function get_property_detail(Request $request)
    {
        $id_properti = $request->id_properti;
        $id_bahasa = $request->id_bahasa;
        $tipe = MProperti::selectRaw('m_properti.*, m_jenis_tempat.nama_jenis_tempat, m_tipe_properti.nama_tipe_properti')
                ->join('m_jenis_tempat','m_properti.id_jenis_tempat','m_jenis_tempat.id_ref_bahasa')
                ->join('m_tipe_properti','m_properti.id_tipe_properti','m_tipe_properti.id_ref_bahasa')
                ->where('m_properti.id_ref_bahasa',$id_properti)
                ->where('m_properti.id_bahasa',$id_bahasa)
                ->where('m_properti.deleted',1)
                ->where('m_jenis_tempat.id_bahasa',$id_bahasa)
                ->where('m_jenis_tempat.deleted',1)
                ->where('m_tipe_properti.id_bahasa',$id_bahasa)
                ->where('m_tipe_properti.deleted',1)
                ->get();

        $fasilitas = MFasilitas::selectRaw('m_fasilitas.*')
                ->join('map_fasilitas','m_fasilitas.id_ref_bahasa','map_fasilitas.id_fasilitas')                
                ->where('m_fasilitas.id_bahasa',$id_bahasa)
                ->where('m_fasilitas.deleted',1)
                ->where('map_fasilitas.id_properti',$id_properti)
                ->orderBy('m_fasilitas.nama_fasilitas','asc')
                ->limit(3)
                ->get();

        $amenities = MAmenities::selectRaw('m_amenities.*')
                ->join('map_amenities','m_amenities.id_ref_bahasa','map_amenities.id_amenities')                
                ->where('m_amenities.id_bahasa',$id_bahasa)
                ->where('m_amenities.deleted',1)
                ->where('map_amenities.id_properti',$id_properti)
                ->orderBy('m_amenities.nama_amenities','asc')
                ->limit(3)
                ->get();

        $bedroom = MPropertiKamarTidur::where('id_properti',$id_properti)
                ->get();

        $review = MReviewRating::selectRaw('h_review_rating.*, CONCAT(m_customer.nama_depan,m_customer.nama_belakang) as nama_lengkap')
                ->join('m_users','m_users.id_user','h_review_rating.id_user')
                ->join('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.deleted',1)                
                ->where('h_review_rating.id_properti',$id_properti)
                ->orderBy('h_review_rating.created_date','desc')
                ->limit(3)
                ->get();

        $extra = MPropertiExtra::where('id_properti',$id_properti)
                ->get();

        $galery = MPropertiGalery::where('id_properti',$id_properti)
                ->orderBy('featured_image','desc')
                ->orderBy('id_properti_galery','asc')
                ->get();

        $custom = MPropertiHargaPeriode::where('id_properti',$id_properti)                
                ->orderBy('start_date','asc')
                ->get();

        $availability = MBooking::selectRaw('t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.id_status_booking, m_status_booking.nama_status_booking')
                ->join('m_status_booking','m_status_booking.id_status_booking','t_booking.id_status_booking')                
                ->where('t_booking.id_tipe',1)                
                ->where('t_booking.id_ref',$id_properti)                
                ->get();

        if (count($tipe)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'detail' => $tipe,
                'detail_bedroom' => $bedroom,
                'detail_facilities_amenities' => [
                    'facilities' => $fasilitas,
                    'amenities' => $amenities,
                ],
                'detail_properti_extra' => $extra,
                'detail_review' => $review,
                'detail_properti_galery' => $galery,
                'detail_seasonial_price' => $custom,
                'detail_availability' => $availability,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
                'code' => 0,
            ], 400);
        }
    }
    public function get_property(Request $request)
    {
        $id_kota = $request->id_kota;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $total_tamu = $request->total_tamu;
        $total_hewan = $request->total_hewan;
        $jumlah_kamar_tidur = $request->jumlah_kamar_tidur;
        $id_jenis_tempat = $request->id_jenis_tempat;
        $harga_minimal = $request->harga_minimal;
        $harga_maksimal = $request->harga_maksimal;
        $amenities = $request->amenities;
        $fasilitas = $request->fasilitas;
        $id_bahasa = $request->id_bahasa;

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan, latitude, longitude')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa);        

        if ($id_kota != null) {
            $tipe = $tipe->where('id_kota',$id_kota);
        }
        if ($total_tamu != null) {
            $tipe = $tipe->whereRaw('(jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) >= '.$total_tamu);
        }
        if ($total_hewan != null) {
            $tipe = $tipe->where('binatang',1);
        }
        if ($jumlah_kamar_tidur != null) {
            $tipe = $tipe->where('jumlah_kamar_tidur','>=',$jumlah_kamar_tidur);
        }
        if ($id_jenis_tempat != null) {
            $tipe = $tipe->where('id_jenis_tempat',$id_jenis_tempat);
        }
        if ($harga_minimal != null && $harga_maksimal != null) {
            $tipe = $tipe->whereBetween('harga_tampil', [$harga_minimal, $harga_maksimal]);
        }
        if ($amenities != null) {            
            $list_id_properti_ame = MapAmenities::selectRaw('id_properti')
                ->whereIn('id_amenities',$amenities)
                ->get()->toArray();
            $tipe = $tipe->whereIn('id_properti',$list_id_properti_ame);
        }
        if ($fasilitas != null) {
            $list_id_properti_fas = MapFasilitas::selectRaw('id_properti')
                ->whereIn('id_fasilitas',$fasilitas)
                ->get()->toArray();
            $tipe = $tipe->whereIn('id_properti',$list_id_properti_fas);
        }
        if ($fasilitas != null && $amenities != null) {
            $list_id_properti_fas = MapFasilitas::selectRaw('id_properti')
                ->whereIn('id_fasilitas',$amenities)
                ->get();
            $list_id_properti_ame = MapAmenities::selectRaw('id_properti')
                ->whereIn('id_amenities',$fasilitas)
                ->get();
            $a = [];
            $b = [];
            foreach ($list_id_properti_fas as $key) {
               $a[] = $key->id_properti;
            }
            foreach ($list_id_properti_ame as $key) {
               $b[] = $key->id_properti;
            }
            $variable = array_intersect($a,$b);
            // dd($variable);
            if (count($variable) > 0) {
                $tipe = $tipe->whereIn('id_properti',$variable);
            }else {
                $tipe = $tipe->whereIn('id_properti',['0']);
            }
        }
        if ($tanggal_mulai != null && $tanggal_selesai != null) {
            $cek_data_1 = MProperti::selectRaw('id_ref_bahasa')
                ->whereIn('id_tipe_booking',['1'])
                ->groupBy('id_ref_bahasa')->get()->toArray();

            $cek_data_2 = MProperti::selectRaw('id_ref_bahasa')
                ->whereIn('id_tipe_booking',['2','3','4'])
                ->groupBy('id_ref_bahasa')->get()->toArray();
            
            $arr_tampung_id_properti = [];
            if(!empty($cek_data_1)){
                $data1 = MBooking::selectRaw('id_ref')
                    ->where('tanggal_selesai','>',$request->tanggal_mulai)
                    ->where('tanggal_mulai','<=',$request->tanggal_selesai)
                    ->whereIn('id_ref',$cek_data_1)
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',1)
                    ->groupBy('id_ref')->get()->toArray();
                array_push($data1,$arr_tampung_id_properti);
			}
            if(!empty($cek_data_2)){
                $data2 = MBooking::selectRaw('id_ref')
                    ->where('tanggal_selesai','>',$request->tanggal_mulai)
                    ->where('tanggal_mulai','<=',$request->tanggal_selesai)
                    ->whereIn('id_ref',$cek_data_2)
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',1)
                    ->groupBy('id_ref')->get()->toArray();
                array_push($data2,$arr_tampung_id_properti);
			}

            if(!empty($arr_tampung_id_properti)){
                $tipe = $tipe->whereNotIn('id_ref_bahasa',$arr_tampung_id_properti);				
			}
        }
        $data = $tipe->get();

        if (count($data)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
                'code' => 0,
            ], 400);
        }
    }
    public function get_property_detail_harga(Request $request)
    {
        $id_properti = $request->id_properti;
        $id_tipe_booking = $request->id_tipe_booking;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $tamu_dewasa = $request->tamu_dewasa;
        $tamu_anak = $request->tamu_anak;
        $tamu_bayi = $request->tamu_bayi;
        $catatan = $request->catatan;
        $jam_mulai = $request->jam_mulai;
        $jam_selesai = $request->jam_selesai;
        $extra_service = $request->extra_service;
        
        $pro = MProperti::find($id_properti);
        $cus = MPropertiHargaPeriode::where('id_properti',$id_properti)->get();
        // dd($cus);

        if ($id_tipe_booking == 1) {
            if ($tanggal_selesai < $tanggal_mulai) {                
                return response()->json([
                    'success' => false,
                    'message' => 'Check out date must be greater then check in date',
                    'code' => 0,
                ], 400);
            }

            $mulai = date('Y-m-d', strtotime($tanggal_mulai.' -1 day'));
            $selesai = date('Y-m-d', strtotime($tanggal_selesai));
            $from = Carbon::parse($mulai);
            $to = Carbon::parse($selesai);
            $diff_in_hours = $to->diffInDays($from);
            
            $min = ($pro->min_durasi_inap != null ? $pro->min_durasi_inap : 1);
            $max = ($pro->max_durasi_inap != null ? $pro->max_durasi_inap : 1);
            // dd($min);

            if ($diff_in_hours < $min) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum days of a booking are '.$min,
                    'code' => 0,
                ], 400);
            }

            if ($diff_in_hours > $max) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum days of a booking are '.$max,
                    'code' => 0,
                ], 400);
            }

            $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',1)
                    ->where('id_ref',$id_properti)
                    ->first();
            if ($data->total == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your dates are not available',
                    'code' => 0,
                ], 400);
            }else {
                return response()->json([
                    'success' => true,
                    'message' => 'Your dates are available!',
                    'code' => 1,
                ], 400);
            }

        }elseif ($id_tipe_booking == 2 || $id_tipe_booking == 3 || $id_tipe_booking == 4) {
            if ($tanggal_selesai <= $tanggal_mulai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check out date must be greater then check in date',
                    'code' => 0,
                ], 400);
            }

            if ($id_tipe_booking == 2) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                
                $min = ($pro->min_durasi_inap != null ? $pro->min_durasi_inap : 1);
                $max = ($pro->max_durasi_inap != null ? $pro->max_durasi_inap : 1);
                
                if ($diff_in_hours < $min) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum days of a booking are '.$min,
                        'code' => 0,
                        'result' => []
                    ], 400);
                }

                if ($diff_in_hours > $max) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum days of a booking are '.$max,
                        'code' => 0,
                        'result' => []
                    ], 400);
                }

                $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',2)
                    ->where('id_ref',$id_properti)
                    ->first();

                $tamu_tambahan = ((($tamu_dewasa+$tamu_anak+$tamu_bayi) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak+$tamu_bayi) - $pro->jumlah_tamu));
                $result['durasi_inap'] = $diff_in_hours;
                $result['durasi_inap_jam'] = 0;
                $result['extra_day'] = 0;
                $result['tamu_dewasa'] = $tamu_dewasa;
                $result['tamu_anak'] = $tamu_anak;
                $result['tamu_bayi'] = $tamu_bayi;
                $result['tamu_maksimal'] = $pro->jumlah_tamu;
                $result['tamu_tambahan'] = $tamu_tambahan;
                $result['harga_tampil'] = $pro->harga_tampil;
                if ($pro->harga_weekend == null && count($cus)==0) {
                    $result['harga_final_properti'] = $pro->harga_tampil * $diff_in_hours;
                }elseif ($pro->harga_weekend != null && count($cus)==0) {
                    $period = new DatePeriod(
                        new DateTime($tanggal_mulai),
                        new DateInterval('P1D'),
                        new DateTime(date('Y-m-d', strtotime($tanggal_selesai.' -1 day')).' +1 days')
                    );
                    $final = $this->get_harga_by_input($period,$pro);
                    $result['harga_final_properti'] = $final;
                }elseif ($pro->harga_weekend == null && count($cus) > 0) {
                    $period = new DatePeriod(
                        new DateTime($tanggal_mulai),
                        new DateInterval('P1D'),
                        new DateTime(date('Y-m-d', strtotime($tanggal_selesai.' -1 day')).' +1 days')
                    );
                    $final=0;
                    foreach ($cus as $c) {
                        # code...
                        $period1 = new DatePeriod(
                            new DateTime($c->start_date),
                            new DateInterval('P1D'),
                            new DateTime(date('Y-m-d', strtotime($c->end_date)).' +1 days')
                        );                        
                        foreach ($period as $key) {
                            foreach ($period1 as $key1) {                                
                                if ($key->format('Y-m-d') == $key1->format('Y-m-d')) {
                                    if ($key->format('D')=='Sat') {
                                        $final = $final + $c->harga_weekend;
                                    }elseif ($key->format('D')=='Sun') {
                                        $final = $final + $c->harga_weekend;
                                    }else {
                                        $final = $final + $c->harga;
                                    }
                                }
                            }
                        }
                    }                    
                    $result['harga_final_properti'] = $final;
                }else{
                    $date1 = $this->get_date_by_input($tanggal_mulai,$tanggal_selesai);
                    $date2 = $this->get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai);
                    $date_week = array_diff($date1,$date2);
                    $date_cus = array_intersect($date1,$date2);
                    $har_week = $this->get_harga_by_input(array_values($date_week),$pro);
                    $har_cus = $this->get_harga_cus_by_input(array_values($date_cus),$cus);
                    // dd($har_week);
                    $result['harga_final_properti'] = $har_week + $har_cus;
                }
                if ($result['tamu_tambahan'] > 0) {
                    if ($pro->harga_weekend != null && count($cus) != 0) {
                        $date1 = $this->get_date_by_input($tanggal_mulai,$tanggal_selesai);
                        $date2 = $this->get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai);
                        $date_week = array_diff($date1,$date2);
                        $date_cus = array_intersect($date1,$date2);
                        if (count($date_week) == 0) {
                            
                        }else {
                            
                        }
                    }
                }
                if ($pro->biaya_kebersihan==2) {                    
                    $result['cleaning_fee'] = $pro->biaya_kebersihan;
                }else {
                    $result['cleaning_fee'] = $pro->biaya_kebersihan * $diff_in_hours;
                }
                $result['security_deposit'] = $pro->uang_jaminan;
                $result['total_extra_service'] = 0;
                $result['detail_extra_service'] = 0;
                $result['persen_pajak'] = $pro->pajak;
                
                if ($data->total > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your dates are not available',
                        'code' => 0,
                        'result' => []
                    ], 400);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your dates are available!',
                        'code' => 1,
                        'result' => $result
                    ], 400);
                }
            }

            if ($id_tipe_booking == 3) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                $week = floor($diff_in_hours / 7);
                // dd($week);
                $min = ($pro->min_durasi_inap != null ? $pro->min_durasi_inap : 1);
                $max = ($pro->max_durasi_inap != null ? $pro->max_durasi_inap : 1);

                if ($week < $min) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum weeks of a booking are '.$min,
                        'code' => 0,
                    ], 400);
                }

                if ($week > $max) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum weeks of a booking are '.$max,
                        'code' => 0,
                    ], 400);
                }

                $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',3)
                    ->where('id_ref',$id_properti)
                    ->first();

                if ($data->total == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your dates are not available',
                        'code' => 0,
                    ], 400);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your dates are available!',
                        'code' => 1,
                    ], 400);
                }
            }

            if ($id_tipe_booking == 4) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                $week = floor($diff_in_hours / 30);

                $min = ($pro->min_durasi_inap != null ? $pro->min_durasi_inap : 1);
                $max = ($pro->max_durasi_inap != null ? $pro->max_durasi_inap : 1);

                if ($week < $min) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum months of a booking are '.$min,
                        'code' => 0,
                    ], 400);
                }

                if ($week > $max) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum months of a booking are '.$max,
                        'code' => 0,
                    ], 400);
                }

                $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',4)
                    ->where('id_ref',$id_properti)
                    ->first();

                if ($data->total == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your dates are not available',
                        'code' => 0,
                    ], 400);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your dates are available!',
                        'code' => 1,
                    ], 400);
                }
            }

        }else {
            if ($jam_selesai <= $jam_mulai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check out hour must be greater then check in hour',
                    'code' => 0,
                ], 400);
            }
                $mulia = date('Y-m-d H:s:i', strtotime($tanggal_mulai.' '.$jam_mulai));
                $selesai = date('Y-m-d H:s:i', strtotime($tanggal_mulai.' '.$jam_selesai));
                $from = Carbon::parse($mulia);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInHours($from);
                // dd($diff_in_hours);
                $min = ($pro->min_durasi_inap != null ? $pro->min_durasi_inap : 1);
                $max = ($pro->max_durasi_inap != null ? $pro->max_durasi_inap : 1);

                if ($diff_in_hours < $min) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum hours of a booking are '.$min,
                        'code' => 0,
                    ], 400);
                }

                $data = MBooking::selectRaw('count(id_ref) as total')                    
                    ->where('tanggal_mulai',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('jam_selesai','>',$jam_mulai)
                    ->where('jam_mulai','<=',$jam_selesai)
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',5)
                    ->where('id_ref',$id_properti)
                    ->first();

                if ($data->total == 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your dates are not available',
                        'code' => 0,
                    ], 400);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Your dates are available!',
                        'code' => 1,
                    ], 400);
                }            
        }

        
    }
}
