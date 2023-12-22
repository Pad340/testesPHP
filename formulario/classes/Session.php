<?php

namespace classes;

class Session
{
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public function set(string $key, $value): Session
    {
        $_SESSION[$key] = (is_array($value) ? (object)$value : $value);
        return $this;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
}