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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        //'summary' => '',
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
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '撤单',
                'template' => '{cancel}',
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
