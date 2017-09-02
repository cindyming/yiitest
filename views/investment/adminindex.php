<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvestmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '追加投资';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="investment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加新的追加投资', ['admincreate'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="b_download">
        <?= Html::a('下载最近一周', '/investment/export?week=1') ?>
        <?= Html::a('下载筛选数据', '/investment/export', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
        一次性下载最多5000条数据
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' =>'user_id',
                'filter' => true
            ],
//            'added_by',
            'amount',
            [
                'attribute' => 'stack',
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
            [
                'attribute' => 'created_at',
                'filter' => true,
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '撤单',
                'template' => '{cancel}',
                'hiddenFromExport' => true,
                'buttons' => [
                    'cancel' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '撤单'),
                            'aria-label' => Yii::t('yii', '撤单'),
                            'data-confirm' => Yii::t('yii', '你确定要撤销会员[' . $model->user_id . ']的追加投资[' . $model->amount . ']吗?'),
                            'data-method' => 'post',
                        ];
                        return ($model->status == 1) ? Html::a('撤单', $url, $options) : '已经撤销';
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'cancel') {
                        $url ='/investment/cancel?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
