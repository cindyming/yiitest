<?php

namespace app\controllers;

use Yii;
use app\models\Investment;
use app\models\InvestmentSearch;
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
                        'actions' => ['adminindex', 'cancel', 'admincreate', 'admindelete', 'adminupdate',  'adminview'],
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
            $validateAmount = ($model->amount >= 1);
            if (!$validateAmount){
                $model->addError('amount', '投资额必须大于1W,并且为万的倍数,例如:10000,100000.请重新输入');
            }
            if (($user && $user->getId()) && ($user->role_id == 3)) {
                if ($validateAmount && $model->save()) {
                    Yii::$app->getSession()->set('message', '追加投资添加成功');
                    return $this->redirect(['adminindex']);
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
        $model = $this->findModel($id);
        if($model->status) {
            $model->status = 0;
            $model->save();
            if ($model->merited) {
                $connection=Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();

                    $amount = $model->amount;
                    $user = User::findById($model->user_id);
                    $user->reduceAchivement($amount);
                    $user->reduceMerit($model);
                    $transaction->commit();

                    Yii::$app->getSession()->set('message', '追加投资撤销成功');
                } catch (Exception $e) {
                    $transaction->rollback();//回滚函数
                    Yii::$app->log($e->getMessage());
                    $model->status = 1;
                    $model->save();
                    Yii::$app->getSession()->set('danger', '追加投资撤销失败, 请稍后再试');
                }
            }
        }

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
