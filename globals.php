<?php
/**
 * Globals to be used across the plugin.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


define( 'OptimizeGenie_Plugin_Name', 'OptimizeGenie' );
define( 'OptimizeGenie_Plugin_Version', '1.1.0' );
define( 'OptimizeGenie_Plugin_Prefix', 'optimize-genie' );
define( 'OptimizeGenie_Plugin_Text_Domain', 'optimize-genie' );
define( 'OptimizeGenie_Plugin_Path', plugin_dir_path( __FILE__ ) );
define( 'OptimizeGenie_Plugin_Url', plugin_dir_url( __FILE__ ) );

/**
 * Server configs globals.
 */
define( 'OptimizeGenie_Htaccess_Markers', [
	'begin' => '# BEGIN Optimize Genie',
	'end'   => '# END Optimize Genie',
] );

/**
 * Meta Keys gobals.
 */
define( 'OptimizeGenie_Meta_Key_Server_Configs_Code', OptimizeGenie_Plugin_Prefix . '_server_configs_code' );
