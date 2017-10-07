<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvestmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = $model->username . ' 的投资  - ' . $model->id;
$this->params['breadcrumbs'][] = $this->title;
$stack = $model->init_stack;
?>
<div class="investment-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <h3>目前对可换的总股数: <?php echo $model->stack?> </h3>
    <h3>初始投资</h3>
    <div class="first_investment">
        初始投资额 : <?php echo $model->init_investment ?>

        <?php if (\app\models\System::loadConfig('open_stack_transfer')) :?>

        <span>(等值配股数: <?php echo  $stack ?  $stack :  '股数计算中'?> )</span>
        <?php
        if ($stack && !$model->be_stack ) {
            echo ( Html::a('转成股票', '/investment/free?id=' . $model->id . '&type=all' , ['data-confirm'=>"你确定要转换成股票: "  . $stack])) ;
        } else if($stack && !$model->be_stack) {
            echo "锁定中";
        } else if ($stack && $model->be_stack){
            echo "已转换";
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
                    return $model->getStatus();
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
                    return ($model->stack && ($model->status == 1) && ($model->be_stack == 0) && \app\models\System::loadConfig('open_stack_transfer')) ? ( Html::a('转成股票', '/investment/free?id='.$model->id . '&type=investment', ['data-confirm'=>"你确定要转换成股票"  . $model->stack])) : '';
                }
            ],
        ],
    ]); ?>

</div>
