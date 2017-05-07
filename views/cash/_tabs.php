<?php

use \yii\bootstrap\Html;
?>

<ul class="tabswitch">
	<?php if(\app\models\System::loadConfig('open_cash')):?>
		<?php  if ($type == 'create'): ?>
			<li class="active">银行卡提现</li>
		<?php else: ?>
			<li><?= HTML::a('银行卡提现', ['/cash/create'])?></li>
		<?php endif ?>
	<?php endif ?>

	<?php  if ($type == 'transfer'): ?>
		<li class="active">提现至股票</li>
	<?php else: ?>
		<li><?= HTML::a('提现至股票', ['/cash/create', 'type' => 'transfer'])?></li>
	<?php endif ?>

	<?php  if ($type == 'baodan'): ?>
		<li class="active">提现至报单员</li>
	<?php else: ?>
		<li><?= HTML::a('提现至报单员', ['/cash/create', 'type' => 'baodan'])?></li>
	<?php endif ?>

	<?php if(\app\models\System::loadConfig('open_mall_transfer')):?>
		<?php  if ($type == 'mallmoney'): ?>
			<li class="active">提现至商城</li>
		<?php else: ?>
			<li><?= HTML::a('提现至商城', ['/cash/create', 'type' => 'mallmoney'])?></li>
		<?php endif ?>
	<?php endif ?>

	<?php if(\app\models\System::loadConfig('open_cuohe_transfer')):?>
		<?php  if ($type == 'cuohe'): ?>
			<li class="active">提现至撮合</li>
		<?php else: ?>
			<li><?= HTML::a('提现至撮合', ['/cash/create', 'type' => 'cuohe'])?></li>
		<?php endif ?>
	<?php endif ?>
</ul>
