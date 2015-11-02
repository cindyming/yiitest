<?php

namespace app\controllers;

class BlankController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
