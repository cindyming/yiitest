<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '申请现金提现';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= $this->render('_tabs', [
        'type' => $type,
    ]) ?>
    <?= $this->render('_form', [
        'model' => $model,
        'type' => $type
    ]) ?>

</div>
