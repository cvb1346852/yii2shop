<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_photo`.
 */
class m170613_151330_create_goods_photo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_photo', [
            'id' => $this->primaryKey(),
            'phpto' => $this->string(255)->comment('照片'),
            'goods_id' => $this->integer()->comment('所属商品')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_photo');
    }
}
