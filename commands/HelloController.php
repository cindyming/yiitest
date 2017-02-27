<?php

namespace app\commands;

use app\models\Cash;
use app\models\Investment;
use app\models\Log;
use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;


class HelloController extends Controller
{
	public function loadDiamondMembers()
	{
		$diamonds = User::find()->where(['=','role_id', 3])->andWhere(['=', 'level',10]);
		return $diamonds->all();
	}

	public function actionIndex($message = 'hello world')
	{
		//$diamonds = $this->loadDiamondMembers();
		$users = User::find()->where(['=','role_id', 4])->andWhere(['=','merited', 1])->orderBy([ 'created_at' => SORT_ASC])->all();

		foreach ($users as $user) {

			$merits = Revenue::find()->where(['=','type', 1])->andWhere(['like','note', '钻石总监绩效 - 新会员 - ' . $user->id])->all();
			$note = '错误报单,撤销会员[' . $user->id . '%';
			echo $user->id . ';MERIT:' . $user->merited . ';绩效:' . count($merits) . PHP_EOL;

			foreach ($merits as $merit) {

				$cash = Cash::find()->where(['=','user_id', $merit->user_id])->andWhere(['like','note', $note])->all();
				echo $merit->id  . ';COUNT;' . count($cash). PHP_EOL;
				if (!count($cash)) {
					$merit_amount = $merit->merit;
					$user = User::findById($merit->user_id);
					if($merit_amount) {
						$merit_amount = round($merit_amount, 2);
						$merit_remain = round($merit_amount * 0.9);

						$user->mall_remain -= ($merit_amount - $merit_remain);
						$user->mall_total -= ($merit_amount - $merit_remain);
						$user->merit_total -= $merit_amount;
						$user->merit_remain -= $merit_remain;

						$meritData = array(
							'user_id' => $merit->user_id,
							'note' => '错误报单,撤销新会员[' .$this->id . '],绩效扣除: ' . $merit->id,
							'amount' => $merit_remain,
							'type' => 5,
							'status' => 2,
							'total' => $user->merit_remain
						);

						$merit = new Cash();
						$merit->load($meritData, '');

						$mallData = array(
							'user_id' => $merit->user_id,
							'note' => '错误报单,撤销会员[' .$this->id . '],商城币扣除:' . $merit->id,
							'amount' => ($merit_amount - $merit_remain),
							'type' => 7,
							'status' => 2,
							'total' => $user->mall_remain
						);
						$mall = new Cash();
						$mall->load($mallData, '');
						$user->setScenario('cancel');
						if(!$user->save(true, array('mall_remain', 'mall_total','merit_total', 'merit_remain')) || !$merit->save() || !$mall->save()) {
							throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($merit->getErrors()). User::arrayToString($mall->getErrors()));
							break;
						}
					}
				}
			}
		}
	}
}