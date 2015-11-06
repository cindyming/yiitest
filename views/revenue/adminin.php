<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '入账明细';
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
                'label' => '编号',
                'filter' => true,
            ],
            [
                'attribute' => 'type',
                'header' => '账户类型',
                'content' => function($model) {
                    return $model->bonus ? '分红' : '绩效';
                }
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '入账金额',
                'content' => function($model) {
                        return $model->bonus ? $model->bonus : $model->merit;
                    }
            ],
            [
                'attribute' => 'total',
                'label' => '入账后余额',
            ],
            [
                'attribute' => 'created_at',
                'label' => '日期',
                'filter' => true,
            ],
            [
                'attribute' => 'note',
                'label' => '摘要',
                'filter' => true,
            ],
        ],
    ]); ?>

</div>
