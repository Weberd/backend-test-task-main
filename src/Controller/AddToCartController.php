<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\Repository\ProductRepository;
use Raketa\BackendTestTask\View\CartView;

final readonly class AddToCartController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CartView $cartView,
        private CartManager $cartManager,
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
        $cart = $this->cartManager->addToCart($rawRequest['productUuid'], $rawRequest['quantity']);

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                [
                    'status' => 'success',
                    'cart' => $this->cartView->toArray($cart)
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }
}
