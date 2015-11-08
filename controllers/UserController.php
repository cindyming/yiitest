<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Revenue;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserSearch;
use app\components\AccessRule;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
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
                        'actions' => ['adminindex', 'admincreate', 'delete', 'adminreject','success','adminapplyindex','adminchange','adminapproveforaddmember', 'admintree', 'admintreelazy', 'adminindexapprove', 'adminindexunapprove', 'adminupdate', 'adminview', 'adminapprove'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'changepassword', 'create', 'success', 'view', 'applyaddmember'],
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
                    'bonus' => $meritAmount,
                    'total' => $meritAmount +  $addedBy->merit_remain
                );
                $merit = new Revenue();
                $merit->load($data, '');
                $merit->save();
                $addedBy->merit_remain += $meritAmount;
                $addedBy->merit_total += $meritAmount;
                $addedBy->save();
            }

            return $this->redirect(['adminindexapprove']);
        } else {
            return $this->redirect(['adminindexunapprove']);
        }
    }

    public function actionAdminreject($id)
    {
        $model = $this->findModel($id);
        $data = array('User' => array('role_id'=> 4));

        if ($model->load($data) && $model->save()) {

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

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdmincreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->isNewRecord) {
                $model->achievements = $model->investment * 10000;
            }
            $user =  User::findOne($model->referer);
            if ($model->investment < 5){
                $model->addError('investment', '投资额不可以少于5W,请重新输入');
            }
            if ($model->referer === '#' || ($user && $user->getId())) {
                if (($model->investment >= 5) && $model->save()) {
                    return $this->redirect(['success', 'id' => $model->id]);
                }
            } else {
                $model->addError('referer', '推荐人的会员ID不正确, 请确认之后重新输入');
            }
        }
        return $this->render('admincreate', [
            'model' => $model,
        ]);

    }


    public function actionAdmintree()
    {
        $users = User::find()->where(['=', 'role_id', 3])->orderBy(['id' => SORT_ASC])->all();

        $result = array();

        foreach ($users as $use) {
            $result[] = array(
                "id" => $use->id,
                "parent" => (($use->referer == '#') || ($use->referer == 0)) ? '#' : $use->referer,
                "text" => $use->id . "(昵称: " . $use->username  . ", 投资额 : " . $use->investment . ", 总业绩 : "  . $use->achievements . ")"
            );
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


    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->isNewRecord) {
                $model->achievements = $model->investment * 10000;
            }
            $user =  User::findOne($model->referer);
            if ($model->investment < 5){
                $model->addError('investment', '投资额不可以少于5W,请重新输入');
            }
            if ($model->referer === '#' || ($user && $user->getId())) {
                if (($model->investment >= 5) && $model->save()) {
                    return $this->redirect(['success', 'id' => $model->id]);
                }
            } else {
                $model->addError('referer', '推荐人的会员ID不正确, 请确认之后重新输入');
            }
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
        $referer = Yii::$app->request->get('referer');

        $query = User::find()->where(['!=','role_id',1]);

        if ($referer) {
            $query->andWhere(['=','referer',$referer]);
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
            if (isset($data['password'])) {
                if ($model->validatePassword($data['password_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        $success = true;
                    }
                } else {
                    $model->addError('password_old', '原一级密码不正确');
                }
            } else if (isset($data['password2'])){
                if ($model->validatePassword2($data['password2_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        $success = true;
                    }
                } else {
                    $model->addError('password_old', '原二级密码不正确');
                }
            }
        }

        if ($success) {
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
                        $result['status'] = true;
                    } else {
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

    public function actionSuccess()
    {
        return $this->render('success');
    }
}
