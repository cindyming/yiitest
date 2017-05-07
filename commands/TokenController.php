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


class TokenController extends Controller
{

	public function actionIndex($message = 'hello world')
	{
		$sql = "UPDATE user SET access_token=md5(concat('hainan', `id`, '" .date('YmD'). "'))";
		$command = Yii::$app->db->createCommand($sql);

		return $command->execute();

	}
}