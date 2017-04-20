<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '提现撮合';
?>

<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_tabs', [
        'type' => $type,
    ]) ?>

<div class="cash-_form sm-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validationUrl' => '/cash/validate?type='.$type,
    ]); ?>
    <?= $form->field($model, 'type')->radioList(array('1' => '分红', 2 => '绩效', 3 =>'服务费')) ?>
    <div class="form-group required field-cash-sc_account ">
        <label class="control-label" for="cash-sc_account">入账类型</label>
        <span id="mondyInType">
                撮合现金
        </span>

        <div class="help-block"></div>
    </div>
    <?= $form->field($model, 'amount', [])->textInput(['class'=>'amountNumber form-control'])->hint("您输入的金额是: <span class='realAmount'></span>") ?>
    <?= $form->field($model, 'password2')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
    <?= $form->field($model, 'user_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
    </div>

