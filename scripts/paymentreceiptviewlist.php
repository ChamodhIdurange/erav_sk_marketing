<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_invoice_payment';

// Table's primary key
$primaryKey = 'idtbl_invoice_payment';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`u`.`idtbl_invoice_payment`', 'dt' => 'idtbl_invoice_payment', 'field' => 'idtbl_invoice_payment' ),
    array( 'db' => '`i`.`nettotal`', 'dt' => 'nettotal', 'field' => 'nettotal' ),
    array( 'db' => '`i`.`invoiceno`', 'dt' => 'invoiceno', 'field' => 'invoiceno' ),
    array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
    array(
        'db' => 'SUM(`p`.`payamount`)',
        'dt' => 'payamount',
        'field' => 'payamount',
        'as' => 'payamount'
      ),
    array( 'db' => '`u`.`balance`', 'dt' => 'balance', 'field' => 'balance' ),
    array( 'db' => '`c`.`name`', 'dt' => 'cusname', 'field' => 'cusname', 'as' => 'cusname' ),
    array( 'db' => '`e`.`name`', 'dt' => 'repname', 'field' => 'repname', 'as' => 'repname' ),
    array( 'db' => '`c`.`address`', 'dt' => 'address', 'field' => 'address' ),
    array( 'db' => '`i`.`paymentcomplete`', 'dt' => 'paymentcomplete', 'field' => 'paymentcomplete' ),
    array( 'db' => '`i`.`idtbl_invoice`', 'dt' => 'idtbl_invoice', 'field' => 'idtbl_invoice' ),
    array( 'db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status' ),
);



// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );


$joinQuery = "FROM `tbl_invoice_payment_has_tbl_invoice` AS `p` LEFT JOIN  `tbl_invoice_payment` AS `u` ON (`p`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`) left JOIN `tbl_invoice` AS `i` ON (`i`.`idtbl_invoice` = `p`.`tbl_invoice_idtbl_invoice`) LEFT JOIN `tbl_customer_order` AS `o` ON (`o`.`idtbl_customer_order` = `i`.`tbl_customer_order_idtbl_customer_order`) LEFT JOIN `tbl_employee` AS `e` ON (`e`.`idtbl_employee`=`o`.`tbl_employee_idtbl_employee`) LEFT JOIN `tbl_customer` as `c` ON (`i`.`tbl_customer_idtbl_customer` = `c`.`idtbl_customer`)";

// $joinQuery = "FROM `tbl_invoice_payment` AS `u`  LEFT JOIN  `tbl_invoice_payment_has_tbl_invoice` AS `p` ON (`p`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`) left JOIN `tbl_invoice` AS `i` ON (`i`.`idtbl_invoice` = `p`.`tbl_invoice_idtbl_invoice`) LEFT JOIN `tbl_customer_order` AS `o` ON (`o`.`idtbl_customer_order` = `i`.`tbl_customer_order_idtbl_customer_order`) LEFT JOIN `tbl_employee` AS `e` ON (`e`.`idtbl_employee`=`o`.`tbl_employee_idtbl_employee`) LEFT JOIN `tbl_customer` as `c` ON (`i`.`tbl_customer_idtbl_customer` = `c`.`idtbl_customer`)";

$extraWhere = "`u`.`status` IN (1, 2) GROUP BY `p`.`tbl_invoice_idtbl_invoice`";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);