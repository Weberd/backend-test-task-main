<?php

namespace Raketa\BackendTestTask\Exception;

use Exception;

class CartNotFoundException extends Exception
{
    public function __construct(string $parameter)
    {
        parent::__construct("Cart not found by parameter: $parameter");
    }
}
