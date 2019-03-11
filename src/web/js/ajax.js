$(function() {
    let $body = $('body');

    /* Создание отзыва */
    $body.on('click', '.create-feedback', function () {
        let city = ($(this).data('city')) ? $(this).data('city') : null;
        ajaxFeedback('create', null, city);
    });

    /* Редактирование отзыва */
    $body.on('click', '.update-feedback', function () {
        let id = $(this).data('id');
        ajaxFeedback('update', id);
    });

    /* Удаление отзыва */
    $body.on('click', '.delete-feedback', function () {
        let id = $(this).data('id');
        ajaxFeedback('delete', id);
    });

    /* Информация о пользователе */
    $body.on('click', '.user-info', function () {
        let id = $(this).data('id'),
            fio = $(this).text();
        ajaxAuthor(id, fio);
    });

    /* ajax запрос для выполнения action отзыва */
    function ajaxFeedback (action, id = null, city = null) {
        let srcId = (id === null) ? '' : '?id=' + id,
            srcCity = (city === null) ? '' : '?city=' + city;

        $.post('/reviews/main/' + action + srcId + srcCity, function(res) {
            if (res) {
                if (action === 'delete') {
                    $('#review-' + id).remove();
                } else {
                    modalCreate(
                        (action === 'create') ? 'Создание' : 'Редактирование',
                        res
                    );
                }
            }
        });
    }

    /* ajax запрос для получения ифнормации о пользователе */
    function ajaxAuthor (id, fio) {
        $.post('/reviews/main/user-info?id=' + id, function(res) {
            if (res) {
                let body = `<p>Email: ${res.email}</p>` +
                    `<p>Телефон: ${res.phone}</p>` +
                    `<a href="/reviews/main/user-reviews?id=${res.id}">Посмотреть отзывы</a>`;
                modalCreate(fio, body);
            }
        }, 'json');
    }

    /* заполняем модель */
    function modalCreate(header, body) {
        $('.modal-header').find('h2').html(header);
        $('.modal-body').html(body);
        $('#btn-modal').click();
    }
});