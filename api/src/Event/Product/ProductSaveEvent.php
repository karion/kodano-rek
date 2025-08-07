<?php

declare(strict_types=1);

namespace App\Event\Product;

use App\Entity\Product\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductSaveEvent extends Event
{
    public function __construct(
        private readonly Product $product,
        private readonly bool $isNew,
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
