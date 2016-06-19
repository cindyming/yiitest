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

    public function lessThan15Investment()
    {
        $invertMents = Investment::find()->where(['>', 'created_at', $this->_startTime])->andWhere(['=', 'status', 1])->andWhere(['=', 'merited', 1])->orderBy(['created_at' => SORT_DESC])->all();
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

    public function addBonus($total, $inverstiment, $days, $date)
    {
        $rate = 1;

        if ($days < 15) {
            $rate = $days / 15;
        }

        if ($date >= $this->_diffTime) {
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

        $amount = $amount * $rate;
        return $amount;
    }

    public function actionIndex()
    {
        $this->_startTime = date("Y-m-d",strtotime("-15 days")) . ' 00:00:00';
        $this->lessThan15Investment();

        $userQuery = User::find()->where(['=','role_id', 3])->andWhere(['=', 'stop_bonus', 0])->andwhere(['=','locked', 0]);

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
                if (($user->bonus_total + $user->merit_total) > ($user->investment * 2)) {
                    $user->stop_bonus = 1;
                    $user->save();
                    continue;
                }
                var_dump('分红开始:' . $user->id);

                $total = $user->investment;
                $bonusTotal = 0;

                if ($user->approved_at < $this->_diffTime . ' 00:00:00') {
                    $newInvestiments = Investment::findAll("user_id=:user_id AND created_at>:created_at AND status=1 AND merited=1 AND created_at>:approved_at ORDER BY created_at ASC", array(':user_id' => $user->id, ':approved_at' => date('Y-m-d', strtotime($user->approved_at)) . ' 23:59:59', ':created_at' => $this->_diffTime . ' 00:00:00'));

                    foreach ($newInvestiments as $in) {
                        $this->_lessInvestiments[$in->user_id][$in->id];

                        $data = array('id' => $in->id, 'amount' => $in->amount, 'created_at' => $in->created_at, 'merited' => $in->merited);

                        if (isset($this->_lessInvestiments[$in->user_id])) {

                            $this->_lessInvestiments[$in->user_id][$in->id] = $data;
                        } else {
                            $this->_lessInvestiments[$in->user_id] = array( $in->id => $data);
                        }
                    }
                }

                $lastDate = (int)(strtotime(date('Y-m-d', time())));
                if (isset($this->_lessInvestiments[$user->id])) {
                    $items = $this->_lessInvestiments[$user->id];
                 //   $items = array_reverse($items);
                    foreach ($items as $key => $item) {
                        var_dump('追加投资:' . json_encode($item));
                        if ((date('Y-m-d', strtotime($item['created_at']) < date('Y-m-d', strtotime($lastDate))))  && (date('Y-m-d', strtotime($item['created_at']))  != date('Y-m-d', strtotime($user->approved_at)))) {
                            $days = ($lastDate - strtotime(date('Y-m-d', strtotime($item['created_at'])))) / 86400;
                            var_dump('金额:' . $item['amount']);
                            var_dump('天数:' . $days);
                            $bonusTotal += $this->addBonus($total, $item['amount'], $days, date('Y-m-d', strtotime($item['created_at'])));
                            var_dump('分红额:' . $bonusTotal);
                            $total -= $item['amount'];
                           // $lastDate = strtotime(date('Y-m-d', strtotime($item['created_at'])));
                        }
                        var_dump('停止追加投资');
                    }
                }


                if (date('Y-m-d', strtotime($this->_startTime)) < date('Y-m-d', strtotime($user->approved_at))) {
                    $days = ($lastDate - strtotime(date('Y-m-d', strtotime($user->approved_at)))) / 86400;
                } else {
                    $days = 15;
                }
                $bonusTotal += $this->addBonus($total, $total, $days, date('Y-m-d', strtotime($user->approved_at)));
                var_dump('金额:' . $total);
                var_dump('天数:' . $days);
                var_dump('分红额:' . $bonusTotal);
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
