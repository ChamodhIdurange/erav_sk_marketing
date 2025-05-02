<?php 
require_once('../connection/db.php');

$record=$_POST['invoiceno'];

$sql="SELECT `tbl_customer_idtbl_customer` FROM `tbl_invoice` WHERE `invoiceno`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->customerId=$row['tbl_customer_idtbl_customer'];

echo json_encode($obj);


?>