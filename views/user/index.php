<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '序号'],
            [
                'attribute' => 'id',
                'label'=>'会员编号',
            ],
            'username',
            'investment',
            'phone',
            'created_at:datetime',
            [
                'attribute' => 'approved_at',
                'label'     => '状态',
                'value'  => function($model) {
                        return $model->getStatus();
                    }
            ],
        ],
    ]); ?>

</div>
