<?php

/*

* media_upload

* @Package: rvinventory

*/

require_once( ABSPATH . 'wp-load.php' );

require_once( ABSPATH . 'wp-admin/admin.php' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );



$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if (file_exists($rvAutoload)) {

    require_once $rvAutoload;

}

use Inc\VehicleData;

$rvData=new Vehiclea();

$rvVehicles=$rvData->rvVehicleList();

$rvVehicles=json_decode($rvVehicles);



function rvurl(string $rvLink){

	return plugins_url($rvLink, dirname(__FILE__));

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
      <input id="rv-media-url" type="text" name="media" />
      <input id="rv-button" type="button" class="button" value="Upload Image" />
      <input type="submit" value="Submit" />
    </form>
  </div>
</div>
<script>
jQuery(document).ready(function($){
  // Define a variable rvMedia
  var rvMedia;
  $('#rv-button').click(function(e) {
    e.preventDefault();
    // If the upload object has already been created, reopen the dialog
      if (rvMedia) {
      rvMedia.open();
      return;
    }

    // Extend the wp.media object
    rvMedia = wp.media.frames.file_frame = wp.media({
      title: 'Select media',
      button: {
      text: 'Select media'
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    rvMedia.on('select', function() {
      var attachment = rvMedia.state().get('selection').first().toJSON();
      $('#rv-media-url').val(attachment.url);
    });

    // Open the upload dialog
    rvMedia.open();
  });
});
</script>

<div style="display: block; clear: both;"></div>