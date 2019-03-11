<?php

namespace app\modules\cities\controllers;

use Yii;
use yii\web\Controller;
use app\modules\cities\geoip\Geo;

/**
 * Default controller for the `cities` module
 */
class MainController extends Controller
{
    /**
     * Получение города пользователя по IP
     *
     * @return string|boolean
     */
    public function actionIndex()
    {
        $geo = new Geo;

        if (!Yii::$app->session->has($geo->nameSession)) {

            $cities = Yii::$app->cache->get(Yii::$app->params['nameCacheCities']);
            $cityName = $geo->getCity('46.147.142.190');
//            $cityName = $geo->getCity();
            $cityId = array_search($cityName, $cities);

            return $this->render('index', [
                'city' => $cityName,
                'cityId' => $cityId
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
    public function actionSetCity($city, $idCity)
    {
        $geo = new Geo;

        if (isset($city) && in_array($city, $geo->russianCities())) {
            $geo->setCity($idCity);
            return true;
        }

        return false;
    }
}
