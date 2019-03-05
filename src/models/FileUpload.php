<?php
/**
 * Created by PhpStorm.
 * User: TTaRazut
 * Date: 21.02.2019
 * Time: 15:00
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * This is the model class for table "feedback".
 *
 * @property UploadedFile $image
 * @property string $nameFolder
 */
class FileUpload extends Model
{
    /**
     * @var UploadedFile
     */
    public $image;

    /**
     * @var string имя директории
     */
    public $nameFolder = 'uploads';

    /**
     * Загрузка файла
     *
     * @param UploadedFile $file файл который нужно загрузить
     * @param null|string $currentImage текущий файл
     *
     * @return string
     */
    public function uploadFile(UploadedFile $file, $currentImage = null)
    {
        $this->image = $file;

        if (!file_exists($this->_getFolder())) {
            mkdir($this->_getFolder());
        }

        $this->deleteCurrentImage($currentImage);
        return $this->_saveImage();
    }

    /**
     * Удаление существующего файла
     *
     * @return boolean
     */
    public function deleteCurrentImage($currentImage)
    {
        if ($currentImage && file_exists($currentImage)) {
            unlink($currentImage);
            return true;
        }

        return false;
    }

    /**
     * Полный путь к папке с файлами
     *
     * @return string
     */
    private function _getFolder()
    {
        return Yii::getAlias('@web') . $this->nameFolder;
    }

    /**
     * Генерация имени файла
     *
     * @return string
     */
    private function _generateFileName()
    {
        return md5(uniqid($this->image->baseName)) . '.' . $this->image->extension;
    }

    /**
     * Сохранение файла
     *
     * @return string имя файла
     */
    private function _saveImage()
    {
        $path = $this->_getFolder() . '/' . $this->_generateFileName();

        $this->image->saveAs($path);

        return $path;
    }
}