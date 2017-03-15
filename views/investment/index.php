<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvestmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '追加投资';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="investment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        //'summary' => '',
        'layout' => '{items} {summary} {pager}',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            'amount',
            [
                'attribute' => 'status',
                'header' => '状态',
                'value' =>  function($model) {
                    return $model->status ? '正常' : '已撤销';
                },
            ],
            'created_at',
        ],
    ]); ?>

</div>
