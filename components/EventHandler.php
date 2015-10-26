<?php

class EventHanlder
{
    public function checkLogin()
    {
         var_dump(get_class(Yii::$app->user));
    }
}