<?php

namespace app\commands;

use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class BonusController extends Controller
{
    private $_diff = 200000;

    public function actionIndex()
    {
        $query = User::find()->where(['=','role_id', 3])->andWhere(['=', 'stop_bonus', 0]);

        $users = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($users->models as $user) {
            $data = array(
                'user_id' => $user->id,
            );
            if ($user->investment >= $this->_diff) {
                $data['bonus'] =  $user->investment * 0.02;
            } else {
                $data['bonus'] =  $user->investment * 0.03;
            }
            $data['note'] = '分红结算: ' .  date('Y-m-d', time());
            $bonus = new Revenue();
            $bonus->load($data, '');
            $bonus->save();
        }
    }
}
