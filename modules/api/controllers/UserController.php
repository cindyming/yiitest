<?php

namespace app\modules\api\controllers;

use app\models\InRecord;
use app\models\Log;
use app\models\Revenue;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Description of UserController
 *
 * @author TNChalise <teknarayanchalise@lftechnology.com>
 * @created_on : 18 Dec, 2014, 8:45:10 PM
 * @package 

 */
class UserController extends \yii\rest\Controller
{

    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'update' => ['post'],
                'exchange' => ['post'],
                'cash' => ['post'],
                'create' => ['post'],
                'baodan' => ['get']
            ],
        ];

        return $behaviors;

    }

    public function auth($username, $password) {
        // username, password are mandatory fields
        if(empty($username) || empty($password))
            return null;

        // get user using requested email
        $user = User::findOne([
            'username' => $username,
        ]);

        // if no record matching the requested user
        if(empty($user))
            return null;

        // validate password
        $isPass = $user->validatePassword($password);
        // if password validation fails
        if(!$isPass)
            return null;
        // if user validates (both user_email, user_password are valid)
        return $user;
    }

    public function actionBaodan()
    {
        $data = $_GET;
        $result = ['status' => 0, 'code' => '201', 'data' => $data, 'message' => 'Dont need to update'];

        if (isset($data['token']) && $data['token']) {
            $user = $this->findModel($data['token']);
            if ($user && $user->id) {
                if ($user->isBaodan()) {
                    $result = ['status' => 1, 'code' => 200 , 'data' => $user->id, 'message' => '验证成功'];
                } else {
                    $result['message'] = '报单员不存在';
                }
            } else {
                $result['message'] = '会员不存在';
            }
        } else {
            $result['message'] = 'Token不存在';
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }

    public function actionCash()
    {
        $data = Yii::$app->request->post();
        $result = ['status' => 0 , 'code' => '201', 'data' => $data, 'message' => 'Dont need to update'];

        if (isset($data['token']) && $data['token']) {
            $user = $this->findModel($data['token']);
            if ($user && $user->id) {
                if ($user->isBaodan()) {
                    $inRecord = Revenue::prepareIutRecordForCashTransfer($user->id, $data);
                    $user->duichong_total += $inRecord->duichong;
                    $user->duichong_remain += $inRecord->duichong;

                    $inRecord->total = $user->duichong_remain;
                    $connection = \Yii::$app->db;
                    try {
                        $transaction = $connection->beginTransaction();
                        if ($inRecord->save() && $user->save()) {
                            $transaction->commit();
                            $result = ['status' => 1, 'code' => 200 , 'data' => $inRecord->id, 'message' => '转账成功'];
                        } else {
                            $transaction->rollback();
                            Log::add($user->id, '撮合转入', '失败',
                                json_encode($inRecord->getErrors()) .
                                json_encode($user->getErrors())
                            );
                            $result['message'] = "DB error: ";
                        }
                    } catch (\Exception $e) {
                        $transaction->rollback();
                        Log::add($user->id, '撮合转入', '失败',
                            $e->getMessage()
                        );
                        $result['message'] = "Transfer ERROR: " . $e->getMessage();
                    }
                } else {
                    $result['code'] = 203;
                    $result['message'] = "USER IS NOT Baodan";
                }
            } else {
                $result['message'] = "USER IS NOT FOUND";
            }
        } else {
            $result['message'] = "MISS USER TOKEN";
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findIdentityByAccessToken($id)) !== null) {
            return $model;
        } else {

            $this->getHeader(400);
            echo json_encode(['status' => 0, 'error_code' => 400, 'message' => 'Bad request'], JSON_PRETTY_PRINT);
            exit;
            // throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function getHeader($status)
    {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Nintriva <nintriva.com>");
    }

    private function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = [
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        ];
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}
