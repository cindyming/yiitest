<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Cash;
use app\models\Investment;
use app\models\Log;
use app\models\Revenue;
use app\models\User;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\debug\panels\ProfilingPanel;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InvestmentController extends Controller
{

    public function listParentsAddInvestment($user, &$parents )
    {
        /**
         * 在这里不计算级别和总投资的原因是因为不合适
         */
        $parent = $user->getParennt()->one();
        if ($parent && $parent->role_id != 1) {
            if (!in_array($parent->id, $parents)) {
                $parents[] = $parent->id;
                $this->listParentsAddInvestment($parent, $parents);
            }
        }
    }

    public function actionIndex()
    {
        $userQuery = User::find()->where(['in','role_id', array(3)])->orderBy(array('id' => SORT_ASC));

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 300,
            ],
        ]);

        $connection  = \Yii::$app->db;

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
            $sql = '';

            foreach ($users as $user) {
                $parents = array($user->id);
                $this->listParentsAddInvestment($user, $parents);
                $sql = "UPDATE user set achievements=investment+" . $user->investment . ' WHERE id in (' . implode(',', $parents)  . ');';
                $command = $connection->createCommand($sql);
                $res     = $command->execute($sql);
            }



        }

    }
}
