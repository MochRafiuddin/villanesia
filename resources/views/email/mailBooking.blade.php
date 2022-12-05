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
                            <p style="margin:0 0 16px;font-size:14px;line-height:21px"> {{$nama_properti}} <br>Check-in date : {{date('l, j F Y', strtotime($tanggal_check_in))}}</p>
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