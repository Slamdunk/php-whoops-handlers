<?php

declare(strict_types=1);

namespace SlamTest\WhoopsHandlers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slam\WhoopsHandlers\BodilessHtmlHandler;
use Whoops\Handler\Handler;

#[CoversClass(BodilessHtmlHandler::class)]
final class BodilessHtmlHandlerTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testDoesntLeakErrorDetails(): void
    {
        $exception = new RuntimeException(\uniqid());

        $handler = new BodilessHtmlHandler();
        $handler->setException($exception);

        \ob_start();
        $returnValue = $handler->handle();
        $output      = \ob_get_clean();

        self::assertSame(Handler::QUIT, $returnValue);
        self::assertNotFalse($output);
        self::assertStringContainsString('500', $output);
        self::assertStringNotContainsString($exception->getMessage(), $output);
    }
}
