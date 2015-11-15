<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '出帐明细';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'user_id',
                'filter' => true,
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '出帐类型',
                'content' => function($model){
                        return '提现';
                    }
            ],
            [
                'attribute' => 'type',
                'label'=>'账户类型',
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' =>$searchModel->getTypes(true),
                'value' => function($model) {
                        return $model->getType();
                    }
            ],
            [
                'attribute' => 'amount',
                'label' => '出账金额',
            ],
            [
                'attribute' => 'amount',
                'label'=>'手续费',
                'value' => function($model) {
                        return $model->type == 2 ? $model->amount * 0.1 : 0 ;
                    }
            ],
            'created_at',
        ],
    ]); ?>

</div>
