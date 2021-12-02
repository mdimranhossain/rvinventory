<?php
/*
* viaddnew
* @Package: VehicleInventory
*/
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if(file_exists($viAutoload)){
  require_once $viAutoload;
}

// use Inc\Vehicle;

function viurl(string $viLink){
	return plugins_url($viLink, dirname(__FILE__));
}
?>
<div id="vehicle">
	<h2>Add New Vehicle</h2>
  <?php
  global $wpdb;
  $table = $wpdb->prefix.'inventory';

  if (!empty($_POST)){
    $input = $_POST;
    
    $data = [
            'make' => $input['make'],
            'model' => $input['model'],
            'additional' => $input['additional'],
            'slug' => $input['slug'],
            'salePrice' => $input['salePrice'],
            'msrp' => $input['msrp'],
            'description' => $input['description'],
            'vehicleCondition' => $input['vehicleCondition'],
            'payloadCapacity' => $input['payloadCapacity'],
            'emptyWeight' => $input['emptyWeight'],
            'floorLength' => $input['floorLength'],
            'floorWidth' => $input['floorWidth'],
            'sideHeight' => $input['sideHeight'],
            'bodyType' => $input['bodyType'],
            'rvCategory' => $input['rvCategory'],
            'addtionalInfo' => $input['addtionalInfo'],
            'featuredImage' => $input['featuredImage'],
            'featuredid' => $input['featuredid'],
            'gallery' => rtrim($input['gallery'],','),
            'galleryfiles' => rtrim($input['galleryfiles'],','),
            'status' => $input['status'],
            'createdBy' => $input['createdBy'],
            'createdAt' => $input['createdAt']
    ];
        $format = ['%s','%s','%s','%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s'];
        $wpdb->insert($table,$data,$format);
        $inserted = $wpdb->insert_id;

        if($inserted && empty($_GET['id'])){
          $url='admin.php?page=viedit&id='.$inserted;
          echo "<script>location.href = '".$url."'</script>";
        }
  }

  ?>
	<div class="vehicleform">
    <form id="vehicleform" action="" method="POST" enctype="multipart/form-data">
    <div class="container-fluid">
      <div class="row">
    <div class="col-sm-12 addnewtop"><h2>ADD NEW</h2></div>
    <div id="tabs" class="col-sm-3">
      <div class="tab title active"><a href="#">Title</a></div>
      <div class="tab price"><a href="#">Price</a></div>
      <div class="tab description"><a href="#">Description</a></div>
      <div class="tab specs"><a href="#">Specs</a></div>
      <div class="tab additional"><a href="#">Additional Info</a></div>
      <div class="tab gallery"><a href="#">Gallery</a></div>
      <div class="tab publish"><a href="#">Publish</a></div>
      <div class="tab"><br /><br /><br /></div>
    </div>
    <div id="tabcontent" class="col-sm-7">
      <div class="tabcontent titlecontent">
      <input type="hidden" name="id" id="id" value="<?php echo !empty($vehicle->id)?$vehicle->id:'';?>" />
        <div class="form-group">
          <label for="make" class="control-label">Make</label>
          <input name="make" id="make" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->make)?$vehicle->make:'';?>" placeholder="Make" />
          <label for="model" class="control-label">Model</label>
          <input name="model" id="model" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->model)?$vehicle->model:'';?>" placeholder="Model" />
          <label for="additional" class="control-label">Additional</label>
          <input name="additional" id="additional" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->additional)?$vehicle->additional:'';?>" placeholder="additional" />
          <input type="hidden" name="slug" id="slug" />
        </div>
      </div>
      <div class="tabcontent pricecontent">
        <div class="form-group">
          <label for="salePrice" class="control-label">Sale Price</label>
          <input name="salePrice" id="salePrice" class="form-control" value="<?php echo !empty($vehicle->salePrice)?$vehicle->salePrice:'';?>" placeholder="0.00" />
        </div>
        <div class="form-group">
          <label for="msrp" class="control-label">MSRP</label>
          <input name="msrp" id="msrp" class="form-control" value="<?php echo !empty($vehicle->msrp)?$vehicle->msrp:'';?>" placeholder="0.00" />
        </div>
      </div>
      <div class="tabcontent descriptioncontent">
        <div class="form-group">
          <label for="description" class="control-label">Description</label>
          <textarea class="form-control" rows="10" autocomplete="off" cols="40" name="description" id="description"><?php echo !empty($vehicle->description)?$vehicle->description:''; ?></textarea>       
        </div>
      </div>
      <div class="tabcontent specscontent">
        <div class="form-group">
          <label for="vehicleCondition" class="control-label">Vehicle Condition</label>
          <input name="vehicleCondition" id="vehicleCondition" class="form-control" value="<?php echo !empty($vehicle->vehicleCondition)?$vehicle->vehicleCondition:'';?>" placeholder="Vehicle Condition" />
        </div>
        <div class="form-group">
          <label for="payloadCapacity" class="control-label">Payload Capacity</label>
          <input name="payloadCapacity" id="payloadCapacity" class="form-control" value="<?php echo !empty($vehicle->payloadCapacity)?$vehicle->payloadCapacity:'';?>" placeholder="Payload Capacity" />
        </div>
        <div class="form-group">
          <label for="emptyWeight" class="control-label">Empty Weight</label>
          <input type="text" name="emptyWeight" id="emptyWeight" class="form-control" value="<?php echo !empty($vehicle->emptyWeight)?$vehicle->emptyWeight:'';?>" placeholder="Empty Weight" />
        </div>
        <div class="form-group">
          <label for="floorLength" class="control-label">Floor Length</label>
          <input type="text" name="floorLength" id="floorLength" class="form-control" value="<?php echo !empty($vehicle->floorLength)?$vehicle->floorLength:'';?>" placeholder="Floor Length" />
        </div>
        <div class="form-group">
          <label for="floorWidth" class="control-label">Floor Width</label>
          <input type="text" name="floorWidth" id="floorWidth" class="form-control" value="<?php echo !empty($vehicle->floorWidth)?$vehicle->floorWidth:'';?>" placeholder="Floor Width" />
        </div>
        <div class="form-group">
          <label for="sideHeight" class="control-label">Side Height</label>
          <input type="text" name="sideHeight" id="sideHeight" class="form-control" value="<?php echo !empty($vehicle->sideHeight)?$vehicle->sideHeight:'';?>" placeholder="Side Height" />
        </div>
        <div class="form-group">
          <label for="bodyType" class="control-label">Body Type</label>
          <input type="text" name="bodyType" id="bodyType" class="form-control" value="<?php echo !empty($vehicle->bodyType)?$vehicle->bodyType:'';?>" placeholder="Vehilce Body Type" />
        </div>
        <div class="form-group">
          <label for="rvCategory" class="control-label">RV Category</label>
          <select name="rvCategory" id="rvCategory" class="form-control">
            <option value="">Select</option>
            <option value="Class C RVs">Class C RVs</option>
            <option value="Campers">Campers</option>
            <option value="Fifth Wheels">Fifth Wheels</option>
            <option value="Motorhomes">Motorhomes</option>
            <option value="Travel Trailers">Travel Trailers</option>
            <option value="Misc Inventory">Misc. Inventory</option>
          </select>
        </div>
      </div>
      <div class="tabcontent additionalcontent">
        <div class="form-group">
          <label for="addtionalInfo" class="control-label">Addtional Info</label>
          <textarea class="form-control" rows="8" autocomplete="off" cols="40" name="addtionalInfo" id="addtionalInfo"><?php echo !empty($vehicle->addtionalInfo)?$vehicle->addtionalInfo:''; ?></textarea>
    
        </div>
      </div>
      <div class="tabcontent gallerycontent">
        <div class="form-group">
        <?php
        wp_enqueue_media();
        ?>
          <h4>Featured Image</h4>
          <img id="featuredThumb" src="<?php echo !empty($vehicle->featuredImage)?$vehicle->featuredImage:'';?>" width="90" alt="" />
          <button id="featuredUpload" class="btn btn-info btn-md d-none" type="button"><?php echo !empty($vehicle->featuredImage)?'Change Image':'Select Image';?></button>
          <input type="hidden" name="featuredImage" id="featuredImage" value="<?php echo !empty($vehicle->featuredImage)?$vehicle->featuredImage:'';?>" />
          <input type="hidden" name="featuredid" id="featuredid" value="<?php echo !empty($vehicle->featuredid)?$vehicle->featuredid:'';?>" />
          <div class="drop_area" onclick="document.getElementById('featured').click()">
          <input id="featured" class="d-none" name="featured" type="file" />
          Drag & Drop Images Here
          </div>

          <div id="featured_thumb" class="col-sm-12"></div>
          
        </div>
        <br/>
        <div class="form-group">
        <h4>Gallery</h4>
          <button id="gallery_button" class="btn btn-info btn-md d-none" type="button">Add Image</button>
          <textarea name="gallery" id="gallery" style="display:none;"><?php echo !empty($vehicle->gallery)?$vehicle->gallery:'';?></textarea>
          <input type="hidden" name="galleryfiles" id="galleryfiles" value="<?php echo !empty($vehicle->galleryfiles)?$vehicle->galleryfiles:'';?>" />
          <div id="gallery_container">
          <?php 
          if(!empty($vehicle->gallery)){
            $links = rtrim($vehicle->gallery,',');
            $links = explode(',',$links);
            foreach($links as $link){
              echo '<img width="90" src="'.$link.'" alt="" />';
            }
          }
          ?>
          </div>
          
          <div id="drop_area" onclick="document.getElementById('ddgallery').click()">
          <input id="ddgallery" class="d-none" name="file[]" type="file" multiple />
          Drag & Drop Images Here</div>
          <b class="error"></b>
          <div id="uploaded_file"></div>
          
        </div>
      </div>
      <div class="tabcontent publishcontent">
        <div class="form-group">
          <label for="status" class="control-label">Status</label>
          <select name="status" id="status" class="form-control">
            <option value="0" <?php echo (!empty($vehicle->status) && $vehicle->status==0)?'selected':'';?>>Draft</option>
            <option value="1" <?php echo (!empty($vehicle->status) && $vehicle->status==1)?'selected':'';?>>Publish</option>
          </select>
        </div> 
        <div class="form-group">
          <input type="hidden" name="createdBy" id="createdBy" value="<?php echo get_current_user_id();?>" />
          <input type="hidden" name="createdAt" id="createdAt" value="<?php echo date('Y-m-d H:i:s'); ?>" />
          <input type="hidden" name="vehicle" id="vehicle" value="create" />
          <button type="submit" id="vipublish" class="btn btn-primary btn-sm">Publish</button>
        </div>
      </div>
    </div>
    <div class="col-sm-12 addnewbottom"><h2><button type="button" id="visave" class="btn btn-primary btn-sm pull-right">Save</button><br /><br /></h2></div>
    </div>
    </div>
    </form>

	</div>
	
</div>
<script>
function checkslug(){
      var endpoint = "<?php echo viurl("/checkslug.php");?>";
      jQuery.ajax({
            url:endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                jQuery('#slug').val(data.slug);
                 
            }
        });
      
      var endpoint = "<?php echo viurl('/vehicle.php');?>";
      var url = 'admin.php?page=viedit&id=';
      //console.log(url);
      jQuery.ajax({
            url:endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                if(data.insertid){
                  url += data.insertid;
                  location.href = url;
                }

            }
        });

  }
function save(){
  var endpoint = "<?php echo viurl('/vehicle.php');?>";
  jQuery.ajax({
            url:endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);

            }
        });
}

jQuery(document).ready(function($){
    $('#visave').on('click', function(e){
      e.preventDefault();
      var endpoint = "<?php echo viurl('/vehicle.php');?>";
      var url = 'admin.php?page=viedit&id=';
      console.log(url);
      $.ajax({
            url:endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                if(data.insertid){
                  url += data.insertid;
                  location.href = url;
                }

            }
        });
    });

    // $('.gallery').on('click', function(e){
    //   e.preventDefault();
    //   var endpoint = "<?php echo viurl('/vehicle.php');?>";
    //   var url = 'admin.php?page=viedit&id=';
  
    //   $.ajax({
    //         url:endpoint,
    //         method: "POST",
    //         data: new FormData(document.getElementById('vehicleform')),
    //         contentType: false,
    //         cache: false,
    //         processData: false,
    //         dataType: "json",
    //         success: function(data) {
    //             console.log(data);
    //             if(data.insertid){
    //               url += data.insertid;
    //               location.href = url+'#gallerycontent';
    //               // history.pushState({}, null, url);
    //               // $('#vehicle').val('update');
    //             }

    //         }
    //     });
    // });

    // image upload
    $('#featured').on('change', function(e){
      e.preventDefault();
      //var endpoint = "<?php //echo admin_url('admin-ajax.php');?>";
      var endpoint = "<?php echo viurl("/image.php");?>";
 
      var formData = new FormData(document.getElementById('vehicleform'));

      $.ajax({
            url:endpoint,
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                html += '<div class="thumbnail"><span class="btn btn-danger btn-xs pull-right delete" data-image_id="'+data.save.insertid+'"><i class="fa fa-times"></i></span><img class="img-fluid" src="'+data.url+'" alt="" /></div>';
                $('#featured_thumb').html(html);
                $('#featuredImage').val(data.url);
                $('#featuredid').val(data.save.insertid);
                if(data.type.error){
                  $('.error').text(data.type.error);
                }else{
                  $('.error').text('');
                }
                $('.drop_area').hide();
            }
        });
    });

    // drag and drop
    $("html").on("dragover", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });

    $("html").on("drop", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });

    $('#drop_area').on('dragover', function () {
      $(this).addClass('drag_over');
      return false;
    });

    $('#drop_area').on('dragleave', function () {
      $(this).removeClass('drag_over');
      return false;
    });

    // single drag drop
    $('.drop_area').on('drop', function (e) {
      e.preventDefault();
      $(this).removeClass('drag_over');
      var formData = new FormData();
      var files = e.originalEvent.dataTransfer.files;
      formData.append('file[0]', files[0]);
      var endpoint = "<?php echo viurl("/dragdrop.php");?>";
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          console.log(data);
          $('#featured_thumb').append(data.thumbs);
          $('.drop_area').hide();

          $('#featuredImage').val(data.url);
          $('#featuredid').val(data.save.insertid);

        }
      });
    });

    // multiple select
    $('#ddgallery').on('change', function (e) {
      e.preventDefault();
      var formData = new FormData(document.getElementById('vehicleform'));
   
      var endpoint = "<?php echo viurl("/dragdrop.php");?>";
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          //console.log(data);
          $('#uploaded_file').append(data.thumbs);
          $('#drop_area').hide();
          $('#gallery').text(data.gallery);
          $('#galleryfiles').val(data.galleryfiles);
        }
      });
    });
    
    // multiple drag drop
    $('#drop_area').on('drop', function (e) {
      e.preventDefault();
      $(this).removeClass('drag_over');
      var formData = new FormData();
      var files = e.originalEvent.dataTransfer.files;
      for (var i = 0; i < files.length; i++) {
        formData.append('file[]', files[i]);
      }
      var endpoint = "<?php echo viurl("/dragdrop.php");?>";
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          //console.log(data);
          $('#uploaded_file').append(data.thumbs);
          $('#drop_area').hide();
          $('#gallery').text(data.gallery);
          $('#galleryfiles').val(data.galleryfiles);
        }
      });
    });


    // delete image
    $(document).on('click', '.delete', function(e){
      e.preventDefault();
      //var endpoint = "<?php //echo admin_url('admin-ajax.php');?>";
      var endpoint = "<?php echo viurl("/image_delete.php");?>";
      var image = $(this).data('image_id');
      var thumb = $(this).parent();
      $.ajax({
            url:endpoint+'?image_id='+image,
            method: "GET",
            //data: new FormData(document.getElementById('vehicleform')),
            //data:'{"image_id":"'+image+'"}',
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                thumb.html(html);
                thumb.hide();
            }
        });
    });


    $(document).on('click', '.title', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.titlecontent').show();
    });
    $(document).on('click', '.price', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.pricecontent').show();
    });
    $(document).on('click', '.description', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.descriptioncontent').show();
    });
    $(document).on('click', '.specs', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.specscontent').show();
    });
    $(document).on('click', '.additional', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.additionalcontent').show();
    });
    $(document).on('click', '.gallery', function(e){
      e.preventDefault();

      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.gallerycontent').show();
    });
    $(document).on('click', '.publish', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.publishcontent').show();
    });

    //Set Featured Image

    var viFeatured;

    $('#featuredUpload').click(function(e) {
      e.preventDefault();
      // If the upload object has already been created, reopen the dialog
        if (viFeatured) {
        viFeatured.open();
        return;
      }
      // Extend the wp.media object
      viFeatured = wp.media.frames.file_frame = wp.media({
        title: 'Select Image',
        button: {
        text: 'Select Image'
      }, multiple: false });

      // When a file is selected, grab the URL and set it as the text field's value
      viFeatured.on('select', function() {
        var attachment = viFeatured.state().get('selection').first().toJSON();
        $('#featuredImage').val(attachment.url);
        $('#featuredid').val(attachment.id);
        $('#featuredThumb').attr('src',attachment.url);
        $('#featuredUpload').text('Change Image');
      });
      // Open the upload dialog
      viFeatured.open();
    });


    //Set Gallery

    var viGallery;

    $('#gallery_button').click(function(e) {
      e.preventDefault();
     
        if (viGallery) {
            viGallery.open();
          return;
        }
 
      viGallery = wp.media.frames.file_frame = wp.media({
        title: 'Select Image',
        button: {
        text: 'Select Image'
      }, multiple: true });

      viGallery.on('select', function() {
        var attachment = viGallery.state().get('selection').toJSON();
        var gallery = '';
        var thumbs = '';
        var galleryfiles = '';
        
        $.each( attachment, function( key, value ) {
          thumbs +='<img width="90" src="'+value.url+'" alt="" />';
          //gallery +='<input type="hidden" name="gallery[]" value="'+value.url+'" id="img'+key+'" />';
          gallery += value.url+',';
          galleryfiles += value.id+',';
        });

        $('#gallery').text(gallery);
        $('#galleryfiles').val(galleryfiles);
        $('#gallery_container').html(thumbs);
      });

      viGallery.open();
    });

  });
</script>
<div style="display: block; clear: both;"></div>
