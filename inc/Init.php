<?php
/*
* Init
* @Package: rvinventory
*/

declare(strict_types=1);

namespace Inc;

use Inc\Vehicle as Vehicle;

class Init
{
    public $plugin;
    private $viUrl;
    private $viPath;
    private $db;
    private $table;
    private $imageTable;
    private $vi_slug;
    private $vi_emailfriend;
    private $vi_availability;
    private $vi_contact_dealer;
    private $vi_address;
    private $vi_phone;
    private $vi_weekday;
    private $vi_saturday;
    private $vi_weekend;
    
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'inventory';
        $this->imageTable = $this->db->prefix.'inventory_images';
        $this->plugin = plugin_basename(__FILE__);
        $this->viUrl = plugins_url("", dirname(__FILE__));
        $this->viPath = dirname(__FILE__, 2);
        
        $this->vi_slug = !empty(get_option('vi_slug'))?get_option('vi_slug'):'inventory';
        $this->vi_emailfriend = !empty(get_option('vi_emailfriend'))?get_option('vi_emailfriend'):'';
        $this->vi_availability = !empty(get_option('vi_availability'))?get_option('vi_availability'):'';
        $this->vi_contact_dealer = !empty(get_option('vi_contact_dealer'))?get_option('vi_contact_dealer'):'/contact';
        $this->vi_address = !empty(get_option('vi_address'))?get_option('vi_address'):'';
        $this->vi_phone = !empty(get_option('vi_phone'))?get_option('vi_phone'):'';
        $this->vi_weekday = !empty(get_option('vi_weekday'))?get_option('vi_weekday'):'';
        $this->vi_saturday = !empty(get_option('vi_saturday'))?get_option('vi_saturday'):'';
        $this->vi_weekend = !empty(get_option('vi_weekend'))?get_option('vi_weekend'):'';

    }

    public function start()
    {
        add_action('admin_enqueue_scripts', [$this, 'viAdminAssets']);
        // $cats = ['rvs-for-sale-everett','class-c-rvs','campers','fifth-wheels','motorhomes','travel-trailers'];
        // foreach($cats as $cat){
        //     if (strpos($_SERVER['REQUEST_URI'], $cat) !== false){
        //     add_action('wp_enqueue_scripts', [$this, 'viAssets']);
        //     }
        // }
        add_action('wp_enqueue_scripts', [$this, 'viAssets']);
        add_action('admin_menu', [$this, 'viAddPage']);
        add_action('vivehicles', [$this, 'viVehicles']);
        add_action( 'wp_ajax_viDeleteAttachment', [$this, 'viDeleteAttachment']);
        
        add_action( 'wp_ajax_viUpload', [$this, 'viUpload']);

        add_filter( 'query_vars', function( $query_vars ){
            $query_vars[] = $this->vi_slug;
            return $query_vars;
        } );

        add_action( 'init',  function() {
            add_rewrite_rule('^'.$this->vi_slug.'/?([^/]*)/?', 'index.php?'.$this->vi_slug.'=$matches[1]', 'top');
        } );

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

        // ShortCode
        add_action( 'init', [$this, 'viShortcodes_init'] );

    }

    public function viShortcodes_init(){
        add_shortcode( 'inventory', [$this, 'viShortcode'] );
    }

    public function viShortcode( $cat = 'rvs-for-sale-everett') {
        $catpages = ['class-c-rvs'=>'Class C RVs','campers'=>'Campers','fifth-wheels'=>'Fifth Wheels','motorhomes'=>'Motorhomes','travel-trailers'=>'Travel Trailers'];
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
        // return output
        return $output;
    }


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

    public function viUpload()
    {
        ini_set('post_max_size', '5M'); //or bigger by multiple files
        ini_set('upload_max_filesize', '5M');
        ini_set('max_file_uploads', 1);

        $target_dir = wp_upload_dir();
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $chars = [' ',',','(',')'];
        $target_file = str_replace($chars, '', $target_file);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $data=[];
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if($check !== false) {
                $data['mime'] = "File is - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                $data['not_allowed'] = "File is not allowed.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $data['duplicate'] = "Sorry, file already exists. Please rename your file.";
            //$data['filename'] = $target_file.'-1';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["file"]["size"] > 500000) {
            $data['size_error'] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        $data['filetype'] = $imageFileType;
        // Allow certain file formats
        $allowed =['pdf','doc','docx','odt'];
        if(!in_array($imageFileType,$allowed)) {
            $data['type_error'] = "Sorry, only PDF and or Word files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $data['upload_error'] = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $data['success'] = "The file ". htmlspecialchars( basename($_FILES["file"]["name"])). " has been uploaded.";
                $data['dir'] = $target_dir;
                $data['file'] = wp_upload_dir().'/'.$target_file;
            } else {
                $data['file_error'] = "Sorry, there was an error uploading your file.";
            }
        }
        echo json_encode($data);
        die();
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
        global $vi_emailfriend;
        $vi_emailfriend = $this->vi_emailfriend;
        global $vi_availability;
        $vi_availability = $this->vi_availability;
        global $vi_contact_dealer;
        $vi_contact_dealer = $this->vi_contact_dealer;
        global $vi_address;
        $vi_address = $this->vi_address;
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
        add_option( 'vi_emailfriend', $vi_emailfriend );
        add_option( 'vi_availability', $vi_availability );
        add_option( 'vi_contact_dealer', $vi_contact_dealer );
        add_option( 'vi_address', $vi_address );
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