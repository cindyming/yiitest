<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '转账给报单员';
?>

<div class="cash-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<ul class="tabswitch">
		<?php if(\app\models\System::loadConfig('open_cash')):?>
			<li><?= HTML::a('申请现金提现', ['/cash/create'])?></li>
		<?php endif ?>
		<li><?= HTML::a('申请股票提现', ['/cash/create', 'type' => 'transfer'])?></li>
		<li class="active">转账给报单员</li>
		<?php if(\app\models\System::loadConfig('open_mall_transfer')):?>
			<li><?= HTML::a('商城币转海币', ['/cash/create', 'type' => 'mallmoney'])?></li>
		<?php endif ?>
	</ul>

	<div class="cash-_form sm-form">

		<?php $form = ActiveForm::begin(); ?>
		<?= $form->field($model, 'user_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
		<?= $form->field($model, 'type')->label('')->hiddenInput() ?>
		<?= $form->field($model, 'baodan_id', ['options' => ['class' => 'form-group required']])->textInput(array( 'required'=> true)); ?>
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
