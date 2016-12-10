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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '序号'],
            [
                'attribute' => 'id',
                'label'=>'会员编号',
            ],
            [
                'attribute' => 'username',
                'filter' => true,
            ],
            'investment',
            'suggest_by',
            'referer',
            [
                'attribute' => 'phone',
                'filter' => true,
            ],
            'created_at:datetime',
            [
                'attribute' => 'approved_at',
                'label'     => '状态',
                'value'  => function($model) {
                        return $model->getStatus();
                    },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=> [2 => '待审核', 3 => '正式', 4=> '拒绝'],
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
