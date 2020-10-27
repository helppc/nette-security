<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Encoder;

use HelpPC\NetteSecurity\Entity\UserInterface;

interface UserPasswordEncoderInterface
{

    public function encodePassword(UserInterface $user, string $plainPassword): string;

    public function isPasswordValid(UserInterface $user, string $raw): bool;

    public function needsRehash(UserInterface $user): bool;
}