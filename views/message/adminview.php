<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = '留言内容';
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'content:ntext',
            'replied_content:ntext'
        ],
    ]) ?>

    <p>
        <?= Html::a('返回', ['adminindex'], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
