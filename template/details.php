<?php
/**
 * Template Name: Inventory Details
 */
get_header(); 

$slug = get_option('vi_slug');
$slug = get_query_var($slug);

$emailfriend = get_option('vi_emailfriend');
$availability = get_option('vi_availability');
$contact_dealer = get_option('vi_contact_dealer');
$address = get_option('vi_address');
$phone = get_option('vi_phone');
$weekday = get_option('vi_weekday');
$saturday = get_option('vi_saturday');
$weekend = get_option('vi_weekend');
//echo $inventory;
global $wpdb;
$table = $wpdb->prefix.'inventory';
$query = $wpdb->prepare("SELECT * FROM {$table} WHERE slug='%s'", $slug);
$vehicle = $wpdb->get_row($query);
$vehicleTitle = $vehicle->year.' '.$vehicle->make.' '.$vehicle->model;
// print_r($vehicle);
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
										<a data-fancybox="gallery" data-caption="<?php echo $vehicle->year.' '.$vehicle->make.' '.$vehicle->model; ?>" href="<?php echo esc_url($image); ?>">
											<img src="<?php echo esc_url($image); ?>" alt="<?php echo $vehicle->year.' '.$vehicle->make.' '.$vehicle->model; ?>" />
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
				
				</div>
			
			</div>
			<div class="container">
			<div class="row bg-white rounded">
				<div class="col-sm-12">
					<h4 class="pt-2 pb-2 mt-2 bg-grey rounded text-center">Details</h4>
					<h4 class="pt-2 pb-2"><?php echo $vehicle->year.' '.$vehicle->make.' '.$vehicle->model; ?></h4>
					
				</div>
			
				<div class="col-sm-12">
					<p><?php echo $vehicle->description; ?></p>
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
			<p><?php echo $address; ?><br>
			<b>Phone:</b> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a><br>
			<b>Monday – Friday:</b> <?php echo $weekday;?><br> <b>Saturday:</b> <?php echo $saturday;?><br> <b>Sunday:</b> <?php echo $weekend;?></p>
			</div>
		</div>
	</div> 
</div>

<?php
get_footer();