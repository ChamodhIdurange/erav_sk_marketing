<?php session_start();

require_once('../connection/db.php'); //die('bc');
$userID = $_SESSION['userid'];

$updatedatetime = date('Y-m-d h:i:s');
$tableData = $_POST['tableData'];

$suppliergrn = $_POST['suppliergrn'];
$total = $_POST['total'];
$invoicestatus = $_POST['invoicestatus'];
$supplier = $_POST['supplier'];
$remarks = $_POST['remarks'];

$today = date('Y-m-d');
    


$query = "INSERT INTO `tbl_grn_return`(`returndate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `acceptance_status`, `total`, `damaged_reason`, `credit_note`, `credit_note_issue`, `tbl_grn_idtbl_grn`, `tbl_supplier_idtbl_supplier`) VALUES ('$today','1','$updatedatetime','$userID', '0', '$total', '$remarks', '0', '0', '$suppliergrn', '$supplier')";

if ($conn->query($query) == true) {
    $last_id = mysqli_insert_id($conn);

    foreach ($tableData as $rowtabledata) {
        $productID = $rowtabledata['col_1'];
        $qty = $rowtabledata['col_4'];
        $discount = $rowtabledata['col_5'];
        $subtotal = $rowtabledata['col_6'];
        $unitprice = $rowtabledata['col_11'];

        $insertreturndetails = "INSERT INTO `tbl_grn_return_details`(`unitprice`, `qty`, `actualqty`, `discount`, `total`, `tbl_product_idtbl_product`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_grn_return_idtbl_grn_return`) VALUES ('$unitprice','$qty', '0', '$discount', '$subtotal', '$productID','$updatedatetime','$userID','$last_id')";
        $conn->query($insertreturndetails);
    }
    // echo $insertreturndetails;
    $actionObj = new stdClass();
    $actionObj->icon = 'fas fa-check-circle';
    $actionObj->title = '';
    $actionObj->message = 'Add Successfully';
    $actionObj->url = '';
    $actionObj->target = '_blank';
    $actionObj->type = 'success';

    echo $actionJSON = json_encode($actionObj);
} else {
    $actionObj = new stdClass();
    $actionObj->icon = 'fas fa-times';
    $actionObj->title = '';
    $actionObj->message = 'Something went wrong';
    $actionObj->url = '';
    $actionObj->target = '_blank';
    $actionObj->type = 'danger';

    echo $actionJSON = json_encode($actionObj);

}