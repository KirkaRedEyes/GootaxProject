<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m190204_095408_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'surname' => $this->string(),
            'middle_name' => $this->string(),
            'phone' => $this->string(),
            'email' => $this->string(),
            'password' => $this->string(),
            'date_create' => $this->integer(),
        ]);

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-feedback_author_id-user_id}}',
            '{{%feedback}}',
            'author_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-feedback_author_id-user_id}}',
            '{{%feedback}}'
        );

        $this->dropTable('{{%user}}');
    }
}
