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
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => '保存为Excel文件']
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
        ],
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
                'attribute' => 'cardnumber',
                'label' => '提现信息',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getCashInfo();
                }
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
