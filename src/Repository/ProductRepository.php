<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Repository;

use Doctrine\DBAL\Connection;
use Raketa\BackendTestTask\Repository\Entity\Product;
use Exception;

final class ProductRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /*
        @throws ProductNotFoundException
    */ 
    public function getByUuid(string $uuid): Product
    {
        $row = $this->connection->fetchAssociative(
            "SELECT uuid, is_active, category, service_type, description, thumbnail, price FROM products WHERE uuid = ?",
            [$uuid]
        );

        if (empty($row)) {
            throw new ProductNotFoundException($uuid);
        }

        return $this->make($row);
    }

    public function getByCategory(string $category): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT uuid, is_active, category, service_type, description, thumbnail, price FROM products WHERE is_active = 1 AND category = ?",
            [$category]
        );
       
        return array_map(
            fn (array $row): Product => $this->make($row),
            $rows
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['service_type'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
