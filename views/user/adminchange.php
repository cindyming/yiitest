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
            <?php if ($status !== null): ?>
                <div class="<?php echo ($status) ? 'success' : 'fail'?>">
                     <?= $message ?>
                </div>
            <?php endif ?>
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'old_username')->textInput(['maxlength' => true, 'readonly' => true, 'value' => $model->username]) ?>

            <?= $form->field($model, 'password_old')->passwordInput(['maxlength' => true, 'label' => '原一级密码'])->label('原密码') ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'value' => $model->username])->label('新登录名') ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'label' => '新一级密码', 'value' => ''])->label('新密码') ?>

            <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true, 'label' => '确认新一级密码', 'value' => ''])->label('确认新密码')  ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>
