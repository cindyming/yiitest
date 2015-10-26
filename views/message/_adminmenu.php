<?php
use yii\helpers\Html;

$this->title = '信息管理';
$this->params['breadcrumbs'][0] = '信息管理';
?>
<div>
    <ul>
        <li>
            <?= HTML::a('公告管理', yii\helpers\Url::to('/news/adminindex')) ?>
        </li>
        <li>
            <?= HTML::a('添加公告', yii\helpers\Url::to('/news/admincreate')) ?>
        </li>
        <li>
            <?= HTML::a('信息管理', yii\helpers\Url::to('/message/adminindex')) ?>
        </li>
    </ul>
</div>