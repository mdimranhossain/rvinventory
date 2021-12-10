<?php
/**
 * Template Name: Inventory List
 */
get_header();
$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if(file_exists($viAutoload)){
  require_once $viAutoload;
}
$slug = get_option('vi_slug');
// $catpages = ['rvs-for-sale-everett'=>'rvs-for-sale-everett','class-c-rvs'=>'Class C RVs','campers'=>'Campers','fifth-wheels'=>'Fifth Wheels','motorhomes'=>'Motorhomes','travel-trailers'=>'Travel Trailers'];
$cats = ['rvs-for-sale-everett','class-c-rvs','campers','fifth-wheels','motorhomes','travel-trailers'];
// foreach($catpages as $key=>$val){
// 	$inventory = get_query_var($key);
//     if(strpos($_SERVER['REQUEST_URI'], $key) == true && !empty($inventory)){
// 		$slug = $key;
// 	}
// }
function viurl(string $viLink){
	return plugins_url($viLink, dirname(__FILE__));
}
$phone = get_option('vi_phone');
$contact_dealer = get_option('vi_contact_dealer');
$pageTitle = get_option('vi_pageTitle');
?>
<script>document.title = "<?php echo $pageTitle;?>";</script>
<div class="bead">
	<div class="container">
	      <div class="text-center">
		   <h1 class="entry-title"><?php echo $pageTitle;?></h1>
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
	<?php
		global $wpdb;
		$table = $wpdb->prefix.'inventory';
		$page = isset( $_GET['page'] ) ? abs( (int) $_GET['page'] ) : 1;
		$rvcat = 1;
		$where = "%s";
		if(!empty($_GET['rvcat'])){
			$rvcat = $_GET['rvcat'];
			$where = "rvCategory ='%s'";
		}
		
		$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT('id') FROM {$table} WHERE $where", $rvcat));
		$per_page = 10;
		if ($page > 1) {
			$offset = ($page- 1) * $per_page ;
		} else {
			$offset = 0;
		}
		

		$query = $wpdb->prepare("SELECT * FROM {$table} WHERE $where ORDER BY year DESC LIMIT $offset,$per_page", $rvcat);
		//echo $query;
		$vehicles = $wpdb->get_results($query);
		//print_r($vehicles);
		// exit;
		
		foreach($vehicles as $vehicle){
		?>

		<div class="vehicle container">
			<div class="row vehicle-title">
				<div class="col-sm-6">
					<h3><a href="<?php echo esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug; ?>"><?php echo $vehicle->year.' '.$vehicle->make.' '.$vehicle->model; ?></a></h3>
				</div>
				<div class="col-sm-6">
					<ul>
						<li class="phone"><i class="fa fa-phone"></i> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a></li>
						<li data-id="<?php echo $vehicle->id;?>" class="print"> <i class="fa fa-print"></i> </li>
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
						<p><?php echo $vehicle->description; ?></p>
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
		$('.print').click(function(){
			var id = $(this).data('id');
			var details = "<?php echo viurl("/vehicle_details.php");?>";

			$.ajax({
				url: details+'?vehicle='+id,
				method: "GET",
				// data: {vehicle:id},
				contentType: false,
				cache: false,
				processData: false,
				dataType: "json",
				success: function(data){
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
