<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Entity;

use Nette\Security\IIdentity;

interface UserInterface extends IIdentity
{

    public function getPassword(): string;

    public function setPassword(string $password): UserInterface;

    public function getEmail(): string;

    public function getSalt(): string;

    public function isEnabled(): bool;

    /**
     * @return string[]
     */
    public function getRoles(): array;

}