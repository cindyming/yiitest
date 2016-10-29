<?php

namespace app\controllers;

use app\models\Cash;
use app\models\Revenue;
use common\models\JLock;
use Yii;
use app\models\Investment;
use app\models\InvestmentSearch;
use yii\base\Exception;
use yii\console\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\AccessRule;
use app\models\User;

/**
 * InvestmentController implements the CRUD actions for Investment model.
 */
class InvestmentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'logout'],
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['showduichong', 'adminindex', 'cancel', 'admincreate', 'admindelete', 'adminupdate',  'adminview'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
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
     * Lists all Investment models.
     * @return mixed
     */
    public function actionAdminindex()
    {
        $searchModel = new InvestmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('adminindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new InvestmentSearch();
        $data = Yii::$app->request->queryParams;

        $data['InvestmentSearch']['user_id'] = Yii::$app->user->identity->id;

        $dataProvider = $searchModel->search($data);
        $dataProvider->pagination = [
            'pageSize' => 10
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Investment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionShowduichong()
    {
        $id = Yii::$app->request->get('id');
        $result = array(
            'code' => 0,
            'message' => '该报单不存在'
        );

        if ($id) {
            $user = User::findOne($id);
            if ($user && $user->add_member && ($user->duichong_remain > 0)) {
                $result = array(
                    'code' => 1,
                    'message' => $user->duichong_remain
                );
            } else if ($user && $user->add_member) {
                $result['message'] = '该报单员对冲帐户余额为0';
            }
        }

        echo json_encode($result);die;
    }

    /**
     * Creates a new Investment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /**
     * Creates a new Investment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdmincreate()
    {
        $model = new Investment();

        if ($model->load(Yii::$app->request->post()))
        {
            $user =  User::findOne($model->user_id);
            $addedBy = User::findOne($model->added_by);

            $validateAmount = ($model->amount >= 1);
            if (!$validateAmount){
                $model->addError('amount', '投资额必须大于1W,并且为万的倍数,例如:10000,100000.请重新输入');
            }

            if ($model->useBaodan && $model->duichong_invest && ($model->duichong_invest > $addedBy->duichong_remain)) {
                $validateAmount = false;
                $model->addError('duichong_invest', '对冲帐户余额不足:' . $addedBy->duichong_remain);

            }

            if (($user && $user->getId()) && ($user->role_id == 3)) {

                $connection=Yii::$app->db;
                try {

                    $transaction = $connection->beginTransaction();
                    if ($validateAmount && $model->save()) {

                        if ($addedBy && $addedBy->getId() && ($addedBy->role_id == 3)) {

                            $meritAmount = 0;

                            if ($model->duichong_invest && $model->useBaodan) {
                                $meritAmount += round($model->duichong_invest * 0.01, 2);

                                $addedBy->duichong_remain -= $model->duichong_invest;
                                $data = array(
                                    'user_id' => $addedBy->id,
                                    'amount' => $model->duichong_invest,
                                    'status' => 2,
                                    'type' => 8,
                                    'fee' => 0,
                                    'total' => $addedBy->duichong_remain,
                                    'note' => '追加投资:' . $model->id . ', 使用对冲帐户金额:' . $model->duichong_invest
                                );
                                $cash = new Cash();
                                $cash->setAttributes($data);
                                $cash->save();
                            }
                            if ($meritAmount) {

                                $data = array(
                                    'user_id' => $addedBy->id,
                                    'note' => '会员：' . $model->user_id . '追加投资' . date('Y-m-d H:i:s') . '的报单奖励',
                                    'type' => 1,
                                    'baodan' => $meritAmount,
                                    'total' => $meritAmount + $addedBy->baodan_remain
                                );
                                $merit = new Revenue();
                                $merit->load($data, '');
                                $merit->save();
                                $addedBy->baodan_remain += $meritAmount;
                                $addedBy->baodan_total += $meritAmount;
                                $addedBy->save();
                            }
                        }
                        $transaction->commit();
                        Yii::$app->getSession()->set('message', '追加投资添加成功');
                        return $this->redirect(['adminindex']);

                    } else {
                        $transaction->rollback();
                        Yii::$app->getSession()->set('danger', '追加投资添加失败');
                    }


                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                }

            } else {
                $model->addError('user_id', '此会员不存在,请确认后再添加');
            }
        }
        return $this->render('admincreate', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing Investment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Investment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $key = 'INVESTIMENT' . $id;
        $sellLock = new \app\models\JLock($key);
        $sellLock->start();
        $model = $this->findModel($id);
        if($model->status) {
            $model->status = 0;
            $connection=Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();

                $model->save();
                $amount = $model->amount;
                $user = User::findById($model->user_id);
                if ($model->merited == 1) {
                    $user->reduceAchivement($amount);
                    $user->reduceMerit($model);
                    $user->reduceBonus($model);
                }
                $user->reduceBaodan($model);
                if ($user->save()) {
                    $transaction->commit();

                    Yii::$app->getSession()->set('message', '追加投资撤销成功');
                } else {
                    throw new Exception('Failed to save user ' . json_encode($user->getErrors()));
                }
            } catch (Exception $e) {
                $transaction->rollback();//回滚函数

                $model->status = 1;
                $model->save();

                Yii::$app->systemlog->add('Admin', '撤销投资', '失败', $e->getMessage());
                Yii::$app->getSession()->set('danger', '追加投资撤销失败, 请稍后再试. ' .  $e->getMessage());
            }

        }

        $sellLock->end();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Investment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Investment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Investment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
