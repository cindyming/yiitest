<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员奖金统计';
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
                'attribute' => 'id',
                'filter' => true,
            ],
            'bonus_total',
            [
                'attribute' => 'merit_total',
                'value' => function($model) {
                        return $model->mert_total-$model->mall_total;
                    }
            ],
            'baodan_total',
            'mall_total',
            [
                'class' => 'yii\grid\Column',
                'header' => '实发总额',
                'content' => function($model) {
                        return $model->bonus_total + $model->merit_total + $model->baodan_total;
                    }
            ],
        ],
    ]); ?>

</div>
