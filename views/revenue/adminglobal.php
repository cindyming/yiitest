<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '拨比统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        总投资金额 : <?= round($inTotal, 2) ?>
    </div>
    <div>
        总分红金额 : <?= round($bonusTotal, 2) ?>
    </div>
    <div>
        总绩效金额 : <?= round($meritTotal, 2) ?>
    </div>
    <div>
        总报单金额 : <?= round($baodanTotal, 2) ?>
    </div>
    <div>
        商城币金额 : <?= round($mallTotal, 2) ?>
    </div>
    <div>
        总剩余金额 : <?= round($inTotal, 2) - round($bonusTotal, 2) -  round($meritTotal, 2) - round($mallTotal, 2) ?>
    </div>

</div>
