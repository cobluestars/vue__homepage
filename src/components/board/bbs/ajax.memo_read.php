<?php
include_once('./_common.php');

$sender = $_POST["sender"];
$receiver = $_POST["receiver"];
$from_record = "20";

$sqla = "select * from {$g5['memo_table']} where (me_send_mb_id ='".$sender."' and me_recv_mb_id = '".$receiver."') or (me_send_mb_id = '".$receiver."' and me_recv_mb_id = '".$sender."') order by me_send_datetime asc";

$res=sql_query($sqla);  

$resa="";
for($i=0;$row=sql_fetch_array($res);$i++) {

	$ch="N";
	 if(substr($row['me_read_datetime'],0,1) != 0) {
		 $ch="Y";
	 }
	 $resa=$resa.$row["me_id"]."|".$ch."--";
}
echo $resa;
?>
