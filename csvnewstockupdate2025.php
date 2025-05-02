<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('connection/db.php');//die('bc');
$userID=$_SESSION['userid'];

$updatedatetime=date('Y-m-d h:i:s');
$filename = "process/stockupdate2025V3.csv";

$file1 = fopen($filename, 'r');
$file2 = fopen($filename, 'r');
$c = 0;
$x = 0;
$month = date('m');
$year = substr(date('y'), -2);;

$total = 0;
$batchNo = "BTH".$year.$month.sprintf('%04s', '0000');

while (($line = fgetcsv($file2)) !== FALSE) {
    $x++;
    $productId = $line[0];
    $productCode = $line[2];
    $retailPrice = $line[4];
    $availableqty = $line[5];
    $holdQty = $line[6];

    if($x == 1){
        continue;
    }
    $sqlproduct="SELECT `idtbl_product`, `unitprice`, `saleprice` FROM `tbl_product` WHERE `idtbl_product`='$productId' AND `status` = 1";
    $result=$conn->query($sqlproduct);
    $row=$result->fetch_assoc();

    $unitPrice = $row['unitprice'];
    $salePrice = $row['saleprice'];
    // $productId = $row['idtbl_product'];

    // if($productId == 0){
    //     echo $productCode;
    //     echo '////';

    // }

    // $sqlupdate = "UPDATE `tbl_product` SET `saleprice` = '$retailPrice', `retail` = '$wholePrice', `unitprice` = '$unitPrice' WHERE `idtbl_product` = '$productId'";
    // $conn->query($sqlupdate);

    $insertstock="INSERT INTO `tbl_stock` (`batchqty`, `qty`, `unitprice`, `saleprice`, `update`, `status`, `batchno`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`) VALUES ('$availableqty', '$availableqty', '$unitPrice', '$salePrice', '$updatedatetime', '1', '$batchNo', '$updatedatetime', '$userID', '$productId')";
    $conn->query($insertstock);
}



print_r($x);

?>

