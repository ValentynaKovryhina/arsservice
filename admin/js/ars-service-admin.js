jQuery(document).ready(function ($) {

    createOrUpdateOrder();
    searchInOrderTable();
    deleteOrder();

    function createOrUpdateOrder() {

        $(document).on('submit', '#ars_order_form', function (e) {
            e.preventDefault();

            var form = $(this);
            var loader = form.find('.ars_loader_wrapper');

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

            //checkboxes
            var checkboxes = [];
            form.find('input[name="ars_checkboxes"]:checked').each(function () {
                checkboxes.push($(this).val());
            });

            data.checkboxes = checkboxes;

            $.ajax({
                url: ars_service.ajax_url,
                type: 'POST',
                data: data,
                beforeSend: function () {
                    loader.css('display', 'flex');
                },
                success: function (response) {

                    // Success
                    if (response.success) {
                        if (response.data && response.data.form_html) {
                            // Get HTML and replace form
                            form.replaceWith(response.data.form_html);
                        }
                    } else {
                        // highlight errors
                        if (response.data && response.data.field) {
                            form.find('#' + response.data.field).addClass('ars_error');
                        }
                        alert(response.data.message);
                    }
                    loader.hide();

                },
                error: function (xhr, status, error) {
                    alert('Произошла ошибка. Пожалуйста, попробуйте позже.');
                }
            });
        });
    }

    // function searchInOrderTable() {
    //
    //     $('#ars_search').on('input', function () {
    //         var searchValue = $(this).val().toLowerCase();
    //
    //         $('#ars_orders_list tbody tr').each(function () {
    //             var id = $(this).data('id').toString().toLowerCase();
    //             var sn = $(this).data('sn').toString().toLowerCase();
    //             var phone = $(this).data('phone').toString().toLowerCase();
    //
    //             if (id.startsWith(searchValue) || sn.startsWith(searchValue) || phone.startsWith(searchValue)) {
    //                 $(this).show();
    //             } else {
    //                 $(this).hide();
    //             }
    //         });
    //     });
    // }

    function searchInOrderTable() {
        $('#ars_search, #ars_status_search').on('input change', function () {
            var searchValue = $('#ars_search').val().toLowerCase();
            var statusValue = $('#ars_status_search').val().toLowerCase(); // Получаем выбранное значение статуса

            $('#ars_orders_list tbody tr').each(function () {
                var id = $(this).data('id').toString().toLowerCase();
                var sn = $(this).data('sn').toString().toLowerCase();
                var phone = $(this).data('phone').toString().toLowerCase();
                var status = $(this).data('status').toString().toLowerCase(); // Получаем значение статуса ряда

                // Проверяем оба условия: и по текстовому поиску, и по статусу
                if (
                    (id.startsWith(searchValue) || sn.startsWith(searchValue) || phone.startsWith(searchValue)) &&
                    (status === statusValue || statusValue === "") // Показываем все, если статус не выбран
                ) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }


    function deleteOrder() {

        $('.ars_delete_item').off('click').on('click', function (e) {
            e.preventDefault(); // Отменяем стандартное действие ссылки

            var deleteId = $(this).data('delete-id');
            var popup = $('#ars_popup_delete_confirmation');

            $('#ars_popup_id').text(deleteId);

            popup.fadeIn();

            $('#ars_popup_delete').off('click').on('click', function (e) {
                e.preventDefault();

                var loader = popup.find('.ars_loader_wrapper');

                var data = {
                    action: 'ars_delete_order',
                    nonce: ars_service.nonce,
                    service_id: deleteId
                };

                // AJAX запрос для удаления элемента
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: data,
                    beforeSend: function () {
                        loader.css('display', 'flex');
                        popup.hide();
                    },
                    success: function (response) {

                        if (response.success) {
                            popup.hide();
                            $('tr[data-id="' + deleteId + '"]').remove();
                            loader.hide();
                        } else if (response.data && response.data.message) {
                            alert(response.data.message);
                            loader.hide();
                        } else {
                            alert('Ошибка при удалении. Попробуйте еще раз.');
                            loader.hide();
                        }
                    },
                    error: function (response) {
                        alert('Ошибка при удалении. Попробуйте еще раз.');
                        loader.hide();
                    }
                });
            });


        });

        // Закрытие popup при клике на кнопку "Отмена"
        $('#ars_popup_cancel').on('click', function (e) {
            e.preventDefault();

            // Скрываем popup
            $('#ars_popup_delete_confirmation').fadeOut();
        });


    }

});