<?php

/*

* visettings

* @Package: rvinventory

*/

//require_once( ABSPATH . 'wp-load.php' );

require_once( ABSPATH . 'wp-admin/admin.php' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );



 $viAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if (file_exists($viAutoload)) {

    require_once $viAutoload;

}

use Inc\Setting;

$setting = new Setting();



$data = $setting->viInventoryOptions();



function viurl(string $viLink){

	return plugins_url($viLink, dirname(__FILE__));

}

if(!empty($_REQUEST['vi_slug'])){

  $data['slug'] = trim($_REQUEST['vi_slug']);

  $data['pageTitle'] = trim($_REQUEST['vi_pageTitle']);

  $data['emailfriend'] = trim($_REQUEST['vi_emailfriend']);

  $data['availability'] = trim($_REQUEST['vi_availability']);

  $data['address'] = trim($_REQUEST['vi_address']);

  $data['phone'] = trim($_REQUEST['vi_phone']);

  $data['weekday'] = trim($_REQUEST['vi_weekday']);

  $data['saturday'] = trim($_REQUEST['vi_saturday']);

  $data['weekend'] = trim($_REQUEST['vi_weekend']);

  $setting->viUpdateInventoryOptions($data);

  global $wp_rewrite;

  $permalink_structure = get_option( 'permalink_structure' );

  $wp_rewrite->set_permalink_structure( $permalink_structure );



  $using_index_permalinks = $wp_rewrite->using_index_permalinks();

  echo '<script>location.reload();</script>';

}



flush_rewrite_rules();



?>

<div id="settings">

	<h2>Vehicle Inventory Settings</h2>

	<div class="container">

    <div class="row">

      <div class="col-sm-12">

        <form id="visettings" action="" method="POST">

          <input type="hidden" name="user_id" value="">

          <div class="form-group">

          <label class="control-label" for="vi_slug">Inventory Slug/URL</label>

            <div class="row">

              <input type="text" class="form-control col-sm-2" value="<?php echo esc_url(home_url()).'/';?>" disabled /><input type="text" class="form-control col-sm-3" id="vi_slug" name="vi_slug" value="<?php echo !empty($data['slug'])?$data['slug']:'';?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_pageTitle">List Page Title: </label>

            <input type="text" class="form-control col-sm-6" id="vi_pageTitle" name="vi_pageTitle" value="<?php echo !empty($data['pageTitle'])?$data['pageTitle']:'';?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_emailfriend">Email A Friend: </label>

            <input type="text" class="form-control col-sm-6" id="vi_emailfriend" name="vi_emailfriend" value="<?php echo $data['emailfriend'];?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_availability">Check Availability: </label>

            <input type="text" class="form-control col-sm-6" id="vi_availability" name="vi_availability" value="<?php echo $data['availability'];?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_address">Address: </label>

            <input type="text" class="form-control col-sm-6" id="vi_address" name="vi_address" value="<?php echo $data['address'];?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_phone">Phone: </label>

            <input type="text" class="form-control col-sm-6" id="vi_phone" name="vi_phone" value="<?php echo $data['phone'];?>" />

            </div>



            <div class="row">

            <label class="control-label" for="vi_weekday">Monday – Friday: </label>

            <input type="text" class="form-control col-sm-5" id="vi_weekday" name="vi_weekday" value="<?php echo $data['weekday'];?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_saturday">Saturday: </label>

            <input type="text" class="form-control col-sm-6" id="vi_saturday" name="vi_saturday" value="<?php echo $data['saturday'];?>" />

            </div>

            <div class="row">

            <label class="control-label" for="vi_weekend">Sunday: </label>

            <input type="text" class="form-control col-sm-6" id="vi_weekend" name="vi_weekend" value="<?php echo $data['weekend'];?>" />

            </div>

            <div class="row">

            <button type="submit" id="saveslug" class="btn btn-primary btn-md">Save</button>

            </div>

          </div>

        </form>

      </div>

    </div>

	</div>

</div>

<div style="display: block; clear: both;"></div>