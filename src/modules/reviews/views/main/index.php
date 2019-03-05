<?php
/* @var $this yii\web\View */
/* @var array $reviews отзывы города */

use yii\bootstrap\Modal;
use yii\helpers\Html;

//echo '<pre>';
//print_r($reviews);
//echo '</pre>';die();

$this->title = 'Страница отзывов';
?>
<div class="reviews">
    <?php foreach ($reviews['reviews'] as $review): ?>
        <div id="review-<?= $review['id'] ?>" class="review" data-id="<?= $review['id'] ?>">
            <div class="title-review">
                <span class="title-text"><?= $review['title'] ?></span>
                <div class="action-review">
                    <button type="button" data-id="<?= $review['id'] ?>"
                            class="img-btn glyphicon glyphicon-pencil update-feedback"
                            title="Редактировать">
                    </button>
                    <button type="button" data-id="<?= $review['id'] ?>"
                       class="img-btn glyphicon glyphicon-trash delete-feedback"
                       title="Удалить"
                       data-confirm="Вы уверены, что хотите удалить этот элемент?">
                    </button>
                </div>
                <span class="time-review">
                    <?= Yii::$app->formatter->format($review['date_create'], 'date') ?>
                </span>
            </div>
            <div class="body-review">
                <div class="body-text">
                    <?= $review['text'] ?>
                </div>
                <?php if (isset($review['img']) && !empty($review['img'])): ?>
                    <div class="body-img">
                        <a href="/<?= $review['img'] ?>">
                            <img src="/<?= $review['img'] ?>" width="200" alt="">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer-review">
                <span class="footer-name"><?= $reviews['name'] ?></span>
                <span>рейтинг: <span class="footer-rating"><?= $review['rating'] ?></span></span>
                <span>Автор: <span class="footer-author_id"><?= $review['author_id'] ?></span></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php Modal::begin([
    'header' => '<h2></h2>',
    'toggleButton' => [
        'id' => 'btn-modal',
        'style' => 'display:none',
    ],
]);

 Modal::end(); ?>
