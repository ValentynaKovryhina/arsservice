<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://imaris-agentur.de/
 * @since      1.0.0
 *
 * @package    Ars_Service
 * @subpackage Ars_Service/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ars_Service
 * @subpackage Ars_Service/admin
 * @author     Imaris <info@imaris.ua>
 */
class Ars_Service_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ars_Service_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ars_Service_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ars-service-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ars_Service_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ars_Service_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

//		$ver = $this->version;
		$ver = time();
		wp_enqueue_script( 'ars-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/ars-service-admin.js', [ 'jquery' ], $ver, false );

		wp_localize_script( 'ars-admin-scripts', 'ars_service', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ars_service_nonce' ),
		] );

	}

	public function add_plugin_page() {

		add_menu_page(
			__( 'Ars Service', 'ars-service' ),
			__( 'Ars Service', 'ars-service' ),
			'manage_options',
			'ars-service-info',
			function () {
				include 'partials/ars-service-admin-info-page.php';
			},
			'dashicons-admin-generic'
		);

		add_submenu_page(
			'ars-service-info',
			__( 'Create Order', 'ars-service' ),
			__( 'Create Order', 'ars-service' ),
			'manage_options',
			'ars-create-order',
			function () {
				include 'partials/ars-service-admin-create-order-page.php';
			}
		);

		add_submenu_page(
			'ars-service-info',
			__( 'Logs', 'ars-service' ),
			__( 'Logs', 'ars-service' ),
			'manage_options',
			'ars-service-logs',
			function () {
				include 'partials/ars-service-admin-logs-page.php';
			}
		);

	}

	public function create_update_order() {
		// handler for ajax ars_order_form_action

		if ( empty( $_POST ) ) {
			wp_send_json_error( [ 'message' => 'Empty POST' ] );
		}

		// check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ars_service_nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Nonce error' ] );
		}

		// TODO Validation
		// TODO check that S/N is unique
		// TODO move data to separate function

		global $wpdb;


		$data = [
			'sn'                 => sanitize_text_field( $_POST['sn'] ),
			'client_name'        => sanitize_text_field( $_POST['client_name'] ),
			'address'            => sanitize_text_field( $_POST['address'] ),
			'phone'              => sanitize_text_field( $_POST['phone'] ),
			'document'           => sanitize_text_field( $_POST['document'] ),
			'reported_failure'   => sanitize_text_field( $_POST['reported_failure'] ),
			'comment'            => sanitize_text_field( $_POST['comment'] ),
			'complete_comment'   => sanitize_text_field( $_POST['complete_comment'] ),
			'appearance_comment' => sanitize_text_field( $_POST['appearance_comment'] ),
			'device'             => sanitize_text_field( $_POST['device'] ),
			'price'              => intval( $_POST['price'] ),
			'status'             => intval( $_POST['status'] ),
		];

		// check id
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {
			$data['id']      = intval( $_POST['id'] );
			$success_message = 'Запись обновлена успешно';
		} else {
			$success_message = 'Запись создана успешно';
		}

		if ( isset( $_POST['date'] ) && ! empty( $_POST['date'] ) ) {
			$data['date'] = $_POST['date'];
		}

		// todo prepare format
		$insert = $wpdb->replace( $wpdb->prefix . 'ars_orders', $data );

		if ( $insert === false ) {
			wp_send_json_error( [ 'message' => 'Произошла ошибка. Пожалуйста, попробуйте позже.' ] );
		}

		if ( $insert === 0 ) {
			wp_send_json_error( [ 'message' => 'Не было сделалано изменений' ] );
		}

		$inserted_id = $wpdb->insert_id;
		wp_send_json_success( [ 'message' => $success_message, 'id' => $inserted_id, 'post' => $_POST ] );


	}


}
