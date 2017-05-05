<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员入账明细';
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
                'header' => '入账类型',
                'value' => function($model) {
                        return $model->type == 1 ? '奖金' : '充值';
                    },
            ],
            [
                'attribute' => 'account_type',
                'header' => '账户类型',
                'value' => function($model) {
                    return $model->bonus ? '分红' : (($model->merit) ? '绩效' : ($model->baodan ? '服务费' : ($model->duichong ? '对冲帐户' :  ($model->stack ? '股票账户' :  '商城币'))));
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> ['' => '不限',  1=> '分红', 2 => '绩效', 3 => '服务费', 4=> '商城币', 5=> '对冲帐户', 6 => '股票账户'],
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '入账金额',
                'content' => function($model) {
                    return $model->bonus ? $model->bonus : ($model->baodan ? $model->baodan : ($model->merit ? $model->merit : ($model->duichong ? $model->duichong : $model->mall)));
                }
            ],
            [
                'attribute' => 'total',
                'label' => '入账后余额',
            ],
            [
                'attribute' => 'created_at',
                'label' => '日期',
            ],
            [
                'attribute' => 'note',
                'label' => '摘要',
                'filter' => true,
            ],
        ],
    ]); ?>

</div>
