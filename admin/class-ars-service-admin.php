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

		global $wpdb;

		$data = [
			'client_name'        => isset( $_POST['client_name'] ) ? $this->validate_text_field( $_POST['client_name'], 250, '"ФИО"' ) : '',
			'address'            => isset( $_POST['address'] ) ? $this->validate_text_field( $_POST['address'], 250, '"Адрес"' ) : '',
			'phone'              => isset( $_POST['phone'] ) ? $this->validate_text_field( $_POST['phone'], 25, '"Телефон"' ) : '',
			'document'           => isset( $_POST['document'] ) ? $this->validate_text_field( $_POST['document'], 250, '"Документ"' ) : '',
			'reported_failure'   => isset( $_POST['reported_failure'] ) ? $this->validate_text_field( $_POST['reported_failure'], 500, '"Заявленная неисправность"' ) : '',
			'complete_comment'   => isset( $_POST['complete_comment'] ) ? $this->validate_text_field( $_POST['complete_comment'], 500, '"Комплектность Другое"', true ) : '',
			'appearance_comment' => isset( $_POST['appearance_comment'] ) ? $this->validate_text_field( $_POST['appearance_comment'], 500, '"Внешний вид Другое"', true ) : '',
			'device'             => isset( $_POST['device'] ) ? $this->validate_text_field( $_POST['device'], 500, '"Устройство"' ) : '',
			'price'              => isset( $_POST['price'] ) ? $this->validate_price( $_POST['price'] ) : '',
			'status'             => isset( $_POST['status'] ) ? $this->validate_status( $_POST['status'] ) : '',
		];

		// add checkboxes
		if ( isset( $_POST['checkboxes'] ) && is_array( $_POST['checkboxes'] ) && ! empty( $_POST['checkboxes'] ) ) {
			$data['additional_info'] = serialize( $_POST['checkboxes'] );
		}

		// check id
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {
			$data['id']      = intval( $_POST['id'] );
			$data['sn']      = isset( $_POST['sn'] ) ? $this->validate_sn( $_POST['sn'], $_POST['id'] ) : '';
			$success_message = 'Запись обновлена успешно';
		} else {
			$data['sn']      = isset( $_POST['sn'] ) ? $this->validate_sn( $_POST['sn'] ) : '';
			$success_message = 'Запись создана успешно';
		}

		if ( isset( $_POST['date'] ) && ! empty( $_POST['date'] ) ) {
			$data['date'] = $_POST['date'];
		}

		// todo prepare format
		$insert = $wpdb->replace( $wpdb->prefix . 'ars_orders', $data );

		if ( $insert === false ) {
			wp_send_json_error( [ 'message' => 'Произошла ошибка #3. Пожалуйста, попробуйте позже.' ] );
		}

		if ( $insert === 0 ) {
			wp_send_json_error( [ 'message' => 'Не было сделалано изменений' ] );
		}

		$inserted_id = $wpdb->insert_id;

		if ( ! $inserted_id ) {
			wp_send_json_error( [ 'message' => 'Произошла ошибка #1. Пожалуйста, попробуйте позже.' ] );
		}

		// insert comment
		if ( isset( $_POST['comment'] ) && ! empty( $_POST['comment'] ) ) {
			$comment_data = [
				'order_id' => $inserted_id,
				'comment'  => isset( $_POST['comment'] ) ? $this->validate_text_field( $_POST['comment'], 500, '"Комментарий"', true ) : '',
			];
			$wpdb->insert( $wpdb->prefix . 'ars_comments', $comment_data );
		}

		$form = $this->generate_form( $inserted_id );

		if ( ! $form ) {
			wp_send_json_error( [ 'message' => 'Произошла ошибка #2. Пожалуйста, попробуйте позже.' ] );
		}

		wp_send_json_success( [ 'message' => $success_message, 'form_html' => $form ] );
	}

	private function validate_text_field( $text = null, $length = 0, $error_message_field = null, $length_only = false ) {

		if ( ! $length_only && empty( $text ) ) {
			wp_send_json_error( [ 'message' => 'Поле ' . $error_message_field . ' не заполнено.' ] );
		}

		if ( $length && mb_strlen( $text ) > $length ) {
			wp_send_json_error( [ 'message' => 'Длина поля превышает допустимое значение. Поле ' . $error_message_field . ' не может быть длиннее ' . $length . ' символов.' ] );
		}

		return sanitize_text_field( $text );
	}

	private function validate_status( $status = 0 ) {

		if ( ! $status || $status < 1 || $status > 4 ) {
			wp_send_json_error( [ 'message' => 'Выберете статус' ] );
		}

		return intval( $status );
	}

	private function validate_price( $price = 0 ) {

		if ( $price && strlen( (string) $price ) > 6 ) {
			wp_send_json_error( [ 'message' => 'Цена не может быть более 5 символов' ] );
		}

		return intval( $price );
	}

	private function validate_sn( $sn = null, $id = null ) {

		global $wpdb;

		if ( empty( $sn ) ) {
			wp_send_json_error( [ 'message' => 'Поле SN должно присутствовать' ] );
		}

		if ( strlen( $sn ) > 50 ) {
			wp_send_json_error( [ 'message' => 'Поле SN не может быть длиннее 50 символов' ] );
		}

		$sql = "SELECT id FROM {$wpdb->prefix}ars_orders WHERE sn = %s AND id != %d LIMIT 1";

		$query = $wpdb->prepare( $sql, $sn, $id ? intval( $id ) : 0 );

		$existing_sn = $wpdb->get_var( $query );

		if ( $existing_sn ) {
			wp_send_json_error( [ 'message' => 'Поле SN должно быть уникальным' ] );
		}

		return sanitize_text_field( $sn );
	}

	public function delete_order() {

		if ( empty( $_POST ) ) {
			wp_send_json_error( [ 'message' => 'Empty POST' ] );
		}

		// check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ars_service_nonce' ) ) {
			wp_send_json_error( [ 'message' => 'Nonce error' ] );
		}

		$id = intval( $_POST['service_id'] );

		if ( ! $id ) {
			wp_send_json_error( [ 'message' => 'ID is empty' ] );
		}

		// delete record from orders
		// delete record from comments
		global $wpdb;
		$delete_order    = $wpdb->delete( $wpdb->prefix . 'ars_orders', [ 'id' => $id ] );
		$delete_comments = $wpdb->delete( $wpdb->prefix . 'ars_comments', [ 'order_id' => $id ] );

		if ( $delete_order === false || $delete_comments === false ) {
			wp_send_json_error( [ 'message' => 'Произошла ошибка #4. Пожалуйста, попробуйте позже.' ] );
		}

		wp_send_json_success( [ 'message' => 'Запись удалена успешно' ] );

	}

	public function generate_form( $order_id = null ) {

		$order = $additional_info = $comments = [];

		// get data if we have get parameter
		if ( isset( $_GET['ars-order'] ) || $order_id ) {

			$id = $order_id ? $order_id : ( isset( $_GET['ars-order'] ) && $_GET['ars-order'] ? $_GET['ars-order'] : null );

			$id = intval( $id );

			global $wpdb;

			// get data from database
			$order = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ars_orders WHERE id = %d", $id ), ARRAY_A );

			if ( $order && is_array( $order ) && ! empty( $order ) ) {
				$additional_info = unserialize( $order['additional_info'] );
				$additional_info = is_array( $additional_info ) ? $additional_info : [];
			}

			// get comments
			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ars_comments WHERE order_id = %d ORDER BY date DESC", $id ), ARRAY_A );

		}

		if ( ! $order ) {
			$order = [];
			$title = __( 'Создать новый заказ', 'ars-service' );
		} else {
			$title = __( 'Редактировать заказ', 'ars-service' );
		}

		if ( ! $comments ) {
			$comments = [];
		}

		ob_start(); ?>

        <form id='ars_order_form' class='ars_order_form'>

            <h1><?php echo $title; ?></h1>

            <div class="ars_form_block">

                <h2 class="ars_form_group_heading"><?php _e( 'Информация о клиенте', 'ars-service' ); ?></h2>

                <div class="ars_form_group">
                    <label for="client_name"><?php _e( 'ФИО *', 'ars-service' ); ?></label>
                    <input type="text" id="client_name" name="client_name"
                           value="<?php echo isset( $order['client_name'] ) && $order['client_name'] ? $order['client_name'] : ''; ?>"
                           required>
                </div>

                <div class="ars_form_group">
                    <label for="address"><?php _e( 'Адрес *', 'ars-service' ); ?></label>
                    <input type="text" id="address" name="address"
                           value="<?php echo isset( $order['address'] ) && $order['address'] ? $order['address'] : ''; ?>"
                           required>
                </div>

            </div>

            <div class="ars_form_block">

                <div class="ars_form_group">
                    <label for="phone"><?php _e( 'Телефон *', 'ars-service' ); ?></label>
                    <input type="text" id="phone" name="phone"
                           value="<?php echo isset( $order['phone'] ) && $order['phone'] ? $order['phone'] : ''; ?>"
                           required>
                </div>

                <div class="ars_form_group">
                    <label for="document"><?php _e( 'Документ *', 'ars-service' ); ?></label>
                    <input type="text" id="document" name="document"
                           value="<?php echo isset( $order['document'] ) && $order['document'] ? $order['document'] : ''; ?>"
                           required>
                </div>

            </div>

            <div class="ars_form_block">

                <h2 class="ars_form_group_heading"><?php _e( 'Информация о заказе', 'ars-service' ); ?></h2>

                <div class="ars_form_group">
                    <label for="id"><?php _e( 'ID', 'ars-service' ); ?></label>
                    <input type="text" id="id" name="id"
                           value="<?php echo isset( $order['id'] ) && $order['id'] ? $order['id'] : ''; ?>"
                           readonly>
                </div>

                <div class="ars_form_group">
                    <label for="device"><?php _e( 'Устройство *', 'ars-service' ); ?></label>
                    <input type="text" id="device" name="device"
                           value="<?php echo isset( $order['device'] ) && $order['device'] ? $order['device'] : ''; ?>"
                           required>
                </div>

            </div>

            <div class="ars_form_block">

                <div class="ars_form_group">
                    <label for="date"><?php _e( 'Дата создания', 'ars-service' ); ?></label>
                    <input type="text" id="date" name="date"
                           value="<?php echo isset( $order['date'] ) && $order['date'] ? $order['date'] : ''; ?>"
                           readonly>
                </div>

                <div class="ars_form_group">
                    <label for="price"><?php _e( 'Стоимость', 'ars-service' ); ?></label>
                    <input type="number" id="price" name="price"
                           value="<?php echo isset( $order['price'] ) && $order['price'] ? $order['price'] : ''; ?>"
                           min="0">
                </div>

            </div>

            <div class="ars_form_block">

                <div class="ars_form_group">
                    <label for="sn"><?php _e( 'S/N *', 'ars-service' ); ?></label>
                    <input type="text" id="sn" name="sn"
                           value="<?php echo isset( $order['sn'] ) && $order['sn'] ? $order['sn'] : ''; ?>"
                           required>
                </div>

				<?php $status = isset( $order['status'] ) && $order['status'] ? intval( $order['status'] ) : 0; ?>

                <div class="ars_form_group">
                    <label for="status"><?php _e( 'Статус *', 'ars-service' ); ?></label>
                    <select id="status" name="status" required>
                        <option value="0" <?php echo $status === 0 ? 'selected' : ''; ?> ></option>
                        <option value="1" <?php echo $status === 1 ? 'selected' : ''; ?> ><?php _e( 'Принят на сервис', 'ars-service' ); ?></option>
                        <option value="2" <?php echo $status === 2 ? 'selected' : ''; ?> ><?php _e( 'Ожидает детали', 'ars-service' ); ?></option>
                        <option value="3" <?php echo $status === 3 ? 'selected' : ''; ?> ><?php _e( 'Готов', 'ars-service' ); ?></option>
                        <option value="4" <?php echo $status === 4 ? 'selected' : ''; ?> ><?php _e( 'Выдан клиенту', 'ars-service' ); ?></option>
                    </select>
                </div>

            </div>

            <div class="ars_form_block">

                <h2 class="ars_form_group_heading"><?php _e( 'Сведения об устройстве', 'ars-service' ); ?></h2>

                <div class="ars_form_group ars_form_checkboxes_group">

                    <h4 class="ars_form_group_heading"><?php _e( 'Комплектность', 'ars-service' ); ?></h4>

                    <div class="ars_form_checkboxes_block">

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="tv" <?php echo in_array( "tv", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Телевизор', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="monitor" <?php echo in_array( "monitor", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Монитор', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="laptop" <?php echo in_array( "laptop", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Ноутбук', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="tablet" <?php echo in_array( "tablet", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Планшет', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="cellphone" <?php echo in_array( "cellphone", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Телефон', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="system_block" <?php echo in_array( "system_block", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Системный блок', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="printer" <?php echo in_array( "printer", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Принтер', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="laser_cartridge" <?php echo in_array( "laser_cartridge", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Лазерный картридж', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="inkjet_cartridge" <?php echo in_array( "inkjet_cartridge", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Струйный картридж', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="cables" <?php echo in_array( "cables", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Кабеля', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="box" <?php echo in_array( "box", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Коробка', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="packaging" <?php echo in_array( "packaging", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Упаковка', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="charger" <?php echo in_array( "charger", $additional_info ) ? 'checked' : ''; ?> > <?php _e( 'Зарядное устройство', 'ars-service' ); ?>
                        </label>

                    </div>

                    <div class="ars_form_group">
                        <label for="complete_comment"><?php _e( 'Другое', 'ars-service' ); ?></label>
                        <textarea type="text" id="complete_comment"
                                  name="complete_comment"><?php echo isset( $order['complete_comment'] ) && $order['complete_comment'] ? $order['complete_comment'] : ''; ?></textarea>
                    </div>

                </div>

                <div class="ars_form_group ars_form_checkboxes_group">

                    <h4 class="ars_form_group_heading"><?php _e( 'Внешний вид', 'ars-service' ); ?></h4>

                    <div class="ars_form_checkboxes_block">

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="clear_signs_of_use" <?php echo in_array( "clear_signs_of_use", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Явные следы эксплуатации', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="no_visible_signs_of_use" <?php echo in_array( "no_visible_signs_of_use", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Без видимых следов эксплуатации', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="layering_of_sweat_marks" <?php echo in_array( "layering_of_sweat_marks", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Наслоение потожировых следов', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="moisture_marks" <?php echo in_array( "moisture_marks", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Следы влаги', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="dents" <?php echo in_array( "dents", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Вмятины', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="minor_scratches" <?php echo in_array( "minor_scratches", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Мелкие царапины', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="deep_scratches" <?php echo in_array( "deep_scratches", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Глубокие царапины', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="external_damage" <?php echo in_array( "external_damage", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Внешние повреждения', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="deposition_of_foreign_matter" <?php echo in_array( "deposition_of_foreign_matter", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Наслоение стороннего вещества', 'ars-service' ); ?>
                        </label>

                        <label>
                            <input type="checkbox" name="ars_checkboxes"
                                   value="shock_drop_marks" <?php echo in_array( "shock_drop_marks", $additional_info ) ? 'checked' : ''; ?> >
							<?php _e( 'Следы ударов/падения', 'ars-service' ); ?>
                        </label>

                    </div>


                    <div class="ars_form_group">
                        <label for="appearance_comment"><?php _e( 'Другое', 'ars-service' ); ?></label>
                        <textarea id="appearance_comment"
                                  name="appearance_comment"><?php echo isset( $order['appearance_comment'] ) && $order['appearance_comment'] ? $order['appearance_comment'] : ''; ?></textarea>
                    </div>

                </div>

            </div>

            <div class="ars_form_block">
                <div class="ars_form_group">
                    <h4 class="ars_form_group_heading"><?php _e( 'Заявленная неисправность *', 'ars-service' ); ?></h4>
                    <textarea id="reported_failure" name="reported_failure"
                              required><?php echo isset( $order['reported_failure'] ) && $order['reported_failure'] ? $order['reported_failure'] : ''; ?></textarea>
                </div>

				<?php if ( empty( $comments ) ) : ?>
                    <div class="ars_form_group">
                        <h4 class="ars_form_group_heading"><?php _e( 'Комментарий', 'ars-service' ); ?></h4>
                        <textarea id="comment" name="comment"></textarea>
                    </div>
				<?php endif; ?>

            </div>

			<?php if ( ! empty( $comments ) ) : ?>

                <h4 class="ars_form_group_heading"><?php _e( 'Комментарии', 'ars-service' ); ?></h4>

                <div class="ars_form_block">
                <textarea id="comment"
                          name="comment"
                          placeholder="<?php _e( 'Написать комментарий...', 'ars-service' ); ?>"></textarea>
                </div>

				<?php foreach ( $comments as $comment ) :
					$date = new DateTime( $comment['date'] );
					$date_text = $date->format( 'd.m.Y H:i' ); ?>
                    <div class="ars_form_block">
                        <div class="ars_comment">
                            <p><?php echo $comment['comment']; ?></p>
                            <span><?php echo $date_text; ?></span>
                        </div>
                    </div>
				<?php endforeach; ?>

			<?php endif; ?>

            <div class="ars_form_block ars_form_button_block">
                <button type="reset" class="button button-secondary"><?php _e( 'Отменить', 'ars-service' ); ?></button>
                <button type="submit" class="button button-primary"><?php _e( 'Сохранить', 'ars-service' ); ?></button>
            </div>

            <div class="ars_loader_wrapper">
                <div class="ars_loader"></div>
            </div>

        </form>
		<?php return ob_get_clean();

	}


}
