<?php 
require_once('../connection/db.php');

if(!isset($_POST['searchTerm'])){ 
    $sql="SELECT `idtbl_supplier`, `suppliername`, `supcode` FROM `tbl_supplier`  WHERE `status`=1 LIMIT 5";

}else{
    $search = $_POST['searchTerm'];   
    $sql="SELECT `idtbl_supplier`, `suppliername`, `supcode` FROM `tbl_supplier`  WHERE `status`=1 AND `suppliername` LIKE '%$search%'";

}
$result=$conn->query($sql);


$arraylist=array();


while($row=$result->fetch_assoc()){
    $fullname = $row['suppliername'] . ' - ' . $row['supcode'];
    $obj=new stdClass();
    $obj->id=$row['idtbl_supplier'];
    $obj->text=$fullname;
    
    array_push($arraylist, $obj);
}

echo json_encode($arraylist);
?>