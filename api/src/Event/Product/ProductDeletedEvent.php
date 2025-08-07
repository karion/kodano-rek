<?php

declare(strict_types=1);

namespace App\Event\Product;

use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

class ProductDeletedEvent extends Event
{
    public function __construct(
        private readonly Uuid $id,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
