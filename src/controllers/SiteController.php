<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use app\modules\cities\models\City;
use app\modules\cities\geoip\Geo;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
//        unset($_SESSION['city']);
//        print_r($_SESSION['city']);
        $city = Yii::$app->params['nameSessionCity'];

        if (!Yii::$app->session->has($city)) {
            return $this->redirect(['/cities/main']);
        }

        return $this->redirect(['/reviews/main']);
    }
}
