<?php

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use yii\db\Migration;

/**
 * Class m200518_072025_create_shop_elasticsearch_index
 */
class m200518_072025_create_shop_elasticsearch_index extends Migration
{
    public function safeUp()
    {
        $client = $this->_getClient();

        try {
            $client->indices()->delete([
                'index' => 'shop'
            ]);
        } catch (Missing404Exception $e) {}

        $client->indices()->create([
            'index' => 'shop',
            'body' => [
                'mappings' => [
                    'products' => [
                        '_source' => [
                            'enabled' => true,
                        ],
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                            ],
                            'name' => [
                                'type' => 'text',
                            ],
                            'description' => [
                                'type' => 'text',
                            ],
                            'price' => [
                                'type' => 'integer',
                            ],
                            'rating' => [
                                'type' => 'float',
                            ],
                            'brand' => [
                                'type' => 'integer',
                            ],
                            'categories' => [
                                'type' => 'integer',
                            ],
                            'tags' => [
                                'type' => 'integer',
                            ],
                            'values' => [
                                'type' => 'nested',
                                'properties' => [
                                    'characteristic' => [
                                        'type' => 'integer'
                                    ],
                                    'value_string' => [
                                        'type' => 'keyword',
                                    ],
                                    'value_int' => [
                                        'type' => 'integer',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function safeDown()
    {
        try {
            $this->_getClient()->indices()->delete([
                'index' => 'shop'
            ]);
        } catch (Missing404Exception $e) {}
    }

    private function _getClient(): Client
    {
        return Yii::$container->get(Client::class);
    }
}
