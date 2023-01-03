<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HMcpaymentCallback;
use App\Models\MBooking;
use App\Models\TKonfirmasiBayar;
use App\Models\User;
use App\Models\MProperti;
use App\Models\MBookingDiscount;
use App\Models\MBookingExtra;
use App\Models\MBookingHargaSatuan;
use App\Models\MBookingPropertiExtra;
use Auth;
use App\Traits\Helper;
use PDF;
use App\Mail\EmailPembayaran;
use Mail;

class CMCPayment extends Controller
{
    use Helper;

    public function index(Request $request)
	{
		$user = User::where('id_user',1)->first();
		Auth::login($user); 

		// $dataJson = '{"transaction_id":"jvRdN75sETAmYB6W6oBpGA","order_id":"202211230001","external_id":"PG202211230001","currency":"IDR","transaction_status":"SUCCESS","payment_method":"VIRTUAL_ACCOUNT","payment_channel":"VA","acq":"CIMB","paid_date":"2022-11-21T10:34:25.033Z","fee":7000,"amount":1504777,"selected_channels":[{"channel":"CARD","acq":"BCACC"},{"channel":"VA","acq":"CIMB"},{"channel":"VA","acq":"PERMATA"}],"is_customer_paying_fee":false,"item_details":[{"item_id":"1","name":"harga_final_properti","amount":880000,"qty":1},{"item_id":"2","name":"harga_cleaning_fee","amount":20000,"qty":1},{"item_id":"3","name":"harga_security_deposit","amount":6777,"qty":1},{"item_id":"4","name":"harga_pajak","amount":558000,"qty":1},{"item_id":"5","name":"harga_extra_service","amount":40000,"qty":1}],"save_card":false,"token":null,"customer_details":{"full_name":"Andi Bagus","email":"andibagus@mail.com","phone":"081180081180","address":"Jl. Maju Bersama no. 1"},"shipping_address":{"full_name":"Andi Bagus","phone":"081180081180","address":"Jl. Maju Bersama no. 1","city":"Malang","postal_code":"65154","country":"ID"},"billing_address":{"full_name":"Andi Bagus","phone":"081180081180","address":"Jl. Maju Bersama no. 1","city":"Malang","postal_code":"65154","country":"ID"},"callback_url":"http://all.aptikmabiz.com/mcpayment-villanesia/callback-mcpayment.php","success_redirect_url":"http://all.aptikmabiz.com/mcpayment-villanesia/redirect-invoice-sukses.php","failed_redirect_url":"http://all.aptikmabiz.com/mcpayment-villanesia/redirect-invoice-gagal.php","expired_time":"2022-11-22T10:06:15.395Z","mis_version":3}';
						
		$data = $request->getContent();//file_get_contents("php://input");
		// echo json_encode($data);
		// $input = json_decode($data);

		$txt = date("Y-m-d H:i:s") . " -> " . $data;
		$myfile = file_put_contents('callback_mcpayment.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
		$input = json_decode($data);
		// dd($input);
		$mHMCPaymentCallback = new HMcpaymentCallback;
		$mHMCPaymentCallback->pg_order_code = $input->external_id;
		$tBooking = MBooking::where('pg_order_code', $input->external_id)->first();
		$tKonfirmasiBayar = TKonfirmasiBayar::where('kode_booking', $input->order_id)->first();
		
		if ($input->transaction_status == 'SUCCESS') {
			$mHMCPaymentCallback->status = $tBooking->payment_status = 1;
			$tBooking->payment_date = date("Y-m-d H:i:s");
			$tBooking->id_status_booking = 5;
			$tKonfirmasiBayar->konfirmasi = 1;
			$tKonfirmasiBayar->konfirmasi_tanggal = date("Y-m-d H:i:s");
			$tKonfirmasiBayar->update();
			
			// $this->kirim_email($input->customer_details->email,$input->customer_details->full_name,null,null,null,null,null,'email.emailPembayaran','Proof of payment - ORDER ID #'.$input->order_id.' - Villanesia',$kode_booking,$get_user->no_telfon);

			$this->pdf_email($input->customer_details->email,$input->order_id,$tKonfirmasiBayar->nama_depan,$tKonfirmasiBayar->nama_belakang,$input->customer_details->phone);
		} else {
			$mHMCPaymentCallback->status = 2;
			$tBooking->payment_status = 3;
		}
		$tBooking->pg_name = $input->acq;
		$tBooking->update();
		$mHMCPaymentCallback->respon = json_encode($input);
		$mHMCPaymentCallback->save();

		
		return response()->json(['status'=>true,'msg'=>'Sukses']);
	}

	public function set_lunas($kode_booking)
    {        		
		$tBooking = MBooking::where('kode_booking', $kode_booking)->first();		
		if ($tBooking == null) {
			return response()->json(['status'=>false,'msg'=>'Kode Booking Tidak Sesuai']);
		}
		$id_user = $tBooking->id_user;
		$tBooking->payment_date = date("Y-m-d H:i:s");				
		$tBooking->id_status_booking = 5;
		$tBooking->payment_status = 1;
		$tBooking->update();		

        $get_user = User::select('m_customer.*', 'm_users.id_user', 'm_users.id_ref', 'm_users.email')
                ->leftJoin('m_customer','m_customer.id','m_users.id_ref')
                ->where('m_users.id_user',$id_user)
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->first();

        // $this->kirim_email($get_user->email,$get_user->nama_depan,$get_user->nama_belakang,null,null,null,null,'email.emailPembayaran','Proof of payment - ORDER ID #'.$kode_booking.' - Villanesia',$kode_booking,$get_user->no_telfon);
		$this->pdf_email($get_user->email,$kode_booking,$get_user->nama_depan,$get_user->nama_belakang,$get_user->no_telfon);
		
		return response()->json(['status'=>true,'msg'=>'Sukses']);
		// $pdf = PDF::loadview('pdf.tripDetail',['kode_booking'=>$kode_booking]);
		// return $pdf->stream('result.pdf');
    }

	public function redirect_invoice_sukses(Request $request)
	{

		$tam_trans_id = $request->transaction_id;

		$data = array();

		if(!empty($tam_trans_id)){
			$detail_booking = MBooking::from( 't_booking as a' )
				->selectRaw('a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, c.nama_status_booking, CONCAT(e.nama_depan," ",e.nama_belakang) as nama_pemilik_properti, CONCAT(g.nama_depan," ",g.nama_belakang) as nama_pemesan, h.nama_tipe_properti')
				->leftJoin('m_properti as b','a.id_ref', '=','b.id_ref_bahasa')
				->leftJoin('m_status_booking as c','a.id_status_booking', '=','c.id_ref_bahasa')
				->leftJoin('m_users as d','d.id_user', '=','b.created_by')
				->leftJoin('m_customer as e','d.id_ref', '=','e.id')
				->leftJoin('m_users as f','f.id_user', '=','a.id_user')
				->leftJoin('m_customer as g','f.id_ref', '=','g.id')
				->leftJoin('m_tipe_properti as h','h.id_ref_bahasa', '=','b.id_tipe_booking')
				->where('h.id_bahasa',1)
				->where('a.deleted',1)
				->where('b.deleted',1)
				->where('b.id_bahasa',1)
				->where('a.pg_transaction_id',$tam_trans_id)
				->where('d.deleted',1)
				->where('f.deleted',1)->get();
			
			$id_booking = 0;
			if($detail_booking->count() > 0){
				$id_booking = $detail_booking->first()->id_booking;
			}

			$detail_booking_harga_satuan = MBookingHargaSatuan::where('id_booking',$id_booking)->get();
			$detail_booking_properti_extra = MBookingPropertiExtra::where('id_booking',$id_booking)->get();
			$detail_booking_extra = MBookingExtra::where('id_booking',$id_booking)->get();
			$detail_booking_discount = MBookingDiscount::where('id_booking',$id_booking)->get();

			// return response()->json([
			//     'success' => true,
			//     'message' => 'Success',
			//     'code' => 1,            
			//     'detail_booking' => $detail_booking,
			//     'detail_payment' =>[
			//         'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
			//         'detail_booking_properti_extra' => $detail_booking_properti_extra,
			//         'detail_booking_extra' => $detail_booking_extra,
			//         'detail_booking_discount' => $detail_booking_discount,
			//     ]
			// ], 200);  
			
			$data = array(
				'all_data' => array(
							'detail_booking' => $detail_booking,
							'detail_payment' =>[
								'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
								'detail_booking_properti_extra' => $detail_booking_properti_extra,
								'detail_booking_extra' => $detail_booking_extra,
								'detail_booking_discount' => $detail_booking_discount
							],
							'message' => 'success',
							'code' => 1
						)
			);
		}

		return view('mc_payment.redirect_invoice_sukses', $data)            
			->with('title','Redirect invoice sukses');
	}

	public function redirect_invoice_gagal(Request $request)
	{

		$tam_trans_id = $request->transaction_id;

		$data = array();

		if(!empty($tam_trans_id)){

			$detail_booking = MBooking::from( 't_booking as a' )
				->selectRaw('a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, c.nama_status_booking, CONCAT(e.nama_depan," ",e.nama_belakang) as nama_pemilik_properti, CONCAT(g.nama_depan," ",g.nama_belakang) as nama_pemesan, h.nama_tipe_properti')
				->leftJoin('m_properti as b','a.id_ref', '=','b.id_ref_bahasa')
				->leftJoin('m_status_booking as c','a.id_status_booking', '=','c.id_ref_bahasa')
				->leftJoin('m_users as d','d.id_user', '=','b.created_by')
				->leftJoin('m_customer as e','d.id_ref', '=','e.id')
				->leftJoin('m_users as f','f.id_user', '=','a.id_user')
				->leftJoin('m_customer as g','f.id_ref', '=','g.id')
				->leftJoin('m_tipe_properti as h','h.id_ref_bahasa', '=','b.id_tipe_booking')
				->where('h.id_bahasa',1)
				->where('a.deleted',1)
				->where('b.deleted',1)
				->where('b.id_bahasa',1)
				->where('a.pg_transaction_id',$tam_trans_id)
				->where('d.deleted',1)
				->where('f.deleted',1)->get();
			
			$id_booking = 0;
			if($detail_booking->count() > 0){
				$id_booking = $detail_booking->first()->id_booking;
			}

			$detail_booking_harga_satuan = MBookingHargaSatuan::where('id_booking',$id_booking)->get();
			$detail_booking_properti_extra = MBookingPropertiExtra::where('id_booking',$id_booking)->get();
			$detail_booking_extra = MBookingExtra::where('id_booking',$id_booking)->get();
			$detail_booking_discount = MBookingDiscount::where('id_booking',$id_booking)->get();

			MBooking::where('id_booking',$id_booking)->update(['pg_url' => ""]);

			$data = array(
				'all_data' => array(
							'detail_booking' => $detail_booking,
							'detail_payment' =>[
								'detail_booking_harga_satuan' => $detail_booking_harga_satuan,
								'detail_booking_properti_extra' => $detail_booking_properti_extra,
								'detail_booking_extra' => $detail_booking_extra,
								'detail_booking_discount' => $detail_booking_discount
							],
							'message' => 'failed',
							'code' => 2
						)
			);

		}

		return view('mc_payment.redirect_invoice_gagal', $data)            
			->with('title','Redirect invoice gagal');
	}

	public function getContent($asResource = false)
	{
		if (false === $this->content || (true === $asResource && null !== $this->content)) {
			throw new \LogicException('getContent() can only be called once when using the resource return type.');
		}

		if (true === $asResource) {
			$this->content = false;

			return fopen('php://input', 'rb');
		}

		if (null === $this->content) {
			$this->content = file_get_contents('php://input');
		}

		return $this->content;
	}
	
	public function pdf_email($email,$kode_booking,$nama_depan,$nama_belakang,$no_telfon)
    {        				
		
		$pdf = PDF::loadview('pdf.tripDetail',['kode_booking'=>$kode_booking]);
		// return $pdf->stream('result.pdf');
		// $pdf = PDF::loadview('pdf.tripDetail',['kode_booking'=>$kode_booking]);  

        // $this->kirim_email($get_user->email,$get_user->nama_depan,$get_user->nama_belakang,null,null,null,null,'email.emailPembayaran','Proof of payment - ORDER ID #'.$kode_booking.' - Villanesia',$kode_booking,$get_user->no_telfon);

		Mail::to($email)->send(new EmailPembayaran($nama_depan,$nama_belakang,'email.emailPembayaran','Payment Confirmation - ORDER ID #'.$kode_booking.' - Villanesia',$kode_booking,$no_telfon,$pdf->output(),$email));

		return true;
    }
}
