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
            [['bank', 'cardname', 'cardnumber', 'bankaddress', 'created_at', 'update_at'], 'safe'],
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
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'bank', $this->bank])
              ->andFilterWhere(['like', 'cardname', $this->cardname])
              ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
              ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
              ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
