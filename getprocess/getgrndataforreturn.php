<?php
require_once('../connection/db.php');
session_start();
$grnId = $_POST['grnId'];

$sql = "SELECT `d`.`tbl_product_idtbl_product`, `d`.`idtbl_grndetail`, `p`.`product_name`, `d`.`unitprice`, `d`.`qty`, `i`.`idtbl_grn` FROM `tbl_grn` AS `i` LEFT JOIN `tbl_grndetail` AS `d` ON (`i`.`idtbl_grn` = `d`.`tbl_grn_idtbl_grn`) LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`) WHERE `i`.`idtbl_grn` = '$grnId'";
$result = $conn->query($sql);
$array = array();


while ($row = $result->fetch_assoc()) { 
    array_push($array, array( "grnid" => $row['idtbl_grn'], "productid" => $row['tbl_product_idtbl_product'], "grndetailid" => $row['idtbl_grndetail'], "productname" => $row['product_name'], "unitprice" => $row['unitprice'], "qty" => $row['qty']));
}

print(json_encode($array));

?>


