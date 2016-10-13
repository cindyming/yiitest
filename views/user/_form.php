<?php

use yii\helpers\Html;
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
            'enableAjaxValidation' => true,
            'validateOnBlur' => true,
            'validationUrl' => '/user/validate?' . ($model->id ? 'id=' . $model->id : ''),
            'attributes' => array(
                'username',
                'referer'
            )
        ]
    ); ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        <?= $form->field($model, 'locked')->dropDownList([0 => '未锁定', 1 => '锁定']) ?>
    <?php endif ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'class' => 'popup form-control']) ?>

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
         <?= $form->field($model, 'referer', [ 'template' => "{label}<label class='des'>如会员没有接点人请键入“#”</label>\n{input}\n{hint}\n{error}"])->textInput(['class' => 'popup form-control','maxlength' => true,'readonly' => true, 'value' => ($model->referer == 0) ? '#' : $model->referer])->label() ?>
    <?php else: ?>
        <?= $form->field($model, 'investment',[ 'template' => "{label}\n{input}<label class='des' style='color:#ff0000'>万,例如你输入1就代表1万</label>\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'referer', [ 'template' => "{label}<label class='des'>如会员没有接点人请键入“#”</label>\n{input}\n{hint}\n{error}", 'options' => ['class' => 'form-group required']])->textInput(['class' => 'popup form-control', 'maxlength' => true, 'required'=> true])->label() ?>
        <?php if (Yii::$app->user->identity->isBaodan() && Yii::$app->user->identity->duichong_remain) : ?>
            <?= $form->field($model, 'useBaodan')->checkbox([1 => '使用对冲帐户余额'])?>
            <?= $form->field($model, 'duichong_invest')->textInput() ?>
        <?php endif ?>
    <?php endif ?>

    <?= $form->field($model, 'suggest_by',[ 'template' => "{label}<label class='des'>推荐人ID</label>\n{input}\n{hint}\n{error}", 'options' => ['class' => 'form-group required']])->textInput(['class' => 'popup form-control','maxlength' => true, 'value' => ($model->id && ($model->suggest_by == 0)) ? '#' : $model->suggest_by]) ?>

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

<?php $this->beginBlock('js') ?>
$('.field-user-duichong_invest').hide();
$('#user-usebaodan').click(function(){ console.log($(this).is(':checked'));
if ($(this).is(':checked')) {
$('.field-user-duichong_invest').show();
} else {
$('.field-user-duichong_invest').hide();
}

});

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
