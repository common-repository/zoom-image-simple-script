<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function ziss_delete_plugin() {
	global $wpdb;

	delete_option( 'zoom-image-simple-script' );

	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'zoom_image_simples' ) );
}

ziss_delete_plugin();