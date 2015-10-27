<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;



/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '密码修改';
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <div id="changeFirstPassword">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password_old')->passwordInput(['maxlength' => true, 'label' => '原一级密码']) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'label' => '新一级密码']) ?>

            <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true, 'label' => '确认新一级密码']) ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


        <div id="changeSecondPassword">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password2_old')->passwordInput(['maxlength' => true, 'label' => '原二级密码']) ?>

            <?= $form->field($model, 'password2')->passwordInput(['maxlength' => true, 'label' => '新二级密码']) ?>

            <?= $form->field($model, 'password3')->passwordInput(['maxlength' => true, 'label' => '确认新二级密码']) ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>
