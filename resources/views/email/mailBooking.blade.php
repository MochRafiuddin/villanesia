<html>
<head></head>
<body>
	<div>
        <table style="max-width:600px;margin:0 auto;background-color:#ffffff;border-spacing:0;border-collapse:collapse;line-height:1.5rem;letter-spacing:0;font-family:Open Sans,sans-serif" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center">
			<tbody>
				<tr>
					<td style="padding-top:13px;padding-bottom:30px;text-align:left">
						<img style="margin:auto" src="{{$message->embed(public_path().'/assets/images/VILLANESIA.png')}}" class="CToWUd" data-bit="iit" width="200px">
					</td>
				</tr>
				<tr>
					<td style="padding:0px 16px">
						<table style="border-collapse:collapse;width:100%" cellspacing="0" cellpadding="0" border="0">
							<tbody><tr>
								<td>
									<h1 style="margin:0 0 16px;font-size:25px;line-height:38px">Hi {{$nama_depan}} {{$nama_belakang}},</h1>
									<p style="margin:0;font-size:16px;line-height:24px">We have confirmed the availability of your order, please make payment immediately to be able to enjoy it.</p>
								</td>
							</tr>
						</tbody></table>
					</td>
				</tr>
				<tr>
					<td style="padding:12px 16px">
						<h2 style="background:#f15e75;font-size:16px;line-height:24px;margin:0;padding:8px 16px;border-color:#dee2ee;border-style:solid;border-width:1px 1px 0;color:white;"> Order Details </h2>
						<div style="border-color:#dee2ee;border-style:solid;border-width:0 1px 1px;padding:16px;">
							<table width='100%'>
								@php
									$booking = DB::table('t_booking')
										->join('m_properti','m_properti.id_properti','t_booking.id_ref')
										->select('t_booking.*','m_properti.id_tipe_booking','m_properti.harga_tampil')
										->where('t_booking.id_booking',$id_booking)
										->first();
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
								<tr>
									<td width='25%' style="font-size:14px;line-height:21px;font-weight: bold;">Property</td>
									<td width='75%' style="font-size:14px;line-height:21px;">{{$nama_properti}}</td>
								</tr>
								<tr>
									<td width='25%' style="font-size:14px;line-height:21px;font-weight:bold;">Period :</td>
									<td width='75%' style="font-size:14px;line-height:21px;">{{date('d-m-Y', strtotime($booking->tanggal_mulai))}} to {{date('d-m-Y', strtotime($booking->tanggal_selesai))}}</td>
								</tr>
								<tr>
									<td style="font-size:14px;line-height:21px;font-weight:bold;">No of {{$label}}:</td>
									<td style="font-size:14px;line-height:21px;">{{$inap}}</td>
								</tr>
								<tr>
									<td style="font-size:14px;line-height:21px;font-weight:bold;">Guest:</td>
									<td style="font-size:14px;line-height:21px;">{{$booking->tamu_dewasa+$booking->tamu_anak+$booking->tamu_bayi}} ({{$tamu_dewasa}} Guest, {{$tamu_anak}} Childern, {{$tamu_bayi}} Infants)</td>
								</tr>
								<tr>
									<td style="font-size:14px;line-height:21px;font-weight:bold;">Price Per {{$label}}</td>
									<td style="font-size:14px;line-height:21px;">Rp. {{number_format($booking->harga_tampil)}}</td>
								</tr>
							</table><br>
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
					</td>
				</tr>
				<tr>
					<td style="padding:12px 16px">						
						<p style="margin:0;font-size:16px;line-height:24px">Thank you for choosing Villanesia</p>						
					</td>
				</tr>
				<tr>
					<td>
						<div style="border-top:1px solid #dee2ee"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="vertical-align:center;text-align:center;padding-top:16px"> 
                            <a href="https://www.facebook.com/villanesia" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="Facebook villanesia.com Link" alt="Facebook villanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-facebook-48.png')}}" width="20"> </a>                                     
                            <a href="https://www.instagram.com/villanesia/" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="instagram villanesia.com Link" alt="LinkedIn villanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-instagram-48.png')}}" width="20"> </a> 
                            <a href="https://villanesia.com" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="Website vilanesia.com Link" alt="Website vilanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-internet-48.png')}}" width="20"> </a>
                        </div>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
</body>
</html>