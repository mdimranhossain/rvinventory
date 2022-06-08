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
$rvAutoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($rvAutoload)) {
    require_once $rvAutoload;
}

use Inc\Init;

$rvInit = new Init;

$rvInit->start();

register_activation_hook(__FILE__, [$rvInit, 'rvActivate']);
register_deactivation_hook(__FILE__, [$rvInit, 'rvDeactivate']);
register_uninstall_hook(__FILE__, ['Init', 'rvUninstall']);