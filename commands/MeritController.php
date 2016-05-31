<?php

namespace app\commands;

use app\models\Investment;
use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;


class MeritController extends Controller
{
    public $excludeDiamondMembers = array();
    public function loadDiamondMembers()
    {
        $diamonds = User::find()->where(['=','role_id', 3])->andWhere(['=', 'level',10]);
//        if (count($this->excludeDiamondMembers)) {
//            var_dump('exclude', implode(',', $this->excludeDiamondMembers));
//            $diamonds->andWhere(['not in', 'id', $this->excludeDiamondMembers]);
//        }
        return $diamonds->all();
    }
    public function actionIndex()
    {
        $this->calculateNewMember();
        $this->calculateAddtionalInvestment();
    }

    public function calculateAddtionalInvestment()
    {

        $additionalInvestList = Investment::find()->where(['=','merited', 0])->orderBy([ 'id' => SORT_ASC, ])->all();

        foreach ($additionalInvestList as $addtionalInvest)
        {
            $diamondMembers = $this->loadDiamondMembers();
            $user = User::findOne($addtionalInvest->user_id);

            var_dump ('start calculate addintional investment for user: ' . $user->id);
            $newInvestment = $addtionalInvest->amount;
            $investmentParents = array();
            $this->listParentsAddInvestment($user, $investmentParents);
            $this->dealWithInvestmentMembers($investmentParents, $newInvestment);
            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();

                $addtionalInvest->merited = 1;
                $addtionalInvest->save();

                $user->investment += $newInvestment;
                if ($user->stop_bonus) {
                    if (($user->bonus_total + $user->merit_total) < ($user->investment * 2 )) {
                        $user->stop_bonus = 0;
                    }
                }

                $parents = array();

                $this->listParentsAddMerit($user, $parents, 0, true);

                $this->addMeritForMember($user, $newInvestment);

                $note = '追加投资 - ' . $addtionalInvest->id . ' - 会员(' . $user->id . ')';
                $this->dealWithParentMembers($parents ,$newInvestment, $note);


                $note = '钻石总监绩效 - '. $note;
                $this->dealWithDiamondMembers($diamondMembers, $newInvestment, $note);

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();//回滚函数
                Yii::$app->log($e->getMessage());
            }
            var_dump ('end for users ');
        }
    }

    public function calculateNewMember()
    {
        $users = User::find()->where(['=','role_id', 3])->andWhere(['=','merited', 0])->orderBy([ 'id' => SORT_ASC, ])->all();

        foreach ($users as $user) {
            $diamondMembers = $this->loadDiamondMembers();
            var_dump ('start calculate for user: ' . $user->id);
            $amount =  $user->investment;

            $investmentParents = array();
            $this->listParentsAddInvestment($user, $investmentParents);
            $this->dealWithInvestmentMembers($investmentParents, $amount);

            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();

                $user->merited = 1;

                $parents = array();

                $this->listParentsAddMerit($user, $parents, 0, true);


                $this->addMeritForMember($user);

                $note = '新会员绩效 -  ' . $user->id;
                $this->dealWithParentMembers($parents, $amount, $note);

                $note = '钻石总监绩效 - 新会员 - ' . $user->id;
                $this->dealWithDiamondMembers($diamondMembers, $amount, $note);

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();//回滚函数
                Yii::$app->log($e->getMessage());
            }
            var_dump ('end for users ');
        }
    }


    public function dealWithDiamondMembers($parents, $amount, $note)
    {
        if (count($parents)) {
            $merit_amount = round($amount * 0.02/count($parents), 2);
            foreach ($parents as $per) {
                var_dump ('diamonds member: ' . $per->id);

                $this->addMeritForMember($per, 0, $merit_amount, $note);
            }
        }
    }


    public function dealWithParentMembers($parents, $newInvestment, $note)
    {
        if (count($parents)) {
            $lastMeritRate = 0;
            foreach ($parents as $level => $pars) {
                var_dump ('level: ' . $level);
                if($level == 10) {
                } else {
                    $firstParent = array_shift($pars);
                    $meritRate = $firstParent->getMeritRate($level);
                    $merit_amount = $newInvestment * ($meritRate - $lastMeritRate);
                    $this->addMeritForMember($firstParent, 0, $merit_amount, $note);

                    $total = count($pars);
                    foreach ($pars as $per) {
                        $this->addMeritForMember($per, 0, round($newInvestment * 0.02 / $total, 2), '加权平均绩效:' . $note);
                    }
                    $lastMeritRate = $meritRate;
                }
            }
        }
    }


    public function dealWithInvestmentMembers($parents, $newInvestment)
    {
        if (count($parents)) {
            foreach ($parents as $level => $per) {
                $this->addMeritForMember($per, $newInvestment);
            }
        }
    }

    public function addMeritForMember($user, $newInvestment = 0, $merit_amount = 0, $note = '')
    {
        if ($newInvestment) {
            $user->achievements += $newInvestment;
        }

        $calLevel = $user->calculateLevel();
        if (($user->level < $calLevel)) {
            $user->level =  $calLevel;
        }

        if($merit_amount) {
            $merit_amount = round($merit_amount, 2);
            $merit_remain = round($merit_amount * 0.9);
            $data = array(
                'user_id' => $user->id,
                'note' => $note,
                'merit' => $merit_amount,
                'type' => 1,
                'total' => $merit_remain +  $user->merit_remain
            );
            $user->mall_remain += ($merit_amount - $merit_remain);
            $user->mall_total += ($merit_amount - $merit_remain);
            $user->merit_total += $merit_amount;
            $user->merit_remain += $merit_remain;

            $merit = new Revenue();
            $merit->load($data, '');
            $merit->save();
        }
        $user->save();
    }


    public function listParentsAddMerit($user, &$parents, $lastLevel = 0, $isFirst=false)
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        if ($isFirst) {
            $parent = $user->getSuggest()->one();
        } else {
            $parent = $user->getParennt()->one();
        }

        if ($parent && $parent->role_id != 1) {
            $level = $parent->level;

            if ($level >= $lastLevel) {
                if (!isset($parents[$level])) {
                    $parents[$level] = array();
                }
                $parents[$level][] = $parent;
                $lastLevel = $level;
            }
            $this->listParentsAddMerit($parent, $parents, $lastLevel);
        }
    }

    public function listParentsAddInvestment($user, &$parents )
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        $parent = $user->getParennt()->one();
        if ($parent && $parent->role_id != 1) {
            $parents[] = $parent;

            $this->listParentsAddInvestment($parent, $parents);
        }
    }
}