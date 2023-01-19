<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MProperti;
use App\Models\MPropertiKamarTidur;
use App\Models\MPropertiExtra;
use App\Models\MPropertiGalery;
use App\Models\MPropertiHargaPeriode;
use App\Models\MBooking;
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingExtra;
use App\Models\MBookingPropertiExtra;
use App\Models\MBookingDiscount;
use App\Models\MApiKey;
use App\Models\HPesan;
use App\Models\HPesanDetail;
use App\Models\User;
use App\Models\MNotif;

use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Auth;
use Carbon\Carbon;
use App\Traits\Helper;
use App\Services\Firestore;
use Google\Cloud\Firestore\Timestamp;

class CAIntegrasi extends Controller
{
    use Helper;

    public function post_property_booking(Request $request)
    {
        // $user = MApiKey::where('token',$request->header('auth-key'))->first();
        $user = User::where('id_user',1)->first();
		Auth::login($user);
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
        $id_user = $request->id_user;
        $kode_booking = $request->kode_booking;
        
        $customer = User::join('m_customer','m_customer.id','m_users.id_ref')
                    ->select('m_customer.nama_depan')
                    ->where('m_users.id_user',$id_user)
                    ->first();

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
            // $squencedtoday = MBooking::where('deleted',1)->get()->count();
            // $squence = 1000+$squencedtoday+1;            
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
            $tipe->kode_booking = $kode_booking;
            $tipe->id_user = $id_user;
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
                // $judul_p = '#'.$kode_booking.' - '.$customer->nama_depan;
                // $id_user_pengirim = $id_user;
                // $id_user_penerima = 1;
                // $pesan_terakhir = ($catatan == null ? 'check availability for '.date('d-m-Y', strtotime($tanggal_mulai)).' to '.date('d-m-Y', strtotime($tanggal_selesai)) : $catatan);
                // $id_ref_p = $kode_booking;
                // $judul_mobile = '#'.$kode_booking.' - '.$pro->judul;

                // $hpesan = new HPesan;
                // $hpesan->judul = $judul_p;
                // $hpesan->id_user_pengirim = $id_user_pengirim;
                // $hpesan->id_user_penerima = $id_user_penerima;
                // $hpesan->pesan_terakhir = $pesan_terakhir;
                // $hpesan->waktu_pesan_terakhir = date('Y-m-d H:i:s');
                // $hpesan->id_ref = $id_ref_p;
                // $hpesan->id_booking = $tipe->id_booking;
                // $hpesan->judul_mobile = $judul_mobile;
                // // $hpesan->updated_date = date('Y-m-d H:i:s');
                // $hpesan->save();

                // $hdetail = new HPesanDetail;
                // $hdetail->id_pesan = $hpesan->id_pesan;
                // $hdetail->id_ref = $id_ref_p;
                // $hdetail->id_tipe = 3;
                // $hdetail->pesan = $pesan_terakhir;
                // $hdetail->id_user = $id_user_pengirim;
                // $hdetail->save();
                
                // // $timestamp = Timestamp::fromDate(date('Y-m-d H:i:s'));
                // $firestore = Firestore::get();
                // $firePesan = $firestore->collection('h_pesan')->newDocument();
                // $firePesan->set([    
                //     'badge' => 1,
                //     'created_date' => date('Y-m-d H:i:s'),
                //     'id_pesan' => $hpesan->id_pesan,
                //     'id_ref' => $id_ref_p,
                //     'id_user_penerima' => $id_user_penerima,
                //     'id_user_pengirim' => $id_user_pengirim,
                //     'judul' => $judul_p,
                //     'id_booking' => $tipe->id_booking,
                //     'judul_mobile' => $judul_mobile,
                //     'penerima_lihat' => 0,
                //     'pengirim_lihat' => 0,
                //     'pesan_terakhir' => '',//$pesan_terakhir,
                //     'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
                //     'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
                // ]);

                // $fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
                // $fireDetail->set([    
                //     'id_pesan_detail' => $hdetail->id_pesan_detail,
                //     'id_pesan' => $hpesan->id_pesan,
                //     'id_ref' => $id_ref_p,
                //     'id_tipe' => 3,
                //     'url' => "",
                //     'pesan' => $pesan_terakhir,
                //     'created_date' => date('Y-m-d H:i:s'),
                //     'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
                //     'id_user' => $id_user_pengirim,
                // ]);

                // $query = $firestore->collection('h_pesan')
                // ->where('id_pesan', '=', $hpesan->id_pesan);
            
                // $documents = $query->documents();        
                // $id = null;
                // foreach ($documents as $document) {
                //     $id = $document->id();
                //     $doc = $firestore->collection('h_pesan')->document($id)
                //         ->set([
                //             'badge' => $document['badge'],
                //             'created_date' => $document['created_date'],
                //             'id_pesan' => $document['id_pesan'],
                //             'id_ref' => $document['id_ref'],
                //             'id_user_penerima' => $document['id_user_penerima'],
                //             'id_user_pengirim' => $document['id_user_pengirim'],
                //             'judul' => $document['judul'],
                //             'id_booking' => $document['id_booking'],
                //             'judul_mobile' => $document['judul_mobile'],
                //             'penerima_lihat' => $document['penerima_lihat'],
                //             'pengirim_lihat' => $document['pengirim_lihat'],
                //             'pesan_terakhir' => $pesan_terakhir,
                //             'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
                //             'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
                //         ]);
                // }

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

    public function post_property_booking_extra_expenses(Request $request)
    {
        $kode_booking = $request->kode_booking;
        $nama_biaya_extra = explode(",",$request->nama_biaya_extra);
        $harga = explode(",",$request->harga);

        $cek_kode = MBooking::where('kode_booking',$kode_booking)->first();
        if ($cek_kode == null) {
            return response()->json([
                'success' => false,
                'message' => 'booking code not found',
                'code' => 0,
            ], 400);
        }

        if ($request->nama_biaya_extra) {
            $total = 0;
            for ($i=0; $i < count($nama_biaya_extra); $i++) {
                $arr_extra[] = [                    
                    'id_booking' => $cek_kode->id_booking,
                    'nama_biaya_extra' => $nama_biaya_extra[$i],
                    'harga' => $harga[$i],
                ];
                $total = $total + $harga[$i];
            }
            MBookingExtra::where('id_booking',$cek_kode->id_booking)->delete();
            MBookingExtra::insert($arr_extra);
            $tot = MBooking::where('id_booking',$cek_kode->id_booking)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + $total - $tot->total_booking_discount;
            MBooking::where('id_booking',$cek_kode->id_booking)->update(['total_booking_extra' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$cek_kode->id_booking)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + 0 - $tot->total_booking_discount;
            MBooking::where('id_booking',$cek_kode->id_booking)->update(['total_booking_extra' => 0, 'harga_total' => $all]);
            MBookingExtra::where('id_booking',$cek_kode->id_booking)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Success add extra expenses',
            'code' => 1,
        ], 200);
    }

    public function post_property_booking_discount(Request $request)
    {
        $kode_booking = $request->kode_booking;
        $nama_biaya_discount = explode(",",$request->nama_biaya_discount);
        $harga = explode(",",$request->harga);

        $cek_kode = MBooking::where('kode_booking',$kode_booking)->first();
        if ($cek_kode == null) {
            return response()->json([
                'success' => false,
                'message' => 'booking code not found',
                'code' => 0,
            ], 400);
        }

        if ($nama_biaya_discount) {
            $total = 0;
            for ($i=0; $i < count($nama_biaya_discount); $i++) {
                $arr_discount[] = [                    
                    'id_booking' => $cek_kode->id_booking,
                    'nama_biaya_discount' => $nama_biaya_discount[$i],
                    'harga' => $harga[$i],
                ];
                $total = $total + $harga[$i];
            }
            MBookingDiscount::where('id_booking',$cek_kode->id_booking)->delete();
            MBookingDiscount::insert($arr_discount);
            $tot = MBooking::where('id_booking',$cek_kode->id_booking)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - $total + $tot->total_booking_extra;
            MBooking::where('id_booking',$cek_kode->id_booking)->update(['total_booking_discount' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$cek_kode->id_booking)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - 0 + $tot->total_booking_extra;
            MBooking::where('id_booking',$cek_kode->id_booking)->update(['harga_total' => $all, 'total_booking_discount' => 0]);
            MBookingDiscount::where('id_booking',$cek_kode->id_booking)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Success add extra expenses',
            'code' => 1,
        ], 200);
    }

    function post_property_booking_confirm(Request $request)
    {
        $kode_booking = $request->kode_booking;

        MBooking::where('kode_booking',$kode_booking)->update(['id_status_booking' => 2]);
        $booking = MBooking::selectRaw('t_booking.id_user, m_properti.nama_properti, t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.jam_mulai, t_booking.jam_selesai, t_booking.kode_booking, m_properti.judul, m_properti.id_tipe_booking')
        ->join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
        ->leftJoin('m_users','m_users.id_user','=','t_booking.id_user')
        ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
        ->where('kode_booking',$kode_booking)->first();
        $id_booking=$booking->id_booking;
        // dd($booking);
        $user = User::selectRaw('m_customer.nama_depan,m_customer.nama_belakang, m_users.id_user, m_users.id_ref, m_users.email')
                ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
                ->where('m_users.id_user',$booking->id_user)
                ->first();
        if ($user != null) {            
            $pdf = PDF::loadview('pdf.invoice',['kode_booking'=>$booking->kode_booking]);
            // $this->kirim_email($booking->email,$booking->nama_depan,$booking->nama_belakang,null,null,$booking->nama_properti,$booking->tanggal_mulai,'email.mailBooking','Availability Confirmation - ORDER ID #'.$booking->kode_booking.' - Villanesia',$id,null);
            // return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
            Mail::to($user->email)->send(new EmailBooking($user->nama_depan,$user->nama_belakang,$booking->nama_properti,$booking->tanggal_mulai,'email.mailBooking','Availability Confirmation - ORDER ID #'.$booking->kode_booking.' - Villanesia',$id,$pdf->output()));
        }

            // $judul_p = 'Booking #'.$booking->kode_booking.' - '.$booking->judul;
			// $id_user_pengirim = $booking->id_user;
			// $id_user_penerima = 1;
			// $pesan_terakhir = 'Confirm Availability';
			// $id_ref_p = $booking->kode_booking;
            
            // $hpesan = HPesan::where('id_ref',$id_ref_p)->first();

			// $hdetail = new HPesanDetail;
			// $hdetail->id_pesan = $hpesan->id_pesan;
			// $hdetail->id_ref = $id_ref_p;
			// $hdetail->id_tipe = 2;
			// $hdetail->pesan = $pesan_terakhir;
			// $hdetail->id_user = $id_user_pengirim;
			// $hdetail->url = $id;
			// $hdetail->save();

            // $firestore = Firestore::get();
			// $fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
			// $fireDetail->set([    
			// 	'id_pesan_detail' => $hdetail->id_pesan_detail,
			// 	'id_pesan' => $hpesan->id_pesan,
			// 	'id_ref' => $id_ref_p,
			// 	'id_tipe' => 2,
			// 	'url' => $id,
			// 	'pesan' => $pesan_terakhir,
			// 	'created_date' => date('Y-m-d H:i:s'),
			// 	'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
			// 	'id_user' => $id_user_pengirim,
			// ]);

			// $query = $firestore->collection('h_pesan')
			// ->where('id_pesan', '=', $hpesan->id_pesan);
		
			// $documents = $query->documents();        
			// $id = null;
			// foreach ($documents as $document) {
			// 	$id = $document->id();
			// 	$doc = $firestore->collection('h_pesan')->document($id)
			// 		->set([
			// 			'badge' => $document['badge'],
			// 			'created_date' => $document['created_date'],
			// 			'id_pesan' => $document['id_pesan'],
			// 			'id_ref' => $document['id_ref'],
			// 			'id_user_penerima' => $document['id_user_penerima'],
			// 			'id_user_pengirim' => $document['id_user_pengirim'],
			// 			'judul' => $document['judul'],
            //             'id_booking' => $document['id_booking'],
            //             'judul_mobile' => $document['judul_mobile'],
			// 			'penerima_lihat' => $document['penerima_lihat'],
			// 			'pengirim_lihat' => $document['pengirim_lihat'],
			// 			'pesan_terakhir' => $pesan_terakhir,
			// 			'updated_date' => new \Google\Cloud\Core\Timestamp(new \DateTime(date('Y-m-d H:i:s'))),
			// 			'waktu_pesan_terakhir' => date('Y-m-d H:i:s')
			// 		]);
			// }
        if ($booking->id_tipe_booking == 5) {
            $isi = 'Inquiry #'.$booking->kode_booking.' - '.$booking->judul.' for '.date('d-m-Y',strtotime($booking->tanggal_mulai)).' at '.$booking->jam_mulai.' to '.date('d-m-Y',strtotime($booking->tanggal_mulai)).' at '.$booking->jam_selesai.', has been confirmed';
        }else {
            $isi = 'Inquiry #'.$booking->kode_booking.' - '.$booking->judul.' for '.date('d-m-Y',strtotime($booking->tanggal_mulai)).' to '.date('d-m-Y',strtotime($booking->tanggal_selesai)).', has been confirmed';
        }
        
        $judul = 'Admin has confirmed the availability of the villa';
        $route = 'detailJurney';
        $user = $booking->id_user;
        $param = '{"id_ref":'.$id_booking.'}';
        // dd($user);
        $notif = new MNotif();
        $notif->id_user = $user;
        $notif->judul = $judul;
        $notif->isi = $isi;
        $notif->route = $route;
        $notif->param = $param;
        $notif->save();

        $ceks = $this->send_fcm($judul,$isi,$route,$param,$user,$notif->id_notif);
        // dd($ceks);

        return response()->json([
            'success' => true,
            'message' => 'Success confirm booking',
            'code' => 1,
        ], 200);
    }

    function post_property_booking_decline(Request $request)
    {
        $kode_booking = $request->kode_booking;
        $alasan = $request->alasan;

        $book = MBooking::where('kode_booking',$kode_booking)->first();

        $judul = 'Admin has declined your order';
        $isi = "Sorry, inquiry #".$kode_booking." can't be accepted due to availability";
        $route = 'detailJurney';
        $user = $book->id_user;
        $param = '{"id_ref":'.$book->id_booking.'}';
        // dd($user);
        $notif = new MNotif();
        $notif->id_user = $user;
        $notif->judul = $judul;
        $notif->isi = $isi;
        $notif->route = $route;
        $notif->param = $param;
        $notif->save();

        $ceks = $this->send_fcm($judul,$isi,$route,$param,$user,2222);
        // dd($ceks);

        MBooking::where('id_booking',$book->id_booking)->update(['id_status_booking' => 3, 'alasan_reject' => $request->decline]);

        return response()->json([
            'success' => true,
            'message' => 'Success decline booking',
            'code' => 1,
        ], 200);
    }

    public function post_property_booking_cancel(Request $request)
    {        
        $kode_booking = $request->kode_booking;
        $alasan = $request->alasan;
    
        $prop = MBooking::where('kode_booking',$kode_booking)->update(['id_status_booking' => 4, 'alasan_cancel' => $alasan]);

        return response()->json([
            'success' => true,
            'message' => 'booking cancel',
            'code' => 1,            
        ], 200);        
    }
}
