<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cash */
/* @var $form ActiveForm */
?>
<div class="cash-_form sm-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'bank')->dropDownList($model->getBankNames(), ['value' => Yii::$app->user->identity->bank]) ?>
        <?= $form->field($model, 'type')->label('')->hiddenInput() ?>
        <?= $form->field($model, 'cardnumber')->textInput(['value' => Yii::$app->user->identity->cardnumber, 'readonly' => true]); ?>
        <?= $form->field($model, 'cardname')->textInput(['value' => Yii::$app->user->identity->cardname]); ?>
        <?= $form->field($model, 'bankaddress')->textInput(['value' => Yii::$app->user->identity->bankaddress, 'readonly' => true]); ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'password2')->passwordInput() ?>
        <div class="form-group">
            <?= Html::submitButton('确认[分红]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(1)"]) ?>
            <?= Html::submitButton('确认[绩效]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(2)"]) ?>
            <?= Html::submitButton('确认[服务费]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(3)"]) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
