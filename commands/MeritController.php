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
            $parents = array();
            $lowLevelParents = array();

            $this->listParentsAddMerit($user, $parents, $lowLevelParents);

            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();

                $addtionalInvest->merited = 1;
                $addtionalInvest->save();
                $newInvestment = $addtionalInvest->amount;
                $user->investment += $newInvestment;
                if ($user->stop_bonus) {
                    if (($user->bonus_total + $user->merit_total) < ($user->investment * 2 )) {
                        $user->stop_bonus = 0;
                    }
                }

                $this->addMeritForMember($user, $newInvestment);

                $note = '追加投资 - ' . $addtionalInvest->id . ' - 会员(' . $user->id . ')';
                $this->dealWithParentMembers($parents ,$newInvestment, $note);

                $this->dealWithLowLevelMembers($lowLevelParents, $newInvestment);


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
            $parents = array();
            $lowLevelParents = array();

            $this->listParentsAddMerit($user, $parents, $lowLevelParents);

            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();

                $user->merited = 1;
                $amount =  $user->investment;

                $this->addMeritForMember($user);

                $note = '新会员绩效 -  ' . $user->id;
                $this->dealWithParentMembers($parents, $amount, $note);

                $this->dealWithLowLevelMembers($lowLevelParents, $amount);

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

    public function dealWithLowLevelMembers($parents,$amount)
    {
        if (count($parents)) {
            foreach ($parents as $per) {
                var_dump ('low level  parents: ' . $per->id);
                $this->addMeritForMember($per, $amount);
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
                    foreach ($pars as $per) {
                        var_dump ('slibing parents: ' . $per->id);
                        $this->excludeDiamondMembers[] = $per->id;
                        $this->addMeritForMember($per, $newInvestment);
                    }
                } else {
                    $firstParent = array_shift($pars);
                    $meritRate = $firstParent->getMeritRate($level);
                    $merit_amount = $newInvestment * ($meritRate - $lastMeritRate);
                    $this->addMeritForMember($firstParent, $newInvestment, $merit_amount, $note);
                    var_dump ('first parent: ' . $firstParent->id . 'level:' .$firstParent->level);

                    $total = count($pars);
                    foreach ($pars as $per) {
                        var_dump ('slibing parents: ' . $per->id);
                        $this->addMeritForMember($per, $newInvestment, round($newInvestment * 0.02 / $total, 2), '加权平均绩效:' . $note);
                    }
                    $lastMeritRate = $meritRate;
                }
            }
        }
    }

    public function addMeritForMember($user, $newInvestment = 0, $merit_amount = 0, $note = '')
    {
        if ($newInvestment) {
            $user->achievements += $newInvestment;
        }

        $calLevel = $user->calculateLevel(); var_dump('calculate level : ' . $calLevel . ', true level: ' . $user->level);
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


    public function listParentsAddMerit($user, &$parents, &$lowLevelParents, $lastLevel = 0)
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        $parent = $user->getSuggest()->one();
        if ($parent && $parent->role_id != 1) {
             $level = $parent->level;

             if ($level < $lastLevel) {
                 $lowLevelParents[] = $parent;
             } else {
                if (!isset($parents[$level])) {
                    $parents[$level] = array();
                }
                $parents[$level][] = $parent;
                $lastLevel = $level;
             }

            $this->listParentsAddMerit($parent, $parents,$lowLevelParents, $lastLevel);
        }
    }
}