<html>
<head></head>
<body>
	<div>
		<table style="max-width:600px;margin:0 auto;background-color:#ffffff;border-spacing:0;border-collapse:collapse" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" align="center">
			<tbody>
				<tr>
					<td>
						<u></u>
						<div style="margin:0;padding:26px 0 40px 0;width:100%;line-height:1.5rem;letter-spacing:0;font-family:Open Sans,sans-serif">
							<div style="width:600px;margin:0px auto;background-color:#ffffff;border-radius:8px">
								<div style="padding:40px 32px 0px"> 
									<img src="{{$message->embed(public_path().'/assets/images/VILLANESIA.png')}}" alt="Villanesia" width="450">
								</div>
								<div style="padding:56px 32px 48.4px">									
									<div>
										<p style="line-height:1.5rem;letter-spacing:0;font-size:16px;margin-top:16px"> Hello {{$nama_depan}} {{$nama_belakang}}</p>
                                        <p style="line-height:1.5rem;letter-spacing:0;font-size:16px;margin-top:16px"> We have received payment for your order. Now you can enjoy all the services from us according to your order.</p>
                                        <p style="line-height:1.5rem;letter-spacing:0;font-size:16px;margin-top:16px"> Thank you for choosing Villanesia</p>
								<table style="max-width:600px;border-spacing:0;border-collapse:collapse" width="100%">
                                    <tr>
                                        <td width="100%" style="padding:12px 16px">
                                            <h2 style="background:#f15e75;font-size:16px;line-height:24px;margin:0;padding:8px 16px;border-color:#dee2ee;border-style:solid;border-width:1px 1px 0;color:white;"> Order Details </h2>
											<div style="border-color:#dee2ee;border-style:solid;border-width:0 1px 1px;padding:16px;">
											<table width='100%'>
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
												$cek_cus = DB::table('t_booking_harga_satuan')->where('id_booking',$id_booking)->where('custom_periode',1)->get()->count();
                                				if ($cek_cus > 0) {
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
										<tr>
											<td width='25%' style="font-size:14px;line-height:21px;font-weight: bold;">Property</td>
											<td width='75%' style="font-size:14px;line-height:21px;">{{$booking->judul}}</td>
										</tr>
										<tr>
											<td width='25%' style="font-size:14px;line-height:21px;font-weight: bold;">Rented By</td>
											<td width='75%' style="font-size:14px;line-height:21px;">{{$nama_depan}} {{$nama_belakang}}</td>
										</tr>
										<tr>
											<td width='25%' style="font-size:14px;line-height:21px;font-weight: bold;">Email</td>
											<td width='75%' style="font-size:14px;line-height:21px;">{{$email}}</td>
										</tr>
										<tr>
											<td width='25%' style="font-size:14px;line-height:21px;font-weight: bold;">Phone</td>
											<td width='75%' style="font-size:14px;line-height:21px;">{{$no_telfon}}</td>
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
												You Pay
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
                                </table>									
								<br>
								<div style="letter-spacing:0;font-size:16px;margin-top:24px"> Best Regards,</div>
								<div style="letter-spacing:0;font-size:16px;font-weight:600"> Villanesia Team</div>									

								</div>
                                <table style="margin:0 auto;max-width:600px;height:180px;background-color:#f15e75;border-spacing:0;border-collapse:collapse" width="100%" height="100%">
                                    <tr>
                                        <td width="35%" align="center">
                                            <img src="{{$message->embed(public_path().'/assets/images/design-logo-VILLANESIA-putih-02-150x150.png')}}" width="80" alt="">
                                            <p style="font-size:13px;font-weight:600;color:white;">Managed by Jetwings Bali</p>
                                        </td>
                                        <td width="65%">
                                            <p style="margin:-70px 0 10px 0;font-size:15px;color:white;">Jl. Pulau Moyo No. 34B Pedungan, Denpasar Selatan – Bali, Indonesia</p>
                                            <p style="margin:0 0 0 0;font-size:15px;color:white;"><img src="{{$message->embed(public_path().'/assets/images/hotline-50.png')}}" alt="" width="20"> +62 812 3816 7828 / +62 813 3797 9669</p>
                                        </td>
                                    </tr>
                                </table>
                                <div style="vertical-align:center;text-align:center;padding-top:16px"> 
                                    <a href="https://www.facebook.com/villanesia" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="Facebook villanesia.com Link" alt="Facebook villanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-facebook-48.png')}}" width="20"> </a>                                     
                                    <a href="https://www.instagram.com/villanesia/" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="instagram villanesia.com Link" alt="LinkedIn villanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-instagram-48.png')}}" width="20"> </a> 
                                    <a href="https://villanesia.com" style="text-decoration:none;padding-right:4px" target="_blank"> <img title="Website vilanesia.com Link" alt="Website vilanesia.com Link" src="{{$message->embed(public_path().'/assets/images/icons8-internet-48.png')}}" width="20"> </a>
                                </div>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
</body>
</html>