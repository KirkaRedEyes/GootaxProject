<?php
/* @var $this yii\web\View */
/* @var array $reviews отзывы */

use yii\helpers\Html;

$this->title = 'Страница отзывов';
?>
<div class="reviews">
    <?php foreach ($reviews as $review):
        $feedback = (isset($review['cities'])) ? $review : $review['feedback'];
        ?>
        <div id="review-<?= $feedback['id'] ?>" class="review" data-id="<?= $feedback['id'] ?>">
            <div class="title-review">
                <span class="title-text"><?= $feedback['title'] ?></span>
                <?php if (Yii::$app->user->id == $feedback['author']['id']): ?>
                    <div class="action-review">
                        <button type="button" data-id="<?= $feedback['id'] ?>"
                                class="img-btn glyphicon glyphicon-pencil update-feedback"
                                title="Редактировать">
                        </button>
                        <button type="button" data-id="<?= $feedback['id'] ?>"
                           class="img-btn glyphicon glyphicon-trash delete-feedback"
                           title="Удалить"
                           data-confirm="Вы уверены, что хотите удалить этот элемент?">
                        </button>
                    </div>
                <?php endif; ?>
                <span class="time-review">
                    <?= Yii::$app->formatter->format($feedback['date_create'], 'date') ?>
                </span>
            </div>
            <div class="body-review">
                <div class="body-text">
                    <?= $feedback['text'] ?>
                </div>
                <?php if (isset($feedback['img']) && !empty($feedback['img'])): ?>
                    <div class="body-img">
                        <a href="/<?= $feedback['img'] ?>">
                            <img src="/<?= $feedback['img'] ?>" width="200" alt="">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer-review">
                <div class="footer-info">
                    <span>рейтинг: <span class="footer-rating"><?= $feedback['rating'] ?></span></span>
                    <?php
                    $author = $feedback['author']['surname'] . ' ' .  $feedback['author']['name'] . ' ' .  $feedback['author']['middle_name'];
                    if (Yii::$app->user->isGuest): ?>
                        <div><?= $author ?></div>
                    <?php else: ?>
                        <button type="button" class="user-info" data-id="<?= $feedback['author']['id'] ?>">
                            <?= $author ?>
                        </button>
                    <?php endif; ?>
                </div>
                <?php if (isset($feedback['cities'])) {
                    $strCities = '';

                    foreach ($feedback['cities'] as $city) {
                        $strCities .= $city['name'] . ' ';
                    }

                    if (empty($strCities)) {
                        $strCities = 'для всех городов';
                    }

                    echo Html::tag('div', $strCities, ['class' => 'footer-cities']);
                } ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>