<?php
/**
 * Class VehicleTest
 *
 * @package rvinventory
 */

/**
 * Sample test case.
 */

  $autoload=dirname(__FILE__,2)."/vendor/autoload.php";
if(file_exists($autoload)){
	require_once($autoload);
}
use Inc\Vehicle;

class VehicleTest extends WP_UnitTestCase {

	var $endpoint;

	public function setUp()
	    {
		parent::setUp();
	    }

	public function test_rvList() {
		$vehicle = new Vehicle();
		$list = json_decode($vehicle->rvList());
		$this-> assertEquals(count($list), 10);
	}
	public function test_rvDetails() {
		foreach(array(1, 2, 3) as $id) {
			$details = json_decode($vehicle->rvDetails($id));
			$this->assertEquals($id, $details->id);
		}
	}
}