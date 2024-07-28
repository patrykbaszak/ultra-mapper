<?php

declare(strict_types=1);

namespace Tests\Support\UseCases\TransformPersistenceIntoArray\Entity;

class Person
{
    public int $id;
    public string $name;
    public ?Sex $sex;
    public string $email;
    public Address $address;
    public \DateTimeImmutable $birthDate;
    public ?string $phone;

    public static function createRandomStub(): self
    {
        $person = new self();
        $person->id = self::randomId();
        $person->name = self::randomName();
        $person->sex = self::randomSex();
        $person->email = self::randomEmail();
        $person->address = Address::createRandomStub();
        $person->birthDate = self::randomBirthDate();
        $person->phone = self::randomPhone();

        return $person;
    }

    private static function randomId(): int
    {
        return random_int(10000, 1000000);
    }

    private static function randomName(): string
    {
        $names = [
            'John Doe',
            'Jane Doe',
            'Alice',
            'Bob',
            'Charlie',
            'George R. R. Martin',
        ];

        return $names[array_rand($names)];
    }

    private static function randomSex(): ?string
    {
        $sexes = [
            null,
            Sex::M,
            Sex::F,
            Sex::U,
        ];

        return $sexes[array_rand($sexes)];
    }

    private static function randomEmail(): string
    {
        $domains = [
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'outlook.com',
            'protonmail.com',
        ];

        $names = [
            'john.doe',
            'jane.doe',
            'alice',
            'bob',
            'charlie',
            'george+martin',
        ];

        return sprintf(
            '%s@%s',
            $names[array_rand($names)],
            $domains[array_rand($domains)]
        );
    }

    private static function randomBirthDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(sprintf(
            '-%d years',
            random_int(0, 120)
        ));
    }

    private static function randomPhone(): ?string
    {
        $phones = [
            null,
            '+1 123 456 789',
            '+48123456789',
            '+49 123 456 789',
            '123 456 789',
            '123456789',
            '0039 123 456 789',
            '0044123456789',
        ];

        return $phones[array_rand($phones)];
    }
}
