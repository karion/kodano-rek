<?php

declare(strict_types=1);

namespace App\EventHandler\Product;

use App\Entity\Product\Product;
use App\Event\Product\ProductSaveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEventListener()]
class ProductSaveEventHandler
{
    public function __construct(
        readonly private LoggerInterface $auditLogger,
        readonly private MailerInterface $mailer
    )
    {
    }

    public function __invoke(ProductSaveEvent $event)
    {
        $this->saveLog($event->getProduct(), $event->isNew());

        if ($event->isNew()) {
            $this->sendMail($event->getProduct());
        }
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

    private function sendMail(Product $product): void
    {
        $email = (new Email())
            ->from('noreply@kodano.pl')
            ->to('supervisor@kodano.pl')
            
            ->subject(sprintf('New product: %s', $product->getName()))
            ->html(sprintf('<h1>New product: %s</h1><p>Link to product %s</p>', $product->getName(), $product->getId()->toString()));

        $this->mailer->send($email);
    }
}
