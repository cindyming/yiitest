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

        <?php if (\app\models\System::loadConfig('open_stack_transfer')) :?>

        <span>(等值配股数: <?php echo  $stack ?  $stack : '股数计算中'?> )</span>
        <?php
        if ((!Yii::$app->user->identity->redeemed) && $stack ) {
            echo ( Html::a('兑换自由股', '/investment/transfer?id=all', ['data-confirm'=>"你确定要兑换成自由配股: "  . $stack])) ;
        }
        ?>

    <?php endif ?>
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
            [
                'attribute' => 'stack',
                'hidden' => (\app\models\System::loadConfig('open_stack_transfer')) ? false : true,
                'value' =>  function($model) {
                    return ($model->stack) ?  $model->stack : '股数计算中';
                },
            ],
            [
                'attribute' => 'status',
                'header' => '状态',
                'value' =>  function($model) {
                    return ($model->status==2) ?  '已兑换' : ($model->status ? '正常' : '已撤销');
                },
            ],
            'created_at',
            [
                'attribute' => 'note',
            ],
            [
                'attribute' => 'status',
                'label' => '操作',
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return (($model->status == 1) && \app\models\System::loadConfig('open_stack_transfer')) ? ( Html::a('兑换自由股', '/investment/transfer?id='.$model->id, ['data-confirm'=>"你确定要兑换成自由配股"  . $model->stack])) : '';
                }
            ],
        ],
    ]); ?>

</div>
