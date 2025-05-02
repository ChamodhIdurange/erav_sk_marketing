<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location:../index.php");
}
require_once('../connection/db.php');


$updatedatetime = date('Y-m-d h:i:s');
$currentDate = date('mdY');
$userID = $_SESSION['userid'];
$record = $_GET['record'];
// $type=$_GET['type'];
$sqlReturn = "SELECT `total`, `tbl_supplier_idtbl_supplier` FROM `tbl_grn_return` WHERE `idtbl_grn_return` = '$record'";
$resultReturn = $conn->query($sqlReturn);
$rowReturn = $resultReturn->fetch_assoc();
$returntotal =  $rowReturn['total'];
$supplierid =  $rowReturn['tbl_supplier_idtbl_supplier'];

$sqlgetqty = "SELECT `qty`, `tbl_product_idtbl_product` FROM `tbl_grn_return_details` WHERE `tbl_grn_return_idtbl_grn_return` = '$record'";
$resultsqlgetqty = $conn->query($sqlgetqty);

$sql = "UPDATE `tbl_grn_return` SET `acceptance_status`='1',`tbl_user_idtbl_user`='$userID' WHERE `idtbl_grn_return`='$record'";
if ($conn->query($sql) == true) {
    $sqlcredit = "INSERT INTO `tbl_grn_creditenote`(`returnamount`, `payAmount`, `balAmount`, `baltotalamount`, `settle`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_supplier_idtbl_supplier`) VALUES ('$returntotal', 0, '$returntotal', 0, 0, 1, '$updatedatetime', '$userID', '$supplierid')";

    echo $sqlcredit;
    $conn->query($sqlcredit);
    $noteId = mysqli_insert_id($conn);


    $sqlcreditdetail = "INSERT INTO `tbl_grn_creditenote_detail`(`returntotal`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_grn_return_idtbl_grn_return`, `tbl_grn_creditenote_idtbl_grn_creditenote`) VALUES ('$returntotal', 1, '$updatedatetime', '$userID', '$record', '$noteId')";
    $conn->query($sqlcreditdetail);

    if ($resultsqlgetqty->num_rows > 0) {
        while ($row = $resultsqlgetqty->fetch_assoc()) {
            $qty = $row['qty'];
            $reducedqty = $row['qty'];
            $product = $row['tbl_product_idtbl_product'];

            $getstock = "SELECT * FROM `tbl_stock` WHERE `qty` > 0 AND `tbl_product_idtbl_product` = '$product' ORDER BY SUBSTRING(batchno, 4) ASC LIMIT 1";
            $result = $conn->query($getstock);
            $stockdata = $result->fetch_assoc();

            $stockbatch = $stockdata['batchno'];
            $batchqty = $stockdata['qty'];

            while($batchqty < $reducedqty){
                $updatestock="UPDATE `tbl_stock` SET `qty`=0 WHERE `tbl_product_idtbl_product`='$product' AND `batchno` = '$stockbatch'";
                $conn->query($updatestock);
    
                $reducedqty = $reducedqty - $batchqty;
    
                $regetstock = "SELECT * FROM `tbl_stock` WHERE `qty` > 0 AND `tbl_product_idtbl_product` = '$product' ORDER BY SUBSTRING(batchno, 4) ASC LIMIT 1";
                $reresult =$conn-> query($regetstock); 
                $restockdata = $reresult-> fetch_assoc();
    
                $stockbatch = $restockdata['batchno'];
                $batchqty = $restockdata['qty'];
    
                if($batchqty > $reducedqty){
                    break;
                }
            }
            // echo $reducedqty;
    
            $updatestock="UPDATE `tbl_stock` SET `qty`=(`qty`-'$reducedqty') WHERE `tbl_product_idtbl_product`='$product' AND `batchno` = '$stockbatch'";
            $conn->query($updatestock);

            
        }
    }
    header("Location:../allgrnreturn.php?action=6");


} else {
    header("Location:../allgrnreturn.php?action=5");
}
