<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '报单员申请';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>



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
                'attribute' => 'id',
                'filter' => true,
                'label'=>'会员编号',
            ],
            [
                'attribute' => 'level',
                'label'=>'业务等级',
                'value' => function($model) {
                        return $model->level ? $model->getLevelOptions()[$model->level] : '';
                    }
            ],
            'username',
            'investment',
            'referer',
            'phone',
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{approve}',
                'buttons' => [
                    'approve' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '批准'),
                                'aria-label' => Yii::t('yii', '批准'),
                                'data-confirm' => Yii::t('yii', '你确定要批准会员[' . $model->id . ']成为报单员吗?'),
                                'data-method' => 'post',
                            ];
                            return Html::a('批准', $url, $options);
                        },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'approve') {
                            $url ='/user/adminapproveforaddmember?id='.$model->id;
                            return $url;
                        }
                    }
            ],
        ],
    ]); ?>

</div>
