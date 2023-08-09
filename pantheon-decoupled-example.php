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
	set_transient( 'pantheon_decoupled_example_menu_created', true );
}


/**
 * Show example preview password admin notice.
 *
 * @return void
 */
function show_example_preview_password_admin_notice() {
	$preview_password = get_transient( 'example_preview_password' );
	if ( $preview_password ) {
		?>
		<div class="notice notice-success notice-alt below-h2 is-dismissible">
			<strong><?php esc_html_e( 'Pantheon Decoupled Preview Example', 'wp-decoupled-preview' ); ?></strong>
			<p class="decoupled-preview-example">
				<label for="new-decoupled-preview-example-value">
					<?php echo wp_kses( __( 'The shared secret of the <strong>Example NextJS Preview</strong> site is:', 'wp-decoupled-preview' ), [ 'strong' => [] ] ); ?>
				</label>
				<input type="text" class="code" value="<?php printf( esc_attr( get_transient( 'example_preview_password' ) ) ); ?>" />
			</p>
			<p><?php esc_html_e( 'Be sure to save this in a safe location. You will not be able to retrieve it.', 'wp-decoupled-preview' ); ?></p>
		</div>
		<?php
		delete_transient( 'example_preview_password' );
	}
}

/**
 * Delete preview sites options when deactivation plugin.
 *
 * @return void
 */
function delete_default_options() {
	delete_option( 'preview_sites' );
}

/**
 * Set default values for the preview sites options.
 *
 * @return void
 */
function set_default_options () {
	$secret = wp_generate_password( 10, false );
	set_transient( 'example_preview_password', $secret );

	add_option(
		'preview_sites',
		[
			'preview' => [
				1 => [
					'label' => esc_html__( 'Example NextJS Preview', 'wp-decoupled-preview' ),
					'url' => 'https://example.com/api/preview',
					'secret_string' => $secret,
					'preview_type' => 'Next.js',
					'associated_user' => 'decoupled_example_user',
					'id' => 1,
				],
			],
		]
	);
}

/**
 * Activate the plugin.
 */
function pantheon_decoupled_example_activate() {
	if ( ! post_exists( 'Example Post with Image' ) ) {
		if ( ! get_transient( 'pantheon_decoupled_example_created' ) ) {
			pantheon_decoupled_example_create_post();
		}
		if ( ! get_transient( 'pantheon_decoupled_example_menu_created' ) ) {
			pantheon_decoupled_example_menu();
		}
	}
}



add_action( 'init', 'pantheon_decoupled_example_activate' );
add_action( 'admin_notices', 'show_example_preview_password_admin_notice' );
register_activation_hook( __FILE__, __NAMESPACE__ . '\\set_default_options' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\delete_default_options' );
