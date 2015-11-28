<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '推荐列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->getSession()->get('message')): ?>
        <div class="Message">
            <?= Yii::$app->getSession()->get('message');
            Yii::$app->getSession()->set('message',null);
            ?>
        </div>
    <?php endif ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        //'summary' => '',
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'suggest_by',
                'filter' => true,
            ],
            [
                'attribute' => 'id',
                'filter' => true,
                'label'=>'会员编号',
            ],
            [
                'attribute' => 'username',
                'filter' => true,
            ],

            [
                'attribute' => 'level',
                'label'=>'业务等级',
                'value' => function($model) {
                        return $model->level ? $model->getLevelOptions()[$model->level] : '';
                    },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> $searchModel->getLevelOptions(true),
            ],
            'investment',

        ],
    ]); ?>

</div>
