<?php
/*
* vehicle
* @Package: rvinventory
*/

declare(strict_types=1);

$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($viAutoload)) {
    require_once $viAutoload;
}
define( 'SHORTINIT', true );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/post.php' );

use Inc\Vehicle;

$vehicle = new Vehicle($wpdb);

$id = 1;
if(!empty($_REQUEST['vehicle'])){
    $id = $_REQUEST['vehicle'];
}

echo json_encode($vehicle->viDetails($id));