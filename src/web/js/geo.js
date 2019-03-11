$(function() {
    /* Если пользователь нажал "Нет" */
    $('.btn-no').on('click', function () {
       $('#layer').hide();
    });

    /* Если пользователь нажал "Да" */
    $('.btn-city').on('click', function () {
        ajaxCity($(this).data('city'), $(this).data('id'));
    });

    /* После выбора города */
    $('.select-city').on('change', function () {
        let selectCity = $(this).find(':selected');
        ajaxCity(selectCity.text(), selectCity.val());
    });

    /* Выполнять если нет города в котором находится пользователь */
    $('.btn-no-city').on('click', function () {
        let $layerBody = $('.layer-body');

        $layerBody.find('p').html('Для вашего города нет отзывов.');

        $layerBody.find('div').html($(this).data('guest')
            ? '<a href="/user/auth/login" style="margin-right: 30px">Войти</a>' +
              '<a href="/user/auth/signup">Зарегистрироваться</a>'
            : `<button type="button" class="btn btn-success create-feedback" data-city="${$(this).data('city')}">Оставить отзыв</button>`);

    });

    /* Сохраняем город в сессию и перезагружаем страницу */
    function ajaxCity (city, idCity) {
        $.post('/cities/main/set-city?city=' + city + '&idCity=' + idCity,
            function (res) {
                if (res) window.location.href = '/';
        });
    }
});