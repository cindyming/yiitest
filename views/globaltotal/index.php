<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GlobalTotalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '总账拨比统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="global-total-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('结算', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            'total_in',
            'merit',
            'bonus',
            'mall',
            'baodan',
            'total_out',
            [
                'class' => 'yii\grid\Column',
                'header' => '比率',
                'content' => function($model){
                        return $model->total_in ? $model->total_out / $model->total_in : 0;
                    }
            ],
            'created_at',
        ],
    ]); ?>

</div>
