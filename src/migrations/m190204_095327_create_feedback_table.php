<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%feedback}}`.
 */
class m190204_095327_create_feedback_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%feedback}}', [
            'id' => $this->primaryKey(),
            'id_city' => $this->integer(),
            'id_autor' => $this->integer(),
            'title' => $this->string(),
            'text' => $this->text(),
            'rating' => $this->tinyInteger('1'),
            'img' => $this->text(),
            'date_create' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%feedback}}');
    }
}
