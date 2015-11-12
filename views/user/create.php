<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '注册会员';
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div> 会员信息一经提交将不能自行更改，如需更改请联系管理员</div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
