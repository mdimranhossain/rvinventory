<?php
/*
* Vehicle
* @Package: rvinventory
*/

declare(strict_types=1);

namespace Inc;

class Vehicle
{   
    private $db;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'inventory';
    }

    public function rvUrl(string $rvLink): string
    {
        return plugins_url($rvLink, dirname(__FILE__));
    }

    public function rvList(): string
    {
        $page = isset( $_GET['page'] ) ? abs( (int) $_GET['page'] ) : 1;
        //$rvcat = 1;
        $where = "1";
        if(!empty($_GET['rvcat'])){
            $rvcat = $_GET['rvcat'];
            $where = "rvCategory ='".$rvcat."'";
        }

        $per_page = 10;
        if ($page > 1) {
            $offset = ($page- 1) * $per_page ;
        } else {
            $offset = 0;
        }
        
        $query = "SELECT * FROM ".$this->table." WHERE ".$where." ORDER BY year DESC LIMIT ".$offset.",".$per_page;
        //echo $query;
        $vehicles = $this->db->get_results($query);

        return json_encode($vehicles);
    }

    public function rvShortcodeList($rvcat = 1): string
    {
        $page = isset( $_GET['page'] ) ? abs( (int) $_GET['page'] ) : 1;

        $where = 1;

        if(!empty($rvcat)){
            $where = "rvCategory ='".$rvcat."'";
        }

        $per_page = 10;
        if ($page > 1) {
            $offset = ($page- 1) * $per_page ;
        } else {
            $offset = 0;
        }
        
        $query = "SELECT * FROM ".$this->table." WHERE ".$where." ORDER BY year DESC LIMIT ".$offset.",".$per_page;
        //echo $query;
        $vehicles = $this->db->get_results($query);
        // return $vehicles;
        $total  = $this->rvTotal($rvcat);
        $slug = get_option('rv_slug');
        $phone = get_option('rv_phone');
        $contact_dealer = get_option('rv_contact_dealer');

        $pagination = paginate_links( array(
            'base' => add_query_arg('page', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo; Previous'),
            'next_text' => __('Next &raquo;'),
            'total' => ceil($total / $per_page),
            'type'=>'list',
            'current' => $page
        ));

        $output = '<div id="inventory">';
        //$output .= '<p>'.$query.'</p>';
        foreach($vehicles as $vehicle){
            $output .= '<div class="vehicle container">'; //vehicle wrapper
            $output .= '<div class="row vehicle-title">
                    <div class="col-sm-6">
                        <h3><a href="'.esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug.'">'.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.'</a></h3>
                    </div>
                    <div class="col-sm-6">
                    <ul>
                        <li class="phone"><i class="fa fa-phone"></i> <a href="tel:'.$phone.'">'.$phone.'</a></li>
                        <li data-id="'.$vehicle->slug.'" class="print"> <i class="fa fa-print"></i> </li>
                    </ul>
                    </div> 
                </div>'; //vehicle title ends

            $output .= '<div class="row vehicle-content">'; // vehicle content
                $output .= '<div class="col-sm-4 imagegallery">';

                if($vehicle->gallery){ 
                    $images = explode(',',$vehicle->gallery);
                    $output .= '<ul class="slider">';
                        foreach( $images as $image ){
                            $output .= '<li>
                                            <a href="'.esc_url($image).'">
                                                <img src="'.esc_url($image).'" alt="'.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.'" />
                                            </a>
                                        </li>';
                        } // end foreach images
                    $output .= '</ul>'; //slider ends
                } // endif gallery

                $output .= '</div>'; // gallery ends
                $output .= '<div class="col-sm-4">
                                <div class="vehicle-description">
                                    <p>'.$vehicle->description.'</p>
                                </div>
                            </div>'; // description ends
                $output .= '<div class="col-sm-4 text-center dealer">
                            <h4>';
                            if(!empty($vehicle->salePrice) && $vehicle->salePrice>0){
                                $output .= "Price: $".number_format(floatval($vehicle->salePrice),2);
                            }elseif(!empty($vehicle->msrp) && $vehicle->msrp>0){
                                $output .= "<br>MSRP: $".number_format($vehicle->msrp,2);
                            }else{
                                $output .= 'Call For Pricing';
                            }        
                $output .= '</h4>
                            <a href="'.esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug.'" class="btn btn-link btn-lg border border-danger d-block">View Details</a>
                            <a href="'.esc_url($contact_dealer).'" class="btn btn-link btn-lg border border-danger d-block">$ Contact Dealer</a>
                        </div>'; // dealer info ends
            $output .= '</div>'; // end vehicle content
            
            $output .= '</div>'; // end vehicle wrapper
        } // end foreach vehicles
        
        $output .= '';
        // pagination
        $output .= '<div class="pages container"><nav aria-label="Page navigation">'.$pagination.'</nav></div>';
        // pagination format
        $output .= "<script>
		$(document).ready(function() {
			$('.pages ul').addClass('pagination');
			$('.pages ul').removeClass('page-numbers');
			$('.pages ul li').addClass('page-item');
			$('.pages ul li a').addClass('page-link');
			$('.pages ul li a').removeClass('page-numbers');
			$('.pages ul li span.current').addClass('page-link');
		});
		</script>";

        $output .='<div id="print" class="hidden">
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
                    </div>';
        $output .="<script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
        $(document).ready(function(){
            $(document).on('click','.print',function(){
                var id = $(this).data('id');
                var details = '".$this->rvUrl('/vehicle_details.php')."';
        
                $.ajax({
                    url: details+'?vehicle='+id,
                    method: 'GET',
                    // data: {vehicle:id},
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
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
        });</script>";

        $output .= '</div>';
        
        return $output;
    }

    public function rvTotal($rvcat=NULL): int
    {
        $where = 1;
        if(!empty($rvcat)){
            $where = "rvCategory ='".$rvcat."'";
        }
        $total = intval($this->db->get_var('SELECT COUNT(id) FROM '.$this->table.' WHERE '.$where));
        return $total;
    }

    public function rvCreate(): string
    {
        $input = $_POST;
        $data['post'] = $_POST;
        $data['insert'] = [
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
            'gallery' => $input['gallery'],
            'galleryfiles' => $input['galleryfiles'],
            'status' => $input['status'],
            'createdBy' => $input['createdBy'],
            'createdAt' => $input['createdAt']
        ];
        $format = ['%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s'];

        $data['success'] = $this->db->insert($this->table,$data['insert'],$format);

        if($data['success']){
            $data['message'] = "Vehicle Added Successfully.";
        }else{
            $data['error'] = "Something Went Wrong!!!".$this->db->last_error;
        }

        $data['insertid'] = $this->db->insert_id;

        return json_encode($data);
    }

    // Vehicle Slug
    public function rvSlug(): string
    {
        $data['year'] = '';
        $data['make'] = '';
        $data['model'] = '';
        $data['slug'] = '';

        if(!empty($_REQUEST['year'])){
            $data['year'] = trim($_REQUEST['year']);
            $data['slug'] = $this->rvBuildSlug($data['year']);
        }

        if(!empty($_REQUEST['make'])){
            $data['make'] = trim($_REQUEST['make']);
            $data['slug'] .= '-'.$this->rvBuildSlug($data['make']);
        }

        if(!empty($_REQUEST['model'])){
            $data['model'] = trim($_REQUEST['model']);
            $data['slug'] .= '-'.$this->rvBuildSlug($data['model']);
        }

        $data['count'] = $this->db->get_var("SELECT COUNT('id') FROM ".$this->table." WHERE slug LIKE '%".$data['slug']."%'");

        if($data['count']>0){
            $data['slug'] = $data['slug'].'-'.($data['count']+1);
        }

        $data['slug'] = strtolower($data['slug']);

       return json_encode($data);
    }

    // build vehicle slug
    public function rvBuildSlug(string $string)
    {
        $string = trim(strtolower($string));
        $string = preg_replace('/[^a-z0-9 -]+/', '', $string);
        $string = preg_replace('/ +/', ' ', $string);
        $string = str_replace(' ', '-', $string);
        return trim($string, '-');
    }

    // Vehicle Update

    public function rvUpdate(): string
    {
        $input = $_POST;
        $id = $input['id'];
        $data['update'] = [
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
            'gallery' => $input['gallery'],
            'galleryfiles' => $input['galleryfiles'],
            'status' => $input['status'],
            'createdBy' => $input['createdBy'],
            'createdAt' => $input['createdAt']
        ];

        $where = [ 'id' => $id ];
        $data['success'] = $this->db->update($this->table, $data['update'], $where);

        if($data['success']){
            $data['message'] = "Vehicle Updated Successfully.";
        }else{
            $data['error'] = "Something Went Wrong!!!".$this->db->last_error;
            $data['message'] = "";
        }

       return json_encode($data);
    }

    public function rvDetails($slug)
    {
        $result = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table} WHERE slug = %s", $slug));
        return $result;
    }

    public function rvDelete(): string
    {
        $id = $_REQUEST['post_id'];
        $data['vehicle'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
        $data['delete'] = $this->db->delete($this->table, ['id' => $id]);

        $data['message'] = '';
        if($data['delete']){
            $data['message']['inventory'] = 'Vehicle Deleted!!!';
        }
        echo json_encode($data);
    }

    public function rvSitemap(): array
    {
        $home = get_option('home');
        $rvslug = get_option('rv_slug');
        $invlink = $home.'/'.$rvslug.'/';
        $query = "SELECT slug FROM ".$this->table." WHERE 1";
        $slugs = $this->db->get_results($query);
           
        $links = [];
        foreach($slugs as $slug){
           $links[] = ['loc'=>$invlink.$slug->slug, 'priority' => '0.5', 'changefreq' => 'daily', 'lastmod' => date('Y-m-d')];
        }
        return $links;
    }

}