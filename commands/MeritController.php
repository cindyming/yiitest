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
        $query->orderBy([ 'id' => SORT_DESC, ]);

        $users = new ActiveDataProvider([
            'query' => $query
        ]);

        foreach ($users->models as $user) {
            $this->calculateMerit($user, $user->investment);
        }
    }

    public function calculateMerit($user, $total)
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
                    'user_id' => $parent->id
                );
                if ($parent->level == $user->level) {
                    $data['merit'] = $total * 0.01;
                } else {
                    $data['merit'] = $total * $parent->getMeritRate();
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
                $this->calculateMerit($parent, $nextTotal);
            } catch (Exception $e) {
                $transaction->rollback();//回滚函数
            }
        }
    }
}