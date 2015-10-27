<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
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
    <div class="top">
        <?php
        $h=date('G');
        $welcome = ($h<11) ? '早上好' : (($h<13) ? '中午好' : (($h<17) ? '下午好' : '晚上好'));
        $welcome .= ', ' . Yii::$app->user->identity->username;
        $welcome .= ', 欢迎回来.';
        ?>
        <h3>在线办公平台</h3>
        <div>
            会员ID: <?php echo Yii::$app->user->id?>
            分红余额:
            绩效工资余额:
        </div>

    </div>
    <div class="left sidebar">
    <?php
    NavBar::begin();
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            ['label' => '信息管理', 'url' => ['/site/index']],
            [
                'label' => '会员专区', 'url' => ['/site/about'],
                'items' => [
                    ['label' => '修改密码', 'url' => ['/user/changepassword']],
                    ['label' => '会员资料', 'url' => ['/user/view', 'id' => Yii::$app->user->identity->id]]
                ]
            ],
            [
                'label' => '业务中心', 'url' => ['/user/create'],
                'items' => [
                    ['label' => '会员注册', 'url' => ['/user/create']],
                    ['label' => '我的推荐', 'url' => ['/user/index', 'referer' => Yii::$app->user->identity->id]]
                ]

            ],
            ['label' => '财务中心', 'url' => ['/site/contact']],
            ['label' => '电子账户', 'url' => ['/site/contact']],
        ],
    ]);
    NavBar::end();
    ?>

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
                    ['label' => '奖金明细', 'url' => ['/site/contact']],
                    ['label' => '奖金统计', 'url' => ['/site/contact']],
                    ['label' => '申请提现', 'url' => ['/site/contact']],
                    ['label' => '添加留言', 'url' => ['/message/add']],
                    Yii::$app->user->isGuest ?
                        ['label' => '安全退出', 'url' => ['/site/login']] :
                        [
                            'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
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
