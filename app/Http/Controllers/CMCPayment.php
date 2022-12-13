<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HMcpaymentCallback;
use App\Models\MBooking;
use App\Models\TKonfirmasiBayar;
use App\Models\User;
use Auth;
use App\Traits\Helper;


class CMCPayment extends Controller
{
    use Helper;

    public function index()
    {
        $user = User::where('id_user',1)->first();
        Auth::login($user); 

        $dataJson = '{"transaction_id":"jvRdN75sETAmYB6W6oBpGA","order_id":"202211230001","external_id":"PG202211230001","currency":"IDR","transaction_status":"SUCCESS","payment_method":"VIRTUAL_ACCOUNT","payment_channel":"VA","acq":"CIMB","paid_date":"2022-11-21T10:34:25.033Z","fee":7000,"amount":1504777,"selected_channels":[{"channel":"CARD","acq":"BCACC"},{"channel":"VA","acq":"CIMB"},{"channel":"VA","acq":"PERMATA"}],"is_customer_paying_fee":false,"item_details":[{"item_id":"1","name":"harga_final_properti","amount":880000,"qty":1},{"item_id":"2","name":"harga_cleaning_fee","amount":20000,"qty":1},{"item_id":"3","name":"harga_security_deposit","amount":6777,"qty":1},{"item_id":"4","name":"harga_pajak","amount":558000,"qty":1},{"item_id":"5","name":"harga_extra_service","amount":40000,"qty":1}],"save_card":false,"token":null,"customer_details":{"full_name":"Andi Bagus","email":"andibagus@mail.com","phone":"081180081180","address":"Jl. Maju Bersama no. 1"},"shipping_address":{"full_name":"Andi Bagus","phone":"081180081180","address":"Jl. Maju Bersama no. 1","city":"Malang","postal_code":"65154","country":"ID"},"billing_address":{"full_name":"Andi Bagus","phone":"081180081180","address":"Jl. Maju Bersama no. 1","city":"Malang","postal_code":"65154","country":"ID"},"callback_url":"http://all.aptikmabiz.com/mcpayment-villanesia/callback-mcpayment.php","success_redirect_url":"http://all.aptikmabiz.com/mcpayment-villanesia/redirect-invoice-sukses.php","failed_redirect_url":"http://all.aptikmabiz.com/mcpayment-villanesia/redirect-invoice-gagal.php","expired_time":"2022-11-22T10:06:15.395Z","mis_version":3}';
						
		// $data = file_get_contents("php://input");
		// $input = json_decode($data);

		// $txt = date("Y-m-d H:i:s") . " -> " . $data;
		// $myfile = file_put_contents('callback_mcpayment.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
        $input = json_decode($dataJson);
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
			
			$this->kirim_email($input->customer_details->email,$input->customer_details->full_name,null,null,null,null,null,'email.emailPembayaran','Proof of payment - ORDER ID #'.$input->order_id.' - Villanesia');			
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

        $get_user = User::selectRaw('m_customer.*, m_users.id_user, m_users.id_ref, m_users.email,')
                ->leftJoin('m_customer','m_customer.id','=','m_users.id_ref')
                ->where('m_users.id_user',$id_user)
                ->where('m_users.deleted',1)
                ->where('m_customer.deleted',1)
                ->first();

        $this->kirim_email($get_user->email,$get_user->nama_depan,$get_user->nama_belakang,null,null,null,null,'email.mailPembayaran','Proof of payment - ORDER ID #'.$kode_booking.' - Villanesia');
		
		return response()->json(['status'=>true,'msg'=>'Sukses']);
    }
}
