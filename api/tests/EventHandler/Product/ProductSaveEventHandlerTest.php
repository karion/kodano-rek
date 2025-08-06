<?php

declare(strict_types=1);

namespace App\Tests\EventHandler\Product;

use App\Entity\Product\Product;
use App\Event\Product\ProductSaveEvent;
use App\EventHandler\Product\ProductSaveEventHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

class ProductSaveEventHandlerTest extends TestCase
{

    public function testItLogsAndSendsMailForNewProduct(): void
    {
        $product = $this->createMock(Product::class);
        $productId = Uuid::v4();
        $product->method('getId')->willReturn($productId);
        $product->method('getName')->willReturn('Test Product');

        $event = new ProductSaveEvent($product, true);

        $logger = $this->createMock(LoggerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);

        $logger->expects($this->once())
            ->method('info')
            ->with(
                sprintf('Product %s created', $productId->toString()),
                [
                    'id' => $productId->toString(),
                    'name' => 'Test Product',
                ]
            );

        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($productId) {
                return $email->getSubject() === 'New product: Test Product'
                    && str_contains($email->getHtmlBody(), $productId->toString());
            }));

        $handler = new ProductSaveEventHandler($logger, $mailer);
        $handler($event);
    }

    public function testItLogsWithoutSendingMailForUpdatedProduct(): void
    {
        $product = $this->createMock(Product::class);
        $productId = Uuid::v4();
        $product->method('getId')->willReturn($productId);
        $product->method('getName')->willReturn('Updated Product');

        $event = new ProductSaveEvent($product, false);

        $logger = $this->createMock(LoggerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);

        $logger->expects($this->once())
            ->method('info')
            ->with(
                sprintf('Product %s updated', $productId->toString()),
                [
                    'id' => $productId->toString(),
                    'name' => 'Updated Product',
                ]
            );

        $mailer->expects($this->never())
            ->method('send');

        $handler = new ProductSaveEventHandler($logger, $mailer);
        $handler($event);
    }
}