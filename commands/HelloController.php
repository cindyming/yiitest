<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Log;
use app\models\User;
use yii\console\Controller;
use yii\data\Sort;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $user = User::findOne(10000923);
        $newInvestment = 100000;

        $investmentParents = array();
        $this->listParentsAddInvestment($user, $investmentParents);
        $this->dealWithInvestmentMembers($investmentParents, $newInvestment);

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

    public function dealWithInvestmentMembers($parents, $newInvestment)
    {
        if (count($parents)) {
            foreach ($parents as $level => $per) {
                $this->addMeritForMember($per, $newInvestment);
            }
        }
    }

    public function addMeritForMember($user, $newInvestment = 0)
    {
        if ($newInvestment) {
            $user->achievements += $newInvestment;
        }

        $calLevel = $user->calculateLevel();
        if ($calLevel > $user->level) {
            $user->level =  $calLevel;
        }
        $user->save();
    }
}
