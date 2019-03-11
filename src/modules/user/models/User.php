<?php

namespace app\modules\user\models;

use app\modules\reviews\models\Feedback;
use yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string $status
 * @property string $email_confirm_token
 * @property int $date_create
 *
 * @property Feedback[] $feedbacks
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @var integer статус ожидания поддтверждения
     */
    const STATUS_WAIT = 2;
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var string проверка пароля
     */
    private $_confirmPassword;

    /**
     * @var string капча
     */
    private $_captcha;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'middle_name', 'phone', 'email', 'password', 'confirmPassword', 'captcha'], 'required'],
            [['date_create'], 'integer'],
            [['name', 'surname', 'middle_name', 'password'], 'string', 'max' => 255],
            [
                ['phone'],
                'match',
                'pattern' => '/^8[0-9]{10}$/',
                'message' => 'Неверно введет телефон. Пример: 81234567890'
            ],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            [['captcha'], 'captcha',  'captchaAction'=>'/user/auth/captcha'],
            ['status', 'in', 'range' => [self::STATUS_DELETED, self::STATUS_WAIT, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * Выполнять до сохранения
     *
     * @param boolean $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $this->email_confirm_token = Yii::$app->security->generateRandomString();
            $this->status = User::STATUS_WAIT;
            $this->date_create = time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Номер телефона',
            'email' => 'Email',
            'password' => 'Пароль',
            'confirmPassword' => 'Подтвердить пароль',
            'captcha' => 'Введите символы с картинки',
            'date_create' => 'Дата регистрации',
        ];
    }

    /**
     * Записать пароль поддтверждения
     *
     * @param string $password
     */
    public function setConfirmPassword($password)
    {
        $this->_confirmPassword = $password;
    }

    /**
     * Получить пароль поддтверждения
     *
     * @return string
     */
    public function getConfirmPassword()
    {
        return $this->_confirmPassword;
    }

    /**
     * Запись капчи
     *
     * @param string $captcha
     */
    public function setCaptcha($captcha)
    {
        $this->_captcha = $captcha;
    }

    /**
     * Получение капчи
     *
     * @return string
     */
    public function getCaptcha()
    {
        return $this->_captcha;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasMany(Feedback::class, ['author_id' => 'id']);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * @return IdentityInterface the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
    }

    public static function findByEmail($email)
    {
        return User::find()->where(['email' => $email])->one();
    }

    public function validatePassword($password)
    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);

        return (Yii::$app->getSecurity()->validatePassword($password, $hash)) ? true : false;
    }
}
