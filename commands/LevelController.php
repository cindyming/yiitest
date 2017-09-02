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
class LevelController extends Controller
{
	/**
	 * This command echoes what you have entered as the message.
	 * @param string $message the message to be echoed.
	 */
	public function actionIndex($message = 'hello world')
	{
		$filename = 'hainan.csv';
		$fp = fopen($filename, 'w');
		fputcsv($fp, array('用户编号', '现在等级', '实际应该的等级'));
		$userQuery = User::find()->where(['in','role_id', array(3)])->orderBy(array('id' => SORT_ASC));

		$provider = new ActiveDataProvider([
			'query' => $userQuery,
			'pagination' => [
				'pageSize' => 300,
			],
		]);

		$provider->prepare();
		$count = $provider->getPagination()->getPageCount();
		$user =new User();
		$levels = $user->getLevelOptions();

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
				$calLevel = $user->calculateLevel();
				if ($user->level != $calLevel) {
					fputcsv($fp, array($user->id , $levels[$user->level], $levels[$calLevel]));
				}
			}
		}

	}


}
