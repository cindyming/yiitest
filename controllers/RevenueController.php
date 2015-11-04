<?php

namespace app\controllers;

use app\models\RevenueSearch;
use Yii;
use app\models\Revenue;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\AccessRule;

/**
 * RevenueController implements the CRUD actions for Revenue model.
 */
class RevenueController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['adminindex', 'admintotal', 'adminglobal', 'adminin'],
                            'roles' => [User::ROLE_ADMIN]
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'total'],
                            'roles' => [User::ROLE_USER],
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
     * Lists all Revenue models.
     * @return mixed
     */
    public function actionAdminindex()
    {
        $searchModel = new RevenueSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('adminindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionAdminglobal()
    {
        $connection=Yii::$app->db;

        $invertTotal = $connection->createCommand('SELECT sum(investment) as "total" FROM user WHERE role_id!=1')->queryColumn(['total']);
        $invertTotal = $invertTotal[0];

        $outTotal = $connection->createCommand('SELECT sum(bonus) as "bonus_total", sum(merit) as "merit_total" FROM revenue')->queryAll();
;
        $bonusTotal = $outTotal[0]['bonus_total'] ? $outTotal[0]['bonus_total'] : 0;
        $meritTotal = $outTotal[0]['merit_total'] ? $outTotal[0]['merit_total'] : 0;

        return $this->render('adminglobal', [
            'inTotal' => $invertTotal,
            'bonusTotal' => $bonusTotal,
            'meritTotal' => $meritTotal
        ]);
    }

    public function actionAdminin()
    {
        $searchModel = new RevenueSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('adminin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Revenue models.
     * @return mixed
     */
    public function actionAdmintotal()
    {

        $searchModel = new RevenueSearch();

        $dataProvider = $searchModel->searchTotal(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('admintotal', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Revenue models.
     * @return mixed
     */
    public function actionTotal()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Revenue::find()
                        ->select(['id', 'sum(bonus) as bonus_total', 'sum(merit) as merit_total', 'user_id'])
                        ->where(['=', 'user_id', Yii::$app->user->identity->id]),
        ]);


        return $this->render('total', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Revenue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RevenueSearch();

        $data = Yii::$app->request->queryParams;

        $data['RevenueSearch']['user_id'] = Yii::$app->user->identity->id;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 10,
        ];


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);

    }

    /**
     * Finds the Revenue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Revenue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Revenue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
