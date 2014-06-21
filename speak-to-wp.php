<?php

/**
 * Plugin Name: Speak to WP
 * Plugin URI: http://wordpress.org/plugins/speak-to-wp
 * Description: Give your visitors the ability to navigate your site by speaking to it!
 * Version: 1.0
 * Author: David Michael Ross
 * Author URI: http://davidmichaelross.com
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: speaktowp
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) die( 'Cannot access pages directly.' );

define( 'SPEAK_TO_WP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SPEAK_TO_WP_URL', plugin_dir_url( __FILE__ ) );

class SpeakToWP {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_ajax_nopriv_speaktowp', array( $this, 'wp_ajax_nopriv_speaktowp' ) );
	}

	public function wp_enqueue_scripts() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$annyang_script_src     = SPEAK_TO_WP_URL . 'vendor/annyang/annyang.js';
			$speak_to_wp_script_src = SPEAK_TO_WP_URL . 'assets/js/speak-to-wp.js';
		}
		else {
			$annyang_script_src     = SPEAK_TO_WP_URL . 'vendor/annyang/annyang.min.js';
			$speak_to_wp_script_src = SPEAK_TO_WP_URL . 'assets/js/speak-to-wp.js';
		}

		wp_enqueue_script( 'annyang', $annyang_script_src, array(), '1.1.0', true );
		wp_enqueue_script( 'speak-to-wp', $speak_to_wp_script_src, array( 'annyang', 'jquery' ), '1.0', true );

		$settings = array(
				'home_url'   => home_url( '/' ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'login_url'  => wp_login_url(),
				'logout_url' => wp_logout_url(),
		);

		wp_localize_script( 'annyang', 'AnnyangSettings', $settings );

	}

	public function wp_ajax_nopriv_speaktowp() {

		global $wpdb;
		$action = $_GET['speak-to-wp-action'];

		// Whitelist
		if ( 'sanitize-search' === $action ) {

			wp_send_json_success( wp_kses( $_GET['search-query'], array() ) );

		}
		elseif ( 'post-by-title' === $action ) {

			// Get a list of all the registered post types
			$post_types = get_post_types(
					array(
							'publicly_queryable' => true,

					),
					'names'
			);

			// Except 'attachment' because we know it's not a real post
			unset( $post_types['attachment'] );

			$post = get_page_by_title( $_GET['search-query'], OBJECT, $post_types );
			if ( $post ) {
				wp_send_json_success( get_the_permalink( $post->ID ) );
			}

		}

		wp_send_json_error();

	}

}

new SpeakToWP();