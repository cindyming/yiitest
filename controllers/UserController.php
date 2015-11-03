<?php

namespace app\controllers;

use Yii;
use app\models\User;
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
                        'actions' => ['adminindex', 'admincreate', 'adminindexapprove', 'adminindexunapprove', 'adminupdate', 'adminview', 'adminapprove'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'changepassword', 'create', 'delete'],
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
            return $this->redirect(['adminindexapprove']);
        } else {var_dump($model->getErrors());die;
            return $this->redirect(['adminindexunapprove']);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAutologin($id)
    {
        $model = $this->findModel($id);

        Yii::$app->user->login($model, 3600*24*30);

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['adminindexunapprove', 'id' => $model->id]);
        } else {
            return $this->render('admincreate', [
                'model' => $model,
            ]);
        }
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['adminindex', 'id' => $model->id]);
        } else {
            return $this->render('adminupdate', [
                'model' => $model,
            ]);
        }
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

        return $this->redirect(['index']);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
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
                }
            } else if (isset($data['password2'])){
                if ($model->validatePassword2($data['password2_old'])) {
                    $model->setAttributes($data);
                    if ($model->save()) {
                        $success = true;
                    }
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
}
