<?php

use Intervention\Image\ImageManager;

// Maximum allowed width of the resized image
define( 'OptimizeGenie_MAX_WIDTH', 3000000 );  // 3 million pixels

// Ensure the 'width' parameter is set and is a positive integer that is below the maximum allowed width
$width = filter_input( INPUT_GET, 'width', FILTER_VALIDATE_INT );
if ( ! $width || $width <= 0 || $width > OptimizeGenie_MAX_WIDTH ) {
	die();
}

// Capture the height parameter if it is set
$aspectRatio = filter_input( INPUT_GET, 'ar', FILTER_VALIDATE_FLOAT );

// Sanitize the 'image' parameter to prevent directory traversal attacks
$image = filter_input( INPUT_GET, 'image', FILTER_SANITIZE_STRING );
if ( ! $image ) {
	die();
}

// Calculate the absolute path of the image.
$imagePath = realpath( dirname( __DIR__, 3 ) . '/' . $image );

// Ensure the final path is still within the desired directory
$baseDir = realpath( dirname( __DIR__, 3 ) );
if ( strpos( $imagePath, $baseDir ) !== 0 ) {
	// The requested image is outside of the base directory.
	die();
}


/**
 * Define globals.
 */
define( 'OptimizeGenie_CACHE_DIR', dirname( __DIR__, 3 ) . '/_imagecache/' );
define( 'OptimizeGenie_CACHE_TIME', 60 * 60 * 24 );
define( 'OptimizeGenie_CACHE_KEY', basename( $imagePath ) . '_' . $width );


// Check if the cached image already exists and is still valid
if ( ! optimizegenie_checkIfCachedExists( $aspectRatio ) ) {
	if ( $aspectRatio ) {
		$imagePath = optimizegenie_resizeByAspectRatio( $imagePath, $width, $aspectRatio );
	} else {
		$imagePath = optimizegenie_resizeByWidth( $width, $imagePath );

	}

	// Set cache headers
	header( 'Cache-Control: public, max-age=' . OptimizeGenie_CACHE_TIME );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + OptimizeGenie_CACHE_TIME ) );

	// Return the resized image
	header( 'Content-Type: ' . optimizegenie_getImageMimeType( $imagePath ) );
	readfile( $imagePath );

}

/**
 * Check if the cached version of the image exists, to serve it as is.
 *
 * @return bool
 */
function optimizegenie_checkIfCachedExists( $aspectRatio ): bool {
	// If the cache dir does not exist, attempt to create it.
	if ( ! is_dir( OptimizeGenie_CACHE_DIR ) ) {
		@mkdir( OptimizeGenie_CACHE_DIR, 0777, true );

		// Check if the directory was created or if it existed from another concurrent request.
		if ( ! is_dir( OptimizeGenie_CACHE_DIR ) ) {
			die( 'Failed to create cache directory' );
		}
	}

	$cachedFile = $aspectRatio ? OptimizeGenie_CACHE_DIR . OptimizeGenie_CACHE_KEY . "_$aspectRatio" : OptimizeGenie_CACHE_DIR . OptimizeGenie_CACHE_KEY;

	if ( file_exists( $cachedFile ) && filemtime( $cachedFile ) > time() - OptimizeGenie_CACHE_TIME ) {
		// If the cached image exists and is less than an hour old, serve it directly.
		header( 'Content-Type: ' . optimizegenie_getImageMimeType( $cachedFile ) );
		readfile( $cachedFile );

		return true;
	}

	return false;
}

/**
 * Resize the image by the width provided.
 *
 * @param $width
 * @param $imagePath
 *
 * @return string
 */
function optimizegenie_resizeByWidth( $width, $imagePath ): string {
	require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';

	$image        = new ImageManager();
	$imageResized = $image->make( $imagePath );
	$imageResized = $imageResized->scale( $width );


	$cachedFile = OptimizeGenie_CACHE_DIR . OptimizeGenie_CACHE_KEY . '.webp';

	// Save the image with the correct format (JPEG, PNG, GIF, WebP, .heic, .ico)
	$imageResized->toWebp()->save( $cachedFile );

	return $cachedFile;
}

/**
 * Resize the image by aspect ratio, and maximum width given.
 */
function optimizegenie_resizeByAspectRatio( string $imagePath, int $width, float $aspectRatio ) {
	require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';

	// Calculate the height based on the aspect ratio
	$height = $width / $aspectRatio;

	$image        = new ImageManager();
	$imageResized = $image->make( $imagePath );
	$imageResized = $imageResized->fit( $width, $height );


	$cachedFile = OptimizeGenie_CACHE_DIR . OptimizeGenie_CACHE_KEY . "_$aspectRatio" . '.webp';

	// Save the image with the correct format (JPEG, PNG, GIF, WebP, .heic, .ico)
	$imageResized->toWebp()->save( $cachedFile );

	return $cachedFile;
}


/**
 * Get the image format (extension) based on the image file path.
 *
 * @param $imagePath
 *
 * @return string
 */
function optimizegenie_getImageFormat( $imagePath ): string {
	$extension = optimizegenie_getImageExtension( $imagePath );
	switch ( $extension ) {
		case 'png':
			return 'png';
		case 'gif':
			return 'gif';
		case 'webp':
			return 'webp';
		case 'heic':
			return 'heic';
		case 'ico':
			return 'ico';
		default:
			return 'jpeg'; // Default to JPEG format if the format is not recognized.
	}
}


/**
 * Get the image MIME type based on the image file path.
 *
 * @param $imagePath
 *
 * @return string
 */
function optimizegenie_getImageMimeType( $imagePath ): string {
	$extension = optimizegenie_getImageExtension( $imagePath );
	switch ( $extension ) {
		case 'png':
			return 'image/png';
		case 'gif':
			return 'image/gif';
		default:
			return 'image/jpeg'; // Default to JPEG if the format is not recognized.
	}
}

/**
 * Get the image extension based on the image file path.
 *
 * @param $imagePath
 *
 * @return string
 */
function optimizegenie_getImageExtension( $imagePath ): string {
	$extension = pathinfo( $imagePath, PATHINFO_EXTENSION );

	return strtolower( $extension );
}
