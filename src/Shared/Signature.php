<?php

namespace Testcenter\Testcenter\Shared;

class Signature
{
    public static function get(string $string, string $secret)
    {
        $sig = hash_hmac('sha256', $string, $secret);
        return $sig;
    }

    public static function check(string $string, string $secret, string $signature)
    {
        $sig = hash_hmac('sha256', $string, $secret);
        return $sig === $signature;
    }
}
