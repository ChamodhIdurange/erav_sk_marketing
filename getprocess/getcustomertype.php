<?php 
require_once('../connection/db.php');

$record=$_POST['recordID'];

$sql="SELECT * FROM `tbl_customer_type` WHERE `idtbl_customer_type`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_customer_type'];
$obj->customertype=$row['customertype'];

echo json_encode($obj);
?>