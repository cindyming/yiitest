<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '申请股票提现';
?>

<div class="cash-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<ul class="tabswitch">
		<li><?= HTML::a('申请现金提现', ['/cash/create'])?></li>
		<li class="active">申请股票提现</li>
	</ul>

	<div class="cash-_form sm-form">

		<?php $form = ActiveForm::begin(); ?>
		<?= $form->field($model, 'type')->label('')->hiddenInput() ?>
		<?= $form->field($model, 'stack_number', ['options' => ['class' => 'form-group required']])->textInput(array( 'required'=> true)); ?>
		<?= $form->field($model, 'amount') ?>
		<?= $form->field($model, 'password2')->passwordInput() ?>
		<div class="form-group">
			<?= Html::submitButton('确认[分红]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(1)"]) ?>
			<?= Html::submitButton('确认[绩效]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(2)"]) ?>
			<?= Html::submitButton('确认[服务费]提现', ['class' => 'btn btn-primary', 'onClick' => "$('#cash-type').val(3)"]) ?>
		</div>
		<?php ActiveForm::end(); ?>

	</div><!-- cash-_form -->
</div>
