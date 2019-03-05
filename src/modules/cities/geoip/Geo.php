<?php

namespace app\modules\cities\geoip;

use Yii;

class Geo
{
    /**
     * @var string $languageName язык получения города
     */
    public $languageName = 'ru';
    /**
     * @var boolean $saveCity нужно ли сохранять в сессию город
     */
    public $saveCity = true;
    /**
     * @var string $nameSession имя переменной хранящую город в сессии
     */
    public $nameSession = 'city';

    /**
     * Получение полной информации по IP пользователя
     *
     * @param string $ip
     *
     * @return array
     */
    public function getData($ip = '')
    {
        $geo = new Sypexgeo();
        return $geo->get($ip);
    }

    /**
     * Получение IP пользователя
     *
     * @return string
     */
    public function getIp()
    {
        $geo = new Sypexgeo();
        return $geo->getIP();
    }

    /**
     * Получение города
     *
     * @param string $ip
     *
     * @return string|false
     */
    public function getCity($ip = '')
    {
        $session = Yii::$app->session;
        $name = $this->nameSession;

        if ($session->has($name)) {
            return $session->get($name);
        }

        $geo = $this->getData($ip);

        if (!empty($geo[$name]["name_{$this->languageName}"])) {
            $city = $geo[$name]["name_{$this->languageName}"];
            return $city;
        } else return false;
    }

    /**
     * Сохранение города в сессию
     *
     * @param string $city наименование города
     */
    public function saveCity($city)
    {
        Yii::$app->session->set($this->nameSession, $city);
    }

    /**
     * Удаление города из сессии
     */
    public function removeCity()
    {
        Yii::$app->session->remove($this->nameSession);
    }

    /**
     * Все города россии
     *
     * @return array
     */
    public function russianCities()
    {
        $russianCities = [];
        $file = fopen(__DIR__ . '/russian_cities.txt', 'r');
        while (!feof($file)) {
            $russianCities[] = trim(fgets($file));
        }
        fclose($file);
        return $russianCities;
    }
}