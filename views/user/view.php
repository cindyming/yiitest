<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->title;
?>
<div class="user-view">

    <h1><?= Html::encode($model->username) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'identity',
            'phone',
            'title',
            'referer',
            'investment',
            [
                'attribute' => 'bank',
                'value' => $model->bank ? $model->getBankNames()[$model->bank] : ''
            ],
            'cardname',
            'cardnumber',
            'bankaddress',
            'email:email',
            'qq',
        ],
    ]) ?>

</div>
