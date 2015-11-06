<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class MeritController extends Controller
{
    public function actionIndex()
    {
        $query = User::find()->where(['=','role_id', 3]);
        $query->andWhere(['=','merited', 0]);
        $query->orderBy([ 'id' => SORT_ASC, ]);

        $users = new ActiveDataProvider([
            'query' => $query
        ]);

        $diamondMembers = User::find()->where(['=','role_id', 3])->andWhere('=', 'level',8);

        foreach ($users->models as $user) {
            $parents = array();
            $noMeritParents = array();
                // $this->calculateMerit($user, $user->investment, $user->investment, '新增新会员:' . $user->id);

                /** to do
                 * 1. list all the parent user for this user
                 * 2. calculate the merit and achivements
                 * */
            $this->listParentsAddMerit($user, $parents, $noMeritParents, $user->investment);

            if (count($parents)) {
                $note = '新增新会员:' . $user->id;
                $newInvertment = $user->investment;
                $connection=Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();
                    $lastMeritRate = 0;
                    foreach ($parents as $level => $pars) {
                        if (count($pars) && ($level > 2)) {
                            $firstParent = $pars[0];
                            unset($pars[0]);
                            $meritRate = $firstParent->getMeritRate($level);
                            $merit_amount = $newInvertment * ($meritRate - $lastMeritRate);

                            $data = array(
                                'user_id' => $firstParent->id,
                                'note' => $note,
                                'merit' => $merit_amount,
                                'total' => $merit_amount +  $firstParent->merit_remain
                            );
                            $firstParent->merit_total +=$merit_amount;
                            $firstParent->merit_remain +=$merit_amount;

                            $merit = new Revenue();
                            $merit->load($data, '');
                            $merit->save();
                            $firstParent->save();

                            $total = count($pars);
                            foreach ($pars as $per) {
                                $merit_amount = round($newInvertment * 0.05 / $total, 2);
                                $data = array(
                                    'user_id' => $per->id,
                                    'note' => $note,
                                    'merit' => $merit_amount,
                                    'total' => $merit_amount +  $per->merit_remain
                                );
                                $merit = new Revenue();
                                $merit->load($data, '');
                                $merit->save();
                                $per->merit_total +=$merit_amount;
                                $per->merit_remain +=$merit_amount;
                                $per->save();
                            }

                            $lastMeritRate = $meritRate;
                        } else {
                            if (count($pars)){
                                foreach ($pars as $per) {
                                    $per->save();
                                }
                            }
                        }
                    }
                    if (count($noMeritParents)) {
                        foreach ($noMeritParents as $per) {
                            $per->save();
                        }
                    }

                    if (count($diamondMembers)) {
                        foreach ($diamondMembers as $per) {
                            $merit_amount = round($newInvertment * 0.02, 2);
                            $data = array(
                                'user_id' => $per->id,
                                'note' => $note,
                                'merit' => $merit_amount,
                                'total' => $merit_amount +  $per->merit_remain
                            );
                            $merit = new Revenue();
                            $merit->load($data, '');
                            $merit->save();
                            $per->merit_total +=$merit_amount;
                            $per->merit_remain +=$merit_amount;
                            $per->save();
                        }
                    }
                    $user->merited = 1;
                    $user->achievements += $user->investment;
                    $user->level = $user->calculateLevel();
                    $user->save();
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                }
            } else {
                $user->merited = 1;
                $user->achievements += $user->investment;
                $user->level = $user->calculateLevel();
                $user->save();
            }
        }
    }


    public function listParentsAddMerit($user, &$parents, &$noMeritParents, $merit, $lastLevel = 0)
    {
        $parent = $user->getParennt()->one();
        if ($parent && $parent->role_id != 1) {

            if ($parent->level < $lastLevel) {
                $noMeritParents[] = $parent;
            } else if ($parent->level < 8) {
                if (!isset($parents[$parent->level])) {
                    $parents[$parent->level] = array();
                }
                $parents[$parent->level][] = $parent;
                $lastLevel = $parent->level;
            }

            $parent->achievements += $merit;
            $parent->level = $parent->calculateLevel();
            $this->listParentsAddMerit($parent, $parents, $noMeritParents, $merit, $lastLevel);
        }
    }

    public function calculateMerit($user, $new, $total, $message)
    {
        $parent = $user->getParennt()->one();
        $nextTotal = 0;
        if ($parent->role_id === 3) {
            if ($parent->merited == 1) {
                $nextTotal = $total;
            } else {
                $nextTotal = $total + $user->achievements;
            }
            $parent->achievements += $total;
            $parent->level = $parent->calculateLevel();
            $data = array();

            if ($parent->level > 2) {
                $data = array(
                    'user_id' => $parent->id,
                    'note' => $message
                );
                if ($parent->level == $user->level) {
                    $data['merit'] = $new * 0.01;
                } else {
                    $data['merit'] = $new * $parent->getMeritRate();
                }
            }
            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();
                if (count($data)) {
                    $bonus = new Revenue();
                    $bonus->load($data, '');
                    $bonus->save();
                }
                $user->merited = 1;
                $parent->save();
                $user->save();
                $transaction->commit();//事物结束
             //   $this->calculateMerit($parent, $nextTotal);
            } catch (Exception $e) {
                $transaction->rollback();//回滚函数
            }
        }
    }
}