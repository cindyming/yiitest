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
            [['note', 'created_at', 'updated_at'], 'safe'],
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
            'merited' => $this->merited
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
}
