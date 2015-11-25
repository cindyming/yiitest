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
        $invertMents = Investment::findAll(['>', 'created_at', $this->_startTime]);
        $inverts = array();
        foreach ($invertMents as $iv) {
            $user_id = $iv->user_id;
            $id = $iv->id;
            $amount = $iv->amount;
            if (isset($inverts[$user_id])) {
                $inverts[$user_id][$id] = array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at);
            } else {
                $inverts[$user_id] = array( $id => array('id' => $id, 'amount' => $amount, 'created_at' => $iv->created_at));
            }
        }
        $this->_lessInvestiments = $inverts;
    }

    public function addBonus(&$user, $created_at, $amount)
    {
        $rate = 1;
        if ($created_at > $this->_startTime) {
            $days = (int)(strtotime(date('Y-m-d', strtotime($created_at))) - strtotime(date('Y-m-d', strtotime($this->_startTime)))) / 86400;
            $rate = (15 - $days) / 15;
        }
        if ($user->investment >= $this->_diff) {
            $data['bonus'] =  $amount * 0.03 * $rate;
        } else {
            $data['bonus'] =  $amount * 0.02 * $rate;
        }
        $data['bonus'] = round($data['bonus'], 2);
        $data['total'] = $user->bonus_remain + $data['bonus'];
        $data['note'] = '分红结算: ' .  date('Y-m-d', time());
        $data['type'] = 1;
        $data['user_id'] = $user->id;
        $user->bonus_total = $user->bonus_total + $data['bonus'];
        $user->bonus_remain = $user->bonus_remain + $data['bonus'];
        $bonus = new Revenue();
        $bonus->load($data, '');
        $bonus->save();
    }

    public function actionIndex()
    {
        $this->_startTime = date("Y-m-d",strtotime("-14 days")) . ' 00:00:00';
        $this->lessThan15Investment();

        $users = User::find()->where(['=','role_id', 3])->andWhere(['=', 'stop_bonus', 0])->all();

        foreach ($users as $user) {
            $data = array(
                'user_id' => $user->id,
            );

            if (($user->bonus_total + $user->merit_total) > ($user->investment * 2 )) {
                $user->stop_bonus = 1;
                $user->save();
                continue;
            }
            $total = $user->investment;
            if (isset($this->_lessInvestiments[$user->id])) {
                 foreach ($this->_lessInvestiments[$user->id] as $item){
                     $this->addBonus($user, $item['created_at'], $item['amount']);
                     $total -= $item['amount'];
                 }
            }
            $this->addBonus($user, $user->created_at, $total);

            if (($user->bonus_total + $user->merit_total) > ($user->investment * 2 )) {
                $user->stop_bonus = 1;
            }

            $user->save();
        }
    }
}
