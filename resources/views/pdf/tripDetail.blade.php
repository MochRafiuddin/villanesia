<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>

<style>
    .left, .right{
    width: 50%;
}
.information{
    display: flex;
    padding: 0.5rem;
}
.mb-0{
    margin-bottom: 0 !important;
}
.text-right{
    text-align: right;
}
.rp{
    padding-right: 1rem;
    padding-left: 0.5rem;
    border-right: 0px !important;
}
.border-left-0{
    border-left: 0px !important;
}
.fw-b{
    font-weight: bold;
}
table{
    border-collapse: collapse;
}
table td{
    padding: 2px;
}
.text-center{
    text-align: center;
}
.w-100{
    width: 100%;
}
.mt-5{
    margin-top: 3rem;
}
.mb-1{
    margin-bottom: 0.5rem !important;
}
.mb-2{
    margin-bottom: 1rem;
}
.mr-2{
    margin-right: 2rem;
}
.m-0{
    margin: 0rem;
}
.total-all{
    padding: 0.5rem;
    border: 1px solid grey;
    margin-top: 0.5rem;
}
.garis{
    padding: 0.5rem;
    border: 1px solid grey;
    margin-top: 0.5rem;
}
.page-break {
    page-break-after: always;
}
</style>
</head>
<body>
    <div class="page garis">        
        <div class="content">
            @php
                $booking = DB::table('t_booking')
					->join('m_properti','m_properti.id_properti','t_booking.id_ref')
					->join('m_jenis_tempat','m_jenis_tempat.id_jenis_tempat','m_properti.id_jenis_tempat')
					->join('m_tipe_properti','m_tipe_properti.id_tipe_properti','m_properti.id_tipe_properti')
					->join('m_users','m_users.id_user','t_booking.id_user')
					->join('m_customer','m_customer.id','m_users.id_ref')
					->select('t_booking.*','m_properti.*','m_jenis_tempat.nama_jenis_tempat','m_customer.nama_depan','m_customer.nama_belakang','m_tipe_properti.nama_tipe_properti')
					->where('t_booking.kode_booking',$kode_booking)
					->first();
                if($booking->id_tipe_booking == 1){
					$label = 'Day';
					$inap = $booking->durasi_inap;
					$txt = $booking->durasi_inap." Day";
				}elseif($booking->id_tipe_booking == 2){
					$label = 'Night';
					$inap = $booking->durasi_inap;
					if ($harga_satuan->count() > 2) {
						$txt = $booking->durasi_inap." Nights (with custom period)";
					}else {
						$txt = $booking->durasi_inap." Nights";
					}
				}elseif($booking->id_tipe_booking == 3){
					$label = 'Week';
					$inap = $booking->durasi_inap;
					if ($booking->extra_hari > 0) {
						$txt = $booking->durasi_inap." Week (with extra day)";
					}else {
						$txt = $booking->durasi_inap." Week";
					}
				}elseif($booking->id_tipe_booking == 4){
					$label = 'Month';
					$inap = $booking->durasi_inap;
					if ($booking->extra_hari > 0) {
						$txt = $booking->durasi_inap." Mounth (with extra day)";
					}else {
						$txt = $booking->durasi_inap." Mounth";
					}
				}else{
					$label = 'Hours';
					$inap = $booking->durasi_inap_jam;
					$txt = $booking->durasi_inap_jam." Hours";
				}
                $tamu_dewasa = ($booking->tamu_dewasa == 0 ? 0 : $booking->tamu_dewasa);
				$tamu_anak = ($booking->tamu_anak == 0 ? 0 : $booking->tamu_anak);
				$tamu_bayi = ($booking->tamu_bayi == 0 ? 0 : $booking->tamu_bayi);
            @endphp
            <div class="information" style="">
                <h2>Yout Trip Detail</h2>

                <p style="font-size:14px;line-height:21px;">Hi {{$booking->nama_depan}} {{$booking->nama_belakang}}</p>
                <p style="font-size:14px;line-height:21px;">Here's your itinerary, including the address and check-in details.</p>                          
                @if($booking->nama_file != null)
                    <img src="{{ public_path('upload/properti/'.$booking->nama_file) }}" alt="images" width='380px'>
                @endif
                <p style="font-size:16px;line-height:21px;font-weight: bold;">{{$booking->judul}}</p>                
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">Listed in {{$booking->nama_jenis_tempat}} and {{$booking->nama_tipe_properti}}</p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Date</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">No of {{$label}} {{$inap}}</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;"><span style="font-weight: bold;">From</span> {{date('l, j-m-Y', strtotime($booking->tanggal_mulai))}} <span style="font-weight: bold;">To</span> {{date('l, j-m-Y', strtotime($booking->tanggal_selesai))}}</p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Guest</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">{{$booking->tamu_dewasa+$booking->tamu_anak+$booking->tamu_bayi}} ({{$tamu_dewasa}} Guest, {{$tamu_anak}} Childern, {{$tamu_bayi}} Infants)</p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Address</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">{{$booking->alamat}}</p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">You Host</p>
                <img src="{{ public_path('/assets/images/logo-VILLANESIA-02.png') }}" alt="images" width="110px">
                <p style="font-size:16px;line-height:21px;font-weight: bold;">Villanesia Bali</p>
                <p style="font-size:16px;line-height:21px;margin-top:-10px;"><span style="font-weight: bold;">Mobile</span> : 085101728858</p>
                <p style="font-size:16px;line-height:21px;margin-top:-10px;"><span style="font-weight: bold;">Email</span> : reservation@villanesia.com</p>
                
                <p style="font-size:16px;line-height:21px;font-weight: bold;">House Rules</p>
                <p style="font-size:16px;line-height:21px;font-weight: bold;">Cancellation Policy</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">{!! $booking->kebijakan_pembatalan !!}</p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Smoking Allowed</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">
                @if($booking->merokok == 0)
                    No
                @else
                    Yes
                @endif
                </p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Pets Allowed</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">
                @if($booking->binatang == 0)
                    No
                @else
                    Yes
                @endif
                </p>
                
                <p style="font-size:16px;line-height:21px;font-weight: bold;">Party Allowed</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">
                @if($booking->acara == 0)
                    No
                @else
                    Yes
                @endif
                </p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Children Allowed</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">
                @if($booking->anak == 0)
                    No
                @else
                    Yes
                @endif
                </p>

                <p style="font-size:16px;line-height:21px;font-weight: bold;">Other Rules</p>
                <p style="font-size:14px;line-height:21px;margin-top:-10px;">{!! $booking->aturan_tambahan !!}</p>
            </div>
        </div>
    </div>
</body>

</html>