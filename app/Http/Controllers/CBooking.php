<?php

namespace App\Http\Controllers;

use App\Models\MBooking;
use App\Models\MProperti;
use App\Models\MPropertiHargaPeriode;
use App\Models\MStatusBooking;
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingPropertiExtra;
use App\Models\MBookingExtra;
use App\Models\MBookingDiscount;
use App\Models\MNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\Helper;
use DataTables;
use PDF;
use App\Mail\EmailBooking;
use Mail;
use App\Models\HPesan;
use App\Models\HPesanDetail;
use App\Services\Firestore;
use Google\Cloud\Firestore\DocumentReference;
use Carbon\Carbon;

class CBooking extends Controller
{
    use Helper;

    public function index()
    {
        $status = MStatusBooking::where('id_bahasa',1)->get();
        return view('booking.index')
            ->with('status',$status)            
            ->with('title','Booking');
    }
    public function detail($id)
    {        
        $data = MBooking::selectRaw('t_booking.*, m_properti.judul as judul, m_users.username as username, m_properti.id_tipe_booking as tipe_booking, m_status_booking.nama_status_booking as nama_status_booking, m_customer.nama_depan, m_customer.nama_belakang')
            ->join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
            ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_ref_bahasa','left')
            ->join('m_users','t_booking.id_user','m_users.id_user','left')
            ->join('m_customer','m_customer.id','m_users.id_ref','left')
            ->where('m_status_booking.id_bahasa',1)
            ->where('t_booking.id_booking',$id)
            ->orWhere('t_booking.kode_booking',$id)
            ->first();
        $harga_satuan = MBookingHargaSatuan::where('id_booking',$data->id_booking)->get();
        $extra_service = MBookingPropertiExtra::where('id_booking',$data->id_booking)
            ->join('m_properti_extra','m_properti_extra.id_properti_extra','t_booking_properti_extra.id_properti_extra')
			->select('t_booking_properti_extra.*','m_properti_extra.nama_service')->get();
        $extra = MBookingExtra::where('id_booking',$data->id_booking)->get();
        $discount = MBookingDiscount::where('id_booking',$data->id_booking)->get();
        return view('booking.detail')
            ->with('data',$data)
            ->with('harga_satuan',$harga_satuan)
            ->with('extra_service',$extra_service)
            ->with('extra',$extra)
            ->with('discount',$discount)
            ->with('id',$data->id_booking)
            ->with('title','Booking')
            ->with('titlePage','Detail');
            // $pdf = PDF::loadview('pdf.invoice',['kode_booking'=>$id]);
            // return $pdf->download('pdfview.pdf');
    }
    public function confirm($id)
    {
        MBooking::where('id_booking',$id)->update(['id_status_booking' => 2]);
        $booking = MBooking::selectRaw('m_customer.nama_depan,m_customer.nama_belakang, m_users.id_user, m_users.id_ref, m_users.email, m_properti.nama_properti, t_booking.tanggal_mulai, t_booking.tanggal_selesai, t_booking.jam_mulai, t_booking.jam_selesai, t_booking.kode_booking, m_properti.judul, m_properti.id_tipe_booking')
        ->join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
        ->leftJoin('m_users','m_users.id_user','=','t_booking.id_user')
        ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
        ->where('id_booking',$id)->first();
        $id_booking=$id;
        // dd($booking);
        $pdf = PDF::loadview('pdf.invoice',['kode_booking'=>$booking->kode_booking]);
        // $this->kirim_email($booking->email,$booking->nama_depan,$booking->nama_belakang,null,null,$booking->nama_properti,$booking->tanggal_mulai,'email.mailBooking','Availability Confirmation - ORDER ID #'.$booking->kode_booking.' - Villanesia',$id,null);
        // return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
        Mail::to($booking->email)->send(new EmailBooking($booking->nama_depan,$booking->nama_belakang,$booking->nama_properti,$booking->tanggal_mulai,'email.mailBooking','Availability Confirmation - ORDER ID #'.$booking->kode_booking.' - Villanesia',$id,$pdf->output()));

            $judul_p = 'Booking #'.$booking->kode_booking.' - '.$booking->judul;
			$id_user_pengirim = $booking->id_user;
			$id_user_penerima = 1;
			$pesan_terakhir = 'Confirm Availability';
			$id_ref_p = $booking->kode_booking;
            
            $hpesan = HPesan::where('id_ref',$id_ref_p)->first();

			$hdetail = new HPesanDetail;
			$hdetail->id_pesan = $hpesan->id_pesan;
			$hdetail->id_ref = $id_ref_p;
			$hdetail->id_tipe = 2;
			$hdetail->pesan = $pesan_terakhir;
			$hdetail->id_user = $id_user_pengirim;
			$hdetail->url = $id;
			$hdetail->save();

            $firestore = Firestore::get();
			$fireDetail = $firestore->collection('h_pesan_detail')->newDocument();
			$fireDetail->set([    
				'id_pesan_detail' => $hdetail->id_pesan_detail,
				'id_pesan' => $hpesan->id_pesan,
				'id_ref' => $id_ref_p,
				'id_tipe' => 2,
				'url' => $id,
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

        return response()->json(['status'=>true,'msg'=>'Sukses Mengubah Data']);
    }
    public function decline($id,Request $request)
    {
        $book = MBooking::where('id_booking',$id)->first();

        $judul = 'Admin has declined your order';
        $isi = "Sorry, inquiry #".$book->kode_booking." can't be accepted due to availability";
        $route = 'detailJurney';
        $user = $book->id_user;
        $param = '{"id_ref":'.$id.'}';
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

        MBooking::where('id_booking',$id)->update(['id_status_booking' => 3, 'alasan_reject' => $request->decline]);
        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function extra($id,Request $request)
    {
        if ($request->nama_biaya_extra) {
            $total = 0;
            for ($i=0; $i < count($request->nama_biaya_extra); $i++) {
                $arr_extra[] = [                    
                    'id_booking' => $id,
                    'nama_biaya_extra' => $request->nama_biaya_extra[$i],
                    'harga' => $request->harga[$i],
                ];
                $total = $total + $request->harga[$i];
            }
            MBookingExtra::where('id_booking',$id)->delete();
            MBookingExtra::insert($arr_extra);
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + $total - $tot->total_booking_discount;
            MBooking::where('id_booking',$id)->update(['total_booking_extra' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service + 0 - $tot->total_booking_discount;
            MBooking::where('id_booking',$id)->update(['total_booking_extra' => 0, 'harga_total' => $all]);
            MBookingExtra::where('id_booking',$id)->delete();
        }

        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function discount($id,Request $request)
    {
        if ($request->nama_biaya_discount) {
            $total = 0;
            for ($i=0; $i < count($request->nama_biaya_discount); $i++) {
                $arr_discount[] = [                    
                    'id_booking' => $id,
                    'nama_biaya_discount' => $request->nama_biaya_discount[$i],
                    'harga' => $request->harga[$i],
                ];
                $total = $total + $request->harga[$i];
            }
            MBookingDiscount::where('id_booking',$id)->delete();
            MBookingDiscount::insert($arr_discount);
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - $total + $tot->total_booking_extra;
            MBooking::where('id_booking',$id)->update(['total_booking_discount' => $total, 'harga_total' => $all]);
        }else {
            $tot = MBooking::where('id_booking',$id)->first();
            $all = $tot->harga_final_properti + $tot->cleaning_fee + $tot->security_deposit + $tot->nominal_pajak + $tot->total_extra_service - 0 + $tot->total_booking_extra;
            MBooking::where('id_booking',$id)->update(['harga_total' => $all, 'total_booking_discount' => 0]);
            MBookingDiscount::where('id_booking',$id)->delete();
        }

        return redirect()->to('/booking/detail/'.$id)->with('msg','Sukses Menambahkan Data');
    }
    public function data(Request $request)
    {        
        if ($request->status == 0) {
            $model = MBooking::join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
                ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_ref_bahasa','left')
                ->selectRaw('t_booking.*, m_properti.judul as judul, m_properti.alamat as alamat, m_properti.id_tipe_booking as tipe_booking, m_properti.binatang as binatang, m_status_booking.nama_status_booking as nama_status_booking')
                ->where('m_status_booking.id_bahasa',1)
                ->where('t_booking.deleted',1)
                ->orderBy('t_booking.created_date','desc');
        }else {
            $model = MBooking::join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
                ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_ref_bahasa','left')
                ->selectRaw('t_booking.*, m_properti.judul as judul, m_properti.alamat as alamat, m_properti.id_tipe_booking as tipe_booking, m_properti.binatang as binatang, m_status_booking.nama_status_booking as nama_status_booking')
                ->where('m_status_booking.id_bahasa',1)
                ->where('m_status_booking.id_status_booking',$request->status)
                ->where('t_booking.deleted',1)
                ->orderBy('t_booking.created_date','desc');
        }
        return DataTables::eloquent($model)
            ->addColumn('action', function ($row) {
                if ($row->id_status_booking == 1) {
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-success text-light">Confirm</a>';
                }else{
                    $btn = '<a href="'.url('booking/detail/'.$row->id_booking).'" class="btn btn-primary text-light">Detail</a>';
                }
                return $btn;
            })
            ->addColumn('status', function ($row) {
                if ($row->id_status_booking == 1) {
                    $btn = '<p class="badge badge-primary text-white">New</p>';
                }elseif ($row->id_status_booking == 5 || $row->id_status_booking == 2) {
                    $btn = '<p class="badge badge-success text-white">Waiting Payment</p>';
                }else{
                    $btn = '<p class="badge badge-danger text-white">'.$row->nama_status_booking.'</p>';
                }
                    // $btn = '<p style="white-space: nowrap">'.$row->nama_status_booking.'</p>';
                return $btn;
            })
            ->addColumn('date', function ($row) {                                
                $date = date('d-m-Y H:i', strtotime($row->created_date));
                return $date;
            })
            ->addColumn('alamat', function ($row) {                                
                $date = '<div style="width:600px"><p>'.$row->judul.'</p><p>'.$row->alamat.'</p></div>';
                return $date;
            })
            ->addColumn('in', function ($row) {
                if ($row->tipe_booking == 5) {                    
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai.' '.$row->jam_mulai)).' at '.date('H:i', strtotime($row->tanggal_mulai.' '.$row->jam_mulai)).'</p>';
                }else {
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai)).'</p>';
                }
                return $date;
            })
            ->addColumn('out', function ($row) {
                if ($row->tipe_booking == 5) {                    
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_mulai.' '.$row->jam_selesai)).' at '.date('H:i', strtotime($row->tanggal_mulai.' '.$row->jam_selesai)).'</p>';
                }else {
                    $date = '<p>'.date('d-m-Y', strtotime($row->tanggal_selesai)).'</p>';
                }
                return $date;
            })
            ->addColumn('tamu', function ($row) {                
                $date = ($row->tamu_dewasa + $row->tamu_anak + $row->tamu_bayi);
                return $date;
            })
            ->addColumn('pet', function ($row) {                
                if ($row->binatang == 1) {
                    $data = "Yes";
                }else {
                    $data = "No";
                }
                return $data;
            })
            ->addColumn('harga_total', function ($row) {                
                $nominal = "<p>Rp.".$this->ribuan(ceil($row->harga_total))."</p>";
                return $nominal;
            })
            ->editColumn('kode_booking', function ($row) {                
                $nominal = "#".$row->kode_booking;
                return $nominal;
            })
            ->rawColumns(['action','alamat','in','out','harga_total','status'])
            ->addIndexColumn()
            ->toJson();
    }
    public function cek_tanggal(Request $request)
    {   
        $tanggal_mulai = $request->date_in;
        $tanggal_selesai = $request->date_out;

        if ($request->date_out < $request->date_in) {                
            return response()->json([
                'status' => false,
                'msg' => 'Check out date must be greater then check in date',                    
            ]);
        }
        $book = MBooking::find($request->id);
        $pro = MProperti::find($book->id_ref);
        $cus = MPropertiHargaPeriode::where('id_properti',$book->id_ref)->get();
        $data = MBooking::selectRaw('count(id_ref) as total')
                    ->where('tanggal_selesai','>',date('Y-m-d', strtotime($request->date_in)))
                    ->where('tanggal_mulai','<=',date('Y-m-d', strtotime($request->date_out)))
                    ->where('id_status_booking','!=',4)
                    ->where('id_tipe',$book->id_tipe)
                    ->where('id_ref',$book->id_ref)
                    ->first();
        // dd($data->total);
        if ($data->total != 0) {
            return response()->json([
                    'status' => false,
                    'msg' => 'Your dates are not available',                    
                ]);
        }else {
            MBookingHargaSatuan::where('id_booking',$request->id)->delete();
            $this->harga_booking_satuan($book,$pro,$tanggal_mulai,$tanggal_selesai,$cus);
            $extra_service = MBookingPropertiExtra::where('id_booking',$request->id)->get();

            $durasi = $this->durasi_inap($pro,$tanggal_mulai,$tanggal_selesai);
            $durasi_inap = $durasi['durasi_inap'];
            $durasi_inap_jam = $durasi['durasi_inap_jam'];

                $harga_final_properti = MBookingHargaSatuan::where('id_booking',$book->id_booking)->get()->sum('harga_final');
                $total_extra_service = MBookingPropertiExtra::where('id_booking',$book->id_booking)->get()->sum('harga_final');

                $data_update = [
                    'tanggal_mulai' => date('Y-m-d', strtotime($request->date_in)),
                    'tanggal_selesai' => date('Y-m-d', strtotime($request->date_out)),
                    'durasi_inap' => $durasi_inap,
                    'durasi_inap_jam' => $durasi_inap_jam,
                    'harga_final_properti' => $harga_final_properti,
                    'total_extra_service' => $total_extra_service,
                    'harga_total' => $harga_final_properti + $book->biaya_kebersihan + $book->total_extra_service + $pro->uang_jaminan + $book->nominal_pajak,
                ];
                MBooking::where('id_booking',$book->id_booking)->update($data_update);

                return response()->json([
                    'status' => true,
                    'msg' => 'Your dates available',                    
                ]);        
        }
    }

    function harga_booking_satuan($book,$pro,$tanggal_mulai,$tanggal_selesai,$cus)
    {
        
        $tamu_tambahan = ((($book->tamu_dewasa+$book->tamu_anak) - $pro->jumlah_tamu) < 0 ? 0 : (($book->tamu_dewasa+$book->tamu_anak) - $pro->jumlah_tamu));

        $durasi = $this->durasi_inap($pro,$tanggal_mulai,$tanggal_selesai);
        $durasi_inap = $durasi['durasi_inap'];
        $durasi_inap_jam = $durasi['durasi_inap_jam'];

            if ($pro->id_tipe_booking == 1) {            
                if ($pro->harga_weekly != null && $pro->harga_monthly != null) {                
                    if ($durasi_inap > 30) {
                        $harga_final_properti = $pro->harga_monthly * $durasi_inap;
                    }elseif ($durasi_inap > 7 && $durasi_inap <= 30) {
                        $harga_final_properti = $pro->harga_weekly * $durasi_inap;
                    }else {
                        $harga_final_properti = $pro->harga_tampil * $durasi_inap;
                    }
                }else {
                    $harga_final_properti = $pro->harga_tampil * $durasi_inap;
                }

                if($tamu_tambahan > 0){
                    $harga_final_properti_tamu = $pro->harga_tampil * $durasi_inap * $tamu_tambahan;
                    $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti_tamu, $durasi_inap, 0);
                }

                $this->booking_harga($book->id_booking, 1, 1, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti, $durasi_inap, 0);
            }elseif ($pro->id_tipe_booking == 2) {            
                $date1 = $this->get_date_by_input($tanggal_mulai,$tanggal_selesai);
                $date2 = $this->get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai);
                $date_week = array_values(array_diff($date1,$date2));
                $date_cus = array_values(array_intersect($date1,$date2));
                // dd('first :'.$date_week[0].' last :'.end($date_week));
                // dd(date('Y-m-d', strtotime(end($date_cus).' +1 day')));
                // dd($date2);
                if (count($date_cus) > 0) {
                    $har_cus = $this->get_harga_cus_by_input($date_cus,$cus);                
                    $this->booking_harga($book->id_booking, 1, 1, null, null, $date_cus[0], date('Y-m-d', strtotime(end($date_cus).' +1 day')), $har_cus['harga_tampil'], $har_cus['harga'], count($date_cus), 1);

                    if ($tamu_tambahan > 0) {
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_cus['tamu'];
                        $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, $date_cus[0], date('Y-m-d', strtotime(end($date_cus).' +1 day')), $har_cus['tamu'], $tamu_add, count($date_cus), 1);
                    }
                }

                if (count($date_week) > 0) {
                    if ($pro->harga_weekend == null) {
                        //$fin = $pro->harga_tampil * $durasi_inap;
                        $fin = $pro->harga_tampil * count($date_week);
                        $this->booking_harga($book->id_booking, 1, 1, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $pro->harga_tampil, $fin, count($date_week), 0);
                    }elseif ($pro->harga_weekend != null) {
                        $final = $this->get_harga_by_input($date_week,$pro);
                        $fin = $final['harga'];
                        $this->booking_harga($book->id_booking, 1, 1, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $pro->harga_tampil, $fin, count($date_week), 0);
                    }

                    if ($tamu_tambahan > 0) {
                        $har_week = $this->get_harga_by_input($date_week,$pro);
                        $tamu_add = $tamu_tambahan * $durasi_inap * $har_week['tamu'];                

                        $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, $date_week[0], date('Y-m-d', strtotime(end($date_week).' +1 day')), $har_week['tamu'], $tamu_add, count($date_week), 0);
                    }
                }
                            
            }elseif ($pro->id_tipe_booking == 3) {
                $harga_final_properti = $pro->harga_tampil * $durasi_inap;
                $this->booking_harga($book->id_booking, 1, 1, null, null, $tanggal_mulai, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 7).' day')), $pro->harga_tampil, $harga_final_properti, $durasi_inap, 0);

                if($book->extra_hari > 0){
                    $harga_final_properti_extra = ($pro->harga_tampil * $durasi_inap) + floor($pro->harga_tampil / 7 * $book->extra_hari);

                    $this->booking_harga($book->id_booking, 1, $book->extra_hari, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 7).' day')), $tanggal_selesai, floor($pro->harga_tampil / 7), $harga_final_properti_extra, $durasi_inap, 0);
                }

                if($tamu_tambahan > 0){
                    if ($pro->harga_tamu_tambahan != null) {                    
                        $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $durasi_inap * $tamu_tambahan;                    
                    }else {
                        $harga_final_properti_tamu = 0;
                    }

                    $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 7).' day')), $pro->harga_tamu_tambahan, $harga_final_properti_tamu, $durasi_inap, 0);
                    
                    if($book->extra_hari > 0){
                        $harga_final_properti_tamu_extra = ($pro->harga_tamu_tambahan * $durasi_inap * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 7 * $tamu_tambahan * $book->extra_hari);

                        $this->booking_harga($book->id_booking, 2, ($tamu_tambahan * $book->extra_hari), null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 7).' day')), $tanggal_selesai, floor($pro->harga_tamu_tambahan / 7), $harga_final_properti_tamu_extra, $durasi_inap, 0);
                    }
                }
            }elseif ($pro->id_tipe_booking == 4) {            
                $harga_final_properti = $pro->harga_tampil * $durasi_inap;
                $this->booking_harga($book->id_booking, 1, 1, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti, $durasi_inap, 0);
                
                if($book->extra_hari > 0){
                    $harga_final_properti_extra = ($pro->harga_tampil * $durasi_inap) + floor($pro->harga_tampil / 30 * $book->extra_hari);
                    
                    $this->booking_harga($book->id_booking, 1, $book->extra_hari, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 30).' day')), $tanggal_selesai, floor($pro->harga_tampil / 30), $harga_final_properti_extra, $durasi_inap, 0);
                }

                if($tamu_tambahan > 0){
                    if ($pro->harga_tamu_tambahan != null) {                    
                        $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $durasi_inap * $tamu_tambahan;                    
                    }else {
                        $harga_final_properti_tamu = 0;
                    }

                    $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, $tanggal_mulai, $tanggal_selesai, $pro->harga_tampil, $harga_final_properti_tamu, $durasi_inap, 0);
                    
                    if($book->extra_hari > 0){
                        $harga_final_properti_tamu_extra = ($pro->harga_tamu_tambahan * $durasi_inap * $tamu_tambahan) + floor($pro->harga_tamu_tambahan / 30 * $tamu_tambahan * $book->extra_hari);
                        
                        $this->booking_harga($book->id_booking, 2, $tamu_tambahan, null, null, date('Y-m-d', strtotime($tanggal_mulai.' +'.($durasi_inap * 30).' day')), $tanggal_selesai, floor($pro->harga_tampil / 30), $harga_final_properti_tamu, $durasi_inap, 0);
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
                $this->booking_harga($book->id_booking, 1, 1, $jam_mulai, $jam_selesai, null, null, $pro->harga_tampil, $final, $durasi_inap_jam, 0);

                if ($tamu_tambahan > 0) {
                    $harga_final_properti_tamu = $pro->harga_tamu_tambahan * $tamu_tambahan;
                    
                    $this->booking_harga($book->id_booking, 2, $tamu_tambahan, $jam_mulai, $jam_selesai, null, null, $pro->harga_tampil, $harga_final_properti_tamu, $durasi_inap_jam, 0);
                }
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

    function durasi_inap($pro,$tanggal_mulai,$tanggal_selesai)
    {
        $extra = 0;
            if ($pro->id_tipe_booking == 1) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai.' -1 day'));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $durasi_inap = $to->diffInDays($from);
                $extra = 0;
                $durasi_inap_jam = 0;
            }elseif ($pro->id_tipe_booking == 2) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $durasi_inap = $to->diffInDays($from);
                $durasi_inap_jam = 0;
                $extra = 0;
            }elseif ($pro->id_tipe_booking == 3) {
                $mulai = date('Y-m-d', strtotime($tanggal_mulai));
                $selesai = date('Y-m-d', strtotime($tanggal_selesai));
                $from = Carbon::parse($mulai);
                $to = Carbon::parse($selesai);
                $diff_in_hours = $to->diffInDays($from);
                // dd($diff_in_hours);
                $durasi_inap = floor($diff_in_hours / 7);
                $durasi_inap_jam = 0;
                $extra = $diff_in_hours % 7;
            }elseif ($pro->id_tipe_booking == 4) {
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
        $data['extra'] = $extra;
        $data['durasi_inap'] = $durasi_inap;
        $data['durasi_inap_jam'] = $durasi_inap_jam;
        return $data;
    }

    function properti_extra($pro,$book,$extra_service)
    {
        if ($extra_service != null) {                            
            $id_extra = [];
            foreach ($extra_service as $key) {                
                $ser = MPropertiExtra::find($key->id_properti_extra);
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
                $book->id_booking = $book->id_booking;
                $book->id_properti_extra = $ser->id_properti_extra;
                $book->harga_satuan = $ser->harga;
                $book->harga_final = $sevice;
                $book->save();

                $id_extra[]=$key->id;
            }
            MBookingPropertiExtra::whereIn('id',$id_extra)->delete();
        }
    }
}
