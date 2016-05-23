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

    public function lessThan15Investment()
    {
        $invertMents = Investment::find()->where(['>', 'created_at', $this->_startTime])->orderBy(['created_at' => SORT_DESC])->all();
        $inverts = array();
        foreach ($invertMents as $iv) {
            $user_id = $iv->user_id;
            $id = $iv->id;
            $amount = $iv->amount;
            if (isset($inverts[$user_id])) {
                $inverts[$user_id][$id] = array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at, 'merited' => $iv->merited);
            } else {
                $inverts[$user_id] = array( $id => array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at,'merited' => $iv->merited));
            }
        }
        $this->_lessInvestiments = $inverts;
    }

    public function addBonus($inverstiment, $days)
    {
        $rate = 1;
        if ($days < 15) {
            $rate = $days / 15;
        }

        if ($inverstiment >= $this->_diff) {
            $amount =  $inverstiment * 0.015 * $rate;
        } else {
            $amount =  $inverstiment * 0.01 * $rate;
        }
        return $amount;
    }

    public function actionIndex()
    {
        $this->_startTime = date("Y-m-d",strtotime("-15 days")) . ' 00:00:00';
        $this->lessThan15Investment();

        $userQuery = User::find()->where(['=','role_id', 3])->andWhere(['in', 'id', array(10000518,10001114,10001115,10001116,10001117,10001118, 10001119,10001120,10001121,10001122,10001123,10001124,10001125,10001126,10001127,10001128,10002164,10002165,10002919,10002934)]);

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 1000,
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
                        'pageSize' => 1000,
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

                $total = $user->investment;
                $bonusTotal = 0;
                $lastDate = (int)(strtotime(date('Y-m-d', strtotime('2016-05-20 00:00:01'))));
                if (isset($this->_lessInvestiments[$user->id])) {
                    foreach ($this->_lessInvestiments[$user->id] as $item){
                        if (date('Y-m-d', strtotime($item['created_at']) < date('Y-m-d', strtotime($lastDate)))) {
                            $days = ($lastDate - strtotime(date('Y-m-d', strtotime($item['created_at'])))) / 86400;
                            $bonusTotal += $this->addBonus($total, $days);
                            $total -= $item['amount'];
                            $lastDate =strtotime(date('Y-m-d', strtotime($item['created_at'])));
                        }
                    }
                }
                if (date('Y-m-d', strtotime($this->_startTime)) > date('Y-m-d', strtotime($user->created_at))) {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($this->_startTime))))  / 86400;
                } else {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($user->created_at)))) / 86400;
                }

                $bonusTotal += $this->addBonus($total, $days);

                if ($bonusTotal > 0 ) {
                    $data['bonus'] = round($bonusTotal, 2);
                    $data['note'] = '分红结算: ' .  date('Y-m-d', strtotime('2016-05-20 00:00:01'));
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

    }
}
