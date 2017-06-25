<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170621_033102_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name' => $this->string(20)->notNull()->comment('收货人'),
            'member_id' => $this->integer()->notNull()->comment('所属id'),
            'province' => $this->string(20)->notNull()->comment('省'),
            'city' => $this->string(30)->notNull()->comment('市'),
            'area' => $this->string(30)->notNull()->comment('区县'),
            'detail_address' => $this->text()->comment('详细地址'),
            'phone' => $this->char(11)->notNull()->comment('电话'),
            'status' => $this->smallInteger(1)->comment('状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
