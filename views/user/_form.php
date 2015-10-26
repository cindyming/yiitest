<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'referer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
