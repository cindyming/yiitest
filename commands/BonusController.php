<?php

namespace app\commands;

use app\models\Investment;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class BonusController extends Controller
{
    private $_diff = 200000;
    private $_startTime;
    private $_lessInvestiments;

    public function lessThan15Investment($user_id)
    {

        $invertMents = Investment::find()->where(['>', 'created_at', $this->_startTime])->andWhere(['=', 'status', 1])->andWhere(['=', 'user_id', $user_id])->andWhere(['=', 'merited', 1])->orderBy(['created_at' => SORT_DESC])->all();
        $inverts = array();
        foreach ($invertMents as $iv) {
            $id = $iv->id;
            $amount = $iv->amount;
            $inverts[$id] = array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at, 'merited' => $iv->merited);
        }

        return $inverts;
    }

    public function addBonus($inverstiment, $totalInvestment, $days)
    {
        $rate = 1;
        if ($days < 30) {
            $rate = $days / 30;
        }

        if ($totalInvestment >= $this->_diff) {
            $amount =  $inverstiment * 0.03 * $rate;
        } else {
            $amount =  $inverstiment * 0.02 * $rate;
        }
        return $amount;
    }

    public function actionIndex()
    {
        echo date('Y-m-d H:i:s', time()) . PHP_EOL;
        $this->_startTime = date("Y-m-d",strtotime("-30 days")) . ' 00:00:00';

        $userQuery = User::find()->where(['=','role_id', 3])->andWhere(['!=','locked', 1])->andWhere(['>','investment', 0]);

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);
        $provider->prepare();
        $j = 0;var_dump($provider->getTotalCount());

        for($i=1; $i<=$provider->getPagination()->getPageCount();$i++) {
            var_dump($i);
            if($i != 1) {
                $provider = new ActiveDataProvider([
                    'query' => $userQuery,
                    'pagination' => [
                        'pageSize' => 500,
                        'page' => $i-1,
                    ],
                ]);
            }
            $users = $provider->getModels();
            foreach ($users as $user) {
                $j++;
                if ($user->stop_bonus) {
                    continue;
                }
                if (($user->bonus_total + $user->merit_total) > ($user->investment * 2 )) {
                    $user->stop_bonus = 1;
                    $user->save();
                    continue;
                }

                $total = $user->total_investment;
                $investTotal =$user->investment;
                $bonusTotal = 0;
                $lastDate = (int)(strtotime(date('Y-m-d', time())));
                $invers = $this->lessThan15Investment($user->id);
                if (is_array($invers)  && count($invers)) {
                    foreach ($invers as $item){
                        if (date('Y-m-d', strtotime($item['created_at']) < date('Y-m-d', strtotime($lastDate)))) {
                            $days = ($lastDate - strtotime(date('Y-m-d', strtotime($item['created_at'])))) / 86400;
                            $bonusTotal += $this->addBonus($investTotal, $total, $days);
                            $total -= $item['amount'];
                            $investTotal -= $item['amount'];
                            $lastDate =strtotime(date('Y-m-d', strtotime($item['created_at'])));
                        }
                    }
                }
                if (date('Y-m-d', strtotime($this->_startTime)) > date('Y-m-d', strtotime($user->approved_at))) {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($this->_startTime))))  / 86400;
                } else {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($user->approved_at)))) / 86400;
                }

                $bonusTotal += $this->addBonus($investTotal, $total, $days);

                if ($bonusTotal > 0 ) {
                    $data['bonus'] = round($bonusTotal, 2);
                    $data['note'] = '分红结算: ' .  date('Y-m-d', time());
                    $data['type'] = 1;
                    $data['user_id'] = $user->id;
                    $data['total'] = $user->bonus_remain + $data['bonus'];
                    $user->bonus_total = $user->bonus_total + $data['bonus'];
                    $user->bonus_remain = $user->bonus_remain + $data['bonus'];
                    $bonus = new Revenue();
                    $bonus->load($data, '');
                    $bonus->save();

                    if (($user->bonus_total + $user->merit_total) > ($user->investment * 2 )) {
                        $user->stop_bonus = 1;
                    }

                    $user->save();
                }

            }
        }
        var_dump($j);
        echo date('Y-m-d H:i:s', time()) . PHP_EOL;

    }
}
