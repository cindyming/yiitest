<?php

namespace app\controllers;

use Yii;
use app\models\GlobalTotal;
use app\models\GlobalTotalSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\filters\VerbFilter;
use app\models\User;

/**
 * GlobalTotalController implements the CRUD actions for GlobalTotal model.
 */
class GlobaltotalController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['adminindex', 'adminop'],
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
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
     * Lists all GlobalTotal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GlobalTotalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new GlobalTotal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GlobalTotal();

        $connection=Yii::$app->db;

        $invertTotal = $connection->createCommand('SELECT sum(investment) as "total", sum(merit_total) as "merit_total", sum(bonus_total) as "bonus_total",  sum(baodan_total) as "baodan_total" FROM user WHERE role_id=3')->queryAll();

        $data = array(
            'GlobalTotal' => array(
                'total_in' => $invertTotal[0]['total'],
                'total_out' => $invertTotal[0]['merit_total'] + $invertTotal[0]['bonus_total'] + $invertTotal[0]['baodan_total']
            )
        );

        if ($model->load($data) && $model->save()) {
            Yii::$app->systemlog->add('管理员', '拨比统计结算');
            return $this->redirect(['index']);
        }
    }
}