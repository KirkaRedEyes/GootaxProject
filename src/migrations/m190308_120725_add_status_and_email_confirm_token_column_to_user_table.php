<?php

use yii\db\Migration;

/**
 * Handles adding status and email_confirm_token to table `{{%user}}`.
 */
class m190308_120725_add_status_and_email_confirm_token_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'status', $this->smallInteger()->defaultValue(0)->after('password'));
        $this->addColumn('{{%user}}', 'email_confirm_token', $this->string()->unique()->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'status');
        $this->dropColumn('{{%user}}', 'email_confirm_token');
    }
}
