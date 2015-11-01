<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use kartik\sidenav\SideNav;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap member">
    <div class="top-header">
        <div class="container">
            <?php
            $h=date('G');
            $welcome = ($h<11) ? '早上好' : (($h<13) ? '中午好' : (($h<17) ? '下午好' : '晚上好'));
            $welcome .= ', ' . Yii::$app->user->identity->username;
            $welcome .= ', 欢迎回来.';
            ?>
            <h3>在线办公平台</h3>
            <div class="m-info">
                会员ID: <?php echo Yii::$app->user->id?>
                分红余额:
                绩效工资余额:
            </div>
        </div>
    </div>
    <div class="menubar">
        <div class="container">
        <?php
            echo SideNav::widget([
                'encodeLabels' => false,
            'items' => [
                [
                    'label' => '会员管理', 'url' => ['/site/about'],
                    'items' => [
                        ['label' => '修改密码', 'url' => ['/user/changepassword']],
                        ['label' => '会员资料', 'url' => ['/user/view', 'id' => Yii::$app->user->identity->id]]
                    ]
                ],
                [
                    'label' => '财务管理', 'url' => ['/site/contact'],
                    'items' => [
                        ['label' => '奖金明细', 'url' => ['/revenue/index']],
                        ['label' => '奖金统计', 'url' => ['/revenue/total']]
                    ]
                ],
                [
                    'label' => '电子货币', 'url' => ['/site/contact'],
                    'items' => [
                        ['label' => '入账明细', 'url' => ['/revenue/index']],
                        ['label' => '出帐明细', 'url' => ['/revenue/total']],
                        ['label' => '申请提现', 'url' => ['/revenue/index']],
                        ['label' => '汇款提醒', 'url' => ['/revenue/index']],
                    ]
                ],
                [
                    'label' => '信息管理', 'url' => ['/news/index'],
                    'items' => [
                        ['label' => '新闻公告', 'url' => ['/news/index']],
                        ['label' => '留言列表', 'url' => ['/message/index', 'user_id' => Yii::$app->user->identity->id]],
                        ['label' => '添加留言', 'url' => ['/message/create']]
                    ]
                ],
                [
                    'label' => '业务中心', 'url' => ['/user/create'],
                    'items' => [
                        ['label' => '会员注册', 'url' => ['/user/create']],
                        ['label' => '我的推荐', 'url' => ['/user/index', 'referer' => Yii::$app->user->identity->id]]
                    ]

                ],
            ],
        ]);
        ?>
        </div>
    </div>

    <div class="container">
        <div class="top nav">
            <?php
            NavBar::begin();
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => [
                    ['label' => '首页', 'url' => ['/news/index']],
                    ['label' => '系统公告', 'url' => ['/news/index']],
                    ['label' => '注册会员', 'url' => ['/user/create']],
                    ['label' => '奖金明细', 'url' => ['/revenue/index']],
                    ['label' => '奖金统计', 'url' => ['/revenue/total']],
                    ['label' => '申请提现', 'url' => ['/revenue/withdraw']],
                    ['label' => '添加留言', 'url' => ['/message/create']],
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
        </div>
        <!-- Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) -->
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
