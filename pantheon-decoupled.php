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

require_once ABSPATH . 'wp-admin/includes/plugin.php';
use Pantheon\DecoupledPreview\Decoupled_Preview_Settings;

/**
 * Enable plugins necessary for Decoupled WordPress sites.
 */
function pantheon_decoupled_enable_deps() {
	activate_plugin( 'pantheon-decoupled-auth-example/pantheon-decoupled-auth-example.php' );
	activate_plugin( 'pantheon-decoupled/pantheon-decoupled-example.php' );
	activate_plugin( 'decoupled-preview/wp-decoupled-preview.php' );
	activate_plugin( 'pantheon-advanced-page-cache/pantheon-advanced-page-cache.php' );
	activate_plugin( 'wp-graphql/wp-graphql.php' );
	activate_plugin( 'wp-graphql-smart-cache/wp-graphql-smart-cache.php' );
	activate_plugin( 'wp-gatsby/wp-gatsby.php' );
	activate_plugin( 'wp-force-login/wp-force-login.php' );
	if ( ! get_transient( 'permalinks_customized' ) ) {
		pantheon_decoupled_change_permalinks();
	}
	if ( ! get_transient( 'graphql_smart_object_cache' ) ) {
		pantheon_decoupled_graphql_smart_object_cache();
	}
}

/**
 * Change permalinks to /%postname%/ when activating the plugin.
 */
function pantheon_decoupled_change_permalinks() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules( true );
	set_transient( 'permalinks_customized', true );
}

/**
 * Enable GraphQL Smart Object Cache when activating the plugin.
 */
function pantheon_decoupled_graphql_smart_object_cache() {
	update_option( 'graphql_cache_section', [ 'global_max_age' => 600 ] );
	set_transient( 'graphql_smart_object_cache', true );
}

/**
 * Initialize settings pages.
 *
 * @return void
 */
function pantheon_decoupled_settings_init() {
	add_options_page(
		'Pantheon Front-End Sites',
		'Pantheon Front-End Sites',
		'manage_options',
		'pantheon-front-end-sites',
		'pantheon_decoupled_settings_page'
	);
	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'test_preview_site',
		'pantheon_decoupled_test_preview_page'
	);

	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'env_vars',
		'pantheon_decoupled_env_vars'
	);

	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'env_regen',
		'pantheon_decoupled_regen_env_vars'
	);

	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'env_regen_action',
		'pantheon_decoupled_regen_env_vars_action'
	);


	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'fes_add_preview_sites',
		'pantheon_decoupled_create_html'
	);

	add_submenu_page(
		null,
		'',
		'',
		'manage_options',
		'preview_delete',
		'pantheon_decoupled_preview_delete'
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

/**
 * Removes Preview Sites from settings menu since the the Front-End Sites
 * settings page includes equivalent functionality.
 * It will still be possible to access the settings page directly.
 *
 * @return void
 */
function remove_preview_sites_submenu() {
	remove_submenu_page( 'options-general.php', 'preview_sites' );
}

/**
 * Render markup for front-end sites settings page
 *
 * @return void
 */
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

/**
 * Render documentation and resources for front-end sites settings page
 *
 * @return void
 */
function pantheon_decoupled_resources() {
	?>
		<div class="wrap">
			<p>
				<?php esc_html_e( 'Front-End Sites on Pantheon allow you to use', 'wp-pantheon-decoupled' ); ?>
				<a href="<?php echo esc_url( 'https://docs.pantheon.io/guides/decoupled/overview#what-is-a-decoupled-site' ); ?>">
						<?php echo esc_html( 'decoupled architecture' ); ?>
				</a>
				<?php esc_html_e( 'to separate your frontend and backend into distinct entities.', 'wp-pantheon-decoupled' ); ?>
			</p>
			<p><?php esc_html_e( 'You can use the WordPress backend starter kit to streamline the creation of your Front-End Site on Pantheon.', 'wp-pantheon-decoupled' ); ?></p>
			<h2><?php esc_html_e( 'Documentation', 'wp-pantheon-decoupled' ); ?></h2>

			<ul style="list-style-type:disc">
				<li>
					<a href="<?php echo esc_url( 'https://docs.pantheon.io/guides/decoupled/overview' ); ?>">
						<?php echo esc_html( 'Front-End Sites Overview' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'https://docs.pantheon.io/guides/decoupled/wp-backend-starters' ); ?>">
						<?php echo esc_html( 'WordPress Backend Starters' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters' ); ?>">
						<?php echo esc_html( 'WordPress + Next.js Frontend Starter' ); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'https://docs.pantheon.io/guides/decoupled/wp-gatsby-frontend-starters' ); ?>">
						<?php echo esc_html( 'WordPress + Gatsby Frontend Starter' ); ?>
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
	require_once plugin_dir_path( __FILE__ ) . 'src/class-fes-preview-table.php';
	add_thickbox();
	$add_site_url = wp_nonce_url(
		add_query_arg( [
			'page' => 'fes_add_preview_sites',
		], admin_url( 'options-general.php' ) ),
		'edit-preview-site',
		'nonce'
	);
	?>
		<h2><?php esc_html_e( 'Preview Sites', 'wp-pantheon-decoupled' ); ?></h2>
		<span>
			<a href="<?php echo esc_url_raw( $add_site_url ); ?>&width=600&height=500" class="button-primary thickbox">+ <?php esc_html_e( 'Add Preview Site', 'wp-pantheon-decoupled-list' ); ?></a>
		</span>
		<div>
		<?php
		wp_create_nonce( 'preview-site-list' );
		$wp_list_table = new FES_Preview_Table();
		$wp_list_table->prepare_items();
		$wp_list_table->display();
		?>
		</div>
	<?php
}

/**
 * Render markup for front-end sites specific create preview site form.
 *
 * @return void
 */
function pantheon_decoupled_create_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}

	check_admin_referer( 'edit-preview-site', 'nonce' );
	$edit_id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : false;
	if ( $edit_id ) {
		$action = 'options.php?fes=1&edit=' . $edit_id;
	} else {
		$action = 'options.php?fes=1';
	}
	?>
	<style>
		/*
		Styles here are for ajax version of thickbox. We lose some styles, but gain
		a loading indicator...
	 */
		#TB_window {
		background-color: rgb(240, 240, 241);
		}
		#TB_window #adminmenumain,
		#TB_window #wpfooter {
		display: none;
		}
		#TB_window #wpcontent {
		margin-left: 0;
		}
	</style>
	<div class="wrap">
		<h1><?php esc_html_e( 'Create or Edit Preview Site', 'wp-decoupled-preview' ); ?></h1>
		<form action="<?php echo esc_url( $action ); ?>" method="post">
		<?php
		settings_fields( 'wp-decoupled-preview' );
		do_settings_sections( 'preview_sites' );
		?>
		<?php wp_nonce_field( 'edit-preview-site', 'nonce' ); ?>
		<?php submit_button(); ?>
		</form>
		</div>
	<?php
}

/**
 * Render test preview site modal.
 *
 * @return void
 */
function pantheon_decoupled_test_preview_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}
	check_admin_referer( 'test-preview-site', 'nonce' );

	// Get data for preview site.
	$id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : null;
	$preview_sites = get_option( 'preview_sites' );
	$preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : null;
	$post_type = isset( $preview_site['content_type'] ) ? $preview_site['content_type'][0] : 'post';

	// Get example content to preview.
	$args = [
		'numberposts'   => 1,
		'order' => 'ASC',
		'post_type' => $post_type,
		'suppress_filters' => false,
	];
  // phpcs:disable WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
	$posts = get_posts( $args );
	$post = $posts[0];

	// Make test API call.
	$test_url = $preview_site['url'] . '?secret=' . $preview_site['secret_string'] . '&uri=' . $post->post_name . '&id=' . $post->ID . '&content_type=' . $post_type . '&test=true';
	$response = wp_http_validate_url( $test_url ) ? $response = wp_remote_get( $test_url ) : null;
	$body     = json_decode( wp_remote_retrieve_body( $response ), true );
  // phpcs:disable WordPressVIPMinimum.UserExperience.AdminBarRemoval.HidingDetected
	?>
<style>
	/*
	Tweak styles for ajax version of thickbox.
	*/
	#TB_window {
		background-color: rgb(240, 240, 241);
	}
	#TB_window #adminmenumain,
	#TB_window #wpfooter {
		display: none;
	}
	#TB_window #wpcontent {
		margin-left: 0;
	}
	</style>
		<div class="wrap">
			<h1><?php esc_html_e( 'Test Preview Site', 'wp-pantheon-decoupled' ); ?></h1>
			<?php
			echo '<h3>' . esc_html(
			// Translators: %s is the preview site label.
				sprintf( __( '%s', 'wp-decoupled-preview' ), $preview_site['label'] )
			) . "</h3>\n";
			if ( empty( $body ) ) {
				// We weren't able to reach the preview endpoint at all.
				echo "<p>There was an error connecting to the preview site. Please confirm that the URL is a valid preview API endpoint.</p>\n";
				if ( ! empty( $response ) ) {
					echo '<p>' . esc_html(
					// Translators: %s is the response code.
						sprintf( __( 'Code: %s', 'wp-decoupled-preview' ), $response['response']['code'] )
					) . "</p>\n";
					echo '<p>' . esc_html(
					// Translators: %s is the response message.
						sprintf( __( 'Message: %s', 'wp-decoupled-preview' ), $response['response']['message'] )
					) . "</p>\n";
				}
				echo "<p>Consult the Pantheon Documentation for more information on <a href='https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters/content-preview' target='_blank' rel='noopener noreferrer'>configuring content preview</a>.</p>\n";
			} elseif ( isset( $body['error'] ) ) {
				// We were able to reach the preview endpoint, but there was an error.
				echo '<p>' . esc_html(
				// Translators: %s is the error.
					sprintf( __( 'Error: %s', 'wp-decoupled-preview' ), $body['error'] )
				) . "</p>\n";
				if ( isset( $body['message'] ) ) {
					echo '<p>' . esc_html(
					// Translators: %s is the error message.
						sprintf( __( 'Message: %s', 'wp-decoupled-preview' ), $body['message'] )
					) . "</p>\n";
				}
				echo "<p>Consult the Pantheon Documentation for more information on <a href='https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters/content-preview' target='_blank' rel='noopener noreferrer'>configuring content preview</a>.</p>\n";
			} else {
				// Success!
				echo "<p>WordPress was able to communicate with your preview site and preview example content.</p>\n";
				if ( isset( $body['message'] ) ) {
					echo '<p>' . esc_html(
					// Translators: %s is the response code.
						sprintf( __( 'Code: %s', 'wp-decoupled-preview' ), $response['response']['code'] )
					) . "</p>\n";
					echo '<p>' . esc_html(
					// Translators: %s is the error message.
						sprintf( __( 'Message: %s', 'wp-decoupled-preview' ), $body['message'] )
					) . "</p>\n";
				}
			}
			?>
		</div>
	<?php
}

/**
 * Modal to display environment variables.
 *
 * @return void
 */
function pantheon_decoupled_env_vars() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}
	check_admin_referer( 'env-vars', 'nonce' );

	// Get data for preview site.
	$id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : null;
	$preview_sites = get_option( 'preview_sites' );
	$preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : null;

	global $wp;
	$home_url = home_url( $wp->request );

	?>
<style>
	/*
	Tweak styles for ajax version of thickbox.
	*/
	#TB_window {
		background-color: rgb(240, 240, 241);
	}
	#TB_window #adminmenumain,
	#TB_window #wpfooter {
		display: none;
	}
	#TB_window #wpcontent {
		margin-left: 0;
	}
	</style>
		<div class="wrap">
			<h1><?php esc_html_e( 'Environment Variables', 'wp-pantheon-decoupled' ); ?></h1>
			<h4>PREVIEW_SECRET</h4>
			<?php
			echo '<p>' . esc_html(
					// Translators: %s is the preview site secret.
				sprintf( __( 'Value: %s', 'wp-decoupled-preview' ), $preview_site['secret_string'] )
			) . "</p>\n";
			?>
			<h4>WP_APPLICATION_USERNAME</h4>
			<?php
			echo '<p>' . esc_html(
					// Translators: %s is the associated user.
				sprintf( __( 'Value: %s', 'wp-decoupled-preview' ), $preview_site['associated_user'] )
			) . "</p>\n";
			?>
			<h4>WP_APPLICATION_PASSWORD</h4>
			<p>The application password associated with this user intended to be used with this preview site.</p>
			<h4>Linked CMS</h4>
			<?php
			echo '<p>Link the CMS site that relates to: <strong>' . esc_html(
					// Translators: %s is the home url.
				sprintf( __( '%s', 'wp-decoupled-preview' ), $home_url )
			) . "</strong></p>\n";
			?>
		</div>
	<?php
}

/**
 * Display post install admin notice.
 *
 * @return void
 */
function pantheon_decoupled_admin_notice() {
	$add_site_url = wp_nonce_url(
		add_query_arg( [
			'page' => 'pantheon-front-end-sites',
		], admin_url( 'options-general.php' ) ),
		'pantheon-front-end-sites',
		'nonce'
	);

	if ( ! get_transient( 'post_install_next_steps' ) ) {
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
		set_transient( 'post_install_next_steps', true );
	}
}

/**
 * Callback to redirect to FES settings page when preview sites are updated.
 *
 * @return void
 */
function pantheon_decoupled_redirect_to_fes() {
	// If options are being edited on FES settings page, redirect there when
	// option is updated.
	// A nonce was already used on the options page and we're sanitizing the query
	// param, so we can safely ignore the phpcs warning.
  // phpcs:disable WordPress.Security.NonceVerification.Recommended
	$is_fes = isset( $_GET['fes'] ) ? absint( sanitize_text_field( $_GET['fes'] ) ) : null;
	if ( $is_fes ) {
		echo '<script type="text/javascript">window.location = "options-general.php?page=pantheon-front-end-sites"</script>';
		exit;
	}
}

/**
 * Form to delete a preview site in a modal.
 *
 * @return void
 */
function pantheon_decoupled_preview_delete() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}
	check_admin_referer( 'edit-preview-site', 'nonce' );
	$edit_id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : false;
	if ( $edit_id ) {
		$action = 'options.php?fes=1&edit=' . $edit_id;
	} else {
		$action = 'options.php?fes=1';
	}

	$preview_sites = get_option( 'preview_sites' );
	$preview_site = isset( $preview_sites['preview'][ $edit_id ] ) ? $preview_sites['preview'][ $edit_id ] : null;

	?>
		<style>
		/*
		Styles here are for ajax version of thickbox. We lose some styles, but gain
		a loading indicator...
	 */
		#TB_window {
		background-color: rgb(240, 240, 241);
		}
		#TB_window #adminmenumain,
		#TB_window #wpfooter {
		display: none;
		}
		#TB_window #wpcontent {
		margin-left: 0;
		}
	</style>
		<div class="wrap">
		<h1><?php esc_html_e( 'Delete Preview Site', 'wp-pantheon-decoupled' ); ?></h1>
		<form action="<?php echo esc_url( $action ); ?>" method="post">
		<?php
		if ( $edit_id ) {
			$site_label = $preview_site['label'];
			$url = wp_nonce_url(
				add_query_arg( [
					'page' => 'delete_preview_site',
					'id' => $edit_id,
				], admin_url( $action ) ),
				'edit-preview-site',
				'nonce'
			);
			?>
			<a id="delete-preview" class="button-secondary button-large" href="<?php echo esc_url( $url ); ?>">
			<?php
			echo esc_html(
				// Translators: %s is the preview site label.
				sprintf( __( 'Delete %s', 'wp-decoupled-preview' ), $site_label )
			);

			?>
			</a>
			<?php
		}
		?>
		</form>
		</div>
	<?php
}

/**
 * Deletes a preview site
 *
 * @return void
 */
function pantheon_decoupled_delete_success() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}

	check_admin_referer( 'edit-preview-site', 'nonce' );
	$delete_id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : null;

	if ( ! $delete_id ) {
		wp_die( esc_html__( 'Unable perform action: Site not found.', 'wp-decoupled-preview' ) );
	}
	$wp_preview_delete = new Decoupled_Preview_Settings();
	$wp_preview_delete->delete_preview_site( $delete_id );
}

/**
 * Form to regenerate environment variables.
 *
 * @return void
 */
function pantheon_decoupled_regen_env_vars() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}
	check_admin_referer( 'env-regen', 'nonce' );

	// Get data for preview site.
	$id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : null;
	$preview_sites = get_option( 'preview_sites' );
	$preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : null;

	$docs_url = 'https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters/content-preview'

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
		<h1><?php esc_html_e( 'Regenerate Environment Variables', 'wp-pantheon-decoupled' ); ?></h1>
		<?php
		if ( $id ) {
			$site_label = $preview_site['label'];
			$url = wp_nonce_url( add_query_arg( [
				'page' => 'env_regen_action',
				'id' => $id,
			], admin_url( 'options-general.php' ) ), 'env-regen', 'nonce' );

			if ( ! $preview_site['associated_user'] ) {
				echo '<p>A preview site must have an associated user to generate an application password.';
			}
			?>

		<a id="regen-password" <?php echo $preview_site['associated_user'] ? 'class="button-secondary button-large thickbox" href="' . esc_url( $url ) . '&TB_iframe=true&width=600&height=500"' : 'class="button-secondary button-large" disabled="true"'; ?>>
			<?php
			echo esc_html(
			// Translators: %s is the preview site label.
				sprintf( __( 'Regenerate %s WP_APPLICATION_PASSWORD', 'wp-decoupled-preview' ), $site_label )
			);
			?>
		</a>
		<p>Consult the Pantheon Documentation for more information on
			<a id="docs-link" target="_blank" rel='noopener noreferrer' href="<?php echo esc_url( $docs_url ); ?>">
		configuring content preview</a>.
		</p>
			<?php
		}
		?>
	</div>
	<?php
}

/**
 * Form that displays results of regenerated environment variables.
 *
 * @return void
 */
function pantheon_decoupled_regen_env_vars_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-decoupled-preview' ) );
	}
	check_admin_referer( 'env-regen', 'nonce' );
	$id = isset( $_GET['id'] ) ? absint( sanitize_text_field( $_GET['id'] ) ) : null;
	$preview_sites = get_option( 'preview_sites' );
	$preview_site = isset( $preview_sites['preview'][ $id ] ) ? $preview_sites['preview'][ $id ] : null;

	$app_password = WP_Application_Passwords::create_new_application_password( get_user_by( 'login', $preview_site['associated_user'] )->ID, [ 'name' => 'preview-' . wp_generate_uuid4() ] );

	$site_label = $preview_site['label'];
	$docs_url = 'https://docs.pantheon.io/guides/decoupled/wp-nextjs-frontend-starters/content-preview'

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
		<h1><?php esc_html_e( 'Regenerate Environment Variables', 'wp-pantheon-decoupled' ); ?></h1>
		<p>
			<label for="new-application-password-value">
			The password of the <strong>
			<?php
			echo esc_html(
				// Translators: %s is the preview site label.
				sprintf( __( '%s', 'wp-decoupled-preview' ), $site_label )
			);
			?>
			</strong> site is:
			</label>
		</p>
		<input type="text" class="code" size="30" value="<?php printf( esc_attr( WP_Application_Passwords::chunk_password( $app_password[0] ) ) ); ?>" />
		<p>Consult the Pantheon Documentation for more information on
			<a id="docs-link" target="_blank" rel='noopener noreferrer' href="<?php echo esc_url( $docs_url ); ?>">
		configuring content preview</a>.
		</p>
		</p>
	</div>
	<?php
}

add_action( 'admin_notices', 'pantheon_decoupled_admin_notice' );
add_action( 'init', 'pantheon_decoupled_enable_deps' );
add_action( 'admin_menu', 'pantheon_decoupled_settings_init' );
add_action( 'update_option_preview_sites', 'pantheon_decoupled_redirect_to_fes' );
add_action( 'admin_menu', 'remove_preview_sites_submenu', 999 );
