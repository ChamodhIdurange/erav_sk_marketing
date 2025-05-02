<?php
require_once('../connection/db.php');

session_start();



$id = $_POST['id'];

if (isset($id) && !empty($id)) {
    $stmt = $conn->prepare("SELECT `tbl_return`.`total`, SUM(`tbl_creditenote`.`payAmount`) AS `payAmount` FROM `tbl_return` LEFT JOIN `tbl_creditenote` ON (`tbl_creditenote`.`returnid` = `tbl_return`.`idtbl_return`) WHERE `idtbl_return` = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $obj = new stdClass();
            $obj->id = $id;
            $obj->total = $row['total'];
            if ($row['payAmount'] != null) {
                $obj->payAmount = $row['payAmount'];
            }else{
                $obj->payAmount = 0;
            }
            echo json_encode($obj);
        } else {
            echo json_encode(['error' => 'No record found']);
        }
    } else {
        echo json_encode(['error' => 'Query execution failed']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid ID']);
}

$conn->close();
