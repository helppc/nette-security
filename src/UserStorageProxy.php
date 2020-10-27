<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity;

use Nette;
use Doctrine;

final class UserStorageProxy implements Nette\Security\IUserStorage
{
    private Nette\Security\IUserStorage $userStorage;

    private Doctrine\ORM\EntityManagerInterface $em;

    private ?Nette\Security\IIdentity $currentIdentity = null;

    public function __construct(Nette\Security\IUserStorage $userStorage, Doctrine\ORM\EntityManagerInterface $em)
    {
        $this->userStorage = $userStorage;
        $this->em = $em;
    }

    public function setNamespace(string $namespace): self
    {
        $this->userStorage->setNamespace($namespace);

        return $this;
    }

    public function getNamespace(): string
    {
        return $this->userStorage->getNamespace();
    }

    public function setAuthenticated($state): self
    {
        $this->userStorage->setAuthenticated($state);

        return $this;
    }

    public function isAuthenticated(): bool
    {
        # Get the identity as first. If the entity is not found then the user can't be authenticated.
        $this->getIdentity();

        return $this->userStorage->isAuthenticated();
    }

    public function setIdentity(?Nette\Security\IIdentity $identity = null): self
    {
        if (null !== $identity) {
            try {
                $metadata = $this->em->getMetadataFactory()->getMetadataFor(get_class($identity));

                $identity = new IdentityReference(
                    $metadata->getName(),
                    $metadata->getIdentifierValues($identity)
                );
            } catch (Doctrine\Common\Persistence\Mapping\MappingException $e) {
                # an empty catch block because we can't test if the MetadataFactory contains a metadata for identity's classname.
                # The classname can be a Doctrine Proxy and the method `MetadataFactory::hasMetadataFor()` doesn't convert Proxy's classname into real classname.
            }
        }

        $this->userStorage->setIdentity($identity);
        $this->currentIdentity = null;

        return $this;
    }

    public function getIdentity(): ?Nette\Security\IIdentity
    {
        if (null !== $this->currentIdentity) {
            return $this->currentIdentity;
        }

        $identity = $this->userStorage->getIdentity();

        if (!$identity instanceof IdentityReference) {
            return $identity;
        }

        $identity = $this->em->find($identity->getClassName(), $identity->getId());

        if (!$identity instanceof Nette\Security\IIdentity) {
            $identity = null;

            $this->setAuthenticated(false);
            $this->setIdentity($identity);
        }

        return $this->currentIdentity = $identity;
    }

    public function setExpiration($time, $flags = 0): self
    {
        $this->userStorage->setExpiration($time, $flags);

        return $this;
    }

    public function getLogoutReason(): ?int
    {
        return $this->userStorage->getLogoutReason();
    }
}