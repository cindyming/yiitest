<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待审核会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="color:#ff0000;margin:10px 0px">会员审核后5分钟内系统计算发放绩效，请届时刷新页面</div>

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
                    },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> $searchModel->getLevelOptions(true),
            ],
            [
                'attribute' => 'username',
                'filter' => true,
            ],
            'investment',
            [
                'attribute' => 'referer',
                'filter' => true,
            ],
            'phone',
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '拒绝',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '拒绝'),
                                'aria-label' => Yii::t('yii', '拒绝'),
                                'data-confirm' => Yii::t('yii', '你确定要拒绝会员[' . $model->id . ']吗?'),
                                'data-method' => 'post',
                            ];
                            return Html::a('拒绝', $url, $options);
                        },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'delete') {
                            $url ='/user/adminreject?id='.$model->id;
                            return $url;
                        }
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '审核',
                'template' => '{approve}',
                'buttons' => [
                    'approve' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '审核'),
                                'aria-label' => Yii::t('yii', '审核'),
                                'data-confirm' => Yii::t('yii', '你确定要审核会员[' . $model->id . ']吗?'),
                                'data-method' => 'post',
                            ];
                            $parent = $model->getParennt()->one();
                            return (!$parent || ($parent->role_id == 3)) ? Html::a('审核', $url, $options) : '接点人' . (($parent->role_id == 2) ? '未被审核' : '已拒绝');
                        },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'approve') {
                            $url ='/user/adminapprove?id='.$model->id;
                            return $url;
                        }
                    }
            ],
        ],
    ]); ?>

</div>
