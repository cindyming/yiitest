<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\sidenav\SideNav;

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

<div class="wrap">
    <div class="top-header">
        <div class="container">
            <?php
            echo SideNav::widget([
                'encodeLabels' => false,
                'heading' => '<div style="margin:20px 5px"> ADMIN </div>',
                'items' => [
                    ['label' => '会员管理', 'url' => ['/user/adminindex']],
                    ['label' => '财务管理', 'url' => ['/invest/about']],
                    ['label' => '电子货币', 'url' => ['/site/contact']],
                    ['label' => '信息管理', 'url' => ['/news/adminindex']],
                    ['label' => '系统管理', 'url' => ['/site/contact']],
                 ]]);
            ?>
        </div>
    </div>

    <div class="container">
        <div class="top">
            <?php
            NavBar::begin();
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => [
                    ['label' => '欢迎 (' . Yii::$app->user->identity->username . ')', 'url' => ['/user/index']],
                    ['label' => '备份数据库', 'url' => ['/system/backup']],
                    Yii::$app->user->isGuest ?
                        ['label' => 'Login', 'url' => ['/site/login']] :
                        [
                            'label' => '退出',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']
                        ],
                ],
            ]);
            NavBar::end();
            ?>
        </div>
        <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])     ?>
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
