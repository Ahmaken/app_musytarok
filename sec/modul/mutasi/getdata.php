<?php include'../../session/level3.php';?>
<?php

if (isset($_POST['kode'])) {
    include '../../config/kon.php';
$kode	= $_POST['kode'];
$sql 	= mysql_query("SELECT a.kode, a.nama, a.spesifikasi, a.harga, a.umur, a.nilaisisa/a.harga*100 as nilaisisa, a.keterangan, DATE_FORMAT(a.tglperolehan, '%m-%d-%Y')AS tglperolehan, a.instansi AS kodeins, b.instansi FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id AND a.kode='$kode'");
$row	= mysql_num_rows($sql);
if($row>0){
	$r = mysql_fetch_array($sql);
	$data['nama'] = $r['nama'];
	$data['spesifikasi'] = $r['spesifikasi'];
	$data['tglperolehan'] = $r['tglperolehan'];
	$data['harga'] = $r['harga'];
	$data['umur'] = $r['umur'];
	$data['nilaisisa'] = $r['nilaisisa'];
	$data['keterangan'] = $r['keterangan'];
	
	echo json_encode($data);
}else{
	$data['nama'] = '';
	$data['harga'] = '';
	$data['instansi'] = '';
	$data['kodeins'] = '';
	echo json_encode($data);
}
}
?>