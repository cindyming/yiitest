<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;  // http://www.yiiframework.com/extension/yii2-ckeditor-widget/

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'be_top')->dropDownList(['0' => '正常', '1' => '置顶']) ?>

    <?= $form->field($model, 'public_at')->widget(\kartik\datetime\DateTimePicker::classname(), [
        'language' => '',
        'convertFormat' => true,
        'pluginOptions' => [
            'autoclose'=>true,
            'timePicker'=> false,
            'format' => 'dd-M-yyyy',
        ]
    ]) ?>

    <?= $form->field($model, 'content')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
