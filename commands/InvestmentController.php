<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Cash;
use app\models\Investment;
use app\models\Log;
use app\models\Revenue;
use app\models\User;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\debug\panels\ProfilingPanel;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InvestmentController extends Controller
{
	public function lessThan15Investment($id)
	{
		$invertMents = Investment::find()->andWhere(['=', 'user_id', $id])->andWhere(['=', 'status', 1])->andWhere(['=', 'merited', 1])->all();
		$inverts = 0;
		foreach ($invertMents as $iv) {
			$inverts += $iv->amount;

		}
		return $inverts;
	}

	public function actionIndex()
	{
		$userQuery = User::find()->where(['in','role_id', array(2,3)]);

		$provider = new ActiveDataProvider([
			'query' => $userQuery,
			'pagination' => [
				'pageSize' => 100,
			],
		]);
		$provider->prepare();
		for($i=1; $i<=$provider->getPagination()->getPageCount();$i++) {
			if ($i != 1) {
				$provider = new ActiveDataProvider([
					'query' => $userQuery,
					'pagination' => [
						'pageSize' => 1000,
						'page' => $i - 1,
					],
				]);
			}
			$users = $provider->getModels();

			foreach ($users as $user) {
				$investment = $this->lessThan15Investment($user->id);
				$allInverstment = $user->investment;
				if ($allInverstment - $investment) {
					$user->init_investment = $allInverstment - $investment;
					$user->save(true, array('init_investment'));
				}

			}
		}

	}
}