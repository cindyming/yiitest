<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\News;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Newssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'News';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'label' => '编号'
            ],
            [
                'attribute' => 'be_top',
                'options' => [
                    'width' => 150
                ],
                'value' => function ($model) {
                        return $model->getBetopOptions()[$model->be_top];
                    },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'options' => [
                        'placeholder' => 'Select Status ...',
                        'multiple' => false
                    ],
                    'data' => [
                        0 => '正常',
                        1 => '置顶',
                    ]
                ]
            ],
            'title',
            // 'updated_at',
            'public_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
