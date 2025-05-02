<?php session_start();

if( !isset($_SESSION['userid'])) {
    header ("Location:index.php");
}
require_once('../connection/db.php'); //die('bc');
$userID=$_SESSION['userid'];

$updatedatetime=date('Y-m-d h:i:s');

$unitprice=$_POST['unitprice'];
$record=$_POST['hiddenid'];
$discount=$_POST['discount'];
$qty=$_POST['qty'];
$prevtot=$_POST['hiddentotal'];
$hiddendiscount=$_POST['hiddendiscount'];
$total=$_POST['total'];
$mainID=$_POST['mainID'];

$tot = $total - ($total * ($discount/100));
 
if($prevtot !=  $total || $hiddendiscount != $discount ){
    $query="UPDATE `tbl_grn_return_details` SET `qty`='$qty',`discount`='$discount', `total`='$tot',`updatedatetime`='$updatedatetime',`tbl_user_idtbl_user`='$userID' WHERE `idtbl_grn_return_details`='$record'";
        
        if($conn->query($query)==true) {
            $query1="UPDATE `tbl_grn_return` SET `total`=(`total` - '$prevtot' + '$tot') WHERE `idtbl_grn_return`='$mainID'";
            
            if($conn->query($query1)==true) {
                header("Location:../allgrnreturn.php?action=6");
            }else{
                header("Location:../allgrnreturn.php");
            }
        }else{

        }

}else{
    header("Location:../allgrnreturn.php");
}
?>

