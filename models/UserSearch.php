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
        $query = User::find()->where(['!=','role_id',1])->orderBy(['created_at' => SORT_DESC]);

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
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }

    public function suggestSearch($params)
    {
        $query = User::find()->where(['!=','role_id',1])
            ->orderBy(['created_at' => SORT_DESC]);

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
            ->orderBy(['created_at' => SORT_DESC]);


        return $dataProvider;
    }


    public function export($data) {
        $query = User::find()
            ->select(array(
                'id',
                'username',
                'level',
                'add_member',
                'init_investment',
                'investment',
                'referer',
                'phone',
                'identity',
                'approved_at',
                'locked'
            ))
            ->orderBy(['created_at' => SORT_DESC]);


        $this->load($data);
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'offical' => $this->offical,
            'locked' => $this->locked,
            'role_id' => $this->role_id,
            'referer' => $this->referer,
            'added_by' => $this->added_by,
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

        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'id' => '会员编号',
            'username' => '用户名',
            'level' => '等级',
            'add_member' => '是否为报单员',
            'init_investment' => '初始投资',
            'investment' => '总投资额',
            'referer' => '接点人',
            'phone' => '电话号码',
            'identity' => '身份证号',
            'approved_at' => '审核通过时间',
            'locked' => '是否锁定'
        );

        $data = array($header);
        foreach ($result as $row) {
            $row['level'] = $row['level'] ? $this->getLevelOptions()[$row['level']] : '';
            $row['add_member'] =  $row['add_member'] == 2 ? '是' : '否';
            $row['locked'] = $this->getLockedOptions()[$row['locked']];
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'Users.xls',
            'data' => $data
        ], 'member');
    }
}
