<?php
session_start();
require_once('../connection/db.php');
require_once '../vendor/autoload.php'; // Adjust the path as necessary

ini_set('memory_limit', '999M');
ini_set('max_execution_time', '999');



use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

$today = date('Y-m-d');
$today2 = date('Y/m');
$last_two_digits = substr($today2, 2);
$recordID = $_GET['id'];
$userID=$_SESSION['userid'];

$empty = 'null';
$fulltot = 0;
$discount = 0;
$fulldiscount = 0;
$totaloutstanding = 0;
$fulloutstanding = 0;
$totalpayment = 0;
$net_total = 0;
$newtemp = 0;

if($userID==1){
    $sqlpoprinted="UPDATE `tbl_customer_order` SET `is_printed`='1' WHERE `idtbl_customer_order`='$recordID'";
    $conn->query($sqlpoprinted);
}

$sqlporderinfo = "SELECT `o`.`cuspono`, `o`.`discount`, `o`.`podiscount`, `o`.`confirm`, `o`.`dispatchissue`, `o`.`delivered`,`o`.`remark`, `o`.`idtbl_customer_order`, `o`.`date`, `o`.`total`, `l`.`idtbl_locations`, `l`.`locationname`, `c`.`name`, `c`.`address`, `c`.`phone`, `e`.`name` AS `saleref`, `e`.`phone`, `a`.`area`, `u`.`name` as `username`, `o`.`tbl_customer_idtbl_customer`, `o`.`cuspono` FROM `tbl_customer_order` AS `o` LEFT JOIN `tbl_customer_order_detail` AS `od` ON `o`.`idtbl_customer_order`=`od`.`tbl_customer_order_idtbl_customer_order` LEFT JOIN `tbl_customer` AS `c` ON (`c`.`idtbl_customer` = `o`.`tbl_customer_idtbl_customer`) LEFT JOIN `tbl_locations` AS `l` ON (`l`.`idtbl_locations` = `o`.`tbl_locations_idtbl_locations`) LEFT JOIN `tbl_employee` AS `e` ON `e`.`idtbl_employee`=`o`.`tbl_employee_idtbl_employee` LEFT JOIN `tbl_area` AS `a` ON `a`.`idtbl_area`=`o`.`tbl_area_idtbl_area` LEFT JOIN `tbl_user` AS `u` ON `u`.`idtbl_user`=`o`.`tbl_user_idtbl_user` WHERE `o`.`status`=1 AND `o`.`idtbl_customer_order`='$recordID'";
$resultporderinfo = $conn->query($sqlporderinfo);
$rowporderinfo = $resultporderinfo->fetch_assoc();

$customerID = $rowporderinfo['tbl_customer_idtbl_customer'];
$customerPhone = $rowporderinfo['phone'];
$porderDate = $rowporderinfo['date'];
$customername = $rowporderinfo['name'];
$location = $rowporderinfo['locationname'];
$customeraddress = $rowporderinfo['address'];
$poderId = $rowporderinfo['idtbl_customer_order'];
$remark = $rowporderinfo['remark'];
$cuspono = $rowporderinfo['cuspono'];
$fulltot = $rowporderinfo['total'];

$confirm = $rowporderinfo['confirm']; 
$dispatchissue = $rowporderinfo['dispatchissue']; 
$delivered = $rowporderinfo['delivered']; 
$qtyflag=0;


if($confirm == 1 && ($dispatchissue == null || $dispatchissue == 0) && ($delivered == null || $delivered == 0)){
    $qtyflag = 1;
}else if($confirm == 1 && $dispatchissue == 1  && ($delivered == null || $delivered == 0)){
    $qtyflag = 2;
}else if($confirm == 1 && $dispatchissue == 1 && $delivered == 1){
    $qtyflag = 3;
}

$sqlporderdetail = "SELECT `p`.`product_name`, `p`.`product_code`, `p`.`idtbl_product`, `d`.`orderqty`, `d`.`confirmqty`, `d`.`discount`, `d`.`dispatchqty`, `d`.`qty`, `d`.`saleprice` FROM `tbl_customer_order_detail` AS `d` LEFT JOIN `tbl_product` AS `p` ON `p`.`idtbl_product`=`d`.`tbl_product_idtbl_product` WHERE `d`.`tbl_customer_order_idtbl_customer_order`='$recordID' AND `d`.`status`=1";
$resultporderdetail = $conn->query($sqlporderdetail);

$html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EVEREST Hardware Co</title>
        <style>
            * {
            font-size: 10px;
            margin: 0.2px;
            font-family: "San-Serif", sans-serif;
            }
            #detailtable {
                width: 100%;
                border-collapse: collapse;
                padding-left: 1cm;
                padding-right: 1cm;
                padding-top: 0.2cm;
                border: 1px solid black;
            }
            #detailtable th, #detailtable td { 
                border: 1px solid black;
                padding: 5px;
            }
            #detailth {
                background-color: #f2f2f2;
                text-align: left;
                border: 1px solid black; 
            }
            #detailtd {
                border: 1px solid black; 
            }
            .page-total {
                background-color: #f9f9f9;
                font-weight: bold;
            }

            #tablefooter {
                width: 100%;
            }

            .leftboxtop {
                padding-left: 0.3cm;
            }
            .rightboxtop {
                padding-right: 0.3cm;
            }

            /* Table header styling */
            th {
                background-color: #f2f2f2;
                font-size: 12px;
                text-align: left;
                padding: 5px;
            }

            /* Table data cell styling */
            td {
                padding: 5px;
                vertical-align: top;
                font-size: 12px;
            }
            .tdheader {
                padding: 5px;
                vertical-align: top;
                font-size: 20px;

            }

            /* Specific alignment for certain cells */
            .left-align {
                text-align: left;
            }

            .right-align {
                text-align: right;
            }

            .center-align {
                text-align: center;
            }

            /* Custom spacing */
            .spacer {
                height: 1.8cm;
            }
           
        </style>
    </head>
    <body style="height:14cm">

    <header>
        <table border="0" width="100%">
            <tr>
                <td colspan="3" height="1.8cm"></td>
            </tr>
            <tr>
                <td class="leftboxtop" width="100%">
                    <table border="0" width="100%" style="margin-top:-43; padding-left:0.3cm;">
                        <tr>
                            <td>
                                <h3 style="font-weight: bold; font-size: 20px; margin: 0;">EVEREST HARDWARE CO. (PVT) LTD;</h3>
                                <h4 style="font-size: 16px; margin-top: 0.2cm;">#363/10/01, Malwatte, Kal-Eliya (Mirigama).</h4>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border="0" width="100%">
            <tr>
                <td colspan="3" height="1.8cm"></td>
            </tr>
            <tr>
                <td class="leftboxtop" width="10cm">
                    <table border="0" width="100%" style="margin-top:-70; padding-left:0.3cm;">
                        <tr>
                            <th>Customer Details - '. $customerID .'</th>
                        </tr>
                        <tr>
                            <td>'. $customername . '<br><br>' . $customeraddress . '<br><br>Tel : ' . $customerPhone . '<br><br>Date : ' . $porderDate . '</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left:100px;" width="8cm">
                    <table width="100%" height="100%" style="margin-top:-70;" border="0">
                        <tr>
                            <th align="center" colspan="2">Purchase Order Details - '. $cuspono .'</th>
                        </tr>
                        <tr><td align="left" style="font-weight: bold;">LOCATION </td><td>' . $location . ' </td></tr>
                        <tr><td align="left" style="font-weight: bold;">EMPLOYEE </td><td>' . $rowporderinfo['saleref'] . ' </td></tr>
                        <tr><td align="left" style="font-weight: bold;">CONTACT </td><td>' . $rowporderinfo['phone'] . ' </td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div class="" style="margin-top:15px;">
            <table width="100%" style="padding-left:1cm; padding-right:1cm; padding-top:0.4cm;" id="detailtable">
            <thead>
                <tr>
                    <th style="width:1.5cm;" id="detailth">Code</th>
                    <th style="width:7.5cm;" id="detailth">Product Name</th>
                    <th style="width:1.5cm;" id="detailth" align="right">Quantity</th>
                    <th style="width:2.5cm;" id="detailth" align="right">Sale Price</th>
                    <th style="width:2.6cm;" id="detailth" align="right">Total</th>
                </tr>
            </thead>
            ';
            $rowCount = mysqli_num_rows($resultporderdetail);
            $itemCount = 0;
            $count = 0;
            $count1 = 0;

            while ($rowporderdetail = $resultporderdetail->fetch_assoc()) {
                
                $count = $count + 1;
                $count1++;
                $itemCount++;

                $qtyValue = 0;
                if ($qtyflag == 0) {
                    $qtyValue = $rowporderdetail['orderqty'];
                } else if ($qtyflag == 1) {
                    $qtyValue = $rowporderdetail['confirmqty'];
                } else if ($qtyflag == 2) {
                    $qtyValue = $rowporderdetail['dispatchqty'];
                } else if ($qtyflag == 3) {
                    $qtyValue = $rowporderdetail['qty'];
                }
                $totnew = $qtyValue * $rowporderdetail['saleprice'];
                // $fulltot += $totnew;

                $html .= '
                    <tr>
                        <td style="width:1.5cm;" id="detailtd">' . $rowporderdetail['product_code'] . '</td>
                        <td style="width:9.5cm;" id="detailtd">' . $rowporderdetail['product_name'] . '</td>
                        <td style="width:1.5cm;" id="detailtd" align="right">' . $qtyValue . '</td>
                        <td style="width:2.5cm;" id="detailtd" align="right">' . number_format($rowporderdetail['saleprice'], 2) . '</td>
                        <td style="width:2.6cm;" id="detailtd" align="right">' . number_format((($rowporderdetail['saleprice'] * $qtyValue)-$rowporderdetail['discount']), 2) . '</td>
                    </tr>
                ';
                $temptotal = $qtyValue * $rowporderdetail['saleprice'];
                $newtemp += $temptotal;
                if ($count1 % 28 == 0) {
                    $html .= '
                        <tr>
                            <td colspan="5">This page Total Showing here. See the Next page Thank You</td>
                            <td style="width:2.6cm;" align="right">' . number_format($newtemp, 2) . '</td>
                        </tr>
                    ';
                    $newtemp = 0;
                }
            }
            $discount = $fulltot - $rowporderinfo['total'];
            $html .= '
            </table> 
            ';

            if ($resultporderdetail->num_rows == $count) {
                $html .= '
                    <footer>
                        <div style="">
                            <table border="0" width="100%">
                                <tr>
                                    <td class="leftboxtop" width="10cm">
                                        <table border="0" width="100%" style="padding-left:0.5cm;">
                                            <tr>
                                                <th>REMARKS</th>
                                            </tr>
                                            <tr>
                                                <td>'. $remark . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="8cm">
                                        <table border="0" id="tablefooter" style="margin-right:35px"> 
                                        ';
                                            // $fulldiscount = $rowporderinfo["discount"] + $rowporderinfo["podiscount"];
                                            $fulldiscount = $rowporderinfo["podiscount"];
                                            $net_total = $fulltot - ($fulldiscount + $rowporderinfo["discount"]) ;

                                            $html .= '
                                            <tr>
                                                <td align="right" style="font-weight: bold;">Item Count:</td>
                                                <td align="right">' . $itemCount . '</td>
                                            </tr>
                                            <tr>
                                                <td align="right" style="font-weight: bold;">Net Total:</td>
                                                <td align="right">' . number_format($fulltot - $rowporderinfo["discount"] , 2) . '</td>
                                            </tr>
                                            <tr>
                                                <td align="right" style="font-weight: bold;">Discount:</td>
                                                <td align="right" style="padding-top:0.2cm;">' . number_format($fulldiscount, 2) . '</td>
                                            </tr>
                                            <tr>
                                                <td align="right" style="font-weight: bold;">Total:</td>
                                                <td align="right" style="padding-top:0.2cm;font-weight: bold;">' . number_format($net_total, 2) . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </footer>';
            }
            $html .= '  
        </div>
        
    </main>
    </body>
    </html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('21.5cm', '27.5cm', 'portrait');
$dompdf->render();
$dompdf->stream("Test.pdf", ["Attachment" => 0]);
