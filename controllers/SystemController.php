<?php

namespace app\controllers;

class SystemController extends \yii\web\Controller
{
    public function actionBackup()
    {
        shell_exec('sh /home/backup/backup.sh');
    }

    public function actionConfig()
    {
        return $this->render('config');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
