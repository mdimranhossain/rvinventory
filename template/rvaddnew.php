<?php
/*
* rvaddnew
* @Package: rvinventory
*/
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if(file_exists($rvAutoload)){
  require_once $rvAutoload;
}

use Inc\Vehicle;

function rvurl(string $rvLink){
	return plugins_url($rvLink, dirname(__FILE__));
}
?>
<div id="vehicle">
    <h2>Add New Vehicle</h2>
<?php

$rv = new Vehicle();

  if (!empty($_POST)){
    $insert = $rv->rvCreate();
    if($insert['insertid'] && empty($_GET['id'])){
        $url='admin.php?page=rvedit&id='.$insert['insertid'];
        echo "<script>location.href = '".$url."'</script>";
    }
  }
  ?>
    <div class="vehicleform">
        <form id="vehicleform" action="" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 addnewtop">
                        <h2>ADD NEW</h2>
                    </div>
                    <div id="tabs" class="col-sm-3">
                        <div class="tab detail active"><a href="#">Details</a></div>
                        <div class="tab gallery"><a href="#">Gallery</a></div>
                        <div class="tab publish"><a href="#">Publish</a></div>
                        <div class="tab"><br /><br /><br /></div>
                    </div>
                    <div id="tabcontent" class="col-sm-7">
                        <div class="tabcontent detailcontent">
                            <input type="hidden" name="id" id="id"
                                value="<?php echo !empty($vehicle->id)?$vehicle->id:'';?>" />
                            <div class="form-group">
                                <label for="year" class="control-label">Year</label>
                                <input type="text" name="year" id="year" class="form-control" onBlur="checkslug();"
                                    value="<?php echo !empty($vehicle->year)?$vehicle->year:'';?>" placeholder="Year" />
                                <label for="make" class="control-label">Make</label>
                                <input type="text" name="make" id="make" class="form-control" onBlur="checkslug();"
                                    value="<?php echo !empty($vehicle->make)?$vehicle->make:'';?>" placeholder="Make" />
                                <label for="model" class="control-label">Model</label>
                                <input type="text" name="model" id="model" class="form-control" onBlur="checkslug();"
                                    value="<?php echo !empty($vehicle->model)?$vehicle->model:'';?>"
                                    placeholder="Model" />
                                <input type="hidden" name="slug" id="slug" />
                                <label for="rvCategory" class="control-label">RV Category</label>
                                <select name="rvCategory" id="rvCategory" class="form-control">
                                    <option value="">Select</option>
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
                                <input type="text" name="mileage" id="mileage" class="form-control" value=""
                                    placeholder="1000" />
                            </div>
                            <div class="form-group">
                                <label for="salePrice" class="control-label">Sale Price</label>
                                <input type="text" name="salePrice" id="salePrice" class="form-control"
                                    value="<?php echo (!empty($vehicle->salePrice) && $vehicle->salePrice>0)?$vehicle->salePrice:'';?>"
                                    placeholder="0" />
                            </div>
                            <div class="form-group">
                                <label for="description" class="control-label">Description</label>
                                <textarea class="form-control" rows="5" autocomplete="off" cols="40" name="description"
                                    id="description"><?php echo !empty($vehicle->description)?$vehicle->description:''; ?></textarea>
                            </div>

                        </div>


                        <div class="tabcontent gallerycontent">
                            <div class="form-group">
                                <?php wp_enqueue_media(); ?>
                                <h4>Featured Image</h4>
                                <img id="featuredThumb"
                                    src="<?php echo !empty($vehicle->featuredImage)?$vehicle->featuredImage:'';?>"
                                    width="90" alt="" />
                                <button id="featuredUpload" class="btn btn-info btn-md d-none"
                                    type="button"><?php echo !empty($vehicle->featuredImage)?'Change Image':'Select Image';?></button>
                                <input type="hidden" name="featuredImage" id="featuredImage"
                                    value="<?php echo !empty($vehicle->featuredImage)?$vehicle->featuredImage:'';?>" />
                                <input type="hidden" name="featuredid" id="featuredid"
                                    value="<?php echo !empty($vehicle->featuredid)?$vehicle->featuredid:'';?>" />
                                <div class="drop_area" onclick="document.getElementById('featured').click()">
                                    <input id="featured" class="d-none" name="featured" type="file" />
                                    Drag & Drop Images Here
                                </div>
                                <div id="featured_thumb" class="col-sm-12"></div>
                            </div>
                            <br />
                            <div class="form-group">
                                <h4>Gallery</h4>
                                <button id="gallery_button" class="btn btn-info btn-md d-none" type="button">Add
                                    Image</button>
                                <textarea name="gallery" id="gallery"
                                    style="display:none;"><?php echo !empty($vehicle->gallery)?$vehicle->gallery:'';?></textarea>
                                <input type="hidden" name="galleryfiles" id="galleryfiles"
                                    value="<?php echo !empty($vehicle->galleryfiles)?$vehicle->galleryfiles:'';?>" />
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
                                    Drag & Drop Images Here
                                </div>
                                <b class="error"></b>
                                <div id="uploaded_file"></div>
                            </div>
                        </div>
                        <div class="tabcontent publishcontent">
                            <div class="form-group">
                                <label for="status" class="control-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Publish</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="createdBy" id="createdBy"
                                    value="<?php echo get_current_user_id();?>" />
                                <input type="hidden" name="createdAt" id="createdAt"
                                    value="<?php echo date('Y-m-d H:i:s'); ?>" />
                                <input type="hidden" name="vehicle" id="vehicle" value="create" />
                                <input type="hidden" name="action" id="action" value="" />
                                <?php wp_nonce_field( 'create-vehicle_'.time());?>
                                <button type="submit" id="rvpublish" class="btn btn-primary btn-sm">Publish</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 addnewbottom">
                        <h2><button type="button" id="rvsave"
                                class="btn btn-primary btn-sm pull-right">Save</button><br /><br /></h2>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function checkslug() {
    var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
    jQuery('#action').val('rvSlug');
    jQuery.ajax({
        url: endpoint,
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

function save() {
    var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
    jQuery('#action').val('rvVehicle');
    var url = 'admin.php?page=rvedit&id=';
    jQuery.ajax({
        url: endpoint,
        method: "POST",
        data: new FormData(document.getElementById('vehicleform')),
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        success: function(data) {
            console.log(data);
            if (data.insertid) {
                url += data.insertid;
                location.href = url;
            }
        }
    });
}

jQuery(document).ready(function($) {
    $('#rvsave').on('click', function(e) {
        e.preventDefault();
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        jQuery('#action').val('rvVehicle');
        var url = 'admin.php?page=rvedit&id=';
        console.log(url);
        $.ajax({
            url: endpoint,
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

    $('.gallery').on('click', function(e) {
        e.preventDefault();
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        jQuery('#action').val('rvVehicle');
        var url = 'admin.php?page=rvedit&id=';
        $.ajax({
            url: endpoint,
            method: "POST",
            data: new FormData(document.getElementById('vehicleform')),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                if (data.insertid) {
                    url += data.insertid;
                    location.href = url;
                }
            }
        });
    });

    // image upload
    $('#featured').on('change', function(e) {
        e.preventDefault();
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        var formData = new FormData(document.getElementById('vehicleform'));
        formData.append('action', 'rvUpload');
        $.ajax({
            url: endpoint,
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                var html = '';
                html +='<div class="thumbnail"><span class="btn btn-danger btn-xs pull-right delete" data-image_id="' + data.save.insertid +'"><i class="fa fa-times"></i></span><img class="img-fluid" src="' + data.url + '" alt="" /></div>';
                $('#featured_thumb').html(html);
                $('#featuredImage').val(data.url);
                $('#featuredid').val(data.save.insertid);
                if (data.type.error) {
                    $('.error').text(data.type.error);
                } else {
                    $('.error').text('');
                }
                $('.drop_area').hide();
            }
        });
    });

    // drag and drop
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $("html").on("drop", function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    $('#drop_area').on('dragover', function() {
        $(this).addClass('drag_over');
        return false;
    });
    $('#drop_area').on('dragleave', function() {
        $(this).removeClass('drag_over');
        return false;
    });

    // single drag drop
    $('.drop_area').on('drop', function(e) {
        e.preventDefault();
        jQuery('#action').val('rvDragDrop');
        $(this).removeClass('drag_over');
        var formData = new FormData();
        var files = e.originalEvent.dataTransfer.files;
        formData.append('file[0]', files[0]);
        formdata.append('id', id);
        formdata.append('action', 'rvSingleDragDrop');
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        $.ajax({
            url: endpoint,
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log(data);
                $('#featured_thumb').append(data.thumbs);
                $('.drop_area').hide();
                $('#featuredImage').val(data.url);
                $('#featuredid').val(data.save.insertid);
            }
        });
    });

    // multiple select
    $('#ddgallery').on('change', function(e) {
        e.preventDefault();
        var formData = new FormData(document.getElementById('vehicleform'));
        var id = $('#id').val();
        formData.append('id', id);
        formData.append('action', 'rvDragDrop');
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        $.ajax({
            url: endpoint,
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                //console.log(data);
                $('#uploaded_file').append(data.thumbs);
                $('#drop_area').hide();
                $('#gallery').text(data.gallery);
                $('#galleryfiles').val(data.galleryfiles);
            }
        });
    });

    // multiple drag drop
    $('#drop_area').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag_over');
        var id = $('#id').val();
        var formData = new FormData(document.getElementById('vehicleform'));
        var files = e.originalEvent.dataTransfer.files;
        for (var i = 0; i < files.length; i++) {
            formData.append('file[]', files[i]);
        }
        formData.append('id', id);
        formData.append('action', 'rvDragDrop');
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        $.ajax({
            url: endpoint,
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                //console.log(data);
                $('#uploaded_file').append(data.thumbs);
                $('#drop_area').hide();
                $('#gallery').text(data.gallery);
                $('#galleryfiles').val(data.galleryfiles);
            }
        });
    });

    // delete image
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
        var formdata = new FormData(document.getElementById('vehicleform'));
        var image = $(this).data('image_id');
        formdata.append('image_id', image);
        var thumb = $(this).parent();
        formdata.append('action', 'rvDeleteImage');

        $.ajax({
            url: endpoint,
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
            }
        });
    });

    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        $('#tabs').find('.tab').removeClass('active');
        $(this).addClass('active');
        $('#tabcontent').find('.tabcontent').hide();
        $('.detailcontent').show();
    });

    $(document).on('click', '.gallery', function(e) {
        e.preventDefault();
        $('#tabs').find('.tab').removeClass('active');
        $(this).addClass('active');
        $('#tabcontent').find('.tabcontent').hide();
        $('.gallerycontent').show();
    });

    $(document).on('click', '.publish', function(e) {
        e.preventDefault();
        $('#tabs').find('.tab').removeClass('active');
        $(this).addClass('active');
        $('#tabcontent').find('.tabcontent').hide();
        $('.publishcontent').show();
    });

    //Set Featured Image
    var rvFeatured;
    $('#featuredUpload').click(function(e) {
        e.preventDefault();
        // If the upload object has already been created, reopen the dialog
        if (rvFeatured) {
            rvFeatured.open();
            return;
        }
        // Extend the wp.media object
        rvFeatured = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: false
        });
        // When a file is selected, grab the URL and set it as the text field's value
        rvFeatured.on('select', function() {
            var attachment = rvFeatured.state().get('selection').first().toJSON();
            $('#featuredImage').val(attachment.url);
            $('#featuredid').val(attachment.id);
            $('#featuredThumb').attr('src', attachment.url);
            $('#featuredUpload').text('Change Image');
        });
        // Open the upload dialog
        rvFeatured.open();
    });
    //Set Gallery
    var rvGallery;
    $('#gallery_button').click(function(e) {
        e.preventDefault();
        if (rvGallery) {
            rvGallery.open();
            return;
        }
        rvGallery = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: true
        });
        rvGallery.on('select', function() {
            var attachment = rvGallery.state().get('selection').toJSON();
            var gallery = '';
            var thumbs = '';
            var galleryfiles = '';
            $.each(attachment, function(key, value) {
                thumbs += '<img width="90" src="' + value.url + '" alt="" />';
                gallery += value.url + ',';
                galleryfiles += value.id + ',';
            });
            $('#gallery').text(gallery);
            $('#galleryfiles').val(galleryfiles);
            $('#gallery_container').html(thumbs);
        });
        rvGallery.open();
    });
});
</script>
<div style="display: block; clear: both;"></div>