<?php
/**
 * @author     FutrX
 * @year       2023
 * @package    futrx.com
 * @subpackage OptimizeGenie
 */

namespace OptimizeGenie;

class Helper {

	/**
	 * Clears all cached images' sizes from the cached directory.
	 *
	 * @return void
	 */
	public static  function clearCache(): void {
		$cacheDir = dirname( __DIR__, 3 ) . '/_imagecache/';
		$files    = glob( $cacheDir . '*' );

		// Remove all files.
		foreach ( $files as $file ) {
			// Check if the file
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}

		// Flush WordPress rewrites.
		flush_rewrite_rules();

		// Flush WordPress cache.
		wp_cache_flush();

	}
}
