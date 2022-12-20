<html>
<head>
	<title></title>
</head>
<body>
	<?php

	/*echo "Pembayaran anda gagal<br>";*/
	//$data = file_get_contents("php://input");
	//print_r($tampung);

	?>
	<script>
		
		var tampung_data = <?= json_encode($all_data); ?>;
		var hasil = JSON.stringify(tampung_data);
		window.ReactNativeWebView.postMessage(hasil);
	
	</script>

</body>
</html>
