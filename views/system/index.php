<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\System */
?>
<div class="system-create">
	<h1><?= Html::encode($this->title) ?></h1>
    <div class="system-form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'open_member_tree')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'open_baodan_tree')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'enable_memmber_login')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'duichong_audit')->dropDownList([1 => '是', 0 => '否']) ?>
        <?= $form->field($model, 'opend_duichong_baodan_fee')->dropDownList([1 => '是', 0 => '否']) ?>
        <?= $form->field($model, 'opend_investment_duichong_baodan_fee')->dropDownList([1 => '是', 0 => '否']) ?>
        <?= $form->field($model, 'maintenance')->dropDownList([1 => '否', 0 => '是']) ?>
        <?= $form->field($model, 'open_suggest_list')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'mall_audit')->dropDownList([1 => '是', 0 => '否']) ?>
        <?= $form->field($model, 'open_mall_transfer')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'open_cash')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'lowest_cash_amount',[ 'template' => "{label}\n{input}百，如你输入1就代表1百\n{hint}\n{error}"])->textInput(['value' => ($model->lowest_cash_amount / 100)]) ?>
        <?= $form->field($model, 'cash_factorage', [ 'template' => "{label}\n{input}%\n{hint}\n{error}"])->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>
