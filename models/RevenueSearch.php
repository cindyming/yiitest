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
            [['id', 'user_id', 'approved', 'bonus', 'merit', 'baodan'], 'integer'],
            [['note', 'created_at', 'updated_at','type', 'account_type', 'duichong'], 'safe'],
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

        if (is_array($this->type)) {
            $query->andFilterWhere(['in', 'type', $this->type]);
        } else {
            $query->andFilterWhere(['type' => $this->type]);
        }

        if ($this->account_type) {
            if ($this->account_type == 1) {
                $query->andFilterWhere(['>', 'bonus', 0]);
            } else if ($this->account_type == 2) {
                $query->andFilterWhere(['>', 'merit', 0]);
            } else if ($this->account_type == 3) {
                $query->andFilterWhere(['>', 'baodan', 0]);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchForCaiwu($params)
    {
        $query = Revenue::find()->where(['=', 'type', 1])->orderBy(['id' => SORT_DESC]);

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
            'type' => $this->type,
            'merit' => $this->merit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        if ($this->account_type) {
            if ($this->account_type == 1) {
                $query->andFilterWhere(['>', 'bonus', 0]);
            } else if ($this->account_type == 2) {
                $query->andFilterWhere(['>', 'merit', 0]);
            } else if ($this->account_type == 3) {
                $query->andFilterWhere(['>', 'baodan', 0]);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }

    public function searchForHuobi($params)
    {
        $query = Revenue::find()->where(['in', 'type', array(2, 3)])->orderBy(['id' => SORT_DESC]);

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
            'type' => $this->type,
            'merit' => $this->merit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        if ($this->account_type) {
            if ($this->account_type == 1) {
                $query->andFilterWhere(['>', 'bonus', 0]);
            } else if ($this->account_type == 2) {
                $query->andFilterWhere(['>', 'merit', 0]);
            } else if ($this->account_type == 3) {
                $query->andFilterWhere(['>', 'baodan', 0]);
            } else if ($this->account_type == 4) {
                $query->andFilterWhere(['>', 'mall', 0]);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note]);

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
        $query = Revenue::find()->select(['COUNT(*) AS cnt', 'SUM(bonus) as bonus_total', 'SUM(merit) as merit_total','sum(baodan) as baodan_total', 'id', 'user_id']);

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
        $query->groupBy(['user_id'])->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
