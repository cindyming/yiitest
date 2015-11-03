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

        foreach ($users->models as $user) {
            $parents = array();
                // $this->calculateMerit($user, $user->investment, $user->investment, '新增新会员:' . $user->id);

                /** to do
                 * 1. list all the parent user for this user
                 * 2. calculate the merit and achivements
                 * */
            $this->listParentsAddMerit($user, $parents, $user->investment);
            if (count($parents)) {
                $note = '新增新会员:' . $user->id;
                $newInvertment = $user->investment;
                $connection=Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();
                    $lastMeritRate = 0;
                    foreach ($parents as $level => $pars) {
                        if (count($pars) && ($level > 2)) {
                            $firstParent = array_shift($pars);
                            $meritRate = $firstParent->getMeritRate();

                            $data = array(
                                'user_id' => $firstParent->id,
                                'note' => $note,
                                'merit' => $newInvertment * ($meritRate - $lastMeritRate)
                            );

                            $merit = new Revenue();
                            $merit->load($data, '');
                            $merit->save();
                            $firstParent->save();

                            $total = count($pars);
                            foreach ($pars as $per) {
                                $data = array(
                                    'user_id' => $per->id,
                                    'note' => $note
                                );
                                $data['merit'] = $newInvertment * 0.05 / $total;
                                $merit = new Revenue();
                                $merit->load($data, '');
                                $merit->save();
                                $per->save();
                            }

                            $lastMeritRate = $meritRate;
                        } else {
                            if (count($per)){
                                foreach ($pars as $per) {
                                    $per->save();
                                }
                            }
                        }
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                }
            }
        }
    }
    public function listParentsAddMerit($user, &$parents, $merit)
    {
        $parent = $user->getParennt()->one();
        if ($parent->role_id != 1) {
            if ($parent->merited == 1) {
                $nextMerit = $merit;
            } else {
                $nextMerit = $merit + $user->achievements;
            }
            $parent->achievements += $merit;
            $parent->level = $parent->calculateLevel();
            $parent->merited = 1;
            if (isset($parents[$parent->level])) {
                $parents[$parent->level] = array($parent);
            } else {
                $parents[$parent->level][] = $parent;
            }
            $this->listParentsAddMerit($parent, $parents, $nextMerit);
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