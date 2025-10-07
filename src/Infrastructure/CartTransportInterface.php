<?php

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;

interface CartTransportInterface
{
    public function get(string $key): Cart;
    public function set(string $key, Cart $value): void;
    public function has(string $key): bool;
}
