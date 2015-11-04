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
        总投资金额 : <?= $inTotal ?>
    </div>
    <div>
        总分红金额 : <?= $bonusTotal ?>
    </div>
    <div>
        总绩效金额 : <?= $meritTotal ?>
    </div>

</div>
