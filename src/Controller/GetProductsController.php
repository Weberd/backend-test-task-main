<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\View\ProductsView;
use Raketa\BackendTestTask\Exception\ProductNotFoundException;
use Exception;

final readonly class GetProductsController
{
    public function __construct(
        private ProductsView $productsVew
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        try {
            $response = new JsonResponse();
            $rawRequest = json_decode($request->getBody()->getContents(), true);

            $response->getBody()->write(
                json_encode(
                    $this->productsVew->toArray($rawRequest['category']),
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
        } catch (Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500, 'Internal server error');   
        }
    }
}
