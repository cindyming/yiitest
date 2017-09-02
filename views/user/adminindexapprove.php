<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '正式会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="b_download">
        <?= Html::a('下载最近一周', '/user/export?week=1') ?>
        <?= Html::a('下载筛选数据', '/user/export', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
        一次性下载最多5000条数据
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        'autoXlFormat' => true,
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
                'attribute' => 'username',
                'filter' => true,
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
                'attribute' => 'add_member',
                'label'=>'报单员',
                'value' => function($model) {
                        return $model->add_member == 2 ? '是':'否';
                    },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> [''=> '不限', 0=>'否', 2=> '是']
            ],
            'init_investment',
            'investment',
            [
                'attribute' => 'referer',
                'filter' => true,
            ],
            [
                'attribute' => 'phone',
                'filter' => true,
            ],
            [
                'attribute' => 'identity',
                'filter' => true,
            ],
            [
                'attribute' => 'approved_at',
                'filter' => true,
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'locked',
                'label' => '状态',
                'value' => function($model) {
                        return $model->getLockedOptions()[$model->locked];
                    }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '修改',
                'hiddenFromExport' => true,
                'template' => '{update} {cancel} {resetpassword}',
                'buttons' => [
                    'resetpassword' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '重置密码'),
                            'aria-label' => Yii::t('yii', '重置密码'),
                            'data-confirm' => Yii::t('yii', '你确定要为会员[' . $model->id . ']重置密码?密码将被设置为123456'),
                        ];
                        return Html::a('重置密码', $url, $options);
                    },
                    'cancel' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '撤销'),
                            'aria-label' => Yii::t('yii', '撤销'),
                            'data-confirm' => Yii::t('yii', '你确定要撤销会员[' . $model->id . ']'),
                        ];
                        return Html::a('撤销', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url ='/user/adminupdate?id='.$model->id;
                        return $url;
                    }
                    if ($action === 'cancel') {
                        $url ='/user/cancel?id='.$model->id;
                        return $url;
                    }
                    if ($action === 'resetpassword') {
                        $url ='/user/adminresetpassword?id='.$model->id;
                        return $url;
                    }
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '操作',
                'hiddenFromExport' => true,
                'template' => '{login}',
                'buttons' => [
                    'login' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '登录会员平台'),
                            'aria-label' => Yii::t('yii', '登录会员平台'),
                            'data-pjax' => '0',
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
