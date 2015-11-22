<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '玫瑰家园 v2';
?>
<div class="loginbox">
    <h1><?= Html::encode($this->title) ?></h1>

    <div id="maintaining">
        <?php if (!(\app\models\System::loadConfig('enable_memmber_login'))):?>
            维护中
        <?php endif ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'loginform',
        'options' => ['class' => 'form-horizontal'],
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(),
        ['captchaAction'=>'site/captcha',
            'imageOptions'=>['alt'=>'点击换图','title'=>'点击换图', 'style'=>'cursor:pointer']
    ]) ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('登录', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
