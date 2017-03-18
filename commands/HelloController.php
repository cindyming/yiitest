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
class HelloController extends Controller
{
	private $_currentDate = 0;
	private $_startTime;
	private $_oneYear;
	private $_diffTime = '2016-06-05';
	/**
	 * This command echoes what you have entered as the message.
	 * @param string $message the message to be echoed.
	 */
	public function actionIndex($message = 'hello world')
	{
//		$ids = "1000165,1002143,1002218,1002253,1002269,1002315,1002561,1002585,1002594,1002841,1002855,1003426,1003589,1003744,1003831,1004002,1004065,1004087,1004241,1004255,1004353,1004388,1004406,1004473,1004484,1004536,1004825,10019615,10021715,10022014,10023214,10023512,10023620,10024713,10026216,10026615,10026713,10027119,10029720,10030011,10030517,10031020,10031215,10033013,10033217,10033619,10034714,10035111,10035213,10036416,10036719,10037216,10038012,10038216,10038418,10039317,10040912,10041218,10042018,10043120,10043313,10044410,10046112,100015579,100015995,100016168,100016442,100016474,100016663,100016752,100017038,100017348,100017359,100017733,100017751,100017769,100017852,100018037,100018212,100018226,100018247,100018657,100018667,100018709,100018766,100018773,100018887,100018954,100018987,100018997,100019008,100019168,100019177,100019212,100019392,100019539,1000151420,1000153212,1000155520,1000157211,1000158714,1000160219,1000161219,1000161314,1000162810,1000164320,1000164911,1000165414,1000165910,1000166715";

		 // $ids = "1000166715";
		$users = User::find()->where(['=','role_id', 3])->andWhere(['<','approved_at', '2016-06-05 00:00:00'])->all();
		$dates = array(
            '2016-07-05',
            '2016-07-20',
            '2016-08-05',
            '2016-08-20',
            '2016-09-05',
            '2016-09-20',
            '2016-10-05',
            '2016-10-20',
            '2016-11-05',
            '2016-11-20',
            '2016-12-20',
			'2017-01-20',
			'2017-02-20',
		);
		$filename = 'kunming.csv';
		$fp = fopen($filename, 'w');
		fputcsv($fp, array('用户编号', '实际分红', '现发分红', '备注'));
		$ids = array();
		foreach ($users as $user) {
			$allInvesments = Investment::find()->where(['=', 'user_id', $user->id])->andWhere(['>', 'created_at', '2016-06-15 00:00'])->andWhere(['=', 'status', 1])->andWhere(['=', 'merited', 1])->orderBy(['created_at' => SORT_DESC])->all();

			foreach ($dates as $date) {//var_dump($date);
				$start = 0;
				if  (($user->approved_at > $date . ' 00:00:00')) {
					continue;
				}
				if ($date < '2016-12-20') {
					$start = date('Y-m-d H:i:s', (strtotime($date) - 15 * 86400));
				} else {
					$start = date('Y-m-d H:i:s',(strtotime($date) - 30 * 86400));
				}

				$useOldBonusLogic = (int) (($user->approved_at < $this->_diffTime . ' 00:00:00') && ($user->approved_at >= date('Y-m-d H:i:s',strtotime('-11 months', strtotime($date)))));

				$inversments =  Investment::find()->where(['<', 'created_at', $start])->andWhere(['>', 'created_at', '2016-06-15 00:00'])->andWhere(['=', 'user_id', $user->id])->andWhere(['=', 'status', 1])->andWhere(['=', 'merited', 1])->orderBy(['created_at' => SORT_DESC])->all();

				if (count($inversments)) {
					$this->_currentDate = $date;
					$bonus = Revenue::find()->where(['=', 'user_id', $user->id])->andWhere(['=', 'type', 1])->andWhere(['=', 'note', '分红结算: ' . $date])->all();
					$oldTotal = 0;
					foreach ($bonus as $b) {
						$oldTotal += $b->bonus;
					}

					if ($oldTotal) {
						$mustBe = $this->calculateBouns($user, $date . ' 00:00:00', $allInvesments, $start, $useOldBonusLogic);
						//	var_dump($mustBe .':'. $oldTotal);
						if ($mustBe != $oldTotal) {
							if (!in_array($user->id, $ids)) {
								$ids[] = $user->id;
							}
							fputcsv($fp, array($user->id , $mustBe, $oldTotal, $date . ' 分红'));
	                       $redu = $oldTotal - $mustBe;
//							if ($redu > 0) {
//								$user->bonus_remain = $user->bonus_remain - $redu;
//								$data = array(
//									'user_id' => $user->id,
//									'type' => 4,
//									'amount' => $redu,
//									'real_amount' => $redu,
//									'status' => 2,
//									'total' => $user->bonus_remain,
//									'note' => '分红扣除' . $date .  '多发的金额'
//								);
//
//								$cash = new Cash();
//								$cash->load($data, '');
//								if (!$cash->save()) {
//									echo json_encode($cash->getErrors());
//								}
//							}
						}
					}

				}
			}
		}
echo count($ids);

	}

	public function addBonus($total, $inverstiment, $days, $useOldBonusLogic)
	{
		$rate = 1;
		$days = intval($days);
		if ( $this->_currentDate > '2016-12-00') {
			if ($days < 30) {
				$rate = $days / 30;
			}

			if (!$useOldBonusLogic) {
				if ($total >= 200000) {
					$amount =  $inverstiment * 0.03;
				} else {
					$amount =  $inverstiment * 0.02;
				}
			} else {
				if ($total < 100000) {
					$amount =  $inverstiment * 0.02;
				} else if ($total < 200000) {
					$amount =  $inverstiment * 0.03;
				} else {
					$amount =  $inverstiment * 0.04;
				}
			}
		} else {
			if ($days < 15) {
				$rate = $days / 15;
			}

			if (!$useOldBonusLogic) {
				if ($total >= 200000) {
					$amount =  $inverstiment * 0.015;
				} else {
					$amount =  $inverstiment * 0.01;
				}
			} else {
				if ($total < 100000) {
					$amount =  $inverstiment * 0.01;
				} else if ($total < 200000) {
					$amount =  $inverstiment * 0.015;
				} else {
					$amount =  $inverstiment * 0.02;
				}
			}
		}

       echo $total . ':' .$inverstiment, ":days", $days, ':', $amount, ':', $rate . PHP_EOL;
		$amount = $amount * $rate;
		return $amount;
	}

	public function calculateBouns($user, $date, $allInvesments, $start, $useOldBonusLogic) {

		echo $date . PHP_EOL;
		echo $start . PHP_EOL;
		$total = $user->investment;
		$basie = $user->investment;
		$bonusTotal = 0;
		$afterDiffIn = 0;var_dump($date);
		foreach ($allInvesments as $key => $item) {
			if (((date('Ymd', strtotime($item->created_at)) < date('Ymd', strtotime($date))))) {
				if (((date('Ymd', strtotime($item->created_at))) > date('Ymd', strtotime($start)))) {
					echo $item->amount . ';' . $item->created_at . PHP_EOL;
					$days = (strtotime($date) - strtotime(date('Y-m-d 00:00:00', strtotime($item->created_at)))) / 86400;
					$bonusTotal += $this->addBonus($total, $total, $days, false);
					$date = date('Y-m-d 00:00:00', strtotime($item->created_at));
					$total -= $item->amount;
				} else if(date('Ymd', strtotime($item->created_at)) > '20160605') {
					$afterDiffIn += $item->amount;
				}
			} else {
				$total -= $item->amount;
			}
			$basie -= $item->amount;
		}

		if (date('Y-m-d', strtotime($start)) < date('Y-m-d', strtotime($user->approved_at))) {
			$days = (strtotime($date) - strtotime(date('Y-m-d', strtotime($user->approved_at)))) / 86400;
		} else {
			$days = (strtotime($date) - strtotime(date('Y-m-d', strtotime($start)))) / 86400;
		}

		if ($afterDiffIn) {
			$bonusTotal += $this->addBonus($total, $afterDiffIn, $days, false);
		}

		if ($useOldBonusLogic) {
			if ($basie <= 200000) {
				$oldLevel = floor($user->investment/100000);
				$newLevel = floor($basie/100000);
				if ($newLevel - $oldLevel) {
					$useOldBonusLogic = false;
				}
			}
		}
		$bonusTotal += $this->addBonus($total, $basie, $days, $useOldBonusLogic);

		return round($bonusTotal, 2);
	}

}
