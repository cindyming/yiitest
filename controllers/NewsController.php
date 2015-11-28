<?php

namespace app\controllers;

use Yii;
use app\models\News;
use app\models\NewsSearch;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\User;
use app\components\AccessRule;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
                        'actions' => ['adminindex', 'admincreate', 'admindelete', 'adminupdate',  'adminview'],
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionAdminindex()
    {
        $searchModel = new NewsSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 5,
        ];

        return $this->render('adminindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionAdminview($id)
    {
        return $this->render('adminview', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdmincreate()
    {
        $model = new News();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->set('message', '公告发布成功');
            return $this->redirect(['adminview', 'id' => $model->id]);
        } else {
            return $this->render('admincreate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAdminupdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['adminview', 'id' => $model->id]);
        } else {
            return $this->render('adminupdate', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAdmindelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['adminindex']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionIndex()
    {
      //  $searchModel = new Newssearch();

        $dataProvider = new ActiveDataProvider([
            'query' => News::find()->orderBy(['be_top' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

    //    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
           // 'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
}
