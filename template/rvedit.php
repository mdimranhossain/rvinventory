<?php
/*
* viedit
* @Package: rvinventory
*/
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if(file_exists($rvAutoload)){
  require_once $rvAutoload;
}
// use Inc\Vehicle;
function rvurl(string $rvLink){
	return plugins_url($rvLink, dirname(__FILE__));
}
?>
<div id="vehicle">
	<h2>Edit Vehicle</h2>
  <?php
  global $wpdb;
  $table = $wpdb->prefix.'inventory';
  if(empty($_GET['id'])){
    $url='admin.php?page=viaddnew';
    echo "<script>location.href = '".$url."'</script>";
  }
  if(!empty($_GET['id']) && empty($_POST)){
    $id = $_GET['id'];
    $vehicle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
  }
  if(!empty($_GET['id']) && !empty($_POST)){
    $input = $_POST;
    $id = $input['id'];
    $data = [
          'year' => $input['year'],
          'make' => $input['make'],
          'model' => $input['model'],
          'slug' => $input['slug'],
          'mileage' => $input['mileage'],
          'salePrice' => $input['salePrice'],
          'description' => $input['description'],
          'rvCategory' => $input['rvCategory'],
          'featuredImage' => $input['featuredImage'],
          'featuredid' => $input['featuredid'],
          'gallery' => rtrim($input['gallery'],','),
          'galleryfiles' => rtrim($input['galleryfiles'],','),
          'status' => $input['status'],
          'createdBy' => $input['createdBy'],
          'createdAt' => $input['createdAt']
    ];
    $where = [ 'id' => $id ];
    $wpdb->update($table, $data, $where);
    if($id){
      $url='admin.php?page=viedit&id='.$id;
      echo "<script>location.href = '".$url."'</script>";
    }
  } 
  ?>
	<div class="vehicleform">
    <form id="vehicleform" action="" method="POST" enctype="multipart/form-data">
    <div class="container-fluid">
      <div class="row">
    <div class="col-sm-12 addnewtop"><div class="col-sm-6"><h2>Update</h2></div></div>
    <div id="tabs" class="col-sm-3">
        <div class="tab detail active"><a href="#">Details</a></div>
        <div class="tab gallery"><a href="#">Gallery</a></div>
        <div class="tab publish"><a href="#">Publish</a></div>
        <div class="tab"><br /><br /><br /></div>
    </div>
    <div id="tabcontent" class="col-sm-7">
      <div class="tabcontent detailcontent">
      <input type="hidden" name="id" id="id" value="<?php echo !empty($vehicle->id)?$vehicle->id:'';?>" />
        <div class="form-group">
        <label for="year" class="control-label">Year</label>
          <input type="text" name="year" id="year" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->year)?$vehicle->year:'';?>" placeholder="Year" />
          <label for="make" class="control-label">Make</label>
          <input type="text" name="make" id="make" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->make)?$vehicle->make:'';?>" placeholder="Make" />
          <label for="model" class="control-label">Model</label>
          <input type="text" name="model" id="model" class="form-control" onBlur="checkslug();" value="<?php echo !empty($vehicle->model)?$vehicle->model:'';?>" placeholder="Model" />
          
          <input type="hidden" name="slug" id="slug" value="<?php echo !empty($vehicle->slug)?$vehicle->slug:$vehicle->id;?>" />
          <label for="rvCategory" class="control-label">RV Category</label>
          <select name="rvCategory" id="rvCategory" class="form-control">
          <?php echo !empty($vehicle->rvCategory)?'<option selected value="'.$vehicle->rvCategory.'">'.$vehicle->rvCategory.'</option>':'<option value="">Select</option>';?>
            <option value="Class A, B & C RVs">Class A, B & C RV's</option>
            <option value="Campers">Campers</option>
            <option value="Fifth Wheels">Fifth Wheels</option>
            <option value="Toy Hauler">Toy Hauler</option>
            <option value="Travel Trailers">Travel Trailers</option>
            <option value="Miscellaneous">Miscellaneous</option>
          </select>
        </div>

        <div class="form-group">
            <label for="mileage" class="control-label">Mileage</label>
            <input type="text" name="mileage" id="mileage" class="form-control"
                value="<?php echo !empty($vehicle->mileage)?$vehicle->mileage:'';?>"
                placeholder="1000" />
        </div>

        <div class="form-group">
          <label for="salePrice" class="control-label">Sale Price</label>
          <input type="text" name="salePrice" id="salePrice" class="form-control" value="<?php echo (!empty($vehicle->salePrice) && $vehicle->salePrice > 0)?$vehicle->salePrice:'';?>" placeholder="0" />
        </div>

        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea class="form-control" rows="5" autocomplete="off" cols="40" name="description"
                id="description"><?php echo !empty($vehicle->description)?$vehicle->description:''; ?></textarea>
        </div>

      </div>
  
      <div id="gallerycontent" class="tabcontent gallerycontent">
        <span class="message"></span>
        <div class="form-group">
        <?php
        //wp_enqueue_media();
        ?>
          <h4>Featured Image</h4>
          <button id="featuredUpload" class="btn btn-info btn-md d-none" type="button"><?php echo !empty($vehicle->featuredImage)?'Change Image':'Select Image';?></button>
          <input type="hidden" name="featuredImage" id="featuredImage" value="<?php echo !empty($vehicle->featuredImage)?$vehicle->featuredImage:'';?>" />
          <input type="hidden" name="featuredid" id="featuredid" value="<?php echo !empty($vehicle->featuredid)?$vehicle->featuredid:'';?>" />
          <div id="featured_thumb" class="col-sm-12">
          <?php if(!empty($vehicle->featuredImage) && !empty($vehicle->featuredid)){ ?>
            <div class="thumbnail"><span class="btn btn-danger btn-xs pull-right fupdate" data-image_id="<?php echo $vehicle->featuredid;?>"><i class="fa fa-times"></i></span><img class="img-fluid" src="<?php echo $vehicle->featuredImage;?>" alt="" /></div>
          <?php } ?>
          </div>
          <div class="drop_area<?php if(!empty($vehicle->featuredImage) && !empty($vehicle->featuredid)){ echo ' d-none';} ?>" onclick="document.getElementById('featured').click()">
          <input id="featured" class="d-none" name="featured" type="file" />
          Drag & Drop Images Here
          </div>
        </div>
        <div class="form-group">
        <h4>Gallery</h4>
          <button id="gallery_button" class="btn btn-info btn-md d-none" type="button">Add Image</button>
          <textarea name="gallery" id="gallery" class="d-none"><?php echo !empty($vehicle->gallery)?$vehicle->gallery:'';?></textarea>
          <input type="hidden" name="galleryfiles" id="galleryfiles" value="<?php echo !empty($vehicle->galleryfiles)?$vehicle->galleryfiles:'';?>" />
          <div id="uploaded_file">
            <?php 
              if(!empty($vehicle->gallery) && !empty($vehicle->galleryfiles)){
                $links = rtrim($vehicle->gallery,',');
                $links = ltrim($links,',');
                //echo $links;
                $links = explode(',',$links); 
                //print_r($links);
                $fileids = rtrim($vehicle->galleryfiles,',');
                $fileids = ltrim($fileids,',');
                //echo $fileids;
                $fileids = explode(',',$fileids); 
                //print_r($fileids);
                global $wpdb,$table_prefix;
                foreach($fileids as $id){
                  if($id){
                  $link = $wpdb->get_var('SELECT url FROM '.$table_prefix.'inventory_images WHERE id = '.$id);
                  echo '<div class="thumbnail" data-id="'.$id.'"><span class="btn btn-danger btn-xs pull-right update" data-image_id="'.$id.'"><i class="fa fa-times"></i></span><img class="img-fluid" src="'.$link.'" alt="" /></div>';
                  }
                }
          
              }
            ?>
          </div>
          <div id="drop_area" class="<?php //if(!empty($vehicle->gallery)){echo 'd-none';} ?>" onclick="document.getElementById('ddgallery').click()">
            <input id="ddgallery" class="d-none" name="file[]" type="file" multiple />
            Drag & Drop Images Here</div>
            <b class="error"></b>
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
          <input type="hidden" name="createdAt" id="createdAt" value="<?php echo date('Y-m-d H:i:s');?>" />
          <input type="hidden" name="vehicle" id="vehicle" value="update" />
          <input type="hidden" name="action" id="action" value="" />
          <?php wp_nonce_field( 'update-vehicle_'.time());?>
          <button type="button" id="rvpublish" class="btn btn-primary btn-sm">Update</button>
        </div>
      </div>
    </div>
    </div>
    <div class="row addnewbottom"><div class="col-sm-6"><div id="message"></div></div><div class="col-sm-6"><h2><button type="button" id="rvsave" class="btn btn-primary btn-sm pull-right">Save</button><br /><br /></h2></div></div>
    </div>
    </form>
	</div>
</div>
<script>
function checkslug(){
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      jQuery('#action').val('rvSlug');
      jQuery.ajax({
            url:endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                console.log(result);
                jQuery('#slug').val(result.slug);
            }
        });
  }

  jQuery(document).ready(function($){
    $('#rvsave').on('click', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      $('#action').val('rvVehicle');
      var message = "";
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
                var html = '';
                message = '<div class="alert success">'+data.message+'</div>';
                $('#message').html(message);
            }
        });
    });
    $('#rvpublish').on('click', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      $('#action').val('rvVehicle');
      var message = "";
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
                var html = '<span>Saved <a href="">View</a></span>';
                message = '<div class="alert success">'+data.message+'</div>';
                $('#message').html(message);
            }
        });
    });
    // image upload
    $('#featured').on('change', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      var formdata = new FormData(document.getElementById('vehicleform'));
      formdata.append('action', 'rvUpload');
      $.ajax({
            url:endpoint,
            method: "POST",
            data: formdata,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                html += '<div class="thumbnail"><span class="btn btn-danger btn-xs pull-right fupdate" data-image_id="'+data.save.insertid+'"><i class="fa fa-times"></i></span><img class="img-fluid" src="'+data.url+'" alt="" /></div>';
                $('#featured_thumb').html(html);
                $('#featuredImage').val(data.url);
                $('#featuredid').val(data.save.insertid);
                if(data.type.error){
                  $('.error').text(data.type.error);
                }else{
                  $('.error').text('');
                }
                $('.drop_area').addClass('d-none');
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
      var id = $('#id').val();
      var formdata = new FormData(document.getElementById('vehicleform'));
      var files = e.originalEvent.dataTransfer.files;
      formdata.append('file[0]', files[0]);
      formdata.append('id', id);
      formdata.append('action', 'rvSingleDragDrop');
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      console.log(files);
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formdata,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          console.log(data);
          $('#featured_thumb').append(data.thumbs);
          $('.drop_area').addClass('d-none');
          $('#featuredImage').val(data.url);
          $('#featuredid').val(data.save.insertid);
        }
      });
    });
    // multiple select
    $('#ddgallery').on('change', function (e) {
      e.preventDefault();

      var formdata = new FormData(document.getElementById('vehicleform'));
      $(this).removeClass('drag_over');
      var id = $('#id').val();
   
      formdata.append('id', id);
      formdata.append('action', 'rvDragDrop');
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formdata,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          console.log(data);
          $('#uploaded_file').append(data.thumbs);
          //$('#drop_area').hide();
          $('#gallery').text(data.gallery);
          $('#galleryfiles').val(data.galleryfiles);
        }
      });
    });
    // multiple drag drop
    $('#drop_area').on('drop', function (e) {
      e.preventDefault();
      $(this).removeClass('drag_over');
      var id = $('#id').val();
      var formdata = new FormData(document.getElementById('vehicleform'));
      var files = e.originalEvent.dataTransfer.files;
      for (var i = 0; i < files.length; i++) {
        formdata.append('file[]', files[i]);
      }
      formdata.append('id', id);
      formdata.append('action', 'rvDragDrop');
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      $.ajax({
        url: endpoint,
        method: "POST",
        data: formdata,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function (data) {
          console.log(data);
          $('#uploaded_file').append(data.thumbs);
          //$('#drop_area').hide();
          $('#gallery').text(data.gallery);
          $('#galleryfiles').val(data.galleryfiles);

          if(data.message){
            var message = '<div class="alert success">'+data.message+'</div>';
            $('#message').html(message);
          }
          
        }
      });
    });
    // delete image
    $(document).on('click', '.delete', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        var formdata = new FormData(document.getElementById('vehicleform'));
      var image = $(this).data('image_id');
      formdata.append('image_id', image);
      var thumb = $(this).parent();
      formdata.append('action', 'rvDeleteImage');
      
      $.ajax({
            url:endpoint,
            method: "POST",
            data: formdata,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                thumb.html(html);
                thumb.hide();
                if(data.message){
                    var message = '<div class="alert success">'+data.message+'</div>';
                    $('#message').html(message);
                }
            }
        });
    });
    // update image
    $(document).on('click', '.fupdate', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
      var formdata = new FormData(document.getElementById('vehicleform'));
      var image = $(this).data('image_id');
      var id = $('#id').val();
      formdata.append('image_id', image);
      formdata.append('action', 'rvUpdateImage');
      var thumb = $(this).parent();

      $.ajax({
            url:endpoint,
            method: "POST",
            data: formdata,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                thumb.html(html);
                thumb.hide();
                $('#featuredImage').val('');
                $('#featuredid').val('');
                $('.drop_area').removeClass('d-none');
                if(data.message.update){
                    var message = '<div class="alert success">'+data.message.update+'</div>';
                    $('#message').html(message);
                }
            }
        });
    });
    // update image
    $(document).on('click', '.update', function(e){
      e.preventDefault();
      var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
    var formdata = new FormData(document.getElementById('vehicleform'));
      var image = $(this).data('image_id');
      var id = $('#id').val();
      formdata.append('image_id', image);
      var thumb = $(this).parent();
      formdata.append('action', 'rvUpdateGallery');
      
      $.ajax({
            url:endpoint,
            method: "POST",
            data: formdata,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                thumb.html(html);
                thumb.hide();
                $('#gallery').text(data.gallery);
                $('#galleryfiles').val(data.galleryfiles);
                if(data.message.update){
                    var message = '<div class="alert success">'+data.message.update+'</div>';
                    $('#message').html(message);
                }
            }
        });
    });
    $(document).on('click', '.detail', function(e){
      e.preventDefault();
      $('#tabs').find('.tab').removeClass('active');
      $(this).addClass('active');
      $('#tabcontent').find('.tabcontent').hide();
      $('.detailcontent').show();
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
    // sortable
    $( "#uploaded_file" ).sortable({
      update: function(){
        var list = new Array();
        $(this).find('.thumbnail').each(function(){
          var id=$(this).attr('data-id');	
          list.push(id);
        });
        //console.log(list);
        $('#galleryfiles').val(list.join());
      }
    });
  });
</script>
<div style="display: block; clear: both;"></div>
