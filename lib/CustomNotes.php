<?php

declare(strict_types=1);

namespace Slam\WhoopsHandlers;

final class CustomNotes
{
    private string $details = '';

    public function append(string $details): void
    {
        $this->details .= $details;
    }

    public function clear(): void
    {
        $this->details = '';
    }

    public function get(): string
    {
        return $this->details;
    }
}
