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

$handle = '';
if(!empty($_REQUEST['vehicle'])){
    $handle = $_REQUEST['vehicle'];
}

switch($handle){
    case 'create':
        echo $vehicle->viCreate();
        break;
    case 'update':
       echo $vehicle->viUpdate();
        break;
    case 'delete':
        $vehicle->viDelete();
        break;
    default:
        echo $vehicle->viList();
}