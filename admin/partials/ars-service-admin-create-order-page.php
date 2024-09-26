<div class="ars_container">


    <h1><?php _e( 'Create new order', 'ars-service' ); ?></h1>
    <p><?php _e( 'Welcome to the Ars Service Plugin.', 'ars-service' ); ?></p>


    <form id='ars_order_form' class='ars_order_form'>


        <div class="ars_form_block">

            <h2 class="ars_form_group_heading"><?php _e( 'Информация о клиенте', 'ars-service' ); ?></h2>

            <div class="ars_form_group">
                <label for="client_name"><?php _e( 'ФИО *', 'ars-service' ); ?></label>
                <input type="text" id="client_name" name="client_name" required>
            </div>

            <div class="ars_form_group">
                <label for="address"><?php _e( 'Адрес *', 'ars-service' ); ?></label>
                <input type="text" id="address" name="address" required>
            </div>

        </div>

        <div class="ars_form_block">

            <div class="ars_form_group">
                <label for="phone"><?php _e( 'Телефон *', 'ars-service' ); ?></label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="ars_form_group">
                <label for="document"><?php _e( 'Документ *', 'ars-service' ); ?></label>
                <input type="text" id="document" name="document" required>
            </div>

        </div>

        <div class="ars_form_block">

            <h2 class="ars_form_group_heading"><?php _e( 'Информация о заказе', 'ars-service' ); ?></h2>

            <div class="ars_form_group">
                <label for="id"><?php _e( 'ID', 'ars-service' ); ?></label>
                <input type="text" id="id" name="id" readonly>
            </div>

            <div class="ars_form_group">
                <label for="device"><?php _e( 'Устройство *', 'ars-service' ); ?></label>
                <input type="text" id="device" name="device" required>
            </div>

        </div>

        <div class="ars_form_block">

            <div class="ars_form_group">
                <label for="date"><?php _e( 'Дата создания', 'ars-service' ); ?></label>
                <input type="text" id="date" name="date" readonly>
            </div>

            <div class="ars_form_group">
                <label for="price"><?php _e( 'Стоимость', 'ars-service' ); ?></label>
                <input type="number" id="price" name="price">
            </div>

        </div>

        <div class="ars_form_block">

            <div class="ars_form_group">
                <label for="sn"><?php _e( 'S/N *', 'ars-service' ); ?></label>
                <input type="text" id="sn" name="sn" required>
            </div>

            <div class="ars_form_group">
                <label for="status"><?php _e( 'Статус *', 'ars-service' ); ?></label>
                <select id="status" name="status" required>
                    <option value="0"></option>
                    <option value="1"><?php _e( 'Принят на сервис', 'ars-service' ); ?></option>
                    <option value="2"><?php _e( 'Ожидает детали', 'ars-service' ); ?></option>
                    <option value="3"><?php _e( 'Готов', 'ars-service' ); ?></option>
                    <option value="4"><?php _e( 'Выдан клиенту', 'ars-service' ); ?></option>
                </select>
            </div>

        </div>

        <div class="ars_form_block">

            <h2 class="ars_form_group_heading"><?php _e( 'Сведения об устройстве', 'ars-service' ); ?></h2>

            <div class="ars_form_group ars_form_checkboxes_group">

                <h4 class="ars_form_group_heading"><?php _e( 'Комплектность', 'ars-service' ); ?></h4>

                <div class="ars_form_checkboxes_block">

                    <label><input type="checkbox" name="checkboxes"
                                  value="Телевизор"> <?php _e( 'Телевизор', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Монитор"> <?php _e( 'Монитор', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Ноутбук"> <?php _e( 'Ноутбук', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Планшет"> <?php _e( 'Планшет', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Телефон"> <?php _e( 'Телефон', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Системный блок"> <?php _e( 'Системный блок', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Принтер"> <?php _e( 'Принтер', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Лазерный картридж"> <?php _e( 'Лазерный картридж', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Струйный картридж"> <?php _e( 'Струйный картридж', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Кабеля"> <?php _e( 'Кабеля', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Коробка"> <?php _e( 'Коробка', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Упаковка"> <?php _e( 'Упаковка', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Зарядное устройство"> <?php _e( 'Зарядное устройство', 'ars-service' ); ?>
                    </label>
                </div>

                <div class="ars_form_group">
                    <label for="other1"><?php _e( 'Другое', 'ars-service' ); ?></label>
                    <textarea type="text" id="other1" name="other1"></textarea>
                </div>

            </div>

            <div class="ars_form_group ars_form_checkboxes_group">

                <h4 class="ars_form_group_heading"><?php _e( 'Внешний вид', 'ars-service' ); ?></h4>

                <div class="ars_form_checkboxes_block">

                    <label><input type="checkbox" name="checkboxes"
                                  value="Явные следы эксплуатации"> <?php _e( 'Явные следы эксплуатации', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Без видимых следов эксплуатации"> <?php _e( 'Без видимых следов эксплуатации', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Наслоение потожировых следов"> <?php _e( 'Наслоение потожировых следов', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Следы влаги"> <?php _e( 'Следы влаги', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Вмятины"> <?php _e( 'Вмятины', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Мелкие царапины"> <?php _e( 'Мелкие царапины', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Глубокие царапины"> <?php _e( 'Глубокие царапины', 'ars-service' ); ?></label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Внешние повреждения"> <?php _e( 'Внешние повреждения', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Наслоение стороннего вещества"> <?php _e( 'Наслоение стороннего вещества', 'ars-service' ); ?>
                    </label>
                    <label><input type="checkbox" name="checkboxes"
                                  value="Следы ударов/падения"> <?php _e( 'Следы ударов/падения', 'ars-service' ); ?>
                    </label>

                </div>

                <div class="ars_form_group">
                    <label for="other2"><?php _e( 'Другое', 'ars-service' ); ?></label>
                    <textarea type="text" id="other2" name="other2"></textarea>
                </div>

            </div>

        </div>

        <div class="ars_form_block">
            <div class="ars_form_group">
                <label for="reported_failure"><?php _e( 'Заявленная неисправность *', 'ars-service' ); ?></label>
                <textarea id="reported_failure" name="reported_failure" required></textarea>
            </div>

            <div class="ars_form_group">
                <label for="comment"><?php _e( 'Комментарий', 'ars-service' ); ?></label>
                <textarea type="text" id="comment" name="comment"></textarea>
            </div>
        </div>

        <div class="ars_form_block ars_form_button_block">
            <button type="reset" class="button button-secondary"><?php _e( 'Отменить', 'ars-service' ); ?></button>
            <button type="submit" class="button button-primary"><?php _e( 'Сохранить', 'ars-service' ); ?></button>
        </div>

    </form>


</div>
