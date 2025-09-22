<?php

declare(strict_types=1);

namespace Slam\WhoopsHandlers;

use Throwable;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Util\Misc;

final class EmailHandler extends Handler
{
    /**
     * @param callable(string, string): void $emailCallback
     * @param list<class-string<Throwable>>  $ignoreExceptions
     * @param non-empty-string               $remoteAddrHeader
     */
    public function __construct(
        private $emailCallback,
        private readonly CustomNotes $customNotes = new CustomNotes(),
        private readonly array $ignoreExceptions = [],
        private readonly string $remoteAddrHeader = 'REMOTE_ADDR',
    ) {}

    public function handle(): int
    {
        if (\in_array($this->getException()::class, $this->ignoreExceptions, true)) {
            return Handler::DONE;
        }

        $plainTextHandler = new PlainTextHandler();
        $plainTextHandler->setRun($this->getRun());
        $plainTextHandler->setException($this->getException());
        $plainTextHandler->setInspector($this->getInspector());

        if (Misc::isCommandLine()) {
            $bodyArray = [
                'Date'      => \date(\DATE_RFC850),
                'Command'   => \sprintf('$ %s %s', \PHP_BINARY, \implode(' ', $_SERVER['argv'])),
                'User'      => \sprintf('%s (%s:%s)', \get_current_user(), \getmyuid(), \getmygid()),
            ];
            $sudoUser = \getenv('SUDO_USER');
            if (false !== $sudoUser) {
                $bodyArray['SUDO'] = \sprintf('%s (%s:%s)', $sudoUser, \getenv('SUDO_UID'), \getenv('SUDO_GID'));
            }
        } else {
            // @codeCoverageIgnoreStart
            $method    = $_SERVER['REQUEST_METHOD'] ?? '';
            $bodyArray = [
                'Date'              => \date(\DATE_RFC850),
                'REQUEST_URI'       => $_SERVER['REQUEST_URI'] ?? '',
                'REQUEST_METHOD'    => $method,
                'HTTP_REFERER'      => $_SERVER['HTTP_REFERER'] ?? '',
                'REMOTE_ADDR'       => 'https://whois.domaintools.com/' . ($_SERVER[$this->remoteAddrHeader] ?? ''),
                'USER_AGENT'        => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ];
            if ('POST' === $method) {
                $bodyArray['$_POST = '] = \trim(\print_r($_POST, true));
            }
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
