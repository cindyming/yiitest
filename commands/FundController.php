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


class FundController extends Controller
{
	public function loadAddtionalInvestment($user_id)
	{
		$invertMents = Investment::find()->where(['=', 'user_id', $user_id])->andWhere(['=', 'status', 1])->orderBy(array('id' => SORT_ASC))->all();

		return  $invertMents;
	}

	public function fundTransfer($user, $cash, $id, $created_at, $note, $model = null)
	{
		if (isset($cash) && $cash && $cash->amount) {
			try {
				$service_url = Yii::$app->params['cuohe_url'] . 'api/user/fund';
				$curl = curl_init($service_url);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $user->access_token); //Your credentials goes here
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, (
					array(
						'fund' => $cash->amount,
						'token' => $user->access_token,
						'origin_id' => $id,
						'created_at' => $created_at,
						'note' => $note,
					)));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate


				$curl_response = curl_exec($curl);
				$response = (array)json_decode($curl_response);
				curl_close($curl);

				Log::add('会员(' . $user->id . ')', '基金兑换', '返回', $service_url . $curl_response);
				if (is_array($response) && isset($response['code']) && ($response['code'] == 200)) {
					$cash->note .= '基金兑换成功, id:' . $response['data'];
					$cash->save(false);
					$user->save(false);
					if ($model) {
						$model->save(false);
					}
				}

			} catch (Exception $e) {var_dump($e->getMessage());
				Log::add('会员(' . $user->id . ')', '基金兑换', '失败', $e->getMessage());
			}
		}
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
				if (!$user->redeemed) {
					$user->redeemed = 5;
					$user->investment -= $user->init_investment;
					$cash = new Cash();
					$cash->user_id = $user->id;
					$cash->cash_type = 8;
					$cash->type = 12;
					$cash->amount = $user->init_investment;
					$cash->total = $user->investment;
					$cash->status = 2;
					$created_at = $user->approved_at;
					$note = 1;
					$this->fundTransfer($user, $cash, $user->id, $created_at, $note);
				}

				$addtions = $this->loadAddtionalInvestment($user->id);
				foreach ($addtions as $model) {
					if ($model->status == 1) {
						$model->status = 5;
						$user->investment -= $model->amount;
						$cash = new Cash();
						$cash->cash_type = 8;
						$cash->type = 12;
						$cash->user_id = $user->id;
						$cash->amount = $model->amount;
						$cash->total = $user->investment;
						$cash->status = 2;
						$created_at = $model->created_at;
						$note = 2;
						$this->fundTransfer($user, $cash, $model->id, $created_at, $note, $model);
					}

				}

			}
		}

	}
}