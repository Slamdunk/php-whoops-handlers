<?php

declare(strict_types=1);

namespace Slam\WhoopsHandlers;

use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Util\Misc;

final class EmailHandler extends Handler
{
    /** @var callable(string, string): void */
    private $emailCallback;
    private CustomNotes $customNotes;

    /** @param callable(string, string): void $emailCallback */
    public function __construct(callable $emailCallback, ?CustomNotes $customNotes = null)
    {
        $this->emailCallback = $emailCallback;
        $this->customNotes   = $customNotes ?? new CustomNotes();
    }

    public function handle(): int
    {
        $plainTextHandler = new PlainTextHandler();
        $plainTextHandler->setRun($this->getRun());
        $plainTextHandler->setException($this->getException());
        $plainTextHandler->setInspector($this->getInspector());

        if (Misc::isCommandLine()) {
            $bodyArray = [
                'Date'      => \date(\DATE_RFC850),
                'Command'   => \sprintf('$ %s %s', \PHP_BINARY, \implode(' ', $_SERVER['argv'])),
            ];
        } else {
            // @codeCoverageIgnoreStart
            $bodyArray = [
                'Date'         => \date(\DATE_RFC850),
                'REQUEST_URI'  => $_SERVER['REQUEST_URI']     ?? '',
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER']    ?? '',
                'REMOTE_ADDR'  => $_SERVER['REMOTE_ADDR']     ?? '',
                'USER_AGENT'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ];
            // @codeCoverageIgnoreEnd
        }
        $bodyArray['Notes'] = $this->customNotes->get();

        $body = '';
        foreach ($bodyArray as $key => $val) {
            $body .= \sprintf('%-15s%s%s', $key, $val, \PHP_EOL);
        }
        $body .= \PHP_EOL;
        $body .= $plainTextHandler->generateResponse();

        ($this->emailCallback)(
            $this->getException()->getMessage(),
            $body,
        );

        return Handler::DONE;
    }
}
