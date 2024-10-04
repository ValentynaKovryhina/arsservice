<?php

/**
 * Fired during plugin activation
 *
 * @link       https://imaris-agentur.de/
 * @since      1.0.0
 *
 * @package    Ars_Service
 * @subpackage Ars_Service/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ars_Service
 * @subpackage Ars_Service/includes
 * @author     Imaris <info@imaris.ua>
 */

use Ars_Service_Logger as Logger;

class Ars_Service_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Create tables for orders and comments
		global $wpdb;

		$table_orders    = $wpdb->prefix . 'ars_orders';
		$table_comments  = $wpdb->prefix . 'ars_comments';
		$charset_collate = $wpdb->get_charset_collate();

		$sql_orders = "CREATE TABLE $table_orders (
        id int(6) NOT NULL AUTO_INCREMENT,
        sn varchar(50) NOT NULL,
        client_name varchar(250) DEFAULT NULL,
        address varchar(250) DEFAULT NULL,
        phone varchar(25) DEFAULT NULL,
        document varchar(250) DEFAULT NULL,
        device varchar(250) DEFAULT NULL,
        price int(6) DEFAULT NULL,
        status int(1) DEFAULT NULL,
        reported_failure varchar(1000) DEFAULT NULL,
        complete_comment varchar(1000) DEFAULT NULL,
        appearance_comment varchar(1000) DEFAULT NULL,
        additional_info varchar(2000) DEFAULT NULL,
        date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY sn (sn)
    ) $charset_collate;";

		$sql_comments = "CREATE TABLE $table_comments (
        id int(8) NOT NULL AUTO_INCREMENT,
        order_id int(6) DEFAULT NULL,
        comment varchar(1000) DEFAULT NULL,
        date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

		// Подключение необходимых файлов для выполнения dbDelta
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Создание таблиц
		$create_table_orders   = dbDelta( $sql_orders );
		$create_table_comments = dbDelta( $sql_comments );

		// Логирование результата создания таблиц
		if ( is_array( $create_table_orders ) && ! empty( $create_table_orders ) ) {
			Logger::info( implode( ', ', $create_table_orders ), 'Method:' . __METHOD__ . ' (Orders)' );
		}

		if ( is_array( $create_table_comments ) && ! empty( $create_table_comments ) ) {
			Logger::info( implode( ', ', $create_table_comments ), 'Method:' . __METHOD__ . ' (Comments)' );
		}

	}

}
