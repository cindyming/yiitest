<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?php endif ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->dropDownList(['Mr' => '先生', 'Ms' => '女士']) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password3')->passwordInput(['maxlength' => true]) ?>

    <?php if(Yii::$app->user->identity->isAdmin()): ?>
        <?= $form->field($model, 'level')->dropDownList(Yii::$app->user->identity->getLevelOptions()) ?>
        <?= $form->field($model, 'add_member')->dropDownList([0 => '不开放', 2 => '开放']) ?>
    <?php endif ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->isNewRecord): ?>
    <?= $form->field($model, 'locked')->dropDownList([0 => '未锁定', 1 => '锁定']) ?>
    <?= $form->field($model, 'referer', [ 'template' => "{label}： <label>如会员没有推荐人请键入“#”</label>\n{input}\n{hint}\n{error}"])->textInput(['maxlength' => true,'readonly' => true])->label() ?>
    <?php endif ?>
    <?= $form->field($model, 'bank')->dropDownList(['ICBC' => '工商银行', 'ABC' => '农业银行']) ?>

    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>

    <div class="form-group">

        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
