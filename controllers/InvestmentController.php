<?php

namespace app\controllers;

use app\models\Cash;
use app\models\Log;
use app\models\Revenue;
use app\models\System;
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
                        'actions' => ['showduichong', 'free', 'export', 'freelist', 'adminindex', 'cancel', 'admincreate', 'admindelete', 'adminupdate',  'adminview'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'transfer'],
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

                            if ($model->duichong_invest) {
                                if (System::loadConfig('opend_investment_duichong_baodan_fee')) {
                                    $meritAmount += round($model->duichong_invest * 0.01, 2);
                                }

                                $addedBy->duichong_remain -= $model->duichong_invest;
                                $data = array(
                                    'user_id' => $addedBy->id,
                                    'amount' => $model->duichong_invest,
                                    'status' => 2,
                                    'type' => 8,
                                    'fee' => 0,
                                    'total' => $addedBy->duichong_remain,
                                    'note' => '会员:' . $model->user_id . ', 追加投资' . $model->id . ', 使用对冲帐户金额:' . $model->duichong_invest
                                );
                                $cash = new Cash();
                                $cash->setAttributes($data);
                                $cash->save();
                            }
                            if ($meritAmount) {

                                $data = array(
                                    'user_id' => $addedBy->id,
                                    'note' => '会员：' . $model->user_id . '追加投资' . $model->id . '的报单奖励',
                                    'type' => 1,
                                    'baodan' => $meritAmount,
                                    'total' => $meritAmount + $addedBy->baodan_remain
                                );
                                $merit = new Revenue();
                                $merit->load($data, '');
                                $merit->save();
                                $addedBy->baodan_remain += $meritAmount;
                                $addedBy->baodan_total += $meritAmount;

                            }
                            $addedBy->save();
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
                    if ($model->be_stack) {
                        $user->total_stack -= $model->stack;
                        $user->stack -= $model->stack;
                    }
                    Yii::$app->getSession()->set('big', '追加投资撤销成功, 撤单后等级不自动变化，请核对等级');
                } else {
                    Yii::$app->getSession()->set('message', '追加投资撤销成功');
                }
                $user->reduceBaodan($model);

                if(strpos($model->note, '分红转追加投资')) {
                    $inUserId = str_replace(array('会员(',')分红转追加投资'), array('', ''), $model->note);
                    if ($inUserId == $model->user_id) {
                        $user->bonus_remain = $user->bonus_remain + $model->amount;
                        $inrecordData = array(
                            'user_id' => $inUserId,
                            'type' => 2,
                            'note' => '转出至:' . $user->id . '的追加投资撤销,货币返还.',
                            'bonus' =>  $model->amount,
                            'total' =>  $user->bonus_remain);
                    } else {
                        $inUser  = User::findById($inUserId);
                        if ($inUser) {
                            $inUser->bonus_remain = $inUser->bonus_remain + $model->amount;
                            $inrecordData = array(
                                'user_id' => $inUserId,
                                'type' => 2,
                                'note' => '转出至:' . $inUser->id . '的追加投资撤销,货币返还.',
                                'bonus' =>  $model->amount,
                                'total' =>  $inUser->bonus_remain);
                            $inUser->save();
                        } else {
                            throw new Exception('对应的转出账户编号没有找到 ' . $inUserId);
                        }

                    }
                    if (isset($inrecordData) && is_array($inrecordData)) {
                        $revenue = new Revenue();
                        $revenue->load($inrecordData, '');
                        $revenue->save();
                    }
                }

                if ($user->save()) {
                    $transaction->commit();

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


    public function actionTransfer($id)
    {
        if (System::loadConfig('open_stack_transfer')) {
            $key = 'INVESTIMENT_STACK_TRANSFER_' . Yii::$app->user->identity->id;
            $sellLock = new \app\models\JLock($key);
            $sellLock->start();
            if ($id == 'all') {
                $model = Yii::$app->user->identity;
                if ((!$model->redeemed) && ($model->be_stack == 1)) {
                    $user = $model;
                    $model->redeemed = 1;
                    $model->stack -= $model->init_stack;
                    $cash = new Cash();
                    $cash->cash_type = 6;
                    $cash->type = 11;
                    $cash->amount = $model->init_stack;
                    $cash->total = $model->init_stack;
                    $cash->status = 2;
                }
            } else {
                $model = $this->findModel($id);

                if ((Yii::$app->user->id == $model->user_id) && ($model->status == 1) && ($model->be_stack == 1))  {
                    $model->status = 2;
                    $user = User::findOne(Yii::$app->user->id);
                    $user->stack -= $model->stack;
                    $cash = new Cash();
                    $cash->cash_type = 6;
                    $cash->type = 11;
                    $cash->amount = $model->stack;
                    $cash->total = $user->stack;
                    $cash->status = 2;
                }

            }

            if (isset($cash) && $cash && $cash->amount) {

                try {
                    $service_url = Yii::$app->params['cuohe_url'] . 'api/user/stack';
                    $curl = curl_init($service_url);
                    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($curl, CURLOPT_USERPWD, $user->access_token); //Your credentials goes here
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('stack' => $cash->amount, 'token' => $user->access_token)));
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate


                    $curl_response = curl_exec($curl);
                    $response = (array)json_decode($curl_response);
                    curl_close($curl);

                    Log::add('会员(' . $user->id . ')', '自由股兑换', '返回', $service_url . $curl_response);
                    if (is_array($response) && isset($response['code']) && ($response['code'] == 200)) {
                        $pass = true;
                        $cash->note = '自由股兑换成功, id:' . $response['data'];
                        $cash->save();
                        $user->save();
                        $model->save();
                        Yii::$app->getSession()->set('message', '自由股兑换成功');
                    } else {
                        Yii::$app->getSession()->set('danger', '自由股兑换失败,请稍候再试');
                    }

                } catch (Exception $e) {
                    Log::add('会员(' . $user->id . ')', '自由股兑换', '失败', $curl_response);
                    Yii::$app->getSession()->set('danger', '自由股兑换失败,请稍候再试' . $e->getMessage());
                }
            }
            $sellLock->end();
        } else {
            Yii::$app->getSession()->set('danger', '自由股兑换已关闭,请稍候再试');
        }


        return $this->redirect(['/investment/index']);

    }

    public function actionExport()
    {
        $searchModel = new InvestmentSearch();
        $data = Yii::$app->request->queryParams;
        if (Yii::$app->request->get('week', 0)) {
            $data['InvestmentSearch']['created_at'] = date('Y-m-d', strtotime('-7 days')) . ' - ' .date('Y-m-d', time());
        } else if ((!isset($data["InvestmentSearch"])) && (!isset($data["InvestmentSearch"]['created_at']))) {
            $data['InvestmentSearch']['approved_at'] = date('Y-m-d', strtotime('-7 days')) . ' - ' .date('Y-m-d', time());
        }
        $searchModel->export($data);
        return $this->redirect(['/assets/Investment.xls']);
    }

    public function actionFreelist()
    {
        $searchModel = new InvestmentSearch();
        $data = Yii::$app->request->queryParams;

        $data['InvestmentSearch']['user_id'] = $data['id'];

        $dataProvider = $searchModel->search($data);
        $dataProvider->pagination = [
            'pageSize' => 10
        ];

        $model = User::findOne($data['id']);

        return $this->render('freelist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    public function actionFree($id)
    {
        if (System::loadConfig('open_stack_transfer')) {
            $key = 'INVESTIMENT_STACK_FREE_' . $id;
            $sellLock = new \app\models\JLock($key);
            $sellLock->start();

            $type =  Yii::$app->getRequest()->get('type', 'investment');

            $model = null;
            $stack = 0;
            $userId = null;

            if ($type == 'all') {
                $model = User::findOne($id);
                $model->investment -= $model->init_investment;
                $userId = $id;
                $stack = User::investToStack($model->init_investment, date('Ymd', strtotime($model->approved_at)));
            } else {
                $model = Investment::findOne($id);
                $userId = $model->user_id;
                $stack = User::investToStack($model->amount, date('Ymd', strtotime($model->created_at)));
            }

            if ($model && $model->id && ! $model->be_stack) {
                if ($stack) {
                    $user = null;
                    $model->be_stack =1;
                    $data = array();
                    if ($type == 'all') {
                        $model->total_stack += $stack;
                        $model->stack += $stack;
                        $data = array(
                            'user_id' => $model->id,
                            'note' => '初始投资折算配股数',
                            'stack' => $stack,
                            'type' => 10,
                            'total' => $model->stack
                        );
                    } else {
                        $user = User::findOne($model->user_id);
                        $user->total_stack += $stack;
                        $user->stack += $stack;
                        $user->investment -= $model->amount;

                        $data = array(
                            'user_id' => $user->id,
                            'note' => '追加投资折算配股数:' . $model->id,
                            'stack' => $stack,
                            'type' => 10,
                            'total' => $user->stack,
                        );
                    }

                    if (count($data)) {
                        $revenue = new Revenue();
                        $revenue->load($data, '');

                        $connection = Yii::$app->db;
                        try {
                            $transaction = $connection->beginTransaction();
                            if (($user && $user->save() && $model->save() && $revenue->save()) || (!$user && $model->save() && $revenue->save())) {
                                $transaction->commit();
                                Yii::$app->getSession()->set('message', '股票转换成功');
                            } else {
                                Yii::$app->getSession()->set('message', '股票转换失败');
                                $transaction->rollBack();
                            }
                        } catch (Exception $e) {
                            Yii::$app->getSession()->set('message', '股票转换失败');
                            $transaction->rollBack();
                        }
                    }

                } else {
                    Yii::$app->getSession()->set('danger', '股票转换失败,请稍候再试');
                }

            } else {
                Yii::$app->getSession()->set('danger', '原始模型没有找到,请稍候再试');
            }

            $sellLock->end();
        } else {
            Yii::$app->getSession()->set('danger', '股票转换失败已关闭,请稍候再试');
        }


        return $this->redirect(['/investment/freelist?id=' . $userId]);
    }
}
