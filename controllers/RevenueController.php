<?php

namespace app\controllers;

use app\models\RevenueSearch;
use app\models\UserSearch;
use Yii;
use app\models\Revenue;
use yii\base\Exception;
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
                            'actions' => ['adminindex', 'admintotal', 'adminglobal', 'adminin', 'manualadd'],
                            'roles' => [User::ROLE_ADMIN]
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index', 'total', 'in'],
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

        $dataProvider = $searchModel->searchForCaiwu(Yii::$app->request->queryParams);

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

        $invertTotal = $connection->createCommand('SELECT sum(investment) as "total" FROM user WHERE role_id=3')->queryAll();
        $inTotal = $invertTotal[0]['total'];


        $outTotal = $connection->createCommand('SELECT sum(bonus) as "bonus_total", sum(merit) as "merit_total", sum(baodan) as "baodan_total" FROM revenue')->queryAll();
        $bonusTotal = $outTotal[0]['bonus_total'] ? $outTotal[0]['bonus_total'] : 0;
        $meritTotal = $outTotal[0]['merit_total'] ? $outTotal[0]['merit_total'] : 0;
        $mallTotal = $meritTotal * 0.1;
        $meritTotal = $meritTotal * 0.9;
        $baodanTotal = $outTotal[0]['baodan_total'] ? $outTotal[0]['baodan_total'] : 0;


        return $this->render('adminglobal', [
            'inTotal' => $inTotal,
            'bonusTotal' => $bonusTotal,
            'meritTotal' => $meritTotal,
            'mallTotal' => $mallTotal,
            'baodanTotal' => $baodanTotal
        ]);
    }

    public function actionAdminin()
    {
        $searchModel = new RevenueSearch();

        $dataProvider = $searchModel->searchForHuobi(Yii::$app->request->queryParams);

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

        $searchModel = new UserSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
            'query' => User::find()->where(['=', 'id', Yii::$app->user->identity->id]),
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
        $data['RevenueSearch']['type'] = 1;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 10,
        ];


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);

    }

    public function actionIn()
    {
        $searchModel = new RevenueSearch();

        $data = Yii::$app->request->queryParams;

        $data['RevenueSearch']['user_id'] = Yii::$app->user->identity->id;
        $data['RevenueSearch']['type'] = array(2, 3, 10);

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 10,
        ];
        return $this->render('in', [
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

    public function actionManualadd()
    {
        $model = new Revenue();
        $model->setScenario('manual');

        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne($model->user_id);
            $validateAmount = true;
            if ((float)$model->amount <= 0) {
                $validateAmount = false;
                $model->addError('amount', '提现金额必须大于0.');
            }
            if ($user && $user->id && $validateAmount) {
                $connection = Yii::$app->db;
                try {
                    $model->setScenario('default');
                    $transaction = $connection->beginTransaction();
                    if ($model->type == 1) {
                        $user->bonus_remain = $user->bonus_remain + $model->amount;
                        $user->bonus_total = $user->bonus_total + $model->amount;
                        $model->bonus = $model->amount;
                        $model->total = $user->bonus_remain;
                    } elseif($model->type == 2) {
                        $user->merit_remain = $user->merit_remain + $model->amount;
                        $user->merit_total = $user->merit_total + $model->amount;
                        $model->merit = $model->amount;
                        $model->total = $user->merit_remain;
                    } elseif($model->type == 3) {
                        $user->baodan_remain = $user->baodan_remain + $model->amount;
                        $user->baodan_total = $user->baodan_total + $model->amount;
                        $model->baodan = $model->amount;
                        $model->total = $user->baodan_remain;
                    } elseif($model->type == 4) {
                        $user->mall_remain = $user->mall_remain + $model->amount;
                        $user->mall_total = $user->mall_total + $model->amount;
                        $model->mall = $model->amount;
                        $model->total = $user->mall_remain;
                    } elseif($model->type == 5) {
                        $user->duichong_remain = $user->duichong_remain + $model->amount;
                        $user->duichong_total = $user->duichong_total + $model->amount;
                        $model->duichong = $model->amount;
                        $model->total = $user->duichong_remain;
                    }  else {
                        $model->addError('type', '请选择账户类型.');
                        throw new Exception('账户类型错误');
                    }
                    $model->type = 2;
                    $model->note = $model->note . '(管理员充值: ' . date('Y-m-d', time()) . ')';
                    if ($model->save() && $user->save()) {
                        $transaction->commit();
                        Yii::$app->systemlog->add('管理员', '添加货币 - 收入', '成功','会员: ' .$model->user_id . ' ; ' . $model->note );\
                        Yii::$app->getSession()->set('message', '会员:(' . $model->user_id . ')添加货币成功');
                        return $this->redirect(['/user/huobi']);
                    } else {
                        $transaction->rollback();
                    }


                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                }

            } else {
                $model->addError('user_id', '会员编号不存在, 请确认后重新操作');
            }

            Yii::$app->systemlog->add('管理员', '添加货币 - 收入', '失败','会员: '  .$model->user_id  .serialize($model->getErrors()));
        }

        return $this->render('manualadd', [
            'model' => $model,
        ]);
    }
}
