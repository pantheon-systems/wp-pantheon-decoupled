<?php
/**
 * Plugin Name:     Pantheon Decoupled Example
 * Plugin URI:      https://pantheon.io/
 * Description:     Example content to demonstrate sourcing content from a Decoupled WordPress site.
 * Author:          Pantheon
 * Author URI:      https://pantheon.io/
 * Text Domain:     pantheon-decoupled-example
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pantheon_Decoupled_Example
 */

require_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once(ABSPATH . 'wp-admin/includes/post.php');

/**
 * Create a post when activating the plugin.
 */
function pantheon_decoupled_example_create_post() {
	$image_url = dirname(__FILE__) . '/pizza.jpeg';
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$filename = basename($image_url);
	if (wp_mkdir_p($upload_dir['path'])) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}
	file_put_contents($file, $image_data);
	$wp_filetype = wp_check_filetype($filename, null);
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => sanitize_file_name($filename),
		'post_content' => '',
		'post_status' => 'inherit'
	);
	$attach_id = wp_insert_attachment($attachment, $file);
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata($attach_id, $file);
	wp_update_attachment_metadata($attach_id, $attach_data);

	$example_post = [
		'post_title' => 'Example Post with Image',
		'post_content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
		'post_status' => 'publish'
	];
	$post_id = wp_insert_post($example_post);
	set_post_thumbnail($post_id, $attach_id);
	set_transient('pantheon_decoupled_example_created', true);
}

/**
 * Create example menu when activating the plugin.
 */
function pantheon_decoupled_example_menu() {
	$menu = wp_get_nav_menu_object('Example Menu');
	$menu_id = $menu ? $menu->term_id : wp_create_nav_menu('Example Menu');
	wp_update_nav_menu_item($menu_id, 0, array(
		'menu-item-title' =>  __('Example Post with Image'),
		'menu-item-classes' => 'example_post_with_image',
		'menu-item-url' => home_url( '/example-post-with-image/' ),
		'menu-item-status' => 'publish'));
	$menu_locations = get_nav_menu_locations();
	$menu_locations['footer'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $menu_locations );
	set_transient('pantheon_decoupled_example_menu_created', true);
}

/**
 * Activate the plugin.
 */
function pantheon_decoupled_example_activate() {
	if ( !post_exists("Example Post with Image") ) {
		if ( !get_transient('pantheon_decoupled_example_created') ) {
			pantheon_decoupled_example_create_post();
		}
		if ( !get_transient('pantheon_decoupled_example_menu_created')) {
			pantheon_decoupled_example_menu();
		}
	}
}
add_action('init', 'pantheon_decoupled_example_activate');
