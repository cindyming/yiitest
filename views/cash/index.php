<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现管理';
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
                'attribute' => 'cash_type',
                'label'=>'提现方式',
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => \app\models\Cash::getCachType(),
                'value' => function($model) {
                    return \app\models\Cash::getCachType($model->cash_type);
                }
            ],
            [
                'attribute' => 'baodan_id',
            ],
            [
                'attribute' => 'stack_number',
            ],
            [
                'attribute' => 'sc_account',
            ],
            [
                'attribute' => 'bank',
                'label'=>'银行名称',
                'value' => function($model) {
                        return $model->bank ? $model->getBankNames()[$model->bank] : '';
                    }
            ],
            'cardname',
            [
                'attribute' => 'cardnumber',
                'value' => function($model) {
                    return $model->cardnumber ? $model->cardnumber : '';
                }
            ],
            [
                'attribute' => 'bankaddress',
                'label'=>'开户行',
            ],
            [
                'attribute' => 'type',
                'label'=>'账户类型',
                'value' => function($model) {
                        return $model->getType();
                    }
            ],
            'amount',
            [
                'attribute' => 'real_amount',
                'label'=>'实发金额',
            ],
            [
                'attribute' => 'total',
                'label' => '出账后余额',
                'value' => function($model) {
                        return $model->total ? $model->total :  $model->getStatus()[$model->status];;
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
