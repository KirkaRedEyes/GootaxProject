<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\reviews\models\Feedback */
/* @var $form yii\widgets\ActiveForm */
/* @var array $allCities города России */
/* @var string|null $geoCity город найденый по geoIp */

$action = '';
if (isset($geoCity)) {
    $model->setCityIds([$geoCity]);
    $action = '/reviews/main/create?city=' . $geoCity;
}
?>

<div class="main-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-feedback',
        'action' => $action
    ]); ?>

    <?= $form->field($model, 'cityIds')->widget(Select2::class, [
        'name' => 'kv_lang_select1',
        'language' => 'ru',
        'data' => $allCities,
        'value' => 'Ижевск',
        'showToggleAll' => false,
        'options' => [
            'placeholder' => 'для всех городов',
            'multiple' => true,
        ],
        'pluginOptions' => [
            'minimumInputLength' => 2,
        ],
    ]);
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true])->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'rating')->dropDownList([
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
    ]) ?>

    <div>
        <?php if (isset($model->img) && !empty($model->img)): ?>
            <?= $form->field($model, 'img')
                ->fileInput(['style' => 'display: none', 'disabled' => true])
                ->label( 'Загрузить другое фото', ['class' => 'btn btn-primary']);
            ?>
            <div id="filesList">
                <button type="button" id="delete-img" class="btn btn-danger" style="display: block" data-id="<?= $model->id ?>">Удалить фото</button>
                <a href="/<?= $model->img ?>">
                    <img src="/<?= $model->img ?>" width="200" alt="">
                </a>
            </div>
        <?php else: ?>
            <?= $form->field($model, 'img')
                ->fileInput(['style' => 'display: none'])
                ->label( 'Загрузить фото', ['class' => 'btn btn-primary']);
            ?>
            <div id="filesList"></div>
        <?php endif; ?>
    </div>

    <div style="text-align: right">
        <?= Html::button('Сохранить', [
            'class' => 'btn btn-success',
            'id' => 'model-submit',
            'data-id' => $model->id,
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
