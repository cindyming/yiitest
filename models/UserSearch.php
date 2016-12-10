<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    public $dynTableName = '{{%user}}';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'offical', 'locked', 'role_id', 'referer', 'investment', 'add_member', 'level'], 'integer'],
            [['auth_key', 'access_token', 'username', 'password', 'password2', 'add_member', 'identity', 'phone','suggest_by', 'title', 'bank', 'cardname', 'cardnumber', 'bankaddress', 'email', 'qq', 'created_at', 'updated_at', 'approved_at'], 'safe'],
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
        $query = User::find()->where(['!=','role_id',1])->orderBy(['id' => SORT_DESC]);

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
            'offical' => $this->offical,
            'locked' => $this->locked,
            'role_id' => $this->role_id,
            'referer' => $this->referer,
            'suggest_by' => $this->suggest_by,
            'level' => $this->level,
            'investment' => $this->investment,
            'add_member' => $this->add_member,
        ]);

        if ($this->approved_at) {
            $date = explode(' - ', $this->approved_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', 'approved_at', $date[0]]);
                $query->andFilterWhere(['<=', 'approved_at', $date[1]]);
            }
        }

        $query->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'password2', $this->password2])
            ->andFilterWhere(['like', 'identity', $this->identity])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    public function suggestSearch($params)
    {
        $query = User::find()->where(['!=','role_id',1])
            ->orderBy(['id' => SORT_DESC]);

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
            'offical' => $this->offical,
            'locked' => $this->locked,
            'role_id' => $this->role_id,
            'referer' => $this->referer,
            'level' => $this->level,
            'investment' => $this->investment,
            'suggest_by' => $this->suggest_by,
            'approved_at' => $this->approved_at,
            'add_member' => $this->add_member,
        ]);

        $query->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'password2', $this->password2])
            ->andFilterWhere(['like', 'identity', $this->identity])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->andFilterWhere(['>', 'suggest_by', 0])
            ->orderBy(['id' => SORT_DESC]);


        return $dataProvider;
    }
}
