<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%city_feedback}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%city}}`
 * - `{{%feedback}}`
 */
class m190227_184005_create_junction_table_for_city_and_feedback_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%city_feedback}}', [
            'city_id' => $this->integer(),
            'feedback_id' => $this->integer(),
        ]);

        // creates index for column `city_id`
        $this->createIndex(
            '{{%idx-city_feedback-city_id}}',
            '{{%city_feedback}}',
            'city_id'
        );

        // add foreign key for table `{{%city}}`
        $this->addForeignKey(
            '{{%fk-city_feedback-city_id}}',
            '{{%city_feedback}}',
            'city_id',
            '{{%city}}',
            'id',
            'CASCADE'
        );

        // creates index for column `feedback_id`
        $this->createIndex(
            '{{%idx-city_feedback-feedback_id}}',
            '{{%city_feedback}}',
            'feedback_id'
        );

        // add foreign key for table `{{%feedback}}`
        $this->addForeignKey(
            '{{%fk-city_feedback-feedback_id}}',
            '{{%city_feedback}}',
            'feedback_id',
            '{{%feedback}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%city}}`
        $this->dropForeignKey(
            '{{%fk-city_feedback-city_id}}',
            '{{%city_feedback}}'
        );

        // drops index for column `city_id`
        $this->dropIndex(
            '{{%idx-city_feedback-city_id}}',
            '{{%city_feedback}}'
        );

        // drops foreign key for table `{{%feedback}}`
        $this->dropForeignKey(
            '{{%fk-city_feedback-feedback_id}}',
            '{{%city_feedback}}'
        );

        // drops index for column `feedback_id`
        $this->dropIndex(
            '{{%idx-city_feedback-feedback_id}}',
            '{{%city_feedback}}'
        );

        $this->dropTable('{{%city_feedback}}');
    }
}
