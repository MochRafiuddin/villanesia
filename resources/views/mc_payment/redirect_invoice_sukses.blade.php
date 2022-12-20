<html>
<head>
	<title></title>
</head>
<body>
	<?php

	/*echo "Pembayaran anda berhasil<br>";*/
	
	/*if(!empty($_GET['transaction_id'])){
		
		$tampung = $_GET['transaction_id'];
		
		$conn = new mysqli('localhost', 'root', '', 'villanesia');
		
		if ($conn -> connect_errno) {
		  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		  exit();
		}
		
		//if($_SERVER['HTTP_X_CALLBACK_TOKEN'] == "d77b93a390360207d9394c5d81f1e286252d4eeb4ba5e4e9cf8988d565760c76"){
			$data = file_get_contents("php://input");
			$input = json_decode($data);

			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			$sql = "SELECT a.*, b.id_bahasa, b.id_ref_bahasa, b.judul, b.alamat, b.harga_tampil, b.total_rating, b.nilai_rating, b.nama_file, c.nama_status_booking, CONCAT(e.nama_depan,' ',e.nama_belakang) as nama_pemilik_properti, CONCAT(g.nama_depan,' ',g.nama_belakang) as nama_pemesan, h.nama_tipe_properti
					FROM t_booking a
					left join m_properti b on a.id_ref = b.id_ref_bahasa 
					left join m_status_booking c on a.id_status_booking = c.id_ref_bahasa
					left join m_users d on d.id_user = b.created_by
					left join m_customer e on d.id_ref = e.id
					left join m_users f on f.id_user = a.id_user
					left join m_customer g on f.id_ref = g.id
					left join m_tipe_properti h on h.id_ref_bahasa = b.id_tipe_booking and h.id_bahasa = 1
					where
					a.deleted = 1 and b.deleted = 1 and d.deleted = 1 and f.deleted = 1 and b.id_bahasa = 1 and a.pg_transaction_id = '$tampung' LIMIT 1";
			
			$tam = $conn->query($sql);
			print_r(mysql_fetch_assoc($tam));
			
			if ($conn->query($sql) === TRUE) {
			  print_r(mysqli_query($sql));
			} else {
			  echo "Error: " . $sql . "<br>" . $conn->error;
			}

			$conn->close();

			$txt = date("Y-m-d H:i:s")." -> ".$data;
			$myfile = file_put_contents('callback_invoice.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
		//}else{
		//	echo "Invalid Token!!";
		//}
		
	}*/
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
