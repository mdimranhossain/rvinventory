<?php
/*
* Image
* @Package: rvinventory
*/

declare(strict_types=1);

namespace Inc;

class Image
{   
    private $db;
    private $table;
    private $imageTable;
    private $target;
    private $file;
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'inventory';
        $this->imageTable = $this->db->prefix.'inventory_images';
        if(!defined('WP_CONTENT_URL')){
            define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
        }
        $this->target = wp_upload_dir();       
    }

    // upload
    public function upload(): string
    {   
        $data['target'] = $this->target;
        $data['id'] = $_REQUEST['id'];
        $data['name'] = $this->sanitize($_FILES['featured']["name"]);
        $data['type'] = $this->mime($data['name']);
        $data['size'] = $_FILES['featured']["size"];
       
        if($data['type']['allowed']===1 && $data['size']>0){
            $data['file_path'] = $data['target']['path'].'/'.$data['name'];
            $data['mime'] = $data['type']['mime'];

            if(move_uploaded_file($_FILES["featured"]["tmp_name"], $data['file_path'])){
                $data['success'] = "The file ". htmlspecialchars( basename($data['name'])). " has been uploaded."; 
                $data['url'] = $data['target']['url'].'/'.$data['name'];
                // save to database
                $data['insert'] = [
                    'name' => $data['name'],
                    'mime' => $data['mime'],
                    'size' => $data['size'],
                    'url' => $data['url'],
                    'file_path' => $data['file_path'],
                    'inventory_id' => $data['id'],
                    'attachment' => 'featured',
                    'uploadedBy' => $_POST['createdBy'],
                    'uploadedAt' => $_POST['createdAt']
                ];

                $data['save'] = $this->save($data);
                $data['featuredid'] = $data['save']['insertid'];
            } else {
                $data['file_error'] = "Sorry, there was an error uploading your file.";
            }

            $data['update'] = $this->db->update($this->table,['featuredImage'=>$data['url'],'featuredid'=>$data['featuredid']],['id'=>$data['id']]);
            if(!empty($data['update'])){
                $data['message'] = "Image Updated!";
            }
        }

        return json_encode($data);
    }

    // sigle dragdrop
    public function singleDragdrop(): string
    {   
        $data['target'] = $this->target;
        $data['id'] = $_REQUEST['id'];
        if(isset($_FILES['file']['name'][0]))
        {
            $data['thumbs']='';
            foreach($_FILES['file']['name'] as $key => $value)
            {
                $data['name'] = $this->sanitize($_FILES['file']["name"][$key]);
                $data['type'] = $this->mime($data['name']);
                $data['size'] = $_FILES['file']["size"][$key];
                
                if($data['type']['allowed']===1 && $data['size']>0){
                    $data['file_path'] = $data['target']['path'].'/'.$data['name'];
                    $data['mime'] = $data['type']['mime'];
                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$key], $data['file_path'])) {
                        $data['success'] = "The file ". htmlspecialchars( basename($data['name'])). " has been uploaded."; 
                        $data['url'] = $data['target']['url'].'/'.$data['name'];
                        // save to database
                        $data['insert'] = [
                            'name' => $data['name'],
                            'mime' => $data['mime'],
                            'size' => $data['size'],
                            'url' => $data['url'],
                            'file_path' => $data['file_path'],
                            'inventory_id' => $data['id'],
                            'attachment' => 'featured',
                            'uploadedBy' => !empty($_POST['createdBy'])?$_POST['createdBy']:'',
                            'uploadedAt' => !empty($_POST['createdAt'])?$_POST['createdAt']:''
                        ];

                        $data['save'] = $this->save($data);
                        //$data['links'][] = $data['url'];
                        $data['featuredid'] = $data['save']['insertid'];
                        $data['thumbs'] .='<div class="thumbnail"><span class="btn btn-danger btn-xs pull-right fupdate" data-image_id="'.$data['save']['insertid'].'"><i class="fa fa-times"></i></span><img class="img-fluid" src="'.$data['url'].'" alt="" /></div>';

                    } else {
                        $data['file_error'] = "Sorry, there was an error uploading your file.";
                    }
                }

            } //end foreach

            $data['update'] = $this->db->update($this->table,['featuredImage'=>$data['url'],'featuredid'=>$data['featuredid']],['id'=>$data['id']]);
            if(!empty($data['update'])){
                $data['message'] = "Image Updated!";
            }
        }
        return json_encode($data);
    }

    // multiupload
    public function multiupload(): string
    {   
        $data['target'] = $this->target;
        $data['id'] = $_REQUEST['id'];
        $data['insert'] = [];
        if(isset($_FILES['file']['name'][0]))
        {
            $data['thumbs']='';
            foreach($_FILES['file']['name'] as $key => $value)
            {
                $data['name'] = $this->sanitize($_FILES['file']["name"][$key]);
                $data['type'] = $this->mime($data['name']);
                $data['size'] = $_FILES['file']["size"][$key];
                
                if($data['type']['allowed']===1 && $data['size']>0){
                    $data['file_path'] = $data['target']['path'].'/'.$data['name'];
                    $data['mime'] = $data['type']['mime'];
                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$key], $data['file_path'])) {
                        $data['success'] = "The file ". htmlspecialchars( basename($data['name'])). " has been uploaded."; 
                        $data['url'] = $data['target']['url'].'/'.$data['name'];
                        // save to database
                        $data['insert'] = [
                            'name' => $data['name'],
                            'mime' => $data['mime'],
                            'size' => $data['size'],
                            'url' => $data['url'],
                            'file_path' => $data['file_path'],
                            'inventory_id' => $data['id'],
                            'attachment' => 'gallery',
                            'uploadedBy' => !empty($_POST['createdBy'])?$_POST['createdBy']:'',
                            'uploadedAt' => !empty($_POST['createdAt'])?$_POST['createdAt']:''
                        ];

                        $data['save'] = $this->save($data);
                        $data['links'][] = $data['url'];
                        $data['fileids'][] = $data['save']['insertid'];
                        $data['thumbs'] .='<div class="thumbnail"><span class="btn btn-danger btn-xs pull-right delete" data-image_id="'.$data['save']['insertid'].'"><i class="fa fa-times"></i></span><img class="img-fluid" src="'.$data['url'].'" alt="" /></div>';

                    } else {
                        $data['file_error'] = "Sorry, there was an error uploading your file.";
                    }
                }

            } //end foreach

            $data['gallery'] = implode(',',$data['links']);
            $data['galleryfiles'] = implode(',',$data['fileids']);
            $data['existing'] = $this->db->get_row( $this->db->prepare( "SELECT * FROM $this->table WHERE id = %d", $data['id'] ) );
            if(!empty($data['existing']->gallery)){
                $data['gallery'] = $data['existing']->gallery.','.$data['gallery'];
            }

            if(!empty($data['existing']->galleryfiles)){
                $data['galleryfiles'] = $data['existing']->galleryfiles.','.$data['galleryfiles'];
            }

            $data['update_gallery'] = $this->db->update($this->table,['gallery'=>$data['gallery'],'galleryfiles'=>$data['galleryfiles']],['id'=>$data['id']]);
            if(!empty($data['update_gallery'])){
                $data['message'] = "Gallery Updated";
            }
            if(!empty($data['insert'])){
                $data['message'] = "Upload Succeed.";
            }else{
                $data['message'] = "Something wrong with file(s).";
            }
        }
        return json_encode($data);
    }

    //Sanitize file name
    public function sanitize($filename): string
    {
        $chars = [' ',',','(',')'];
        $name = str_replace($chars, '', $filename);
        $file = explode('.',$name);
        $data['query'] = "SELECT COUNT(*) FROM $this->imageTable WHERE name LIKE '%".$file[0]."%'";
        $data['count'] = $this->db->get_var($data['query']);

        if($data['count']>0){
            $name = $file[0].'_'.$data['count'].'.'.$file[1];
        }
        return $name;
    }

    //Check mime
    public function mime($filename): array
    {
        //$data['filename'] = $this->sanitize($filename);
        $data['mime'] = strtolower(pathinfo($filename,PATHINFO_EXTENSION));

        $allowed =['jpg','jpeg','png','gif'];
        $data['allowed'] = 1;
        if(!in_array($data['mime'],$allowed)) {
            $data['error'] = "Sorry, only jpg, jpeg, png and gif files are allowed.";
            $data['allowed'] = 0;
        }
        return $data;
    }

    // Save to database
    public function save($data): array
    {
        
        $format = array('%s','%s','%s','%s','%s','%s');

        $save['success'] = $this->db->insert($this->imageTable,$data['insert'],$format);

        if($save['success']){
            $save['message'] = "Image Saved Successfully.";
        }else{
            $save['error'] = "Something Went Wrong!!!".$this->db->last_error;
        }

        $save['insertid'] = $this->db->insert_id;
        return $save;
    }

    //Delete Image
    public function delete($id): string
    {
        $data['image'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->imageTable} WHERE id = %d", $id));
        $data['delete'] = $this->db->delete($this->imageTable, ['id' => $id]);
        if(!empty($data['image']->file_path) && file_exists($data['image']->file_path)){
            unlink($data['image']->file_path);
        }
        $data['message'] = '';

        if($data['delete']){
            $data['message'] = 'Image Deleted!!!';
        }

        return json_encode($data);
    }

    //Update Image Featured
    public function update($image_id,$inventory_id): string
    {
        $data['image'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->imageTable} WHERE id = %d", $image_id));

        $data['inventory'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table} WHERE id = %d", $inventory_id));
        
        // delete featured image
        $data['delete'] = $this->db->delete($this->imageTable, ['id' => $image_id]);
        if(!empty($data['image']->file_path) && file_exists($data['image']->file_path)){
            unlink($data['image']->file_path);
        }
      
        $data['update'] = $this->db->update($this->table,['featuredImage'=>'','featuredid'=>''],['id'=>$inventory_id]);
        
        $data['message'] = [];

        if($data['delete']){
            $data['message']['delete'] = 'Image Deleted!!!';
        }

        if($data['update']){
            $data['message']['update'] = 'Image Updated!!!';
        }

        return json_encode($data);
    }

    //Update Image Gallery
    public function updateGallery($image_id,$inventory_id): string
    {
        $data['message'] = [];

        $data['image'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->imageTable} WHERE id = %d", $image_id));

        $data['inventory'] = $this->db->get_row($this->db->prepare("SELECT * FROM {$this->table} WHERE id = %d", $inventory_id));
        // delete image
        $data['delete'] = $this->db->delete($this->imageTable, ['id' => $image_id]);
        
        if(!empty($data['image']->file_path) && file_exists($data['image']->file_path)){
            wp_delete_file($data['image']->file_path);
            $data['message']['image'] = "Image found and deleted.";
        }else{
            $data['message']['image'] = "Image not found.";
        }
        //rearrange links
        $links = rtrim($data['inventory']->gallery,',');
        $links = ltrim($links,',');
        $data['links'] = explode(',',$links);
        $data['link'] = $data['image']->url;
        $data['remaining'] = array_diff($data['links'], [$data['link']]);
        $data['gallery'] = implode(',',$data['remaining']);
        //rearragne image ids
        $fileids = rtrim($data['inventory']->galleryfiles,',');
        $fileids = ltrim($fileids,',');
        $data['fileids'] = explode(',',$fileids);
        $data['remains'] = array_diff( $data['fileids'], [$image_id] );
        $data['galleryfiles'] = implode(',',$data['remains']);

        $data['update'] = $this->db->update($this->table,['gallery'=>$data['gallery'],'galleryfiles'=>$data['galleryfiles']],['id'=>$inventory_id]);
        
        if($data['delete']){
            $data['message']['delete'] = 'Image Deleted!!!';
        }

        if($data['update']){
            $data['message']['update'] = 'Image Updated!!!';
        }

        return json_encode($data);
    }

}