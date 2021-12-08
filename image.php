<?php
/*
* image
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

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/default-constants.php' );



use Inc\Image;



$image = new Image($wpdb); 



if(!empty($_POST)){

    echo $image->upload();

}else{

    echo "No file found!";

}