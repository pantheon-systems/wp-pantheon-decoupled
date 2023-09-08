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
    add_submenu_page(
      NULL,
      '',
      '',
      'manage_options',
      'test_preview_site',
      'pantheon_decoupled_test_preview_page',
    );

    add_submenu_page(
        NULL,
        '',
        '',
        'manage_options',
        'env_vars',
        'pantheon_decoupled_env_vars'
    );
  

    add_settings_field(
        'fes-resources',
        '',
        'pantheon_decoupled_resources',
        'pantheon-front-end-sites',
        'wp-pantheon-decoupled-resources'
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
				        do_settings_fields( 'pantheon-front-end-sites', 'wp-pantheon-decoupled-resources' );
                do_settings_fields( 'pantheon-front-end-sites', 'wp-pantheon-decoupled-list' );
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
    if ( ! class_exists( 'List_Table' ) ) {
        require_once WP_PLUGIN_DIR . '/decoupled-preview/src/class-list-table.php';
    }
    require_once plugin_dir_path( __FILE__ ) . 'src/class-list-table.php';
    add_thickbox();
    $add_site_url = wp_nonce_url(
        add_query_arg( [
            'page' => 'add_preview_sites',
        ], admin_url( 'options-general.php' ) ),
        'edit-preview-site',
        'nonce'
    );
    ?>
		<h2><?php esc_html_e( 'Preview Sites', 'wp-pantheon-decoupled' ); ?></h2>
        <span>
            <a href="<?php echo esc_url_raw( $add_site_url ); ?>" class="button-primary">+ <?php esc_html_e( 'Add Preview Site', 'wp-pantheon-decoupled-list' ); ?></a>
        </span>
        <div>
        <?php
        wp_create_nonce( 'preview-site-list' );
        $wp_list_table = new FES_Preview_Table();
        $wp_list_table-> prepare_items();
        $wp_list_table->display();
        ?>
        </div>
    <?php
}

function pantheon_decoupled_test_preview_page() {
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
  }
  check_admin_referer( 'test-preview-site', 'nonce' );

  $docs_link = "<p>Consult the Pantheon Documentation for more information on <a href='https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters/content-preview' target='_blank' rel='noopener noreferrer'>configuring content preview</a>.</p>\n";

  // Get data for preview site
  $id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : NULL;
  $preview_sites = get_option( 'preview_sites' );
  $preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : NULL;
  $post_type = isset( $preview_site['content_type'] ) ? $preview_site['content_type'][0] : 'post';

  // Get example content to preview.
  $args = array(
    'numberposts'	=> 1,
    'order' => 'ASC',
    'post_type' => $post_type
  );
  $posts = get_posts( $args );
  $post = $posts[0];

  // Make test API call.
  $test_url = $preview_site['url'] . '?secret=' . $preview_site['secret_string'] . '&uri=' . $post->post_name . '&id=' . $post->ID . '&content_type=' . $post_type . '&test=true';
  $response = wp_remote_get( $test_url );
  $body     = json_decode(wp_remote_retrieve_body( $response ), true);

  ?>
      <style>
        /* Hide admin bar and padding on top of page. */
        html.wp-toolbar {
          padding-top: 0;
        }
        #wpadminbar {
          display: none;
        }
      </style>
      <div class="wrap">
          <h1><?php esc_html_e( 'Test Preview Site', 'wp-pantheon-decoupled' ); ?></h1>
          <?php
            echo "<h3>{$preview_site['label']}</h3>\n";
            if (empty($body)) {
              // We weren't able to reach the preview endpoint at all.
              echo "<p>There was an error connecting to the preview site.</p>\n";
              echo "<p>Code: {$response['response']['code']}</p>\n";
              echo "<p>Message: {$response['response']['message']}</p>\n";
              echo $docs_link;
            }
            else if (isset($body["error"])) {
              // We were able to reach the preview endpoint, but there was an error.
              echo "<p>Error: " . esc_html__( $body["error"], 'wp-pantheon-decoupled' ) . "</p>\n";
              if (isset($body["message"]))  {
                echo "<p>Message: " . esc_html__( $body["message"], 'wp-pantheon-decoupled' ) . "</p>\n";
              }
              echo $docs_link;
            }
            else {
              // Success!
              echo "<p>WordPress was able to communicate with your preview site and preview example content.</p>\n";
              if (isset($body["message"]))  {
                echo "<p>Code: {$response['response']['code']}</p>\n";
                echo "<p>Message: " . esc_html__( $body["message"], 'wp-pantheon-decoupled' ) . "</p>\n";
              }
            }
          ?>
      </div>
  <?php
}

function pantheon_decoupled_env_vars() {
    if ( ! current_user_can( 'manage_options' ) ) {
      wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
    }
    check_admin_referer( 'env-vars', 'nonce' );

    // Get data for preview site
    $id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : NULL;
    $preview_sites = get_option( 'preview_sites' );
    $preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : NULL;
    
    global $wp;
    $home_url = home_url( $wp->request );
      
    ?>
        <style>
          /* Hide admin bar and padding on top of page. */
          html.wp-toolbar {
            padding-top: 0;
          }
          #wpadminbar {
            display: none;
          }
        </style>
        <div class="wrap">
            <h1><?php esc_html_e( 'Environment Variables', 'wp-pantheon-decoupled' ); ?></h1>
            <h4>PREVIEW_SECRET</h4>
            <?php
              echo "<p>Value: {$preview_site['secret_string']}</p>\n";
            ?>
            <h4>WP_APPLICATION_USERNAME</h4>
            <?php
               echo "<p>Value: {$preview_site['associated_user']}</p>\n";
            ?>
            <h4>WP_APPLICATION_PASSWORD</h4>
            <p>The application password associated with this user intended to be used with this preview site.</p>
            <h4>Linked CMS</h4>
            <?php
              echo "<p>Link the CMS site that relates to: <strong>{$home_url}</strong></p>\n";
            ?>
        </div>
    <?php
}

function pantheon_decoupled_admin_notice() {
  $add_site_url = wp_nonce_url(
    add_query_arg( [
        'page' => 'pantheon-front-end-sites',
    ], admin_url( 'options-general.php' ) ),
    'pantheon-front-end-sites',
    'nonce'
  );

  if( !get_transient( 'post_install_next_steps' ) ) {
		?>
			<div class="notice notice-success notice-alt below-h2">
				<strong>Pantheon Decoupled Configuration</strong>
				<p>
					<label for="pantheon-decoupled-post-install">
            In order to complete your configuration, visit the <a href="<?php echo esc_url_raw( $add_site_url ); ?>">Front-End Sites Settings</a> page.
					</label>
				</p>
			</div>
		<?php
    set_transient('post_install_next_steps', true);
	}
}
add_action('admin_notices', 'pantheon_decoupled_admin_notice');
add_action('init', 'pantheon_decoupled_enable_deps');
add_action( 'admin_menu', 'pantheon_decoupled_settings_init');