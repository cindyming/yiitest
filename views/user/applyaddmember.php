<?php
use yii\helpers\Html;

$this->title = '申请报单员' . ($status == 'success' ? '成功' : '诶哟');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="<?= $status ?>">
        <?= $message?>
    </div>