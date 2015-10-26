<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_adminmenu', []) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'number',
            'username',
            // 'password',
            // 'password2',
            // 'identity',

            // 'title',
            // 'referer',
            'investment',
            'phone',
            'identity',
            // 'bank',
            // 'cardname',
            // 'cardnumber',
            // 'bankaddress',
            // 'email:email',
            // 'qq',
            // 'created_at',
            // 'updated_at',
            'approved_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
