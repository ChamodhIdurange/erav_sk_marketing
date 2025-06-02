<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');
$userID=$_SESSION['userid'];

$recordOption=$_POST['recordOption'];
if(!empty($_POST['recordID'])){$recordID=$_POST['recordID'];}
$customertype=addslashes($_POST['customertype']);
$updatedatetime=date('Y-m-d h:i:s');

if($recordOption==1){
    $insert="INSERT INTO `tbl_customer_type`(`customertype`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$customertype','1','$updatedatetime','$userID')";
    if($conn->query($insert)==true){        
        header("Location:../customertypes.php?action=4");
    }
    else{header("Location:../customertypes.php?action=5");}
}
else{
    $update="UPDATE `tbl_customer_type` SET `customertype`='$customertype',`updatedatetime`='$updatedatetime',`tbl_user_idtbl_user`='$userID' WHERE `idtbl_customer_type`='$recordID'";
    if($conn->query($update)==true){     
        header("Location:../customertypes.php?action=6");
    }
    else{header("Location:../customertypes.php?action=5");}
}
?>