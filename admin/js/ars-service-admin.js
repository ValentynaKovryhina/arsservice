jQuery(document).ready(function ($) {

    createOrUpdateOrder();


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


});

