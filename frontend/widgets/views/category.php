<?php

?>


    <?php foreach ($categorys as $k=>$category):?>
        <div class="cat <?=$k==0?'item1':''?>">
            <h3><?=\yii\helpers\Html::a($category->name,['goods/goods-list','lft'=>$category->lft,'rgt'=>$category->rgt])?> <b></b></h3>
            <div class="cat_detail">
                <?php foreach ($category->children as $i=>$children):?>
                    <dl class="<?=$i==0?'dl_1st':''?>">
                        <dt><?=\yii\helpers\Html::a($children->name,['goods/goods-list','lft'=>$children->lft,'rgt'=>$children->rgt])?></dt>
                        <dd>
                            <?php foreach ($children->children as $cate):?>
                                <?=\yii\helpers\Html::a($cate->name,['goods/goods-list','lft'=>$cate->lft,'rgt'=>$cate->rgt])?>
                            <?php endforeach;?>
                        </dd>
                    </dl>
                <?php endforeach;?>
            </div>
        </div>
    <?php endforeach;?>

