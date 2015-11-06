<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cash */
/* @var $form ActiveForm */
?>
<div class="cash-_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'bank')->dropDownList($model->getBankNames()) ?>
        <?= $form->field($model, 'type')->label('')->hiddenInput() ?>
        <?= $form->field($model, 'cardnumber') ?>
        <?= $form->field($model, 'cardname') ?>
        <?= $form->field($model, 'bankaddress') ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'password2')->passwordInput() ?>
        <div class="form-group">
            <?= Html::submitButton('确认[分红]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(1)"]) ?>
            <?= Html::submitButton('确认[绩效]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(2)"]) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
