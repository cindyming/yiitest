<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留言信息';
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'type',
                'value' => function($model) {
                        return $model->getTypeoptions()[$model->type];
                    }
            ],
            'title',
            [
                'attribute' => 'replied_content',
                'label' => '是否回复',
                'value' => function($model) {
                        return $model->isReplied();
                    }
            ],
            'created_at',
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                        return ($model->updated_at == '0000-00-00 00:00:00') ? '' :  $model->updated_at;
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '详情',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
