<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Encoder;

use HelpPC\NetteSecurity\Entity\UserInterface;

class UserPasswordEncoder implements UserPasswordEncoderInterface
{
    private EncoderInterface $encoder;

    public function __construct(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encodePassword(UserInterface $user, string $plainPassword): string
    {
        return $this->encoder->encodePassword($plainPassword, $user->getSalt());
    }

    public function isPasswordValid(UserInterface $user, string $raw): bool
    {
        if (null === $user->getPassword()) {
            return false;
        }

        return $this->encoder->isPasswordValid($user->getPassword(), $raw, $user->getSalt());
    }

    public function needsRehash(UserInterface $user): bool
    {
        if (null === $user->getPassword()) {
            return false;
        }

        return $this->encoder->needsRehash($user->getPassword());
    }
}