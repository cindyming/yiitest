<?php
use yiidreamteam\jstree\JsTree;
use yii\helpers\Html;

$this->title = '推荐图';
$this->params['breadcrumbs'][] = $this->title;
?>


<h1><?= Html::encode($this->title) ?></h1>
<?= JsTree::widget([
    'containerOptions' => [
        'class' => 'data-tree',
    ],
    'jsOptions' => [
        'core' => [
            'multiple' => true,
            'data' => $data,
            'themes' => [
                'dots' => true,
                'icons' => false,
            ]
        ],
    ]
]) ?>