<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

?>
<?php
$h=date('G');
$welcome = ($h<11) ? '早上好' : (($h<13) ? '中午好' : (($h<17) ? '下午好' : '晚上好'));
$welcome .= ', ' . Yii::$app->user->identity->username;
$welcome .= ', 欢迎回来.';
?>
    <h2>在线办公平台</h2>
    <div class="m-info">
        <ul>
            <li>
                会员ID: <?php echo Yii::$app->user->id?>
            </li>
            <li>
                等级: <?php echo Yii::$app->user->identity->getLevelOptions()[Yii::$app->user->identity->level]; ?>
            </li>
            <li>
                网络昵称: <?php echo Yii::$app->user->identity->username?>
            </li>
            <li>
                总业绩: <?php echo Yii::$app->user->identity->achievements?>
            </li>
            <li>
                分红余额: <?php echo Yii::$app->user->identity->bonus_remain?>
            </li>
            <li>
                报单费余额: <?php echo round(Yii::$app->user->identity->baodan_remain, 2)?>
            </li>
            <li>
                可提现绩效工资余额: <?php echo Yii::$app->user->identity->merit_remain?>
            </li>
            <li>
                商城币余额: <?php echo round(Yii::$app->user->identity->mall_remain, 2)?>
            </li>
        </ul>
    </div>
<?php
NavBar::begin();
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-left'],
    'items' => [
        ['label' => '首页', 'url' => ['/news/index']],
        [
            'label' => '业务中心', 'url' => ['/user/create'],
            'items' => [
                Yii::$app->user->identity->add_member == 2 ?
                    ['label' => '会员注册', 'url' => ['/user/create']]:
                  ['label' => '申请成为报单员', 'url' => ['/user/applyaddmember']],
                ['label' => '我的推荐', 'url' => ['/user/index', 'referer' => Yii::$app->user->identity->id]],
                \app\models\System::loadConfig('open_member_tree') ? ['label' => '推荐图', 'url' => ['/user/tree']] : '',
            ]
        ],
        [
            'label' => '财务管理', 'url' => ['/blank'],
            'items' => [
                ['label' => '我的追加投资', 'url' => ['/investment/index']],
                ['label' => '奖金明细', 'url' => ['/revenue/index']],
                ['label' => '奖金统计', 'url' => ['/revenue/total']],
                ['label' => '入账明细', 'url' => ['/revenue/index']],
                ['label' => '出帐明细', 'url' => ['/cash/index']],
                [
                    'label' => '提现管理', 'url' => ['/cash/index'],
                    'items' => [
                        ['label' => '提现管理', 'url' => ['/cash/index']],
                        ['label' => '申请提现', 'url' => ['/cash/create']],
                    ]
                ],
                ['label' => '汇款提醒', 'url' => ['/blank']],
            ]
        ],
        [
            'label' => '电子货币', 'url' => ['/blank'],
            'items' => [

            ]
        ],
        [
            'label' => '系统公告', 'url' => ['/news/index'],
            'items' => [
                ['label' => '新闻公告', 'url' => ['/news/index']],
                ['label' => '留言列表', 'url' => ['/message/index', 'user_id' => Yii::$app->user->identity->id]],
                ['label' => '添加留言', 'url' => ['/message/create']]
            ]
        ],
        [
            'label' => '会员管理', 'url' => ['/site/about'],
            'items' => [
                ['label' => '修改密码', 'url' => ['/user/changepassword']],
                ['label' => '会员资料', 'url' => ['/user/view', 'id' => Yii::$app->user->identity->id]]
            ]
        ],
        Yii::$app->user->isGuest ?
            ['label' => '安全退出', 'url' => ['/site/login']] :
            [
                'label' => '安全退出 (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ],
    ],
]);
NavBar::end();
?>