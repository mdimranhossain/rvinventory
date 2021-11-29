<?php
/**
 * @wordpress-plugin
 * Plugin Name: RV Inventory
 * Plugin URI:        https://imran.bhubs.com/wp-content/plugins/rvinventory/
 * Description:       Manages Vehicle Inventory for rv dealers.
 * Version:           1.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Md Imran Hossain
 * Author URI:        https://mdimranhossain.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rvinventory
 */

declare(strict_types=1);
$viAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($viAutoload)) {
    require_once $viAutoload;
}

use Inc\Init;

$viInit = new Init;

$viInit->start();

register_activation_hook(__FILE__, [$viInit, 'viActivate']);
register_deactivation_hook(__FILE__, [$viInit, 'viDeactivate']);
register_uninstall_hook(__FILE__, ['Init', 'viUninstall']);