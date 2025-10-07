<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\View\CartView;

final readonly class AddToCartController
{
    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager,
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $rawRequest = json_decode($request->getBody()->getContents(), true);
            $response = new JsonResponse();

            if ($rawRequest === null || !isset($rawRequest['productUuid'], $rawRequest['quantity'])) {
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(400, 'Invalid request payload');
            }

            if (!is_int($rawRequest['quantity']) || $rawRequest['quantity'] <= 0) {
                return $response
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withStatus(400, 'Quantity must be a positive integer');            
            }

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
