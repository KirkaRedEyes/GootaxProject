<?php

namespace app\modules\cities\controllers;

use Yii;
use yii\web\Controller;
use app\modules\cities\models\City;
use app\modules\cities\geoip\Geo;


/**
 * Default controller for the `cities` module
 */
class MainController extends Controller
{
    /**
     * Получение города пользователя по IP
     *
     * @return string|false
     */
    public function actionIndex()
    {
        $geo = new Geo;

        $allCities = [];
        $tableCities = City::find()->select('name')->orderBy('name')->asArray()->all();
        foreach ($tableCities as $k => $arrCity)
            $allCities[$arrCity['name']] = $arrCity['name'];

        unset($tableCities);

        if (empty($_SESSION[$geo->nameSession])) {

//            $cityName = $geo->getCity('46.147.142.190');
            $cityName = $geo->getCity();

            return $this->render('index', [
                'city' => $cityName,
                'allCities' => $allCities,
            ]);
        }

        return false;
    }


    /**
     * Сохранение города в сессию
     *
     * @return boolean
     */
    public function actionSaveCity()
    {
        $geo = new Geo;

        $city = Yii::$app->request->post('city');

        if (isset($city))
            if (strpos(file_get_contents(dirname(__DIR__) . '/geoip/russian_cities.txt'), $city)) {
                $geo->saveCity($city);
                return true;
            }

        return false;
    }
}
