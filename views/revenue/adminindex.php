<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Revenues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revenue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Revenue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'user_id',
            'approved',
            'bonus',
            'merit',
            [
                'class' => 'yii\grid\Column',
                'label' => '总额',
                'content' => function($model) {
                        return $model->bonus + $model->merit;
                    }
            ],
            'created_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
