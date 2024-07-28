<?php

declare(strict_types=1);

namespace PBaszak\UltraMapper\Tests\Assets;

class DataSets
{
    public static function dummyAsArray(): array
    {
        return [
            'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
            'name' => 'test',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
            '_embedded' => [
                'page' => 1,
                'pageSize' => 10,
                'total' => 2,
                'items' => [
                    [
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'CHF',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => [
                            'test' => 'test',
                            'test2' => 52324.43,
                        ],
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ],
                    [
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'EUR',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => [
                            'test' => 'test',
                            'test2' => 52324.43,
                        ],
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function dummyAsAnonymousObject(): object
    {
        return (object) [
            'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
            'name' => 'test',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
            '_embedded' => (object) [
                'page' => 1,
                'pageSize' => 10,
                'total' => 2,
                'items' => [
                    (object) [
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'CHF',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => (object) [
                            'test' => 'test',
                            'test2' => 52324.43,
                        ],
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ],
                    (object) [
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'EUR',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => (object) [
                            'test' => 'test',
                            'test2' => 52324.43,
                        ],
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function dummyAsClassObject(): Dummy
    {
        return new Dummy(...[
            'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
            'name' => 'test',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
            '_embedded' => new EmbeddedDTO(...[
                'page' => 1,
                'pageSize' => 10,
                'total' => 2,
                'items' => [
                    new ItemDTO(...[
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'CHF',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => new MetadataDTO(...[
                            'test' => 'test',
                            'test2' => 52324.43,
                        ]),
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ]),
                    new ItemDTO(...[
                        'id' => 'e2a85ae5-490b-4747-abc1-6efa3352a587',
                        'name' => 'test',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec velit vitae arcu aliquam tincidunt.',
                        'price' => 100.0,
                        'currency' => 'EUR',
                        'quantity' => 1,
                        'type' => 'PHYSICAL',
                        'category' => 'FOOD',
                        'vat' => 21,
                        'metadata' => new MetadataDTO(...[
                            'test' => 'test',
                            'test2' => 52324.43,
                        ]),
                        'created_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'updated_at' => new \DateTime('2018-01-01T12:00:00+00:00'),
                        'availableActions' => [
                            'update',
                            'delete',
                        ],
                    ]),
                ],
            ]),
        ]);
    }
}
