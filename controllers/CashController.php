<?php

namespace app\controllers;

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
                        'actions' => ['adminindex', 'adminreject', 'adminapprove', 'adminupdate',  'adminview', 'adminout'],
                        'roles' => [User::ROLE_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

    /**
     * Lists all Cash models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashSearch();

        $data = Yii::$app->request->queryParams;

        $data['CashSearch']['user_id'] = Yii::$app->user->identity->id;

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
        $model = new Cash();

        $data = Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post()) && isset($data['Cash']) && isset($data['Cash']['password2'])) {
            $validateAmount = true;
            if ($model->type == 1) {
                $compareAmount = Yii::$app->user->identity->bonus_remain;
            } else {
                $compareAmount = Yii::$app->user->identity->merit_remain;
            }

            if ($model->amount > $compareAmount) {
                $validateAmount = false;
                $model->addError('amount', '可供提现的约不足, 请确认后重新输入. 分红余额: ' . Yii::$app->user->identity->bonus_remain . ', 绩效余额: ' . Yii::$app->user->identity->merit_remain . '.');
            }
            if (Yii::$app->user->identity->validatePassword2($data['Cash']['password2'])) {
                if ($validateAmount && $model->save()) {
                    return $this->redirect(['index']);
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } else {
                $model->addError('password2', '二级密码不正确, 请输入正确的二级密码');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionAdminapprove($id)
    {
        $model = $this->findModel($id);

        $connection=Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();

            $model->status = 2;
            $model->save();
            $user = User::findById($model->user_id);
            if ($model->type == 1) {
                $user->bonus_remain = $user->bonus_remain - $model->amount;
            } else {
                $user->merit_remain = $user->merit_remain - $model->amount;
            }
            $user->save();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();//回滚函数
        }


        return $this->redirect(['adminindex', 'id' => $model->id]);

    }


    public function actionAdminreject($id)
    {
        $model = $this->findModel($id);
        $model->status = 3;
        $model->save();

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
}
