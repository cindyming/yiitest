<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖金明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revenue-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'label' => '序号'
            ],
            'user_id',
            'bonus',
            'merit',
            [
                'class' => 'yii\grid\Column',
                'header' => '总额',
                'content' => function($model) {
                        return $model->bonus + $model->merit;
                    }
            ],
            'created_at',
            [
                'attribute' => 'approved',
                'value' => function($model) {
                        return '已发放';
                    }
            ],
        ],
    ]); ?>

</div>
