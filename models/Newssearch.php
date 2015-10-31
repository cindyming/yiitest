<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * Newssearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    /**S
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['be_top', 'title', 'content', 'created_at', 'updated_at', 'public_at'], 'safe'],
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
        $query = News::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($daterange = $this->public_at) {
            $dates = explode(' - ', $this->public_at);
            $query->andFilterWhere(['between', 'public_at', gmdate("Y-m-d h-i-s",strtotime(($dates[0] . ' 00:00:01'))), date("Y-m-d h-i-s",strtotime($dates[1] . ' 24:59:59'))]);
        }

        $query->andFilterWhere(['like', 'be_top', $this->be_top])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
