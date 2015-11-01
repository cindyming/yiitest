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
                            ['label' => '推荐图', 'url' => yii\helpers\Url::to('/user/digram')],
                        ],
                    ],
                    [
                        'label' => '财务管理', 'url' => ['/invest/adminabout'],
                        'items' => [
                            ['label' => '奖金结算', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '奖金统计', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '拨比统计', 'url' => yii\helpers\Url::to('/')],
                        ]
                    ],
                    [
                        'label' => '电子货币', 'url' => ['/'],
                        'items' => [
                            ['label' => '添加货币', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '账户管理', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '出帐明细', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '入账明细', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '体现管理', 'url' => yii\helpers\Url::to('/')],
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
                        'label' => '系统管理', 'url' => ['/'],
                        'items' => [
                            ['label' => '数据管理', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '密码修改', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '系统参数', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '制度参数', 'url' => yii\helpers\Url::to('/')],
                            ['label' => '系统日志', 'url' => yii\helpers\Url::to('/')],
                        ]
                    ],
                    ['label' => '备份数据库', 'url' => ['/system/backup']],
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
        </div>
    </div>

    <div class="container">
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
