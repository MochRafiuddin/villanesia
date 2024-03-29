<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Mail\KirimEmail;
use App\Models\TokenFcm;
use App\Models\Setting;
use Mail;
use Auth;
use DateInterval;
use DatePeriod;
use DateTime;
use Config;

trait Helper
{
    public static function convertDate($tgl, $tampil_hari=true, $with_menit = true){
        if ($tgl != null ||  $tgl != "") {
                $nama_hari    =   array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
                $nama_bulan   =   array (
                                    1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus","September", "Oktober", "November", "Desember"
                                );
                $tahun        =   substr($tgl,0,4);
                $bulan        =   $nama_bulan[(int)substr($tgl,5,2)];
                $tanggal      =   substr($tgl,8,2);

                $text         =   "";

                if ($tampil_hari) {

                    $urutan_hari  =   date('w', mktime(0,0,0, substr($tgl,5,2), $tanggal, $tahun));
                    $hari         =   $nama_hari[$urutan_hari];
                    $text         .=  $hari.", ";

                }

                $text         .=$tanggal ." ". $bulan ." ". $tahun;

                if ($with_menit) {

                $jam    =   substr($tgl,11,2);
                $menit  =   substr($tgl,14,2);

                $text   .=  ", ".$jam.":".$menit;

                }


        }else{

            $text = "-";
        }
    return $text;
    }
    function ribuan($angka,$comma = 0){

      $hasil_rupiah = number_format($angka,$comma,',','.');
      return $hasil_rupiah;
     
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public static function searchData($column1,$column2,$table,$value)
    {
        $listDefault = DB::table($table)->where('deleted',1)->get([$column1,$column2]);
        $result = "";

        foreach($listDefault as $key){
            if($key->{$column1} == $value){
                $result = $key->{$column2};
                return $result;
            }
            
        }
        return $result;
    }
    function showData($data,$column){
        $columnDate = ['tanggal_start','tanggal_end'];

        if($data != null){
                return $data->{$column};
            
        }else{
            if(old($column)){
                if(in_array($column,$columnDate)){
                    return date('Y-m-d',strtotime(old($column)));
                }else{
                    return old($column);
                }
            }
        }
    }
    function showDataDate($data,$column){
        $columnDate = ['tanggal_start','tanggal_end'];
        if($data != null){
            if(strtotime($data->{$column})){
                return date('Y-m-d',strtotime($data->{$column}));
            }else{
                return $data->{$column};
            }
        }else{
            if(old($column)){
                if(in_array($column,$columnDate)){
                    return date('Y-m-d',strtotime(old($column)));
                }else{
                    return old($column);
                }
            }
        }
    }
    function showDataSelected($data,$column,$value){
        if($data != null){
            if($data->{$column} == $value){
                return 'selected';
            };
        }
    }
    function showDataSelected2($data,$column,$value){
        if($data !== null){
            if($data->{$column} === $value){
                return 'selected';
            };
        }
    }
    function showDataSelect2($data,$column1,$column2,$table){
        if($data != null){
            // dd("dsadas");
            $result = Helper::searchData($column1,$column2,$table,$data->{$column1});
            
            return "<option value='{$data->{$column1}}' selected>".$result."</option>";
        }else{
            if(old($column1)){
                $result = Helper::searchData($column1,$column2,$table,old($column1));
                return "<option value='".old($column1)."' selected>".$result."</option>";
            }
        }
    }
    function showDataChecked($data,$column,$value){
        if($data != null){
            if($data->{$column} == $value){
                return 'checked';
            };
        }
    }

    function showDataChecked2($data,$column,$value){
        if($data != null){
            foreach ($data as $a) {
                if($a->{$column} == $value){
                    return 'checked';
                };
            }
        }
    }

    function singkatAngka($n, $presisi=1) {
        if ($n < 900) {
            $format_angka = number_format($n, $presisi);
            $simbol = '';
        } else if ($n < 900000) {
            $format_angka = number_format($n / 1000, $presisi);
            $simbol = 'rb';
        } else if ($n < 900000000) {
            $format_angka = number_format($n / 1000000, $presisi);
            $simbol = 'jt';
        } else if ($n < 900000000000) {
            $format_angka = number_format($n / 1000000000, $presisi);
            $simbol = 'M';
        } else {
            $format_angka = number_format($n / 1000000000000, $presisi);
            $simbol = 'T';
        }
    
        if ( $presisi > 0 ) {
            $pisah = '.' . str_repeat( '0', $presisi );
            $format_angka = str_replace( $pisah, '', $format_angka );
        }
        
        return $format_angka . $simbol;
    }
    public function replaceNumeric($nominal)
    {
        return str_replace(".","",$nominal);
    }
    public function convertBulan($value)
    {
        if($value != null){
            $nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus","September", "Oktober", "November", "Desember"];
        }else{
            return "-";
        }
        return $nama_bulan[$value-1];
    }
    public function convertBulanEn($value)
    {
        if($value != null){
            $nama_bulan = ["January", "February", "March", "April", "May", "June", "July", "August","September", "Oktober", "November", "Desember"];
        }else{
            return "-";
        }
        return $nama_bulan[$value-1];
    }
    public function getMinutes($value)
    {
        //15 - 23
        $hours = date("H",$value);
        $minutes = date("i",$value);
        $seconds = date("s",$value);
        $result = 0;
        if(intval($hours) > 0){
            $result += intval($hours) * 60;
        }
        if($minutes > 0){
            $result += intval($minutes);
        }
        if($seconds > 0){
            $result += intval($seconds) / 60;
        }
        return $result;

    }
    public function calculateDistanceMinutes($value1,$value2)
    {
        //15:00:00 -- 23:00:00
        //23 - 15 = 8
        // $value1 = date("H:i:s",strtotime("01/01/2022 11:00:00 PM"));
        // $value2 = date("H:i:s",strtotime("02/01/2022 07:00:00 AM"));
        // dd($value1);
        $time = strtotime($value2) - strtotime($value1);
        
        $minutes = $this->getMinutes($time);
        return $minutes;
    }
    
    public function get_harga_by_input($period,$pro)
    {
        $day['harga'] = 0;
        $day['tamu'] = 0;
        for ($i=0; $i < count($period) ; $i++) {
            if ($pro->penerapan_harga_weekend==1) {
                if (date('D', strtotime($period[$i]))=='Sat') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sun') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }else {
                    $day['harga'] = $day['harga'] + $pro->harga_tampil;
                }
            }
            if ($pro->penerapan_harga_weekend==2) {
                if (date('D', strtotime($period[$i]))=='Fri') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sat') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }else {
                    $day['harga'] = $day['harga'] + $pro->harga_tampil;
                }
            }
            if ($pro->penerapan_harga_weekend==3) {
                if (date('D', strtotime($period[$i]))=='Fri') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sat') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sun') {
                    $day['harga'] = $day['harga'] + $pro->harga_weekend;
                }else {
                    $day['harga'] = $day['harga'] + $pro->harga_tampil;
                }
            }
            $day['tamu'] = ($pro->harga_tamu_tambahan != null ? $pro->harga_tamu_tambahan : 0);
        }
        return $day;
    }

    public function get_harga_cus_by_input($period,$cus)
    {
        $final=0;
        $day['harga'] = 0;
        $day['tamu'] = 0;
        $day['harga_tampil'] = 0;
        foreach ($cus as $c) {
            $period1 = new DatePeriod(
                new DateTime($c->start_date),
                new DateInterval('P1D'),
                new DateTime(date('Y-m-d', strtotime($c->end_date)).' +1 days')
            );
            for ($i=0; $i < count($period) ; $i++) {             
                foreach ($period1 as $key1) {
                    if ($period[$i] == $key1->format('Y-m-d')) {
                        if (date('D', strtotime($period[$i]))=='Sat') {
                            $day['harga'] = $day['harga'] + $c->harga_weekend;
                        }elseif (date('D', strtotime($period[$i]))=='Sun') {
                            $day['harga'] = $day['harga'] + $c->harga_weekend;
                        }else {
                            $day['harga'] = $day['harga'] + $c->harga;
                        }
                        $day['tamu'] = ($c->harga_tamu_tambahan != null ? $c->harga_tamu_tambahan : 0);
                        $day['harga_tampil'] = $c->harga;
                    }
                }
                // $day[] = $period[$i];
            }
        }   
        return $day;
        // return $day;
    }

    public function get_date_by_input($tanggal_mulai,$tanggal_selesai)
    {
        $period = new DatePeriod(
            new DateTime(date('Y-m-d', strtotime($tanggal_mulai))),
            new DateInterval('P1D'),
            new DateTime(date('Y-m-d', strtotime($tanggal_selesai.' -1 day')).' +1 days')
        );
        $day = [];
        foreach ($period as $key) {
            $day[] = $key->format('Y-m-d');
        }
        return $day;
    }

    public function get_date_custom_harga($cus,$tanggal_mulai,$tanggal_selesai)
    {        
        // $day['date1'] = [];
        // $day['date2'] = [];
        $day = [];
        $period = new DatePeriod(
            new DateTime($tanggal_mulai),
            new DateInterval('P1D'),
            new DateTime(date('Y-m-d', strtotime($tanggal_selesai.' -1 day')).' +1 days')
        );
        // foreach ($period as $key) {
        //     $day['date1'][] = $key->format('Y-m-d');
        // }
        foreach ($cus as $c) {
            $period1 = new DatePeriod(
                new DateTime($c->start_date),
                new DateInterval('P1D'),
                new DateTime(date('Y-m-d', strtotime($c->end_date)).' +1 days')
            );
            foreach ($period as $key) {
                foreach ($period1 as $key1) {                                
                    if ($key->format('Y-m-d') == $key1->format('Y-m-d')) {
                        // $day['date2'][] = $key1->format('Y-m-d');
                        $day[] = $key1->format('Y-m-d');
                    }
                }
            }
        }
        return $day;
    }

    public function postCURL($kode_booking ,$harga_total, $nama_depan, $nama_belakang, $alamat, $nama_provinsi, $nama_kota, $kode_pos,$no_telfon, $email, $iso_code){
        $setting = Setting::where('id',7)->first();
        $input = json_decode($setting->nilai);
        $merchant_id = $input->merchant_id;
        $secret_unbound_id = $input->secret_unbound_id;
        $hash_key = $input->hash_key;
        $url = $input->url;

        // $merchant_id = "MCP2022040419";
		// $secret_unbound_id = "0x001d961efa2c3f4fdc";
		// $hash_key = "EtP0y6yGGikjONW";
        $signature = hash('sha256',$hash_key.'PG'.$kode_booking.$kode_booking);
        $authorization = $merchant_id.":".$secret_unbound_id;
        // dd($url);
        // $url = "https://api-stage.mcpayment.id/payment-page/payment";
        $header[] = "Content-Type: application/json";
        $header[] = "Authorization: Basic ".base64_encode($authorization);
        $header[] = "x-req-signature: ".$signature;
        $header[] = "x-version: v3";
        $postData = '{
                        "order_id": "'.$kode_booking.'",
                        "external_id": "PG'.$kode_booking.'",
                        "amount": '.$harga_total.',
                        "description": "Pembayaran Villanesia order #'.$kode_booking.'",
                        "customer_details": {
                            "full_name": "'.$nama_depan.' '.$nama_belakang.'",
                            "email": "'.$email.'",
                            "phone": "'.$no_telfon.'",
                            "address": "'.$alamat.'",
                            "is_email_show": false,
                            "is_phone_show": false
                        },
                        "item_details": [
                            {
                                "item_id": "1",
                                "name": "Total Reservation Price",
                                "amount": '.$harga_total.',
                                "qty":  1
                            }
                        ],
                        "selected_channels": [
                            {"channel":"CARD", "acq": "BCACC"},
                            {"channel":"VA", "acq": "CIMB"},
                            {"channel":"VA", "acq": "PERMATA"}
                        ],
                        "billing_address": {
                            "full_name": "'.$nama_depan.' '.$nama_belakang.'",
                            "phone": "'.$no_telfon.'",
                            "address": "'.$alamat.'",
                            "city": "'.$nama_kota.'",
                            "postal_code": "'.$kode_pos.'",
                            "country": "'.$iso_code.'"
                        },
                        "shipping_address": {
                            "full_name": "'.$nama_depan.' '.$nama_belakang.'",
                            "phone": "'.$no_telfon.'",
                            "address": "'.$alamat.'",
                            "city": "'.$nama_kota.'",
                            "postal_code": "'.$kode_pos.'",
                            "country": "'.$iso_code.'"
                        },
                        "save_card": false,
                        "callback_url": "http://aptikmamid.ngrok.io/villanesia/public/callback-order",
                        "success_redirect_url": "http://aptikmamid.ngrok.io/villanesia/public/redirect-invoice-sukses",
                        "failed_redirect_url": "http://aptikmamid.ngrok.io/villanesia/public/redirect-invoice-gagal"
                    }';
        // $postData = json_decode($postData);
        //local
        // $header[] = "Origin: http://localhost";
        // dd($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    

        $output=curl_exec($ch);

        curl_close($ch);

        // dd($output);
        return $output;
    }

    public function kirim_email($email,$nama_depan,$nama_belakang,$username,$password,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$no_telfon)
    {
        Mail::to($email)->send(new KirimEmail($nama_depan,$nama_belakang,$username,$password,$nama_properti,$tanggal_check_in,$view,$judul,$id_booking,$email,$no_telfon));
        return TRUE;
    }

    public function send_fcm($title,$body,$route,$param,$id_user,$id_notif)
    {
        $firebaseToken = TokenFcm::where('id_user',$id_user)->get()->pluck('fcm_token');
        $SERVER_API_KEY = Config::get('app.fcm_server_key.key');
        //env('FCM_SERVER_KEY');
        // dd($firebaseToken);
        // $param = json_encode(array("id_ref" => "107"));
        // dd($firebaseToken);
        if (count($firebaseToken) == 0) {
            return false;
        }
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body, 
            ],
            "data" => array(
                "route" => $route,
                "param" => json_decode($param),
                "id_notif" =>$id_notif
            ) 
        ];

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
        // return true;
    }

   public function can_akses($kode = null) {

  $data_akses   =   DB::table('map_role_menu as a')
                    ->select('b.nama_menu')
                    ->join('m_menu as b','a.id_menu','=','b.id_menu')
                    ->where('a.id_role',Auth::user()->id_role)->get();

  // dd($data_akses);

  $datah = [];

  foreach ($data_akses as $key => $value) {

    array_push($datah, $value->nama_menu);

  }

  // if ($ci->session->userdata("is_admin") || in_array($kode, $ci->session->userdata("can_akses"))) {
  if (session('is_admin') == 1 || in_array($kode, $datah)) {

    return true;

  } else {

    return false;

  }

}
}