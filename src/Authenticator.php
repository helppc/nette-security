<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity;

use HelpPC\NetteSecurity\Encoder\UserPasswordEncoderInterface;
use HelpPC\NetteSecurity\Provider\UserProviderInterface;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;

class Authenticator implements IAuthenticator
{
    private UserProviderInterface $userProvider;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserProviderInterface $userProvider, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
    }

    function authenticate(array $credentials): IIdentity
    {
        [$email, $password] = $credentials;
        $user = $this->userProvider->getByEmail($email);

        if (!$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new AuthenticationException();
        }

        if (!$user->isEnabled()) {
            throw new AuthenticationException();
        }

        if ($this->passwordEncoder->needsRehash($user)) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $password)
            );
        }

        return $user;
    }

}