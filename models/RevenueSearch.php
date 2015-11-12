<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Revenue;

/**
 * RevenueSearch represents the model behind the search form about `app\models\Revenue`.
 */
class RevenueSearch extends Revenue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'approved', 'bonus', 'merit'], 'integer'],
            [['note', 'created_at', 'updated_at', 'type'], 'safe'],
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
        $query = Revenue::find()->orderBy(['id' => SORT_DESC]);

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
            'approved' => $this->approved,
            'bonus' => $this->bonus,
            'merit' => $this->merit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $column = '';
        if ($this->type == 1) {
            $column = 'bonus';
        } elseif ($this->type == 2) {
            $column = 'merit';
        }
        if ($column) {
            $query->andFilterWhere(['>', $column, 0]);
        }

        $query->andFilterWhere(['like', 'note', $this->note])
              ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchTotal($params)
    {
        $query = Revenue::find()->select(['COUNT(*) AS cnt', 'SUM(bonus) as bonus_total', 'SUM(merit) as merit_total', 'id', 'user_id']);

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
            'approved' => $this->approved,
            'bonus' => $this->bonus,
            'merit' => $this->merit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);
        $query->groupBy(['user_id']);

        return $dataProvider;
    }
}
