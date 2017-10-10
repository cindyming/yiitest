<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Investment;

/**
 * InvestmentSearch represents the model behind the search form about `app\models\Investment`.
 */
class InvestmentSearch extends Investment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'merited'], 'integer'],
            [['amount'], 'number'],
            [['note', 'created_at', 'updated_at', 'status'], 'safe'],
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
        $query = Investment::find()->orderBy(['id' => SORT_DESC]);

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
            'amount' => $this->amount,
            'merited' => $this->merited,
            'status' => $this->status,
        ]);


        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', 'created_at', $date[0]]);
                $query->andFilterWhere(['<=', 'created_at', $date[1]]);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note])
              ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
    public function export($data) {
        $query = Investment::find()
            ->select(array(
                'user_id', 'amount', 'status', 'created_at', 'note'
            ))
            ->orderBy(['created_at' => SORT_DESC]);


        $this->load($data);
        if ($this->user_id) {
            $query->andFilterWhere(['like', 'user_id', $this->user_id]);
        }

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', 'created_at', $date[0]]);
                $query->andFilterWhere(['<=', 'created_at', $date[1]]);
            }
        }

        $sql = ($query->createCommand()->getRawSql()) . ' LIMIT 5000';

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'user_id' => '会员编号',
            'amount' => '追加金额',
            'status' => '状态',
            'created_at' => '追加时间',
            'note' => '备注'
        );

        $data = array($header);
        foreach ($result as $row) {
            $row['status'] = ($row['status']) ? '正常' : '撤销';
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'Investment.xls',
            'data' => $data
        ], 'member');
    }
}
