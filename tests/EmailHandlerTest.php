<?php

declare(strict_types=1);

namespace SlamTest\WhoopsHandlers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slam\WhoopsHandlers\CustomNotes;
use Slam\WhoopsHandlers\EmailHandler;
use Whoops\Run;

#[CoversClass(EmailHandler::class)]
#[CoversClass(CustomNotes::class)]
final class EmailHandlerTest extends TestCase
{
    public function testIncludesExceptionDetails(): void
    {
        $exception = new RuntimeException(\uniqid('ex_'));

        $subject  = null;
        $body     = null;
        $callable = static function (string $actualSubject, string $actualBody) use (& $subject, & $body): void {
            $subject = $actualSubject;
            $body    = $actualBody;
        };
        $customDetails = new CustomNotes();

        $handler = new EmailHandler($callable, $customDetails);

        $run = new Run();
        $run->pushHandler($handler);

        $myNotes = \uniqid('note_');
        $customDetails->append($myNotes);

        \ob_start();
        $returnValue = $run->handleException($exception);
        $output      = \ob_get_clean();

        self::assertSame('', $returnValue);
        self::assertSame('', $output);

        self::assertIsString($subject);
        self::assertIsString($body);

        self::assertSame($exception->getMessage(), $subject);

        self::assertStringContainsString($exception->getMessage(), $body);
        self::assertStringContainsString(__FILE__, $body);
        self::assertStringContainsString($myNotes, $body);
    }
}
