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

    <?= $this->render('_adminmenu', []) ?>

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
            'identity',
            'approved_at',
            [
                'attribute' => 'locked',
                'label' => '状态',
                'value' => function($model) {
                        return $model->getLockedOptions()[$model->locked];
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '修改',
                'template' => '{update}',
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url ='/user/adminupdate?id='.$model->id;
                            return $url;
                        }
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{login}',
                'buttons' => [
                    'login' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '登录会员平台'),
                            'aria-label' => Yii::t('yii', '登录会员平台'),
                            'data-pjax' => '0',
                            'target' => '_blank'
                        ];
                        return Html::a('登录会员平台', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url ='/user/adminupdate?id='.$model->id;
                            return $url;
                        }
                        if ($action === 'login') {
                            $url ='/user/autologin?id='.$model->id;
                            return $url;
                        }
                    }
            ],
        ],
    ]); ?>

</div>
