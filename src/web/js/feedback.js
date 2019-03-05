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
                    console.log(res);
                    $('.modal-header .close').click();

                    htmlFeedback(res);
                }
            }
        });
    });

    /* Создание html разметки для отзыва */
    function htmlFeedback(attr) {
        if (attr.show) {
            let $city = $('[name=select_city] option:selected').text(),
                htmlImg = (attr.img.length > 0)
                    ? `<div class="body-img">` +
                        `<a href="/${attr.img}">` +
                            `<img src="/${attr.img}" width="200" alt="">` +
                        `</a>` +
                    `</div>`
                    : '';

            if (attr.action === 'create') {
                $('.reviews').prepend(`<div id="review-${attr.id}" class="review" data-id="${attr.id}">` +
                    `<div class="title-review">` +
                        `<span class="title-text">${attr.title}</span>` +
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
                        `<div class="body-text">${attr.text}</div>` +
                        htmlImg +
                    `</div>` +
                    `<div class="footer-review">` +
                        `<span class="footer-name">${$city}</span>` +
                        `<span>рейтинг: <span class="footer-rating">${attr.rating}</span></span>` +
                        `<span>Автор: <span class="footer-author_id"><?= ${attr.author_id}</span></span>` +
                    `</div></div>`)
            } else {
                let $id = $('#review-' + attr.id);

                $id.find('.title-text').html(attr.title);
                $id.find('.body-text').html(attr.text);
                $id.find('.body-review .body-img').remove();
                $id.find('.body-review').append(htmlImg);
                $id.find('.footer-rating').html(attr.rating);
            }
        }
    }
});