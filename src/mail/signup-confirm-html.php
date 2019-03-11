<?php
use yii\helpers\Html;

/* @var $user app\modules\user\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['user/auth/signup-confirm', 'token' => $user->email_confirm_token]);
?>
<div class="password-reset">
    <p>Здравствуйте <?= Html::encode($user->name) ?>,</p>

    <p>Перейдите по ссылке ниже, чтобы подтвердить свой адрес электронной почты:</p>

    <p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>
</div>