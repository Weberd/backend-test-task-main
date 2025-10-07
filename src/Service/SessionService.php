<?php

namespace Raketa\BackendTestTask\Service;

final class SessionService
{
    public static function getId(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }
}
