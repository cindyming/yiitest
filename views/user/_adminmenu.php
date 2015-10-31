<?php
use yii\helpers\Html;

$this->params['breadcrumbs'][0] = '会员管理';
?>
<div>
    <ul>
        <li>
            <?= HTML::a('注册会员', yii\helpers\Url::to('/user/admincreate')) ?>
        </li>
        <li>
            <?= HTML::a('审核会员', yii\helpers\Url::to(['/user/adminindexunapprove'])) ?>
        </li>
        <li>
            <?= HTML::a('正式会员', yii\helpers\Url::to(['/user/adminindexapprove'])) ?>
        </li>
        <li>
            <?= HTML::a('全部会员', yii\helpers\Url::to('/user/adminindex')) ?>
        </li>
        <li>
            <?= HTML::a('推荐图', yii\helpers\Url::to('/user/digram')) ?>
        </li>
    </ul>
</div>