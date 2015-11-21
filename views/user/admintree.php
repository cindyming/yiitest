<?php
use yiidreamteam\jstree\JsTree;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '网络图';
$this->params['breadcrumbs'][] = $this->title;
?>


<h1><?= Html::encode($this->title) ?></h1>


    <div class="user-search">

        <?php $form = ActiveForm::begin(['method' => 'get',]); ?>

        <?= $form->field(new app\models\User(), 'id')->textInput(['value' => Yii::$app->getRequest()->get('id'), 'name' => 'id']) ?>

        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?= JsTree::widget([
    'containerOptions' => [
        'class' => 'data-tree',
    ],
    'jsOptions' => [
        'core' => [
            'multiple' => true,
            'data' => $data,
            'themes' => [
                'dots' => true,
                'icons' => false,
            ]
        ],
    ]
]) ?>