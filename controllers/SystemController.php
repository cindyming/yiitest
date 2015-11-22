<?php

namespace app\controllers;

use app\models\Log;
use app\models\User;
use app\models\Backup;
use Yii;
use app\models\System;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\filters\VerbFilter;

/**
 * SystemController implements the CRUD actions for System model.
 */
class SystemController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'logout', 'autologin'],
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['backup', 'backupindex', 'log','index'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all System models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new System();

        $postData = Yii::$app->request->post('System');
        if (count($postData)) {
            foreach ($postData as $key => $da) {
                $system = System::findOne([ 'name'=> $key]);
                if ($system && $system->id) {
                    $oldValue = $system->value;
                    $system->value = $da;
                    if ($system->save() && ($da != $oldValue)) {
                        Yii::$app->systemlog->add('管理员',  '修改系统参数','成功' , $key . ': 从 (' . $oldValue . ')改为（' . $da . ')');
                    } else if(($da != $oldValue)){
                        Yii::$app->systemlog->add('管理员',  '修改系统参数', '失败' ,$key . ': 从 (' . $oldValue . ')改为（' . $da . ')');
                    }
                } else {
                    $system = new System();
                    $system->name = $key;
                    $system->value = $da;
                    $system->save();
                }

            }
            Yii::$app->cache->set('SYSTEM_CONFIG', null);
        }

        $data = System::loadConfig();
        if ($data && count($data)) {
            foreach($data as $key => $da) {
                $model->$key = $da;
            }
        }

        return $this->render('index', [
            'model' =>$model
        ]);
    }

    /**
     * Displays a single System model.
     * @param integer $id
     * @return mixed
     */
    public function actionLog()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Log::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('log', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBackup()
    {
        exec('sh /home/backup/backup.sh');
        Yii::$app->getSession()->set('backupmessage', '数据库备份成功.');
        $this->redirect(array('/system/backupindex'));
    }

    public function actionBackupindex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Backup::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('backup', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
