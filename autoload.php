<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor/autoload.php';
spl_autoload_register(
	function ( $classname ) {
		$class     = preg_replace( array( '/OptimizeGenie\\\\/i', '/\\\\/i' ), array( '', DIRECTORY_SEPARATOR ), $classname );
		$classpath = __DIR__ . '/lib/' . $class . '.php';

		if ( file_exists( $classpath ) ) {
			require_once $classpath;
		}
	}
);
