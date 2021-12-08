<?php

/*

* visettings

* @Package: rvinventory

*/

require_once( ABSPATH . 'wp-load.php' );

require_once( ABSPATH . 'wp-admin/admin.php' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );



 $viAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if (file_exists($viAutoload)) {

    require_once $viAutoload;

}

use Inc\VehicleData;

$viData=new VehicleData();

$viVehicles=$viData->viVehicleList();

$viVehicles=json_decode($viVehicles);



function viurl(string $viLink){

	return plugins_url($viLink, dirname(__FILE__));

}

?>

<div id="settings">

	<h2>Vehicle Settings</h2>

	<div id="visettings">

  Options for Vehicle Inventory

	</div>

  <div id="uploads">



  <?php

    wp_enqueue_media();

    //global $post;

    ?>



<form method="post">

  <input id="vi-media-url" type="text" name="media" />

  <input id="vi-button" type="button" class="button" value="Upload Image" />

  <input type="submit" value="Submit" />

</form>

  

  </div>

</div>

<script>

jQuery(document).ready(function($){

  // Define a variable viMedia

  var viMedia;



  $('#vi-button').click(function(e) {

    e.preventDefault();

    // If the upload object has already been created, reopen the dialog

      if (viMedia) {

      viMedia.open();

      return;

    }

    // Extend the wp.media object

    viMedia = wp.media.frames.file_frame = wp.media({

      title: 'Select media',

      button: {

      text: 'Select media'

    }, multiple: false });



    // When a file is selected, grab the URL and set it as the text field's value

    viMedia.on('select', function() {

      var attachment = viMedia.state().get('selection').first().toJSON();

      $('#vi-media-url').val(attachment.url);

    });

    // Open the upload dialog

    viMedia.open();

  });

});



</script>

<div style="display: block; clear: both;"></div>