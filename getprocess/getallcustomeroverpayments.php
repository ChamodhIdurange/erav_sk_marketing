<?php 
require_once('../connection/db.php');

$validfrom = $_POST['validfrom'];
$validto = $_POST['validto'];
$customerID = isset($_POST['customer']) ? $_POST['customer'] : null;

$sql = "SELECT `c`.`returnamount`, `c`.`baltotalamount`, `c`.`payAmount`, `c`.`settle`, `cu`.`name`
        FROM `tbl_creditenote` AS `c` 
        LEFT JOIN `tbl_creditenote_detail` AS `cd` ON (`c`.`idtbl_creditenote` = `cd`.`tbl_creditenote_idtbl_creditenote`) 
        LEFT JOIN `tbl_customer` AS `cu` ON (`cu`.`idtbl_customer` = `c`.`tbl_customer_idtbl_customer`)";

if (!empty($customerID)) {
    $sql .= " WHERE `c`.`tbl_customer_idtbl_customer` = '$customerID'";
}



// if ($customerID > 0) {
//     $sql .= " AND ";
// }

// $sql .= " GROUP BY `u`.`idtbl_invoice`";
$result = $conn->query($sql);


if ($result->num_rows == 0) {
    echo "<div style=\"color: red; font-size:20px;\">No Records</div>";
    return;
}

$html = '<table class="table table-striped table-bordered table-sm small" id="reportTable">
    <thead>
        <tr>
            <th class="text-center">Customer</th>
            <th class="text-right">Return Amount</th>
            <th class="text-right">Used Amount</th>
            <th class="text-right">Balance Amount</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>';

$totbalance = 0;

while($row = $result->fetch_assoc()) {
    $settled = 'Settled';
    $notsettled = 'Not Settled';
    $totbalance += $row['baltotalamount'];
    $html .= '<tr>
        <td class="text-center">' . $row['name'] . '</td>
        <td class="text-right">' . number_format($row['returnamount'], 2) . '</td>
        <td class="text-right">' . number_format($row['payAmount'], 2) . '</td>
        <td class="text-right">' . number_format($row['baltotalamount'], 2) . '</td>
        <td class="text-right">' . ($row['settle'] != 1 ? $notsettled : $settled) . '</td>
    </tr>';
}

$html .= '</tbody>
    <tfoot>
        <tr>
        </tr>
    </tfoot>
</table>';

echo $html;
?>
