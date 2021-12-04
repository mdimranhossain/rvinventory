<?php
/*
* Setting
* @Package: VehicleInventory
*/

declare(strict_types=1);

namespace Inc;

class Setting
{   private $db;
    private $table;
    private $imageTable;
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix.'inventory';
        $this->imageTable = $this->db->prefix.'inventory_images';
    }

    public function viUpdateInventoryOptions($data): string
    {
        $slug = strtolower($data['slug']);
        update_option('vi_slug', $slug);

        $pageTitle = $data['pageTitle'];
        update_option('vi_pageTitle', $pageTitle);

        $emailfriend = strtolower($data['emailfriend']);
        update_option('vi_emailfriend', $emailfriend);

        $availability = strtolower($data['availability']);
        update_option('vi_availability', $availability);

        $address = $data['address'];
        update_option('vi_address', $address);

        $phone = $data['phone'];
        update_option('vi_phone', $phone);

        $weekday = $data['weekday'];
        update_option('vi_weekday', $weekday);

        $saturday = $data['saturday'];
        update_option('vi_saturday', $saturday);

        $weekend = $data['weekend'];
        update_option('vi_weekend', $weekend);
        
        $data['slug'] = get_option('vi_slug');
        $data['pageTitle'] = get_option('vi_pageTitle');
        $data['emailfriend'] = get_option('vi_emailfriend');
        $data['availability'] = get_option('vi_availability');
        $data['address'] = get_option('vi_address');
        $data['phone'] = get_option('vi_phone');
        $data['weekday'] = get_option('vi_weekday');
        $data['saturday'] = get_option('vi_saturday');
        $data['weekend'] = get_option('vi_weekend');
        return json_encode($data);
    }

    public function viInventoryOptions(): array
    {
        $data['slug'] = get_option('vi_slug');
        $data['pageTitle'] = get_option('vi_pageTitle');
        $data['emailfriend'] = get_option('vi_emailfriend');
        $data['availability'] = get_option('vi_availability');
        $data['address'] = get_option('vi_address');
        $data['phone'] = get_option('vi_phone');
        $data['weekday'] = get_option('vi_weekday');
        $data['saturday'] = get_option('vi_saturday');
        $data['weekend'] = get_option('vi_weekend');
        return $data;
    }
}