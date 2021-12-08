<?php

/*

* vivehicles

* @Package: rvinventory

*/

require_once( ABSPATH . 'wp-load.php' );

require_once( ABSPATH . 'wp-admin/admin.php' );

require_once( ABSPATH . 'wp-admin/admin-header.php' );



$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';

if (file_exists($viAutoload)) {

    require_once $viAutoload;

}



use Inc\Setting;

$setting = new Setting();

$options = $setting->viInventoryOptions();

$slug = $options['slug'];

function viurl(string $viLink){

	return plugins_url($viLink, dirname(__FILE__));

}

global $wpdb;

$table = $wpdb->prefix.'inventory';

$query = $wpdb->prepare("SELECT * FROM {$table} WHERE %d", 1);

$vehicles = $wpdb->get_results($query);

//print_r($vehicles);

?>

<div id="rvinventory" class="container-fluid">

    <div class="message">

    <div class="image"></div>

    <div class="inventory"></div>

    </div>

	<h2>Vehicle List (<a href="<?php echo esc_url(home_url($slug)); ?>" target="_blank"> Public View</a>)</h2>

	<div id="vehiclelist" class="table-responsive">

		<table id="vehicles" class="display table table-bordered table-striped">

			<thead>

				<tr>

					<th>Title</th>

					<th>Sale Price</th>

					<th>MSRP</th>

					<th>Description</th>

					<th>Condition</th>

					<th>Actions</th>

				</tr>

			</thead>

			<tbody>

			<?php

			if($vehicles){

				foreach($vehicles as $vehicle){

					echo '<tr id="row'.$vehicle->id.'"><td><a target="_blank" class="dlink" dataid="'.$vehicle->id.'" href="/'.$slug.'/'.$vehicle->slug.'">'.$vehicle->make.' '.$vehicle->model.' '.$vehicle->additional.'</a></td><td>'.$vehicle->salePrice.'</td><td>'.$vehicle->msrp.'</td><td>'.$vehicle->description.'</td><td>'.$vehicle->vehicleCondition.'</td><td><a href="/wp-admin/admin.php?page=viedit&id='.$vehicle->id.'" type="button" class="btn btn-warning btn-sm btn-edit" title="Edit" ><i class="fa fa-pencil"></i></a><button data-toggle="modal" data-target="#deletemodal" data-id="'.$vehicle->id.'" data-featured="'.$vehicle->featuredid.'" data-gallery="'.rtrim($vehicle->galleryfiles,',').'" type="button" title="Delete" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></button></td></tr>';

				}

			}

			?>

			</tbody>

			<tfoot>

				<tr>

                    <th>Title</th>

                    <th>Sale Price</th>

                    <th>MSRP</th>

                    <th>Description</th>

                    <th>Condition</th>

                    <th>Actions</th>

				</tr>

			</tfoot>

		</table>

	</div>

</div>



<?php

//    $nonce = wp_create_nonce("viDeleteAttachment");

//     $link = admin_url('admin-ajax.php?action=viDeleteAttachment&post_id=11&nonce='.$nonce);

//     echo '<a class="viDelete" target="_blank" data-nonce="' . $nonce . '" data-post_id="11" href="' . $link . '">Delete</a>';

?>



<!-- delete modal -->

<div id="deletemodal" class="modal fade" role="dialog" data-backdrop="static">

    <div class="modal-dialog">

        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title"></h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                

            </div>

            <div class="modal-body">

                <form id="deleteform" action="" method="post" accept-charset="utf-8">

                    <input type="hidden" id="id" name="post_id" />

                    <input type="hidden" id="action" name="action" value="viDeleteAttachment" />

                    <input type="hidden" id="vehicle" name="vehicle" value="delete" />

                    <input type="hidden" id="featuredid" name="featuredid" />

					<input type="hidden" id="galleryfiles" name="galleryfiles" />

<input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce( 'viDeleteAttachment' ); ?>" />



                    

                    <p class="confirm">Are you sure to delete this vehicle?<br/><button class="btn btn-primary btn-sm submit" type="submit">Yes</button></p>

                </form>

            </div>

        </div>



    </div>

</div>

<script>

  jQuery(document).ready(function($){

	$('#vehicles').DataTable({

                responsive: true

                });

	

	// delete action

	$(document).on('click','.btn-delete', function() {

        var modaltitle = "Delete Vehicle";

        var dataid = $(this).data('id');

        var featured = $(this).data('featured');

        var gallery = $(this).data('gallery');

        $('#deletemodal .modal-title').text(modaltitle);

        $('#deletemodal #id').attr('value',dataid);

        $('#deletemodal #featuredid').attr('value',featured);

        $('#deletemodal #galleryfiles').attr('value',gallery);

        // console.log(modaltitle);

    });





    $('#deleteform').on('submit', function(e) {

        e.preventDefault();

        var deletemedia = "<?php echo admin_url('admin-ajax.php');?>";

        var deleteinventory = "<?php echo viurl("/vehicle.php");?>";

        var form = document.getElementById('deleteform');



        $.ajax({

            url: deletemedia,

            method: "POST",

            data: new FormData(form),

            contentType: false,

            cache: false,

            processData: false,

            dataType: "json",

            success: function(data) {

               // console.log(data);

                var html = '';

                    if (data.errors) {

                        html = '<div class="alert alert-danger">';

                        for (var count = 0; count < data.errors

                            .length; count++) {

                            html += '<p>' + data.errors[count] + '</p>';

                        }

                        html += '</div>';

                    }

                    setTimeout(function () {

                            $('#deletemodal .close').trigger('click');

                        }, 500);

                    if (data.message.image) {

                        html = '<div class="alert alert-success">' + data.message.image + '</div>';



                        $('.message .image').html(html);



                        setTimeout(function () {

                            $('.message .image').fadeOut('slow');

                        }, 1000);

                    }



                    if (data.message.inventory) {

                        html = '<div class="alert alert-success">' + data.message.inventory + '</div>';



                        $('.message .inventory').html(html);

                        $('.message .inventory').show(500);



                        setTimeout(function () {

                            $('.message  .inventory').fadeOut('slow');

                            $('#row'+data.id).fadeOut('slow');

                        }, 1500);

                    }

            }

        });



    });

  });

</script>

<div style="display: block; clear: both;"></div>