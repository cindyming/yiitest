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

    <?php if (Yii::$app->getSession()->get('message')): ?>
        <div class="Message">
            <?= Yii::$app->getSession()->get('message');
            Yii::$app->getSession()->set('message',null);
            ?>
        </div>
    <?php endif ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => '保存为Excel文件']
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
        ],
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
            'approved_at',
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
                'template' => '{update} {resetpassword}',
                'buttons' => [
                    'resetpassword' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '重置密码'),
                                'aria-label' => Yii::t('yii', '重置密码'),
                                'data-confirm' => Yii::t('yii', '你确定要为会员[' . $model->id . ']重置密码?密码将被设置为123456'),
                            ];
                            return Html::a('重置密码', $url, $options);
                        },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url ='/user/adminupdate?id='.$model->id;
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
