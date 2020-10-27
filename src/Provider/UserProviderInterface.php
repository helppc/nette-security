<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Provider;

use HelpPC\NetteSecurity\Entity\UserInterface;

interface UserProviderInterface
{
    public function getByEmail(string $email): UserInterface;

    public function getById($id): UserInterface;

}