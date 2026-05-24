<?php

//if (isset($_GET['kode'])) {
    include '../../config/kon.php';
$kode	= $_POST['kode'];
$sql 	= mysql_query("SELECT a.nama, a.spesifikasi, a.harga, a.instansi AS kodeins, b.instansi,  
@sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur,
@akhir:=(a.umur-@sisaumur)*penyusutan AS akhir,
a.harga-@akhir AS nilaibuku
FROM inv_inventaris a, umum_instansi b WHERE status=1 and a.instansi=b.id AND a.kode='$kode'");
$row	= mysql_num_rows($sql);
if($row>0){
	$r = mysql_fetch_array($sql);
	$data['nama'] = $r['nama'];
	$data['spek'] = $r['spesifikasi'];
	$data['instansi'] = $r['instansi'];
	$data['kodeins'] = $r['kodeins'];
	$data['harga'] = number_format($r['harga'],0,',','.');
	$data['nilaibuku'] = number_format($r['nilaibuku'],0,',','.');
	echo json_encode($data);
}else{
	$data['nama'] = '';
	$data['spek'] = '';
	$data['instansi'] = '';
	$data['kodeins'] = '';
	$data['harga'] = '';
	$data['nilaibuku'] = '';
	echo json_encode($data);
}
//}
?>