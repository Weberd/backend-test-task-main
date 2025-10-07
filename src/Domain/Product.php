<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository\Domain;

final readonly class Product
{
    public function __construct(
        private string $uuid,
        private bool $isActive,
        private string $category,
        private string $serviceType,
        private string $description,
        private string $thumbnail,
        private float $price,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getServiceType(): string
    {
        return $this->serviceType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
