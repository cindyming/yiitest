<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '申请提现';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
