<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = '公告详细';
?>
<div class="news-view">

    <h1><?= Html::encode($model->title) ?></h1>

    <div class="news-date"><?php echo ($model->public_at) ? $model->public_at : $model->created_at?></div>
    <div class="new-content">
        <?php echo $model->content?>

    </div>
    <p>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>
</div>
