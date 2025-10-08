<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Exception\CartTransportException;
use Raketa\BackendTestTask\Exception\CartNotFoundException;
use Raketa\BackendTestTask\Infrastructure\CartTransportInterface;
use Redis;
use RedisException;

class CartTransport implements CartTransportInterface
{
    private Redis $redis;
    private bool $isConnected = false;

    public function __construct(
        private string $host,
        private int $port,
        private string $password,
        private int $dbindex
    ) {        
    }

    public function __destruct()
    {
        if ($this->isConnected && isset($this->redis)) {
            $this->redis->close();
        }
    }

    protected function build(): void
    {
        if ($this->isConnected) {
            return;
        }

        $redis = new Redis();
        
        if (!$redis->connect($this->host, $this->port)) {
            throw new CartTransportException('Failed to connect to Redis');
        }
        
        if (!$redis->auth($this->password)) {
            $redis->close();
            throw new CartTransportException('Redis authentication failed');
        }
        
        if (!$redis->select($this->dbindex)) {
            $redis->close();
            throw new CartTransportException('Redis select failed');
        }

        $this->redis = $redis;
        $this->isConnected = true;
    }

    /**
     * @throws CartTransportException
     */
    public function get(string $key): Cart
    {
        try {
            $this->build();
            $data = $this->redis->get($key);

            if ($data === false) {
                throw new CartNotFoundException("Cart not found for key: {$key}");
            }

            $cart = unserialize($data, ['allowed_classes' => [Cart::class]]);

            if ($cart === false || !$cart instanceof Cart) {
                throw new CartTransportException("Invalid cart data for key: {$key}");
            }

            return $cart;
        } catch (RedisException $e) {
            throw new CartTransportException('Get error', $e->getCode(), $e);
        }
    }

    /**
     * @throws CartTransportException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->build();
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new CartTransportException('Set error', $e->getCode(), $e);
        }
    }

    public function has(string $key): bool
    {
        try {
            $this->build();
            return (bool)$this->redis->exists($key);
        } catch (RedisException $e) {
            throw new CartTransportException('Has error', $e->getCode(), $e);
        }
    }
}
