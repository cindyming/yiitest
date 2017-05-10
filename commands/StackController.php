<?php

namespace app\commands;

use app\models\Cash;
use app\models\Investment;
use app\models\Log;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use app\models\User;
use app\models\Revenue;
use yii\data\ActiveDataProvider;


class StackController extends Controller
{

    public function loadAddtionalInvestment($user_id)
    {
        $invertMents = Investment::find()->where(['=', 'user_id', $user_id])->orderBy(array('id' => SORT_ASC))->all();

        return  $invertMents;
    }

    public function actionIndex($message = 'hello world')
    {
        $userQuery = User::find()->where(['=','role_id', 3])->andWhere(['=', 'merited', 1]);

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 300,
            ],
        ]);
        $provider->prepare();
        $count = $provider->getPagination()->getPageCount();
        for($i=1; $i<=$count;$i++) {
            if ($i != 1) {
                $provider = new ActiveDataProvider([
                    'query' => $userQuery,
                    'pagination' => [
                        'pageSize' => 300,
                        'page' => $i - 1,
                    ],
                ]);
            }
            $users = $provider->getModels();

            foreach ($users as $user) {
                $connection=Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();
                    $total = 0;
                    $investments = $this->loadAddtionalInvestment($user->id);
                    $submit = true;

                    echo $user->init_investment .':::' . date('Ymd', strtotime($user->approved_at)) . PHP_EOL;
                    $stack = User::investToStack($user->init_investment, date('Ymd', strtotime($user->approved_at)));
                    $total += $stack;
                    $user->init_stack = $stack;

                    $data = array(
                        'user_id' => $user->id,
                        'note' => '初始投资折算配股数',
                        'stack' => $stack,
                        'type' => 10,
                        'total' => $stack
                    );
                    $merit = new Revenue();
                    $merit->load($data, '');
                    if (!$merit->save()) {
                        $submit = false;
                    }

                    if ($submit) {
                        foreach ($investments as $investment) {
                            echo $investment->amount .':::' . date('Ymd', strtotime($investment->created_at)) . PHP_EOL;
                            $stack = User::investToStack($investment->amount, date('Ymd', strtotime($investment->created_at)));
                            if (($investment->status == 1) && ($investment->merited == 1)) {
                                $total += $stack;
                            }

                            $data = array(
                                'user_id' => $user->id,
                                'note' => '追加投资折算配股数',
                                'stack' => $stack,
                                'type' => 10,
                                'total' => $total,
                            );
                            $investment->stack = $stack;
                            $investment->save();
                            $merit = new Revenue();
                            $merit->load($data, '');
                            if (!$merit->save()) {
                                $submit = false;
                                break;
                            }

                        }
                    }

                    if ($submit) {
                        $user->total_stack = $user->total_stack + $total;
                        $user->stack = $user->stack + $total;
                        if (!$user->save()) {
                            $submit = false;
                        }
                    }
                    if ($submit) {
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo "PASS ERROR FOR: " . $user->id . $e->getMessage() . PHP_EOL;
                }
            }
        }
    }
}