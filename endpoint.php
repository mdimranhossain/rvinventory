<?php
/*
 * Endpoint
* @Package: rvinventory
*/

declare(strict_types=1);
$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($viAutoload)) {
    require_once $viAutoload;
}

define( 'SHORTINIT', true );

//require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-admin/admin.php' );
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
//require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/rewrite.php' );

use Inc\Setting;

$setting = new Setting();

if(!empty($_REQUEST['vi_slug'])){
    $slug = trim($_REQUEST['vi_slug']);
    echo $setting->viUpdateSlug($slug);
}