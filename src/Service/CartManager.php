<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Service;

use CartRepository;
use Raketa\BackendTestTask\Domain\Cart;
use Raketa\BackendTestTask\Domain\CartItem;
use Ramsey\Uuid\Uuid;
use Raketa\BackendTestTask\Service\SessionService;
use Raketa\BackendTestTask\Repository\ProductRepository;

final class CartManager
{
    public function __construct(
        private ProductRepository $productRepository,
        private CartRepository $cartRepository,
        private SessionService $sessionService,
    ) {
    }

    /**
     * @inheritdoc
     */
    private function saveCart(Cart $cart): void
    {
        $this->cartRepository->set($cart, $this->sessionService->getId());
    }

    public function getCart(): Cart
    {
        return $this->cartRepository->get($this->sessionService->getId());
    }

    /*
        @throws ProductNotFoundException
    */ 
    public function addToCart(string $productUuid, int $quantity): Cart
    {
        $cart = $this->getCart();
        $product = $this->productRepository->getByUuid($productUuid);
        
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $quantity,
        ));

        $this->saveCart($cart);
        return $cart;
    }
}
