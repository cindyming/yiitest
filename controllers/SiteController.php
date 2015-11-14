<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'logout', 'captcha'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' =>  [
                'class' => 'yii\captcha\CaptchaAction',
                'height' => 50,
                'width' => 80,
                'minLength' => 4,
                'maxLength' => 4
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect(array('/site/login'));
        }
        $this->userRedirect();
    }

    public function userRedirect()
    {
        if (Yii::$app->user->getIdentity()->isAdmin()) {
            Yii::$app->systemlog->add('管理员', '登录');
            $this->redirect(array('/news/adminindex'));
        } else {
            Yii::$app->systemlog->add('会员: ' . Yii::$app->user->identity->id, '登录');
            $this->redirect(array('/news/index'));
        }
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->userRedirect();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->userRedirect();
        }

        $this->layout = "login";
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
