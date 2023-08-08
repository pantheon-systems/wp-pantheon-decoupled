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

function pantheon_decoupled_settings_init() { 
    add_options_page( 'Pantheon Front-End Sites', 'Pantheon Front-End Sites', 'manage_options', 'pantheon-front-end-sites', 'pantheon_decoupled_settings_page' );

	add_settings_section(
		'wp-pantheon-decoupled-resources',
		'Resources',
		'', // No callback needed.
		'pantheon-front-end-sites'
	);

	add_settings_field(
		'fes-resources',
		'',
		'pantheon_decoupled_resources',
		'pantheon-front-end-sites',
		'wp-pantheon-decoupled-resources'
	);

	add_settings_section(
		'wp-pantheon-decoupled-list',
		'Preview Sites',
		'', // No callback needed.
		'pantheon-front-end-sites'
	);

	add_settings_field(
		'preview_list',
		'',
		'pantheon_decoupled_preview_list_html',
		'pantheon-front-end-sites',
		'wp-pantheon-decoupled-list'
	);
}

function pantheon_decoupled_settings_page() {
    ?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Pantheon Front-End Sites', 'wp-pantheon-decoupled' ); ?></h1>
			<?php
				do_settings_sections( 'pantheon-front-end-sites' );
			?>
		</div>
    <?php
}

function pantheon_decoupled_resources() {
    ?>
        <div class="wrap">
            <p>
                <?php esc_html_e( 'Front-End Sites on Pantheon allow you to use', 'wp-pantheon-decoupled' ); ?> 
                <a href="<?php echo esc_url('https://docs.pantheon.io/guides/decoupled/overview#what-is-a-decoupled-site'); ?>">
                        <?php echo esc_html('decoupled architecture'); ?>
                </a>
                <?php esc_html_e( 'to separate your frontend and backend into distinct entities.', 'wp-pantheon-decoupled' ); ?> 
            </p>
            <p><?php esc_html_e( 'You can use the WordPress backend starter kit to streamline the creation of your Front-End Site on Pantheon.', 'wp-pantheon-decoupled' ); ?></p>
            <h2><?php esc_html_e( 'Documentation', 'wp-pantheon-decoupled' ); ?></h2>

            <ul style="list-style-type:disc">
                <li>
                    <a href="<?php echo esc_url('https://docs.pantheon.io/guides/decoupled/overview'); ?>">
                        <?php echo esc_html('Front-End Sites Overview');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url('https://docs.pantheon.io/guides/decoupled/wp-backend-starters'); ?>">
                        <?php echo esc_html('WordPress Backend Starters'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url('https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters'); ?>">
                        <?php echo esc_html('WordPress + Next.js Frontend Starter'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url('https://docs.pantheon.io/guides/decoupled/wp-gatsby-frontend-starters'); ?>">
                        <?php echo esc_html('WordPress + Gatsby Frontend Starter'); ?>
                    </a>
                </li>
            </ul>
        </div>
    <?php
}

/**
 * HTML for list preview sites settings page.
 *
 * @return void
 */
function pantheon_decoupled_preview_list_html() {
	// Check if the List_Table class is available.
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}
	require_once plugin_dir_path( __FILE__ ) . 'class-list-table.php';
	$add_site_url = wp_nonce_url(
		add_query_arg( [
			'page' => 'add_preview_sites',
		], admin_url( 'options-general.php' ) ),
		'edit-preview-site',
		'nonce'
	);
	?>
		<span>
			<a href="<?php echo esc_url_raw( $add_site_url ); ?>" class="button-primary">+ <?php esc_html_e( 'Add Preview Site', 'wp-pantheon-decoupled-list' ); ?></a>
		</span>
		<div>
		<?php
		wp_create_nonce( 'preview-site-list' );
		$wp_list_table = new List_Table();
		$wp_list_table-> pantheon_decoupled_prepare_items();
		$wp_list_table->display();
		?>
		</div>
	<?php
}

add_action('init', 'pantheon_decoupled_enable_deps');
add_action( 'admin_menu', 'pantheon_decoupled_settings_init');
