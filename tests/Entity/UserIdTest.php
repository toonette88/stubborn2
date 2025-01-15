<?php

namespace App\Tests\Entity;

use App\Entity\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testGetId()
    {
        // Créez une instance de UserId
        $userId = new UserId();

        // Vérifiez que l'ID est initialisé à null (car il n'a pas encore été persistant)
        $this->assertNull($userId->getId());
    }
}
