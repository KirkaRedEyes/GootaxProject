window.onload = function() {
    /* Если пользователь нажал "Нет" */
    $('.btn-no').on('click', function () {
       $('#layer').hide();
    });

    /* Если пользователь нажал "Да" */
    $('.btn-city').on('click', function () {
        ajaxCity($(this).attr('data-city'));
    });

    /* После выбора города */
    $('.select-city').on('change', function () {
        ajaxCity($(this).find(':selected').val());
    });

    /* Сохраняем город в сессию и перезагружаем страницу */
    function ajaxCity ($city) {
        $.post('/cities/main/save-city', {
            city: $city
        }, function (res) {
            if (res) window.location.href = '/';
        });
    }
};