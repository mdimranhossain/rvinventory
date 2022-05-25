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
    private $rvUrl;
    private $rvPath;
    private $db;
    private $table;
    private $imageTable;
    private $rv_slug;
    private $rv_seoTitle;
    private $rv_seoDescription;
    private $rv_emailfriend;
    private $rv_availability;
    private $rv_contact_dealer;
    private $rv_address;
    private $rv_city;
    private $rv_state;
    private $rv_zip;
    private $rv_areas;
    private $rv_phone;
    private $rv_weekday;
    private $rv_saturday;
    private $rv_weekend;
    
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'rvinventory';
        $this->imageTable = $this->db->prefix.'rvinventory_images';
        $this->plugin = plugin_basename(__FILE__);
        $this->rvUrl = plugins_url("", dirname(__FILE__));
        $this->rvPath = dirname(__FILE__, 2);
        
        $this->rv_slug = !empty(get_option('rv_slug'))?get_option('rv_slug'):'inventory';
        $this->rv_seoTitle = !empty(get_option('rv_seoTitle'))?get_option('rv_seoTitle'):'';
        $this->rv_seoDescription = !empty(get_option('rv_seoDescription'))?get_option('rv_seoDescription'):'';
        $this->rv_emailfriend = !empty(get_option('rv_emailfriend'))?get_option('rv_emailfriend'):'';
        $this->rv_availability = !empty(get_option('rv_availability'))?get_option('rv_availability'):'';
        $this->rv_contact_dealer = !empty(get_option('rv_contact_dealer'))?get_option('rv_contact_dealer'):'/contact';
        $this->rv_address = !empty(get_option('rv_address'))?get_option('rv_address'):'13425 Hwy 99';
        $this->rv_city = !empty(get_option('rv_city'))?get_option('rv_city'):'Everett';
        $this->rv_state = !empty(get_option('rv_state'))?get_option('rv_state'):'WA';
        $this->rv_zip = !empty(get_option('rv_zip'))?get_option('rv_zip'):'98204';
        $this->rv_areas = !empty(get_option('rv_areas'))?get_option('rv_areas'):'Bellevue, Seattle';
        $this->rv_phone = !empty(get_option('rv_phone'))?get_option('rv_phone'):'';
        $this->rv_weekday = !empty(get_option('rv_weekday'))?get_option('rv_weekday'):'';
        $this->rv_saturday = !empty(get_option('rv_saturday'))?get_option('rv_saturday'):'';
        $this->rv_weekend = !empty(get_option('rv_weekend'))?get_option('rv_weekend'):'';

    }

    public function start()
    {
        add_action('admin_enqueue_scripts', [$this, 'rvAdminAssets']);

        add_action('wp_enqueue_scripts', [$this, 'rvAssets']);
        add_action('admin_menu', [$this, 'rvAddPage']);
        add_action('rvvehicles', [$this, 'rvVehicles']);
        add_action( 'wp_ajax_rvDeleteAttachment', [$this, 'rvDeleteAttachment']);
        
        add_action( 'wp_ajax_rvUpload', [$this, 'rvUpload']);
        add_action( 'wp_ajax_rvSlug',[$this,'rvSlug']);
        add_action( 'wp_ajax_rvVehicle',[$this,'rvVehicle']);
        add_action( 'wp_ajax_rvDragDrop',[$this,'rvDragDrop']);
        add_action( 'wp_ajax_rvUpdateImage',[$this,'rvUpdateImage']);
        add_action( 'wp_ajax_rvUpdateGallery',[$this,'rvUpdateGallery']);
        add_action( 'wp_ajax_rvSingleDragDrop',[$this,'rvSingleDragDrop']);
        add_action( 'wp_ajax_rvDeleteImage',[$this,'rvDeleteImage']);

        add_filter( 'query_vars', function( $query_vars ){
            $query_vars[] = $this->rv_slug;
            return $query_vars;
        } );

        add_action( 'init',  function() {
            add_rewrite_rule('^'.$this->rv_slug.'/?([^/]*)/?', 'index.php?'.$this->rv_slug.'=$matches[1]', 'top');
        } );

        // add inventory links to aioseo xml sitemap
        add_filter( 'aioseo_sitemap_additional_pages', [$this,'rvLinks'] );

        // disable aioseo
        add_filter( 'aioseo_disable', [$this,'rvDisableMeta'] );
        
        // add seo title
        add_filter('pre_get_document_title',  [$this,'rvPageTitle'], 9999);


        add_action('template_redirect', function(){
            $inventory = get_query_var( $this->rv_slug );
            if (strpos($_SERVER['REQUEST_URI'], $this->rv_slug) !== false && empty($inventory)){
                include $this->rvPath . '/template/inventory.php';
                die;
            }elseif($inventory){
                include $this->rvPath . '/template/details.php';
                die;
            }
            
        } );

        // init ShortCode
        add_action( 'init', [$this, 'rvShortcodes_init'] );

        // assign meta tags
        add_action( 'wp_head', [$this,'rvAddMetaTags']);

        // add body class
        add_filter( 'admin_body_class', [$this,'rvAdminBodyClass'] );
        
    }

    public function rvAdminBodyClass( $classes ) {
        global $current_user;
        foreach( $current_user->roles as $role )
            $classes .= ' role-' . $role;
        return trim( $classes );
    }

    // sitemap links
    public function rvLinks($sitemap) {
        $vehicles = new Vehicle();
        $sitemap = $vehicles->rvSitemap();
        return $sitemap;
    }

    // disable aioseo
    public function rvDisableMeta($disabled) {
        $inventory = get_query_var( $this->rv_slug );
        if (strpos($_SERVER['REQUEST_URI'], $this->rv_slug)) {
            return true;
        }
        return false;
    }

    // seo data
    public function rvPageTitle(){
        $inventory = get_query_var( $this->rv_slug );
        $seoTitle = !empty(get_option('rv_seoTitle'))?stripslashes(get_option('rv_seoTitle')):'';
        if (strpos($_SERVER['REQUEST_URI'], $this->rv_slug) !== false && empty($inventory)){
            return $seoTitle;
        }elseif($inventory){
            $vehicles = new Vehicle();
            $vehicle = $vehicles->rvDetails($inventory);
            $title = $vehicle->rvCategory.' '.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.' For Sale '.$this->rv_city.', '.$this->rv_areas;
            return $title;
        }
    }

    public function rvAddMetaTags(){
        $inventory = get_query_var( $this->rv_slug );
        $slug = get_option('rv_slug');
        $home = get_option('home');
        $blogname = get_option('blogname');
        $blogdescription = get_option('blogdescription');
        $seoTitle = !empty(get_option('rv_seoTitle'))?stripslashes(get_option('rv_seoTitle')):'';
        $seoDescription = !empty(get_option('rv_seoDescription'))?stripslashes(get_option('rv_seoDescription')):'';
        $vehicles = new Vehicle();
        $vehicle = $vehicles->rvDetails($inventory);

        if (strpos($_SERVER['REQUEST_URI'], $this->rv_slug) !== false && empty($inventory)){
            $meta = '<meta name="description" content="'.$seoDescription.'" />';
            $meta .= '<meta property="og:site_name" content="'.$blogname.' - '.$blogdescription.'" />
            <meta property="og:type" content="article" />
            <meta property="og:title" content="'.$seoTitle.'" />
            <meta property="og:description" content="'.$seoDescription.'" />
            <meta property="og:url" content="'.$home.'/'.$slug.'/" />';
            
            echo $meta;
        }elseif($inventory){
            $title = $vehicle->rvCategory.' '.$vehicle->year.' '.$vehicle->make.' '.$vehicle->model.' For Sale '.$this->rv_city.', '.$this->rv_areas;
            $details = "Check out this great looking ".$vehicle->rvCategory." ".$vehicle->year.", ".$vehicle->make." ".$vehicle->model." for sale at ".$blogname." in ".$this->rv_city.", ".$this->rv_state." serving the greater ".$this->rv_areas." area.";
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
    public function rvShortcodes_init(){
        add_shortcode( 'inventory', [$this, 'rvShortcode'] );
    }

    public function rvShortcode( $cat = 'rvs-for-sale-everett') {
        $catpages = ['class-a-b-c-rvs'=>'Class A, B & C RVs','campers'=>'Campers','fifth-wheels'=>'Fifth Wheels','toy-hauler-rvs-for-sale'=>'Toy Hauler','travel-trailers'=>'Travel Trailers'];
        $atts = shortcode_atts( array(
            'rvcat' => $cat
        ), $cat, 'inventory' );
        $vehicles = new Vehicle();
        $rvcat = $vehicles->rvBuildSlug($atts['rvcat']);

        $category = $rvcat;
        if (array_key_exists($rvcat,$catpages)){
            $category = $catpages[$rvcat];
        }
       
        $output = $vehicles->rvShortcodeList($category);

        return $output;
    }

    // admin pages
    public function rvAddPage()
    {
        add_menu_page(
            'Inventory',
            'Inventory',
            'read',
            'rvvehicles',
            [$this, "rvVehicles"],
            '',
            71
        );
        add_submenu_page(
            'rvvehicles',
            'Add New',
            'Add New',
            'manage_options',
            'rvaddnew',
            [$this, "rvAddNew"],
            71
        );
        add_submenu_page(
            '',
            '',
            '',
            'manage_options',
            'rvedit',
            [$this, "rvEdit"],
            71
        );
        add_submenu_page(
            'rvvehicles',
            'Settings',
            'Settings',
            'manage_options',
            'rvsettings',
            [$this, "rvSettings"],
            71
        );
    }

    public function rvAdminAssets(string $hook)
    {
        wp_enqueue_script('jqueryui_js', $this->rvUrl . '/assets/jqueryui/jquery-ui.min.js');
        wp_enqueue_style('dt_bs_css', $this->rvUrl . '/assets/datatables.min.css');
        wp_enqueue_script('dt_bs_js', $this->rvUrl . '/assets/datatables.min.js');
        wp_enqueue_style('jqueryui_css', $this->rvUrl . '/assets/jqueryui/jquery-ui.min.css');
        wp_enqueue_style('fontawesome', $this->rvUrl.'/assets/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('rv-styles', $this->rvUrl . '/assets/rv-styles.css');
    }

    public function rvAssets(string $hook)
    {
        wp_enqueue_style('fontawesome', $this->rvUrl.'/assets/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('bootstrap-css', $this->rvUrl.'/assets/Bootstrap-4-4.1.1/css/bootstrap.min.css');
        wp_enqueue_style('fancybox-css', $this->rvUrl.'/assets/fancybox/jquery.fancybox.min.css');
        wp_enqueue_style('slider-css', $this->rvUrl.'/assets/slider/slider.min.css');
        wp_enqueue_script('jquery-js', $this->rvUrl.'/assets/jquery.min.js', array(), false, false);
        wp_enqueue_script('fancybox-js', $this->rvUrl.'/assets/fancybox/jquery.fancybox.min.js', array(), false, true);
        wp_enqueue_script('slider-js', $this->rvUrl.'/assets/slider/slider.min.js', array(), false, true);
        wp_enqueue_script('bootstrap-js', $this->rvUrl.'/assets/Bootstrap-4-4.1.1/js/bootstrap.min.js', array(), false, true);
        wp_enqueue_script('scripts-js', $this->rvUrl.'/assets/scripts.js', array(), false, true);
        wp_enqueue_style('styles', $this->rvUrl.'/assets/styles.css');
    }
    
    public function rvDeleteAttachment() {
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

    public function rvSlug(){
        $vehicle = new Vehicle();
        echo $vehicle->rvSlug();
        wp_die();
    }
    public function rvVehicle(){
        $vehicle = new Vehicle();
        $handle = '';
        if(!empty($_REQUEST['vehicle'])){
            $handle = $_REQUEST['vehicle'];
        }

        switch($handle){
            case 'create':
                echo $vehicle->rvCreate();
                break;
            case 'update':
            echo $vehicle->rvUpdate();
                break;
            case 'delete':
                $vehicle->rvDelete();
                break;
            default:
                echo $vehicle->rvList();
        }
        wp_die();
    }

    public function rvUpload(){
        $image = new Image(); 
        if(!empty($_POST)){
            echo $image->upload();
        }else{
            echo "No file found!";
        }
        wp_die();
    }

    public function rvDragDrop(){
        $image = new Image(); 
        echo !empty($_FILES['file'])?$image->multiupload():"No file found!";
        wp_die();
    }

    public function rvUpdateImage(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id']) && !empty($_REQUEST['id'])){
            echo $image->update($_REQUEST['image_id'],$_REQUEST['id']);
         }
         wp_die();
    }

    public function rvSingleDragDrop(){
        $image = new Image(); 
        if(!empty($_FILES['file'])){
            echo $image->singleDragdrop();
        }else{
            echo "No file found!";
        }
        wp_die();
    }

    public function rvUpdateGallery(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id']) && !empty($_REQUEST['id'])){
            echo $image->updateGallery($_REQUEST['image_id'],$_REQUEST['id']);
        }
        wp_die();
    }

    public function rvDeleteImage(){
        $image = new Image(); 
        if(!empty($_REQUEST['image_id'])){
            echo $image->delete($_REQUEST['image_id']);
        }
        wp_die();
    }

    public function rvVehicles()
    {
        include_once($this->rvPath . '/template/rvvehicles.php');
    }

    public function rvAddNew()
    {
        include_once($this->rvPath . '/template/rvaddnew.php');
    }
    
    public function rvEdit()
    {
        include_once($this->rvPath . '/template/rvedit.php');
    }

    public function rvSettings()
    {
        include_once($this->rvPath . '/template/rvsettings.php');
    }
    public function rvSettingLink($links)
    {
        $setting_link['rvsettings'] = '<a href="admin.php?page=rvsettings">Settings</a>';
        return array_merge($links,$setting_link);
        //return $links;
    }
    
    public function rvActivate()
    {
        global $rv_db_version;
        $rv_db_version = '1.0';
        global $rv_slug;
        $rv_slug = $this->rv_slug;
        global $rv_seoTitle;
        $rv_seoTitle = $this->rv_seoTitle;
        global $rv_seoDescription;
        $rv_seoDescription = $this->rv_seoDescription;
        global $rv_emailfriend;
        $rv_emailfriend = $this->rv_emailfriend;
        global $rv_availability;
        $rv_availability = $this->rv_availability;
        global $rv_contact_dealer;
        $rv_contact_dealer = $this->rv_contact_dealer;
        global $rv_address;
        $rv_address = $this->rv_address;
        global $rv_city;
        $rv_city = $this->rv_city;
        global $rv_state;
        $rv_state = $this->rv_state;
        global $rv_zip;
        $rv_zip = $this->rv_zip;
        global $rv_areas;
        $rv_areas = $this->rv_areas;
        global $rv_phone;
        $rv_phone = $this->rv_phone;
        global $rv_weekday;
        $rv_weekday = $this->rv_weekday;
        global $rv_saturday;
        $rv_saturday = $this->rv_saturday;
        global $rv_weekend;
        $rv_weekend = $this->rv_weekend;

        $table = $this->table;
        $imageTable = $this->imageTable;
        include_once($this->rvPath . '/inc/Database.php');
        $this->db->query($create_table);
        $this->db->query($create_imageTable);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta($create_table);
        dbDelta($create_imageTable);

	    add_option( 'rv_db_version', $rv_db_version );

        add_option( 'rv_slug', $rv_slug );
        add_option( 'rv_seoTitle', $rv_seoTitle );
        add_option( 'rv_seoDescription', $rv_seoDescription );
        add_option( 'rv_emailfriend', $rv_emailfriend );
        add_option( 'rv_availability', $rv_availability );
        add_option( 'rv_contact_dealer', $rv_contact_dealer );
        add_option( 'rv_address', $rv_address );
        add_option( 'rv_city', $rv_city );
        add_option( 'rv_state', $rv_state );
        add_option( 'rv_zip', $rv_zip );
        add_option( 'rv_areas', $rv_areas );
        add_option( 'rv_phone', $rv_phone );
        add_option( 'rv_weekday', $rv_weekday );
        add_option( 'rv_saturday', $rv_saturday );
        add_option( 'rv_weekend', $rv_weekend );
    }

    public function rvDeactivate()
    {
        //Nothing to do here this case
    }

    public static function rvUninstall()
    {
        // Nothing to trigger here for this plugins
    }
}