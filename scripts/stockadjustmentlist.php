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
$table = 'tbl_product';

// Table's primary key
$primaryKey = 'idtbl_product';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => '`ua`.`idtbl_product`', 'dt' => 'idtbl_product', 'field' => 'idtbl_product'),
    array('db' => '`ua`.`product_code`', 'dt' => 'product_code', 'field' => 'product_code'),
    array('db' => '`ua`.`product_name`', 'dt' => 'product_name', 'field' => 'product_name'),
    array('db' => '`u`.`adjustmenttype`', 'dt' => 'adjustmenttype', 'field' => 'adjustmenttype'),
    array('db' => '`u`.`adjustqty`', 'dt' => 'adjustqty', 'field' => 'adjustqty'),
    array('db' => '`u`.`remarks`', 'dt' => 'remarks', 'field' => 'remarks'),

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
require('ssp.customized.class.php');

$joinQuery = "FROM `tbl_stock_adjustment` AS `u` LEFT JOIN `tbl_product` AS `ua` ON (`u`.`tbl_product_idtbl_product` = `ua`.`idtbl_product`)";

// $extraWhere = "WHERE `ua`.`status` ='1'";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery)
);
