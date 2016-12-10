<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Update User: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model app\models\User */
    /* @var $form yii\widgets\ActiveForm */
    ?>

    <div class="user-form">

        <div id="errorMessageContainer" style="display:none">
            <div id="errorMessage">
                <div id="errorMessageHtml">
                </div>
                <button id="skipError">确认</button>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            ]
        ); ?>

        <?php if (!$model->isNewRecord): ?>
            <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            <?= $form->field($model, 'locked')->dropDownList([0 => '未锁定', 1 => '锁定'],['readonly' => true]) ?>
        <?php endif ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'readonly' => true, 'class' => 'popup form-control']) ?>

        <?= $form->field($model, 'title')->dropDownList(['Mr' => '先生', 'Ms' => '女士'],[ 'readonly' => true]) ?>


        <?= $form->field($model, 'identity')->textInput(['maxlength' => true, 'readonly' => true]) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        <div class="clearfix"></div>
        <?php if(!$model->isNewRecord): ?>
        <div class="clearfix">
            <?= $form->field($model, 'referer', [ 'template' => "{label}<label class='des'>如会员没有接点人请键入“#”</label>\n{input}\n{hint}\n{error}"])->textInput(['maxlength' => true,'readonly' => true, 'value' => ($model->referer == 0) ? '#' : $model->referer])->label() ?>
            <?php endif ?>

            <?= $form->field($model, 'suggest_by',[ 'template' => "{label}<label class='des'>推荐人ID</label>\n{input}\n{hint}\n{error}", 'options' => ['class' => 'form-group required']])->textInput(['maxlength' => true, 'class' => 'popup form-control', 'value' => (($model->suggest_by == 0) && (!$model->isNewRecord)) ? '#' : $model->suggest_by]) ?>

            <?= $form->field($model, 'bank')->dropDownList(['ICBC' => '工商银行', 'ABC' => '农业银行'], ['readonly' => true]) ?>

            <?= $form->field($model, 'cardname')->textInput(['maxlength' => true, 'readonly' => true]) ?>

            <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true, 'readonly' => true]) ?>

            <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true, 'readonly' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'readonly' => true]) ?>

            <?= $form->field($model, 'qq')->textInput(['maxlength' => true, 'readonly' => true]) ?>

            <div class="form-group buttons">

                <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>


</div>
