<?php

namespace app\modules\reviews\controllers;

use app\modules\reviews\models\CityFeedback;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use app\models\FileUpload;
use app\modules\cities\geoip\Geo;
use app\modules\cities\models\City;
use app\modules\reviews\models\Feedback;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\user\models\User;

/**
 * Main controller for the `reviews` module
 */
class MainController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'update', 'create', 'delete',
                    'delete-image', 'user-reviews', 'user-info'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'update', 'create', 'delete',
                            'delete-image', 'user-reviews', 'user-info'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-image' => ['post'],
                    'user-info' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Отзывы города
     *
     * @return string
     */
    public function actionIndex()
    {
        $reviews = $this->_getCityReviews();
        if ($reviews) {
            return $this->render('index', [
                'reviews' => $reviews,
            ]);
        }

        return $this->goHome();
    }

    /**
     * Creates a new Feedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param null|string $city
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($city = null)
    {
        return $this->_createOrUpdateFeedback('create', null, $city);
    }

    /**
     * Updates an existing Feedback model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        return $this->_createOrUpdateFeedback('update', $id);
    }

    /**
     * Deletes an existing Feedback model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return boolean
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->id !== $model->author_id) {
            return false;
        }

        $uploadModel = new FileUpload();
        $uploadModel->deleteCurrentImage($model->img);

        return ($model->delete()) ? true : false;
    }

    /**
     * Удаление фото.
     * @param integer $id
     * @return boolean
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteImage($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->id !== $model->author_id) {
            return false;
        }

        $pathImg = $model->img;
        $uploadModel = new FileUpload();

        $model->img = '';
        $model->disableAfterSave();
        if ($model->save(false)) {
            $uploadModel->deleteCurrentImage($pathImg);
            return true;
        }

        return false;
    }

    /**
     * Отзывы пользователя
     *
     * @param integer|string $id
     * @return string
     */
    public function actionUserReviews($id)
    {
        $reviews = $this->_getUserReviews($id);
        if ($reviews) {
            return $this->render('index', [
                'reviews' => $reviews,
            ]);
        }

        return $this->goHome();
    }

    /**
     * Информация о пользователе
     *
     * @param integer|string $id
     * @return false|json
     */
    public function actionUserInfo($id)
    {
        if (!Yii::$app->user->isGuest) {
            $user = User::find()
                ->where(['id' => $id])
                ->asArray()
                ->one();

            return json_encode($user);
        }

        return false;
    }

    /**
     * Finds the Feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Feedback::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Создание или редактирование отзыва
     *
     * @param string $action
     * @param string $id
     * @param null|string $geoCity
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function _createOrUpdateFeedback($action, $id = null, $geoCity = null)
    {
        if ($action === 'create') {
            $model = new Feedback();
        } else {
            $model = $this->findModel($id);

            if (Yii::$app->user->id !== $model->author_id) {
                return false;
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'img');

            if (isset($file)) {
                $model->img = $this->_saveFile($file, $model->getOldAttribute('img'));
            }

            if ($model->save()) {
                if (isset($geoCity)) {
                    $cityId = City::find()
                        ->where(['name' => $geoCity])
                        ->one();

                    if (isset($cityId)) {
                        $geo = new Geo();
                        $geo->setCity($cityId->id);
                    }

                    return $this->goHome();
                } else {
                    $nameSessionCity = Yii::$app->params['nameSessionCity'];
                    $sessionId = Yii::$app->session->get($nameSessionCity);
                    $identityUser = Yii::$app->user->identity;

                    $atributes = $model->getAttributes();
                    $atributes['action'] = $action;
                    $atributes['author'] = $identityUser->surname . ' ' . $identityUser->name . ' ' . $identityUser->middle_name;
                    $atributes['show'] = (empty($model->getCityIds()) || in_array($sessionId, $model->getCityIds()))
                        ? true : false;

                    return json_encode($atributes);
                }
            }
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'allCities' => $this->_getAllCities(),
            'geoCity' => $geoCity,
        ]);
    }

    /**
     * Имя файла
     *
     * @return string
     *
     * @param UploadedFile $file
     * @param string $currentImage текущее изображение
     */
    private function _saveFile($file, $currentImage = null)
    {
        $uploadModel = new FileUpload();
        return $uploadModel->uploadFile($file, $currentImage);
    }

    /**
     * Отзывы города
     *
     * @return array|boolean
     */
    private function _getCityReviews()
    {
        $cityId = Yii::$app->session->get(Yii::$app->params['nameSessionCity']);

        if (isset($cityId)) {
            $reviews = CityFeedback::find()
                ->with(['city', 'feedback'])
                ->where(['city_id' => $cityId])
                ->orWhere(['city_id' => null])
                ->asArray()
                ->all();

            return array_reverse($reviews);
        }

        return false;
    }

    /**
     * Отзывы пользователя
     *
     * @param integer|string $id
     * @return array|boolean
     */
    private function _getUserReviews($id)
    {
        if (!Yii::$app->user->isGuest) {
            $reviews = Feedback::find()
                ->with(['author', 'cities'])
                ->where(['author_id' => $id])
                ->orderBy('date_create DESC')
                ->asArray()
                ->all();

            return $reviews;
        }

        return false;
    }

    /**
     * Города России
     *
     * @return array
     */
    private function _getAllCities()
    {
        $geo = new Geo();
        $allCities = Yii::$app->cache->get(Yii::$app->params['nameCacheCities']);

        foreach ($geo->russianCities() as $city) {
            if (!in_array($city, $allCities)) {
                $allCities[$city] = $city;
            }
        }

        return $allCities;
    }
}
