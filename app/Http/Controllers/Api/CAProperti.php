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
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingExtra;
use App\Models\MBookingPropertiExtra;
use App\Models\MBookingDiscount;
use App\Models\MapAmenities;
use App\Models\MapFasilitas;
use App\Models\MApiKey;
use App\Models\MKota;
use App\Models\HReviewRating;
use App\Models\TKonfirmasiBayar;
use App\Models\MKupon;
use App\Models\HPesan;
use App\Models\HPesanDetail;
use App\Models\User;
use App\Models\HReviewRatingLike;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Carbon\Carbon;
use App\Traits\Helper;
use App\Services\Firestore;
use Google\Cloud\Firestore\Timestamp;

class CAProperti extends Controller
{
    use Helper;
    public function get_property_type(Request $request)
    {
        $id_tipe = $request->id_tipe;
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $order_by = $request->order_by;

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan, nama_file')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa)
                ->where('id_tipe_properti',$id_tipe)
                ->limit($limit)
                ->offset($page);

        $get_total_all_data = MProperti::selectRaw('id_properti')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa)
                ->where('id_tipe_properti',$id_tipe);

        if ($order_by == 1) {
            $tipe = $tipe->orderBy('harga_tampil','asc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','asc');
        }elseif ($order_by == 2) {
            $tipe = $tipe->orderBy('harga_tampil','desc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','desc');
        }elseif ($order_by == 3) {
            $tipe = $tipe->orderBy('nilai_rating','desc');
            $get_total_all_data = $get_total_all_data->orderBy('nilai_rating','desc');
        }elseif ($order_by == 4) {
            $tipe = $tipe->orderByRaw('(total_amenities+total_fasilitas) desc');
            $get_total_all_data = $get_total_all_data->orderByRaw('(total_amenities+total_fasilitas) desc');
        }elseif ($order_by == 5) {
            $tipe = $tipe->orderBy('created_date','desc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','desc');
        }else {
            $tipe = $tipe->orderBy('created_date','asc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','asc');
        }
        $data = $tipe->get();
        $get_total_all_data = $get_total_all_data->get()->count();
        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
        if (count($data)>0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'data' => $data,
                'total_page' => $total_page,
                'total_all_data' => $get_total_all_data
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

        $review = MReviewRating::selectRaw('h_review_rating.*, CONCAT(m_customer.nama_depan,m_customer.nama_belakang) as nama_lengkap, m_customer.nama_foto')
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

        $date_now = date('Y-m-d');
        $availability = MBooking::selectRaw('t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.id_status_booking, m_status_booking.nama_status_booking')
                ->join('m_status_booking','m_status_booking.id_ref_bahasa','t_booking.id_status_booking')                
                ->where('t_booking.id_tipe',1)
                ->where('m_status_booking.id_bahasa',$id_bahasa)
                ->where('t_booking.id_ref',$id_properti)
                ->whereRaw('(t_booking.tanggal_mulai >= "'.$date_now.'" or t_booking.tanggal_selesai >= "'.$date_now.'") and t_booking.id_status_booking IN (1,2,5)')
                ->orderBy('t_booking.tanggal_mulai','asc')
                ->get();

        if (count($tipe)>0) {
            $tam_deskripsi = $tipe->first()->deskripsi;
            $short_deskripsi = substr(strip_tags($tam_deskripsi), 0, 300);

            $tam_tipe_booking = $tipe->first()->id_tipe_booking;

            $arr_date = array();

            foreach($availability as $st){

                $begin = new DateTime($st->tanggal_mulai);
                $end = new DateTime($st->tanggal_selesai);
                //$end->modify('next friday'); // 2016-07-08
                if($tam_tipe_booking == "1"){
                    $end->setTime(0,0,1);     // new line
                }

                $interval = new DateInterval('P1D');
                $daterange = new DatePeriod($begin, $interval, $end);

                foreach($daterange as $date) {
                    $tampung_hari = $date->format('Y-m-d');
                    //echo $date->format('Y-m-d')."<br />";
                    if(!in_array($tampung_hari, $arr_date)){
                        array_push($arr_date, $tampung_hari);
                    }
                }

            }

            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'detail' => $tipe,
                'detail_short_description' => $short_deskripsi,
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
                'detail_disable_date' => $arr_date
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
        $nama_properti = $request->nama_properti;
        // dd($amenities);

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan, latitude, longitude, nama_file')
                ->where('deleted',1)
                ->where('id_bahasa',$id_bahasa);        
        if ($nama_properti != null) {
            $tipe = $tipe->whereRaw('LOWER(judul) LIKE "%'.$nama_properti.'%"');
        }
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
                ->whereIn('id_fasilitas',$fasilitas)
                ->get();
            $list_id_properti_ame = MapAmenities::selectRaw('id_properti')
                ->whereIn('id_amenities',$amenities)
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
                    'result' => []
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
                    'result' => []
                ], 400);
            }

            // if ($diff_in_hours > $max) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Maximum days of a booking are '.$max,
            //         'code' => 0,
            //         'result' => []
            //     ], 400);
            // }

            $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',1)
                    ->where('id_ref',$id_properti)
                    ->first();
            
            $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
            $result['durasi_inap'] = $diff_in_hours;
            $result['durasi_inap_jam'] = 0;
            $result['extra_day'] = 0;
            $result['tamu_dewasa'] = $tamu_dewasa;
            $result['tamu_anak'] = $tamu_anak;
            $result['tamu_bayi'] = $tamu_bayi;
            $result['tamu_maksimal'] = $pro->jumlah_tamu;
            $result['tamu_tambahan'] = $tamu_tambahan;
            $result['harga_tampil'] = $pro->harga_tampil;

            if ($pro->harga_weekly != null && $pro->harga_monthly != null) {                
                if ($diff_in_hours > 30) {
                    $result['harga_final_properti'] = $pro->harga_monthly * $diff_in_hours;
                }elseif ($diff_in_hours > 7 && $diff_in_hours <= 30) {
                    $result['harga_final_properti'] = $pro->harga_weekly * $diff_in_hours;
                }else {
                    $result['harga_final_properti'] = $pro->harga_tampil * $diff_in_hours;
                }
            }else {
                $result['harga_final_properti'] = $pro->harga_tampil * $diff_in_hours;
            }
            // dd($pro->harga_tamu_tambahan);
            if ($tamu_tambahan > 0) {
                // $result['harga_tamu_tambahan'] = $tamu_tambahan * $diff_in_hours * $pro->harga_tamu_tambahan;
                $result['harga_tamu_tambahan'] = $tamu_tambahan * $diff_in_hours * $pro->harga_tampil;
            }else {
                $result['harga_tamu_tambahan'] = 0;
            }
            if ($pro->biaya_kebersihan_tipe==2) {                    
                $result['cleaning_fee'] = $pro->biaya_kebersihan;
            }else {
                $result['cleaning_fee'] = $pro->biaya_kebersihan * $diff_in_hours;
            }
            $result['security_deposit'] = $pro->uang_jaminan;
            if ($extra_service != null) {
                $extra = explode(",",$extra_service);
                $sevice = 0;
                for ($i=0; $i < count($extra); $i++) {
                    $ser = MPropertiExtra::find($extra[$i]);
                    if ($ser->tipe == 1) {
                        $sevice = $sevice + $ser->harga;
                    }elseif ($ser->tipe == 2) {
                        $sevice = $sevice + ($ser->harga * $diff_in_hours);
                    }elseif ($ser->tipe == 3) {
                        $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak));
                    }else {
                        $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak) * $diff_in_hours);
                    }
                }
                $result['total_extra_service'] = $sevice;
            }else {
                $result['total_extra_service'] = 0;
            }
            if ($extra_service != null) {
                $extra = explode(",",$extra_service);
                $res = [];
                for ($i=0; $i < count($extra); $i++) {
                    $ser = MPropertiExtra::find($extra[$i]);
                    $res[] = $ser->nama_service;
                }
                $result['detail_extra_service'] = $res;
            }else{
                $result['detail_extra_service'] = [];
            }
            $result['persen_pajak'] = $pro->pajak;
            $result['nominal_pajak'] = ($result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service']) * $result['persen_pajak'] /100;
            $result['harga_total'] = $result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service'] + $result['security_deposit'] + $result['nominal_pajak'];
            if ($data->total != 0) {
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
                ], 200);
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

                $date1 = $this->get_date_by_input($tanggal_mulai,$tanggal_selesai);
                $date2 = $this->get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai);
                $date_week = array_values(array_diff($date1,$date2));
                $date_cus = array_values(array_intersect($date1,$date2));
                
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

                // if ($diff_in_hours > $max) {
                //     return response()->json([
                //         'success' => false,
                //         'message' => 'Maximum days of a booking are '.$max,
                //         'code' => 0,
                //         'result' => []
                //     ], 400);
                // }

                $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($tanggal_mulai)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($tanggal_selesai)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',2)
                    ->where('id_ref',$id_properti)
                    ->first();

                $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
                $result['durasi_inap'] = $diff_in_hours;
                $result['durasi_inap_jam'] = 0;
                $result['extra_day'] = 0;
                $result['tamu_dewasa'] = $tamu_dewasa;
                $result['tamu_anak'] = $tamu_anak;
                $result['tamu_bayi'] = $tamu_bayi;
                $result['tamu_maksimal'] = $pro->jumlah_tamu;
                $result['tamu_tambahan'] = $tamu_tambahan;
                $result['harga_tampil'] = $pro->harga_tampil;

                    $result['harga_final_properti'] = 0;
                    $result['harga_tamu_tambahan'] = 0;
                                    
                if ($result['tamu_tambahan'] == 0) {
                    $result['harga_tamu_tambahan'] = 0;
                }

                if (count($date_cus) > 0) {
                    $har_cus = $this->get_harga_cus_by_input($date_cus,$cus);
                    $result['harga_final_properti'] = $result['harga_final_properti'] + $har_cus['harga'];                    

                    if ($tamu_tambahan > 0) {
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_cus['tamu'];
                        $result['harga_tamu_tambahan'] = $result['harga_tamu_tambahan'] + $tamu_add;
                    }
                }

                if (count($date_week) > 0) {
                    if ($pro->harga_weekend == null) {                        
                        $fin = $pro->harga_tampil * count($date_week);
                        $result['harga_final_properti'] = $result['harga_final_properti'] + $fin;
                    }elseif ($pro->harga_weekend != null) {
                        $final = $this->get_harga_by_input($date_week,$pro);
                        $fin = $final['harga'];
                        $result['harga_final_properti'] = $result['harga_final_properti'] + $fin;
                    }

                    if ($tamu_tambahan > 0) {
                        $har_week = $this->get_harga_by_input($date_week,$pro);
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_week['tamu'];                
                        $result['harga_tamu_tambahan'] = $result['harga_tamu_tambahan'] + $tamu_add;
                    }
                }

                if ($pro->biaya_kebersihan_tipe==2) {                    
                    $result['cleaning_fee'] = $pro->biaya_kebersihan;
                }else {
                    $result['cleaning_fee'] = $pro->biaya_kebersihan * $diff_in_hours;
                }
                $result['security_deposit'] = $pro->uang_jaminan;
                // dd($extra);
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $sevice = 0;
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        if ($ser->tipe == 1) {
                            $sevice = $sevice + $ser->harga;
                        }elseif ($ser->tipe == 2) {
                            $sevice = $sevice + ($ser->harga * $diff_in_hours);
                        }elseif ($ser->tipe == 3) {
                            $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak));
                        }else {
                            $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak) * $diff_in_hours);
                        }
                    }
                    $result['total_extra_service'] = $sevice;
                }else {
                    $result['total_extra_service'] = 0;
                }
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $res = [];
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        $res[] = $ser->nama_service;
                    }                    
                    $result['detail_extra_service'] = $res;
                }else{
                    $result['detail_extra_service'] = [];
                }
                $result['persen_pajak'] = $pro->pajak;
                $result['nominal_pajak'] = ($result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service']) * $result['persen_pajak'] /100;
                $result['harga_total'] = $result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service'] + $result['security_deposit'] + $result['nominal_pajak'];
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
                    ], 200);
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

                $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
                $result['durasi_inap'] = $week;
                $result['durasi_inap_jam'] = 0;
                $result['extra_day'] = $diff_in_hours % 7;
                $result['tamu_dewasa'] = $tamu_dewasa;
                $result['tamu_anak'] = $tamu_anak;
                $result['tamu_bayi'] = $tamu_bayi;
                $result['tamu_maksimal'] = $pro->jumlah_tamu;
                $result['tamu_tambahan'] = $tamu_tambahan;
                $result['harga_tampil'] = $pro->harga_tampil;
                if ($result['extra_day'] == 0) {
                    $result['harga_final_properti'] = $pro->harga_tampil * $week;
                }else {
                    $result['harga_final_properti'] = ($pro->harga_tampil * $week) + floor($pro->harga_tampil / 7 * $result['extra_day']);
                }
                if ($pro->harga_tamu_tambahan != null) {
                    if ($result['extra_day'] == 0) {
                        $result['harga_tamu_tambahan'] = $pro->harga_tamu_tambahan * $week * $tamu_tambahan;
                    }else {
                        $result['harga_tamu_tambahan'] = ($pro->harga_tamu_tambahan * $week * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 7 * $tamu_tambahan * $result['extra_day']);
                    }
                }else {
                    $result['harga_tamu_tambahan'] = 0;
                }
                if ($pro->biaya_kebersihan_tipe==2) {                    
                    $result['cleaning_fee'] = $pro->biaya_kebersihan;
                }else {
                    if ($result['extra_day'] == 0) {                        
                        $result['cleaning_fee'] = $pro->biaya_kebersihan * $week;
                    }else {
                        $result['cleaning_fee'] = ($pro->biaya_kebersihan * $week) + floor($pro->biaya_kebersihan / 7 * $result['extra_day']);
                    }
                }
                $result['security_deposit'] = $pro->uang_jaminan;
                // dd($extra);
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $sevice = 0;
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        if ($ser->tipe == 1) {
                            $sevice = $sevice + $ser->harga;
                        }elseif ($ser->tipe == 2) {
                            if ($result['extra_day'] == 0) {
                                $sevice = $sevice + ($ser->harga * $week);
                            }else {
                                $sevice = $sevice + ($ser->harga * $week) + ($ser->harga / 7 * $result['extra_day']);
                            }
                        }elseif ($ser->tipe == 3) {
                            $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak));
                        }else {
                            if ($result['extra_day'] == 0) {
                                $sevice = $sevice + ($ser->harga * $week * ($tamu_dewasa + $tamu_anak));
                            }else {
                                $sevice = $sevice + ($ser->harga * $week * ($tamu_dewasa + $tamu_anak)) + ($ser->harga / 7 * $result['extra_day'] * ($tamu_dewasa + $tamu_anak));
                            }                            
                        }
                    }
                    $result['total_extra_service'] = $sevice;
                }else {
                    $result['total_extra_service'] = 0;
                }
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $res = [];
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        $res[] = $ser->nama_service;
                    }                    
                    $result['detail_extra_service'] = $res;
                }else{
                    $result['detail_extra_service'] = [];
                }
                $result['persen_pajak'] = $pro->pajak;
                $result['nominal_pajak'] = ($result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service']) * $result['persen_pajak'] /100;
                $result['harga_total'] = $result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service'] + $result['security_deposit'] + $result['nominal_pajak'];
                if ($data->total != 0) {
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
                        'result' => $result,
                    ], 200);
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

                $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
                $result['durasi_inap'] = $week;
                $result['durasi_inap_jam'] = 0;
                $result['extra_day'] = $diff_in_hours % 30;
                $result['tamu_dewasa'] = $tamu_dewasa;
                $result['tamu_anak'] = $tamu_anak;
                $result['tamu_bayi'] = $tamu_bayi;
                $result['tamu_maksimal'] = $pro->jumlah_tamu;
                $result['tamu_tambahan'] = $tamu_tambahan;
                $result['harga_tampil'] = $pro->harga_tampil;
                if ($result['extra_day'] == 0) {
                    $result['harga_final_properti'] = $pro->harga_tampil * $week;
                }else {
                    $result['harga_final_properti'] = ($pro->harga_tampil * $week) + floor($pro->harga_tampil / 30 * $result['extra_day']);
                }
                if ($pro->harga_tamu_tambahan != null) {
                    if ($result['extra_day'] == 0) {
                        $result['harga_tamu_tambahan'] = $pro->harga_tamu_tambahan * $week * $tamu_tambahan;
                    }else {
                        $result['harga_tamu_tambahan'] = ($pro->harga_tamu_tambahan * $week * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 30 * $tamu_tambahan * $result['extra_day']);
                    }
                }else {
                    $result['harga_tamu_tambahan'] = 0;
                }
                if ($pro->biaya_kebersihan_tipe==2) {                    
                    $result['cleaning_fee'] = $pro->biaya_kebersihan;
                }else {
                    if ($result['extra_day'] == 0) {                        
                        $result['cleaning_fee'] = $pro->biaya_kebersihan * $week;
                    }else {
                        $result['cleaning_fee'] = ($pro->biaya_kebersihan * $week) + floor($pro->biaya_kebersihan / 30 * $result['extra_day']);
                    }
                }
                $result['security_deposit'] = $pro->uang_jaminan;
                // dd($extra);
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $sevice = 0;
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        if ($ser->tipe == 1) {
                            $sevice = $sevice + $ser->harga;
                        }elseif ($ser->tipe == 2) {
                            if ($result['extra_day'] == 0) {
                                $sevice = $sevice + ($ser->harga * $week);
                            }else {
                                $sevice = $sevice + ($ser->harga * $week) + floor($ser->harga / 30 * $result['extra_day']);
                            }
                        }elseif ($ser->tipe == 3) {
                            $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak));
                        }else {
                            if ($result['extra_day'] == 0) {
                                $sevice = $sevice + ($ser->harga * $week * ($tamu_dewasa + $tamu_anak));
                            }else {
                                $sevice = $sevice + ($ser->harga * $week * ($tamu_dewasa + $tamu_anak)) + floor($ser->harga / 30 * $result['extra_day'] * ($tamu_dewasa + $tamu_anak));
                            }                            
                        }
                    }
                    $result['total_extra_service'] = $sevice;
                }else {
                    $result['total_extra_service'] = 0;
                }
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $res = [];
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        $res[] = $ser->nama_service;
                    }                    
                    $result['detail_extra_service'] = $res;
                }else{
                    $result['detail_extra_service'] = [];
                }
                $result['persen_pajak'] = $pro->pajak;
                $result['nominal_pajak'] = ($result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service']) * $result['persen_pajak'] /100;
                $result['harga_total'] = $result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service'] + $result['security_deposit'] + $result['nominal_pajak'];
                if ($data->total != 0) {
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
                        'result' => $result,
                    ], 200);
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

                $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
                $result['durasi_inap'] = 0;
                $result['durasi_inap_jam'] = $diff_in_hours;
                $result['extra_day'] = 0;
                $result['tamu_dewasa'] = $tamu_dewasa;
                $result['tamu_anak'] = $tamu_anak;
                $result['tamu_bayi'] = $tamu_bayi;
                $result['tamu_maksimal'] = $pro->jumlah_tamu;
                $result['tamu_tambahan'] = $tamu_tambahan;
                $result['harga_tampil'] = $pro->harga_tampil;
                if ($pro->harga_weekend != null) {
                    $hari = date('D', strtotime($tanggal_mulai));
                    if ($pro->penerapan_harga_weekend==1) {
                        if ($hari == 'Sat') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }elseif ($hari == 'Sun') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }else {
                            $final = $diff_in_hours * $pro->harga_tampil;
                        }
                    }
                    if ($pro->penerapan_harga_weekend==2) {
                        if ($hari == 'Fri') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }elseif ($hari == 'Sat') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }else {
                            $final = $diff_in_hours * $pro->harga_tampil;
                        }
                    }
                    if ($pro->penerapan_harga_weekend==3) {
                        if ($hari == 'Fri') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }elseif ($hari == 'Sat') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }elseif ($hari == 'Sun') {
                            $final = $diff_in_hours * $pro->harga_weekend;
                        }else {
                            $final = $diff_in_hours * $pro->harga_tampil;
                        }
                    }
                    $result['harga_final_properti'] = $final;
                }else {
                    $result['harga_final_properti'] = $pro->harga_tampil * $diff_in_hours;
                }
                if ($tamu_tambahan > 0) {
                    $result['harga_tamu_tambahan'] = $pro->harga_tamu_tambahan * $tamu_tambahan;
                }else{
                    $result['harga_tamu_tambahan'] = 0;
                }
                if ($pro->biaya_kebersihan_tipe==2) {
                    $result['cleaning_fee'] = $pro->biaya_kebersihan;
                }else {
                    $result['cleaning_fee'] = $pro->biaya_kebersihan * $diff_in_hours;
                }
                $result['security_deposit'] = $pro->uang_jaminan;
                // dd($extra);
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $sevice = 0;
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        if ($ser->tipe == 1) {
                            $sevice = $sevice + $ser->harga;
                        }elseif ($ser->tipe == 2) {
                            $sevice = $sevice + ($ser->harga * $diff_in_hours);
                        }elseif ($ser->tipe == 3) {
                            $sevice = $sevice + ($ser->harga * ($tamu_dewasa + $tamu_anak));
                        }else {                            
                            $sevice = $sevice + ($ser->harga * $diff_in_hours * ($tamu_dewasa + $tamu_anak));
                        }
                    }
                    $result['total_extra_service'] = $sevice;
                }else {
                    $result['total_extra_service'] = 0;
                }
                if ($extra_service != null) {
                    $extra = explode(",",$extra_service);
                    $res = [];
                    for ($i=0; $i < count($extra); $i++) {
                        $ser = MPropertiExtra::find($extra[$i]);
                        $res[] = $ser->nama_service;
                    }                    
                    $result['detail_extra_service'] = $res;
                }else{
                    $result['detail_extra_service'] = [];
                }
                $result['persen_pajak'] = $pro->pajak;
                $result['nominal_pajak'] = ($result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service']) * $result['persen_pajak'] /100;
                $result['harga_total'] = $result['harga_final_properti'] + $result['harga_tamu_tambahan'] + $result['cleaning_fee'] + $result['total_extra_service'] + $result['security_deposit'] + $result['nominal_pajak'];
                if ($data->total != 0) {
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
                        'result' => $result,
                    ], 200);
                }            
        }

    }

    public function post_property_booking(Request $request)
    {
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $customer = User::join('m_customer','m_customer.id','m_users.id_ref')
                    ->select('m_customer.nama_depan')
                    ->where('m_users.id_user',$user->id_user)
                    ->first();
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
        
        $operator_where = '>';
        if($id_tipe_booking == "1"){
            $operator_where = '>=';
        }

        $cek_date = MBooking::where('deleted',1)->where('id_tipe',1)->where('id_ref',$id_properti)->whereRaw('("'.$tanggal_mulai.'" < tanggal_selesai AND "'.$tanggal_selesai.'" '.$operator_where.' tanggal_mulai)')->get()->count();

        if($cek_date == 0){

            $pro = MProperti::find($id_properti);
            $cus = MPropertiHargaPeriode::where('id_properti',$id_properti)->get();

            $tamu_tambahan = ((($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($tamu_dewasa+$tamu_anak) - $pro->jumlah_tamu));
                    
            // $date_now = date('Y-m-d');
            $squencedtoday = MBooking::where('deleted',1)->get()->count();
            $squence = 1000+$squencedtoday+1;
            // $squence = str_pad($squence,4,0,STR_PAD_LEFT);
            // $squence = date('Ymd').$squence;
            // dd($squence);
            $extra = 0;
            if ($id_tipe_booking == 1) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai.' -1 day'));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $durasi_inap = $to->diffInDays($from);
                $extra = 0;
                $durasi_inap_jam = 0;
            }elseif ($id_tipe_booking == 2) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $durasi_inap = $to->diffInDays($from);
                $durasi_inap_jam = 0;
                $extra = 0;
            }elseif ($id_tipe_booking == 3) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                // dd($diff_in_hours);
                $durasi_inap = floor($diff_in_hours / 7);
                $durasi_inap_jam = 0;
                $extra = $diff_in_hours % 7;
            }elseif ($id_tipe_booking == 4) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                $durasi_inap = floor($diff_in_hours / 30);
                $durasi_inap_jam = 0;
                $extra = $diff_in_hours % 30;
            }else{
                $mulia = date('Y-m-d H:s:i', strtotime($tanggal_mulai.' '.$jam_mulai));
                $selesai = date('Y-m-d H:s:i', strtotime($tanggal_mulai.' '.$jam_selesai));
                $from = Carbon::parse($mulia);
                $to = Carbon::parse($selesai);
                $durasi_inap_jam = $to->diffInHours($from);
                $extra = 0;
                $durasi_inap = 0;
            }

            if ($pro->biaya_kebersihan_tipe == 1) {
                if ($id_tipe_booking == 5) {                
                    $biaya_kebersihan = $pro->biaya_kebersihan * $durasi_inap_jam;
                }else {
                    $biaya_kebersihan = $pro->biaya_kebersihan * $durasi_inap;
                }
            }else {
                $biaya_kebersihan = $pro->biaya_kebersihan;
            }

            $tipe = new MBooking();
            $tipe->kode_booking = $squence;
            $tipe->id_user = $user->id_user;
            $tipe->id_tipe = 1;
            $tipe->id_ref = $id_properti;
            $tipe->tanggal_mulai = date('Y-m-d', strtotime($tanggal_mulai));
            $tipe->tanggal_selesai = date('Y-m-d', strtotime($tanggal_selesai));
            $tipe->jam_mulai = $jam_mulai;
            $tipe->jam_selesai = $jam_selesai;
            $tipe->durasi_inap = $durasi_inap;
            $tipe->extra_hari = $extra;
            $tipe->durasi_inap_jam = $durasi_inap_jam;
            $tipe->tamu_dewasa = $tamu_dewasa;
            $tipe->tamu_anak = $tamu_anak;
            $tipe->tamu_bayi = $tamu_bayi;
            $tipe->tamu_maksimal = $pro->jumlah_tamu;
            $tipe->catatan = $catatan;
            $tipe->cleaning_fee = $biaya_kebersihan;
            $tipe->security_deposit = $pro->uang_jaminan;
            $tipe->persen_pajak = $pro->pajak;
            // $tipe->id_kupon = '';
            // $tipe->potongan_kupon = '';
            $tipe->id_status_booking = 1;
            $tipe->alasan_reject = '';
            $tipe->alasan_cancel = '';
            $tipe->review = 0;
            $tipe->save();

            if ($id_tipe_booking == 1) {            
                if ($pro->harga_weekly != null && $pro->harga_monthly != null) {                
                    if ($tipe->durasi_inap > 30) {
                        $harga_final_properti = $pro->harga_monthly * $tipe->durasi_inap;
                    }elseif ($tipe->durasi_inap > 7 && $tipe->durasi_inap <= 30) {
                        $harga_final_properti = $pro->harga_weekly * $tipe->durasi_inap;
                    }else {
                        $harga_final_properti = $pro->harga_tampil * $tipe->durasi_inap;
                    }
                }else {
                    $harga_final_properti = $pro->harga_tampil * $tipe->durasi_inap;
                }

                if($tamu_tambahan > 0){
                    $harga_final_properti_tamu = $pro->harga_tampil * $tipe->durasi_inap * $tamu_tambahan;
                    $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti_tamu, $durasi_inap, 0);
                }

                $this->booking_harga($tipe->id_booking, 1, 1, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti, $durasi_inap, 0);
            }elseif ($id_tipe_booking == 2) {            
                $date1 = $this->get_date_by_input($tanggal_mulai,$tanggal_selesai);
                $date2 = $this->get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai);
                $date_week = array_values(array_diff($date1,$date2));
                $date_cus = array_values(array_intersect($date1,$date2));
                // dd('first :'.$date_week[0].' last :'.end($date_week));
                // dd(date('Y-m-d', strtotime(end($date_cus).' +1 day')));
                // dd($date2);
                if (count($date_cus) > 0) {
                    $har_cus = $this->get_harga_cus_by_input($date_cus,$cus);                
                    $this->booking_harga($tipe->id_booking, 1, 1, null, null, $date_cus[0], date('Y-m-d', strtotime(end($date_cus).' +1 day')), $har_cus['harga_tampil'], $har_cus['harga'], count($date_cus), 1);

                    if ($tamu_tambahan > 0) {
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_cus['tamu'];
                        $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, $date_cus[0], date('Y-m-d', strtotime(end($date_cus).' +1 day')), $har_cus['tamu'], $tamu_add, count($date_cus), 1);
                    }
                }

                if (count($date_week) > 0) {
                    if ($pro->harga_weekend == null) {
                        //$fin = $pro->harga_tampil * $durasi_inap;
                        $fin = $pro->harga_tampil * count($date_week);
                        $this->booking_harga($tipe->id_booking, 1, 1, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $pro->harga_tampil, $fin, count($date_week), 0);
                    }elseif ($pro->harga_weekend != null) {
                        $final = $this->get_harga_by_input($date_week,$pro);
                        $fin = $final['harga'];
                        $this->booking_harga($tipe->id_booking, 1, 1, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $pro->harga_tampil, $fin, count($date_week), 0);
                    }

                    if ($tamu_tambahan > 0) {
                        $har_week = $this->get_harga_by_input($date_week,$pro);
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_week['tamu'];                

                        $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $har_week['tamu'], $tamu_add, count($date_week), 0);
                    }
                }
                            
            }elseif ($id_tipe_booking == 3) {
                $harga_final_properti = $pro->harga_tampil * $tipe->durasi_inap;
                $this->booking_harga($tipe->id_booking, 1, 1, null, null, $tanggal_mulai, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 7).' day')), $pro->harga_tampil, $harga_final_properti, $tipe->durasi_inap, 0);

                if($tipe->extra_hari > 0){
                    $harga_final_properti_extra = ($pro->harga_tampil * $tipe->durasi_inap) + floor($pro->harga_tampil / 7 * $tipe->extra_hari);

                    $this->booking_harga($tipe->id_booking, 1, $tipe->extra_hari, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 7).' day')), $tanggal_selesai, floor($pro->harga_tampil / 7), $harga_final_properti_extra, $tipe->durasi_inap, 0);
                }

                if($tamu_tambahan > 0){
                    if ($pro->harga_tamu_tambahan != null) {                    
                        $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $tipe->durasi_inap * $tamu_tambahan;                    
                    }else {
                        $harga_final_properti_tamu = 0;
                    }

                    $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 7).' day')), $pro->harga_tamu_tambahan, $harga_final_properti_tamu, $tipe->durasi_inap, 0);
                    
                    if($tipe->extra_hari > 0){
                        $harga_final_properti_tamu_extra = ($pro->harga_tamu_tambahan * $tipe->durasi_inap * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 7 * $tamu_tambahan * $tipe->extra_hari);

                        $this->booking_harga($tipe->id_booking, 2, ($tamu_tambahan * $tipe->extra_hari), null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 7).' day')), $tanggal_selesai, floor($pro->harga_tamu_tambahan / 7), $harga_final_properti_tamu_extra, $tipe->durasi_inap, 0);
                    }
                }
            }elseif ($id_tipe_booking == 4) {            
                $harga_final_properti = $pro->harga_tampil * $tipe->durasi_inap;
                $this->booking_harga($tipe->id_booking, 1, 1, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti, $tipe->durasi_inap, 0);
                
                if($tipe->extra_hari > 0){
                    $harga_final_properti_extra = ($pro->harga_tampil * $tipe->durasi_inap) + floor($pro->harga_tampil / 30 * $tipe->extra_hari);
                    
                    $this->booking_harga($tipe->id_booking, 1, $tipe->extra_hari, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 30).' day')), $tanggal_selesai, floor($pro->harga_tampil / 30), $harga_final_properti_extra, $tipe->durasi_inap, 0);
                }

                if($tamu_tambahan > 0){
                    if ($pro->harga_tamu_tambahan != null) {                    
                        $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $tipe->durasi_inap * $tamu_tambahan;                    
                    }else {
                        $harga_final_properti_tamu = 0;
                    }

                    $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti_tamu, $tipe->durasi_inap, 0);
                    
                    if($tipe->extra_hari > 0){
                        $harga_final_properti_tamu_extra = ($pro->harga_tamu_tambahan * $tipe->durasi_inap * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 30 * $tamu_tambahan * $tipe->extra_hari);
                        
                        $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($tipe->durasi_inap * 30).' day')), $tanggal_selesai, floor($pro->harga_tampil / 30), $harga_final_properti_tamu, $tipe->durasi_inap, 0);
                    }
                }
            }else{
                if ($pro->harga_weekend != null) {
                    $hari = date('D', strtotime($tanggal_mulai));
                    if ($pro->penerapan_harga_weekend==1) {
                        if ($hari == 'Sat') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }elseif ($hari == 'Sun') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }else {
                            $final = $durasi_inap_jam * $pro->harga_tampil;
                        }
                    }
                    if ($pro->penerapan_harga_weekend==2) {
                        if ($hari == 'Fri') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }elseif ($hari == 'Sat') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }else {
                            $final = $durasi_inap_jam * $pro->harga_tampil;
                        }
                    }
                    if ($pro->penerapan_harga_weekend==3) {
                        if ($hari == 'Fri') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }elseif ($hari == 'Sat') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }elseif ($hari == 'Sun') {
                            $final = $durasi_inap_jam * $pro->harga_weekend;
                        }else {
                            $final = $durasi_inap_jam * $pro->harga_tampil;
                        }
                    }                
                }else {
                    $final = $pro->harga_tampil * $durasi_inap_jam;
                }
                $this->booking_harga($tipe->id_booking, 1, 1, $jam_mulai, $jam_selesai, null, null, $pro->harga_tampil, $final, $tipe->durasi_inap, 0);

                if ($tamu_tambahan > 0) {
                    $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $tamu_tambahan;
                    
                    $this->booking_harga($tipe->id_booking, 2, $tamu_tambahan, $jam_mulai, $jam_selesai, null, null, $pro->harga_tampil, $harga_final_properti_tamu, $tipe->durasi_inap, 0);
                }
            }

                    if ($extra_service != null) {                
                        $extra_service_tam = explode(",",$extra_service);
                        for ($i=0; $i < count($extra_service_tam); $i++) {
                            $ser = MPropertiExtra::find($extra_service_tam[$i]);
                            if ($ser->tipe == 1) {
                                $sevice = $ser->harga;
                            }elseif ($ser->tipe == 2) {
                                if ($pro->id_tipe_booking == 3) {
                                    $sevice = ($ser->harga * $durasi_inap)+($extra * floor($ser->harga/7));
                                }elseif ($pro->id_tipe_booking == 4) {
                                    $sevice = ($ser->harga * $durasi_inap)+($extra * floor($ser->harga/30));
                                }elseif ($pro->id_tipe_booking == 5) {
                                    $sevice = ($ser->harga * $durasi_inap_jam);
                                }else {
                                    $sevice = ($ser->harga * $durasi_inap);
                                }
                            }elseif ($ser->tipe == 3) {
                                $sevice = ($ser->harga * ($tamu_dewasa + $tamu_anak));
                            }else {
                                if ($pro->id_tipe_booking == 3) {
                                    $sevice = ($ser->harga * $durasi_inap * ($tamu_dewasa + $tamu_anak)) + ($extra * floor($ser->harga/7) * ($tamu_dewasa + $tamu_anak));
                                }elseif ($pro->id_tipe_booking == 4) {
                                    $sevice = ($ser->harga * $durasi_inap * ($tamu_dewasa + $tamu_anak)) + ($extra * floor($ser->harga/30) * ($tamu_dewasa + $tamu_anak));
                                }elseif ($pro->id_tipe_booking == 5) {
                                    $sevice = ($ser->harga * $durasi_inap_jam * ($tamu_dewasa + $tamu_anak));
                                }else {                                
                                    $sevice = ($ser->harga * $durasi_inap * ($tamu_dewasa + $tamu_anak));
                                }
                            }
                            $book = new MBookingPropertiExtra();
                            $book->id_booking = $tipe->id_booking;
                            $book->id_properti_extra = $ser->id_properti_extra;
                            $book->harga_satuan = $ser->harga;
                            $book->harga_final = $sevice;
                            $book->save();
                        }                
                    }

                $harga_final_properti = MBookingHargaSatuan::where('id_booking',$tipe->id_booking)->get()->sum('harga_final');
                $total_extra_service = MBookingPropertiExtra::where('id_booking',$tipe->id_booking)->get()->sum('harga_final');
                $total_booking_extra = MBookingExtra::where('id_booking',$tipe->id_booking)->get()->sum('harga');
                $nominal_pajak = $pro->pajak / 100 * ($harga_final_properti + $biaya_kebersihan + $total_extra_service);

                $data_update = [
                    'harga_final_properti' => $harga_final_properti,
                    'nominal_pajak' => $nominal_pajak,
                    'total_extra_service' => $total_extra_service,
                    'total_booking_extra' => $total_booking_extra,
                    'harga_total' => $harga_final_properti + $biaya_kebersihan + $total_extra_service + $pro->uang_jaminan + $nominal_pajak,
                ];
                MBooking::where('id_booking',$tipe->id_booking)->update($data_update);

            if ($tipe) {
                $judul_p = '#'.$squence.' - '.$customer->nama_depan;
			$id_user_pengirim = $user->id_user;
			$id_user_penerima = 1;
			$pesan_terakhir = ($catatan == null ? 'check availability for '.date('d-m-Y', strtotime($tanggal_mulai)).' to '.date('d-m-Y', strtotime($tanggal_selesai)) : $catatan);
			$id_ref_p = $squence;
			$judul_mobile = '#'.$squence.' - '.$pro->judul;

			$hpesan = new HPesan;
			$hpesan->judul = $judul_p;
			$hpesan->id_user_pengirim = $id_user_pengirim;
			$hpesan->id_user_penerima = $id_user_penerima;
			$hpesan->pesan_terakhir = $pesan_terakhir;
			$hpesan->waktu_pesan_terakhir = date('Y-m-d H:i:s');
			$hpesan->id_ref = $id_ref_p;
			$hpesan->id_booking = $tipe->id_booking;
			$hpesan->judul_mobile = $judul_mobile;
			// $hpesan->updated_date = date('Y-m-d H:i:s');
			$hpesan->save();

			$hdetail = new HPesanDetail;
			$hdetail->id_pesan = $hpesan->id_pesan;
			$hdetail->id_ref = $id_ref_p;
			$hdetail->id_tipe = 3;
			$hdetail->pesan = $pesan_terakhir;
			$hdetail->id_user = $id_user_pengirim;
			$hdetail->save();
			
			// $timestamp = Timestamp::fromDate(date('Y-m-d H:i:s'));
			$firestore = Firestore::get();
			$firePesan = $firestore->collection('h_pesan')->newDocument();
			$firePesan->set([    
				'badge' => 1,
				'created_date' => date('Y-m-d H:i:s'),
				'id_pesan' => $hpesan->id_pesan,
				'id_ref' => $id_ref_p,
				'id_user_penerima' => $id_user_penerima,
				'id_user_pengirim' => $id_user_pengirim,
				'judul' => $judul_p,
                'id_booking' => $tipe->id_booking,
                'judul_mobile' => $judul_mobile,
				'penerima_lihat' => 0,
				'pengirim_lihat' => 0,
				'pesan_terakhir' => '',//$pesan_terakhir,
				'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
				'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
			]);

			$fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
			$fireDetail->set([    
				'id_pesan_detail' => $hdetail->id_pesan_detail,
				'id_pesan' => $hpesan->id_pesan,
				'id_ref' => $id_ref_p,
				'id_tipe' => 3,
				'url' => "",
				'pesan' => $pesan_terakhir,
				'created_date' => date('Y-m-d H:i:s'),
				'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
				'id_user' => $id_user_pengirim,
			]);

			$query = $firestore->collection('h_pesan')
			->where('id_pesan', '=', $hpesan->id_pesan);
		
			$documents = $query->documents();        
			$id = null;
			foreach ($documents as $document) {
				$id = $document->id();
				$doc = $firestore->collection('h_pesan')->document($id)
					->set([
						'badge' => $document['badge'],
						'created_date' => $document['created_date'],
						'id_pesan' => $document['id_pesan'],
						'id_ref' => $document['id_ref'],
						'id_user_penerima' => $document['id_user_penerima'],
						'id_user_pengirim' => $document['id_user_pengirim'],
						'judul' => $document['judul'],
                        'id_booking' => $document['id_booking'],
                        'judul_mobile' => $document['judul_mobile'],
						'penerima_lihat' => $document['penerima_lihat'],
						'pengirim_lihat' => $document['pengirim_lihat'],
						'pesan_terakhir' => $pesan_terakhir,
						'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
						'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
					]);
			}

                return response()->json([
                    'success' => true,
                    'message' => 'Booking request sent. Please wait for confirmation!',
                    'code' => 1,            
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Booking request failed. Please contact Admin!',
                    'code' => 0,
                ], 400);
            }

        }else{

            return response()->json([
                    'success' => false,
                    'message' => 'Booking request failed. In the date range there are dates that have been booked!',
                    'code' => 0,
                ], 400);

        }
    }

    public function booking_harga($id, $tipe, $jumlah, $jam_mulai, $jam_selesai, $tanggal_mulai, $tanggal_selesai, $satuan, $final, $durasi, $custom)
    {
        // dd($jumlah);
        $booking = new MBookingHargaSatuan();
        $booking->id_booking = $id;
        $booking->id_tipe = $tipe;
        $booking->jumlah_paket = $jumlah;
        if ($tanggal_mulai == null) {                    
            $booking->jam_mulai = $jam_mulai;
            $booking->jam_selesai = $jam_selesai;
            $booking->durasi_inap_jam = $durasi;
        }else {
            $booking->tanggal_mulai = date('Y-m-d', strtotime($tanggal_mulai));
            $booking->tanggal_selesai = date('Y-m-d', strtotime($tanggal_selesai));
            $booking->durasi_inap = $durasi;
        }
        $booking->custom_periode = $custom;
        $booking->harga_satuan = $satuan;
        $booking->harga_final = $final;
        $booking->save();
    }
    
    public function get_property_detail_amenities_fasilitas(Request $request)
    {
        $id_properti = $request->id_properti;
        $id_bahasa = $request->id_bahasa;
        
        $fasilitas = MapFasilitas::selectRaw('m_fasilitas.*')
                ->join('m_fasilitas','m_fasilitas.id_ref_bahasa','map_fasilitas.id_fasilitas')                
                ->where('m_fasilitas.id_bahasa',$id_bahasa)
                ->where('m_fasilitas.deleted',1)
                ->where('map_fasilitas.id_properti',$id_properti)
                ->orderBy('m_fasilitas.nama_fasilitas','asc')
                ->get();

        $amenities = MapAmenities::selectRaw('m_amenities.*')
                ->join('m_amenities','m_amenities.id_ref_bahasa','map_amenities.id_amenities')                
                ->where('m_amenities.id_bahasa',$id_bahasa)
                ->where('m_amenities.deleted',1)
                ->where('map_amenities.id_properti',$id_properti)
                ->orderBy('m_amenities.nama_amenities','asc')                
                ->get();        

        if ([$fasilitas,$amenities]) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'code' => 1,
                'facilities' => $fasilitas,
                'amenities' => $amenities,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada data',
                'code' => 0,
            ], 400);
        }
    }

    public function get_property_detail_review(Request $request)
    {
        if ($request->header('auth-key') != null) {
            $user = MApiKey::where('token',$request->header('auth-key'))->first();
            $id_user = $user->id_user;
        }else {
            $id_user = 0;
        }
        $id_properti = $request->id_properti;
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $rating = $request->rating;
        
        $result = HReviewRating::selectRaw('h_review_rating.*, CONCAT(m_customer.nama_depan," ",m_customer.nama_belakang) as nama_lengkap, m_customer.nama_foto')
                ->addSelect(DB::raw("( SELECT count(*) FROM h_review_rating_like WHERE id_user = $id_user AND id_review_rating = h_review_rating.id and deleted = 1) as user_like"))
                ->join('m_users','m_users.id_user','h_review_rating.id_user')
                ->join('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.deleted',1)
                ->where('h_review_rating.id_properti',$id_properti)
                ->orderBy('h_review_rating.created_date','desc')
                ->limit($limit)
                ->offset($page);

        $get_total_all_data = HReviewRating::selectRaw('h_review_rating.id')
                ->join('m_users','m_users.id_user','h_review_rating.id_user')
                ->join('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.deleted',1)
                ->where('h_review_rating.id_properti',$id_properti)
                ->orderBy('h_review_rating.created_date','desc');

        if ($rating != 0) {
            if ($rating != null) {
                $result = $result->where('h_review_rating.rating',$rating);
                $get_total_all_data = $get_total_all_data->where('h_review_rating.rating',$rating);
            }
        }
        $data = $result->get();
        $get_total_all_data = $get_total_all_data->get()->count();
        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'total_data' => count($data),
            'result' => $data,
            'total_page' => $total_page
        ], 200);        
    }
    public function get_properti_by_city(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $id_kota = $request->id_kota;
        $order_by = $request->order_by;

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan, nama_file')
                ->where('id_kota',$id_kota)
                ->where('id_bahasa',$id_bahasa)                
                ->where('deleted',1)
                ->limit($limit)
                ->offset($page);
        $get_total_all_data = MProperti::selectRaw('id_properti')
                ->where('id_kota',$id_kota)
                ->where('id_bahasa',$id_bahasa)                
                ->where('deleted',1);
        if ($order_by == 1) {
            $tipe = $tipe->orderBy('harga_tampil','asc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','asc');
        }elseif ($order_by == 2) {
            $tipe = $tipe->orderBy('harga_tampil','desc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','desc');
        }elseif ($order_by == 3) {
            $tipe = $tipe->orderBy('nilai_rating','desc');
            $get_total_all_data = $get_total_all_data->orderBy('nilai_rating','desc');
        }elseif ($order_by == 4) {
            $tipe = $tipe->orderByRaw('(total_amenities+total_fasilitas) desc');
            $get_total_all_data = $get_total_all_data->orderByRaw('(total_amenities+total_fasilitas) desc');
        }elseif ($order_by == 5) {
            $tipe = $tipe->orderBy('created_date','desc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','desc');
        }else {
            $tipe = $tipe->orderBy('created_date','asc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','asc');
        }
        $data = $tipe->get();
        $get_total_all_data = $get_total_all_data->get()->count();
        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
            'total_page' => $total_page
        ], 200);        
    }
    public function get_property_by_facilities(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;
        $limit = 6;
        $page = ($request->page-1)*$limit;
        $order_by = $request->order_by;
        $id_fasilitas = $request->id_properti;

        // $list_id_properti_fas = MapFasilitas::select('id_properti')->whereIn('id_fasilitas',$id_fasilitas)->get()->toArray();
        // dd($id_fasilitas);
        $list_id_properti_fas = explode(",",$id_fasilitas);

        $tipe = MProperti::selectRaw('id_properti, id_bahasa, id_ref_bahasa, judul, alamat, harga_tampil, jumlah_kamar_tidur, jumlah_kamar_mandi, (jumlah_tamu+COALESCE(jumlah_tamu_tambahan, 0)) as jumlah_total_tamu, sarapan, nama_file')
                ->whereIn('id_properti',$list_id_properti_fas)
                ->where('id_bahasa',$id_bahasa)                
                ->where('deleted',1)
                ->limit($limit)
                ->offset($page);
        
        $get_total_all_data = MProperti::selectRaw('id_properti')
                ->whereIn('id_properti',$list_id_properti_fas)
                ->where('id_bahasa',$id_bahasa)                
                ->where('deleted',1);

        if ($order_by == 1) {
            $tipe = $tipe->orderBy('harga_tampil','asc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','asc');
        }elseif ($order_by == 2) {
            $tipe = $tipe->orderBy('harga_tampil','desc');
            $get_total_all_data = $get_total_all_data->orderBy('harga_tampil','desc');
        }elseif ($order_by == 3) {
            $tipe = $tipe->orderBy('nilai_rating','desc');
            $get_total_all_data = $get_total_all_data->orderBy('nilai_rating','desc');
        }elseif ($order_by == 4) {
            $tipe = $tipe->orderByRaw('(total_amenities+total_fasilitas) desc');
            $get_total_all_data = $get_total_all_data->orderByRaw('(total_amenities+total_fasilitas) desc');
        }elseif ($order_by == 5) {
            $tipe = $tipe->orderBy('created_date','desc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','desc');
        }else {
            $tipe = $tipe->orderBy('created_date','asc');
            $get_total_all_data = $get_total_all_data->orderBy('created_date','asc');
        }
        $data = $tipe->get();
        $get_total_all_data = $get_total_all_data->get()->count();
        $total_page = 0;
        $hasil_bagi = $get_total_all_data / $limit;
        if(fmod($get_total_all_data, $limit) == 0){
            $total_page = $hasil_bagi;
        }else{
            $total_page = floor($hasil_bagi)+1;
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'data' => $data,
            'total_page' =>$total_page
        ], 200);        
    }

    public function post_property_payment(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;
        $id_booking = $request->id_booking;
        $kode_booking = $request->kode_booking;
        $nama_depan = $request->nama_depan;
        $nama_belakang = $request->nama_belakang;
        $nama_perusahaan = $request->nama_perusahaan;
        $alamat = $request->alamat;
        $nama_provinsi = $request->nama_provinsi;
        $nama_kota = $request->nama_kota;
        $kode_pos = $request->kode_pos;
        $no_telfon = $request->no_telfon;
        $email = $request->email;
        $catatan = $request->catatan;
        $id_negara = $request->id_negara;
        $iso_code = $request->iso_code;
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $detail_booking = MBooking::from( 't_booking as a' )
            ->selectRaw('a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, c.nama_status_booking, CONCAT(e.nama_depan," ",e.nama_belakang) as nama_pemilik_properti, CONCAT(g.nama_depan," ",g.nama_belakang) as nama_pemesan, h.nama_tipe_properti')
            ->leftJoin('m_properti as b','a.id_ref', '=','b.id_ref_bahasa')
            ->leftJoin('m_status_booking as c','a.id_status_booking', '=','c.id_ref_bahasa')
            ->leftJoin('m_users as d','d.id_user', '=','b.created_by')
            ->leftJoin('m_customer as e','d.id_ref', '=','e.id')
            ->leftJoin('m_users as f','f.id_user', '=','a.id_user')
            ->leftJoin('m_customer as g','f.id_ref', '=','g.id')
            ->leftJoin('m_tipe_properti as h','h.id_ref_bahasa', '=','b.id_tipe_booking')
            ->where('h.id_bahasa',$id_bahasa)
            ->where('a.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->where('b.deleted',1)
            ->where('b.id_bahasa',$id_bahasa)
            ->where('a.id_booking',$id_booking)
            ->where('d.deleted',1)
            ->where('c.id_bahasa',$id_bahasa)
            ->where('f.deleted',1)->first();
        
        $detail_booking_harga_satuan = MBookingHargaSatuan::where('id_booking',$id_booking)->get();
        // $detail_booking_properti_extra = MBookingPropertiExtra::where('id_booking',$id_booking)->get();
        $detail_booking_properti_extra = MBookingPropertiExtra::selectRaw('t_booking_properti_extra.*, m_properti_extra.nama_service as nama_properti_extra, m_properti_extra.tipe as tipe_properti_extra')->leftJoin('m_properti_extra','t_booking_properti_extra.id_properti_extra', '=','m_properti_extra.id_properti_extra')->where('t_booking_properti_extra.id_booking',$id_booking)->get();
        $detail_booking_extra = MBookingExtra::where('id_booking',$id_booking)->get();
        $detail_booking_discount = MBookingDiscount::where('id_booking',$id_booking)->get();
        $cek_kode = TKonfirmasiBayar::where('kode_booking',$kode_booking)->get()->count();

        if ($cek_kode == 0) {                    
            $bayar = new TKonfirmasiBayar;
            // $bayar->id_bahasa = $id_bahasa;
            $bayar->id_booking = $id_booking;
            $bayar->kode_booking = $kode_booking;
            $bayar->nama_depan = $nama_depan;
            $bayar->nama_belakang = $nama_belakang;
            $bayar->nama_perusahaan = $nama_perusahaan;
            $bayar->alamat = $alamat;
            $bayar->nama_provinsi = $nama_provinsi;
            $bayar->nama_kota = $nama_kota;
            $bayar->kode_pos = $kode_pos;
            $bayar->no_telfon = $no_telfon;
            $bayar->email = $email;
            $bayar->catatan = $catatan;
            $bayar->created_by = $user->id_user;
            $bayar->id_negara = $id_negara;
            $bayar->iso_code = $iso_code;
            $bayar->save();

            // dd($detail_booking->harga_total);
            $curl =  $this->postCURL($kode_booking ,$detail_booking->harga_total, $nama_depan, $nama_belakang, $alamat, $nama_provinsi, $nama_kota, $kode_pos,$no_telfon, $email, $iso_code);            

            $payment_url = json_decode($curl)->data->payment_url;
            $data_transaction_id = json_decode($curl)->data->transaction_id;

            MBooking::where('id_booking',$id_booking)->update(['respone_payment_page' => $curl, 'pg_order_code' => 'PG'.$kode_booking, 'pg_url' => $payment_url, 'pg_transaction_id' => $data_transaction_id]);
        }else {
            $cek_booking = MBooking::where('id_booking',$id_booking)->first();
            $expired_time = json_decode($cek_booking->respone_payment_page)->data->expired_time;
            // dd($expired_time);
            if ($cek_booking->pg_url == null) {
                $curl =  $this->postCURL($kode_booking ,$detail_booking->harga_total, $nama_depan, $nama_belakang, $alamat, $nama_provinsi, $nama_kota, $kode_pos,$no_telfon, $email, $iso_code);            
                $payment_url = json_decode($curl)->data->payment_url;
                $data_transaction_id = json_decode($curl)->data->transaction_id;
                MBooking::where('id_booking',$id_booking)->update(['respone_payment_page' => $curl, 'pg_order_code' => 'PG'.$kode_booking, 'pg_url' => $payment_url, 'pg_transaction_id' => $data_transaction_id]);
            }else {
                if ($expired_time < date('Y-m-d H:i:s')) {
                    $curl =  $this->postCURL($kode_booking ,$detail_booking->harga_total, $nama_depan, $nama_belakang, $alamat, $nama_provinsi, $nama_kota, $kode_pos,$no_telfon, $email, $iso_code);            
                    $payment_url = json_decode($curl)->data->payment_url;
                    $data_transaction_id = json_decode($curl)->data->transaction_id;
                    MBooking::where('id_booking',$id_booking)->update(['respone_payment_page' => $curl, 'pg_order_code' => 'PG'.$kode_booking, 'pg_url' => $payment_url, 'pg_transaction_id' => $data_transaction_id]);
                }else{
                    $curl = $cek_booking->respone_payment_page;
                    // dd($curl);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'detail_booking' => $detail_booking,
            'detail_payment' =>[
                'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
                'detail_booking_properti_extra' => $detail_booking_properti_extra,
                'detail_booking_extra' => $detail_booking_extra,
                'detail_booking_discount' => $detail_booking_discount,
            ],
            'respone_payment_gateway' => json_decode($curl)

        ], 200);        
    }

    public function post_property_coupon(Request $request)
    {                
        $id_bahasa = $request->id_bahasa;
        $id_booking = $request->id_booking;
        $kode_coupon = $request->kode_coupon;        
        $user = MApiKey::where('token',$request->header('auth-key'))->first();

        $cek_coupon = MKupon::where('kode_kupon',$kode_coupon)->first();
        if ($cek_coupon == null) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, no coupon code found',
                'code' => 0,            
            ], 400);
        }
        $tgl_coupon = MKupon::where('id_kupon',$cek_coupon->id_kupon)
            ->whereDate('tanggal_mulai', '<=', date("Y-m-d"))
            ->whereDate('tanggal_selesai', '>=', date("Y-m-d"))
            ->first();
        // dd($tgl_coupon);
        if ($tgl_coupon == null) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the coupon code cannot be used',
                'code' => 0,
            ], 400);
        }

        if ($cek_coupon->kuota_terpakai <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the coupon code cannot be used',
                'code' => 0,
            ], 400);
        }

        $booking = MBooking::where('id_booking',$id_booking)->first();

        if ($cek_coupon->satuan == 1) {
            if ($cek_coupon->maks_diskon == null) {
                $potongan = floor($cek_coupon->nominal / 100 * $booking->harga_total);
            }else {
                $dis = $cek_coupon->nominal / 100 * $booking->harga_total;
                if (floor($dis) > $cek_coupon->maks_diskon) {
                    $potongan = $cek_coupon->maks_diskon;
                }else {
                    $potongan = floor($dis);
                }
            }
        }else {
            $potongan = $cek_coupon->nominal;
        }
        $harga_potongan = $booking->harga_total - $potongan;

        MBooking::where('id_booking',$id_booking)->update(['id_kupon' => $cek_coupon->id_kupon, 'potongan_kupon' => $potongan, 'harga_total' => $harga_potongan]);

        $detail_booking = MBooking::from( 't_booking as a' )
            ->selectRaw('a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, c.nama_status_booking, CONCAT(e.nama_depan," ",e.nama_belakang) as nama_pemilik_properti, CONCAT(g.nama_depan," ",g.nama_belakang) as nama_pemesan, h.nama_tipe_properti')
            ->leftJoin('m_properti as b','a.id_ref', '=','b.id_ref_bahasa')
            ->leftJoin('m_status_booking as c','a.id_status_booking', '=','c.id_ref_bahasa')
            ->leftJoin('m_users as d','d.id_user', '=','b.created_by')
            ->leftJoin('m_customer as e','d.id_ref', '=','e.id')
            ->leftJoin('m_users as f','f.id_user', '=','a.id_user')
            ->leftJoin('m_customer as g','f.id_ref', '=','g.id')
            ->leftJoin('m_tipe_properti as h','h.id_ref_bahasa', '=','b.id_tipe_booking')
            ->where('h.id_bahasa',$id_bahasa)
            ->where('a.deleted',1)
            ->where('a.id_user',$user->id_user)
            ->where('b.deleted',1)
            ->where('b.id_bahasa',$id_bahasa)
            ->where('a.id_booking',$id_booking)
            ->where('c.id_bahasa',$id_bahasa)
            ->where('d.deleted',1)
            ->where('f.deleted',1)->get();
        
        $detail_booking_harga_satuan = MBookingHargaSatuan::where('id_booking',$id_booking)->get();
        // $detail_booking_properti_extra = MBookingPropertiExtra::where('id_booking',$id_booking)->get();
        $detail_booking_properti_extra = MBookingPropertiExtra::selectRaw('t_booking_properti_extra.*, m_properti_extra.nama_service as nama_properti_extra, m_properti_extra.tipe as tipe_properti_extra')->leftJoin('m_properti_extra','t_booking_properti_extra.id_properti_extra', '=','m_properti_extra.id_properti_extra')->where('t_booking_properti_extra.id_booking',$id_booking)->get();
        $detail_booking_extra = MBookingExtra::where('id_booking',$id_booking)->get();
        $detail_booking_discount = MBookingDiscount::where('id_booking',$id_booking)->get();

        $coupon = MKupon::find($cek_coupon->id_kupon);
        $coupon->kuota_terpakai = $coupon->kuota_terpakai - 1;
        $coupon->update();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'code' => 1,
            'detail_booking' => $detail_booking,
            'detail_payment' =>[
                'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
                'detail_booking_properti_extra' => $detail_booking_properti_extra,
                'detail_booking_extra' => $detail_booking_extra,
                'detail_booking_discount' => $detail_booking_discount,
            ]
        ], 200);        
    }

    public function post_like_review_rating(Request $request)
    {
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $id_review_rating = $request->id_review_rating;

        $cek = HReviewRatingLike::where('id_user',$user->id_user)->where('id_review_rating',$id_review_rating)->first();
        if ($cek == null) {
            $like = new HReviewRatingLike();
            $like->id_review_rating = $id_review_rating;
            $like->id_user = $user->id_user;
            $like->save();

            $rate = HReviewRating::find($id_review_rating);
            $rate->total_like = $rate->total_like + 1;
            $rate->update();

            return response()->json([                
                'code' => 1,
                'message' => 'Like review rating',
            ], 200);
        }else {
            if ($cek->deleted == 0) {
                $lik = HReviewRatingLike::find($cek->id);
                $lik->deleted = 1;
                $lik->updated_date = date('Y-m-d H:i:s');
                $lik->update();

                $rate = HReviewRating::find($id_review_rating);
                $rate->total_like = $rate->total_like + 1;
                $rate->update();

                return response()->json([                    
                    'code' => 1,
                    'message' => 'Like review rating',
                ], 200);
            }else {
                return response()->json([                    
                    'code' => 0,
                    'message' => 'Cannot like review rating',
                ], 400);
            }
        }
        
    }

    public function post_unlike_review_rating(Request $request)
    {
        $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $id_review_rating = $request->id_review_rating;

        $cek = HReviewRatingLike::where('id_user',$user->id_user)->where('id_review_rating',$id_review_rating)->first();

        if ($cek == null) {
            return response()->json([                    
                'code' => 0,
                'message' => 'Cannot unlike review rating',
            ], 400);
        }else {
            $lik = HReviewRatingLike::find($cek->id);
            $lik->deleted = 0;
            $lik->updated_date = date('Y-m-d H:i:s');
            $lik->update();

            $rate = HReviewRating::find($id_review_rating);
            $rate->total_like = $rate->total_like - 1;
            $rate->update();

            return response()->json([                    
                'code' => 1,
                'message' => 'Unlike review rating',
            ], 200);
        }
        
    }
}
