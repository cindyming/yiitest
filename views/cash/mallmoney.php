<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '商城币转海币';
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
    <?= $form->field($model, 'type')->radioList(array('1' => '分红', 2 => '绩效', 3 =>'服务费', '9' => '商城币')) ?>
    <div class="form-group required field-cash-sc_account ">
        <label class="control-label" for="cash-sc_account">入账类型</label>
        <span id="mondyInType">
            <?php if ($model->type ==9 ):?>
                商城海币
            <?php elseif ($model->type): ?>
                商城海宝
            <?php endif ?>

        </span>

        <div class="help-block"></div>
    </div>
    <?= $form->field($model, 'sc_account')->textInput(); ?>
    <?= $form->field($model, 'telephone')->textInput(); ?>
    <?= $form->field($model, 'amount', [])->textInput(['class'=>'amountNumber form-control'])->hint("您输入的金额是: <span class='realAmount'></span>") ?>
    <?= $form->field($model, 'password2')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
    <?= $form->field($model, 'user_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
    <?php ActiveForm::end(); ?>

</div><!-- cash-_form -->
    </div>

<?php $this->beginBlock('mallmoney') ?>

$("input:radio[name='Cash[type]']").change(function () {
    if ($(this).val() == 9) {
        $('#mondyInType').html('商城海币');
    } else if($(this).val()) {
        $('#mondyInType').html('商城海宝');
    }
});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['mallmoney'], \yii\web\View::POS_END); ?>
