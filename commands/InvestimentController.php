<?php

namespace app\commands;

use app\models\Investment;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class InvestimentController extends Controller
{


    public function actionIndex()
    {;

        $userQuery = User::find()->where(['=','role_id', 3])->orderBy(array('id' => SORT_DESC));

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 1000,
            ],
        ]);
        $provider->prepare();
        $j = 0;

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

                $investiment = Investment::find()->where(['=', 'status', 1])->andWhere(['=', 'user_id', $user->id])->all();
                $revenue = Revenue::find()->where(['=','note', "会员：" .$user->id. "的报单奖励"])->one();

                $addtion = 0;

                foreach ($investiment as $in) {
                    $addtion += $in->amount;
                }

                $init = 0;
                if ($revenue) {
                    $init =  $revenue->baodan ? $revenue->baodan : $revenue->bonus;
                    $init = $init * 100;
                }

                if ($init && $user->investment != ($init + $addtion)) {
                    echo PHP_EOL . 'OLD TOTAL' . $user->investment . PHP_EOL;
                    $user->investment = $init + $addtion;
                    $user->save();
                    echo PHP_EOL . 'NEW TOTAL' . $user->investment . PHP_EOL;
                    echo PHP_EOL . $user->id . ' Total:' .  $user->investment . ' init: ' . $init . ' add: ' . $addtion . PHP_EOL;
                }
            }
        }
        var_dump($j);

    }
}
