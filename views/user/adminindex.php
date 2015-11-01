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
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        //'summary' => '',
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'id',
                'filter' => true,
                'label'=>'会员编号',
            ],
            'username',
            'identity',
            [
                'attribute' => 'bank',
                'value' => function($model) {
                        return $model->bank ? $model->getBankNames()[$model->bank] : '';
                    }
            ],
            'cardname',
            'bankaddress',
            'cardnumber',
            'phone',
            [
                'attribute' => 'approved_at',
                'label' => '状态',
                'value' => function($model) {
                        return $model->getStatus();
                    }
            ],
        ],
    ]); ?>

</div>
