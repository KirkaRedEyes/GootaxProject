<?php

namespace app\modules\cities\models;

use app\modules\reviews\models\CityFeedback;
use Yii;
use app\modules\reviews\models\Feedback;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property int $date_create
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * Свзяь с таблицей 'feedback' через промежуточную таблицу 'city_feedback'
     * @throws \yii\base\InvalidConfigException
     */
    public function getReviews()
    {
        return $this->hasMany(Feedback::class, ['id' => 'feedback_id'])
            ->with('author')
            ->viaTable('city_feedback', ['city_id' => 'id'])
            ->orderBy('date_create DESC');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['date_create'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($insert) $this->date_create = time();

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'date_create' => 'Дата добавления',
        ];
    }
}
