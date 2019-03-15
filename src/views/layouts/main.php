<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var array $this->params['allCities'] города хранящиеся в базе данных */

use app\widgets\Alert;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\select2\Select2;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $session = Yii::$app->session;
    $nameCacheCities = Yii::$app->params['nameCacheCities'];
    $city = Yii::$app->params['nameSessionCity'];

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    if ($session->has($city)) {
        echo Html::beginTag('div', ['style' => 'width: 200px; display: inline-block; margin-top: 9px;']);
            echo Select2::widget([
                'name' => 'select_city',
                'language' => 'ru',
                'data' => Yii::$app->cache->get($nameCacheCities),
                'value' => $session->get($city),
                'size' => Select2::SMALL,
                'showToggleAll' => false,
                'options' => [
                    'placeholder' => 'Выберите город ...',
                    'class' => ['select-city'],
                ],
                'pluginOptions' => [
                    'minimumInputLength' => 2,
                ],
            ]);
        echo Html::endTag('div');
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => Yii::$app->user->isGuest
            ? [
                ['label' => 'Войти', 'url' => ['/user/auth/login']],
                ['label' => 'Зарегистрироваться', 'url' => ['/user/auth/signup']],
            ]
            : [
                '<li><button type="button" class="create-feedback">Оставить отзыв</button></li>',
                ['label' => 'Мои отзывы', 'url' => ['/reviews/main/user-reviews?id=' . Yii::$app->user->id]],
                '<li>'
                . Html::beginForm(['/user/auth/logout'], 'post')
                . Html::submitButton(
                    'Выйти (' . Html::encode(Yii::$app->user->identity->name) . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ]
    ]);

    NavBar::end();
    var_dump(Yii::$app->user->isGuest);
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <?php Modal::begin([
        'header' => '<h2></h2>',
        'toggleButton' => [
            'id' => 'btn-modal',
            'style' => 'display:none',
        ],
    ]);

    Modal::end(); ?>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Gootax Project <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
