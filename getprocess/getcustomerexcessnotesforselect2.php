<?php 
require_once('../connection/db.php');

$customerId = $_POST['customerId'];


if(!isset($_POST['searchTerm'])){ 
    $sql="SELECT `u`.`idtbl_invoice_excess_payment`, `u`.`excess_amount`, `c`.`name` FROM `tbl_invoice_excess_payment` AS `u` LEFT JOIN `tbl_customer` AS `c` ON (`u`.`tbl_customer_idtbl_customer` = `c`.`idtbl_customer`) WHERE `u`.`status`=1 AND `u`.`paystatus`=0 AND `u`.`excess_amount`>0";
}else{
    $search = $_POST['searchTerm'];   
    $sql="SELECT `u`.`idtbl_invoice_excess_payment`, `u`.`excess_amount`, `c`.`name` FROM `tbl_invoice_excess_payment` AS `u` LEFT JOIN `tbl_customer` AS `c` ON (`u`.`tbl_customer_idtbl_customer` = `c`.`idtbl_customer`) WHERE `u`.`status`=1 AND `u`.`paystatus`=0 AND `u`.`excess_amount`>0 AND `c`.`name` LIKE '%$search%'";
}
$result=$conn->query($sql);


$arraylist=array();


while($row = $result->fetch_assoc()) {
    $obj = new stdClass();
    $obj->id = $row['idtbl_invoice_excess_payment'];
    $obj->text = 'EX' . $row['idtbl_invoice_excess_payment'] . ' - ' . number_format($row['excess_amount'], 2) . ' / ' . $row['name'];
    $obj->excessnoteamount = $row['excess_amount']; 

    array_push($arraylist, $obj);
}

echo json_encode($arraylist);
?>