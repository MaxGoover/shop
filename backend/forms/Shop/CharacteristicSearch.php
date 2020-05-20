<?php

namespace backend\forms\Shop;

use shop\entities\Shop\Characteristic;
use shop\helpers\CharacteristicHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CharacteristicSearch extends Model
{
    public $id;
    public $name;
    public $type;
    public $required;

    public function requiredList(): array
    {
        return [
            1 => \Yii::$app->formatter->asBoolean(true),
            0 => \Yii::$app->formatter->asBoolean(false),
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Characteristic::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'required' => $this->required,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function typesList(): array
    {
        return CharacteristicHelper::typeList();
    }

    ##################################################

    public function rules(): array
    {
        return [
            [['id', 'type', 'required'], 'integer'],
            [['name'], 'safe'],
        ];
    }
}
