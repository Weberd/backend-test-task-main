<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\View\ProductsView;

final readonly class GetProductsController
{
    public function __construct(
        private ProductsView $productsVew
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse();

        $rawRequest = json_decode($request->getBody()->getContents(), true);

            if ($rawRequest === null || !isset($rawRequest['category'])) {
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(400, 'Missing category parameter');            
            }

        $response->getBody()->write(
            json_encode(
                $this->productsVew->toArray($rawRequest['category']),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
