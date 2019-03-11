<?php

namespace app\modules\user\controllers;

use app\modules\user\models\LoginForm;
use app\modules\user\models\User;
use app\modules\user\SignupConfirm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Main controller for the `users` module
 */
class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            try{
                if($model->login()){
                    return $this->goBack();
                }
            } catch (\DomainException $e){
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->goHome();
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $city = Yii::$app->params['nameSessionCity'];
        $cityId = Yii::$app->session->get($city);

        Yii::$app->user->logout();

        Yii::$app->session->set($city, $cityId);

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $signupConfirm = new SignupConfirm();

            try{
                $signupConfirm->sentEmailConfirm($model);
                Yii::$app->session->setFlash('success', 'На введенную почту, отправленно письмо для поддтверждения регистрации');
                return $this->goHome();
            } catch (\RuntimeException $e){
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionSignupConfirm($token)
    {
        $signupConfirm = new SignupConfirm();

        try{
            $signupConfirm->confirmation($token);
            Yii::$app->session->setFlash('success', 'Регистрация успешно подтверждена');
        } catch (\Exception $e){
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->goHome();
    }
}
