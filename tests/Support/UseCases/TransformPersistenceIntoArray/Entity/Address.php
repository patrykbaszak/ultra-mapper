<?php

declare(strict_types=1);

namespace Tests\Support\UseCases\TransformPersistenceIntoArray\Entity;

class Address
{
    public string $street;
    public string $zip;
    public string $city;
    public string $state;
    public Country $country;

    public static function createRandomStub(): self
    {
        $address = new self();
        $address->street = self::randomStreet();
        $address->zip = self::randomZip();
        $address->city = self::randomCity();
        $address->state = self::randomState();
        $address->country = self::randomCountry();

        return $address;
    }

    private static function randomStreet(): string
    {
        $streets = [
            'First avenue 123/4',
            'Second st 12',
            'Third street 1A',
            'Fourth avenue 34B',
            'Fifth st 9/11',
        ];

        return $streets[array_rand($streets)];
    }

    private static function randomZip(): string
    {
        $formats = ['%d%d', '%d-%d'];

        return sprintf(
            $formats[array_rand($formats)],
            random_int(0, 99),
            random_int(0, 999)
        );
    }

    private static function randomCity(): string
    {
        $cities = [
            'New York',
            'Los Angeles',
            'Chicago',
            'Houston',
            'Phoenix',
        ];

        return $cities[array_rand($cities)];
    }

    private static function randomState(): string
    {
        $states = [
            'NY',
            'DOL',
            'New York',
            'California',
        ];

        return $states[array_rand($states)];
    }

    private static function randomCountry(): Country
    {
        $countries = [
            Country::PL,
            Country::DE,
            Country::FR,
            Country::ES,
            Country::IT,
            Country::UK,
        ];

        return $countries[array_rand($countries)];
    }
}
