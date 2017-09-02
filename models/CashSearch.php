<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cash;

/**
 * CashSearch represents the model behind the search form about `app\models\Cash`.
 */
class CashSearch extends Cash
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'status', 'amount'], 'integer'],
            [['bank', 'cardname', 'cardnumber', 'bankaddress', 'baodan_id', 'created_at', 'updated_at', 'cash_type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cash::find()->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'cash_type' => $this->cash_type,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'bank', $this->bank])
              ->andFilterWhere(['like', 'cardname', $this->cardname])
              ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
              ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
              ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    public function searchForMeber($params)
    {
        $query = Cash::find()->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => Yii::$app->user->identity->id,
            'amount' => $this->amount,
            'status' => 2,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    public function searchForMemberIndex($params)
    {
        $query = Cash::find()->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => Yii::$app->user->identity->id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cash_type' => $this->cash_type,
        ]);

        $query->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['in', 'type', array(1,2,3, 9)])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    public function searchForAdmininex($params)
    {
        $query = Cash::find()->where(['in', 'type', array(1,2,3, 9)])->orderBy(['status' => SORT_ASC, 'id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,
            'cash_type' => $this->cash_type,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', 'created_at', $date[0]]);
                $query->andFilterWhere(['<=', 'created_at', $date[1]]);
            }
        }

        $query->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->orderBy(['status' => SORT_ASC, 'id' => SORT_DESC]);

        return $dataProvider;
    }

    public function export($data) {
        $query = Cash::find()
            ->select(array(
                'id',
                'user_id',
                'cash_type',
                'stack_number',
                'baodan_id',
                'sc_account',
                'bank',
                'cardname',
                'cardnumber',
                'bankaddress',
                'type',
                'amount',
                'real_amount',
                'status',
                'created_at'
            ))
            ->orderBy(['created_at' => SORT_DESC]);

        $header = array(
            'id'   => '编号',
            'user_id' => '会员编号',
            'cash_type' => '提现方式',
            'stack_number' => '股票会员编号',
            'baodan_id' => '报单员编号',
            'sc_account' => '商城登录名',
            'bank' => '开户银行',
            'cardname' => '开户名',
            'cardnumber' => '银行卡号',
            'bankaddress' => '开户支行',
            'type'  => '出账账户',
            'amount' => '提现金额',
            'real_amount' => '实际金额',
            'status' => '状态',
            'created_at' => '日期',
        );


        $this->load($data);
        // grid filtering conditions
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'amount' => $this->amount,
            'cash_type' => $this->cash_type
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', 'created_at', $date[0]]);
                $query->andFilterWhere(['<=', 'created_at', $date[1]]);
            }
        }

        $query->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->orderBy(['id' => SORT_DESC]);

        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $data = array($header);
        foreach ($result as $row) {
            $row['status'] = isset($row['status']) ? $this->getStatus()[$row['status']] : '';
            $row['type'] = isset($row['type']) ? $this->getTypes()[$row['type']] : '';
            $row['bank'] =  (isset($row['bank']) && isset($this->getBankNames()[$row['bank']] )) ? $this->getBankNames()[$row['bank']] : '';
            $row['cash_type'] = \app\models\Cash::getCachType($row['cash_type']);
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'Cash.xls',
            'data' => $data
        ], 'member');
    }
}
