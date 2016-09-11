<?php

namespace app\controllers;

use app\models\Log;
use app\models\Revenue;
use app\models\System;
use Yii;
use app\models\Cash;
use app\models\CashSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\AccessRule;
use app\models\User;

/**
 * CashController implements the CRUD actions for Cash model.
 */
class CashController extends Controller
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
                        'actions' => ['adminindex', 'adminreject', 'adminapprove', 'adminupdate',  'adminview', 'adminout', 'manualadd'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'create','out'],
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
     * Lists all Cash models.
     * @return mixed
     */
    public function actionAdminindex()
    {
        $searchModel = new CashSearch();
        $dataProvider = $searchModel->searchForAdmininex(Yii::$app->request->queryParams);

        return $this->render('adminindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdminout()
    {
        $searchModel = new CashSearch();
        $data = Yii::$app->request->queryParams;
        $data['CashSearch']['status'] = 2;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('adminout', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOut()
    {
        $searchModel = new CashSearch();

        $dataProvider = $searchModel->searchForMeber(Yii::$app->request->queryParams);

        return $this->render('out', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Cash models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashSearch();

        $data = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchForMemberIndex($data);
        $dataProvider->pagination = [
            'pageSize' => 10
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cash model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cash model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $type = Yii::$app->request->getQueryParam('type');
        $model = new Cash();

        $data = Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post()) && isset($data['Cash']) && isset($data['Cash']['password2'])) {
            $validateAmount = true;
            if (!in_array($model->type, array(1,2,3))){
                $model->addError('type', '请选择账户类型.');
            } else {
                if ($model->type == 1) {
                    $compareAmount = Yii::$app->user->identity->bonus_remain;
                } elseif($model->type == 2) {
                    $compareAmount = Yii::$app->user->identity->merit_remain;
                } elseif($model->type == 3) {
                    $compareAmount = Yii::$app->user->identity->baodan_remain;
                }
                if ($model->amount > $compareAmount) {
                    $validateAmount = false;
                    $model->addError('amount', '可供提现的约不足, 请确认后重新输入. 分红余额: ' . Yii::$app->user->identity->bonus_remain . ', 绩效余额: ' . Yii::$app->user->identity->merit_remain .  ', 服务费余额: ' . (float)Yii::$app->user->identity->baodan_remain  . '.');
                }
            }

            if ((float)$model->amount <= 0) {
                $validateAmount = false;
                $model->addError('amount', '提现金额必须大于0.');
            }


            if ($model->amount < System::loadConfig('lowest_cash_amount')) {
                $validateAmount = false;
                $model->addError('amount', '最低提现额为: ' . System::loadConfig('lowest_cash_amount') . '.');
            }
            if (Yii::$app->user->identity->validatePassword2($data['Cash']['password2'])) {

                if ($validateAmount) {
                    if ($type == 'transfer') {
                        $model->cash_type = 1;
                    }
                    $connection = Yii::$app->db;
                    try {
                        $transaction = $connection->beginTransaction();
                        $user = User::findById(Yii::$app->user->identity->id);
                        if ($model->type == 1) {
                            $user->bonus_remain = $user->bonus_remain - $model->amount;
                        } elseif($model->type == 2) {
                            $user->merit_remain = $user->merit_remain - $model->amount;
                        } elseif($model->type == 3) {
                            $user->baodan_remain = $user->baodan_remain - $model->amount;
                        }
                        $user->save();
                        if ($model->save()) {
                            $transaction->commit();
                            Yii::$app->getSession()->set('message', '提现申请提交成功');
                            return $this->redirect(['index']);
                        } else {
                            $transaction->rollback();
                        }
                    }  catch (Exception $e) {
                        $transaction->rollback();//回滚函数
                    }
                }
            } else {
                $model->addError('password2', '二级密码不正确, 请输入正确的二级密码');
            }
        }

        if ($type == 'transfer') {
            return $this->render('transfer', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAdminapprove($id)
    {
        $model = $this->findModel($id);

        try {
            $model->status = 2;
            $user = User::findById($model->user_id);
            if ($model->type == 1) {
                $model->total = $user->bonus_remain;
            } elseif($model->type == 2) {
                $model->total = $user->merit_remain;
            } elseif($model->type == 3) {
                $model->total = $user->baodan_remain;
            }
            $pass = true;

            if ($model->cash_type == 1) {
                $service_url = Yii::$app->params['stack_url'] . 'v1/accounts';
                $curl = curl_init($service_url);
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_USERPWD, "admin:lml1314"); //Your credentials goes here
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('member_id' => $model->stack_number, 'amount' => $model->real_amount, 'refer_id' => $user->username)));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $curl_response = curl_exec($curl);
                $response = json_decode($curl_response);
                curl_close($curl);

                Yii::info('会员(' . $model->user_id . ')' . '提现股票转移' . '返回' .json_encode($response));
                if (is_array($response) || !$response || !property_exists($response, 'id')) {
                    if (is_array($response)) {
                        foreach ($response as $r) {
                            if ($r->field == 'member_id') {
                                Yii::$app->getSession()->set('message', $r->message);
                            }
                        }
                    }
                    $pass = false;
                    Log::add('会员(' . $model->user_id . ')', '提现股票转移失败', '失败', json_encode($response));
                } else {
                    $model->note = '提现成功; 转移股票账号成功, id' . $model->stack_number;
                }
            } else {
                $model->note = '会员提现发放成功';
            }


            $connection=Yii::$app->db;
            $transaction = $connection->beginTransaction();
            if ($pass && $model->save() && $user->save()) {
                Yii::$app->getSession()->set('message', '会员(' . $model->user_id . ')提现申请发放成功');
                $transaction->commit();
            } else {
                if ($model->cash_type != 1) {
                    Yii::$app->getSession()->set('message', '会员(' . $model->user_id . ')提现申请发放失败');
                }

                $transaction->rollback();
            }

            $this->redirect(Yii::$app->request->referrer);
            return;
        } catch (Exception $e) {
            $transaction->rollback();//回滚函数
        }


        return $this->redirect(['adminindex', 'id' => $model->id]);

    }


    public function actionAdminreject($id)
    {
        $model = $this->findModel($id);
        $connection = Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();
            $model->status = 3;

            $user = User::findById($model->user_id);
            $data = array(
                'user_id' => $model->user_id,
                'type' => 2,
                'note' => '拒绝提现'
            );
            if ($model->type == 1) {
                $user->bonus_remain = $user->bonus_remain + $model->amount;
                $data['bonus'] =  $model->amount;
                $model->total = $user->bonus_remain;
                $data['total'] =  $user->bonus_remain;
            } elseif($model->type == 2) {
                $user->merit_remain = $user->merit_remain + $model->amount;
                $data['merit'] =  $model->amount;
                $data['total'] =  $user->merit_remain;
                $model->total = $user->merit_remain;
            } elseif($model->type == 3) {
                $user->baodan_remain = $user->baodan_remain + $model->amount;
                $data['baodan'] =  $model->amount;
                $data['total'] =  $user->baodan_remain;
                $model->total = $user->baodan_remain;
            }
            $model->note .= '拒绝'  . (($model->cash_type == 1) ? '股票' : '现金') . '提现,货币返还.';
            Yii::$app->getSession()->set('message', '提现申请拒绝成功');
            $revenue = new Revenue();
            $revenue->load($data, '');
            $revenue->save();
            $user->save();
            $model->save();
            $transaction->commit();
            $this->redirect(Yii::$app->request->referrer);
            return;
        }  catch (Exception $e) {
            $transaction->rollback();//回滚函数
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        return $this->redirect(['adminindex', 'id' => $model->id]);

    }
    /**
     * Updates an existing Cash model.
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
     * Deletes an existing Cash model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cash model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cash the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cash::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionManualadd()
    {
        $model = new Cash();

        $data = Yii::$app->request->post();
        $data['Cash']['status'] = 2;
        if (Yii::$app->request->post() && $model->load($data)) {
            $user = User::findOne($model->user_id);
            if ($user && $user->id) {
                $validateAmount = true;
                $model->amount = (float)$model->amount;
                $model->status = 2;

                if(in_array($model->type, array(4,5,6,7))) {
                    if ($model->type == 4) {
                        $compareAmount = $user->bonus_remain;
                    } elseif($model->type == 5) {
                        $compareAmount = $user->merit_remain;
                    } elseif($model->type == 6) {
                        $compareAmount = $user->baodan_remain;
                    } elseif($model->type == 7) {
                        $compareAmount = $user->mall_remain;
                    }
                    if ($model->amount > $compareAmount) {
                        $validateAmount = false;
                        $model->addError('amount', '可供提现的约不足, 请确认后重新输入. 分红余额: ' . $user->bonus_remain . ', 绩效余额: ' . $user->merit_remain .  ', 服务费余额: ' . (float)$user->baodan_remain  .  ', 商城币余额: ' . (float)$user->mall_remain  . '.');
                    }
                } else {
                    $validateAmount = false;
                    $model->addError('type', '请选择账户类型.');
                }

                if ($model->amount <= 0) {
                    $validateAmount = false;
                    $model->addError('amount', '提现金额必须大于0.');
                }

                if ($model->amount < System::loadConfig('lowest_cash_amount')) {
                    $validateAmount = false;
                    $model->addError('amount', '最低提现额为: ' . System::loadConfig('lowest_cash_amount') . '.');
                }
                if ($validateAmount) {
					if ($model->type == 4) {
						$user->bonus_remain = $user->bonus_remain - $model->amount;
						$model->total = $user->bonus_remain;
					} elseif($model->type == 5) {
						$user->merit_remain = $user->merit_remain - $model->amount;
						$model->total = $user->merit_remain;
					} elseif($model->type == 6) {
						$user->baodan_remain = $user->baodan_remain - $model->amount;
						$model->total = $user->baodan_remain;
					} elseif($model->type == 7) {
						$user->mall_remain = $user->mall_remain - $model->amount;
						$model->total = $user->mall_remain;
					}
                    $connection = Yii::$app->db;
                    try {
                        $transaction = $connection->beginTransaction();
                        $model->note = $model->note . '(管理员扣除: ' . date('Y-m-d', time()) . ')';
                        $model->save();
                        $user->save();
                        $transaction->commit();
                        Yii::$app->systemlog->add('管理员', '添加货币 - 支出', '成功','会员: ' .$model->user_id . ' ; ' . $model->note );
                        Yii::$app->getSession()->set('message', '会员:(' . $model->user_id . ')扣除货币成功');
                        return $this->redirect(['/user/huobi']);
                    } catch (Exception $e) {
                        $transaction->rollback();//回滚函数
                        Yii::$app->log($e->getMessage());
                    }
                    $transaction = $connection->beginTransaction();
                }

            } else {
                $model->addError('user_id', '会员编号不存在, 请确认后重新操作');
            }
            Yii::$app->systemlog->add('管理员', '添加货币 - 收入', '失败','会员: ' .$model->user_id  .serialize($model->getErrors()));
        }

        return $this->render('manualadd', [
            'model' => $model,
        ]);
    }
}
