<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Exception\CartTransportException;
use Raketa\BackendTestTask\Infrastructure\CartTransportInterface;
use Redis;
use RedisException;

class CartTransport implements CartTransportInterface
{
    private Redis $redis;

    public function __construct(
        private string $host,
        private int $port,
        private string $password,
        private int $dbindex
    ) {
        $this->build();        
    }

    protected function build(): void
    {
        $redis = new Redis();

        try {
            $isConnected = $redis->isConnected();
            
            if (! $isConnected && $redis->ping('Pong')) {
                $isConnected = $redis->connect(
                    $this->host,
                    $this->port,
                );
            }
        } catch (RedisException $e) {
            $isConnected = false;
        }

        if ($isConnected) {
            $redis->auth($this->password);
            $redis->select($this->dbindex);
        }
    }

    /**
     * @throws CartTransportException
     */
    public function get(string $key): Cart
    {
        try {
            return unserialize($this->redis->get($key));
        } catch (RedisException $e) {
            throw new CartTransportException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws CartTransportException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->redis->setex($key, 24 * 60 * 60, serialize($value));
        } catch (RedisException $e) {
            throw new CartTransportException('Connector error', $e->getCode(), $e);
        }
    }

    public function has($key): bool
    {
        return $this->redis->exists($key);
    }
}
