<?php 
require_once('../connection/db.php');

$validfrom = $_POST['validfrom'];
$validto = $_POST['validto'];
$customerID = $_POST['customer'];
$rep = $_POST['rep'];

$totalAmount = 0;

$sql = "SELECT `u`.`idtbl_invoice`,`u`.`invoiceno`, `uf`.`nettotal`, `ub`.`area`, `uc`.`name` AS `cusname`, `ue`.`name` AS `repname`
        FROM `tbl_invoice` AS `u`
        LEFT JOIN `tbl_customer` AS `uc` ON `u`.`tbl_customer_idtbl_customer` = `uc`.`idtbl_customer`
        LEFT JOIN `tbl_customer_order` AS `uf` ON `u`.`tbl_customer_order_idtbl_customer_order` = `uf`.`idtbl_customer_order`
        LEFT JOIN `tbl_employee` AS `ue` ON `uf`.`tbl_employee_idtbl_employee` = `ue`.`idtbl_employee`
        LEFT JOIN `tbl_area` AS `ub` ON `u`.`tbl_area_idtbl_area` = `ub`.`idtbl_area`
        LEFT JOIN `tbl_invoice_detail` AS `ud` ON `u`.`idtbl_invoice` = `ud`.`tbl_invoice_idtbl_invoice`
        WHERE `u`.`date` BETWEEN '$validfrom' AND '$validto'
        AND `uf`.`delivered` = '1'";

if ($rep > 0) {
    $sql .= "AND `uf`.`tbl_employee_idtbl_employee` = '$rep'";
}

$sql .= " GROUP BY `u`.`idtbl_invoice`";
$result = $conn->query($sql);


if ($result->num_rows == 0) {
    echo "<div style=\"color: red; font-size:20px;\">No Records</div>";
    return;
}

$html = '<table class="table table-striped table-bordered table-sm small" id="reportTable">
    <thead>
        <tr>
            <th>Invoice</th>
            <th class="text-center">Customer</th>
            <th class="text-center">Rep</th>
            <th class="text-center">Area</th>
            <th class="text-center">Amount</th>
        </tr>
    </thead>
    <tbody>';

$totalAmount = 0;

while($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $row['invoiceno'] . '</td>
        <td class="text-center">' . $row['cusname'] . '</td>
        <td class="text-center">' . $row['repname'] . '</td>
        <td class="text-center">' . $row['area'] . '</td>
        <td class="text-center">' . number_format($row['nettotal'], 2) . '</td>
    </tr>';
    $totalAmount += $row['nettotal'];
}

$html .= '</tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-center"><strong>Total</strong></td>
            <td class="text-center"><strong>' . number_format($totalAmount, 2) . '</strong></td>
        </tr>
    </tfoot>
</table>';

echo $html;
?>
