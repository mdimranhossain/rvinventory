<?php
/**
 * Template Name: Inventory List
 */

get_header();
$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if(file_exists($rvAutoload)){
  require_once $rvAutoload;
}

use Inc\Vehicle;

$slug = get_option('rv_slug');

$cats = ['rvs-for-sale-everett','class-a-b-c-rvs','class-c-rvs','campers','fifth-wheels','toy-hauler-rvs-for-sale','travel-trailers'];

function rvurl(string $viLink){
	return plugins_url($viLink, dirname(__FILE__));
}
$phone = get_option('rv_phone');
$contact_dealer = get_option('rv_contact_dealer');
$pageTitle = get_option('rv_pageTitle');
?>

<div class="bead">
	<div class="container">
	      <div class="text-center">
		   <h1 class="entry-title"><?php echo stripslashes($pageTitle);?></h1>
	      </div>
	</div>
</div>
<div class="sub_header_menu">
	<?php wp_nav_menu( array(
	    'theme_location' => 'quick',
	    'container_class' => 'sub-header-link'
	    )
	);
	?>
</div>

<div id="inventory">
<form id="vehicleform" action="" method="POST" enctype="multipart/form-data">
<?php wp_nonce_field( 'print-vehicle_'.time());?>
</form>
	<?php
		$vehicle = new Vehicle();
		$vehicles = json_decode($vehicle->rvList(TRUE));

		$page = isset( $_GET['page'] ) ? abs( (int) $_GET['page'] ) : 1;
		$total = $vehicle->rvTotal();
		$per_page = 10;

		foreach($vehicles as $vehicle){
		?>

		<div class="vehicle container">
			<div class="row vehicle-title">
				<div class="col-sm-6">
					<h3><a href="<?php echo esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug; ?>"><?php echo stripslashes($vehicle->year.' '.$vehicle->make.' '.$vehicle->model); ?></a></h3>
				</div>
				<div class="col-sm-6">
					<ul>
						<li class="phone"><i class="fa fa-phone"></i> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a></li>
						<li data-slug="<?php echo $vehicle->slug;?>" data-id="<?php echo $vehicle->id;?>" class="print"> <i class="fa fa-print"></i> </li>
					</ul>
				</div>
			</div>
			<div class="row vehicle-content">
				<div class="col-sm-4">
					<?php
						if($vehicle->gallery){ 
							$images = explode(',',$vehicle->gallery);
							?>
							<ul class="slider">
								<?php foreach( $images as $image ): ?>
									<li>
										<a href="<?php echo esc_url($image); ?>">
											<img src="<?php echo esc_url($image); ?>" alt="<?php echo $vehicle->year.' '.$vehicle->make.' '.$vehicle->model; ?>" />
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
					<?php } ?>
				</div>

				<div class="col-sm-4">
					<div class="vehicle-description">
						<p><?php echo stripslashes($vehicle->description); ?></p>
					</div>
				</div>

				<div class="col-sm-4 text-center dealer">
					<h4>
						<?php

						if(!empty($vehicle->salePrice) && $vehicle->salePrice>0){
							echo "Price: $".number_format($vehicle->salePrice,2);
						}else{
							echo 'Call For Pricing';
						}
						?>
					</h4>
					<a href="<?php echo esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug; ?>" class="btn btn-link btn-lg border border-danger d-block">View Details</a>
					<a href="<?php echo esc_url($contact_dealer); ?>" class="btn btn-link btn-lg border border-danger d-block"> Contact Dealer</a>
				</div>
					
			</div>

		</div>
	<?php } // end foreach ?>
	<div class="pages container">
		<nav aria-label="Page navigation">
			<?php
				echo paginate_links( array(
					'base' => add_query_arg('page', '%#%'),
					'format' => '',
					'prev_text' => __('&laquo; Previous'),
					'next_text' => __('Next &raquo;'),
					'total' => ceil($total / $per_page),
					'type'=>'list',
					'current' => $page
				));
			?>
		</nav>
	</div>
	<script>
	$(document).ready(function() {
		$('.pages ul').addClass('pagination');
		$('.pages ul').removeClass('page-numbers');
		$('.pages ul li').addClass('page-item');
		$('.pages ul li a').addClass('page-link');
		$('.pages ul li a').removeClass('page-numbers');
		$('.pages ul li span.current').addClass('page-link');
	});
	</script>
</div> <!-- #inventory -->

<div id="print" class="hidden">
	<div class="container">
		<div class="row"><div class="col-sm-12 text-center"><h1 id="ptitle"></h1></div></div>
		<div class="row">
			<div class="col-sm-6">
				<p>Category: <span id="pcategory"></span></p>
				<p>Mileage: <span id="pmileage"></span></p>
				<p>Price: <span id="pprice"></span></p>
			</div>
			<div class="col-sm-6">
				<img class="img-fluid" id="pphoto" src="" alt="" />
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<p id="pdescription">

				</p>
			</div>
		</div>
	</div>
</div>
<script>
	function printDiv(divName) {
		var printContents = document.getElementById(divName).innerHTML;
		var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
		window.print();
		document.body.innerHTML = originalContents;
	}

	$(document).ready(function(){
		$(document).on('click','.print',function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var slug = $(this).data('slug');
			var endpoint = "<?php echo admin_url('admin-ajax.php');?>";
			// var formdata = "{'action':'rvDetails','id':"+id+",'slug':"+slug+"}";
			var formdata = new FormData(document.getElementById('vehicleform'));
			formdata.append('action', 'rvDetails');
			formdata.append('slug', slug);
			formdata.append('id', id);
			// console.log(formdata);
	
			$.ajax({
				url: endpoint,
				method: 'POST',
				data: formdata,
				contentType: false,
				cache: false,
				processData: false,
				dataType: 'json',
				success: function(data){
					console.log(data);
					var pyear = '';
					var pmake = '';
					var pmodel = '';
					if(data.year){
						pyear = data.year;
					}
					if(data.make){
						pmake = data.make;
					}
					if(data.model){
						pmodel = data.model;
					}
					var ptitle = pyear+' '+pmake+' '+pmodel;
					$('#ptitle').text(ptitle);
					$('#pcategory').text(data.rvCategory);
					$('#pmileage').text(data.mileage);
					$('#pprice').text(data.salePrice);
					$('#pphoto').attr('src',data.featuredImage);
					$('#pdescription').text(data.description);

					printDiv('print');
				}

			});
		});
	});
</script>
<?php
get_footer();
