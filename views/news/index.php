<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
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
                    'data' => $searchModel->getStatusOptions()
                ]
            ],
            'title',
            'content:ntext',
            'created_at',
            // 'updated_at',
            // 'public_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
