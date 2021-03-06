<?php
/* @var $this yii\web\View */
/* @var string $city город пользователя */
/* @var integer|false $cityId ID города */

use kartik\select2\Select2;

$this->title = 'Определение города';
?>

<!--<div id="layer" style="display: none">-->
<?php if (!empty($city)) :?>
<div id="layer">
    <div class="layer-body">
        <p style="font-size: 1.2em">"<?= $city ?>" ваш город?</p>
        <div>
            <?php if ($cityId): ?>
                <button class="btn btn-success btn-city"
                    data-city="<?= $city ?>"
                    data-id="<?= $cityId ?>"
                    style="margin-right: 30px">Да</button>
            <?php else: ?>
                <button class="btn btn-success btn-no-city"
                        data-city="<?= $city ?>"
                        data-guest="<?= Yii::$app->user->isGuest ?>"
                        style="margin-right: 30px">Да</button>
            <?php endif; ?>
            <button class="btn btn-danger btn-no">Нет</button>
        </div>
    </div>
</div>
<?php endif;?>

<div class="site-index">
    <?= Select2::widget([
        'name' => 'kv_lang_select1',
        'language' => 'ru',
        'data' =>Yii::$app->cache->get(Yii::$app->params['nameCacheCities']),
        'showToggleAll' => false,
        'options' => [
            'placeholder' => 'Выберите город ...',
            'class' => ['select-city'],
        ],
        'pluginOptions' => [
            'minimumInputLength' => 1,
        ],
    ]);?>
</div>
