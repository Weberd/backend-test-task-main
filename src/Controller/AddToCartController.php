<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Service\CartManager;
use Raketa\BackendTestTask\View\CartView;
use Raketa\BackendTestTask\Controller\JsonResponse;
use Raketa\BackendTestTask\Exception\ProductNotFoundException;
use Exception;

final readonly class AddToCartController
{
    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager,
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            $response = new JsonResponse();

            $cart = $this->cartManager->addToCart($rawRequest['productUuid'], $rawRequest['quantity']);

            $response->getBody()->write(
                json_encode(
                    [
                        'status' => 'success',
                        'cart' => $this->cartView->toArray($cart)
                    ],
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);

        } catch (ProductNotFoundException $e) {
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(404, $e->getMessage());
        } catch (Exception $e) {
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(500, 'Internal server error');
        }
    }
}
