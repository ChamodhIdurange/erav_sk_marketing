<?php
session_start();
require_once('../connection/db.php');
require_once '../vendor/autoload.php'; // Adjust the path as necessary

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);

$today=date('Y-m-d');
$recordID=1;

$empty = 'null';
$fulltot = 0;
$discount = 0;
$totaloutstanding = 0;
$fulloutstanding = 0;
$totalpayment = 0;

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address`, `tbl_employee`.`name` AS `saleref`, `tbl_area`.`area`, `tbl_user`.`name` as `username`, `tbl_invoice`.`tbl_customer_idtbl_customer`  FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`tbl_customer_idtbl_customer` LEFT JOIN `tbl_employee` ON `tbl_employee`.`idtbl_employee`=`tbl_invoice`.`ref_id` LEFT JOIN `tbl_area` ON `tbl_area`.`idtbl_area`=`tbl_invoice`.`tbl_area_idtbl_area` LEFT JOIN `tbl_user` ON `tbl_user`.`idtbl_user`=`tbl_invoice`.`tbl_user_idtbl_user`WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo); 
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$customerID = $rowinvoiceinfo['tbl_customer_idtbl_customer'];
$customername = $rowinvoiceinfo['name'];
$customeraddress = $rowinvoiceinfo['address'];
$paymentcomplete = $rowinvoiceinfo['paymentcomplete'];

// $sqlinvoiceoutstanding="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address`, `tbl_employee`.`name` AS `saleref`, `tbl_area`.`area`, `tbl_user`.`name` as `username`  FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`tbl_customer_idtbl_customer` LEFT JOIN `tbl_employee` ON `tbl_employee`.`idtbl_employee`=`tbl_invoice`.`ref_id` LEFT JOIN `tbl_area` ON `tbl_area`.`idtbl_area`=`tbl_invoice`.`tbl_area_idtbl_area` LEFT JOIN `tbl_user` ON `tbl_user`.`idtbl_user`=`tbl_invoice`.`tbl_user_idtbl_user`WHERE `tbl_invoice`.`status`=1 AND  `tbl_invoice`.`payment_created`=0 AND `tbl_invoice`.`tbl_customer_idtbl_customer`='$customerID'";
// $resultinvoiceoutstanding =$conn-> query($sqlinvoiceoutstanding); 

$sqlinvoicedetail="SELECT `tbl_product`.`product_name`, `tbl_product`.`idtbl_product`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`freeqty`, `tbl_invoice_detail`.`saleprice` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

// $sqlinvoiceoutstanding="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`total`, `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`freeqty`, `tbl_invoice_detail`.`saleprice` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` LEFT JOIN `tbl_invoice` ON `tbl_invoice`.`idtbl_invoice`=`tbl_invoice_detail`.`tbl_invoice_idtbl_invoice` WHERE `tbl_invoice_detail`.`status`=1 AND `tbl_invoice`.`tbl_customer_idtbl_customer`='$customerID' AND  `tbl_invoice`.`idtbl_invoice` != '$recordID' AND `tbl_invoice`.`status` = '1'  GROUP BY `tbl_invoice`.`idtbl_invoice`";
// $sqlinvoiceoutstanding="SELECT  `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`paymentcomplete`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, SUM(`tbl_invoice_payment_has_tbl_invoice`.`payamount`) AS `payamount` FROM `tbl_invoice` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`=`tbl_invoice`.`idtbl_invoice`  WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`paymentcomplete`=0 AND `tbl_invoice`.`payment_created` IN (0,1)  AND `tbl_invoice`.`paymentcomplete`=0 AND `tbl_invoice`.`idtbl_invoice`!='$recordID' Group BY `tbl_invoice`.`idtbl_invoice`";
$sqlinvoiceoutstanding="SELECT `tbl_employee`.`name` as `asm`, `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`paymentcomplete`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, SUM(`tbl_invoice_payment_has_tbl_invoice`.`payamount`) AS `payamount` FROM `tbl_invoice` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`=`tbl_invoice`.`idtbl_invoice` LEFT JOIN `tbl_employee` ON `tbl_employee`.`idtbl_employee` = `tbl_invoice`.`ref_id` WHERE `tbl_invoice`.`tbl_customer_idtbl_customer`='$customerID' AND `tbl_invoice`.`status`=1 AND `tbl_invoice`.`paymentcomplete`=0 AND `tbl_invoice`.`payment_created` IN (0,1) AND `tbl_invoice`.`idtbl_invoice` != '$recordID' Group BY `tbl_invoice`.`idtbl_invoice`";
$resultinvoiceoutstanding=$conn->query($sqlinvoiceoutstanding);

$sqlinvoicedetailfree="SELECT `tbl_product`.`product_name`, `tbl_invoice_detail`.`freeqty` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`freeproductid` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1 AND `tbl_invoice_detail`.`freeqty`>0";
$resultinvoicedetailfree=$conn->query($sqlinvoicedetailfree);

$html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SK Marketing Co</title>
        <style>
            *{
                font-size: 11;
                margin:1;
            }
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height:6cm;
            }

            footer {
                position: fixed; 
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                bottom: 5.7cm;
            }
            .leftboxtop{
                width:10.5cm;
                height:4cm;
            }
            .bottomtop{
                width:2.5cm;
            }
            .righttop{
                width:7.5cm;
            }
            .bottomtable{
                height:13.5cm;
            }
            .divclass{
                border-right:1px solid black;
            }
        </style>
    </head>
    <body style="margin-top:6.2cm; margin-bottom:5cm; height:14cm">

    <header>
        <table border="0" width="100%">
            <tr>
                <td colspan="3" height="2cm"></td>
            </tr>
            <tr>
                <td class="leftboxtop" height="4cm" width="10cm">
                    <table border="0" width="100%" style="margin-top:-50;">
                        <tr>
                            <td>'.$customername.'<br>'.$customeraddress.'</td>
                        </tr>
                    </table>
                </td>
                <td width="3cm">&nbsp;</td>
                <td>
                    <table width="100%" height="100%">
                        <tr><td width="30%" height="0.5cm"> </td><td align="right">' .$today .' </td></tr>
                        <tr><td height="0.5cm"></td> <td align="right">' .$rowinvoiceinfo['idtbl_invoice'].'</td></tr>
                        <tr><td height="0.5cm"></td> <td align="right">&nbsp;</td></tr>
                        <tr><td height="0.5cm"></td> <td align="right">&nbsp;</td></tr>
                        <tr><td height="0.5cm"></td> <td align="right">' .$rowinvoiceinfo['saleref'].'</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div class="">
            <table width="100%">';
            $rowCount = mysqli_num_rows($resultinvoicedetail);
            $count = 0;
            while($rowinvoicedetail=$resultinvoicedetail->fetch_assoc()){
                $totnew=$rowinvoicedetail['qty']*$rowinvoicedetail['saleprice'];
                $fulltot = $fulltot + $totnew;
                $count = $count + 1;
                $html .= '
                    <tr>
                        <td style="width:2cm;">' . $rowinvoicedetail['idtbl_product'] . '</td>
                        <td style="width:8cm;">' . $rowinvoicedetail['product_name'] . '</td>
                        <td style="width:1.5cm;" align="center">' . $rowinvoicedetail['qty'] . '</td>
                        <td style="width:2.5cm;" align="right">' . number_format($rowinvoicedetail['saleprice'], 2) . '</td>
                        <td style="width:3cm;" align="right">' .number_format(($rowinvoicedetail['saleprice'] * $rowinvoicedetail['qty']), 2). '</td>
                    </tr>
                ';
            }

            $discount = $fulltot - $rowinvoiceinfo['total'];
            $html .= '
            </table>   
        </div>
    </main>';
    
    if ($resultinvoicedetail->num_rows == $count) {
        $html .= '
        <footer>
            <div style="margin-left: 670px; padding-right: -2px; height: 75px;">
                <table width="100%" height="100%" style="border-collapse: collapse;">
                    <tr>
                        <td align="right">' . number_format($fulltot, 2) . '</td>
                    </tr>
                    <tr>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right">' . number_format($rowinvoiceinfo["total"], 2) . '</td>
                    </tr>
                </table>
            </div>
        </footer>';
    }

    $html .= '
    </body>
    </html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('21.5cm', '27.5cm', 'portrait');
$dompdf->render();
$dompdf->stream("Test.pdf", ["Attachment" => 0]);


// // Create a new PDF instance
// $pdf = new Dompdf\Dompdf();

// // Load HTML content
// $html = '<h1>Hello, DomPDF!</h1>';
// $pdf->loadHtml($html);

// // Set paper size and orientation (optional)
// $pdf->setPaper('A4', 'portrait');

// // Render the PDF
// $pdf->render();

// // Output the generated PDF (inline or download)
// $pdf->stream('document.pdf', ['Attachment' => false]);

?>