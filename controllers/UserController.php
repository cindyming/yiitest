<?php

namespace app\controllers;

use app\models\Cash;
use app\models\Investment;
use app\models\System;
use Yii;
use app\models\User;
use app\models\Revenue;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserSearch;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\Response;
use app\components\AccessRule;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    private $successInfo = array();
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
                        'actions' => ['adminindex', 'cancel', 'huobi','validate', 'admincreate', 'suggestindex', 'adminreject','success','adminresetpassword','adminapplyindex','adminchange','adminapproveforaddmember', 'admintree', 'admintreelazy', 'adminindexapprove', 'adminindexunapprove', 'adminupdate', 'adminview', 'adminapprove'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'changepassword', 'validate', 'create', 'success', 'view', 'applyaddmember', 'tree', 'baodanindex'],
                        'roles' => [User::ROLE_USER],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionAdminindex()
    {
        $searchModel = new UserSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('adminindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionAdminindexapprove()
    {
        $searchModel = new UserSearch();

        $data = Yii::$app->request->queryParams;

        $data['UserSearch']['role_id'] =  3;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];

        return $this->render('adminindexapprove', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionAdminindexunapprove()
    {
        $searchModel = new UserSearch();

        $data = Yii::$app->request->queryParams;

        $data['UserSearch']['role_id'] = 2;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('adminindexunapprove', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionAdminapplyindex()
    {
        $searchModel = new UserSearch();

        $data = Yii::$app->request->queryParams;

        $data['UserSearch']['add_member'] = 1;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('adminapplyindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionAdminapproveforaddmember($id)
    {
        $model = $this->findModel($id);
        $model->add_member = 2;

        if ( $model->save()) {
            Yii::$app->getSession()->set('message', '会员(' .$id. ')报单员审核成功');
            return $this->redirect(['adminindexapprove']);
        } else {
            return $this->redirect(['adminapplyindex']);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAdminapprove($id)
    {
        $model = $this->findModel($id);
        $data = array('User' => array('approved_at' => date('Y-m-d h:i:s', time()), 'role_id'=> 3));

        try {
            if ($model->canApproved() && $model->load($data) && $model->save()) {
                $addedBy = User::findOne($model->added_by);
                if ($addedBy && $addedBy->getId() && ($addedBy->role_id == 3)) {
                    $meritAmount = round($model->investment * 0.01, 2);
                    if ($model->duichong_invest && System::loadConfig('opend_duichong_baodan_fee')) {
                        $meritAmount +=  round($model->duichong_invest * 0.01, 2);
                    }

                    $data = array(
                        'user_id' => $addedBy->id,
                        'note' => '会员：' .$model->id . '的报单奖励',
                        'type' => 1,
                        'baodan' => $meritAmount,
                        'total' => $meritAmount +  $addedBy->baodan_remain
                    );
                    $merit = new Revenue();
                    $merit->load($data, '');
                    $merit->save();
                    $addedBy->baodan_remain += $meritAmount;
                    $addedBy->baodan_total += $meritAmount;
                    $addedBy->save();

                }
                Yii::$app->getSession()->set('message', '会员(' .$id. ')审核成功');
                return $this->redirect(['adminindexapprove']);
            } else {
                return $this->redirect(['adminindexunapprove']);
            }
        } catch(Exception $e) {
            Yii::$app->getSession()->set('danger', $e->getMessage());
            return $this->redirect(['adminindexunapprove']);
        }

    }

    public function actionAdminresetpassword($id)
    {
        $model = $this->findModel($id);

        if ($model->resetPasword()) {
            Yii::$app->getSession()->set('message', '会员('. $id . ')密码重置成功');
        } else {
            Yii::$app->getSession()->set('message', '会员('. $id . ')密码重置失败, 请稍后再试');
        }

        return $this->redirect(['adminindexapprove']);
    }

    public function actionSuggestindex()
    {
        $searchModel = new UserSearch();

        $dataProvider = $searchModel->suggestSearch(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];


        return $this->render('suggestindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionAdminreject($id)
    {
        $model = $this->findModel($id);
        $data = array('User' => array('role_id'=> 4));

        if ($model->load($data) && $model->save()) {
            if ($model->duichong_invest) {
                $user = User::findOne($model->added_by);
                $user->duichong_remain  += $model->duichong_invest;
                $revene = new Revenue();
                $data = array(
                    'user_id' =>$user->id,
                    'duichong' => $model->duichong_invest,
                    'total' => $user->duichong_remain,
                    'type' => 2,
                    'note' => '报单会员拒绝:' . $model->id
                );
                $revene->setAttributes($data);
                $user->save();
                $revene->save();
            }


            Yii::$app->getSession()->set('message', '会员('. $id . ')拒绝成功');
        }

        return $this->redirect(['adminindexunapprove']);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAutologin($id)
    {
       // Yii::$app->user->logout();

        $model = $this->findModel($id);
        Yii::$app->user->login($model);

        return $this->redirect(['news/index']);
    }

    public function actionValidate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->setScenario("create");
            $result = ActiveForm::validate($model);
            $this->validateUserData($model);
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }
            foreach ($this->successInfo as $attribute => $message) {
                $result[Html::getInputId($model, $attribute) . '-success'] = $message;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        }
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionAdminview($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdmincreate()
    {
        $model = new User();
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post())) {
            $validate = $this->validateUserData($model);
            if ($validate && $model->save()) {
                return $this->redirect(['success', 'id' => $model->id]);
            }
        }
        return $this->render('admincreate', [
            'model' => $model,
        ]);

    }

    public function actionTree()
    {
        if (!System::loadConfig('open_member_tree')) {
            Yii::$app->getSession()->set('danger', '网络图功能已关闭,请联系管理员.');
            return $this->redirect(['/news/index']);
        }

        $users=Yii::$app->db->createCommand('SELECT id,role_id,username,referer,investment,achievements,locked FROM user where role_id in (2,3) AND id>=' . Yii::$app->user->identity->id)->query();

        $result = array();

        $ids = array();

        $id = Yii::$app->getRequest()->get('id');

        foreach($users as $user) {
            if ($user['id'] == Yii::$app->user->identity->id) {
                $ids[] = $user['id'];
                $result[] = array(
                    "id" => $user['id'],
                    "parent" => '#',
                    "text" => $user['id']. "(昵称: " . $user['username']  . ", 投资额 : " . ($user['investment'] / 10000) . "万, 总业绩 : "  . ($user['achievements']/10000) . "万)"
                );
            } elseif ($user['referer'] && in_array($user['referer'], $ids)) {
                $ids[] = $user['id'];
                if ($id == $user['id']) {
                    $result[] = array(
                        "id" => $user['id'],
                        "parent" => (($user['referer'] == '#') || ($user['referer'] == 0)) ? '#' : $user['referer'],
                        'a_attr' => (($user['role_id'] == 2) ? array('class'=>"gray-icon") : array()),
                        "text" => $user['id']. "(昵称: " . $user['username']  . ", 投资额 : " . ($user['investment'] / 10000) . "万, 总业绩 : "  . ($user['achievements']/10000) . "万)" . (($user['role_id'] == 2) ? ' - 待审核' : ''),
                        "state" => array(
                            "opened" => true,
                            "selected" => true
                        )
                    );
                } else {
                    $result[] = array(
                        "id" => $user['id'],
                        "parent" => (($user['referer'] == '#') || ($user['referer'] == 0)) ? '#' : $user['referer'],
                        'a_attr' => (($user['role_id'] == 2 || $user['locked']) ? array('class'=>"gray-icon") : array()),
                        "text" => $user['id'] . "(昵称: " . $user['username']  . ", 投资额 : " . ($user['investment'] / 10000) . "万, 总业绩 : "  . ($user['achievements']/10000) . "万)" . (($user['role_id'] == 2) ? ' - 待审核' : ($user['locked'] ? ' - 已锁定' : ''))
                    );
                }
                $ids[] = $user['id'];
            }
        }


        return $this->render('admintree',array( 'data' => $result));
    }

    public function actionAdmintree()
    {

        $users=Yii::$app->db->createCommand('SELECT id,role_id,username,referer,investment,achievements,locked FROM user where role_id in (2,3)')->query();

        $result = array();

        $ids = array();

        $id = Yii::$app->getRequest()->get('id');

        foreach ($users as $user) {
            $referer = (($user['referer'] == '#') || ($user['referer'] == 0)) ? '#' : $user['referer'];

            if (($referer == '#')  || in_array($referer, $ids)) {
                if ($id == $user['id']) {
                    $result[] = array(
                        "id" => $user['id'],
                        "parent" => (($user['referer'] == '#') || ($user['referer'] == 0)) ? '#' : $user['referer'],
                        'a_attr' => (($user['role_id'] == 2) ? array('class'=>"gray-icon") : array()),
                        "text" => $user['id']. "(昵称: " . $user['username']  . ", 投资额 : " . ($user['investment'] / 10000) . "万, 总业绩 : "  . ($user['achievements']/10000) . "万)" . (($user['role_id'] == 2) ? ' - 待审核' : ''),
                        "state" => array(
                            "opened" => true,
                            "selected" => true
                        )
                    );
                } else {
                    $result[] = array(
                        "id" => $user['id'],
                        "parent" => (($user['referer'] == '#') || ($user['referer'] == 0)) ? '#' : $user['referer'],
                        'a_attr' => (($user['role_id'] == 2 || $user['locked']) ? array('class'=>"gray-icon") : array()),
                        "text" => $user['id'] . "(昵称: " . $user['username']  . ", 投资额 : " . ($user['investment'] / 10000) . "万, 总业绩 : "  . ($user['achievements']/10000) . "万)" . (($user['role_id'] == 2) ? ' - 待审核' : ($user['locked'] ? ' - 已锁定' : ''))
                    );
                }
                $ids[] = $user['id'];
            } else {

            }

        }
        $data = ($result);
        return $this->render('admintree',array( 'data' => $data));
    }

    /**
 * Updates an existing User model.
 * If update is successful, the browser will be redirected to the 'view' page.
 * @param integer $id
 * @return mixed
 */
    public function actionAdminupdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $user =  User::findOne($model->referer);
            if ($model->referer === '#' || ($user && $user->getId())) {
                if ($model->save()) {
                    return $this->redirect(['adminindexapprove', 'id' => $model->id]);
                }
            } else {
                $model->addError('referer', '推荐人的会员ID不正确, 请确认之后重新输入');
            }
        }
        return $this->render('adminupdate', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['adminindexunapprove', 'id' => $id]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function validateUserData(&$model){
        $validate = true;
        if ($model->isNewRecord) {
            $model->achievements = $model->investment * 10000;
        }

        if ($model->investment < 3){
            $validate = false;
            $model->addError('investment', '投资额不可以少于3W, 请重新输入');
        }

        $userNameUser = User::findByUsername($model->username);
        $id = Yii::$app->getRequest()->get('id');
        if ($userNameUser && $userNameUser->id && ((!$id || ($id != $userNameUser->id)))) {
            $validate = false;
            $model->addError('username', '此网络昵称已经被注册, 请重新输入');
        }

        $user =  User::findOne($model->referer);
        if ($model->referer !== '#' && (!$user || $user->locked)) {
            $validate = false;
            $model->addError('referer', '接点人的会员ID不正确, 请确认之后重新输入');
        } elseif ($model->referer !== '#') {
            $this->successInfo['referer'] = '接点人验证成功，网络昵称:' . $user->username;
        }

        $user =  User::findOne($model->suggest_by);
        if ($model->suggest_by !== '#' && (!$user || !$user->getId() || $user->locked)) {
            $validate = false;
            $model->addError('suggest_by', '推荐人的会员ID不正确, 请确认之后重新输入');
        } elseif ($model->suggest_by !== '#') {
            $this->successInfo['suggest_by'] = '推荐人验证成功，网络昵称:' . $user->username;
        }

        if ($model->useBaodan && $model->isNewRecord) {
            $model->duichong_invest = floatval($model->duichong_invest);
            if ($model->duichong_invest <=  0) {
                $validate = false;
                $model->addError('duichong_invest', '对冲帐户金额必须大于0');
            } else if ($model->duichong_invest > Yii::$app->user->identity->duichong_remain) {
                $validate = false;
                $model->addError('duichong_invest', '对冲帐户余额不足: ' .  Yii::$app->user->identity->duichong_remain);
            }
        } else {
            $model->duichong_invest = 0;
        }

        return $validate;
    }


    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if (Yii::$app->user->identity->add_member == 2) {
            if ($model->load(Yii::$app->request->post())) {
                $validate = $this->validateUserData($model);
                if ($validate && $model->save()) {
                    if ($model->useBaodan && $model->duichong_invest) {
                        Yii::$app->user->identity->duichong_remain -= $model->duichong_invest;
                        $data = array(
                            'user_id' => Yii::$app->user->identity->id,
                            'amount' => $model->duichong_invest,
                            'status' => 2,
                            'type' => 8,
                            'fee' => 0,
                            'total' => Yii::$app->user->identity->duichong_remain,
                            'note' => '报单:' . $model->id . ', 使用对冲帐户金额:' . $model->duichong_invest
                        );
                        $cash = new Cash();
                        $cash->setAttributes($data);
                        $cash->save();
                        Yii::$app->user->identity->save();
                    }
                    return $this->redirect(['success', 'id' => $model->id]);
                }
            }
        } else {
            Yii::$app->getSession()->set('message', '您没有报单权限，继续努力哦');
            $this->redirect(['news/index']);
            return;
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!System::loadConfig('open_suggest_list')) {
            Yii::$app->getSession()->set('danger', '会员推荐列表功能已关闭,请联系管理员.');
            return $this->redirect(['/news/index']);
        }
        $referer = Yii::$app->request->get('referer');

        $query = User::find()->where(['!=','role_id',1]);

        if (Yii::$app->user->identity->id) {
            $query->andWhere(['=','suggest_by',Yii::$app->user->identity->id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionBaodanindex()
    {
        $searchModel = new UserSearch();

        $data = Yii::$app->request->queryParams;

        $data['UserSearch']['added_by'] = Yii::$app->user->identity->id;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];

        return $this->render('baodanindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        if(($model->role_id == 3)  && ($model->merited == 1)) {
            $child = User::find()->where(['=', 'referer', $model->id])->andWhere(['=', 'role_id', 3])->count();
            $child += User::find()->where(['=', 'suggest_by', $model->id])->andWhere(['=', 'role_id', 3])->count();

            $invests = Investment::find()->where(['=', 'user_id', $model->id])->andWhere(['=', 'status', 1])->count();

            if ($child) {
                Yii::$app->getSession()->set('danger', '会员撤销失败,  该会员有下线, 请先撤销下线');
            }  else if ($invests) {
                Yii::$app->getSession()->set('danger', '会员撤销失败, 该会员有未撤销的追加投资,请先撤销追加投资');
            } else {
                $model->role_id = 4;
                $connection=Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();

                    $amount = $model->investment;
                    $model->reduceAchivement($amount);
                    $model->reduceMeritForNewMember($amount);
                    $model->reduceDuicong();

                    if ($model->save()) {
                        $transaction->commit();
                        Yii::$app->getSession()->set('big', '新会员撤销成功, 撤单后等级不自动变化，请核对等级');
                    } else {
                        throw new Exception('Failed to save user ' . json_encode($model->getErrors()));
                    }

                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                    Yii::$app->systemlog->add('Admin', '会员撤销', '失败', $id . ':' . $e->getMessage());
                    Yii::$app->getSession()->set('danger', '会员撤销失败, 请稍后再试. ' .  $e->getMessage());
                }
            }


        } else {
            $revenu = Revenue::find()->andFilterWhere(['like', 'note',  $model->id . '的报单奖励'])->one();
            if($revenu && $revenu->baodan) {

                $baodan_amount = $revenu->baodan;
                $user = User::findById($revenu->user_id);
                $user->baodan_total -= $baodan_amount;
                $user->baodan_remain -= $baodan_amount;

                $mallData = array(
                    'user_id' => $revenu->user_id,
                    'note' => '错误报单,撤销会员[' .$model->id . '],投资'.$user->investment.'保单费扣除:' . $revenu->id,
                    'amount' => $baodan_amount,
                    'type' => 6,
                    'status' => 2,
                    'total' => $user->baodan_remain
                );
                $mall = new Cash();
                $mall->load($mallData, '');
                $user->setScenario('cancel');
                if (!$mall->save()  || !$user->save(true, array('baodan_total', 'baodan_remain'))) {
                    throw new Exception('会员扣除失败 ' . User::arrayToString($user->getErrors()).User::arrayToString($mall->getErrors()));
                }
            }
            $model->reduceDuicong();
            $model->role_id = 4;
            $model->save();
            Yii::$app->getSession()->set('message', '新会员撤销成功');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $id = Yii::$app->user->identity->id;
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionChangepassword()
    {
        $model = $this->findModel(Yii::$app->user->identity->id);
        $success = false;

        $data = Yii::$app->request->post('User');
        if (count($data)) {
            $message = '';
            if (isset($data['password'])) {
                if ($model->validatePassword($data['password_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        $success = true;
                        $message = '一级密码修改成功';
                    }
                } else {
                    $model->addError('password_old', '原一级密码不正确');
                }
            } else if (isset($data['password2'])){
                if ($model->validatePassword2($data['password2_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        $message = '二级密码修改成功';
                        $success = true;
                    }
                } else {
                    $model->addError('password2_old', '原二级密码不正确');
                }
            }
        }

        if ($success) {
            Yii::$app->getSession()->set('message', $message);
            return $this->render('view', [
                'model' => $this->findModel($model->id),
            ]);
        } else {
            return $this->render('changepassword', [
                'model' => $model,
            ]);
        }

    }

    public function actionApplyaddmember()
    {
        $result = array(
            'status' => 'fail',
            'message' => ''
        );
        $user = $this->findModel(Yii::$app->user->identity->id);
        if ($user->level > 4) {
            $user->add_member = 1;
            if ($user->save() ) {
                $result['status']  = 'success';
                $result['message'] = '您的申请已提交, 我们将尽快进行审核';
            } else {
                $result['message']  = '您的申请没有提交成功, 请稍后再试, 如还有问题请跟我们的管理员联系';
            }
        } else {
            $result['message']  = '诶哟, 您的级别还不能申请报单员哦, 继续努力吧！';
        }

        return $this->render('applyaddmember', $result);
    }

    public function actionAdminchange()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $result= array ('status' => false, 'message' => '密码修改成功');

        $data = Yii::$app->request->post('User');
        if (count($data)) {
            if (isset($data['password'])) {
                if ($model->validatePassword($data['password_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        Yii::$app->getSession()->set('message', '密码修改成功');
                        $result['status'] = true;
                    } else {
                        Yii::$app->getSession()->set('message', '密码修改失败');
                        $result= array ('status' => false, 'message' => '密码修改失败');
                    }
                } else {
                    $result['message'] = '原密码有误, 请输入正确的原密码';
                }
            }
        } else {
            $result['status'] = null;
            unset($result['message']);
        }
        $result['model'] = $model;

        return $this->render('adminchange', $result);
    }

    public function actionSuccess($id)
    {
        $user = $this->findModel($id);
        return $this->render('success', ['model' => $user]);
    }

    public function actionHuobi()
    {
        $searchModel = new UserSearch();

        $data = Yii::$app->request->queryParams;

        $data['UserSearch']['role_id'] =  3;

        $dataProvider = $searchModel->search($data);

        $dataProvider->pagination = [
            'pageSize' => 20,
        ];

        return $this->render('huobiindex', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->added_by == Yii::$app->user->identity->id) {
            if ($model->load(Yii::$app->request->post())) {
                $user =  User::findOne($model->suggest_by);
                if ($model->suggest_by === '#' || ($user && $user->getId())) {
                    if ($model->save(true, array('suggest_by'))) {
                        return $this->redirect(['baodanindex']);
                    }
                } else {
                    $model->addError('suggest_by', '推荐人的会员ID不正确, 请确认之后重新输入');
                }
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        } else {
            Yii::$app->getSession()->set('message', '你没有权限修改该会员');
            return $this->redirect(['baodanindex']);
        }


    }
}
