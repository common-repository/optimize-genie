<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie
 */

namespace OptimizeGenie;

class Cron {

	/**
	 * Cron constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'schedule' ] );
		add_action( 'optimize_genie_clear_cache', [ 'OptimizeGenie\Helper', 'clearCache' ] );
	}

	/**
	 * Schedule the cron job.
	 *
	 * @return void
	 */
	public function schedule(): void {
		if ( ! wp_next_scheduled( 'optimize_genie_clear_cache' ) ) {
			wp_schedule_event( time(), 'daily', 'optimize_genie_clear_cache' );
		}
	}

}
