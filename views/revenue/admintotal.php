<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖金统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revenue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'user_id',
                'filter' => true,
            ],
            'bonus_total',
            'merit_total',
            [
                'class' => 'yii\grid\Column',
                'header' => '实发总额',
                'content' => function($model) {
                        return $model->bonus_total + $model->merit_total;
                    }
            ],
        ],
    ]); ?>

</div>
