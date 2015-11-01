<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
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
            'username',
            'investment',
            'referer',
            'phone',
            'created_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '删除',
                'template' => '{delete}',
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
                                'data-pjax' => '0',
                                'data-confirm' => Yii::t('yii', '你确定要审核会员[' . $model->id . ']吗?'),
                                'data-method' => 'post',
                            ];
                            return Html::a('审核', $url, $options);
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
