<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_adminmenu', []) ?>

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
            'user_id',
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
                'class' => 'yii\grid\Column',
                'header' => '删除',
                'content' => function($model) {
                        return Html::a('删除', ['admindelete', 'id' => $model->id]);
                    }
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '回复',
                'content' => function($model) {
                        return Html::a('回复', ['adminreply', 'id' => $model->id]);
                    }
            ],
        ],
    ]); ?>

</div>
