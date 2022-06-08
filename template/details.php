<?php
/**
 * Template Name: Inventory Details
 */

get_header(); 

$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if(file_exists($rvAutoload)){
  require_once $rvAutoload;
}

use Inc\Vehicle;

$slug = get_option('rv_slug');
$vslug = get_query_var($slug);

$emailfriend = get_option('rv_emailfriend');
$availability = get_option('rv_availability');
$contact_dealer = get_option('rv_contact_dealer');
$address = get_option('rv_address');
$city = get_option('rv_city');
$state = get_option('rv_state');
$zip = get_option('rv_zip');
$phone = get_option('rv_phone');
$weekday = get_option('rv_weekday');
$saturday = get_option('rv_saturday');
$weekend = get_option('rv_weekend');

$vehicle = new Vehicle();
$vehicle = $vehicle->rvDetails($vslug);

$vehicleTitle = $vehicle->year.' '.$vehicle->make.' '.$vehicle->model;
?>

<div class="bead" style="background-image:url(/wp-content/uploads/2021/12/miscellaneous.png)">
	<div class="container">
	      <div class="text-center">
		   <h1 class="entry-title"><?php echo $vehicleTitle;?></h1>
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
							<ul id="detialslider" class="slider">
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
		<div class="col-sm-4 sidebar dealer">
			<h4>
				<?php
				if(!empty($vehicle->salePrice) && $vehicle->salePrice>0){
					echo "Price: $".number_format($vehicle->salePrice,2);
				}else{
					echo 'Call For Pricing';
				}
				$vfrom = $vehicle->year.'-'.$vehicle->make.'-'.$vehicle->model;
				$vdetail = esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug;
				?>
			</h4>
			<div class="buttons">
				<a class="btn btn-danger btn-block" data-fancybox data-type="iframe" data-src="<?php echo $availability.'?vehicle='.$vfrom.'&vehicleurl='.$vdetail;?>" href="javascript:;" id="" role="button">
				<i class="fa fa-check-square-o"></i><span> Check Availability</span></a>
		
			</div>
		
			<div class="contact p-3 bg-white rounded">
			<h3 class="contact-title text-danger border-bottom-1">Contact Details</h3>
			<p><?php echo $address.' '.$city.', '.$state.', '.$zip; ?><br>
			<b>Phone:</b> <a href="tel:<?php echo $phone;?>"><?php echo $phone;?></a><br>
			<b>Monday – Friday:</b> <?php echo $weekday;?><br> <b>Saturday:</b> <?php echo $saturday;?><br> <b>Sunday:</b> <?php echo $weekend;?></p>
			</div>
		</div>
	</div> 
</div>

<?php
get_footer();