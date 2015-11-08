<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '申请提现';
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
            'user_id',
            [
                'attribute' => 'bank',
                'label'=>'银行名称',
                'value' => function($model) {
                        return $model->getBankNames()[$model->bank];
                    }
            ],
            'cardname',
            'cardnumber',
            [
                'attribute' => 'bankaddress',
                'label'=>'开户行',
            ],
            [
                'attribute' => 'type',
                'label'=>'账户类型',
                'value' => function($model) {
                        return $model->type == 1 ? '分红' : '绩效工资';
                    }
            ],
            'amount',
            [
                'attribute' => 'amount',
                'label'=>'实发金额',
                'value' => function($model) {
                        return ($model->type == 1) ? $model->amount : ($model->amount * 0.9);
                    }
            ],
            'created_at',
            [
                'attribute' => 'status',
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$searchModel->getStatus(true),
                'value' => function($model) {
                        return $model->getStatus()[$model->status];
                    }
            ],
        ],
    ]); ?>

</div>
