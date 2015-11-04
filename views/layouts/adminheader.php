<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

?>

<h2>ADMIN</h2>
<div class="m-info">
    <?php echo  '欢迎 (' . Yii::$app->user->identity->username . ')' ?>
</div>
<?php
NavBar::begin();
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-left'],
    'items' => [
        [
            'label' => '会员管理', 'url' => ['/user/adminindex'],
            'items' => [
                ['label' => '注册会员', 'url' => yii\helpers\Url::to('/user/admincreate')],
                ['label' => '审核会员', 'url' => yii\helpers\Url::to('/user/adminindexunapprove')],
                ['label' => '正式会员', 'url' => yii\helpers\Url::to('/user/adminindexapprove')],
                ['label' => '全部会员', 'url' => yii\helpers\Url::to('/user/adminindex')],
                ['label' => '报单员申请', 'url' => yii\helpers\Url::to('/user/adminapplyindex')],
                ['label' => '推荐图', 'url' => yii\helpers\Url::to('/user/admintree')],
            ],
        ],
        [
            'label' => '财务管理', 'url' => ['/invest/adminabout'],
            'items' => [
                ['label' => '奖金明细', 'url' => yii\helpers\Url::to('/revenue/adminindex')],
                ['label' => '奖金统计', 'url' => yii\helpers\Url::to('/revenue/admintotal')],
                ['label' => '拨比统计', 'url' => yii\helpers\Url::to('/revenue/adminglobal')],
                ['label' => '出帐明细', 'url' => yii\helpers\Url::to('/blank')],
                ['label' => '入账明细', 'url' => yii\helpers\Url::to('/blank')],
                ['label' => '提现管理', 'url' => yii\helpers\Url::to('/cash/adminindex')],
            ]
        ],
        [
            'label' => '电子货币', 'url' => ['/blank'],
            'items' => [
            ]
        ],
        [
            'label' => '信息管理', 'url' => ['/news/adminindex'],
            'items' => [
                ['label' => '公告管理', 'url' => yii\helpers\Url::to('/news/adminindex')],
                ['label' => '添加公告', 'url' => yii\helpers\Url::to('/news/admincreate')],
                ['label' => '信息管理', 'url' => yii\helpers\Url::to('/message/adminindex')],
            ]
        ],
        [
            'label' => '系统管理', 'url' => ['/blank'],
            'items' => [
                ['label' => '密码修改', 'url' => yii\helpers\Url::to('/blank')],
                ['label' => '系统参数', 'url' => yii\helpers\Url::to('/blank')],
                ['label' => '制度参数', 'url' => yii\helpers\Url::to('/blank')],
                ['label' => '系统日志', 'url' => yii\helpers\Url::to('/blank')],
            ]
        ],
        Yii::$app->user->isGuest ?
            ['label' => 'Login', 'url' => ['/site/login']] :
            [
                'label' => '退出',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ],
    ]]);
NavBar::end();
?>