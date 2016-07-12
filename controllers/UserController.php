<?php

namespace app\controllers;

use app\models\Investment;
use Yii;
use app\models\User;
use yii\base\Exception;
use yii\widgets\ActiveForm;
use app\models\Revenue;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserSearch;
use app\components\AccessRule;
use yii\web\Response;

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
                        'actions' => ['adminindex', 'cancel','validate', 'huobi', 'admincreate', 'suggestindex', 'adminreject','success','adminresetpassword','adminapplyindex','adminchange','adminapproveforaddmember', 'admintree', 'admintreelazy', 'adminindexapprove', 'adminindexunapprove', 'adminupdate', 'adminview', 'adminapprove'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'changepassword', 'validate', 'create', 'success', 'view', 'applyaddmember', 'tree'],
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

        if ($model->load($data) && $model->save()) {
            $addedBy = User::findOne($model->added_by);
            if ($addedBy && $addedBy->getId() && ($addedBy->role_id == 3)) {
                $meritAmount = round($model->investment * 0.01, 2);
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

    public function actionValidate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdmincreate()
    {
        $model = new User();

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
        $id = Yii::$app->user->identity->id;

        $user = $this->findModel($id);

        $users=Yii::$app->db->createCommand("SELECT id,role_id,username,referer,investment,achievements,locked FROM user where role_id in (2,3) AND created_at>='" . $user->created_at . "'  ORDER by created_at ASC")->query();

        $result = array();

        $ids = array();

        $id = Yii::$app->user->identity->id;

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
            }
        }


        return $this->render('admintree',array( 'data' => $result));
    }

    public function actionAdmintree()
    {

        $id = Yii::$app->getRequest()->get('id');

        $users=Yii::$app->db->createCommand("SELECT id,role_id,username,referer,investment,achievements,locked FROM user where role_id in (2,3) order by created_at ASC")->query();

        $result = array();

        $ids = array();

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
            $validate = $this->validateUserData($model);
            if ($validate && $model->save()) {
                return $this->redirect(['adminindexapprove', 'id' => $model->id]);
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
        if ($model->referer !== '#' && (!$user  || $user->locked)) {
            $validate = false;
            $model->addError('referer', '接点人的会员ID不正确, 请确认之后重新输入');
        } elseif ($model->referer !== '#') {
            $this->successInfo['suggest_by'] = '接点人验证成功，网络昵称:' . $user->username;
        }

        $user =  User::findOne($model->suggest_by);
        if (($model->suggest_by !== '#' && (!$user  || $user->locked))) {
            $validate = false;
            $model->addError('suggest_by', '推荐人的会员ID不正确, 请确认之后重新输入');
        }  elseif ($model->suggest_by !== '#') {
            $this->successInfo['suggest_by'] = '推荐人验证成功，网络昵称:' . $user->username;
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

    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        if(($model->role_id == 3)  && ($model->merited == 1)) {
            $child = User::find()->where(['=', 'referer', $model->id])->andWhere(['=', 'role_id', 3])->count();

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

                    if ($model->save()) {
                        $transaction->commit();
                        Yii::$app->getSession()->set('message', '会员撤销成功');
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
            $model->role_id = 4;
            $model->save();
            Yii::$app->getSession()->set('message', '新会员撤销成功');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $referer = Yii::$app->request->get('referer');

        $query = User::find()->where(['!=','role_id',1]);

        if ($referer) {
            $query->andWhere(['=','suggest_by',$referer]);
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
     * Displays a single User model.
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
}
