<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

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

        $userParents = array();

        $userQuery = User::find()->orderBy('id', SORT_ASC);

        $provider = new ActiveDataProvider([
            'query' => $userQuery,
            'pagination' => [
                'pageSize' => 1000,
            ],
        ]);
        $provider->prepare();

        for($i=1; $i<=$provider->getPagination()->getPageCount();$i++) {
            if ($i != 1) {
                $provider = new ActiveDataProvider([
                    'query' => $userQuery,
                    'pagination' => [
                        'pageSize' => 1000,
                        'page' => $i - 1,
                    ],
                ]);
            }
            $users = $provider->getModels();
            foreach ($users as $user) {
                if (($user->referer)) {
                    if (isset($userParents[$user->referer])) {
                        $user->parentIds = $userParents[$user->referer]  . $user->id . ',';
                    } else {
                        $user->parentIds = $user->id . ',';
                    }
                    $userParents[$user->id] = $user->parentIds;
                    $user->save();
                }

            }
        }
    }
}
