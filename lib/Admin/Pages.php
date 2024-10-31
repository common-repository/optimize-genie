<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie\Admin
 */

namespace OptimizeGenie\Admin;

class Pages {

	public function __construct() {
		// add new admin page.
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
	}

	/**
	 * Loads up the admin menu for OptimizeGenie settings.
	 *
	 * @return void
	 */
	public function adminMenu() {
		add_menu_page(
			'OptimizeGenie',
			'OptimizeGenie',
			'manage_options',
			'optimize-genie',
			[ $this, 'settingsRender' ],
			'dashicons-admin-generic',
			'100'
		);
	}

	/**
	 * Render the content of the settingsPage
	 *
	 * @return void
	 */
	public function settingsRender() {
		?>
        <div class="wrap">
            <h1>OptimizeGenie</h1>
			<?php include_once OptimizeGenie_Plugin_Path . 'lib/Admin/Pages/Settings/configurations-tab.php'; ?>
        </div>
		<?php
	}
}
