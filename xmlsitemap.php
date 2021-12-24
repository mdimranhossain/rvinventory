<?php
/*
* xmlsitemap
* @Package: rvinventory
*/
declare(strict_types=1);

define( 'SHORTINIT', true );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/post.php' );

$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($viAutoload)) {
    require_once $viAutoload;
}

use Inc\Vehicle;

$vehicle = new Vehicle($wpdb); 

print_r($vehicle->viSitemap());