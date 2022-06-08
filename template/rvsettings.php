<?php
/*
* rvsettings
* @Package: rvinventory
*/

require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if (file_exists($rvAutoload)) {
    require_once $rvAutoload;
}

use Inc\Setting;
$setting = new Setting();
$data = $setting->rvInventoryOptions();

function rvurl(string $rvLink){
	return plugins_url($rvLink, dirname(__FILE__));
}

if(!empty($_REQUEST['rv_slug'])){
  $data['slug'] = trim($_REQUEST['rv_slug']);
  $data['seoTitle'] = trim($_REQUEST['rv_seoTitle']);
  $data['seoDescription'] = trim($_REQUEST['rv_seoDescription']);
  $data['pageTitle'] = trim($_REQUEST['rv_pageTitle']);
  $data['emailfriend'] = trim($_REQUEST['rv_emailfriend']);
  $data['availability'] = trim($_REQUEST['rv_availability']);
  $data['contact_dealer'] = trim($_REQUEST['rv_contact_dealer']);
  $data['address'] = trim($_REQUEST['rv_address']);
  $data['phone'] = trim($_REQUEST['rv_phone']);
  $data['weekday'] = trim($_REQUEST['rv_weekday']);
  $data['saturday'] = trim($_REQUEST['rv_saturday']);
  $data['weekend'] = trim($_REQUEST['rv_weekend']);
  $setting->rvUpdateInventoryOptions($data);
  global $wp_rewrite;
  $permalink_structure = get_option( 'permalink_structure' );
  $wp_rewrite->set_permalink_structure( $permalink_structure );
  $using_index_permalinks = $wp_rewrite->using_index_permalinks();
  echo '<script>location.reload();</script>';
}

flush_rewrite_rules();
?>
<div id="settings">
	<h2>Inventory Settings</h2>
	<div class="container">
    <div class="row">
      <div class="col-sm-12">
        <form id="rvsettings" action="" method="POST">
          <input type="hidden" name="user_id" value="">
          <div class="form-group">
          <label class="control-label" for="rv_slug">Inventory Slug/URL</label>
            <div class="row">
              <input type="text" class="form-control col-sm-2" value="<?php echo esc_url(home_url()).'/';?>" disabled /><input type="text" class="form-control col-sm-3" id="rv_slug" name="rv_slug" value="<?php echo !empty($data['slug'])?$data['slug']:'';?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_pageTitle">List Page Title: </label>
            <input type="text" class="form-control col-sm-6" id="rv_pageTitle" name="rv_pageTitle" value="<?php echo !empty($data['pageTitle'])?$data['pageTitle']:'';?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_seoTitle">SEO Title: </label>
            <input type="text" class="form-control col-sm-6" id="rv_seoTitle" name="rv_seoTitle" value="<?php echo !empty($data['seoTitle'])?stripslashes($data['seoTitle']):'';?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_seoDescription">SEO Description: </label>
            <input type="text" class="form-control col-sm-6" id="rv_seoDescription" name="rv_seoDescription" value="<?php echo !empty($data['seoDescription'])?stripslashes($data['seoDescription']):'';?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_emailfriend">Email A Friend: </label>
            <input type="text" class="form-control col-sm-6" id="rv_emailfriend" name="rv_emailfriend" value="<?php echo $data['emailfriend'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_availability">Check Availability: </label>
            <input type="text" class="form-control col-sm-6" id="rv_availability" name="rv_availability" value="<?php echo $data['availability'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_contact_dealer">Contact Dealer: </label>
            <input type="text" class="form-control col-sm-6" id="rv_contact_dealer" name="rv_contact_dealer" value="<?php echo $data['contact_dealer'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_address">Address: </label>
            <input type="text" class="form-control col-sm-6" id="rv_address" name="rv_address" value="<?php echo $data['address'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_city">City: </label>
            <input type="text" class="form-control col-sm-6" id="rv_city" name="rv_city" value="<?php echo $data['city'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_state">State: </label>
            <input type="text" class="form-control col-sm-6" id="rv_state" name="rv_state" value="<?php echo $data['state'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_zip">ZIP: </label>
            <input type="text" class="form-control col-sm-6" id="rv_zip" name="rv_zip" value="<?php echo $data['zip'];?>" />
            </div>

            <div class="row">
            <label class="control-label" for="rv_areas">Areas Served: </label>
            <input type="text" class="form-control col-sm-6" id="rv_areas" name="rv_areas" value="<?php echo $data['areas'];?>" />
            </div>

            <div class="row">
            <label class="control-label" for="rv_phone">Phone: </label>
            <input type="text" class="form-control col-sm-6" id="rv_phone" name="rv_phone" value="<?php echo $data['phone'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_weekday">Monday – Friday: </label>
            <input type="text" class="form-control col-sm-5" id="rv_weekday" name="rv_weekday" value="<?php echo $data['weekday'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_saturday">Saturday: </label>
            <input type="text" class="form-control col-sm-6" id="rv_saturday" name="rv_saturday" value="<?php echo $data['saturday'];?>" />
            </div>
            <div class="row">
            <label class="control-label" for="rv_weekend">Sunday: </label>
            <input type="text" class="form-control col-sm-6" id="rv_weekend" name="rv_weekend" value="<?php echo $data['weekend'];?>" />
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