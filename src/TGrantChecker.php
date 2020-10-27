<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity;

use Nette\Http\IResponse;
use Nette\Security\User;

/**
 * @method User getUser()
 * @method error(string $message, int $responseCode)
 */
trait TGrantChecker
{

    private function isGranted(string $role): bool
    {
        return $this->getUser()->isInRole($role);
    }

    protected function isAllowed(string $resource, string $operation): bool
    {
        return $this->getUser()->isAllowed($resource, $operation);
    }

    protected function denyAccessUnlessGranted(string $role, string $message = 'Access Denied.'): void
    {
        if (!$this->isGranted($role)) {
            $this->error($message, IResponse::S403_FORBIDDEN);
        }
    }

    protected function denyAccessUnlessAllowed(string $resource, string $operation, string $message = 'Access Denied.'): void
    {
        if (!$this->isAllowed($resource, $operation)) {
            $this->error($message, IResponse::S403_FORBIDDEN);
        }
    }
}