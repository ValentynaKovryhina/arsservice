<div class="ars_container">

    <h1><?php _e( 'Create new order', 'ars-service' ); ?></h1>
    <p><?php _e( 'Welcome to the Ars Service Plugin.', 'ars-service' ); ?></p>

	<?php

	$order = $additional_info = $comments = [];

	// get data if we have get parameter
	if ( isset( $_GET['ars-order'] ) ) {
		$id = $_GET['ars-order'];
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

	?>


    <form id='ars_order_form' class='ars_order_form'>

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
                       value="<?php echo isset( $order['price'] ) && $order['price'] ? $order['price'] : ''; ?>">
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

    </form>


</div>
