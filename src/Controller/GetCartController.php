<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Repository\CartManager;
use Raketa\BackendTestTask\View\CartView;
use Raketa\BackendTestTask\Controller\JsonResponse;
use Raketa\BackendTestTask\Exception\CartNotFoundException;
use Exception;

final readonly class GetCartController
{
    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        try {
            $response = new JsonResponse();
            $cart = $this->cartManager->getCart();

            $response->getBody()->write(
                json_encode(
                    $this->cartView->toArray($cart),
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
        } catch (CartNotFoundException $e) {
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404, 'Cart not found');
        } catch (Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500, 'Internal server error');   
        }
    }
}
