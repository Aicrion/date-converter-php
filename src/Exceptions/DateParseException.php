<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Exceptions;

use RuntimeException;

/**
 * Thrown when a date string cannot be parsed with the given
 * (or any known) format.
 */
final class DateParseException extends RuntimeException
{
    public function __construct(string $value, string $format = '')
    {
        $message = $format !== ''
            ? "Unable to parse date \"{$value}\" using format \"{$format}\""
            : "Unable to parse date \"{$value}\"";

        parent::__construct($message);
    }
}
