<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖金明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revenue-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        //'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'user_id',
                'filter' => false,
            ],
            [
                'attribute' => 'type',
                'header' => '入账类型',
                'content' => function($model) {
                        return $model->type == 1 ? '奖金' : '充值';
                    },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> ['' => '不限',  1=> '奖金', 2 => '充值'],
            ],
            'bonus',
            'merit',
            'baodan',
            [
                'class' => 'yii\grid\Column',
                'header' => '总额',
                'content' => function($model) {
                        return $model->bonus + $model->merit + $model->baodan;
                    }
            ],
            [
                'attribute' => 'total',
                'label' => '入账后余额',
            ],
            'note',
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
