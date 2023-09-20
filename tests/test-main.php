<?php


/**
 * Class Test_Main
 *
 * @package Pantheon_Decoupled
 */


require __DIR__ . '/../pantheon-decoupled-example.php';

use WP_UnitTestCase;

class Test_Main extends WP_UnitTestCase {

	/**
	 * Test the set_default_options() and delete_default_options() functions.
	 *
	 * @covers \Pantheon\DecoupledExample\set_default_options()
	 * @covers \Pantheon\DecoupledExample\delete_default_options()
	 *
	 * @return void
	 */
	public function test_default_options(): void {
		// Set the default options.
		set_default_options();
		$options = get_option( 'preview_sites' );
		$this->assertNotEmpty( $options );

		// Ensure that the transient was set.
		$transient = get_transient( 'example_preview_password' );
		$this->assertNotEmpty( $transient );

		// Ensure that the options were set.
		$this->assertNotEmpty( $options );
		$this->assertArrayHasKey( 'preview', $options );
		$this->assertArrayHasKey( 'label', $options['preview'][1] );
		$this->assertEquals( 'Example NextJS Preview', $options['preview'][1]['label'] );
		$this->assertArrayHasKey( 'url', $options['preview'][1] );
		$this->assertEquals( 'https://example.com/api/preview', $options['preview'][1]['url'] );
		$this->assertArrayHasKey( 'secret_string', $options['preview'][1] );
		$this->assertEquals( $transient, $options['preview'][1]['secret_string'] );
		$this->assertArrayHasKey( 'preview_type', $options['preview'][1] );
		$this->assertEquals( 'Next.js', $options['preview'][1]['preview_type'] );
		$this->assertArrayHasKey( 'id', $options['preview'][1] );
		$this->assertEquals( 1, $options['preview'][1]['id'] );
		$this->assertArrayHasKey( 'associated_user', $options['preview'][1] );
		$this->assertEquals( 'decoupled_example_user', $options['preview'][1]['associated_user'] );

		// Delete the options.
		delete_default_options();
		$options = get_option( 'preview_sites' );

		// Ensure that the options were deleted.
		$this->assertEmpty( $options );
	}
}
