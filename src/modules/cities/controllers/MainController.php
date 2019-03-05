<?php

namespace app\modules\cities\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\modules\cities\geoip\Geo;

/**
 * Default controller for the `cities` module
 */
class MainController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save-city' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Получение города пользователя по IP
     *
     * @return string|boolean
     */
    public function actionIndex()
    {
        $geo = new Geo;

        if (!Yii::$app->session->has($geo->nameSession)) {

//            $cityName = $geo->getCity('46.147.142.190');
            $cityName = $geo->getCity();

            return $this->render('index', [
                'city' => $cityName,
            ]);
        }

        return false;
    }

    /**
     * Сохранение города в сессию
     *
     * @return boolean
     *
     * @param string $city
     * @param string $idCity
     */
    public function actionSaveCity($city, $idCity)
    {
        $geo = new Geo;

        if (isset($city) && in_array($city, $geo->russianCities())) {
            $geo->saveCity($idCity);
            return true;
        }

        return false;
    }
}
