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


    <div class="b_download">
        <?= Html::a('下载最近一周', '/cash/export?week=1') ?>
        <?= Html::a('下载筛选数据', '/cash/export', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
        一次性下载最多5000条数据
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'autoXlFormat' => true,
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
                'attribute' => 'cash_type',
                'label'=>'提现方式',
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => \app\models\Cash::getCachType(),
                'value' => function($model) {
                    return \app\models\Cash::getCachType($model->cash_type);
                }
            ],
            [
                'attribute' => 'stack_number',
            ],
            [
                'attribute' => 'baodan_id',
                'label' => '报单员/会员编号'
            ],
            [
                'attribute' => 'sc_account',
            ],
            [
                'attribute' => 'bank',
                'label'=>'银行名称',
                'value' => function($model) {
                        return isset($model->getBankNames()[$model->bank]) ? $model->getBankNames()[$model->bank] : '';
                    },
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$searchModel->getBankNames(true),
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
            [
                'attribute' => 'amount',
                'format' => 'decimal',
            ],
            [
                'attribute' => 'real_amount',
                'label'=>'实发金额',
                'format' => 'decimal'
            ],
            ['attribute' => 'created_at',
                'filter' => true,
                'filterType'=>GridView::FILTER_DATE_RANGE,
                ],
            [
                'attribute' => 'status',
                'label' => '操作',
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return (in_array($model->status, array(2, 3))) ? '' :( Html::a('发放', '/cash/adminapprove?id='.$model->id, ['data-confirm'=>"你确定要发放[" . $model->user_id. "]"  . $model->amount . "的提现申请"]) . '   ' . Html::a('拒绝', '/cash/adminreject?id='.$model->id, ['data-confirm'=>"你确定要拒绝[" . $model->user_id. "]"  . $model->amount . "的提现申请"]) );
                }
            ],
            [
                'attribute' => 'status',
                'filter'=> true,
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => $searchModel->getStatus(true),
                'value' => function($model) {
                        return $model->getStatus()[$model->status];
                    }
            ],

        ],
    ]); ?>

</div>
