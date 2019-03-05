$(function() {
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
        var selectCity = $(this).find(':selected');
        ajaxCity(selectCity.text(), selectCity.val());
    });

    /* Сохраняем город в сессию и перезагружаем страницу */
    function ajaxCity (city, idCity) {
        $.post('/cities/main/save-city?city=' + city + '&idCity=' + idCity,
            function (res) {
                if (res) window.location.href = '/';
        });
    }
});