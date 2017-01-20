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
    private $_diffTime = '2016-06-05';

    public function lessThan15Investment($id, $lessThanStart = true)
    {
        $startTime = $this->_startTime;
        if ($lessThanStart) {
            $startTime = $this->_diffTime . ' 00:00:00';
        }
        $invertMents = Investment::find()->where(['>', 'created_at', $startTime])->andWhere(['=', 'user_id', $id])->andWhere(['=', 'status', 1])->andWhere(['=', 'merited', 1])->orderBy(['created_at' => SORT_DESC])->all();
        $inverts = array();
        foreach ($invertMents as $iv) {
            $id = $iv->id;
            $amount = $iv->amount;
            $inverts[$id] = array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at, 'merited' => $iv->merited);
        }

        return $inverts;
    }

    public function addBonus($total, $inverstiment, $days, $date)
    {
        $rate = 1;

        if ($days < 30) {
            $rate = $days / 30;
        }

        if ($date >= $this->_diffTime) {
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

        $amount = $amount * $rate;
        return $amount;
    }

    public function actionIndex()
    {
        $this->_startTime = date("Y-m-d",strtotime("-30 days")) . ' 00:00:00';

        $userQuery = User::find()->where(['=','role_id', 3])->andwhere(['=','locked', 0])->andWhere(['=', 'id', 1000191115]);

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 1000,
            ],
        ]);
        $provider->prepare();
        $j = 0;var_dump($provider->getTotalCount());

        for($i=1; $i<=$provider->getPagination()->getPageCount();$i++) {
            if($i != 1) {
                $provider = new ActiveDataProvider([
                    'query' => $userQuery,
                    'pagination' => [
                        'pageSize' => 1000,
                        'page' => $i-1,
                    ],
                ]);
            }
            $users = $provider->getModels();

            foreach ($users as $user) {
                if ($user->stop_bonus) {
                    continue;
                }
                if (($user->bonus_total + $user->merit_total) > ($user->investment * 2)) {
                    $user->stop_bonus = 1;
                    $user->save();
                    continue;
                }
                var_dump('分红开始:' . $user->id);

                $total = $user->investment;
                $bonusTotal = 0;

                $lastDate = (int)(strtotime(date('Y-m-d', time())));
                $lessInvestment = $this->lessThan15Investment($user->id, $user->approved_at < $this->_diffTime . ' 00:00:00');
                if (count($lessInvestment)) {
                    foreach ($lessInvestment as $key => $item) {
                        var_dump('追加投资:' . json_encode($item));
                        if ((date('Y-m-d', strtotime($item['created_at']) < date('Y-m-d', strtotime($lastDate))))  && (date('Y-m-d', strtotime($item['created_at']))  != date('Y-m-d', strtotime($user->approved_at)))) {
                            $days = ($lastDate - strtotime(date('Y-m-d', strtotime($item['created_at'])))) / 86400;
                            $bonusTotal += $this->addBonus($total, $item['amount'], $days, date('Y-m-d', strtotime($item['created_at'])));
                            $total -= $item['amount'];
                        }
                        var_dump('停止追加投资');
                    }
                }

                if (date('Y-m-d', strtotime($this->_startTime)) < date('Y-m-d', strtotime($user->approved_at))) {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($user->approved_at)))) / 86400;
                } else {
                    $days = 30;
                }
                $bonusTotal += $this->addBonus($total, $total, $days, date('Y-m-d', strtotime($user->approved_at)));

                echo $bonusTotal;
                if ($bonusTotal > 0) {
                    $data['bonus'] = round($bonusTotal, 2);
                    $data['note'] = '分红结算: ' . date('Y-m-d', time());
                    $data['type'] = 1;
                    $data['user_id'] = $user->id;
                    $data['total'] = $user->bonus_remain + $data['bonus'];
                    $user->bonus_total = $user->bonus_total + $data['bonus'];
                    $user->bonus_remain = $user->bonus_remain + $data['bonus'];
                    $bonus = new Revenue();
                    $bonus->load($data, '');
                    $bonus->save();

                    if (($user->bonus_total + $user->merit_total) > ($user->investment * 2)) {
                        $user->stop_bonus = 1;
                    }

                    $user->save();
                } else {
                    var_dump('分红' . $bonusTotal);
                }
            }

        }
    }
}
