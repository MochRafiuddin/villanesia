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
.salary table tr:first-child td{
    border: 1px solid black;
}
.salary table tr:last-child  {
    border-top: 1px solid black;
}
.salary table tr:last-child td {
    border-left: 0px solid black;
}
.salary table tr:last-child td:nth-child(2) {
    border-bottom: 1px solid black;
}
.salary table tr:last-child td:last-child {
    border-bottom: 1px solid black;
}
.salary table tr td{
    border-right: 1px;
    border-top: 0px;
    border-bottom: 0px;
    border-left: 1px;
    border-style: solid;
    border-color: black;
}
.salary table tr td:nth-child(2){
    border-right: 0px !important;
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
.garis-3{
    padding: 0.5rem;
    border: 3px solid grey;
    margin-top: 0.5rem;
}
</style>
</head>
<body>
    @php
		$booking = DB::table('t_booking')
			->join('m_properti','m_properti.id_properti','t_booking.id_ref')
			->select('t_booking.*','m_properti.id_tipe_booking','m_properti.harga_tampil','m_properti.nama_properti','m_properti.judul')
			->where('t_booking.kode_booking',$kode_booking)
			->first();
			$id_booking = $booking->id_booking;
		$harga_satuan = DB::table('t_booking_harga_satuan')										
			->where('id_booking',$id_booking)
			->get();
		$extra_service = DB::table('t_booking_properti_extra')
			->join('m_properti_extra','m_properti_extra.id_properti_extra','t_booking_properti_extra.id_properti_extra')
			->select('t_booking_properti_extra.*','m_properti_extra.nama_service')
			->where('t_booking_properti_extra.id_booking',$id_booking)
			->get();
		$extra = DB::table('t_booking_extra')
			->where('id_booking',$id_booking)
			->get();
		$discount = DB::table('t_booking_discount')
			->where('id_booking',$id_booking)
			->get();									
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
    <div class="page garis">
        <div class="header">
            <img src="{{ public_path('assets/images/VILLANESIA.webp') }}" alt="" width="40%">            
        </div>
        <div class="content">
            <h2>Invoice {{$booking->kode_booking}}</h2>            
            <div class="information garis-3" style="">                
                <table>
                    <tr>
                        <tr>
							<td style="font-size:14px;line-height:21px;font-weight: bold;">Property :</td>
							<td style="font-size:14px;line-height:21px;">{{$booking->judul}}</td>
						</tr>
						<tr>
							<td style="font-size:14px;line-height:21px;font-weight:bold;">Period :</td>
							<td style="font-size:14px;line-height:21px;">{{date('d-m-Y', strtotime($booking->tanggal_mulai))}} to {{date('d-m-Y', strtotime($booking->tanggal_selesai))}}</td>
						</tr>
						<tr>
							<td style="font-size:14px;line-height:21px;font-weight:bold;">No of {{$label}} :</td>
							<td style="font-size:14px;line-height:21px;">{{$inap}}</td>
						</tr>
						<tr>
							<td style="font-size:14px;line-height:21px;font-weight:bold;">Guest :</td>
							<td style="font-size:14px;line-height:21px;">{{$booking->tamu_dewasa+$booking->tamu_anak+$booking->tamu_bayi}} ({{$tamu_dewasa}} Guest, {{$tamu_anak}} Childern, {{$tamu_bayi}} Infants)</td>
						</tr>
						<tr>
							<td style="font-size:14px;line-height:21px;font-weight:bold;">Price Per {{$label}} :</td>
							<td style="font-size:14px;line-height:21px;">Rp. {{number_format($booking->harga_tampil)}}</td>
						</tr>
                    </tr>
                </table>
                <br>
                <table width="100%" style="border-collapse: collapse;">
					<tr>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Cost
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Price
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Detail
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Subtotal
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Rp. {{number_format($harga_satuan->sum('harga_final'))}}
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							{{$txt}}
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Cleaning fee
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Rp. {{number_format($booking->cleaning_fee)}}
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Security Deposit
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Rp. {{number_format($booking->security_deposit)}}
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							
						</td>
					</tr>
					<tr>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Taxes {{$booking->persen_pajak}}%
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							Rp. {{number_format($booking->nominal_pajak)}}
						</td>
						<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
							
						</td>
					</tr>
					@if($extra_service->count() > 0)
					@foreach($extra_service as $ex_se)
						<tr>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								{{$ex_se->nama_service}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								Rp. {{number_format($ex_se->harga_final)}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								
							</td>
						</tr>
					@endforeach
					@endif
					@if($booking->total_booking_extra != 0)
					@foreach($extra as $ex)
						<tr>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								{{$ex->nama_biaya_extra}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								Rp. {{number_format($ex->harga)}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								
							</td>
						</tr>
					@endforeach
					@endif
					@if($booking->total_booking_discount != null)
					@foreach($discount as $dis)
						<tr>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								{{$dis->nama_biaya_discount}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								- Rp. {{number_format($dis->harga)}}
							</td>
							<td style="border: 1px solid black; border-right-width: 0px !important; border-left-width: 0px !important; border-top-width: 0px !important; ">
								
							</td>
						</tr>
					@endforeach
					@endif
				</table><br>
				<table width='100%'>
					<tr>
						<td width='40%' style="font-size:16px;line-height:21px;font-weight: bold;">
							Total
						</td>
						<td width='60%' style="font-size:16px;line-height:21px;font-weight: bold;">
							Rp. {{number_format($booking->harga_total)}}
						</td>
					</tr>
					<tr>
						<td width='40%' style="font-size:14px;line-height:21px;">
							Reservation Fee Required
						</td>
						<td width='60%' style="font-size:14px;line-height:21px;">
							Rp. {{number_format($booking->harga_total)}}
						</td>
					</tr>
				</table>
            </div>
        </div>
    </div>
</body>

</html>