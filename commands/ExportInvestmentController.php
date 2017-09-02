<?php

namespace app\commands;

use app\models\Investment;
use Yii;
use yii\console\Controller;
use app\models\User;
use yii\data\ActiveDataProvider;


class ExportInvestmentController extends Controller
{
	public $start = "2015-11-09 00:00:00";
	public $end = "2016-12-18 23:59:60";

	public function loadAddtionalInvestment($user_id)
	{
		$invertMents = Investment::find()->where(['=', 'user_id', $user_id])->orderBy(array('id' => SORT_ASC))->all();

		return  $invertMents;
	}


	public function checkDataRange($time)
	{
		if (($time >= $this->start) && ($time <= $this->end)) {
			return true;
		} else {
			return false;
		}
	}

	public function actionIndex($message = 'hello world')
	{
		$userQuery = User::find()->where(['=','role_id', 3])->andWhere(['=', 'merited', 1])->orderBy(array('id' => 'asc'));

		$provider = new ActiveDataProvider([
			'query' => $userQuery,
			'pagination' => [
				'pageSize' => 300,
			],
		]);
		$file = fopen('investment.csv', 'w');
		fputcsv($file, array('编号', '日期', '投资额', '状态'));
		$provider->prepare();
		$count = $provider->getPagination()->getPageCount();
		for($i=1; $i<=$count;$i++) {
			if ($i != 1) {
				$provider = new ActiveDataProvider([
					'query' => $userQuery,
					'pagination' => [
						'pageSize' => 300,
						'page' => $i - 1,
					],
				]);
			}
			$users = $provider->getModels();

			foreach ($users as $user) {
				$total = 0;
				$id = $user->id;
				if ($this->checkDataRange($user->approved_at)) {
					fputcsv($file, array($id, date('Y-m-d', strtotime($user->approved_at)), $user->init_investment, '', ''));
					$total += $user->init_investment;
				}

				$investments = $this->loadAddtionalInvestment($user->id);
				foreach ($investments as $investment) {
					if ($this->checkDataRange($investment->created_at)) {
						if ($investment->status) {
							$total += $investment->amount;
						}
						fputcsv($file, array($id, date('Y-m-d', strtotime($investment->created_at)), $investment->amount, $investment->status ? '正常' : '撤销'));
					}
				}
				if ($total) {
					fputcsv($file, array($id, '', '', '总投资额', $total));
				}
			}
		}
		fclose($file);
	}
}