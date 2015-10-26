<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = '公告管理';
?>
<div class="news-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_adminmenu', []) ?>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

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

</div>
