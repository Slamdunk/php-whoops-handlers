<?php

declare(strict_types=1);

namespace Slam\WhoopsHandlers;

use Whoops\Handler\Handler;

final class BodilessHtmlHandler extends Handler
{
    public function handle(): int
    {
        $errorType = '500: Internal Server Error';
        \printf('<!DOCTYPE html><html lang="en"><head><title>%1$s</title></head><body><h1>%1$s</h1></body></html>', $errorType);

        return Handler::QUIT;
    }
}
