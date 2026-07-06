<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Exceptions;

use Aicrion\DateConverter\Enum\CalendarType;
use OutOfRangeException;

/**
 * Thrown when a conversion between two calendars cannot be performed,
 * typically because the resulting date falls outside algorithmic bounds
 * or the source year is outside the supported range for that calendar.
 */
final class UnsupportedConversionException extends OutOfRangeException
{
    public function __construct(
        public readonly CalendarType $from,
        public readonly CalendarType $to,
        string $reason = ''
    ) {
        $message = sprintf(
            'Cannot convert from %s to %s%s',
            $from->label(),
            $to->label(),
            $reason !== '' ? ": {$reason}" : ''
        );

        parent::__construct($message);
    }
}
