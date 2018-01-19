<?php

namespace app\commands;

use app\models\Cash;
use app\models\Investment;
use app\models\Log;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class FixInvestmentController extends Controller
{
	public function loadAddtionalInvestment($user_id)
	{
		$invertMents = Investment::find()->where(['=', 'user_id', $user_id])->andWhere(['=', 'status', 2])->orderBy(array('id' => SORT_ASC))->all();

		return  $invertMents;
	}



	public function actionIndex($message = 'hello world')
	{
		$userQuery = User::find()->where(['=','role_id', 3])->andwhere(['=','locked', 0])->andWhere(['>', 'investment', 0]);

		$provider = new ActiveDataProvider([
			'query' => $userQuery,
			'pagination' => [
				'pageSize' => 300,
			],
		]);
		$provider->prepare();
		$j = 0;

		for($i=1; $i<=$provider->getPagination()->getPageCount();$i++) {
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
				$reduceInvestment = 0;
				if (!$user->redeemed) {
					$reduceInvestment = $user->init_investment;
				}
				$addtions = $this->loadAddtionalInvestment($user->id);
				foreach ($addtions as $model) {
					$reduceInvestment += $model->amount;

				}

				if ($reduceInvestment) {
					$user->investment -= $reduceInvestment;
					if ($user->investment) {
						$user->save(false, array('investment'));
					} else {
						echo "ERROR " . $user->id . '  ' . $user->investment . PHP_EOL;
					}

				}

			}
		}

	}
}