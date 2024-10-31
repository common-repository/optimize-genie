<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie
 */

namespace OptimizeGenie;

use OptimizeGenie\Admin;

class Init {
	public function __construct() {
		$this->register_admin();
		$this->register_crons();
	}

	/**
	 * This registers all Admin related functionality.
	 * An "admin-related functionality" is a functionality that exists in the Admin Dashboard.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_admin(): void {
		new Admin\Notices();
		new Admin\Pages();
		new Admin\AdminBar();
	}

	/**
	 * This registers all Cron related functionality.
	 *
	 * @return void
	 */
	public function register_crons(): void {
		new Cron();
	}

	/**
	 * This is run on the activation hook of the plugin.
	 *
	 * @return void
	 */
	public static function activate_plugin(): void {
		// Check if server is apache.
		$serverType = Admin\Server::getServerType();
		if ( $serverType == 'apache' ) {
			// Creates the htaccess rules for apache.
			Admin\Server::handleApacheRedirects();
		}
	}
}
