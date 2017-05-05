<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvestmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '我的投资';
$this->params['breadcrumbs'][] = $this->title;
$stack = Yii::$app->user->identity->init_stack;
?>
<div class="investment-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <h3>初始投资</h3>
    <div class="first_investment">
        初始投资额 : <?php echo Yii::$app->user->identity->init_investment ?> 
        <span>(等值股票数: <?php echo  $stack ?  $stack : '股数计算中'?> )</span>
        <?php
        if (!Yii::$app->user->identity->redeemed && $stack) {
            echo ( Html::a('兑换自由股', '/investment/transfer?id=all', ['data-confirm'=>"你确定要兑换成自由股票: "  . $stack])) ;
        }
        ?>
    </div>

    <h3>追加投资</h3>

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
            'stack',
            [
                'attribute' => 'status',
                'header' => '状态',
                'value' =>  function($model) {
                    return ($model->status==2) ?  '已兑换' : ($model->status ? '正常' : '已撤销');
                },
            ],
            'created_at',
            [
                'attribute' => 'status',
                'label' => '操作',
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return ($model->status == 1) ? ( Html::a('兑换自由股', '/investment/transfer?id='.$model->id, ['data-confirm'=>"你确定要兑换成自由股票"  . $model->stack])) : '';
                }
            ],
        ],
    ]); ?>

</div>
