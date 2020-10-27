<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity;

use Nette;

class IdentityReference implements Nette\Security\IIdentity
{
    private string $className;
    private $id;

    /**
     * @param string $className
     * @param mixed $id
     */
    public function __construct(string $className, $id)
    {
        $this->className = $className;
        $this->id = $id;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return [];
    }
}