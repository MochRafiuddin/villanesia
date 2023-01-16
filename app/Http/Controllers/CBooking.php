<?php

namespace App\Http\Controllers;

use App\Models\MBooking;
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

class CBooking extends Controller
{
    use Helper;

    public function index()
    {
        return view('booking.index')            
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
    public function data()
    {        
        $model = MBooking::join('m_properti','t_booking.id_ref','m_properti.id_properti','left')
            ->join('m_status_booking','t_booking.id_status_booking','m_status_booking.id_ref_bahasa','left')
            ->selectRaw('t_booking.*, m_properti.judul as judul, m_properti.alamat as alamat, m_properti.id_tipe_booking as tipe_booking, m_properti.binatang as binatang, m_status_booking.nama_status_booking as nama_status_booking , m_status_booking.id_status_booking as id_status_booking')
            ->where('m_status_booking.id_bahasa',1)
            ->where('t_booking.deleted',1);
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

}
