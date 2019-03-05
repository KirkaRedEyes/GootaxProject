$(function() {
    let $body = $('body');

    /* Создание отзыва */
    $body.on('click', '.create-feedback', function () {
        ajaxFeedback('create');
    });

    /* Добавление и редактирование отзыва */
    $body.on('click', '.update-feedback', function () {
        let id = $(this).data('id');
        ajaxFeedback('update', id);
    });

    /* Удаление отзыва */
    $body.on('click', '.delete-feedback', function () {
        let id = $(this).data('id');
        ajaxFeedback('delete', id);
    });

    /* ajax запрос для выполнения action отзыва */
    function ajaxFeedback (action, id = null) {
        let srcId = (id === null) ? '' : '?id=' + id;

        $.post('/reviews/main/' + action + srcId, function(res) {
            if (res) {
                if (action === 'delete') {
                    $('#review-' + id).remove();
                } else {
                    $('.modal-header').find('h2').html(
                        (action === 'create') ? 'Создание' : 'Редактирование'
                    );
                    $('.modal-body').html(res);
                    $('#btn-modal').click();
                }
            }
        });
    }
});