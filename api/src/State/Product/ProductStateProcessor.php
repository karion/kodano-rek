<?php

declare(strict_types=1);

namespace App\State\Product;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Event\Product\ProductDeletedEvent;
use App\Event\Product\ProductSaveEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProductStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof DeleteOperationInterface) {
            $id = $data->getId();
            $result = $this->removeProcessor->process($data, $operation, $uriVariables, $context);

            $this->dispatcher->dispatch(new ProductDeletedEvent($id));

            return $result;
        }

        $isNew = null === $data->getId();
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->dispatcher->dispatch(new ProductSaveEvent($data, $isNew));

        return $result;
    }
}
