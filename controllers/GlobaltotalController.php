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

        $date = '2016-06-01 00:00:00';

        $invertTotal = $connection->createCommand("SELECT sum(investment) as 'total' FROM user WHERE role_id=3 AND created_at >'{$date}'")->queryOne();

        $zhuijianvertTotal = $connection->createCommand("SELECT sum(amount) as 'total' FROM investment LEFT JOIN user on user.id=investment.user_id WHERE  investment.created_at >'{$date}' AND user.created_at<'{$date}'")->queryOne();

        $kouchMeritTotal = $connection->createCommand("SELECT sum(amount) as 'total'  FROM cach WHERE type=5 and created_at > '{$date}' and note like '%错误报单%'")->queryOne();

        $kouchBoadanTotal = $connection->createCommand("SELECT sum(amount) as 'total'  FROM cach WHERE type=6 and created_at > '{$date}' and note like '%错误报单%'")->queryOne();

        $bonus  = $connection->createCommand("SELECT sum(merit) as 'merit_total', sum(baodan) as 'baodan_total'  FROM revenue WHERE created_at > '{$date}'")->queryOne();

        $bonuss  = $connection->createCommand("SELECT  sum(bonus) as 'bonus_total' FROM revenue LEFT JOIN user on user.id=revenue.user_id WHERE revenue.created_at > '{$date}'  AND user.created_at>'{$date}' ")->queryOne();

        $data = array(
            'GlobalTotal' => array(
                'total_in' => $invertTotal['total'] + (float)$zhuijianvertTotal['total'],
                'mall' =>  ((float)$bonus['merit_total']  - (float)$kouchMeritTotal['total'])/9 ,
                'bonus' => (float)$bonuss['bonus_total'],
                'baodan' => (float)$bonus['baodan_total'] - (float)$kouchBoadanTotal['total'],
                'merit' => (float)$bonus['merit_total']  - (float)$kouchMeritTotal['total']
          )
        );

        $data['GlobalTotal']['total_out'] = ((float)$bonus['merit_total']  - (float)$kouchMeritTotal['total'])/9
            + (float)$bonuss['bonus_total'] + (float)$bonus['baodan_total'] - (float)$kouchBoadanTotal['total']
            + ((float)$bonus['merit_total']  - (float)$kouchMeritTotal['total']);

        if ($model->load($data) && $model->save()) {
            Yii::$app->systemlog->add('管理员', '拨比统计结算');
            return $this->redirect(['index']);
        }
    }
}