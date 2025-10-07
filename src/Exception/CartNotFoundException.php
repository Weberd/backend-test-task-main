<?php

namespace Raketa\BackendTestTask\Exception;

use Exception;

class CartNotFoundException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
