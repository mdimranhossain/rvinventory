<?php
/*
* checkslug
* @Package: rvinventory
*/

declare(strict_types=1);

$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($viAutoload)) {
    require_once $viAutoload;
}

define( 'SHORTINIT', true );

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
//require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-admin/admin.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/post.php' );

use Inc\Vehicle;

$vehicle = new Vehicle($wpdb);

if(!empty($_REQUEST['make'])){
echo $vehicle->viSlug();
}else{
    $data['error'] = 'No Make Found!';
    $data['form'] = $_REQUEST;
echo json_encode($data);
}