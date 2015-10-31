<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = '公告详细';
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'be_top',
            'title',
            'content:ntext',
            'created_at',
            'updated_at',
            'public_at',
        ],
    ]) ?>

    <p>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>
</div>
