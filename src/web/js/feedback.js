$(function() {
    let $body = $('body');

    /* Активируем кнопку, если уже имелся загрузочный файл */
    $body.on('click', 'label[for=feedback-img]', function () {
        $('input[type=file]').removeAttr('disabled');
    });

    /* Записываем название файла, после его выбора */
    $body.on('change', '#feedback-img', function () {
        let $files = $(this).prop("files"),
            $filesList = $('#filesList');

        $filesList.html('');
        for (let i = 0; i < $files.length; i++) {
            $filesList.append('<p>' + $files[i].name + '</p>');
        }
    });

    /* Удаление фото */
    $body.on('click', '#delete-img', function () {
        let $id = $(this).data('id');

        $.post('/reviews/main/delete-image?id=' + $id, function(res) {
            if (res) $('#filesList').html('');
        });
    });

    /* Отправка формы */
    $body.on('click', '#model-submit', function () {
        let $form = $('#form-feedback');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($form[0]),
            contentType: false,
            processData: false,
            success: function (res) {
                if (res) {
                    $('.modal-header .close').click();

                    addOrUpdateFeedback(res);
                }
            }
        });
    });

    /* Создание html разметки для отзыва */
    function addOrUpdateFeedback(attr) {
        if (attr.show) {
            if (attr.action === 'create') {
                htmlFeedback(attr);
            }

            let $id = $('#review-' + attr.id),
                htmlImg = (attr.img.length > 0)
                    ? `<div class="body-img">` +
                        `<a href="/${attr.img}">` +
                            `<img src="/${attr.img}" width="200" alt="">` +
                        `</a>` +
                    `</div>`
                    : '';

            $id.find('.title-text').text(attr.title);
            $id.find('.body-text').text(attr.text);
            $id.find('.user-info').text(attr.author);
            $id.find('.body-review .body-img').remove();
            $id.find('.body-review').append(htmlImg);
            $id.find('.footer-rating').text(attr.rating);
        }
    }

    /* html шаблон отзыва */
    function htmlFeedback(attr) {
        $('.reviews').prepend(`<div id="review-${attr.id}" class="review" data-id="${attr.id}">` +
            `<div class="title-review">` +
                `<span class="title-text"></span>` +
                `<div class="action-review">` +
                    `<button type="button" data-id="${attr.id}"` +
                        `class="img-btn glyphicon glyphicon-pencil update-feedback"` +
                        `title="Редактировать">` +
                    `</button>` +
                    `<button type="button" data-id="${attr.id}"` +
                        `class="img-btn glyphicon glyphicon-trash delete-feedback"` +
                        `title="Удалить"` +
                        `data-confirm="Вы уверены, что хотите удалить этот элемент?">` +
                    `</button>` +
                `</div>` +
                `<span class="time-review">Сегодня</span>` +
            `</div>` +
            `<div class="body-review">` +
                `<div class="body-text"></div>` +
            `</div>` +
            `<div class="footer-review"><div class="footer-info">` +
                `<span>рейтинг: <span class="footer-rating"></span></span>` +
                `<button type="button" class="user-info" data-id="${attr.author_id}"></button>` +
            `</div></div></div>`);
    }
});