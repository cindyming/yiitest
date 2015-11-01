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

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password3')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->dropDownList(['Mr' => '先生', 'Ms' => '女士']) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->isNewRecord): ?>
    <?= $form->field($model, 'locked')->dropDownList([0 => '未锁定', 1 => '锁定']) ?>
    <?php endif ?>

    <?= $form->field($model, 'referer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank')->dropDownList(['ICBC' => '工商银行', 'ABC' => '农业银行']) ?>

    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>

    <div class="form-group">

        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
