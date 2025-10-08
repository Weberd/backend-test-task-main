<?php

namespace Raketa\BackendTestTask\Repository;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Infrastructure\CartTransportInterface;

final class CartRepository
{
    public function __construct(
        private CartTransportInterface $transport
    ) {
    }

    public function get(string $key): Cart
    {
        return $this->transport->get($key);
    }

    public function set(Cart $value, string $key): void
    {
        $this->transport->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->transport->has($key);
    }
}
