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
		$invertMents = Investment::find()->where(['=', 'user_id', $user_id])->andWhere(['=', 'redeemed', 0])->orderBy(array('id' => SORT_ASC))->all();

		return  $invertMents;
	}

	public function fundTransfer($user, $cash, $id, $created_at, $note)
	{
		if (isset($cash) && $cash && $cash->amount) {
			try {
				$service_url = Yii::$app->params['cuohe_url'] . 'api/user/fund';
				$curl = curl_init($service_url);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $user->access_token); //Your credentials goes here
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(
					array(
						'fund' => $cash->amount,
						'token' => $user->access_token,
						'id' => $id,
						'created_at' => $created_at,
						'note' => $note,
					)));
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate


				$curl_response = curl_exec($curl);
				$response = (array)json_decode($curl_response);
				curl_close($curl);

				Log::add('会员(' . $user->id . ')', '基金兑换', '返回', $service_url . $curl_response);
				if (is_array($response) && isset($response['code']) && ($response['code'] == 200)) {
					$pass = true;
					$cash->note .= '基金兑换成功, id:' . $response['data'];
					$cash->save(false);
					$user->save(false);
					Yii::$app->getSession()->set('message', '基金兑换成功');
				} else {
					Yii::$app->getSession()->set('danger', '基金兑换失败,请稍候再试');
				}

			} catch (Exception $e) {
				Log::add('会员(' . $user->id . ')', '基金兑换', '失败', $curl_response);
				Yii::$app->getSession()->set('danger', '基金兑换失败,请稍候再试' . $e->getMessage());
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
					$cash->cash_type = 8;
					$cash->type = 12;
					$cash->amount = $user->init_investment;
					$cash->total = $user->investment;
					$cash->status = 2;
					$created_at = $user->approved_at;
					$note = '初始投资转换';
					$this->fundTransfer($user, $cash, $cash->id, $created_at, $note);
				}

				$addtions = $this->loadAddtionalInvestment($user->id);
				foreach ($addtions as $model) {
					$model->status = 5;
					$user = User::findOne(Yii::$app->user->id);
					$user->investment -= $model->amout;
					$cash = new Cash();
					$cash->cash_type = 8;
					$cash->type = 12;
					$cash->amount = $model->amout;
					$cash->total = $user->investment;
					$cash->status = 2;
					$created_at = $model->created_at;
					$note = '追加投资投资转换, 追加时间:' .  $created_at;
					$this->fundTransfer($user, $cash, $cash->id, $created_at, $note);
				}

			}
		}

	}
}