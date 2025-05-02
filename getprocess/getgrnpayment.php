<?php 
require_once('../connection/db.php');
if(!empty($_POST['grnno'])){
    $grnno=$_POST['grnno'];
    // $invoiceID=substr($invoiceno, 9);

    $sql="SELECT DATEDIFF(CURDATE(), `tbl_grn`.`date`) AS `date_diff`, `tbl_porder`.`remark`, `tbl_supplier`.`suppliername`, `tbl_grn`.`idtbl_grn`, `tbl_grn`.`paymentcomplete`, `tbl_grn`.`date`, `tbl_grn`.`nettotal`, SUM(`tbl_grn_payment_has_tbl_grn`.`payamount`) AS `payamount` FROM `tbl_grn` LEFT JOIN `tbl_grn_payment_has_tbl_grn` ON `tbl_grn_payment_has_tbl_grn`.`tbl_grn_idtbl_grn`=`tbl_grn`.`idtbl_grn` LEFT JOIN `tbl_porder` ON `tbl_porder`.`idtbl_porder`=`tbl_grn`.`tbl_porder_idtbl_porder` LEFT JOIN `tbl_supplier` ON `tbl_supplier`.`idtbl_supplier` = `tbl_porder`.`tbl_supplier_idtbl_supplier` WHERE `tbl_grn`.`idtbl_grn`='$grnno' AND `tbl_grn`.`status`=1 AND `tbl_grn`.`paymentcomplete`=0  Group BY `tbl_grn`.`idtbl_grn`";
    $result=$conn->query($sql);
}
else if(!empty($_POST['supplierId'])){
    $supplierId=$_POST['supplierId'];

    $sql="SELECT DATEDIFF(CURDATE(), `tbl_grn`.`date`) AS `date_diff`, `tbl_porder`.`remark`, `tbl_supplier`.`suppliername`, `tbl_grn`.`idtbl_grn`, `tbl_grn`.`paymentcomplete`, `tbl_grn`.`date`, `tbl_grn`.`nettotal`, SUM(`tbl_grn_payment_has_tbl_grn`.`payamount`) AS `payamount` FROM `tbl_grn` LEFT JOIN `tbl_grn_payment_has_tbl_grn` ON `tbl_grn_payment_has_tbl_grn`.`tbl_grn_idtbl_grn`=`tbl_grn`.`idtbl_grn` LEFT JOIN `tbl_porder` ON `tbl_porder`.`idtbl_porder`=`tbl_grn`.`tbl_porder_idtbl_porder` LEFT JOIN `tbl_supplier` ON `tbl_supplier`.`idtbl_supplier` = `tbl_porder`.`tbl_supplier_idtbl_supplier` WHERE `tbl_porder`.`tbl_supplier_idtbl_supplier`='$supplierId' AND `tbl_grn`.`status`=1 AND `tbl_grn`.`paymentcomplete`=0  Group BY `tbl_grn`.`idtbl_grn`";
    $result=$conn->query($sql);
}
?>
<table class="table table-striped table-bordered table-sm" id="paymentDetailTable">
    <thead>
        <tr>
            <th class="d-none">Grn Id</th>
            <th>PO No</th>
            <th>Supplier</th>
            <th>Date</th>
            <th class="text-right">Amount</th>
            <th class="text-right">Paid Amount</th>
            <th class="text-right">Balance</th>
            <th>Full Payment</th>
            <th>Half Payment</th>
            <th class="text-right">Payment</th>
            <th>Remarks</th>
            <th>Aging</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td class="d-none"><?php echo $row['idtbl_grn']; ?></td>
            <td><?php echo $row['idtbl_grn']; ?></td>
            <td><?php echo $row['suppliername']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', $row['nettotal']); ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', $row['payamount']); ?></td>
            <td class="text-right"><?php echo sprintf('%.2f', ($row['nettotal']-$row['payamount'])); ?></td>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input fullAmount" name="payCheck1" id="payCheck1<?php echo $row['idtbl_grn']; ?>" value="1" <?php if($row['payamount']>0){echo 'disabled';} ?>>
                    <label class="custom-control-label small" for="payCheck1<?php echo $row['idtbl_grn']; ?>">Full Payment</label>
                </div>
            </td>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input halfAmount" name="payCheck2" id="payCheck2<?php echo $row['idtbl_grn']; ?>">
                    <label class="custom-control-label small" for="payCheck2<?php echo $row['idtbl_grn']; ?>">Half Payment</label>
                </div>
            </td>
            <td class='paidAmount text-right'>0.00</td>            
            <td class='d-none'></td>            
            <td class='text-center'><?php echo $row['remark']; ?></td>            
            <td class='text-center'><?php echo $row['date_diff']; ?></td>            
        </tr>
        <?php } ?>
    </tbody>
</table>