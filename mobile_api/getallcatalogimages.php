<?php

require_once('../connection/db.php');


$sql="SELECT `tbl_product_image`.`idtbl_product_image`, `tbl_product_image`.`imagepath`, `tbl_product_image`.`tbl_catalog_idtbl_catalog`, `tbl_catalog_category`.`category`, `tbl_catalog_category`.`sequence` FROM `tbl_product_image` LEFT JOIN  `tbl_catalog` ON `tbl_catalog`.`idtbl_catalog`=`tbl_product_image`.`tbl_catalog_idtbl_catalog` LEFT JOIN `tbl_catalog_category` ON `tbl_catalog_category`.`idtbl_catalog_category`=`tbl_catalog`.`tbl_catalog_category_idtbl_catalog_category` WHERE `tbl_catalog`.`status`=1 ORDER BY `tbl_catalog_category`.`sequence` ASC";
$res = $conn->query($sql);
$result = array();

while ($row = mysqli_fetch_array($res)) {
    $catelogId =  $row['tbl_catalog_idtbl_catalog'];

    $sqlcatelogstock = "SELECT SUM(`s`.`qty`) AS `avialableqty`, `s`.`tbl_product_idtbl_product` FROM `tbl_catalog_details` as `d` LEFT JOIN `tbl_stock` AS `s` ON (`d`.`product_name`=`s`.`tbl_product_idtbl_product`) WHERE `d`.`tbl_catalog_idtbl_catalog`='$catelogId' GROUP BY `s`.`tbl_product_idtbl_product` HAVING `avialableqty` > 0";
    $stockResult = $conn->query($sqlcatelogstock);


    if ($rowstock = $stockResult->fetch_assoc()) {
        $avialableqty = $rowstock['avialableqty'];
        $productId = $rowstock['tbl_product_idtbl_product'];

        $sqlholdstock="SELECT COALESCE(SUM(`qty`), 0) as `qty` FROM `tbl_customer_order_hold_stock` WHERE `tbl_product_idtbl_product`='$productId' AND `status` = '1' AND `invoiceissue` = '0' GROUP BY `tbl_product_idtbl_product`";
        $resultholdstock = $conn->query($sqlholdstock);

        if ($rowholdstock = $resultholdstock->fetch_assoc()) {
            $holdqty = $rowholdstock['qty'];
        } else {
            $holdqty = 0;
        }

        if($avialableqty-$holdqty > 0){
            array_push($result, array(
                "id" => $row['idtbl_product_image'],
                "path" => $row['imagepath'],
                "catalog_id" => $catelogId,
                "category" => $row['category'],
                "sequence" => $row['sequence']
            ));
        }
    }

}

print(json_encode($result));
