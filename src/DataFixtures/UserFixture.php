<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const USER_1_NAME = 'Test User 1';
    public const USER_2_NAME = 'Test User 2';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $row) {
            $user = User::create($row['name'], $row['balance']);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'name' => self::USER_1_NAME,
                'balance' => 1000,
            ],
            [
                'name' => self::USER_2_NAME,
                'balance' => 900,
            ],
        ];
    }
}
