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

        <div id="changeFirstPassword" class="two-cols">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password_old', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '原一级密码', 'value' => '', 'required' => true]) ?>

            <?= $form->field($model, 'password', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '新一级密码', 'value' => '', 'required' => true]) ?>

            <?= $form->field($model, 'password1', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '确认新一级密码', 'required' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


        <div id="changeSecondPassword" class="two-cols">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password2_old', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '原二级密码', 'value' => '', 'required' => true]) ?>

            <?= $form->field($model, 'password2', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '新二级密码', 'value' => '', 'required' => true]) ?>

            <?= $form->field($model, 'password3', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '确认新二级密码', 'required' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>
