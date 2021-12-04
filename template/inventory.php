<?php
/**
 * Template Name: Inventory List
 */
get_header();
$slug = get_option('vi_slug');
$phone = get_option('vi_phone');
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
			

			$query = $wpdb->prepare("SELECT * FROM {$table} WHERE $where ORDER BY id DESC LIMIT $offset,$per_page", $rvcat);
            //echo $query;
			$vehicles = $wpdb->get_results($query);
            //print_r($vehicles);
			// exit;
			
			foreach($vehicles as $vehicle){
			?>

			<div class="vehicle container">
	      		<div class="row vehicle-title">
	      			<div class="col-sm-6">
	      				<h3><a href="<?php echo esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug; ?>"><?php echo $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional; ?></a></h3>
	      			</div>
	      			<div class="col-sm-6">
						<ul>
							<li class="phone"><i class="fa fa-phone"></i> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a></li>
							<li class="print"> <a href="#"><i class="fa fa-print"></i></a> </li>
						</ul>
	      			</div>
	      		</div>
	      		<div class="row vehicle-content">
	      			<div class="col-sm-4">
						<?php
							if($vehicle->gallery){ 
								$images = explode(',',$vehicle->gallery)
								?>
								<ul class="slider">
									<?php foreach( $images as $image ): ?>
										<li>
											<a href="<?php echo esc_url($image); ?>">
												<img src="<?php echo esc_url($image); ?>" alt="<?php echo $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional; ?>" />
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
								echo "Price: $".$vehicle->salePrice;
							}elseif(!empty($vehicle->msrp) && $vehicle->msrp>0){
								echo "<br>MSRP: $".$vehicle->msrp;
							}else{
								echo 'Call For PRICING';
							}
							?>
						</h4>
						<a href="<?php echo esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug; ?>" class="btn btn-link btn-lg border border-danger d-block">View Details</a>
						<a href="javascript:;" class="btn btn-link btn-lg border border-danger d-block">$ Contact Dealer</a>
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
	</div>

<?php
get_footer();
