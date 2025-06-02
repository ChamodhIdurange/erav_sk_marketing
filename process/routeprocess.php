<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');
$userID=$_SESSION['userid'];

$recordOption=$_POST['recordOption'];
if(!empty($_POST['recordID'])){$recordID=$_POST['recordID'];}
$route=addslashes($_POST['route']);
$updatedatetime=date('Y-m-d h:i:s');

if($recordOption==1){
    $insert="INSERT INTO `tbl_route`(`route`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$route','1','$updatedatetime','$userID')";
    if($conn->query($insert)==true){        
        header("Location:../route.php?action=4");
    }
    else{header("Location:../route.php?action=5");}
}
else{
    $update="UPDATE `tbl_route` SET `route`='$route',`updatedatetime`='$updatedatetime',`tbl_user_idtbl_user`='$userID' WHERE `idtbl_route`='$recordID'";
    if($conn->query($update)==true){     
        header("Location:../route.php?action=6");
    }
    else{header("Location:../route.php?action=5");}
}
?>