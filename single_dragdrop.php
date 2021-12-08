<?php

/*

* single_dragdrop

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

require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/default-constants.php' );



use Inc\Image;



$image = new Image($wpdb); 



if(!empty($_FILES['file'])){

    echo $image->singleDragdrop();

    //echo json_encode($_FILES['file']);

}else{

    echo "No file found!";

}