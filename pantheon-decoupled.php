<?php
/**
 * Plugin Name:     Pantheon Decoupled
 * Plugin URI:      https://pantheon.io/
 * Description:     Configuration necessary for hosting Decoupled WordPress sites on Pantheon.
 * Version:         0.1.0
 * Author:          Pantheon
 * Author URI:      https://pantheon.io/
 * Text Domain:     pantheon-decoupled
 * Domain Path:     /languages
 *
 * @package         Pantheon_Decoupled
 */

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

function pantheon_decoupled_enable_deps() {
	activate_plugin('pantheon-decoupled-auth-example/pantheon-decoupled-auth-example.php');
	activate_plugin( 'pantheon-decoupled/pantheon-decoupled-example.php' );
	activate_plugin( 'wp-decoupled-preview/wp-decoupled-preview.php' );
	set_transient('permalinks_customized', false);
	activate_plugin( 'wp-graphql/wp-graphql.php' );
	pantheon_decoupled_change_permalinks();
}

function pantheon_decoupled_change_permalinks() {
	global $wp_rewrite;
	if ( !get_transient('permalinks_customized') ) {
		$wp_rewrite->set_permalink_structure('/%postname%/');
		update_option( "rewrite_rules", FALSE );
		$wp_rewrite->flush_rules( true );
		set_transient('permalinks_customized', true);
	}
}
add_action('init', 'pantheon_decoupled_enable_deps');
