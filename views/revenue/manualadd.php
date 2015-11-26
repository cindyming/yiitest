<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '增减货币';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('返回', ['/user/huobi'])?>

    <ul class="tabswitch">
        <li class="active">添入会员账户</li>
        <li><?= HTML::a('扣出会员账户', ['/cash/manualadd', 'id' => Yii::$app->getRequest()->get('id')])?></li>
    </ul>

    <div class="cash-_form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'user_id')->textInput(['value' => Yii::$app->getRequest()->get('id')]); ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'type')->radioList([1 => '分红', 2=> '绩效', 3=> '服务费', 4=> '商城币']); ?>
        <?= $form->field($model, 'note')->textInput(['value' => '预存金额']) ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div><!-- cash-_form -->

</div>
