<?php

namespace app\controllers;

use app\models\Log;
use Yii;
use app\models\System;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * SystemController implements the CRUD actions for System model.
 */
class SystemController extends Controller
{
    public function behaviors()
    {
        return [
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
                    $system->value = $da;
                    $system->save();
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


}
