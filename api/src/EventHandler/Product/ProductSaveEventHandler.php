<?php

declare(strict_types=1);

namespace App\EventHandler\Product;

use App\Entity\Product\Product;
use App\Event\Product\ProductSaveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener()]
class ProductSaveEventHandler
{
    public function __construct(
        readonly private LoggerInterface $auditLogger
    )
    {
    }

    public function __invoke(ProductSaveEvent $event)
    {
        $this->saveLog($event->getProduct(), $event->isNew());
    }

    private function saveLog(Product $product, bool $isNew): void
    {
        if ($isNew) {
            $message = sprintf('Product %s created', $product->getId()->toString());
        } else {
            $message = sprintf('Product %s updated', $product->getId()->toString());
        }

        $this->auditLogger->info(
            $message,
            [
                'id' => $product->getId()->toString(),
                'name' => $product->getName()
            ]
        );
    }
}
