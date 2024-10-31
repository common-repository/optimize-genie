<?php
/*
 * Plugin Name: OptimizeGenie
 * Description: OptimizeGenie by FutrX, is a plugin that helps you optimize your WordPress website for speed and performance.
 * Version: 1.1.0
 * Author: FutrX
 * Author URI: http://futrx.com/
 * License: GPL2
 * Text Domain: optimize-genie
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use OptimizeGenie\Init;

// Require the autoloader and globals.
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/globals.php';


// Initialize the plugin.
new Init();

// Register activation hook.
register_activation_hook( __FILE__, array( Init::class, 'activate_plugin' ) );
