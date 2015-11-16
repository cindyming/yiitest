<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\System */
?>
<div class="system-create">

    <div class="system-form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'open_member_tree')->dropDownList([1 => '开放', 0 => '关闭']) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
'
        <?php ActiveForm::end(); ?>

    </div>

</div>
