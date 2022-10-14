<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Auth;
use DateInterval;
use DatePeriod;
use DateTime;

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
    public function setSessionPeriode($periode) //this model paramaters
    {
        Session::put('periode',($periode->bulan ?? null).($periode->tahun ?? null));
        Session::put('id_periode',$periode->id_periode ?? null);
        Session::put('periode_bulan',$periode->bulan ?? null);
        Session::put('periode_tahun',$periode->tahun ?? null);
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
        $final= 0;
        for ($i=0; $i < count($period) ; $i++) {
            if ($pro->penerapan_harga_weekend==1) {
                if (date('D', strtotime($period[$i]))=='Sat') {
                    $final = $final + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='San') {
                    $final = $final + $pro->harga_weekend;
                }else {
                    $final = $final + $pro->harga_tampil;
                }
            }
            if ($pro->penerapan_harga_weekend==2) {
                if (date('D', strtotime($period[$i]))=='Fri') {
                    $final = $final + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sat') {
                    $final = $final + $pro->harga_weekend;
                }else {
                    $final = $final + $pro->harga_tampil;
                }
            }
            if ($pro->penerapan_harga_weekend==3) {
                if (date('D', strtotime($period[$i]))=='Fri') {
                    $final = $final + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sat') {
                    $final = $final + $pro->harga_weekend;
                }elseif (date('D', strtotime($period[$i]))=='Sun') {
                    $final = $final + $pro->harga_weekend;
                }else {
                    $final = $final + $pro->harga_tampil;
                }
            }
        }
        return $final;
    }

    public function get_harga_cus_by_input($period,$cus)
    {
        $final=0;
        $day = [];
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
                            $final = $final + $c->harga_weekend;
                        }elseif (date('D', strtotime($period[$i]))=='Sun') {
                            $final = $final + $c->harga_weekend;
                        }else {
                            $final = $final + $c->harga;
                        }
                    }
                }
                // $day[] = $period[$i];
            }
        }   
        return $final;
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

   public function can_akses($kode = null) {

  $data_akses   =   DB::table('m_action as a')
                    ->select('a.kode')
                    ->join('map_role_action as b','a.id_action','=','b.id_action')
                    ->where('b.id_role',Auth::user()->id_role)->get();

  // dd($data_akses);

  $datah = [];

  foreach ($data_akses as $key => $value) {

    array_push($datah, $value->kode);

  }

  // if ($ci->session->userdata("is_admin") || in_array($kode, $ci->session->userdata("can_akses"))) {
  if (session('is_admin') == 1 || in_array($kode, $datah)) {

    return true;

  } else {

    return false;

  }

}
}