<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use kartik\sidenav\SideNav;
use app\assets\AppAsset;

AppAsset::register($this);

$isAdmin = Yii::$app->user->identity->isAdmin();
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
<body class="<?php echo $isAdmin ? 'u_admin' : 'u_member'; ?>">
<?php $this->beginBody() ?>

<div class="wrap <?php if (!$isAdmin): ?> member <?php endif ?>">
    <div class="top-header">
        <div class="container">
            <?php if ($isAdmin): ?>
                <?= $this->render('adminheader') ?>
            <?php else: ?>
                <?= $this->render('memberheader') ?>
            <?php endif ?>
        </div>
    </div>

    <div class="container">
        <?php if (Yii::$app->getSession()->get('message')): ?>
            <div class="Message">
                <?= Yii::$app->getSession()->get('message');
                Yii::$app->getSession()->set('message',null);
                ?>
            </div>
        <?php endif ?>
        <!-- Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) -->
        <?= $content ?>
    </div>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
