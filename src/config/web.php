<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'name' => 'Gootax Project',
    'language' => 'ru-RU',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qIQO55UEM484TxojqjXPO_Kpbyeq3cXI',
            'baseUrl' => '',

        ],
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => 3600 * 2,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<action>' => 'site/<action>',
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'd MMMM yyyy',
            'datetimeFormat' => 'd-M-Y H:i:s',
            'timeFormat' => 'H:i:s',
        ],
    ],
    'modules' => [
        'cities' => [
            'class' => 'app\modules\cities\Module',
        ],
        'reviews' => [
            'class' => 'app\modules\reviews\Module',
        ],
    ],
    'on beforeAction' => function ($event) {
        /* Записываем города в кэш */
        Yii::$app->cache->getOrSet(Yii::$app->params['nameCacheCities'], function () {
            $tableCities = app\modules\cities\models\City::find()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->asArray()
                ->all();
            return yii\helpers\ArrayHelper::map($tableCities, 'id', 'name');
        });

        /* Если в сессии нет города перенаправляем на главную */
        $city = Yii::$app->params['nameSessionCity'];
        if (!Yii::$app->session->has($city)) {
            if ($event->action->id !== 'index' &&
                $event->action->controller->id !== 'site') {
                Yii::$app->response->redirect(['/']);
            }
        }
    },
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['192.168.99.1', '::1'],
    ];
}

return $config;
