<?php
/*
* Vehicle
* @Package: VehicleInventory
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

    public function viList(): string
    {
        $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE %d", 1);
        $vehicles = $this->db->get_results($query);
        return json_encode($vehicles);
    }

    public function viTotal(): int
    {
        $total = $this->db->get_var($this->db->prepare("SELECT COUNT('id') FROM {$table} WHERE %d", 1));
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
            $data['slug'] = str_replace(' ', '-', $data['make']);
        }
        if(!empty($_REQUEST['model'])){
            $data['model'] = trim($_REQUEST['model']);
            $data['slug'] .= '-'.str_replace(' ', '-', $data['model']);
        }
        if(!empty($_REQUEST['additional'])){
            $data['additional'] = trim($_REQUEST['additional']);
            $data['slug'] .= '-'.str_replace(' ', '-', $data['additional']);
        }

       $data['count'] = $this->db->get_results("SELECT * FROM ".$this->table." WHERE slug LIKE '%".$data['slug']."%'");

        if(count($data['count'])>0){
            $data['slug'] = $data['slug'].count($data['count'])+1;
        }
        $data['slug'] = strtolower($data['slug']);
       return json_encode($data);
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