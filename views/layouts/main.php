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

    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?e9404ddcffdebefc426c52671ec0b323";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
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

        <?php if (Yii::$app->getSession()->get('danger')): ?>
            <div class="fail" >
                <?= Yii::$app->getSession()->get('danger');
                Yii::$app->getSession()->set('danger',null);
                ?>
            </div>
        <?php endif ?>

        <?php if (!\app\models\System::loadConfig('maintenance')) :?>
            <div class="alert alert-danger">系统维护中，请不要操作任何数据，您的操作记录不会被保存</div>
        <?php endif ?>
        <!-- Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) -->
        <?= $content ?>
    </div>
</div>


<?php $this->endBody() ?>

<?php $this->beginBlock('js') ?>
$('body').find('[type=submit]').click(function(){
    $(this).hide();
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</body>
</html>
<?php $this->endPage() ?>
