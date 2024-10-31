<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie\Admin
 */

namespace OptimizeGenie\Admin;


class Server {

	/**
	 * Returns the server type.
	 *
	 * **Available options:**
	 *  - nginx
	 *  - apache
	 *
	 * @return string
	 */
	public static function getServerType(): string {
		if ( ! isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return 'unknown';
		}
		$serverSoftware = strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) );
		if ( strpos( $serverSoftware, 'nginx' ) !== false ) {
			return 'nginx';
		} elseif ( strpos( $serverSoftware, 'Apache' ) !== false ) {
			return 'apache';
		} else {
			return 'unknown';
		}
	}

	/**
	 * Return the full path of the WordPress installation.
	 *
	 * @return string
	 */
	public static function getDocumentRoot(): string {
		$documentRoot = sanitize_text_field( $_SERVER['DOCUMENT_ROOT'] );

		// remove spaces at the end of the path.
		$documentRoot = rtrim( $documentRoot, ' ' );

		// remove dots at the end of the path.
		$documentRoot = rtrim( $documentRoot, '.' );

		// remove trailing slash.
		return rtrim( $documentRoot, '/' );

	}

	/**
	 * Check nginx is configured.
	 *
	 * @return bool
	 */
	public static function nginxConfigured(): bool {
		// Only run this test if the current user is admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return true;
		}
		$testResizeImage = OptimizeGenie_Plugin_Url . 'assets/resize-test/10px_image.jpg?width=1';
		// get size of this image.
		$size  = getimagesize( $testResizeImage );
		$width = $size[0];

		if ( $width > 1 ) {
			return false;
		}

		return true;

	}


	/**
	 * Return the apache directives to be added to the .htaccess file.
	 *
	 * @return string
	 */
	public static function getApacheModRewriteRules(): string {

		return "RewriteEngine On

# Serve the image as-is if there are no query parameters
RewriteCond %{QUERY_STRING} ^$
RewriteRule ^wp-content/uploads/.+\.(jpe?g|png|gif|bmp)$ - [L,NC]

# Redirect image requests under wp-content/uploads to width.php
RewriteRule ^wp-content/uploads/(.+?\.(jpe?g|png|gif|bmp))$ /wp-content/plugins/optimize-genie/customizer/customize.php?image=$1 [L,QSA,NC]
";
	}

	/**
	 * Create new .htaccess configs to redirect all images hosted under wp-content/uploads to the
	 * customizer library.
	 *
	 * @return void
	 */
	public static function handleApacheRedirects(): void {
		global $wp_filesystem;

		// Include the WordPress filesystem API and initialize WP_Filesystem
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();

		// Check if WP_Filesystem initialization was successful
		if ( ! $wp_filesystem ) {
			return; // Handle the error appropriately
		}

		$htaccessFile = $wp_filesystem->abspath() . '.htaccess';

		// Check if the .htaccess file exists
		if ( ! $wp_filesystem->exists( $htaccessFile ) ) {
			return;
		}

		// Read the contents of the .htaccess file
		$htaccess = $wp_filesystem->get_contents( $htaccessFile );
		if ( str_contains( $htaccess, OptimizeGenie_Htaccess_Markers['begin'] ) ) {
			// Replace between the markers
			$beginMarker = OptimizeGenie_Htaccess_Markers['begin'];
			$endMarker   = OptimizeGenie_Htaccess_Markers['end'];

			$htaccess = preg_replace_callback(
				"/$beginMarker(.+)$endMarker/s",
				function ( $matches ) {
					return OptimizeGenie_Htaccess_Markers['begin'] . self::getApacheModRewriteRules() . OptimizeGenie_Htaccess_Markers['end'];
				},
				$htaccess
			);

			// Write the updated rules to the .htaccess file
			$wp_filesystem->put_contents( $htaccessFile, $htaccess );
		} else {
			// Append new rules to the .htaccess file
			$htaccessRules = "\n" . OptimizeGenie_Htaccess_Markers['begin'] . "\n" . self::getApacheModRewriteRules() . "\n" . OptimizeGenie_Htaccess_Markers['end'];
			$wp_filesystem->put_contents( $htaccessFile, $htaccessRules, FS_CHMOD_FILE );
		}

		// Flush the rewrite rules
		flush_rewrite_rules();
	}

}
