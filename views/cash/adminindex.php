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
            [
                'attribute' => 'user_id',
                'filter' => true,
            ],
            [
                'attribute' => 'bank',
                'label'=>'银行名称',
                'value' => function($model) {
                        return $model->getBankNames()[$model->bank];
                    },
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$searchModel->getBankNames(true),
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
                        return ($model->type == 1) ? $model->amount : ($model->amount * 0.95);
                    }
            ],
            'created_at',
            [
                'attribute' => 'status',
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => $searchModel->getStatus(true),
                'content' => function($model) {
                        return $model->status == 2 ? $model->getStatus()[$model->status] :( Html::a('发放', '/cash/adminapprove?id='.$model->id, ['data-confirm'=>"你确定要发放[" . $model->user_id. "]"  . $model->amount . "的提现申请"]) . '   ' . Html::a('拒绝', '/cash/adminreject?id='.$model->id, ['data-confirm'=>"你确定要拒绝[" . $model->user_id. "]"  . $model->amount . "的提现申请"]) );
                    }
            ],
        ],
    ]); ?>

</div>
