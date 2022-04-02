<?php
/*
* Init
* @Package: rvinventory
*/

declare(strict_types=1);

namespace Inc;

use Inc\Vehicle as Vehicle;
use Inc\Image as Image;

class Init
{
    public $plugin;
    private $viUrl;
    private $viPath;
    private $db;
    private $table;
    private $imageTable;
    private $vi_slug;
    private $vi_seoTitle;
    private $vi_seoDescription;
    private $vi_emailfriend;
    private $vi_availability;
    private $vi_contact_dealer;
    private $vi_address;
    private $vi_city;
    private $vi_state;
    private $vi_zip;
    private $vi_areas;
    private $vi_phone;
    private $vi_weekday;
    private $vi_saturday;
    private $vi_weekend;
    
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'rvinventory';
        $this->imageTable = $this->db->prefix.'rvinventory_images';
        $this->plugin = plugin_basename(__FILE__);
        $this->viUrl = plugins_url("", dirname(__FILE__));
        $this->viPath = dirname(__FILE__, 2);
        
        $this->vi_slug = !empty(get_option('vi_slug'))?get_option('vi_slug'):'inventory';
        $this->vi_seoTitle = !empty(get_option('vi_seoTitle'))?get_option('vi_seoTitle'):'';
        $this->vi_seoDescription = !empty(get_option('vi_seoDescription'))?get_option('vi_seoDescription'):'';
        $this->vi_emailfriend = !empty(get_option('vi_emailfriend'))?get_option('vi_emailfriend'):'';
        $this->vi_availability = !empty(get_option('vi_availability'))?get_option('vi_availability'):'';
        $this->vi_contact_dealer = !empty(get_option('vi_contact_dealer'))?get_option('vi_contact_dealer'):'/contact';
        $this->vi_address = !empty(get_option('vi_address'))?get_option('vi_address'):'13425 Hwy 99';
        $this->vi_city = !empty(get_option('vi_city'))?get_option('vi_city'):'Everett';
        $this->vi_state = !empty(get_option('vi_state'))?get_option('vi_state'):'WA';
        $this->vi_zip = !empty(get_option('vi_zip'))?get_option('vi_zip'):'98204';
        $this->vi_areas = !empty(get_option('vi_areas'))?get_option('vi_areas'):'Bellevue, Seattle';
        $this->vi_phone = !empty(get_option('vi_phone'))?get_option('vi_phone'):'';
        $this->vi_weekday = !empty(get_option('vi_weekday'))?get_option('vi_weekday'):'';
        $this->vi_saturday = !empty(get_option('vi_saturday'))?get_option('vi_saturday'):'';
        $this->vi_weekend = !empty(get_option('vi_weekend'))?get_option('vi_weekend'):'';

    }

    public function start()
    {
        add_action('admin_enqueue_scripts', [$this, 'viAdminAssets']);

        add_action('wp_enqueue_scripts', [$this, 'viAssets']);
        add_action('admin_menu', [$this, 'viAddPage']);
        add_action('vivehicles', [$this, 'viVehicles']);
        add_action( 'wp_ajax_viDeleteAttachment', [$this, 'viDeleteAttachment']);
        
        add_action( 'wp_ajax_viUpload', [$this, 'viUpload']);
        add_action( 'wp_ajax_viSlug',[$this,'viSlug']);
        add_action( 'wp_ajax_viVehicle',[$this,'viVehicle']);
        add_action( 'wp_ajax_viDragDrop',[$this,'viDragDrop']);
        add_action( 'wp_ajax_viUpdateImage',[$this,'viUpdateImage']);
        add_action( 'wp_ajax_viUpdateGallery',[$this,'viUpdateGallery']);
        add_action( 'wp_ajax_viSingleDragDrop',[$this,'viSingleDragDrop']);
        add_action( 'wp_ajax_viDeleteImage',[$this,'viDeleteImage']);

        add_filter( 'query_vars', function( $query_vars ){
            $query_vars[] = $this->vi_slug;
            return $query_vars;
        } );

        add_action( 'init',  function() {
            add_rewrite_rule('^'.$this->vi_slug.'/?([^/]*)/?', 'index.php?'.$this->vi_slug.'=$matches[1]', 'top');
        } );

        // add inventory links to aioseo xml sitemap
        add_filter( 'aioseo_sitemap_additional_pages', [$this,'viLinks'] );

        // disable aioseo
        add_filter( 'aioseo_disable', [$this,'viDisableMeta'] );
        
        // add seo title
        add_filter('pre_get_document_title',  [$this,'viPageTitle'], 9999);


        add_action('template_redirect', function(){
            $inventory = get_query_var( $this->vi_slug );
            if (strpos($_SERVER['REQUEST_URI'], $this->vi_slug) !== false && empty($inventory)){
                include $this->viPath . '/template/inventory.php';
                die;
            }elseif($inventory){
                include $this->viPath . '/template/details.php';
                die;
            }
            
        } );

        // init ShortCode
        add_action( 'init', [$this, 'viShortcodes_init'] );

        // assign meta tags
        add_action( 'wp_head', [$this,'viAddMetaTags']);

        // add body class
        add_filter( 'admin_body_class', [$this,'viAdminBodyClass'] );
        
    }

    public function viAdminBodyClass( $classes ) {
        global $current_user;
        foreach( $current_user->roles as $role )
            $classes .= ' role-' . $role;
        return trim( $classes );
    }

    // sitemap links
    public function viLinks($sitemap) {
        $vehicles = new Vehicle();
        $sitemap = $vehicles->viSitemap();
        return $sitemap;
    }

    // disable aioseo
    public function viDisableMeta($disabled) {
        $inventory = get_query_var( $this->vi_slug );
        if (strpos($_SERVER['REQUEST_URI'], $this->vi_slug)) {
            return true;
        }
        return false;
    }

    // seo data
    public function viPageTitle(){
        $inventory = get_query_var( $this->vi_slug );
        $seoTitle = !empty(get_option('vi_seoTitle'))?stripslashes(get_option('vi_seoTitle')):'';
        if (strpos($_SERVER['REQUEST_URI'], $this->vi_slug) !== false && empty($inventory)){
            return $seoTitle;
        }elseif($inventory){
            $vehicles = new Vehicle();
            $vehicle = $vehicles->viDetails($inventory);
            $title = $vehicle->rvCategory.' '.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.' For Sale '.$this->vi_city.', '.$this->vi_areas;
            return $title;
        }
    }

    public function viAddMetaTags(){
        $inventory = get_query_var( $this->vi_slug );
        $slug = get_option('vi_slug');
        $home = get_option('home');
        $blogname = get_option('blogname');
        $blogdescription = get_option('blogdescription');
        $seoTitle = !empty(get_option('vi_seoTitle'))?stripslashes(get_option('vi_seoTitle')):'';
        $seoDescription = !empty(get_option('vi_seoDescription'))?stripslashes(get_option('vi_seoDescription')):'';
        $vehicles = new Vehicle();
        $vehicle = $vehicles->viDetails($inventory);

        if (strpos($_SERVER['REQUEST_URI'], $this->vi_slug) !== false && empty($inventory)){
            $meta = '<meta name="description" content="'.$seoDescription.'" />';
            $meta .= '<meta property="og:site_name" content="'.$blogname.' - '.$blogdescription.'" />
            <meta property="og:type" content="article" />
            <meta property="og:title" content="'.$seoTitle.'" />
            <meta property="og:description" content="'.$seoDescription.'" />
            <meta property="og:url" content="'.$home.'/'.$slug.'/" />';
            
            echo $meta;
        }elseif($inventory){
            $title = $vehicle->rvCategory.' '.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.' For Sale '.$this->vi_city.', '.$this->vi_areas;
            $details = "Check out this great looking ".$vehicle->rvCategory." ".$vehicle->year.", ".$vehicle->make." ".$vehicle->model." for sale at ".$blogname." in ".$this->vi_city.", ".$this->vi_state." serving the greater ".$this->vi_areas." area.";
            $meta = '<meta name="description" content="'.$details.'" />';
            $meta .= '<meta property="og:site_name" content="'.$blogname.' - '.$blogdescription.'" />
            <meta property="og:type" content="article" />
            <meta property="og:title" content="'.$title.'" />
            <meta property="og:description" content="'.$details.'" />
            <meta property="og:url" content="'.$home.'/'.$slug.'/'.$vehicle->slug.'" />';
            
            echo $meta;
        }
    }
    

    // generate shortcode
    public function viShortcodes_init(){
        add_shortcode( 'inventory', [$this, 'viShortcode'] );
    }

    public function viShortcode( $cat = 'rvs-for-sale-everett') {
        $catpages = ['class-a-b-c-rvs'=>'Class A, B & C RVs','campers'=>'Campers','fifth-wheels'=>'Fifth Wheels','toy-hauler-rvs-for-sale'=>'Toy Hauler','travel-trailers'=>'Travel Trailers'];
        $atts = shortcode_atts( array(
            'rvcat' => $cat
        ), $cat, 'inventory' );
        $vehicles = new Vehicle();
        $rvcat = $vehicles->viBuildSlug($atts['rvcat']);

        $category = $rvcat;
        if (array_key_exists($rvcat,$catpages)){
            $category = $catpages[$rvcat];
        }
       
        $output = $vehicles->viShortcodeList($category);

        return $output;
    }

    // admin pages
    public function viAddPage()
    {
        add_menu_page(
            'Inventory',
            'Inventory',
            'read',
            'vivehicles',
            [$this, "viVehicles"],
            '',
            71
        );
        add_submenu_page(
            'vivehicles',
            'Add New',
            'Add New',
            'manage_options',
            'viaddnew',
            [$this, "viAddNew"],
            71
        );
        add_submenu_page(
            '',
            '',
            '',
            'manage_options',
            'viedit',
            [$this, "viEdit"],
            71
        );
        add_submenu_page(
            'vivehicles',
            'Settings',
            'Settings',
            'manage_options',
            'visettings',
            [$this, "viSettings"],
            71
        );
    }

    public function viAdminAssets(string $hook)
    {
        wp_enqueue_script('jqueryui_js', $this->viUrl . '/assets/jqueryui/jquery-ui.min.js');
        wp_enqueue_style('dt_bs_css', $this->viUrl . '/assets/datatables.min.css');
        wp_enqueue_script('dt_bs_js', $this->viUrl . '/assets/datatables.min.js');
        wp_enqueue_style('jqueryui_css', $this->viUrl . '/assets/jqueryui/jquery-ui.min.css');
        wp_enqueue_style('fontawesome', $this->viUrl.'/assets/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('vi-styles', $this->viUrl . '/assets/vi-styles.css');
    }

    public function viAssets(string $hook)
    {
        wp_enqueue_style('fontawesome', $this->viUrl.'/assets/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('bootstrap-css', $this->viUrl.'/assets/Bootstrap-4-4.1.1/css/bootstrap.min.css');
        wp_enqueue_style('fancybox-css', $this->viUrl.'/assets/fancybox/jquery.fancybox.min.css');
        wp_enqueue_style('slider-css', $this->viUrl.'/assets/slider/slider.min.css');
        wp_enqueue_script('jquery-js', $this->viUrl.'/assets/jquery.min.js', array(), false, false);
        wp_enqueue_script('fancybox-js', $this->viUrl.'/assets/fancybox/jquery.fancybox.min.js', array(), false, true);
        wp_enqueue_script('slider-js', $this->viUrl.'/assets/slider/slider.min.js', array(), false, true);
        wp_enqueue_script('bootstrap-js', $this->viUrl.'/assets/Bootstrap-4-4.1.1/js/bootstrap.min.js', array(), false, true);
        wp_enqueue_script('scripts-js', $this->viUrl.'/assets/scripts.js', array(), false, true);
        wp_enqueue_style('styles', $this->viUrl.'/assets/styles.css');
    }
    
    public function viDeleteAttachment() {
        $data['id'] = $_REQUEST['post_id'];
        $data['galleryfiles'] = $_REQUEST['galleryfiles'];
        $data['featuredid'] = $_REQUEST['featuredid'];
        $data['delete'] = [];
        $data['message'] = [];
        $data['files'] = [];

        if(!empty($data['galleryfiles'])){
            $data['files'] = explode(',',$data['galleryfiles']);
            foreach($data['files'] as $image_id){
                $data['gallery'][$image_id] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->imageTable} WHERE id = %d", $image_id));
                $data['delete']['gallery'][$image_id] = $this->db->delete($this->imageTable, ['id' => $image_id]);
                if(!empty($data['gallery'][$image_id]->file_path) && file_exists($data['gallery'][$image_id]->file_path)){
                    unlink($data['gallery'][$image_id]->file_path);
                }

                if(!empty($data['delete']['gallery'][$image_id])){
                    $data['message']['gallery'][$image_id]= 'Gallery Image(s) Deleted';
                }
            }
                  
        }

        if(!empty($data['featuredid'])){
           // $data['files'] = explode(',',$data['galleryfiles']);
           $data['featured'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->imageTable} WHERE id = %d", $data['featuredid']));
           $data['delete']['featured'] = $this->db->delete($this->imageTable, ['id' => $data['featuredid']]);
           if(!empty($data['featured']->file_path) && file_exists($data['featured']->file_path)){
               unlink($data['featured']->file_path);
           }
            if(!empty($data['delete']['featured'])){
                $data['message']['featured']= 'Featured Image Deleted';
            }      
        }

        $data['delete']['inventory'] = $this->db->delete($this->table, ['id' => $data['id']]);

        if(!empty($data['delete']['inventory'])){
            $data['message']['inventory']= 'Inventory Deleted';
        }

        echo json_encode($data);
        die();
    }

    public function viSlug(){
        $vehicle = new Vehicle();
        echo $vehicle->viSlug();
        wp_die();
    }
    public function viVehicle(){
        $vehicle = new Vehicle();
        $handle = '';
        if(!empty($_REQUEST['vehicle'])){
            $handle = $_REQUEST['vehicle'];
        }

        switch($handle){
            case 'create':
                echo $vehicle->viCreate();
                break;
            case 'update':
            echo $vehicle->viUpdate();
                break;
            case 'delete':
                $vehicle->viDelete();
                break;
            default:
                echo $vehicle->viList();
        }
        wp_die();
    }

    public function viUpload(){
        $image = new Image(); 
        if(!empty($_POST)){
            echo $image->upload();
        }else{
            echo "No file found!";
        }
        wp_die();
    }

    public function viDragDrop(){
        $image = new Image(); 
        echo !empty($_FILES['file'])?$image->multiupload():"No file found!";
        wp_die();
    }

    public function viUpdateImage(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id']) && !empty($_REQUEST['id'])){
            echo $image->update($_REQUEST['image_id'],$_REQUEST['id']);
         }
         wp_die();
    }

    public function viSingleDragDrop(){
        $image = new Image(); 
        if(!empty($_FILES['file'])){
            echo $image->singleDragdrop();
        }else{
            echo "No file found!";
        }
        wp_die();
    }

    public function viUpdateGallery(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id']) && !empty($_REQUEST['id'])){
            echo $image->updateGallery($_REQUEST['image_id'],$_REQUEST['id']);
        }
        wp_die();
    }

    public function viDeleteImage(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id'])){
            echo $image->delete($_REQUEST['image_id']);
        }
        wp_die();
    }

    public function viVehicles()
    {
        include_once($this->viPath . '/template/vivehicles.php');
    }

    public function viAddNew()
    {
        include_once($this->viPath . '/template/viaddnew.php');
    }
    
    public function viEdit()
    {
        include_once($this->viPath . '/template/viedit.php');
    }

    public function viSettings()
    {
        include_once($this->viPath . '/template/visettings.php');
    }
    public function viSettingLink($links)
    {
        $setting_link['visettings'] = '<a href="admin.php?page=visettings">Settings</a>';
        return array_merge($links,$setting_link);
        //return $links;
    }
    
    public function viActivate()
    {
        global $vi_db_version;
        $vi_db_version = '1.0';
        global $vi_slug;
        $vi_slug = $this->vi_slug;
        global $vi_seoTitle;
        $vi_seoTitle = $this->vi_seoTitle;
        global $vi_seoDescription;
        $vi_seoDescription = $this->vi_seoDescription;
        global $vi_emailfriend;
        $vi_emailfriend = $this->vi_emailfriend;
        global $vi_availability;
        $vi_availability = $this->vi_availability;
        global $vi_contact_dealer;
        $vi_contact_dealer = $this->vi_contact_dealer;
        global $vi_address;
        $vi_address = $this->vi_address;
        global $vi_city;
        $vi_city = $this->vi_city;
        global $vi_state;
        $vi_state = $this->vi_state;
        global $vi_zip;
        $vi_zip = $this->vi_zip;
        global $vi_areas;
        $vi_areas = $this->vi_areas;
        global $vi_phone;
        $vi_phone = $this->vi_phone;
        global $vi_weekday;
        $vi_weekday = $this->vi_weekday;
        global $vi_saturday;
        $vi_saturday = $this->vi_saturday;
        global $vi_weekend;
        $vi_weekend = $this->vi_weekend;

        $table = $this->table;
        $imageTable = $this->imageTable;
        include_once($this->viPath . '/inc/Database.php');
        $this->db->query($create_table);
        $this->db->query($create_imageTable);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta($create_table);
        dbDelta($create_imageTable);

	    add_option( 'vi_db_version', $vi_db_version );

        add_option( 'vi_slug', $vi_slug );
        add_option( 'vi_seoTitle', $vi_seoTitle );
        add_option( 'vi_seoDescription', $vi_seoDescription );
        add_option( 'vi_emailfriend', $vi_emailfriend );
        add_option( 'vi_availability', $vi_availability );
        add_option( 'vi_contact_dealer', $vi_contact_dealer );
        add_option( 'vi_address', $vi_address );
        add_option( 'vi_city', $vi_city );
        add_option( 'vi_state', $vi_state );
        add_option( 'vi_zip', $vi_zip );
        add_option( 'vi_areas', $vi_areas );
        add_option( 'vi_phone', $vi_phone );
        add_option( 'vi_weekday', $vi_weekday );
        add_option( 'vi_saturday', $vi_saturday );
        add_option( 'vi_weekend', $vi_weekend );
    }

    public function viDeactivate()
    {
        //Nothing to do here this case
    }

    public static function viUninstall()
    {
        // Nothing to trigger here for this plugins
    }
}