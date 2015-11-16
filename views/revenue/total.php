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
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'user_id',
                'filter' => false,
            ],
            'bonus_total',
            [
                'attribute' => 'merit_total',
                'value' => function($model) {
                        return $model->merit_total * 0.9;
                    }
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '商城币总额',
                'content' => function($model) {
                        return  $model->merit_total * 0.1;
                    }
            ],
            'baodan_total',
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
