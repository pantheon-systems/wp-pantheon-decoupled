<?php
/**
 * List table for displaying the list of sites.
 *
 * @package Pantheon_Decoupled
 */

use Pantheon\DecoupledPreview\List_Table;

/**
 * List table for displaying the list of sites.
 */
class FES_Preview_Table extends List_Table {
  // phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.stringFound
	/**
	 * Render a column value.
	 *
	 * @param object $item        The item to render.
	 * @param string $column_name The column name.
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'label':
			case 'url':
			case 'associated_user':
			case 'preview_type':
				return isset( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : '';
			case 'content_type':
				return isset( $item['content_type'] ) ? ucwords( implode( ', ', $item['content_type'] ) ) : __( 'Post, Page', 'wp-decoupled-preview' );
			case 'actions':
				return sprintf(
					'<a href="%s&width=600&height=500" class="thickbox">%s</a> | <a href="%s&width=600&height=500" class="thickbox">%s</a> | <a href="%s&width=600&height=500" class="thickbox">%s</a> | <a href="%s&width=600&height=500" class="thickbox">%s</a> | <a href="%s&TB_iframe=true&width=600&height=500" class="thickbox">%s</a>',
					wp_nonce_url( add_query_arg( [
						'page' => 'fes_add_preview_sites',
						'action' => 'edit',
						'id' => $item['id'],
					], admin_url( 'options-general.php' ) ), 'edit-preview-site', 'nonce' ),
					esc_html__( 'Edit', 'wp-decoupled-preview' ),
					wp_nonce_url( add_query_arg( [
						'page' => 'test_preview_site',
						'action' => 'test',
						'id' => $item['id'],
					], admin_url( 'options-general.php' ) ), 'test-preview-site', 'nonce' ),
					esc_html__( 'Test', 'wp-decoupled-preview' ),
					wp_nonce_url( add_query_arg( [
						'page' => 'env_vars',
						'action' => 'env',
						'id' => $item['id'],
					], admin_url( 'options-general.php' ) ), 'env-vars', 'nonce' ),
					esc_html__( 'Environment Variables', 'wp-decoupled-preview' ),
					wp_nonce_url( add_query_arg( [
						'page' => 'preview_delete',
						'action' => 'delete',
						'id' => $item['id'],
					], admin_url( 'options-general.php' ) ), 'edit-preview-site', 'nonce' ),
					esc_html__( 'Delete', 'wp-decoupled-preview' ),
					wp_nonce_url( add_query_arg([
						'page' => 'env_regen',
						'action' => 'regen',
						'id' => $item['id'],
					], admin_url( 'options-general.php' ) ), 'env-regen', 'nonce' ),
					esc_html__( 'Regenerate Variables', 'wp-decoupled-preview' ),
				);
			default:
				return '';
		}
	}
}
