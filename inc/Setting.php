<?php
/*
* Setting
* @Package: rvinventory
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

        $seoTitle = $data['seoTitle'];
        update_option('vi_seoTitle', $seoTitle);

        $seoDescription = $data['seoDescription'];
        update_option('vi_seoDescription', $seoDescription);

        $pageTitle = $data['pageTitle'];
        update_option('vi_pageTitle', $pageTitle);

        $emailfriend = strtolower($data['emailfriend']);
        update_option('vi_emailfriend', $emailfriend);

        $availability = strtolower($data['availability']);
        update_option('vi_availability', $availability);

        $contact_dealer = strtolower($data['contact_dealer']);
        update_option('vi_contact_dealer', $contact_dealer);

        $address = $data['address'];
        update_option('vi_address', $address);

        $city = $data['city'];
        update_option('vi_city', $city);

        $state = $data['state'];
        update_option('vi_state', $state);

        $zip = $data['zip'];
        update_option('vi_zip', $zip);

        $areas = $data['areas'];
        update_option('vi_areas', $areas);

        $phone = $data['phone'];
        update_option('vi_phone', $phone);

        $weekday = $data['weekday'];
        update_option('vi_weekday', $weekday);

        $saturday = $data['saturday'];
        update_option('vi_saturday', $saturday);

        $weekend = $data['weekend'];
        update_option('vi_weekend', $weekend);
        
        $data['slug'] = get_option('vi_slug');
        $data['seoTitle'] = stripslashes(get_option('vi_seoTitle'));
        $data['seoDescription'] = stripslashes(get_option('vi_seoDescription'));
        $data['pageTitle'] = get_option('vi_pageTitle');
        $data['emailfriend'] = get_option('vi_emailfriend');
        $data['availability'] = get_option('vi_availability');
        $data['contact_dealer'] = get_option('vi_contact_dealer');
        $data['address'] = get_option('vi_address');
        $data['city'] = get_option('vi_city');
        $data['state'] = get_option('vi_state');
        $data['zip'] = get_option('vi_zip');
        $data['areas'] = get_option('vi_areas');
        $data['phone'] = get_option('vi_phone');
        $data['weekday'] = get_option('vi_weekday');
        $data['saturday'] = get_option('vi_saturday');
        $data['weekend'] = get_option('vi_weekend');
        return json_encode($data);
    }

    public function viInventoryOptions(): array
    {
        $data['slug'] = get_option('vi_slug');
        $data['seoTitle'] = stripslashes(get_option('vi_seoTitle'));
        $data['seoDescription'] = stripslashes(get_option('vi_seoDescription'));
        $data['pageTitle'] = get_option('vi_pageTitle');
        $data['emailfriend'] = get_option('vi_emailfriend');
        $data['availability'] = get_option('vi_availability');
        $data['contact_dealer'] = get_option('vi_contact_dealer');
        $data['address'] = get_option('vi_address');
        $data['city'] = get_option('vi_city');
        $data['state'] = get_option('vi_state');
        $data['zip'] = get_option('vi_zip');
        $data['areas'] = get_option('vi_areas');
        $data['phone'] = get_option('vi_phone');
        $data['weekday'] = get_option('vi_weekday');
        $data['saturday'] = get_option('vi_saturday');
        $data['weekend'] = get_option('vi_weekend');
        return $data;
    }
}