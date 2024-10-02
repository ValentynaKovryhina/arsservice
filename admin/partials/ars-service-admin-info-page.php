<div class="ars_container">

    <h1><?php _e( 'Ars Service', 'ars-service' ); ?></h1>

	<?php

	global $wpdb;

	$services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ars_orders", ARRAY_A );

	if ( ! empty( $services ) ) :

		$statuses = array(
			0 => __( '', 'ars-service' ),
			1 => __( 'Принят на сервис', 'ars-service' ),
			2 => __( 'Ожидает детали', 'ars-service' ),
			3 => __( 'Готов', 'ars-service' ),
			4 => __( 'Выдан клиенту', 'ars-service' ),
		); ?>

        <div class="ars_manage_orders">

            <a href="<?php echo admin_url( 'admin.php?page=ars-create-order' ); ?>"
               class="button button-primary"><?php _e( 'Создать', 'ars-service' ); ?></a>

            <label class="ars_search_wrapper">
                <input type="text" id="ars_search" name="ars_search"
                       value="" placeholder="<?php _e( 'Поиск заказа', 'ars-service' ); ?>">
                <span class="dashicons dashicons-search"></span>
            </label>

        </div>

        <div class="ars_table_wrapper">
            <table id="ars_orders_list" class="widefat striped">
                <thead>
                <tr>
                    <th class="manage-column">Id</th>
                    <th class="manage-column"><?php _e( 'Дата создания', 'ars-service' ); ?></th>
                    <th class="manage-column"><?php _e( 'Устройство', 'ars-service' ); ?></th>
                    <th class="manage-column">S/N</th>
                    <th class="manage-column"><?php _e( 'Стоимость', 'ars-service' ); ?></th>
                    <th class="manage-column"><?php _e( 'Статус', 'ars-service' ); ?></th>
                    <th class="manage-column"><?php _e( 'Телефон', 'ars-service' ); ?></th>
                    <th class="manage-column"></th>
                    <th class="manage-column"></th>
                </tr>
                </thead>

                <tbody>

				<?php foreach ( $services as $service ) : ?>
                    <tr data-id="<?php echo mb_strtolower( $service['id'] ); ?>"
                        data-sn="<?php echo mb_strtolower( $service['sn'] ); ?>"
                        data-phone="<?php echo isset( $service['phone'] ) && $service['phone'] ? mb_strtolower( $service['phone'] ) : ''; ?>">
                        <td><?php echo $service['id']; ?></td>
                        <td><?php echo $service['date']; ?></td>
                        <td><?php echo $service['device']; ?></td>
                        <td><?php echo $service['sn']; ?></td>
                        <td><?php echo isset( $service['price'] ) && $service['price'] ? $service['price'] . '&nbsp;&euro;' : ''; ?></td>
                        <td><?php echo isset( $statuses[ $service['status'] ] ) && $statuses[ $service['status'] ] ? $statuses[ $service['status'] ] : ''; ?></td>
                        <td><?php echo isset( $service['phone'] ) && $service['phone'] ? $service['phone'] : ''; ?></td>

                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=ars-create-order&ars-order=' . $service['id'] ); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </a>
                        </td>

                        <td>
                            <a href="#" class="ars_delete_item" data-delete-id="<?php echo $service['id']; ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </a>
                        </td>

                    </tr>
				<?php endforeach; ?>

                </tbody>
            </table>
        </div>

	<?php else : ?>

        <p><?php _e( 'Заказов не найдено', 'ars-service' ); ?></p>

	<?php endif; ?>


    <div id="ars_popup_delete_confirmation" class="ars_popup_delete_confirmation">

        <div class="ars_popup_content_wrapper">
            <div class="ars_popup_content">
				<?php _e( 'Вы действительно хотите удалить данные о заказе id: ', 'ars-service' );
				echo '<span id="ars_popup_id"></span>?'; ?>

                <div class="ars_popup_buttons">
                    <button id="ars_popup_cancel"
                            class="button button-secondary"><?php _e( 'Отмена', 'ars-service' ); ?></button>
                    <button id="ars_popup_delete"
                            class="button button-primary"><?php _e( 'Удалить', 'ars-service' ); ?></button>
                </div>
            </div>
        </div>

        <div class="ars_loader_wrapper">
            <div class="ars_loader"></div>
        </div>

    </div>


</div>


