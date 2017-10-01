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
                总投资额: <?php echo Yii::$app->user->identity->investment ?>
                <?php if(Yii::$app->user->identity->showTotal()):?>
                <?php if (\app\models\System::loadConfig('open_stack_transfer')): ?>
                (等值配股数: <?php echo (Yii::$app->user->identity->stack)?>)
            </li>

            <li class="">
                总业绩: <?php echo Yii::$app->user->identity->achievements?>
            </li>
            <li class="">
                总配股数: <?php echo Yii::$app->user->identity->getTotalStack() ?>
                <?php endif ?>
            </li>
            <li class="">
                分红余额: <?php echo Yii::$app->user->identity->bonus_remain?>
            </li>
            <li class="">
                服务费余额: <?php echo round(Yii::$app->user->identity->baodan_remain, 2)?>
            </li>
            <li class="">
                可提现绩效工资余额: <?php echo Yii::$app->user->identity->merit_remain?>
            </li>
            <li class="">
                商城币余额: <?php echo round(Yii::$app->user->identity->mall_remain, 2)?>
            </li>
            <li class="">
                对冲帐户余额: <?php echo round(Yii::$app->user->identity->duichong_remain, 2)?>
            </li>
            <?php endif ?>
        </ul>
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
        ['label' => '首页', 'url' => ['/news/index']],
        [
            'label' => '业务中心', 'url' => ['/user/create'],
            'items' => [
                Yii::$app->user->identity->add_member == 2 ?
                    ['label' => '会员注册', 'url' => ['/user/create']]:
                    ['label' => '申请成为报单员', 'url' => ['/user/applyaddmember']],
                ( Yii::$app->user->identity->openSuggestion()) ?
                    ['label' => '我的报单', 'url' => ['/user/baodanindex']] : '',
                Yii::$app->user->identity->openSuggestion() ? ['label' => '我的推荐', 'url' => ['/user/index']] : '',
                Yii::$app->user->identity->haveTree() ? ['label' => '网络图', 'url' => ['/user/tree']] : '',
            ],

        ],
        [
            'label' => '财务管理', 'url' => ['/blank'],
            'items' => [
                ['label' => '我的投资', 'url' => ['/investment/index']],
                ['label' => '奖金明细', 'url' => ['/revenue/index']],
                ['label' => '入账明细', 'url' => ['/revenue/in']],
                ['label' => '出账明细', 'url' => ['/cash/out']],
                [
                    'label' => '提现管理', 'url' => ['/cash/index'],
                    'items' => [
                        ['label' => '提现管理', 'url' => ['/cash/index']],
                        ['label' => '申请提现', 'url' => ['/cash/create']],
                    ]
                ],
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