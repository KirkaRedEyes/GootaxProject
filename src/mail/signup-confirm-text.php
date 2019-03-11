<?php

/* @var $user app\modules\user\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['user/auth/signup-confirm', 'token' => $user->email_confirm_token]);
?>
    Здравствуйте <?= $user->name ?>,

    Перейдите по ссылке ниже, чтобы подтвердить свой адрес электронной почты:

<?= $confirmLink ?>