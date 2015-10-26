<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '信息管理';
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_adminmenu', []) ?>

    <h3>
       公告信息
    </h3>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterPosition' => 'FILTER_POS_HEADER',
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'title',
            [
                'attribute' => 'be_top',
                'label'=>'是否置顶',
                'value'=> function($model) {
                    if ($model->be_top == 0) {
                        return '正常';
                    } else {
                        return '置顶';
                    }

                 },
                'filter' => '<input type="text"/>',     //此处我们可以将筛选项组合成key-value形式
            ],
            'public_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
