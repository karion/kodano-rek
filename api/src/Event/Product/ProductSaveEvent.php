<?php

declare(strict_types=1);

namespace App\Event\Product;

use App\Entity\Product\Product;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

class ProductSaveEvent extends Event
{
    public function __construct(
        readonly private Product $product,
        readonly private bool $isNew
    )
    {
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
