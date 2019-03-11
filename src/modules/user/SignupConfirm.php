<?php

namespace app\modules\user;

use app\modules\user\models\User;
use Yii;

class SignupConfirm
{
    /**
     * Отправка ссылки подтверждения на email пользователя
     *
     * @param User $user
     */
    public function sentEmailConfirm(User $user)
    {
        $sent = Yii::$app->mailer
            ->compose(
                ['html' => 'signup-confirm-html', 'text' => 'signup-confirm-text'],
                ['user' => $user])
            ->setTo($user->email)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Подтверждение о регистрации')
            ->send();

        if (!$sent) {
            throw new \RuntimeException('Ошибка отправки');
        }
    }


    /**
     * Подтверждение пользователя
     *
     * @param string $token
     */
    public function confirmation($token)
    {
        if (empty($token)) {
            throw new \DomainException('Пустой токен подтверждения');
        }

        $user = User::findOne(['email_confirm_token' => $token]);
        if (!$user) {
            throw new \DomainException('Пользователь не найден.');
        }

        $user->email_confirm_token = null;
        $user->status = User::STATUS_ACTIVE;
        if (!$user->save(false)) {
            throw new \RuntimeException('Ошибка сохранения.');
        }

        if (!Yii::$app->getUser()->login($user)){
            throw new \RuntimeException('Ошибка аудентификации.');
        }
    }
}