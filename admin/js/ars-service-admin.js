jQuery(document).ready(function ($) {

    console.log('ars-service-admin.js');
    createOrUpdateOrder();


    function createOrUpdateOrder() {

        // Обработчик события отправки формы
        $('#ars_order_form').submit(function (e) {
            e.preventDefault(); // Предотвращаем стандартную отправку формы

            var form = $(this);

            var data = {
                action: 'ars_service_create_order',
                nonce: ars_service.nonce
            };

            data.id = form.find('#id').val();
            data.client_name = form.find('#client_name').val();
            data.address = form.find('#address').val();
            data.phone = form.find('#phone').val();
            data.document = form.find('#document').val();
            data.device = form.find('#device').val();
            data.price = form.find('#price').val();
            data.sn = form.find('#sn').val();
            data.status = form.find('#status').val();
            data.reported_failure = form.find('#reported_failure').val();
            data.comment = form.find('#comment').val();
            data.date = form.find('#date').val();
            data.appearance_comment = form.find('#appearance_comment').val();
            data.complete_comment = form.find('#complete_comment').val();






            console.log('data');
            console.log(data);
            // return;

            // Отправляем AJAX-запрос
            $.ajax({
                url: ars_service.ajax_url,
                type: 'POST',
                data: data,
                success: function (response) {
                    // Обрабатываем успешный ответ
                    alert('Форма успешно отправлена: ' + response);
                },
                error: function (xhr, status, error) {
                    // Обрабатываем ошибку
                    console.log('Произошла ошибка: ' + error);
                }
            });
        });
    }


});

