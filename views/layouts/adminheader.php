<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

?>

<h2>管理员界面</h2>
<div class="m-info">
    <?php echo  '欢迎 (' . Yii::$app->user->identity->username . ')' ?>

    <?php if (\app\models\System::loadConfig('open_stack_transfer')): ?>
        <ul>
            <li>
                <a class="ch_link" target="_blank" href="<?php echo Yii::$app->params['cuohe_url'] ?>user/autologin?token=<?php echo Yii::$app->user->identity->access_token?>" data-method="post">登录自由股交易大厅</a>
            </li>
        </ul>
    <?php endif ?>
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
                ['label' => '追加投资', 'url' => yii\helpers\Url::to('/investment/adminindex')],
                ['label' => '审核会员', 'url' => yii\helpers\Url::to('/user/adminindexunapprove')],
                ['label' => '正式会员', 'url' => yii\helpers\Url::to('/user/adminindexapprove')],
                ['label' => '推荐列表', 'url' => yii\helpers\Url::to('/user/suggestindex')],
                ['label' => '全部会员', 'url' => yii\helpers\Url::to('/user/adminindex')],
                ['label' => '报单员申请', 'url' => yii\helpers\Url::to('/user/adminapplyindex')],
                ['label' => '网络图', 'url' => yii\helpers\Url::to('/user/admintree')],
            ],
        ],
        [
            'label' => '财务统计', 'url' => ['/invest/adminabout'],
            'items' => [
                ['label' => '奖金明细', 'url' => yii\helpers\Url::to('/revenue/adminindex')],
                ['label' => '奖金统计', 'url' => yii\helpers\Url::to('/revenue/admintotal')],
                ['label' => '拨比统计', 'url' => yii\helpers\Url::to('/globaltotal/index')],
            ]
        ],
        [
            'label' => '货币管理', 'url' => ['/revenue/manualadd'],
            'items' => [
                ['label' => '增减货币', 'url' => yii\helpers\Url::to('/user/huobi')],
                ['label' => '出账明细', 'url' => yii\helpers\Url::to('/cash/adminout')],
                ['label' => '入账明细', 'url' => yii\helpers\Url::to('/revenue/adminin')],
                ['label' => '提现管理', 'url' => yii\helpers\Url::to('/cash/adminindex')],
            ]
        ],
        [
            'label' => '信息管理', 'url' => ['/news/adminindex'],
            'items' => [
                ['label' => '公告管理', 'url' => yii\helpers\Url::to('/news/adminindex')],
                ['label' => '添加公告', 'url' => yii\helpers\Url::to('/news/admincreate')],
                ['label' => '留言管理', 'url' => yii\helpers\Url::to('/message/adminindex')],
            ]
        ],
        [
            'label' => '系统管理', 'url' => ['/blank'],
            'items' => [
                ['label' => '数据库备份', 'url' => yii\helpers\Url::to('/system/backupindex')],
                ['label' => '密码修改', 'url' => yii\helpers\Url::to('/user/adminchange')],
                ['label' => '系统设置', 'url' => yii\helpers\Url::to('/system/index')],
                ['label' => '系统日志', 'url' => yii\helpers\Url::to('/system/log')],
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