<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '添加货币';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="tabswitch">
        <li class="active">添入会员账户</li>
        <li><?= HTML::a('扣出会员账户', ['/cash/manualadd'])?></li>
    </ul>

    <div class="cash-_form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'user_id')->textInput(); ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'type')->radioList([1 => '分红', 2=> '绩效', 3=> '服务费', 4=> '商城币']); ?>
        <?= $form->field($model, 'note')->textInput(['value' => '预存金额']) ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div><!-- cash-_form -->

</div>
