<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '我的报单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '序号'],
            [
                'attribute' => 'id',
                'label'=>'会员编号',
                'filter' => true,
            ],
            [
                'attribute' => 'username',
                'filter' => true,
            ],
            'investment',
            [
                'attribute' => 'suggest_by',
                'filter' => true,
            ],
            [
                'attribute' => 'referer',
                'filter' => true,
            ],
            [
                'attribute' => 'phone',
            ],
            'created_at:datetime',
            [
                'attribute' => 'approved_at',
                'label'     => '状态',
                'value'  => function($model) {
                        return $model->getStatus();
                    }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '修改',
                'hiddenFromExport' => true,
                'template' => '{update}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url ='/user/update?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
