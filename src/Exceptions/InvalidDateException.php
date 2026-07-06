<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Exceptions;

use Aicrion\DateConverter\Enum\CalendarType;
use InvalidArgumentException;

/**
 * Thrown when a given year/month/day triple is not a valid date
 * within the specified calendar system.
 */
final class InvalidDateException extends InvalidArgumentException
{
    public function __construct(
        public readonly CalendarType $calendar,
        public readonly int $year,
        public readonly int $month,
        public readonly int $day,
        string $reason = ''
    ) {
        $message = sprintf(
            'Invalid %s date: %04d-%02d-%02d%s',
            $calendar->label(),
            $year,
            $month,
            $day,
            $reason !== '' ? " ({$reason})" : ''
        );

        parent::__construct($message);
    }
}
