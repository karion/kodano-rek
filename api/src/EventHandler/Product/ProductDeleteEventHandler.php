<?php

declare(strict_types=1);

namespace App\EventHandler\Product;

use App\Event\Product\ProductDeletedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Uid\Uuid;

#[AsEventListener()]
class ProductDeleteEventHandler
{
    public function __construct(
        readonly private LoggerInterface $auditLogger
    )
    {
    }

    public function __invoke(ProductDeletedEvent $event)
    {
        $this->saveLog($event->getId());
    }

    private function saveLog(Uuid $id): void
    {
        $this->auditLogger->info(
            sprintf('Product %s deleted', $id->toString()),
            [
                'id' => $id->toString(),
            ]
        );
    }
}
