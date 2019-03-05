<?php

namespace app\modules\reviews\controllers;

use Yii;
use yii\web\Controller;
use app\models\FileUpload;
use app\modules\cities\geoip\Geo;
use app\modules\cities\models\City;
use app\modules\reviews\models\Feedback;
use app\modules\reviews\models\CityFeedback;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-image' => ['POST'],
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
//        unset($_SESSION['city']);

        $reviews = $this->_getReviews();
        if ($reviews) {
            return $this->render('index', [
                'reviews' => $reviews,
            ]);
        }

        return Yii::$app->response->redirect(['/']);
    }

    /**
     * Creates a new Feedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        return $this->_createOrUpdateFeedback('create');
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
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function _createOrUpdateFeedback($action, $id = null)
    {
        $model = ($action === 'create') ? new Feedback() : $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
//            print_r($model);die;
            $file = UploadedFile::getInstance($model, 'img');

            if (isset($file)) {
                $model->img = $this->_saveFile($file, $model->getOldAttribute('img'));
            }

            if ($model->save()) {
                $nameSessionCity = Yii::$app->params['nameSessionCity'];
                $sessionId = Yii::$app->session->get($nameSessionCity);

                $atributes = $model->getAttributes();
                $atributes['show'] = (in_array($sessionId, $model->getCityIds())) ? true : false;
                $atributes['action'] = $action;

                return json_encode($atributes);
            }
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'allCities' => $this->_getAllCities(),
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
    private function _getReviews()
    {
        $cityId = Yii::$app->session->get(Yii::$app->params['nameSessionCity']);

        if (isset($cityId)) {
            $reviews = City::find()
                ->with('reviews')
                ->where(['id' => $cityId])
                ->asArray()
                ->all();

            return $reviews[0];
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
