<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cash */

$this->title = '申请现金提现';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>


    <ul class="tabswitch">
        <?php if(\app\models\System::loadConfig('open_cash')):?>
        <li class="active">申请现金提现</li>
        <?php endif ?>
        <li><?= HTML::a('申请股票提现', ['/cash/create', 'type' => 'transfer'])?></li>
        <li><?= HTML::a('转账给报单员', ['/cash/create', 'type' => 'baodan'])?></li>
    </ul>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
