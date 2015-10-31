<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;


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
        'filterModel' => $searchModel,
        'striped'=> true,
        'hover'=> true,
        //'summary' => '',
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '编号'
            ],
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
                'filterType'=>GridView::FILTER_SELECT2,
                'filter'=>$searchModel->getBetopOptions(),
            ],
            [
                'attribute' => 'public_at',
                'filterType' => GridView::FILTER_DATE_RANGE,
                'format' => 'date',
                'filter' => DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'public_at',
                        'convertFormat'=>true,
                        'pluginOptions'=>[
                            'timePicker'=> false,
                            'timePickerIncrement'=>30,
                            'locale'=>['format'=>'DD-MMM-YYYY'], // from demo config
                            'opens'=>'left'
                        ],
                        'useWithAddon'=>true,
                        'language' => 'zh-CN',             // from demo config
                        'hideInput'=> true,           // from demo config
                        'presetDropdown'=> false, // from demo config
                    ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '删除',
                'template' => '{delete}',
                'buttons' => [

                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'delete') {
                            $url ='/news/admindelete?id='.$model->id;
                            return $url;
                        }
                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '修改',
                'template' => '{update}',
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'update') {
                            $url ='/news/adminupdate?id='.$model->id;
                            return $url;
                        }
                }
            ],
        ],
    ]); ?>

</div>
