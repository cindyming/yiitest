<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Investment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="investment-form sm-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>
    <?= $form->field($model, 'added_by')->textInput() ?>

    <?= $form->field($model, 'amount',[ 'template' => "{label}\n{input}<label class='des' style='color:#ff0000'>万,例如你输入1就代表1万</label>\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'useBaodan', [ 'template' => "{label}\n{input}<label id='message'></label>"])->checkbox([1 => '使用对冲帐户余额'])?>
    <?= $form->field($model, 'duichong_invest', [ 'template' => "{label}\n{input}<label id='duichongRemain'></label>"])->textInput() ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    $('.field-investment-duichong_invest').hide();
    $('#investment-usebaodan').click(function(){
    if ($('#investment-usebaodan').is(':checked')) {
        if ($('#investment-added_by').val()) {
            $.ajax({
                url: "/investment/showduichong?id=" + $('#investment-added_by').val(),
                async: false,
                dataType: 'json',
                success: function (result) {
                    if (result.code == 1) {
                        $('#duichongRemain').html('对冲帐户余额:' + result.message);
                        $('.field-investment-duichong_invest').show();
                    } else {
                        $('.field-investment-duichong_invest').hide();
                        $('#message').html(result.message);
                        $('#investment-usebaodan').attr('checked', false);
                    }
                }
            });
        } else {
            $('.field-investment-duichong_invest').hide();
            $('#investment-usebaodan').attr('checked', false);
            alert('请先输入报单员号码');
        }
    } else {
        $('.field-investment-duichong_invest').hide();
    }
    });
</script>

<?php $this->beginBlock('js') ?>
    $('.field-investment-duichong_invest').hide();
    $('#investment-usebaodan').click(function(){
    if ($('#investment-usebaodan').is(':checked')) {
    if ($('#investment-added_by').val()) {
    $.ajax({
    url: "/investment/showduichong?id=" + $('#investment-added_by').val(),
    async: false,
    dataType: 'json',
    success: function (result) {
    if (result.code == 1) {
    $('#duichongRemain').html('对冲帐户余额:' + result.message);
    $('.field-investment-duichong_invest').show();
    } else {
    $('.field-investment-duichong_invest').hide();
    $('#message').html(result.message);
    $('#investment-usebaodan').attr('checked', false);
    }
    }
    });
    } else {
    $('.field-investment-duichong_invest').hide();
    $('#investment-usebaodan').attr('checked', false);
    alert('请先输入报单员号码');
    }
    } else {
    $('.field-investment-duichong_invest').hide();
    }
    });
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>