<?php
/**
 * Template Name: Inventory Details
 */
get_header(); 

$slug = get_option('vi_slug');
$slug = get_query_var($slug);
$emailfriend = get_option('vi_emailfriend');
$availability = get_option('vi_availability');
$address = get_option('vi_address');
$phone = get_option('vi_phone');
$weekday = get_option('vi_weekday');
$weekend = get_option('vi_weekend');
//echo $inventory;
global $wpdb;
$table = $wpdb->prefix.'inventory';
$query = $wpdb->prepare("SELECT * FROM {$table} WHERE slug='%s'", $slug);
$vehicle = $wpdb->get_row($query);
$vehicleTitle = $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional;
//print_r($vehicle);
//echo "Best Built Inventory Details Page";
?>
<script>document.title = "<?php echo $vehicleTitle; ?>";</script>
<div class="container bg-grey pt-2 pb-2">
	<div class="row">
		<div class="col-sm-8">
			<div class="row">
				<div class="col-sm-12 text-center">
					<h2><?php echo $vehicleTitle; ?></h2>
				</div>
				<div class="col-sm-12">
					<?php
						if($vehicle->gallery){ 
							$images = explode(',',$vehicle->gallery)
							?>
							<ul class="slider">
								<?php 
									foreach( $images as $image ): 
									
									?>
									<li>
										<a data-fancybox="gallery" data-caption="<?php echo $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional; ?>" href="<?php echo esc_url($image); ?>">
											<img src="<?php echo esc_url($image); ?>" alt="<?php echo $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional; ?>" />
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
							<ul class="pager m-0 p-0">
								<?php 
								$i = 0;
									foreach( $images as $image ): 
										$i++;
									?>
									<li>
										<a data-slide-index="<?php echo $i-1;?>" href="#">
											<img src="<?php echo esc_url($image); ?>" alt="" />
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
					<?php } ?>
					<!-- <img src="<?php //echo $vehicle->featuredImage; ?>" alt="" class="img-fluid" /> -->
				</div>
			
				<div class="col-sm-12">
					<p><?php echo $vehicle->description; ?></p>
				</div>
			</div>
			<div class="container">
			<div class="row bg-white rounded">
				<div class="col-sm-12">
					<h4 class="pt-2 pb-2 mt-2 bg-grey rounded text-center">Details</h4>
					<h4 class="pt-2 pb-2"><?php echo $vehicle->make.' '.$vehicle->model.' '.$vehicle->additional; ?></h4>
					<ul class="ml-4">
						<li>Condition: <?php echo stripslashes($vehicle->vehicleCondition); ?></li>
						<li>Payload capacity: <?php echo stripslashes($vehicle->payloadCapacity); ?></li>
						<li>Empty weight: <?php echo stripslashes($vehicle->emptyWeight); ?></li>
						<li>Floor length: <?php echo stripslashes($vehicle->floorLength); ?></li>
						<li>Floor width: <?php echo stripslashes($vehicle->floorWidth); ?></li>
						<li>Side height: <?php echo stripslashes($vehicle->sideHeight); ?></li>
						<li>Body type: <?php echo stripslashes($vehicle->bodyType); ?></li>
					</ul>
				</div>
				<div class="col-sm-12 m-3">
					<h5 class="pt-2 pb-2">Additional Information:</h5>
					<p><?php echo nl2br(htmlentities($vehicle->addtionalInfo, ENT_QUOTES, 'UTF-8')); ?></p>
				</div>
				
			</div> 
			</div>
		</div>
		<div class="col-sm-4 sidebar">
			<div class="buttons">
				<a class="btn btn-danger btn-block" data-fancybox data-type="iframe" data-src="<?php echo $availability; ?>" href="javascript:;" id="" role="button">
				<i class="fa fa-check-square-o"></i><span> Check Availability</span></a>
				<a class="btn btn-danger btn-block" data-fancybox data-type="iframe" data-src="<?php echo $emailfriend; ?>" href="javascript:;" id="" role="button">
				<i class="fa fa-envelope-o"></i><span> Email a Friend</span></a>
			</div>
		
			<div class="contact p-3 bg-white rounded">
			<h3 class="contact-title text-danger border-bottom-1">Contact Details</h3>
			<p><?php echo strtoupper($address); ?><br>
			<b>Phone:</b> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a><br>
			<b>Monday – Saturday:</b> <?php echo strtoupper($weekday);?><br> <b>Sunday:</b> <?php echo strtoupper($weekend);?></p>
			</div>
		</div>
	</div> 
</div>

<?php
get_footer();