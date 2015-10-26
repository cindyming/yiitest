<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="user-view">

    <h1><?= Html::encode($model->username) ?></h1>

    <?= $this->render('_adminmenu', []) ?>

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
            'bank',
            'cardname',
            'cardnumber',
            'bankaddress',
            'email:email',
            'qq',
        ],
    ]) ?>

</div>
