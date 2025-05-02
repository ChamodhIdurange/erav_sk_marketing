<?php
require_once('../connection/db.php');
session_start();
$supplierId=$_POST['supplierId'];
$invoiceId=$_POST['invoiceId'];

$array = [];

$sql="SELECT  `g`.`idtbl_grn`, `g`.`invoicenum` FROM `tbl_grn` AS `g` LEFT JOIN `tbl_porder` AS `p` ON (`g`.`tbl_porder_idtbl_porder` = `p`.`idtbl_porder`) WHERE `p`.`tbl_supplier_idtbl_supplier` = '$supplierId' AND `g`.`status` = '1' AND `g`.`transferstatus` = '1'";
$result =$conn-> query($sql); 

while ($row = $result-> fetch_assoc()) { 
    $obj=new stdClass();
    $obj->grnId=$row['idtbl_grn'];
    $obj->invoicenum= $row['invoicenum'];
    array_push($array,$obj);
}

echo json_encode($array);
?>