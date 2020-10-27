<?php declare(strict_types=1);

namespace HelpPC\NetteSecurity\Encoder;

interface EncoderInterface
{
    public function encodePassword(string $plainPassword, string $salt): string;

    public function isPasswordValid(string $password, string $rawPassword, string $salt): bool;

    public function needsRehash(string $password): bool;
}