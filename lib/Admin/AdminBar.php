<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    FutrX
 * @subpackage OptimizeGenie\Admin
 */

namespace OptimizeGenie\Admin;

use OptimizeGenie\Helper;

class AdminBar {

	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'addAdminBarMenu' ), 999 );
		add_action( 'init', array( $this, 'clearImagesCache' ) );
	}

	/**
	 * Add the OptimizeGenie Admin bar.
	 *
	 * @return void
	 */
	public function addAdminBarMenu(): void {
		// Add new button to the admin bar.
		global $wp_admin_bar;
		$wp_admin_bar->add_menu(
			array(
				'id'    => OptimizeGenie_Plugin_Prefix,
				'title' => OptimizeGenie_Plugin_Name,
				'href'  => '#',
			)
		);

		// Add clear cache button.
		$this->addClearCacheButton();
	}

	/**
	 * This adds the "Clear Images Cache" button to OptimizeGenie Admin Bar menu.
	 *
	 * @return void
	 */
	public function addClearCacheButton(): void {
		global $wp_admin_bar;
		$cache_flush_url = wp_nonce_url(
			add_query_arg(
				[
					'caller' => OptimizeGenie_Plugin_Prefix,
					'action' => 'clear_images_cache',
				],
				get_site_url()
			),
			'clear_images_cache_action'
		);
		$wp_admin_bar->add_menu(
			array(
				'id'     => OptimizeGenie_Plugin_Prefix . '-clear-cache',
				'title'  => __( 'Clear Images Cache', 'optimize-genie' ),
				'parent' => OptimizeGenie_Plugin_Prefix,
				'href'   => $cache_flush_url,
			)
		);
	}

	/**
	 * Clear images cache if the user clicks on the "Clear Images Cache" button.
	 *
	 * @return void
	 */
	public function clearImagesCache(): void {
		if (
			isset( $_GET['caller'], $_GET['action'], $_GET['_wpnonce'] ) &&
			$_GET['caller'] === OptimizeGenie_Plugin_Prefix &&
			$_GET['action'] === 'clear_images_cache' &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'clear_images_cache_action' )
		) {
			Helper::clearCache();
			wp_safe_redirect( remove_query_arg( [ 'caller', 'action', '_wpnonce' ] ) );
			exit;
		}
	}

}
