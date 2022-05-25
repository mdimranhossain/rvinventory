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

    public function rvUpdateInventoryOptions($data): string
    {
        $slug = strtolower($data['slug']);
        update_option('rv_slug', $slug);

        $seoTitle = $data['seoTitle'];
        update_option('rv_seoTitle', $seoTitle);

        $seoDescription = $data['seoDescription'];
        update_option('rv_seoDescription', $seoDescription);

        $pageTitle = $data['pageTitle'];
        update_option('rv_pageTitle', $pageTitle);

        $emailfriend = strtolower($data['emailfriend']);
        update_option('rv_emailfriend', $emailfriend);

        $availability = strtolower($data['availability']);
        update_option('rv_availability', $availability);

        $contact_dealer = strtolower($data['contact_dealer']);
        update_option('rv_contact_dealer', $contact_dealer);

        $address = $data['address'];
        update_option('rv_address', $address);

        $city = $data['city'];
        update_option('rv_city', $city);

        $state = $data['state'];
        update_option('rv_state', $state);

        $zip = $data['zip'];
        update_option('rv_zip', $zip);

        $areas = $data['areas'];
        update_option('rv_areas', $areas);

        $phone = $data['phone'];
        update_option('rv_phone', $phone);

        $weekday = $data['weekday'];
        update_option('rv_weekday', $weekday);

        $saturday = $data['saturday'];
        update_option('rv_saturday', $saturday);

        $weekend = $data['weekend'];
        update_option('rv_weekend', $weekend);
        
        $data['slug'] = get_option('rv_slug');
        $data['seoTitle'] = !empty(get_option('rv_seoTitle'))?stripslashes(get_option('rv_seoTitle')):'';
        $data['seoDescription'] = !empty(get_option('rv_seoDescription'))?stripslashes(get_option('rv_seoDescription')):'';
        $data['pageTitle'] = get_option('rv_pageTitle');
        $data['emailfriend'] = get_option('rv_emailfriend');
        $data['availability'] = get_option('rv_availability');
        $data['contact_dealer'] = get_option('rv_contact_dealer');
        $data['address'] = get_option('rv_address');
        $data['city'] = get_option('rv_city');
        $data['state'] = get_option('rv_state');
        $data['zip'] = get_option('rv_zip');
        $data['areas'] = get_option('rv_areas');
        $data['phone'] = get_option('rv_phone');
        $data['weekday'] = get_option('rv_weekday');
        $data['saturday'] = get_option('rv_saturday');
        $data['weekend'] = get_option('rv_weekend');
        return json_encode($data);
    }

    public function rvInventoryOptions(): array
    {
        $data['slug'] = get_option('rv_slug');
        $data['seoTitle'] = !empty(get_option('rv_seoTitle'))?stripslashes(get_option('rv_seoTitle')):'';
        $data['seoDescription'] = !empty(get_option('rv_seoDescription'))?stripslashes(get_option('rv_seoDescription')):'';
        $data['pageTitle'] = get_option('rv_pageTitle');
        $data['emailfriend'] = get_option('rv_emailfriend');
        $data['availability'] = get_option('rv_availability');
        $data['contact_dealer'] = get_option('rv_contact_dealer');
        $data['address'] = get_option('rv_address');
        $data['city'] = get_option('rv_city');
        $data['state'] = get_option('rv_state');
        $data['zip'] = get_option('rv_zip');
        $data['areas'] = get_option('rv_areas');
        $data['phone'] = get_option('rv_phone');
        $data['weekday'] = get_option('rv_weekday');
        $data['saturday'] = get_option('rv_saturday');
        $data['weekend'] = get_option('rv_weekend');
        return $data;
    }
}