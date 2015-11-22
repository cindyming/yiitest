<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        <?= $form->field($model, 'locked')->dropDownList([0 => '未锁定', 1 => '锁定']) ?>
    <?php endif ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->dropDownList(['Mr' => '先生', 'Ms' => '女士']) ?>

    <?php if($model->isNewRecord): ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password3')->passwordInput(['maxlength' => true]) ?>
    <?php endif ?>

    <?php if(Yii::$app->user->identity->isAdmin()): ?>
        <?php if(!$model->isNewRecord): ?>
        <?= $form->field($model, 'level')->dropDownList(Yii::$app->user->identity->getLevelOptions()) ?>
        <?php endif ?>
        <?= $form->field($model, 'add_member')->dropDownList([0 => '不开放', 2 => '开放']) ?>
    <?php endif ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if(!$model->isNewRecord): ?>
         <?= $form->field($model, 'referer', [ 'template' => "{label}<label class='des'>如会员没有接点人请键入“#”</label>\n{input}\n{hint}\n{error}"])->textInput(['maxlength' => true,'readonly' => true, 'value' => ($model->referer == 0) ? '#' : $model->referer])->label() ?>
    <?php else: ?>
        <?= $form->field($model, 'investment',[ 'template' => "{label}\n{input}<label class='des' style='color:#ff0000'>万,例如你输入1就代表1万</label>\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'referer', [ 'template' => "{label}<label class='des'>如会员没有接点人请键入“#”</label>\n{input}\n{hint}\n{error}", 'options' => ['class' => 'form-group required']])->textInput(['maxlength' => true, 'required'=> true])->label() ?>
    <?php endif ?>

    <?= $form->field($model, 'suggest_by',[ 'template' => "{label}<label class='des'>推荐人ID</label>\n{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank')->dropDownList(['ICBC' => '工商银行', 'ABC' => '农业银行']) ?>

    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>

    <div class="form-group buttons">

        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
