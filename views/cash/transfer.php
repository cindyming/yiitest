<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '申请股票提现';
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
    <?= $form->field($model, 'stack_number', ['options' => ['class' => 'form-group required']])->textInput(); ?>
    <?= $form->field($model, 'amount', [])->textInput(['class'=>'amountNumber form-control'])->hint("您输入的金额是: <span class='realAmount'></span>") ?>
    <?= $form->field($model, 'password2')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
    <?= $form->field($model, 'user_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
    </div>


