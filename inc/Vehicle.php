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

    // public function viList(): string
    // {
    //     $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE %d", 1);
    //     $vehicles = $this->db->get_results($query);
    //     return json_encode($vehicles);
    // }

    public function viList(): string
    {
        $page = isset( $_GET['page'] ) ? abs( (int) $_GET['page'] ) : 1;
        $rvcat = 1;
        $where = "%s";
        if(!empty($_GET['rvcat'])){
            $rvcat = $_GET['rvcat'];
            $where = "rvCategory ='%s'";
        }

        $per_page = 10;
        if ($page > 1) {
            $offset = ($page- 1) * $per_page ;
        } else {
            $offset = 0;
        }
        
        $query = $this->db->prepare("SELECT * FROM {$table} WHERE $where ORDER BY id DESC LIMIT $offset,$per_page", $rvcat);
        //echo $query;
        $vehicles = $this->db->get_results($query);

        return json_encode($vehicles);
    }

    public function viShortcodeList($rvcat = 1): string
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
        
        $query = "SELECT * FROM ".$this->table." WHERE ".$where." ORDER BY id DESC LIMIT ".$offset.",".$per_page;
        //echo $query;
        $vehicles = $this->db->get_results($query);
        // return $vehicles;
        $total  = $this->viTotal($rvcat);
        $slug = get_option('vi_slug');
        $phone = get_option('vi_phone');

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
                        <h3><a href="'.esc_url(home_url()).'/'.$slug.'/'.$vehicle->slug.'">'.$vehicle->make.' '.$vehicle->model.' '.$vehicle->additional.'</a></h3>
                    </div>
                    <div class="col-sm-6">
                    <ul>
                        <li class="phone"><i class="fa fa-phone"></i> <a href="tel:'.$phone.'">'.$phone.'</a></li>
                        <li class="print"> <a href="#"><i class="fa fa-print"></i></a> </li>
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
                                                <img src="'.esc_url($image).'" alt="'.$vehicle->make.' '.$vehicle->model.' '.$vehicle->additional.'" />
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
                            <a href="javascript:;" class="btn btn-link btn-lg border border-danger d-block">$ Contact Dealer</a>
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
        $output .= '</div>';
        
        return $output;
    }

    public function viTotal($rvcat=NULL): int
    {
        $where = 1;
        if(!empty($rvcat)){
            $where = "rvCategory ='".$rvcat."'";
        }

        $total = intval($this->db->get_var('SELECT COUNT(id) FROM '.$this->table.' WHERE '.$where));

        return $total;
    }

    public function viCreate(): string
    {
        $input = $_POST;
        $data['post'] = $_POST;
        $data['insert'] = [
            'make' => $input['make'],
            'model' => $input['model'],
            'additional' => $input['additional'],
            'slug' => $input['slug'],
            'salePrice' => $input['salePrice'],
            'msrp' => $input['msrp'],
            'description' => $input['description'],
            'vehicleCondition' => $input['vehicleCondition'],
            'payloadCapacity' => $input['payloadCapacity'],
            'emptyWeight' => $input['emptyWeight'],
            'floorLength' => $input['floorLength'],
            'floorWidth' => $input['floorWidth'],
            'sideHeight' => $input['sideHeight'],
            'bodyType' => $input['bodyType'],
            'rvCategory' => $input['rvCategory'],
            'addtionalInfo' => $input['addtionalInfo'],
            'featuredImage' => $input['featuredImage'],
            'featuredid' => $input['featuredid'],
            'gallery' => $input['gallery'],
            'galleryfiles' => $input['galleryfiles'],
            'status' => $input['status'],
            'createdBy' => $input['createdBy'],
            'createdAt' => $input['createdAt']
        ];
        $format = array('%s','%s','%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s');
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
    public function viSlug(): string
    {
        $data['make'] = '';
        $data['model'] = '';
        $data['additional'] = '';
        $data['slug'] = '';
        if(!empty($_REQUEST['make'])){
            $data['make'] = trim($_REQUEST['make']);
            $data['slug'] = $this->viBuildSlug($data['make']);
        }
        if(!empty($_REQUEST['model'])){
            $data['model'] = trim($_REQUEST['model']);
            $data['slug'] .= '-'.$this->viBuildSlug($data['model']);
        }
        if(!empty($_REQUEST['additional'])){
            $data['additional'] = trim($_REQUEST['additional']);
            $data['slug'] .= '-'.$this->viBuildSlug($data['additional']);
        }

        $data['count'] = $this->db->get_var("SELECT COUNT('id') FROM ".$this->table." WHERE slug LIKE '%".$data['slug']."%'");

        if($data['count']>0){
            $data['slug'] = $data['slug'].'-'.($data['count']+1);
        }
        $data['slug'] = strtolower($data['slug']);
       return json_encode($data);
    }
    // build vehicle slug
    public function viBuildSlug(string $string)
    {
        $string = trim(strtolower($string));
        $string = preg_replace('/[^a-z0-9 -]+/', '', $string);
        $string = preg_replace('/ +/', ' ', $string);
        $string = str_replace(' ', '-', $string);
        return trim($string, '-');
    }

    // Vehicle Update

    public function viUpdate(): string
    {
        $input = $_POST;
        $id = $input['id'];
        $data['update'] = [
            'make' => $input['make'],
            'model' => $input['model'],
            'additional' => $input['additional'],
            'slug' => $input['slug'],
            'salePrice' => $input['salePrice'],
            'msrp' => $input['msrp'],
            'description' => $input['description'],
            'vehicleCondition' => $input['vehicleCondition'],
            'payloadCapacity' => $input['payloadCapacity'],
            'emptyWeight' => $input['emptyWeight'],
            'floorLength' => $input['floorLength'],
            'floorWidth' => $input['floorWidth'],
            'sideHeight' => $input['sideHeight'],
            'bodyType' => $input['bodyType'],
            'rvCategory' => $input['rvCategory'],
            'addtionalInfo' => $input['addtionalInfo'],
            'featuredImage' => $input['featuredImage'],
            'featuredid' => $input['featuredid'],
            'gallery' => $input['gallery'],
            'galleryfiles' => $input['galleryfiles'],
            'status' => $input['status'],
            'updatedBy' => $input['createdBy'],
            'updatedAt' => $input['createdAt']
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

    public function viDetails(string $id)
    {
        $result = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id));
        return $result;
    }

    public function viDelete(): string
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

}