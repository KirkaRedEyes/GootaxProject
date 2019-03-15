<?php

namespace app\modules\reviews\models;


use app\modules\user\models\User;
use Yii;
use app\modules\cities\models\City;

/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $text
 * @property int $rating
 * @property string $img
 * @property int $date_create
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @var boolean флаг для выполнения функции 'beforeSave'
     */
    private $_doAfterSave = true;

    /**
     * @var array ID старых городов отзыва
     */
    private $_oldCityIds;

    /**
     * @var array ID городов отзыва
     */
    private $_cityIds;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * Связь с таблицей 'user'
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }


    /**
     * Свзяь с таблицей 'city' через промежуточную таблицу 'city_feedback'
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function getCities()
    {
        return $this->hasMany(City::class, ['id' => 'city_id'])
            ->viaTable('city_feedback', ['feedback_id' => 'id']);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'text', 'rating'], 'required'],
            [['author_id', 'date_create'], 'integer'],
            [['img'], 'file', 'extensions' => 'jpg,jpeg,png'],
            [['rating'], 'number', 'min' => 1, 'max' => 5],
            [['text'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 100],
            [['cityIds'], 'each', 'rule' => ['string']],
//            [['cityIds'], 'trim'],
        ];
    }

    /**
     * Выполнять после получения данных
     */
    public function afterFind()
    {
        $this->_cityIds = $this->_getCitiesFeedback($this->id);
        $this->_oldCityIds = $this->_cityIds;

        return parent::afterFind();
    }

    /**
     * Выполнять до сохранения
     *
     * @param boolean $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->author_id = Yii::$app->user->id;
            $this->date_create = time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * Выполнять после сохранения
     *
     * @param boolean $insert
     * @param array $changedAttributes
     * @return void
     * @throws \yii\db\Exception
     */

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->_doAfterSave) {
            $this->_addNewCity();
            $this->_addOrUpdateCityFeedback($insert);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Выполнять после удаления
     */
    public function afterDelete()
    {
        $this->_deleteUnnecessaryCity();

        parent::afterDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор',
            'cityIds' => 'Город',
            'title' => 'Заголовок',
            'text' => 'Описание',
            'rating' => 'Рейтинг',
            'img' => 'Фото',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * Запись ID городов отзыва
     *
     * @param array $ids
     */
    public function setCityIds($ids)
    {
        $this->_cityIds = $ids;
    }

    /**
     * Получить ID городов отзыва
     */
    public function getCityIds()
    {
        return $this->_cityIds;
    }

    /**
     * Отключение 'beforeSave'
     */
    public function disableAfterSave()
    {
        $this->_doAfterSave = false;
    }

    /**
     * Получить ID города(ов) отзыва
     *
     * @param integer $feedbackId ID отзыва
     *
     * @return array
     */
    private function _getCitiesFeedback($feedbackId)
    {
        $cityFeedback = CityFeedback::find()
            ->select('city_id')
            ->where(['feedback_id' => $feedbackId])
            ->asArray()
            ->all();

        $citiesId = [];
        foreach ($cityFeedback as $cityId) {
            $citiesId[] = $cityId['city_id'];
        }

        return $citiesId;
    }


    /**
     * Добавление в базу данных городов которых нет
     *
     * @return array|false
     */
    private function _addNewCity()
    {
        if (!empty($this->_cityIds)) {
            $addNewCity = false;
            $idCities = [];

            foreach ($this->_cityIds as $city) {
                if (!is_numeric($city)) {
                    $newCity = new City();
                    $newCity->name = $city;

                    if ($newCity->save()) {
                        $idCities[] = $newCity->id;
                        $addNewCity = true;
                    }
                } else {
                    $idCities[] = $city;
                }
            }

            $this->_cityIds = $idCities;

            if ($addNewCity) {
                $this->_clearCache();
            }

            return $addNewCity;
        }

        return false;
    }

    /**
     * Добавление в промежуточную таблицу
     *
     * @param boolean $insert
     * @throws \yii\db\Exception
     */
    private function _addOrUpdateCityFeedback($insert)
    {
        if (!$insert) {
            $this->_deleteCityFeedback();
        }

        $this->_addCityFeedback();

        if (!$insert) {
            $this->_deleteUnnecessaryCity();
        }
    }

    /**
     * Удаление в промежуточной таблице
     */
    private function _deleteCityFeedback()
    {
        CityFeedback::deleteAll([
            'feedback_id' => $this->id
        ]);
    }

    /**
     * Добавление в промежуточную таблицу
     *
     * @throws \yii\db\Exception
     */
    private function _addCityFeedback()
    {
        if (!empty($this->_cityIds)) {
            $addRows = [];
            foreach ($this->_cityIds as $cityId) {
                $addRows[] = [
                    $cityId,
                    $this->id
                ];
            }

            Yii::$app->db->createCommand()
                ->batchInsert('city_feedback', ['city_id', 'feedback_id'], $addRows)
                ->execute();
        } else {
            $cityFeedback = new CityFeedback();
            $cityFeedback->feedback_id = $this->id;
            $cityFeedback->save();
        }
    }

    /**
     * Удаление городов без отзывов
     *
     * @return array|boolean
     */
    private function _deleteUnnecessaryCity()
    {
        $unnecessaryCity = $this->_unnecessaryCity();

        if (!empty($unnecessaryCity) &&
            $this->_deleteCities($unnecessaryCity)) {

            return $unnecessaryCity;
        }

        return false;
    }

    /**
     * Удаление городов
     *
     * @param array $citiesId
     *
     * @return boolean
     */
    private function _deleteCities($citiesId)
    {
        if (City::deleteAll(['id' => $citiesId])) {
            $this->_clearCache();
            $this->_clearSessionCity($citiesId);

            return true;
        }

        return false;
    }

    /**
     * Удаление городов без отзывов
     *
     * @return array|boolean
     */
    private function _unnecessaryCity()
    {
        $arrReviews = CityFeedback::find()
            ->select('city_id')
            ->where(['city_id' => $this->_oldCityIds])
            ->asArray()
            ->all();

        $citiesIdWithReviews = [];
        foreach ($arrReviews as $arr) {
            $citiesIdWithReviews[] = $arr['city_id'];
        }

        $unnecessaryCity = [];
        foreach ($this->_oldCityIds as $cityId) {
            if (!in_array($cityId, $citiesIdWithReviews)) {
                $unnecessaryCity[] = $cityId;
            }
        }

        return $unnecessaryCity;
    }


    /**
     * Очистка кеша
     */
    private function _clearCache()
    {
        $nameCacheCities = Yii::$app->params['nameCacheCities'];
        Yii::$app->cache->delete($nameCacheCities);
    }

    /**
     * Удаление города из сессии
     *
     * @param array|null $_cityIds
     */
    private function _clearSessionCity($_cityIds = null)
    {
        $city = Yii::$app->params['nameSessionCity'];
        if (is_null($_cityIds) || in_array(Yii::$app->session->get($city), $_cityIds)){
            Yii::$app->session->remove($city);
        }
    }
}
