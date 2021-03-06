<?php

namespace app\modules\reviews\models;

use app\modules\cities\models\City;

/**
 * This is the model class for table "city_feedback".
 *
 * @property int $city_id
 * @property int $feedback_id
 *
 * @property City $city
 * @property Feedback $feedback
 */
class CityFeedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['feedback_id'], 'required'],
            [['city_id', 'feedback_id'], 'integer'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['feedback_id'], 'exist', 'skipOnError' => true, 'targetClass' => Feedback::class, 'targetAttribute' => ['feedback_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'city_id' => 'City ID',
            'feedback_id' => 'Feedback ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeedback()
    {
        return $this->hasOne(Feedback::class, ['id' => 'feedback_id'])
            ->with('author');
    }
}
