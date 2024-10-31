<?php
/**
 * This class registers all admin notices
 *
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie\Admin
 */

namespace OptimizeGenie\Admin;


class Notices {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'serverIsNginx' ) );
	}


	/**
	 * Adds notices for bulk optimize only after the bulk edit process.
	 *
	 * @return void
	 */
	public function serverIsNginx(): void {
		// Check if current user has the required permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Check if server is nginx and not configured.
		$serverType = Server::getServerType();
		if ( $serverType === 'nginx' && ! Server::nginxConfigured() ) {
			$settingsPageUrl = admin_url( 'admin.php?page=optimize-genie' );
			?>
            <div class='notice notice-error'>
                <p><?php printf(
						esc_html__(
							"OptimizeGenie requires custom server configuration to work properly. <a href='%s'>More Info</a>",
							'optimize-genie'
						),
						esc_url($settingsPageUrl)
					); ?></p>
            </div>
			<?php
		}
	}

}
