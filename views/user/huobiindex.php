<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '增减货币';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="Message">
        <?= Yii::$app->getSession()->get('message');
        Yii::$app->getSession()->set('message',null);
        ?>
    </div>

    <p>
    <?= HTML::a('增减货币', ['/revenue/manualadd'],['class' => 'btn btn-success'])?>
    </p>
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
            'bonus_remain',
            'merit_remain',
            'baodan_remain',
            'mall_remain',
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '操作',
                'template' => '{add}',
                'buttons' => [
                    'add' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', '增减货币'),
                                'aria-label' => Yii::t('yii', '增减货币'),
                                'data-ajax' => 0
                            ];
                            return Html::a('增减货币', $url, $options);
                        },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'add') {
                            $url ='/revenue/manualadd?id='.$model->id;
                            return $url;
                        }
                    }
            ],
        ],
    ]); ?>

</div>
