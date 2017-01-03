<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '转账到商城';
?>

<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="tabswitch">
        <?php if(\app\models\System::loadConfig('open_cash')):?>
        <li><?= HTML::a('申请现金提现', ['/cash/create'])?></li>
        <?php endif ?>
        <li><?= HTML::a('申请股票提现', ['/cash/create', 'type' => 'transfer'])?></li>
        <li><?= HTML::a('转账给报单员', ['/cash/create', 'type' => 'baodan'])?></li>
        <?php if(\app\models\System::loadConfig('open_mall_transfer')):?>
            <li class="active">转账到商城</li>
        <?php endif ?>
    </ul>

<div class="cash-_form sm-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'sc_account', ['options' => ['class' => 'form-group required']])->textInput(array( 'required'=> true)); ?>
    <?= $form->field($model, 'amount') ?>
    <?= $form->field($model, 'password2')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('确认提现', ['class' => 'btn btn-primary']) ?>
    </div>
    <?= $form->field($model, 'user_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
    <?= $form->field($model, 'type')->label('')->hiddenInput(['value' => 9]) ?>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
    </div>
