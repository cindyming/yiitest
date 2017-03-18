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
        <li><?= HTML::a('添入会员账户', ['/revenue/manualadd', 'id' => Yii::$app->getRequest()->get('id')])?></li>
        <li class="active">扣出会员账户</li>
    </ul>

    <div class="cash-_form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'user_id')->textInput(['value' => Yii::$app->getRequest()->get('id')]); ?>
        <?= $form->field($model, 'amount', [])->textInput(['class'=>'amountNumber'])->hint("您输入的金额是: <span class='realAmount'></span>") ?>
        <?= $form->field($model, 'type')->radioList([4 => '分红', 5=> '绩效', '6' => '服务费', '7' => '商城币', 8 => '对冲帐户']); ?>
        <?= $form->field($model, 'note')->textInput(['value' => '系统扣除金额']) ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div><!-- cash-_form -->

</div>
