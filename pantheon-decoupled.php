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
	activate_plugin( 'pantheon-decoupled-auth-example/pantheon-decoupled-auth-example.php' );
	activate_plugin( 'pantheon-decoupled/pantheon-decoupled-example.php' );
	activate_plugin( 'decoupled-preview/wp-decoupled-preview.php' );
	activate_plugin( 'pantheon-advanced-page-cache/pantheon-advanced-page-cache.php' );
	activate_plugin( 'wp-graphql/wp-graphql.php' );
	activate_plugin( 'wp-graphql-smart-cache/wp-graphql-smart-cache.php' );
	activate_plugin( 'wp-gatsby/wp-gatsby.php' );
  activate_plugin( 'wp-force-login/wp-force-login.php' );
	if ( !get_transient('permalinks_customized') ) {
		pantheon_decoupled_change_permalinks();
	}
	if ( !get_transient( 'graphql_smart_object_cache' ) ) {
		pantheon_decoupled_graphql_smart_object_cache();
	}
}

function pantheon_decoupled_change_permalinks() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure('/%postname%/');
	update_option( "rewrite_rules", FALSE );
	$wp_rewrite->flush_rules( true );
	set_transient('permalinks_customized', true);
}

function pantheon_decoupled_graphql_smart_object_cache() {
	update_option( 'graphql_cache_section', [ 'global_max_age' => 600 ] );
	set_transient( 'graphql_smart_object_cache', true );
}

add_action('init', 'pantheon_decoupled_enable_deps');
